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
