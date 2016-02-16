<?php 

$request =  new Api();
$request -> add_request('profileList');
$request -> add_request('profileTime');
$request -> add_request('confMenuProtocol');
$result  =  $request -> send_request();

echo '<title>'._('Users configuration').'</title>';

$listuser = $result->profileList;
$time = $result->profileTime;
$currentuser = $request->getId();
$menuProtocol= $result->confMenuProtocol;

?>