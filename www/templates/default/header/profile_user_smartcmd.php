<?php 

$request = new Api();
$request -> add_request('profileList');
$request -> add_request('listSmartcmd');
$result  =  $request -> send_request();

$userid = $request->getId();
$listuser = $result->profileList;
$smartcmdList = $result->listSmartcmd;

?>