<?php

include('header.php');

if (!empty($_GET['id_scenario'])) {
	$id_scenario = $_GET['id_scenario'];
	
	$request = new Api();
	$request -> add_request('getScenario', array($id_scenario));
	$result  =  $request->send_request();
	
	$scenario_infos = $result->getScenario;
	
	echo showScenarioSummary($scenario_infos->name_scenario, $scenario_infos->id_smartcmd,
							$scenario_infos->id_trigger, $scenario_infos->id_schedule);
}
else {
	echo '';
}

function showScenarioSummary($name, $id_smartcmd, $id_trigger, $id_schedule) {
	
	$request = new Api();
	if ($id_trigger != 0) {
		$request -> add_request('searchTriggerById', array($id_trigger));
	}
	if ($id_schedule != 0) {
		$request -> add_request('searchScheduleById', array($id_schedule));
	}
	$request -> add_request('searchSmartcmdById', array($id_smartcmd));
	$result  =  $request->send_request(); 
	
	$display =
				'<h4>
					'._('Scenario Name : ').$name.'</br>
				</h4></br>';
	$display.=
				'<div class="alert alert-success" role="alert">
					'._('Smartcommand : ').$result->searchSmartcmdById->name.'
				</div></br>';
	
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