<?php 

$request =  new Api();
$request -> add_request('confUserInstallation');
$request -> add_request('profileList');
$request -> add_request('profileInfo');
$result  =  $request -> send_request();

echo '<title>'._('Profile').'</title>';

$userid = $request->getId();
$listuser = $result->profileList;
$userinfo = $result->profileInfo;
if (!empty($listuser->$userid)) {
	$bg_color = $listuser->$userid->bg_color;
}
else {
	$bg_color = $userinfo->bg_color;
}
if (empty($bg_color)) {
	$bg_color = "#eee";
}

$accordioninfo = $result->confUserInstallation;

?>