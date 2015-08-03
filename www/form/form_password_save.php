<?php

include('header.php');

$request =  new Api();
$request -> add_request('passwordRename', array($_GET['old'], $_GET['password'], $_GET['id']));
$result  =  $request -> send_request();

echo $result->passwordRename;

?>