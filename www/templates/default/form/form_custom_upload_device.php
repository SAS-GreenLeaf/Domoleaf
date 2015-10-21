<?php 

include('header.php');

if (empty($_POST['userid'])) {
	$_POST['userid'] = 0;
}
$request =  new Api();
$request -> add_request('mcAllowed');
$request -> add_request('confUserDeviceEnable', array($_POST['userid']));
$request -> add_request('profileList');
$result  =  $request -> send_request();

$userList = $result->profileList;

if (empty($result -> confUserDeviceEnable) || sizeof($result -> confUserDeviceEnable) == 0) {
	$listAllVisible = $result->mcAllowed;
	$devices = $listAllVisible->ListDevice;
}
else {
	$devices = $result->confUserDeviceEnable;
}

$iduser = $_POST['userid'];
if (empty($iduser) || empty($userList->$iduser)) {
	$iduser = $request -> getId();
}

$target_dir_abs = "/etc/domoleaf/www/templates/default/custom/device/";
$target_dir = "/templates/default/custom/device/";
$uploadOk = 1;
$target_file = $target_dir_abs . basename($_FILES["file"]["name"]);
$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

// Check if image file is a actual image or fake image
if(isset($_POST["submit"])) {
	$check = getimagesize($_FILES["file"]["tmp_name"]);
	if($check !== false) {
		$uploadOk = 1;
	} else {
		$uploadOk = 0;
	}
}

// Check file size
if ($_FILES["file"]["size"] > 2000000) {
	$uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg") {
	$uploadOk = 0;
}

$imageFileType = "jpg";

function compress_image($src, $dest , $quality)
{
	$info = getimagesize($src);

	if ($info['mime'] == 'image/jpeg')
	{
		$image = imagecreatefromjpeg($src);
	}
	elseif ($info['mime'] == 'image/png')
	{
		$image = imagecreatefrompng($src);
	}
	else
	{
		return null;
	}

	//compress and save file to jpg
	imagejpeg($image, $dest, $quality);

	//return destination file
	return $dest;
}

// Check if $uploadOk is set to 0 by an error
// if everything is ok, try to upload file
if ($uploadOk == 1) {
	if (!empty($_POST['device']) && !empty($devices->$_POST['device']) && !empty($iduser)){
		$filename = $iduser.'_'.$_POST['device'].'_'.$_SERVER['REQUEST_TIME'].'.'.$imageFileType;
		$target_file = $target_dir_abs.$filename;
		
		if (!(move_uploaded_file($_FILES["file"]["tmp_name"], $target_file))){
			$uploadOk = 0;
		}
		else {
			$compressed = compress_image($target_file, $target_file, 90);
			if (!(rename($compressed, $target_file))){
				$uploadOk = 0;
			}
			else {
				$current_device = $devices->$_POST['device'];
				if (!empty($current_device->device_bgimg)){
					unlink($target_dir_abs.$current_device->device_bgimg);
				}
				$request =  new Api();
				$request -> add_request('confUserDeviceBgimg',
										array($_POST['device'], $filename, $iduser));
				$result  =  $request -> send_request();
				$uploadOk = $target_dir.$filename;
			}
		}
	}
	else {
		$uploadOk = 0;
	}
}

echo $uploadOk;
?> 