<?php

include('profile-menu.php');

echo '
	<div class="col-xs-offset-2 col-xs-2 bhoechie-tab-menu sidebar">
		<div class="list-group">';
		foreach ($installation_info as $floor) {
			echo '
			<div id="floor-'.$floor->floor_id.'">
				<div href="#" onclick="ShowRoomList('.$floor->floor_id.')" class="floor-scenar cursor">
					&nbsp;
					<i id ="arrow-icon" class="margin-right fa fa-caret-down"></i>
					'.$floor->floor_name.'
				</div>
				<ul id="roomList-'.$floor->floor_id.'" hidden="hidden" class="nav">';
					foreach ($floor->room as $room){
						echo '
							<div id="room-'.$room->room_id.'" class="">
								<div href="#" onclick="ShowDeviceList('.$room->room_id.')" class="box-scenar-rooms cursor">
									'.$room->room_name.'
								</div>
								<ul id="deviceList-'.$room->room_id.'" hidden class="nav">';
									foreach ($room->devices as $device){
										echo '
										<li class="list-item">
											<div href="#" id="device-'.$device->room_device_id.'"
												 class="box-scenar-devices cursor"
												 onclick="selectDevice('.$id_smartcmd.','.$device->room_device_id.')">
												<i class="margin-right '.getIcon($device->device_id).'"></i>
												'.$device->name.'
											</div>
										</li>';
									}
									echo '
								</ul>
							</div>';
					}
					echo '
				</ul>
			</div>';
		}
		echo
		'</div>
	</div>';

echo '
	<div class="col-xs-8 col-xs-offset-4 navbar navbar-inverse navbar-fixed-top save-navbar">
		<div id="navbarSmartcmdName" class="navbar-brand">
			'.$name_smartcmd.'
		</div>
		<button type="button"
		        title="'._('Edit Smartcommand Name').'"
		        class="btn btn-primary"
		        onclick="popupUpdateSmartcmdName('.$id_smartcmd.')">
			<i class="glyphicon glyphicon-edit"></i>
		</button>';
		if ($id_scenario != 0) {
			echo
				'<button type="button"
				        title="'._('Back to Scenario').'"
				        class="btn btn-primary block-right"
				        onclick="redirect(\'/profile_user_scenarios/'.$id_scenario.'/1\')">
					'._('Back to Scenario').'
				</button>';
		}
		echo '
		<div id="linked-room" class="navbar-brand">
			'._('Linked Room').'
			<select class="selectpicker span2" id="selectFloor-'.$id_smartcmd.'" data-size="10"
			        onchange="listRoomsOfFloor('.$id_smartcmd.')">
				<option value="0">'._('No floor selected').'</option>';
				foreach ($installation_info as $floor) {
					echo '<option value="'.$floor->floor_id.'">'.$floor->floor_name.'</option>';
				}
				echo '
			</select>
			<select class="selectpicker span2" id="selectRoom-'.$id_smartcmd.'" data-size="10"
			        onchange="saveLinkedRoom('.$id_smartcmd.')">
				<option value="0">'._('No floor selected').'</option>
			</select>
		</div>
	</div>
	<div id="drop-smartcmd" class="col-xs-8 col-xs-offset-4">
	</div>
	<div class="col-xs-8 col-xs-offset-4 navbar navbar-inverse navbar-fixed-bottom">
		<div class="navbar-brand">
			Options
		</div>
		<div id="optionList" class="navbar-collapse" hidden>
		</div>
	</div>';

echo
'<script type="text/javascript">
	
	$(document).ready(function(){
		displaySmartcmd('.$id_smartcmd.');
	});
	
	function popupUpdateSmartcmdName(smartcmd_id) {
		$.ajax({
			type: "GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_user_update_smartcmd_name.php",
			data: "smartcmd_id="+smartcmd_id,
			success: function(msg) {
				BootstrapDialog.show({
					title: \'<div id="popupTitle" class="center"></div>\',
					message: msg
				});
			}
		});
	}

	function setDroppable() {
		$(".smartcmdElemDrop").droppable({
				drop: function(event, ui) {
					var id_exec;
					var drop_id;
					var room_id_device;
					if ($(ui.draggable).find("div").attr("id")) {
						id_exec = $(ui.draggable).find("div").attr("id").split("smartcmdElem-")[1];
						drop_id = this.id.split("smartcmdElemDrop-")[1];
						changeElemsOrder('.$id_smartcmd.', id_exec, drop_id);
					}
					else {
						room_id_device = $(ui.draggable).attr("id").split("btn-option-")[1];
						dropNewElem('.$id_smartcmd.', ui, room_id_device, this.id.split("smartcmdElemDrop-")[1]);
					}
				},
				accept: \'.btn-draggable, .smartcmdElem\'
			});
	}
	
	function dropNewElem(id_smartcmd, ui, room_id_device, drop_id) {
		var id_option;
		var id_exec;
		without_params = [363, 364, 365, 366, 367, 368];

		id_option = parseInt($(ui.draggable).find("input").val());
		id_exec = parseInt(drop_id) + 1;
		if (without_params.indexOf(id_option) > -1) {
			saveSmartcmdWithoutParam(id_smartcmd, room_id_device, id_option, id_exec)
		}
		else {
			$.ajax({
				type:"GET",
				url: "/templates/default/popup/popup_smartcmd_device_option.php",
				data: "id_smartcmd="+id_smartcmd
						+"&room_id_device="+room_id_device
						+"&id_option="+id_option
						+"&id_exec="+id_exec
						+"&modif="+1,
				success: function(msg) {
					BootstrapDialog.show({
						title: \'<div id="popupTitle" class="center"></div>\',
						message: msg
					});
				}
			});
		}
	}
	
	function onclickDropNewElem(id_smartcmd, room_id_device, id_option, id_exec) {
		$.ajax({
				type:"GET",
				url: "/templates/default/popup/popup_smartcmd_device_option.php",
				data: "id_smartcmd="+id_smartcmd
						+"&room_id_device="+room_id_device
						+"&id_option="+id_option
						+"&id_exec="+id_exec
						+"&modif="+1,
				success: function(msg) {
					BootstrapDialog.show({
						title: \'<div id="popupTitle" class="center"></div>\',
						message: msg
					});
				}
			});
	}

	function changeElemsOrder(id_smartcmd, old_id_exec, drop_id) {
		var new_id_exec;

		new_id_exec = parseInt(drop_id) + 1;
		if (old_id_exec < new_id_exec) {
			new_id_exec = new_id_exec - 1;
		}
		$.ajax({
			type:"GET",
			url: "/templates/default/form/form_smartcmd_elem_order.php",
			data: "id_smartcmd="+id_smartcmd
					+"&old_id_exec="+old_id_exec
					+"&new_id_exec="+new_id_exec,
			beforeSend:function(result, status){
				PopupLoading();
			},
			success: function(result) {
				displaySmartcmd(id_smartcmd);
				popup_close();
			}
		});
	}

	function getDeviceOptions(room_id_device, id_smartcmd) {
		if (room_id_device > 0) {
			$.ajax({
				type: "GET",
				url: "/templates/default/form/form_user_smartcmd_opt_list.php",
				data: "room_id_device="+room_id_device
				       +"&id_smartcmd="+id_smartcmd,
				success: function(result) {
					$("#optionList").html(result);
				}
			});
		}
	}
		
	function displaySmartcmd(id_smartcmd) {
		$.ajax({
			type: "GET",
			url: "/templates/default/form/form_display_smartcmd.php",
			data: "id_smartcmd="+id_smartcmd,
			success: function(result) {
				$("#drop-smartcmd").html(result);
				setDroppable();
				if ('.$smartcmd_infos->floor_id.' != 0 && '.$smartcmd_infos->room_id.' != 0) {
					setLinkedRoom('.$smartcmd_infos->floor_id.', '.$smartcmd_infos->room_id.');
				}
				openDivs('.$smartcmd_infos->floor_id.', '.$smartcmd_infos->room_id.');
			}
		});
	}
	
	function PopupRemoveSmartcmdElem(exec_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_remove_smartcmd_elem.php",
			data: "exec_id="+exec_id,
			success: function(result) {
				BootstrapDialog.show({
					title: "'._('Delete Smartcommand Elem').'",
					message: result
				});
			}
		});
	}
	
	function RemoveSmartcmdElem(exec_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_remove_smartcmd_elem.php",
			data: "id_exec="+exec_id+"&id_smartcmd="+'.$id_smartcmd.',
			success: function(result) {
				displaySmartcmd('.$id_smartcmd.');
				popup_close();
			}
		});
	}
						
	function setLinkedRoom(floor_id, room_id) {
		$("#selectFloor-'.$id_smartcmd.'").selectpicker(\'val\', floor_id);
		listRoomsOfFloor('.$id_smartcmd.');
		setTimeout(function(){
						$("#selectRoom-'.$id_smartcmd.'").selectpicker(\'val\', room_id);
					}, 500);
	}
	
</script>';

?>