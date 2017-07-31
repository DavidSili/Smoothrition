<?php
session_start();
require_once("controllers/Controller.php");
require_once("models/Model.php");
$model = new Model;

$do = isset($_GET['do']) ? $_GET['do'] : '';
$user = isset($_SESSION['username']) ? $_SESSION['username'] : "";

if ($do == 'login') {
    $user_data = $model->login($_POST['username']);
    if (password_verify($_POST['password'], $user_data['password'])) {
        $_SESSION['valid'] = true;
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['settings'] = $user_data['settings'];
	    header("Location: index.php");
    } else {
        echo 'Wrong username and/or password';
        require_once("views/login_view.php");
    }
}
elseif ("" == $user || $user == false) {
	require_once("views/login_view.php");
}
elseif ($do == "smooth-it") {

}
elseif ($do == "individual-calc") {

}
elseif ($do == "food-input") {
	$stage = isset($_GET['stage']) ? $_GET['stage'] : 'start';
	$group_id = isset($_GET['group_id']) ? $_GET['group_id'] : '';
	$food_id = isset($_GET['food_id']) ? $_GET['food_id'] : '';
	$MI = new FoodInput($model);
	switch ($stage) {
		case 'start':
			echo json_encode($MI->start());
			break;
		case 'food-items':
			echo json_encode($MI->food_items($group_id));
			break;
		case 'that-food':
			echo json_encode($MI->that_food($group_id, $food_id));
			break;
		case 'save-food':
			$name_sr = isset($_POST['name_sr']) ? $_POST['name_sr'] : '';
			$name_en = isset($_POST['name_en']) ? $_POST['name_en'] : '';
			$price = isset($_POST['price']) ? $_POST['price'] : '';
			$refuse = isset($_POST['refuse']) ? $_POST['refuse'] : '';
			$unit = isset($_POST['unit']) ? $_POST['unit'] : '';
			$data = isset($_POST['data']) ? $_POST['data'] : '';
			echo json_encode($MI->save_food($food_id, $name_sr, $name_en, $price, $refuse, $unit, $data));
			break;
	}
}
elseif ($do == "dri-input") {

}
elseif ($do == "done") {
	$t_id = isset($_GET['t_id']) ? $_GET['t_id'] : "";
	$done = isset($_GET['done']) ? $_GET['done'] : "";
	if ("" != $user && "" != $t_id && "" !== $done) {
		$MI = new Done($model);
		echo $MI->doneTask($user, $t_id, $done);
	}
}
elseif ($do == "unos") {
	$task = isset($_GET['task']) ? $_GET['task'] : "";
	if ("" != $user && "" != $task) {
		$MI = new Unos($model);
		$return = $MI->insertTask($user, $task);
		echo '{"callback":"'.$return.'"}';
	}
}
elseif ($do == "logout") {
	unset($_SESSION['username']);
	unset($_SESSION['valid']);
	unset($_SESSION['settings']);
	header("Location: index.php");
}
else {
	require_once("views/layout_view.php");
}