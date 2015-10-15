<?php 

include('header.php');

if (!empty($_GET['idroomdevice']) && !empty($_GET['devname']) && !empty($_GET['addr']) && !empty($_GET['iddevice'])){
	$request =  new Api();
	$request -> add_request('confDeviceSaveInfo', array($_GET['idroomdevice'], $_GET['devname'], $_GET['daemon'], $_GET['addr'], $_GET['iddevice'], $_GET['port'], $_GET['login'], $_GET['pass']));
	$result  =  $request -> send_request();
}
else if (!empty($_GET['idroomdevice']) && !empty($_GET['opt'])){
	$request =  new Api();
	$options = array(
		'id' => $_GET['opt'],
		'status' => $_GET['status'],
		'addr' => $_GET['addr'],
		'addr_plus' => $_GET['addr_plus'],
		'dpt_id' => $_GET['dpt_id']
	);
	$request -> add_request('confDeviceSaveOption', array($_GET['idroomdevice'], $options));
	$result  =  $request -> send_request();
}

?>