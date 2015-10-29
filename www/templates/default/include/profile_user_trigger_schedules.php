<?php

include('profile-menu.php');

echo
'<div id="scheduleList">
	<div class="col-xs-offset-2 margin-top center">
		<button class="btn btn-greenleaf" onclick="createSchedule(0)">
			'._('Create New Schedule').'
		</button>
	</div>
	<div class="col-xs-offset-2 margin-top col-xs-10">
		<table id="listSchedules" class="table table-bordered table-striped table-condensed">
			<thead>
				<tr>
					<th class="center">'._('Schedule Name').'</th>
					<th class="center">'._('Actions').'</th>
				</tr>
			</thead>
			<tbody>';
			foreach ($schedulesList as $elem) {
				echo '
				<tr id="schedule-'.$elem->schedule_id.'">
					<td>'.$elem->name.'</td>
					<td class="center">
						<a href="/profile_user_trigger_schedules/'.$elem->schedule_id.'">
							<button type="button"
							        title="'._('Edit schedule').'"
							        class="btn btn-primary">
								<i class="glyphicon glyphicon-edit"></i>
							</button>
						</a>
						<button type="button"
						        title="'._('Delete schedule').'"
						        class="btn btn-danger"
						        onclick="PopupRemoveSchedule('.$elem->schedule_id.')">
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
	
	$(document).ready(function(){
		ShowScenarios();
		activateMenuElem(\'menu-schedules\');
	});
	
	function PopupRemoveSchedule(schedule_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_remove_schedule.php",
			data: "id_schedule="+schedule_id,
			success: function(result) {
				BootstrapDialog.show({
					title: "'._('Delete schedule').'",
					message: result
				});
			}
		});
	}
				
	function RemoveSchedule(schedule_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_remove_schedule.php",
			data: "id_schedule="+schedule_id,
			success: function(result) {
				$("#schedule-"+schedule_id).remove();
				popup_close();
			}
		});
	}
</script>';

?>

