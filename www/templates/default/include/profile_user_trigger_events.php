<?php

include('profile-menu.php');

echo
'<div id="triggerList">
	<div class="col-xs-offset-2 margin-top center">
		<button class="btn btn-greenleaf" onclick="createTrigger()">
			'._('Create New Trigger').'
		</button>
	</div>
	<div class="col-xs-offset-2 margin-top col-xs-10">
		<table id="listTriggers" class="table table-bordered table-striped table-condensed">
			<thead>
				<tr>
					<th class="center">'._('Trigger Name').'</th>
					<th class="center">'._('Linked Smartcommand').'</th>
					<th class="center">'._('Status').'</th>
					<th class="center">'._('Actions').'</th>
				</tr>
			</thead>
			<tbody>';
			foreach ($triggersList as $elem) {
				echo '
				<tr id="trigger-'.$elem->trigger_id.'">
					<td>'.$elem->name.'</td>
					<td>'.$elem->smartcmd_name.'</td>
					<td class="center">
						<input data-on-color="greenleaf"
						       data-label-width="0"
						       data-on-text="'._('On').'"
						       data-off-text="'._('Off').'"
						       id="trigger-state-'.$elem->trigger_id.'"
						       type="checkbox"
						       onchange="changeTriggerState('.$elem->trigger_id.')" ';
						if ($elem->activated == 1) {
							echo 'checked';
						}
					echo '>
						<script type="text/javascript">
							$("#trigger-state-'.$elem->trigger_id.'").bootstrapSwitch();
						</script>
					</td>
					<td class="center">
						<a href="/profile_user_trigger_events/'.$elem->trigger_id.'">
							<button type="button"
							        title="'._('Edit Trigger').'"
							        class="btn btn-primary">
								<i class="glyphicon glyphicon-edit"></i>
							</button>
						</a>
						<button type="button"
						        title="'._('Delete Trigger').'"
						        class="btn btn-danger"
						        onclick="PopupRemoveTrigger('.$elem->trigger_id.')">
							<i class="fa fa-trash-o"></i>
						</button>
					</td>
				</tr>';
				}
				echo '
			</tbody>
		</table>
	</div>
</div>';

echo '
<script type="text/javascript">
	
	function createTrigger() {
		$.ajax({
			type: "GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_user_create_trigger.php",
			success: function(msg) {
				BootstrapDialog.show({
					title: \'<div id="popupTitle" class="center"></div>\',
					message: msg
				});
			}
		});
	}
				
	function PopupRemoveTrigger(trigger_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_remove_trigger.php",
			data: "id_trigger="+trigger_id,
			success: function(result) {
				BootstrapDialog.show({
					title: "'._('Delete Trigger').'",
					message: result
				});
			}
		});
	}
				
	function RemoveTrigger(trigger_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_remove_trigger.php",
			data: "id_trigger="+trigger_id,
			success: function(result) {
				$("#trigger-"+trigger_id).remove();
				popup_close();
			}
		});
	}

	function changeTriggerState(trigger_id) {
		state = $("#trigger-state-"+trigger_id).bootstrapSwitch(\'state\');
		if (state) {
			state = "1";
		}
		else {
			state = "0";
		}
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_change_trigger_state.php",
			data: "id_trigger="+trigger_id
					+"&trigger_state="+state,
			success: function(result) {
			}
		});
	}
</script>';

?>

