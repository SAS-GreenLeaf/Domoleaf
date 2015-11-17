<?php

include('profile-menu.php');

echo
'<div id="triggerList">
	<div class="col-xs-offset-2 margin-top center">
		<button class="btn btn-greenleaf" onclick="createTrigger(0)">
			'._('Create New Trigger').'
		</button>
	</div>
	<div class="col-xs-offset-2 margin-top col-xs-10">';
		if (empty($triggersList)) {
			echo
			'<div class="alert alert-warning center col-xs-offset-2 margin-top col-xs-8" role="alert">
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
				'._('No Trigger').'
			</div>';
		}
		else {
			echo '
			<table id="listTriggers" class="table table-bordered table-striped table-condensed">
				<thead>
					<tr>
						<th class="center">'._('Trigger Name').'</th>
						<th class="center">'._('Actions').'</th>
					</tr>
				</thead>
				<tbody>';
				foreach ($triggersList as $elem) {
					echo '
					<tr id="trigger-'.$elem->trigger_id.'">
						<td>'.$elem->name.'</td>
						<td class="center">
							<a href="/profile_user_trigger_events/'.$elem->trigger_id.'/0">
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
			</table>';
		}
		echo '
	</div>
</div>';

echo '
<script type="text/javascript">
	
	$(document).ready(function(){
		ShowScenarios();
		activateMenuElem(\'triggers\');
	});
	
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
			url: "/form/form_remove_trigger.php",
			data: "id_trigger="+trigger_id,
			success: function(result) {
				$("#trigger-"+trigger_id).remove();
				popup_close();
			}
		});
	}

</script>';

?>

