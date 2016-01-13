<?php 

include('header.php');

$request =  new Api();
$request -> add_request('profileTime');
$result  =  $request -> send_request();

if(!empty($result->profileTime)) {
	$time = $result->profileTime;
}
else {
	$time = 0;
}

switch(LOCALE) {
	case 'fr_FR':
		echo strftime('%H:%M', $_SERVER['REQUEST_TIME'] + $time);
		break;
	case 'en_UK':
		echo strftime('%l:%M%P', $_SERVER['REQUEST_TIME'] + $time);
		break;
	default:
		echo strftime('%l:%M%P', $_SERVER['REQUEST_TIME'] + $time);
		break;
}

?>