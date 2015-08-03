<?php 

echo '<title>'._('Users configuration').'</title>';


$request =  new Api();
$request -> add_request('profileList');
$result  =  $request -> send_request();

$listuser = $result->profileList;
$currentuser = $request->getId();

?>