<?php

include('header.php');

if (!empty($_GET['id_scenario'])) {
	$id_scenario = $_GET['id_scenario'];
	
	$request = new Api();
	$request -> add_request('getScenario', array($id_scenario));
	$result  =  $request->send_request();
	
	$scenario_infos = $result->getScenario;
	
	echo showScenarioSummary($scenario_infos->name_scenario, $scenario_infos->id_smartcmd,
							$scenario_infos->id_trigger, $scenario_infos->id_schedule, $id_scenario);
}
else {
	$request =  new Api();
	$result  =  $request -> send_request();
	echo '';
}

function showScenarioSummary($name, $id_smartcmd, $id_trigger, $id_schedule, $id_scenario) {
	$request_ok = 0;
	
	$request = new Api();
	if ($id_trigger != 0) {
		$request -> add_request('searchTriggerById', array($id_trigger));
		$request_ok = 1;
	}
	if ($id_schedule != 0) {
		$request -> add_request('searchScheduleById', array($id_schedule));
		$request_ok = 1;
	}
	if ($id_smartcmd != 0) {
		$request -> add_request('searchSmartcmdById', array($id_smartcmd));
		$request_ok = 1;
	}
	if ($request_ok == 1) {
		$result  =  $request->send_request(); 
	}
	$display =
				'<div class="inline">
					<h4 id="summaryScenarioName" class="block-left">
						'.$name.'
					</h4>
					<button type="button"
					        title="'._('Edit Scenario Name').'"
					        class="btn btn-primary margin-left"
					        onclick="popupUpdateScenarioName('.$id_scenario.')">
						<i class="glyphicon glyphicon-edit"></i>
					</button>
				</div>
				</br>';
	if ($id_smartcmd == 0) {
		$display.=
					'<div class="alert alert-danger" role="alert">
						'._('You must select a smartcommand.').'
					</div></br>';
	}
	else {
		$display.=
					'<div class="alert alert-success" role="alert">
						'._('Smartcommand : ').$result->searchSmartcmdById->name.'
					</div></br>';
	}
	if ($id_trigger == 0) {
		$display.=
					'<div class="alert alert-warning" role="alert">
						'._('No Trigger Selected').'
					</div></br>';
	}
	else {
		$display.=
					'<div class="alert alert-success" role="alert">
						'._('Trigger : ').$result->searchTriggerById->trigger_name.'
					</div></br>';
	}
	
	if ($id_schedule == 0) {
		$display.=
					'<div class="alert alert-warning" role="alert">
						'._('No Schedule Selected (All Time)').'
					</div></br>';
	}
	else {
		$display.=
					'<div class="alert alert-success" role="alert">
						'._('Schedule : ').$result->searchScheduleById->schedule_name.'
					</div></br>';
	}
	
	if ($id_trigger == 0 && $id_schedule == 0) {
		$display.=
					'<div class="alert alert-danger" role="alert">
						'._('You must select at least one trigger or one schedule.').'
					</div></br>';
	}
	
	$display.=
				'<script type="text/javascript">
					$(document).ready(function(){
						if ('.$id_trigger.' != 0 || '.$id_schedule.' != 0) {
							$("#completeScenarioBtn").removeAttr(\'disabled\');
						}
						else {
							$("#completeScenarioBtn").attr(\'disabled\', "");
						}
					});
				</script>';
	return $display;
}
?>