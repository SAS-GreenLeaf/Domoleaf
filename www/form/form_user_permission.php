<?php 

include('header.php');

if (empty($_GET['status'])){
	$_GET['status'] = 0;
}

if (!empty($_GET['userid']) && !empty($_GET['vdeviceid'])){
	$request =  new Api();
	$request -> add_request('confUserVisibleDevice', array($_GET['userid'], $_GET['vdeviceid'], $_GET['status']));
	$result  =  $request -> send_request();
}
else if (!empty($_GET['userid']) && !empty($_GET['vroomid'])){
	$request =  new Api();
	$request -> add_request('confUserVisibleRoom', array($_GET['userid'], $_GET['vroomid'], $_GET['status']));
	$result  =  $request -> send_request();
}
else if (!empty($_GET['userid']) && !empty($_GET['vfloorid'])){
	$request =  new Api();
	$request -> add_request('confUserVisibleFloor', array($_GET['userid'], $_GET['vfloorid'], $_GET['status']));
	$result  =  $request -> send_request();
}

if (empty($_GET['allow'])){
	$_GET['allow'] = 0;	
}

if (!empty($_GET['userid']) && !empty($_GET['deviceid'])){
	$request =  new Api();
	$request -> add_request('confUserPermissionDevice', array($_GET['userid'], $_GET['deviceid'], $_GET['allow']));
	$result  =  $request -> send_request();
}
else if (!empty($_GET['userid']) && !empty($_GET['roomid'])){
	$request =  new Api();
	$request -> add_request('confUserPermissionRoom', array($_GET['userid'], $_GET['roomid'], $_GET['allow']));
	$result  =  $request -> send_request();
}
else if (!empty($_GET['userid']) && !empty($_GET['floorid'])){
	$request =  new Api();
	$request -> add_request('confUserPermissionFloor', array($_GET['userid'], $_GET['floorid'], $_GET['allow']));
	$result  =  $request -> send_request();
}


?>