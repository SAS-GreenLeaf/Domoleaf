<?php

include('profile-menu.php');

echo '
	<div class="col-xs-10 col-xs-offset-2 navbar navbar-inverse navbar-fixed-top save-navbar">
		<div id="navbarScheduleName" class="navbar-brand">
			'.$name_schedule.'
		</div>
		<button type="button"
		        title="'._('Edit schedule Name').'"
		        class="btn btn-primary"
		        onclick="popupUpdateScheduleName('.$id_schedule.')">
			<i class="glyphicon glyphicon-edit"></i>
		</button>';
		if ($id_scenario != 0) {
			echo
				'<button type="button"
				        title="'._('Back to Scenario').'"
				        class="btn btn-primary block-right"
				        onclick="redirect(\'/profile_user_scenarios/'.$id_scenario.'/3\')">
					'._('Back to Scenario').'
				</button>';
		}
		echo '
		<button type="button"
		        title="'._('Save Schedule').'"
		        class="btn btn-primary block-right"
		        id="saveTS_btn"
		        onclick="SaveSchedule('.$id_schedule.')">
			'._('Save').'
		</button>
	</div>
	<div id="selectSchedule-'.$id_schedule.'" class="col-xs-10 col-xs-offset-2 selectSchedule">
	</div>';


echo
'<script type="text/javascript">
	
	$(document).ready(function(){
		displaySchedule('.$id_schedule.');
		ShowScenarios();
		activateMenuElem(\'schedules\');
	});

	function popupUpdateScheduleName(schedule_id) {
		$.ajax({
			type: "GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_user_update_schedule_name.php",
			data: "schedule_id="+schedule_id,
			success: function(msg) {
				BootstrapDialog.show({
					title: \'<div id="popupTitle" class="center"></div>\',
					message: msg
				});
			}
		});
	}
					
	function displaySchedule(schedule_id) {
		$.ajax({
			type: "GET",
			url: "/templates/'.TEMPLATE.'/form/form_display_schedule.php",
			data: "schedule_id="+schedule_id,
			success: function(result) {
				$("#selectSchedule-"+schedule_id).html(result);
			}
		});
	}

</script>';
?>