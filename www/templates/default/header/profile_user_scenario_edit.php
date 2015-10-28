<?php 

if (empty($_GET['id_scenario']) || empty($_GET['step'])) {
	redirect();
}

$step = $_GET['step'];
$id_scenario = $_GET['id_scenario'];


$request = new Api();
$request -> add_request('getScenario', array($id_scenario));
$request -> add_request('listSmartcmd');
$request -> add_request('listTriggers');
$request -> add_request('listSchedules');
$result  =  $request -> send_request();

$scenario_info = $result->getScenario;
$smartcmdList = $result->listSmartcmd;
$triggerList = $result->listTriggers;
$scheduleList = $result->listSchedules;

?>