<?php 

echo '<title>'._('Profile').'</title>';


$request =  new Api();
$request -> add_request('confUserInstallation');
$request -> add_request('profileList');
$result  =  $request -> send_request();

$userid = $request->getId();
$listuser = $result->profileList;
$accordioninfo = $result->confUserInstallation;

?>