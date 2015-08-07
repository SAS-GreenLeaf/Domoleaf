<?php 

echo '<title>'._('Profile').'</title>';

$request =  new Api();
$request -> add_request('profileInfo', Array(''));
$request -> add_request('confUserInstallation');
$request -> add_request('language');
$request -> add_request('design');
$result  =  $request -> send_request();

$profilInfo = $result->profileInfo;
$language = $result->language;

?>