<?php 

if (!empty($_GET['userid'])){
	
	$request =  new Api();
	$request -> add_request('confUserInstallation', array($_GET['userid']));
	$request -> add_request('profileList');
	$request -> add_request('profileInfo');
	$request -> add_request('confMenuProtocol');
	$result  =  $request -> send_request();
	
	$userid = $_GET['userid'];
	$listuser = $result->profileList;
	$userinfo = $result->profileInfo;
	if (!empty($listuser->$userid)) {
		$bg_color = $listuser->$userid->bg_color;
		$menus_color = $listuser->$userid->border_color;
	}
	else {
		$bg_color = $userinfo->bg_color;
		$menus_color = $userinfo->border_color;
	}
	if (empty($bg_color)) {
		$bg_color = "#eee";
		$menus_color = "#f5f5f5";
	}
	$accordioninfo = $result->confUserInstallation;

}
else {
	$request =  new Api();
	$request -> add_request('confMenuProtocol');
	$result  =  $request -> send_request();
}

$menuProtocol = $result->confMenuProtocol;

echo '<title>'._('Users permission').'</title>';

?>