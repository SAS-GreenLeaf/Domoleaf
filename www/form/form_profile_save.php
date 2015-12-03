<?php

include('header.php');

$request =  new Api();
$request -> add_request('profileRename', array($_GET['lastname'], $_GET['firstname'], $_GET['gender'], $_GET['email'], $_GET['phone'], $_GET['language'], $_GET['id']));

if (!empty($_GET['level'])) {
	$request -> add_request('profileLevel', array($_GET['id'], $_GET['level']));
}

if (!empty($_GET['username'])) {
	$request -> add_request('profileUsername', array($_GET['id'], $_GET['username']));
}
$result  =  $request -> send_request();

?>