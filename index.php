<?php
session_start();
require_once("controllers/Controller.php");
require_once("models/Model.php");
$model = new Model;

$AC = new AccessControl($model);

if (!$AC->checkAccess($_SERVER['REMOTE_ADDR'])) {
	require_once("views/errors/403.php");
	die;
}

$foo = password_hash('dam4sc3naSILISTRA', PASSWORD_DEFAULT);

$do = isset($_GET['do']) ? $_GET['do'] : '';
$user = isset($_SESSION['username']) ? $_SESSION['username'] : "";

if ($do == 'login') {
    $user_data = $model->login(isset($_POST['username']) ? $_POST['username'] : '');
    if (isset($_POST['password']) && isset($_POST['password']) && password_verify($_POST['password'], $user_data['password'])) {
        $_SESSION['valid'] = true;
        $_SESSION['username'] = $user_data['username'];
        $_SESSION['settings'] = $user_data['settings'];
        $AC->loggedIn($_SERVER['REMOTE_ADDR']);
	    header("Location: index.php");
    } else {
		$AC->notLoggedIn($_SERVER['REMOTE_ADDR']);
        echo 'Pogrešno korisničko ime i/ili šifra. Sačekajte 5 sekundi...';
        echo '<script>setTimeout(function(){window.location.href = "index.php";}, 5000)</script>';
    }
}
elseif ("" == $user || $user == false) {
	require_once("views/login_view.php");
}
elseif ($do == "smooth-it") {
	$stage = isset($_GET['stage']) ? $_GET['stage'] : 'start';
	$RI = new NutriCalc($model);
	switch ($stage) {
		case 'start':
			echo json_encode($RI->startMulti());
			break;
		case 'calc':
			$calc_data = isset($_POST['data']) ? $_POST['data'] : '';
			echo json_encode($RI->smoothIt($calc_data));
			break;
	}
}
elseif ($do == "indi-calc") {
	$stage = isset($_GET['stage']) ? $_GET['stage'] : 'start';
	$food_id = isset($_GET['food_id']) ? $_GET['food_id'] : '';
	$weight = isset($_GET['weight']) ? $_GET['weight'] : 0;
	$price = isset($_GET['price']) ? $_GET['price'] : 0;
	$refuse = isset($_GET['refuse']) ? $_GET['refuse'] : 0;
	$RI = new NutriCalc($model);
	switch ($stage) {
		case 'start':
			echo json_encode($RI->startIndi());
			break;
		case 'calc':
			echo json_encode($RI->indiCalc($food_id, $weight, $price, $refuse));
			break;
	}
}
elseif ($do == "food-input") {
	$stage = isset($_GET['stage']) ? $_GET['stage'] : 'start';
	$group_id = isset($_GET['group_id']) ? $_GET['group_id'] : '';
	$food_id = isset($_GET['food_id']) ? $_GET['food_id'] : '';
	$FI = new FoodInput($model);
	switch ($stage) {
		case 'start':
			echo json_encode($FI->start());
			break;
		case 'food-items':
			echo json_encode($FI->food_items($group_id));
			break;
		case 'that-food':
			echo json_encode($FI->that_food($group_id, $food_id));
			break;
		case 'save-food':
			$name_sr = isset($_POST['name_sr']) ? $_POST['name_sr'] : '';
			$name_en = isset($_POST['name_en']) ? $_POST['name_en'] : '';
			$price = isset($_POST['price']) ? $_POST['price'] : '';
			$refuse = isset($_POST['refuse']) ? $_POST['refuse'] : '';
			$unit = isset($_POST['unit']) ? $_POST['unit'] : '';
			$data = isset($_POST['data']) ? $_POST['data'] : '';
			echo json_encode($FI->save_food($food_id, $name_sr, $name_en, $price, $refuse, $unit, $data));
			break;
	}
}
elseif ($do == "rdi-input") {
	$stage = isset($_GET['stage']) ? $_GET['stage'] : 'start';
	$nid = isset($_GET['nid']) ? $_GET['nid'] : '';
	$RI = new RDIInput($model);
	switch ($stage) {
		case 'start':
			echo json_encode($RI->start());
			break;
		case 'save':
			$name_sr = isset($_POST['name_sr']) ? $_POST['name_sr'] : '';
			$rdi = isset($_POST['rdi']) ? $_POST['rdi'] : '';
			echo json_encode($RI->save($nid, $name_sr, $rdi));
			break;
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