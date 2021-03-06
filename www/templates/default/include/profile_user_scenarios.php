<?php

include('profile-menu.php');

echo '
<div id="editScenario">
	<div class="col-xs-offset-2 margin-top center">
		<button class="btn btn-greenleaf" onclick="createScenario()">
			'._('Create New Scenario').'
		</button>
		<p class="margin-top">'._('Scenario will launch a Smartcommand depending on an Event (Trigger) and/or a Time period (Schedule).').'</p>
	</div>
	<div class="col-xs-offset-2 col-xs-10">';
	if (empty($scenarioList)) {
		echo
		'<div class="alert alert-warning center col-xs-offset-2 margin-top col-xs-8" role="alert">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			'._('No Scenario').'
		</div>';
	}
	else {
		echo '
		<table id="listscenario" class="table table-bordered table-striped table-condensed">
			<thead>
				<tr>
					<th class="center">'._('Scenario Name').'</th>
					<th class="center">'._('Smartcommand Linked').'</th>
					<th class="center">'._('Status').'</th>
					<th class="center">'._('Actions').'</th>
				</tr>
			</thead>
			<tbody>';
			foreach ($scenarioList as $elem) {
				echo '
					<tr id="scenario-'.$elem->scenario_id.'">
						<td>
							<a href="/profile_user_scenarios/'.$elem->scenario_id.'/1">'.
								$elem->name.'
							</a>
						</td>
						<td>';
						if ($elem->id_smartcmd != 0) {
							echo 
							'<a href="/profile_user_smartcmd/'.$elem->id_smartcmd.'/'.$elem->scenario_id.'">'.
								$elem->name_smartcmd.'
							</a>';
						}
						else {
							echo 'None';
						}
						echo '
						</td>
						<td class="center">';
							if ($elem->complete == 0) {
								echo '
								<a href="/profile_user_scenarios/'.$elem->scenario_id.'/1">
									<button type="button"
									        title="'._('Edit scenario').'"
									        class="btn btn-warning">
										<i class="glyphicon glyphicon-warning-sign"></i>
									</button>
								</a>';
							}
							else {
								echo '
								<input data-on-color="greenleaf"
								       data-label-width="0"
								       data-on-text="'._('On').'"
								       data-off-text="'._('Off').'"
								       id="scenario-state-'.$elem->scenario_id.'"
								       type="checkbox"
								       onchange="changeScenarioState('.$elem->scenario_id.')" ';
								       if ($elem->activated == 1) {
								       		echo 'checked';
								       }
								       echo '>
								<script type="text/javascript">
									$("#scenario-state-'.$elem->scenario_id.'").bootstrapSwitch();
								</script>';
							}
							echo '
						</td>
						<td class="center">
							<a href="/profile_user_scenarios/'.$elem->scenario_id.'/1">
								<button type="button"
								        title="'._('Edit scenario').'"
								        class="btn btn-primary">
									<i class="glyphicon glyphicon-edit"></i>
								</button>
							</a>
							<button type="button"
							        title="'._('Delete Scenario').'"
							        class="btn btn-danger"
							        onclick="PopupRemoveScenario('.$elem->scenario_id.')">
								<i class="fa fa-trash-o"></i>
							</button>
						</td>
					</tr>';
				}
				echo '
			</tbody>
		</table>';
		}
		echo '
	</div>
</div>';

echo '
<script type="text/javascript">
	
	$(document).ready(function(){
		ShowScenarios();
		activateMenuElem(\'scenarios\');
	});
	
	function createScenario() {
		$.ajax({
			type: "GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_user_create_scenario.php",
			success: function(msg) {
				BootstrapDialog.show({
					title: \'<div id="popupTitle" class="center"></div>\',
					message: msg
				});
			}
		});
	}

	function PopupRemoveScenario(scenario_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_remove_scenario.php",
			data: "id_scenario="+scenario_id,
			success: function(result) {
				BootstrapDialog.show({
					title: "'._('Delete Scenario').'",
					message: result
				});
			}
		});
	}
	
	function RemoveScenario(scenario_id) {
		$.ajax({
			type:"GET",
			url: "/form/form_remove_scenario.php",
			data: "id_scenario="+scenario_id,
			success: function(result) {
				$("#scenario-"+scenario_id).remove();
				popup_close();
			}
		});
	}
					
	function changeScenarioState(scenario_id) {
		state = $("#scenario-state-"+scenario_id).bootstrapSwitch(\'state\');
		if (state) {
			state = "1";
		}
		else {
			state = "0";
		}
		$.ajax({
			type:"GET",
			url: "/form/form_change_scenario_state.php",
			data: "id_scenario="+scenario_id
					+"&scenario_state="+state,
			success: function(result) {
			}
		});
	}
</script>';

?>

