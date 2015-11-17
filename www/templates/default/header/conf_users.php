<?php 

$request =  new Api();
$request -> add_request('profileList');
$result  =  $request -> send_request();

echo '<title>'._('Users configuration').'</title>';

$listuser = $result->profileList;
$currentuser = $request->getId();

?>