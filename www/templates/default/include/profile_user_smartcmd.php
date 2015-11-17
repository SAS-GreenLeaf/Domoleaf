<?php

include('profile-menu.php');
include('templates/'.TEMPLATE.'/function/display_widget.php');

echo '
<div id="editSmartcmd">
	<div class="col-xs-offset-2 margin-top center">
		<button class="btn btn-greenleaf" onclick="createSmartcmd(0)">
			'._('Create New SmartCommand').'
		</button>
	</div>
	<div class="col-xs-offset-2 margin-top col-xs-10">';
	if (empty($smartcmdList)) {
		echo
		'<div class="alert alert-warning center col-xs-offset-2 margin-top col-xs-8" role="alert">
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
			'._('No Smartcommand').'
		</div>';
	}
	else {
		echo '
		<table id="listSmartcmd" class="table table-bordered table-striped table-condensed">
			<thead>
				<tr>
					<th class="center">'._('Smartcommand Name').'</th>
					<th class="center">'._('Room').'</th>
					<th class="center">'._('Actions').'</th>
				</tr>
			</thead>
			<tbody>';
			foreach ($smartcmdList as $elem) {
				echo '
				<tr id="smartcmd-'.$elem->smartcommand_id.'">
					<td>'.$elem->name.'</td>';
					if (empty($elem->room_name)) {
						$elem->room_name = _('None');
					}
					echo '
					<td>'.$elem->room_name.'</td>
					<td class="center">
						<a href="/profile_user_smartcmd/'.$elem->smartcommand_id.'/0">
							<button type="button"
							        title="'._('Edit Smartcommand').'"
							        class="btn btn-primary">
								<i class="glyphicon glyphicon-edit"></i>
							</button>
						</a>
						<button type="button"
						        title="'._('Delete Smartcommand').'"
						        class="btn btn-danger"
						        onclick="PopupRemoveSmartcmd('.$elem->smartcommand_id.')">
							<i class="fa fa-trash-o"></i>
						</button>
					</td>
				</tr>';
			}
			echo '
			</tbody>
		</table>';
	}
	echo'
	</div>
</div>';

echo '
<script type="text/javascript">

	$(document).ready(function(){
		ShowScenarios();
		activateMenuElem(\'smartcmds\');
	});
	
	function PopupRemoveSmartcmd(smartcmd_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_remove_smartcmd.php",
			data: "id_smartcmd="+smartcmd_id,
			success: function(result) {
				BootstrapDialog.show({
					title: "'._('Delete Smartcommand Elem').'",
					message: result
				});
			}
		});
	}
	
	function RemoveSmartcmd(smartcmd_id) {
		$.ajax({
			type:"GET",
			url: "/form/form_remove_smartcmd.php",
			data: "id_smartcmd="+smartcmd_id,
			success: function(result) {
				$("#smartcmd-"+smartcmd_id).remove();
				popup_close();
			}
		});
	}
</script>';

?>

