<?php 

if (empty($_GET['user'])){
	redirect();
}
else {
	$userid = $_GET['user'];
}

$request =  new Api();
$request -> add_request('profileInfo', Array($_GET['user']));
$request -> add_request('language');
$request -> add_request('design');
$result  =  $request -> send_request();

$profilInfo = $result->profileInfo;
$language = $result->language;
$currentuser = $request->getId();

if (empty($profilInfo)){
	redirect('/conf_users');
}

?>