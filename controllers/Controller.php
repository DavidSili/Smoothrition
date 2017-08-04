<?php

class Controller {
    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}

class AccessControl extends Controller {

	public function checkAccess($ip)
	{
		$thisIp = $this->model->checkThisIp($ip);
		$allIps = $this->model->checkAllIps();

		return !($thisIp || $allIps);
	}

	public function loggedIn($ip) {
		$this->model->userLoggedIn($ip);
	}

	public function notLoggedIn($ip) {
		$this->model->notLoggedIn($ip);
	}

}

class FoodInput extends Controller {

    public function start() {
	    $foodGroups = $this->model->getFoodGroups();
	    ob_start();
	    require_once("views/food_input_view.php");
	    $return = ob_get_clean();

	    return array('html' => $return);
    }

	public function food_items($group_id) {
		$foodGroups = $this->model->getFoodGroups();
		$foodItems = $this->model->getFoodItems($group_id);
		$existingFood = $this->model->getExistingFoodIds();

		ob_start();
		require_once("views/food_input_view.php");
		$return = ob_get_clean();

		return array('html' => $return);
	}

	public function that_food($group_id, $food_id) {
		$foodGroups = $this->model->getFoodGroups();
		$foodItems = $this->model->getFoodItems($group_id);
		$existingFood = $this->model->getExistingFoodIds();
		$thatFood = $this->model->getThatFood($food_id);

		ob_start();
		require_once("views/food_input_view.php");
		$return = ob_get_clean();

		return array('html' => $return);
	}

	public function save_food($food_id, $name_sr, $name_en, $price, $refuse, $unit, $data) {
		$result = $this->model->saveFood($food_id, $name_sr, $name_en, $price, $refuse, $unit, $data);

    	return array('state' => $result);
	}

}

class RDIInput extends Controller {

	public function start() {
		$nutrients = $this->model->getAllNutrients();
		ob_start();
		require_once("views/rdi_input_view.php");
		$return = ob_get_clean();

		return array('html' => $return);
	}

	public function save($nid, $name_sr, $rdi) {
		$result = $this->model->saveRdi($nid, $name_sr, $rdi);

		return array('state' => $result);
	}

}

class NutriCalc extends Controller {

	public function startIndi() {
		$foods = $this->model->getAllMyFoods();
		ob_start();
		require_once("views/indi_calc_view.php");
		$return = ob_get_clean();

		return array('html' => $return);
	}

	public function indiCalc($food_id, $weight, $price, $refuse){
		$results = $this->model->calculatedIndiResults($food_id, $weight, $price, $refuse);
		$general = $results['general'];
		$nutrients = $results['nutrients'];
		ob_start();
		require_once("views/indi_report_view.php");
		$return = ob_get_clean();

		return array('html' => $return);
	}

	public function startMulti() {
		$foods = $this->model->getAllMyFoods();
		ob_start();
		require_once("views/smooth_it_view.php");
		$return = ob_get_clean();

		return array('html' => $return);
	}

	public function smoothIt($data){
		$results = $this->model->smoothItResults($data);
		$general = $results['general'];
		$nutrients = $results['nutrients'];
		ob_start();
		require_once("views/smooth_it_report_view.php");
		$return = ob_get_clean();

		return array('html' => $return);
	}

}