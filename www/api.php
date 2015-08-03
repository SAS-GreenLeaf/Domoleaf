<?php 

include('config.php');
include('functions.php');

header('ACCESS-Control-Allow-Origin: *');
header('ACCESS-Control-Allow-Methods: POST, GET');
header('ACCESS-Control-Allow-Headers: x-requested-width');
ini_set('display_errors', 1);
error_reporting(E_ALL);

include('libs/PHPMailer/class.phpmailer.php');
include('libs/Link.class.php');

include('libs/Socket.class.php');
include('libs/Guest.class.php');
include('libs/User.class.php');
include('libs/Admin.class.php');
include('libs/Root.class.php');

include('libs/Api.class.php');

if(!empty($_GET)) {
	$_POST = $_GET;
}

$answer = Api::action($_POST['token'], $_POST['request']);

echo json_encode($answer);

?>