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
												 onclick="selectDevice('.$id_trigger.','.$device->room_device_id.')">
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
		<div id="navbarTriggerName" class="navbar-brand">
			'.$name_trigger.'
		</div>
		<button type="button"
		        title="'._('Edit Trigger Name').'"
		        class="btn btn-primary"
		        onclick="popupUpdateTriggerName('.$id_trigger.')">
			<i class="glyphicon glyphicon-edit"></i>
		</button>
		<div id="linked-smartcmd" class="navbar-brand">
			'._('Linked Smartcommand').'
			<select class="selectpicker span2" id="selectSmartcmd-'.$id_trigger.'" data-size="10">';
				foreach ($smartcmdList as $smartcmd) {
					echo '<option value="'.$smartcmd->smartcommand_id.'">'.$smartcmd->name.'</option>';
				}
				echo '
			</select>
			<button id="saveLS_btn"
			        onclick="saveLinkedSmartcmd('.$id_trigger.')"
			        class="btn btn-primary">
				'._('Save').'
			</button> 
		</div>
	</div>
	<div id="drop-conditions" class="col-xs-8 col-xs-offset-4">
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
	
	displayTrigger('.$id_trigger.');
	
	function popupUpdateTriggerName(trigger_id) {
		$.ajax({
			type: "GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_user_update_trigger_name.php",
			data: "trigger_id="+trigger_id,
			success: function(msg) {
				BootstrapDialog.show({
					title: \'<div id="popupTitle" class="center"></div>\',
					message: msg
				});
			}
		});
	}

	function setDroppable() {
		$(".triggerElemDrop").droppable({
				drop: function(event, ui) {
					var id_exec;
					var drop_id;
					var room_id_device;
					if ($(ui.draggable).find("div").attr("id")) {
						id_exec = $(ui.draggable).find("div").attr("id").split("triggerElem-")[1];
						drop_id = this.id.split("triggerElemDrop-")[1];
						changeElemsOrder('.$id_trigger.', id_exec, drop_id);
					}
					else {
						room_id_device = $(ui.draggable).attr("id").split("btn-option-")[1];
						dropNewElem('.$id_trigger.', ui, room_id_device, this.id.split("triggerElemDrop-")[1]);
					}
				},
				accept: \'.btn-draggable, .triggerElem\'
			});
	}
	
	function dropNewElem(id_trigger, ui, room_id_device, drop_id) {
		var id_option;
		var id_condition;
		without_params = [363, 364, 365, 366, 367, 368];

		id_option = parseInt($(ui.draggable).find("input").val());
		id_condition = parseInt(drop_id) + 1;
		if (without_params.indexOf(id_option) > -1) {
			saveTriggerWithoutParam(id_trigger, room_id_device, id_option, id_condition)
		}
		else {
			$.ajax({
				type:"GET",
				url: "/templates/default/popup/popup_trigger_device_option.php",
				data: "id_trigger="+id_trigger
						+"&room_id_device="+room_id_device
						+"&id_option="+id_option
						+"&id_condition="+id_condition
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
	
	function onclickDropNewElem(id_trigger, room_id_device, id_option, id_condition) {
		$.ajax({
				type:"GET",
				url: "/templates/default/popup/popup_trigger_device_option.php",
				data: "id_trigger="+id_trigger
						+"&room_id_device="+room_id_device
						+"&id_option="+id_option
						+"&id_condition="+id_condition
						+"&modif="+1,
				success: function(msg) {
					BootstrapDialog.show({
						title: \'<div id="popupTitle" class="center"></div>\',
						message: msg
					});
				}
			});
	}

	function changeElemsOrder(id_trigger, old_id_condition, drop_id) {
		var new_id_condition;

		new_id_condition = parseInt(drop_id) + 1;
		if (old_id_condition < new_id_condition) {
			new_id_condition = new_id_condition - 1;
		}
		$.ajax({
			type:"GET",
			url: "/templates/default/form/form_trigger_elem_order.php",
			data: "id_trigger="+id_trigger
					+"&old_id_condition="+old_id_condition
					+"&new_id_condition="+new_id_condition,
			beforeSend:function(result, status){
				PopupLoading();
			},
			success: function(result) {
				displayTrigger(id_trigger);
				popup_close();
			}
		});
	}

	function getDeviceOptions(room_id_device, id_trigger) {
		if (room_id_device > 0) {
			$.ajax({
				type: "GET",
				url: "/templates/default/form/form_user_trigger_opt_list.php",
				data: "room_id_device="+room_id_device
				       +"&id_trigger="+id_trigger,
				success: function(result) {
					$("#optionList").html(result);
				}
			});
		}
	}
		
	function displayTrigger(id_trigger) {
		$.ajax({
			type: "GET",
			url: "/templates/default/form/form_display_trigger.php",
			data: "id_trigger="+id_trigger,
			success: function(result) {
				$("#drop-conditions").html(result);
				setDroppable();
				openDivs(0, 0);
				setLinkedSmartcmd('.$trigger_info->id_smartcmd.');
			}
		});
	}
	
	function PopupRemoveTriggerElem(condition_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/popup/popup_remove_trigger_elem.php",
			data: "condition_id="+condition_id,
			success: function(result) {
				BootstrapDialog.show({
					title: "'._('Delete Condition').'",
					message: result
				});
			}
		});
	}
	
	function RemoveTriggerElem(condition_id) {
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_remove_trigger_elem.php",
			data: "condition_id="+condition_id+"&id_trigger="+'.$id_trigger.',
			success: function(result) {
				displayTrigger('.$id_trigger.');
				popup_close();
			}
		});
	}
						
	function setLinkedSmartcmd(smartcmd_id) {
		$("#selectSmartcmd-'.$id_trigger.'").selectpicker(\'val\', smartcmd_id);
	}
	
</script>';
?>