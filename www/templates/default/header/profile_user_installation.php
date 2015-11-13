<?php 

echo '<title>'._('Profile').'</title>';


$request =  new Api();
$request -> add_request('confUserInstallation');
$request -> add_request('profileList');
$request -> add_request('profileInfo');
$result  =  $request -> send_request();

$userid = $request->getId();
$listuser = $result->profileList;
$userinfo = $result->profileInfo;
$bg_color = $userinfo->bg_color;
if (empty($bg_color)) {
	$bg_color = "#eee";
}

$accordioninfo = $result->confUserInstallation;

?>