<?php 

include('./header.php');

$request =  new Api();
$request -> add_request('disconnect');
$result  =  $request -> send_request();
setcookie('token', '', -1, '/');

?>