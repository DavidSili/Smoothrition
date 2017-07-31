<?php

class Controller {
    public $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
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
		$result = $this->model->save_food($food_id, $name_sr, $name_en, $price, $refuse, $unit, $data);

    	return array('state' => $result);
	}

}

class Unos extends Controller {
    public function insertTask($user, $task) {
        return $this->model->insertT($user, $task);
    }
}

class Done extends Controller {
    public function doneTask($user, $t_id, $done) {
        return $this->model->doneT($user, $t_id, $done);
    }
}
