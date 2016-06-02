<?php 

$request =  new Api();
$request -> add_request('mcVisible');
$request -> add_request('confApplicationAll');
$request -> add_request('profileList');
$request -> add_request('profileInfo');
$result  =  $request -> send_request();

echo '<title>'._('Master Command').'</title>';

$listAllVisible = $result->mcVisible;

$deviceallowed = $listAllVisible->ListDevice;
$roomallowed = $listAllVisible->ListRoom;
$floorallowed = $listAllVisible->ListFloor;
$smartcmdLinked = $listAllVisible->ListSmartcmd;
$allapp =  $listAllVisible->ListApp;

$app = $result->confApplicationAll;

$userid = $request->getId();
$listuser = $result->profileList;
$userinfo = $result->profileInfo;
if (!empty($listuser->$userid)) {
	$bg_color = $listuser->{$userid}->bg_color;
	$menus_color = $listuser->{$userid}->border_color;
}
else {
	$bg_color = $userinfo->bg_color;
	$menus_color = $userinfo->border_color;
}
if (empty($bg_color)) {
	$bg_color = "#eee";
	$menus_color = "#f5f5f5";
}

$icons = array(
			1 => 'fa fa-lightbulb-o',
			2 => 'fi flaticon-heating1',
			3 => 'fa fa-bars',
			4 => 'fa fa-bolt',
			5 => 'fi flaticon-snowflake149',
			6 => 'fa fa-volume-up',
			7 => 'fi flaticon-playbutton17',
			8 => 'fa fa-tree',
			9 => 'fi flaticon-winds4',
			10 => 'fa fa-fire',
			11 => 'fi flaticon-wind34',
			12 => 'fi flaticon-person206',
			13 => 'fa fa-video-camera',
			14 => 'fi flaticon-sign35',
			15 => 'fa fa-sort-amount-asc rotate--90',
			17 => 'fa fa-tachometer'
	);

?>