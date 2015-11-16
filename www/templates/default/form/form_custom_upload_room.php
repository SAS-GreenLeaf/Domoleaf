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

$iduser = $request -> getId();

$target_dir_abs = "/etc/domoleaf/www/templates/default/custom/room/";
$target_dir = "/templates/default/custom/room/";
$target_file = $target_dir_abs . basename($_FILES["fileToUpload"]["name"]);
$imageFileType = "jpg";
$uploadOk = 0;

if (!empty($_POST['id_elem']) && !empty($rooms->$_POST['id_elem']) && !empty($iduser)){
	$filename = $iduser.'_'.$_POST['id_elem'].'_'.$_SERVER['REQUEST_TIME'].'.'.$imageFileType;
	$target_file = $target_dir_abs.$filename;
	if (empty($_FILES["fileToUpload"]["tmp_name"]) || empty($target_file)) {
		echo 0;
	}
	if (!(move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file))){
		echo 0;
	}
	$compressed = compress_image($target_file, $target_file, 90);
	if (!(rename($compressed, $target_file))){
		echo 0;
	}
	$current_room = $rooms->$_POST['id_elem'];
	if (!empty($current_room->room_bgimg)){
		unlink($target_dir_abs.$current_room->room_bgimg);
	}
	$request =  new Api();
	$request -> add_request('confUserRoomBgimg',
							array($_POST['id_elem'], $filename, $iduser));
	$result  =  $request -> send_request();
	$uploadOk = $target_dir.$filename;
}

echo $uploadOk;
?> 