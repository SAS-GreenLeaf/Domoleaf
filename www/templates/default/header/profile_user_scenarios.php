<?php 

$request = new Api();
//$request -> add_request('profileList');
$request -> add_request('listScenarios');
$result  =  $request -> send_request();

//$userid = $request->getId();
//$listuser = $result->profileList;

$scenarioList = $result->listScenarios;

?>