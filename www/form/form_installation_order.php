<?php 

include('header.php');

if (!empty($_GET['floorid']) && !empty($_GET['action'])){
	$request =  new Api();
	$request -> add_request('SetFloorOrder', array($_GET['userid'], $_GET['floorid'], $_GET['action']));
	$result  =  $request -> send_request();
}
else if (!empty($_GET['roomid']) && !empty($_GET['action'])){
	$request =  new Api();
	$request -> add_request('SetRoomOrder', array($_GET['userid'], $_GET['roomid'], $_GET['action']));
	$result  =  $request -> send_request();
}
else if (!empty($_GET['deviceid']) && !empty($_GET['action'])){
	$request =  new Api();
	$request -> add_request('SetDeviceOrder', array($_GET['userid'], $_GET['deviceid'], $_GET['action']));
	$result  =  $request -> send_request();
}

?>