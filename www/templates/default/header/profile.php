<?php 

$request =  new Api();
$request -> add_request('profileInfo', Array(''));
$request -> add_request('confUserInstallation');
$request -> add_request('language');
$request -> add_request('design');
$result  =  $request -> send_request();

echo '<title>'._('Profile').'</title>';

$profilInfo = $result->profileInfo;
$language = $result->language;

?>