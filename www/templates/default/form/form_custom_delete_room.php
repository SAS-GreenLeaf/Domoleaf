<?php 

include('header.php');

$request =  new Api();
$request -> add_request('mcAllowed');
$request -> add_request('confUserRoomEnable');
$result  =  $request -> send_request();

if (empty($result -> confUserRoomEnable) || sizeof($result -> confUserRoomEnable) == 0) {
	$listAllVisible = $result->mcAllowed;
	$rooms = $listAllVisible->ListRoom;
}
else {
	$rooms = $result->confUserRoomEnable;
}

$target_dir = "/etc/domoleaf/www/templates/default/custom/room/";
$deleteOk = 1;

if (!empty($_POST["room"]) && !empty($rooms->$_POST["room"])){
	
	$current_room = $rooms->$_POST['room'];
	if (!empty($current_room->room_bgimg)){
		unlink($target_dir.$current_room->room_bgimg);
	}
	$request =  new Api();
	$request -> add_request('confUserRoomBgimg', array($_POST['room']));
	$result  =  $request -> send_request();
}
else {
	$deleteOk = 0;
}

echo $deleteOk;
?> 