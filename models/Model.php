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

    public function login ($username) {
        $sql = "SELECT username, password, settings FROM users WHERE username = :username";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(":username" => $username));
        $return = $stmt->fetch(PDO::FETCH_ASSOC);
        return $return;
    }

	public function getFoodGroups() {
		return array(
			'0900' => 'Voća i voćni sokovi',
			'1200' => 'Koštunjavo voće i semenje',
			'1400' => 'Pića',
			'0200' => 'Začini i lekovito bilje',
			'0400' => 'Masti i ulja',
			'1100' => 'Povrće',
			'0100' => 'Proizvodi od mleka i jaja',
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

		$data = array();
		foreach ($thatFood->report->food->nutrients as $nutrient) {
			$data[$nutrient->nutrient_id] = array();
			$data[$nutrient->nutrient_id]['g'] = $nutrient->group;
			$data[$nutrient->nutrient_id]['v'] = $nutrient->value;
		}

		return array(
			'fid' 	  => $thatFood->report->food->ndbno,
			'name_en' => $thatFood->report->food->name,
			'refuse'  => $thatFood->report->food->r,
			'unit' 	  => $thatFood->report->food->ru,
			'data' 	  => json_encode($data),
		);
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

	public function loadTasks ($user = NULL) {
        if (NULL == $user) {
            die;
        }
        $sql = "SELECT id, title, done FROM tasks WHERE user = :user";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(array(":user" => $user));
        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return json_encode($return);
    }

    public function insertT ($user = null, $task = null) {
        if (empty($user) || null == $task) {
            return false;
        } else {
            $sql = 'INSERT INTO tasks (user, title) VALUES (:user, :task)';
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(array(':user' => $user, ':task' => $task));
            return ($result == true) ? 'ok' : 'error';
        }
    }

    public function doneT ($user = null, $t_id = null, $done) {
        if (empty($user) || null == $t_id || null === $done ) {
            return false;
        } else {
            $sql = 'UPDATE tasks SET done = :done WHERE `user` = :user AND id = :id';
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(array(':user' => $user, ':id' => $t_id, ':done' => $done));
            return ($result == true) ? '{"callback":"ok"}' : '{"callback":"error"}';
        }
    }

}