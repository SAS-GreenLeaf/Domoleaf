<?php 

session_start();
session_unset();
session_destroy();

include('./header.php');

$request =  new Api();
$request -> add_request('disconnect');
$result  =  $request -> send_request();
setcookie('token', '', -1, '/');

?>