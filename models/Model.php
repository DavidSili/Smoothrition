<?php
class Db {
    private static $instance = NULL;

    private function __construct() {}

    private function __clone() {}

    public static function getInstance() {
        if (!isset(self::$instance)) {
            $pdo_options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
            $pdo_options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES 'utf8'";
            self::$instance = new PDO('mysql:host=localhost;dbname=smoothrition', 'root', '', $pdo_options);
        }
        return self::$instance;
    }

}
class Model {

    protected $db;

    public function __construct() {
        $this->db = Db::getInstance();
    }

	// ----------------------------------- Authentication -----------------------------------

	public function checkThisIp($ip) {
		$time = time() - 900;
		$sql = "SELECT * FROM login_attempts WHERE ip = :ip AND time > :time AND fails > 4";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(
			":ip" => $ip,
			":time" => $time
		));
		$return = $stmt->fetch(PDO::FETCH_ASSOC);
		return $return;
	}

	public function checkAllIps() {
		$time = time() - 15;
		$sql = "SELECT * FROM login_attempts WHERE `time` > :time AND fails > 9";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(":time" => $time));
		$return = $stmt->fetch(PDO::FETCH_ASSOC);
		return $return;
	}

	public function userLoggedIn($ip) {
		$sql = 'UPDATE login_attempts SET `fails` = 0 WHERE `ip` = :ip';
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(
			':ip' => $ip
		));
	}

	public function notLoggedIn($ip) {
		$sql = "SELECT * FROM login_attempts WHERE ip = :ip";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(
			":ip" => $ip,
		));
		$row = $stmt->fetch(PDO::FETCH_ASSOC);

		if ($row) {
			$fails = $row['fails'] + 1;
			$sql = 'UPDATE login_attempts SET `time` = :time, `fails` = :fails WHERE `ip` = :ip';
		} else {
			$fails = 1;
			$sql = 'INSERT INTO login_attempts (`ip`, `time`, `fails`) VALUES (:ip, :time, :fails)';
		}
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(
			':time' => time(),
			':fails' => $fails,
			':ip' => $ip
		));
	}

	public function login($username) {
        $sql = "SELECT username, password, settings FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(":username" => $username));
        $return = $stmt->fetch(PDO::FETCH_ASSOC);
        return $return;
    }

	// ----------------------------------- Food Input -----------------------------------

	public function getFoodGroups() {
		return array(
			'0900' => 'Voća i voćni sokovi',
			'1200' => 'Koštunjavo voće i semenje',
			'1400' => 'Pića',
			'0200' => 'Začini i lekovito bilje',
			'0400' => 'Masti i ulja',
			'1100' => 'Povrće',
			'0100' => 'Proizvodi od mleka i jaja',
			'1600' => 'Mahunarke',
			'1900' => 'Slatkiši',
		);
	}

	public function getFoodItems($group_id) {
		$url = 'https://api.nal.usda.gov/ndb/search/';
		$api_key = 'PvxMosJ3c46MpwRP9aQ0jn4Z8QO8lIFnHgr6tDDb';
		$options = array(
			'format'  => 'json',
			'fg' 	  => $group_id,
			'sort'	  => 'n',
			'max'	  => '1000',
			'api_key' => $api_key,
		);
		$url_request = $url;
		foreach ($options as $key => $value) {
			if ($url_request == $url) $url_request .= '?';
			else $url_request .= '&';
			$url_request .= $key.'='.$value;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url_request);
		$jsonData=curl_exec($ch);
		curl_close($ch);

		$foodItems = json_decode($jsonData);
		$return = array();
		foreach ($foodItems->list->item as $item) {
			$return[$item->ndbno] = $item->name;
		}

		return $return;
	}

	public function getThatFood ($food_id) {
		$sql = "SELECT * FROM food WHERE id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(":id" => $food_id));
		$return = $stmt->fetch(PDO::FETCH_ASSOC);
		if ($return) {
			return array(
				'fid' 	  => $return['id'],
				'name_sr' => $return['name_sr'],
				'name_en' => $return['name_en'],
				'price' => $return['price'],
				'unit' => $return['unit'],
				'refuse' => $return['refuse'],
				'data' => $return['data'],
			);
		}

		$url = 'https://api.nal.usda.gov/ndb/reports/';
		$api_key = 'PvxMosJ3c46MpwRP9aQ0jn4Z8QO8lIFnHgr6tDDb';
		$options = array(
			'format'  => 'json',
			'ndbno'   => $food_id,
			'type'	  => 'f',
			'api_key' => $api_key,
		);
		$url_request = $url;
		foreach ($options as $key => $value) {
			if ($url_request == $url) $url_request .= '?';
			else $url_request .= '&';
			$url_request .= $key.'='.$value;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url_request);
		$thatFood = json_decode(curl_exec($ch));
		curl_close($ch);

		$this->autoAddNutrients($thatFood->report->food->nutrients);
		$this->autoAddBasicNutrients($food_id);

		$data = array();
		foreach ($thatFood->report->food->nutrients as $nutrient) {
			$data[$nutrient->nutrient_id] = array();
			$data[$nutrient->nutrient_id]['g'] = $nutrient->group;
			$data[$nutrient->nutrient_id]['v'] = $nutrient->value;
		}

		return array(
			'fid' 	  => $thatFood->report->food->ndbno,
			'name_en' => $thatFood->report->food->name,
			'refuse'  => substr($thatFood->report->food->r,0,-1),
			'unit' 	  => $thatFood->report->food->ru,
			'data' 	  => json_encode($data),
		);
	}

	public function getExistingFoodIds() {
		$sql = 'SELECT id FROM food ORDER BY id ASC';
		$stmt = $this->db->query($sql);
		$ids = array();
		while($row = $stmt->fetch()) {
			$ids[] = $row['id'];
		}
		return $ids;
	}

	public function autoAddNutrients ($nutrients) {
		// nutrient_id, name, unit,
		$allIds = array();
		$allData = array();
		foreach ($nutrients as $nutrient) {
			$allIds[] = $nutrient->nutrient_id;
			$allData[$nutrient->nutrient_id] = array(
				'name_en' => $nutrient->name,
				'unit'	  => $nutrient->unit,
			);
		}
		$allIdsJoined = implode(',', $allIds);

		$sql = 'SELECT nid FROM nutrients WHERE FIND_IN_SET(nid, :array)';
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(":array" => $allIdsJoined));
		$usedIds = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$usedIdsArray = array();
		foreach ($usedIds as $nutrient) {
			$usedIdsArray[] = $nutrient['nid'];
		}

		$unusedData = array();
		foreach ($allData as $key => $value) {
			if (!in_array($key, $usedIdsArray))
				$unusedData[$key] = $value;
		}

		foreach ($unusedData as $key => $value) {
			$sql = 'INSERT INTO nutrients (nid, name_en, unit) VALUES (:nid, :name_en, :unit)';
			$stmt = $this->db->prepare($sql);
			$stmt->execute(array(
				':nid' => $key,
				':name_en' => $value['name_en'],
				':unit' => $value['unit'],
			));
		}
	}

	/**
	 * Creates a list of ids of basic nutrients
	 *
	 * @param $food_id
	 */
	public function autoAddBasicNutrients ($food_id) {
		$url = 'https://api.nal.usda.gov/ndb/reports/';
		$api_key = 'PvxMosJ3c46MpwRP9aQ0jn4Z8QO8lIFnHgr6tDDb';
		$options = array(
			'format'  => 'json',
			'ndbno'   => $food_id,
			'type'	  => 'b',
			'api_key' => $api_key,
		);
		$url_request = $url;
		foreach ($options as $key => $value) {
			if ($url_request == $url) $url_request .= '?';
			else $url_request .= '&';
			$url_request .= $key.'='.$value;
		}
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_URL,$url_request);
		$thatFood = json_decode(curl_exec($ch));
		curl_close($ch);
		$nutrients = $thatFood->report->food->nutrients;

		$allIds = array();
		foreach ($nutrients as $nutrient) {
			$allIds[] = $nutrient->nutrient_id;
		}
		$allIdsJoined = implode(',', $allIds);

		$sql = 'SELECT nid FROM basic_nutrients WHERE FIND_IN_SET(nid, :array)';
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(":array" => $allIdsJoined));
		$usedIds = $stmt->fetchAll(PDO::FETCH_ASSOC);
		$usedIdsArray = array();
		foreach ($usedIds as $nutrient) {
			$usedIdsArray[] = $nutrient['nid'];
		}

		$unusedData = array();
		foreach ($allIds as $id) {
			if (!in_array($id, $usedIdsArray))
				$unusedData[] = $id;
		}

		foreach ($unusedData as $id) {
			$sql = 'INSERT INTO basic_nutrients (nid) VALUES (:nid)';
			$stmt = $this->db->prepare($sql);
			$stmt->execute(array(
				':nid' => $id
			));
		}
	}

	public function saveFood($food_id, $name_sr, $name_en, $price, $refuse, $unit, $data) {
		if (empty($food_id) || empty($name_sr) || empty($name_en) || empty($unit) || empty($data)) {
			return 'error';
		}
		if (in_array($food_id, $this->getExistingFoodIds())) {
			$sql = 'UPDATE food SET `name_sr` = :name_sr, `name_en` = :name_en, `price` = :price, `unit` = :unit, `refuse` = :refuse, `data` = :data WHERE `id` = :id';
		} else {
			$sql = 'INSERT INTO food (`id`, `name_sr`, `name_en`, `price`, `unit`, `refuse`, `data`) VALUES (:id, :name_sr, :name_en, :price, :unit, :refuse, :data)';
		}
		$stmt = $this->db->prepare($sql);
		$result = $stmt->execute(array(
			':id' => $food_id,
			':name_sr' => $name_sr,
			':name_en' => $name_en,
			':price' => $price,
			':unit' => $unit,
			':refuse' => $refuse,
			':data' => $data
		));
		return ($result == true) ? 'ok' : 'error';
	}

	// ----------------------------------- RDI Input -----------------------------------

	public function getAllNutrients() {
		$sql = 'SELECT * FROM nutrients ORDER BY nid ASC';
		$stmt = $this->db->query($sql);
		$nutrients = array();
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$nutrients[$row['nid']] = $row;
		};

		return $nutrients;
	}

	public function saveRdi($nid, $name_sr, $rdi) {
		if (empty($nid)) return 'error';
		if (empty($rdi)) {
			$sql = 'UPDATE nutrients SET `name_sr` = :name_sr WHERE `nid` = :nid';
			$stmt = $this->db->prepare($sql);
			$result = $stmt->execute(array(
				':name_sr' => $name_sr,
				':nid' => $nid
			));
		} else {
			$sql = 'UPDATE nutrients SET `name_sr` = :name_sr, `rdi` = :rdi WHERE `nid` = :nid';
			$stmt = $this->db->prepare($sql);
			$result = $stmt->execute(array(
				':name_sr' => $name_sr,
				':rdi' => str_replace(',','.',$rdi),
				':nid' => $nid
			));
		}
		return ($result == true) ? 'ok' : 'error';
	}

	// ----------------------------------- RDI Input -----------------------------------

	public function getAllMyFoods() {
		$sql = 'SELECT * FROM food ORDER BY name_sr ASC';
		$stmt = $this->db->query($sql);
		$foods = array();
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $key => $food) {
			$foods[$food['id']] = array();
			$foods[$food['id']]['id'] = $food['id'];
			$foods[$food['id']]['name'] = ($food['name_sr']) ? $food['name_sr'] : $food['name_en'];
			$foods[$food['id']]['price'] = $food['price'];
			$foods[$food['id']]['refuse'] = $food['refuse'];
		};

    	return $foods;
	}

	public function calculatedIndiResults($food_id, $weight, $price, $refuse){
		$general = array();
		$general['total_price'] = round (100 * $price * $weight / 1000 * (1 + ($refuse / (100 - $refuse)))) / 100;
		$general['weight'] = intval($weight);
		$general['utilization'] = 100 - $refuse;

		$sql = "SELECT name_sr, name_en, data FROM food WHERE id = :id";
		$stmt = $this->db->prepare($sql);
		$stmt->execute(array(":id" => $food_id));
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		$nutritionData = $result['data'];
		$general['name'] = ($result['name_sr']) ? $result['name_sr'] : $result['name_en'];
		$theseNutrients = json_decode($nutritionData);

		$basicNutrients = $this->getBasicNutrients();
		$allNutrients = $this->getAllNutrients();

		$combinedNutrients = array();
		foreach ($theseNutrients as $key => $nutrient) {
			$combinedNutrients[$key] = array();
			$combinedNutrients[$key]['group'] = $nutrient->g;
			$combinedNutrients[$key]['name'] = ($allNutrients[$key]['name_sr']) ? $allNutrients[$key]['name_sr'] : $allNutrients[$key]['name_en'];
			$combinedNutrients[$key]['unit'] = $allNutrients[$key]['unit'];
			$value = $nutrient->v / 100 * $weight;
			$combinedNutrients[$key]['value'] = ($value >= 1000) ? round($value) : round($value * 10) / 10;
			$rdi = ($allNutrients[$key]['rdi']) ? 100 * $allNutrients[$key]['rdi'] / 100 : 0;
			$combinedNutrients[$key]['rdi'] = $rdi;
			$combinedNutrients[$key]['percentage'] = ($rdi) ? round( 1000 * $value / $rdi )/10 : 0;
			if ($combinedNutrients[$key]['percentage'] >= 1000)
				$combinedNutrients[$key]['percentage'] = round($combinedNutrients[$key]['percentage']);
			$combinedNutrients[$key]['list_type'] = (in_array($key, $basicNutrients)) ? 'b' : 'f';
		}

		$data['general'] = $general;
		$data['nutrients'] = $combinedNutrients;

		return $data;
	}

	public function getBasicNutrients() {
		$sql = 'SELECT nid FROM basic_nutrients ORDER BY nid ASC';
		$stmt = $this->db->query($sql);
		$ids = array();
		foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
			$ids[] = intval($row['nid']);
		};

		return $ids;
	}

	public function smoothItResults($data){
		$data = json_decode($data, true);
		$basicNutrients = $this->getBasicNutrients();
		$allNutrients = $this->getAllNutrients();
		$general = array(
			'total_price' => 0,
			'weight' => 0,
			'utilization' => 0,
		);
		$combinedNutrients = array();
		$refuse_weight = 0;

		foreach ($data as $food) {
			$general['total_price'] += round (100 * $food['price'] * $food['weight'] / 1000 * (1 + ($food['refuse'] / (100 - $food['refuse'])))) / 100;
			$general['weight'] += intval($food['weight']);
			$refuse_weight += $food['weight'] * $food['refuse'] / 100;

			$sql = "SELECT name_sr, name_en, data FROM food WHERE id = :id";
			$stmt = $this->db->prepare($sql);
			$stmt->execute(array(":id" => $food['food_id']));
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			$nutritionData = $result['data'];
			$name = ($result['name_sr']) ? $result['name_sr'] : $result['name_en'];
			$general['name'] = (isset($general['name'])) ? $general['name'].', '.$name : $name;
			$theseNutrients = json_decode($nutritionData);

			foreach ($theseNutrients as $key => $nutrient) {
				// do'vde sam stigao
				if (!isset($combinedNutrients[$key])) {
					$combinedNutrients[$key] = array();
					$combinedNutrients[$key]['group'] = $nutrient->g;
					$combinedNutrients[$key]['name'] = ($allNutrients[$key]['name_sr']) ? $allNutrients[$key]['name_sr'] : $allNutrients[$key]['name_en'];
					$combinedNutrients[$key]['unit'] = $allNutrients[$key]['unit'];
					$rdi = ($allNutrients[$key]['rdi']) ? 100 * $allNutrients[$key]['rdi'] / 100 : 0;
					$combinedNutrients[$key]['rdi'] = $rdi;
					$combinedNutrients[$key]['list_type'] = (in_array($key, $basicNutrients)) ? 'b' : 'f';
					$combinedNutrients[$key]['value'] = $nutrient->v / 100 * $food['weight'];
				} else {
					$combinedNutrients[$key]['value'] += $nutrient->v / 100 * $food['weight'];
				}
			}
		}
		foreach ($combinedNutrients as $key => $value) {
			$combinedNutrients[$key]['percentage'] = ($value['rdi']) ? round( 1000 * $value['value'] / $value['rdi'] )/10 : 0;
			if ($combinedNutrients[$key]['percentage'] >= 1000)
				$combinedNutrients[$key]['percentage'] = round($combinedNutrients[$key]['percentage']);
			if ($combinedNutrients[$key]['value'] >= 1000)
				$combinedNutrients[$key]['value'] = round($combinedNutrients[$key]['value']);
			else
				$combinedNutrients[$key]['value'] = round($combinedNutrients[$key]['value'] * 10) / 10;
		}

		$general['utilization'] = 100 - (round(1000 * $refuse_weight / $general['weight']) / 10);

		$data['general'] = $general;
		$data['nutrients'] = $combinedNutrients;

		return $data;
	}

}