<?php 

echo '<title>'._('Users permission').'</title>';

if (!empty($_GET['userid'])){
	
	$request =  new Api();
	$request -> add_request('confUserInstallation', array($_GET['userid']));
	$request -> add_request('profileList');
	$request -> add_request('profileInfo');
	$result  =  $request -> send_request();
	
	$userid = $_GET['userid'];
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

}

?>