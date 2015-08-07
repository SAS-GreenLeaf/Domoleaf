<?php 

if (!empty($_GET['userid'])){
	
	$request =  new Api();
	$request -> add_request('confUserInstallation', array($_GET['userid']));
	$request -> add_request('profileList');
	$result  =  $request -> send_request();
	
	$userid = $_GET['userid'];
	$listuser = $result->profileList;
	$accordioninfo = $result->confUserInstallation;

}

?>