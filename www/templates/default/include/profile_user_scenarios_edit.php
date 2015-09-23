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
		</button>
		<div id="linked-room" class="navbar-brand">
			'._('Linked Room').'
			<select class="selectpicker span2" id="selectFloor-'.$id_smartcmd.'" data-size="10"
			        onchange="listRoomsLR('.$id_smartcmd.')">
				<option value="0">'._('No floor selected').'</option>';
				foreach ($installation_info as $floor) {
					echo '<option value="'.$floor->floor_id.'">'.$floor->floor_name.'</option>';
				}
				echo '
			</select>
			<select class="selectpicker span2" id="selectRoom-'.$id_smartcmd.'" data-size="10"
			        onchange="changeSaveBtn()">
				<option value="0">'._('No floor selected').'</option>
			</select>
			<button id="saveLR_btn"
			        onclick="saveLinkedRoom('.$id_smartcmd.')"
			        class="btn btn-primary">
				'._('Save').'
			</button> 
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
	
	displaySmartcmd('.$id_smartcmd.');
	
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
	
	function selectDevice(id_smartcmd, room_id_device) {
		
		$("#optionList").hide();
		$("li div.active").removeClass("active");
		$("#device-"+room_id_device).addClass("active");
		getDeviceOptions(room_id_device, id_smartcmd);
		$("#optionList").show("slow");
		
	};

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
			
	function ShowRoomList(floor_id){
		
		if ($("#roomList-"+floor_id).hasClass("open")) {
			$("#roomList-"+floor_id).removeClass("open");
			$("#roomList-"+floor_id+" ul.open").toggle("slow");
			$("#roomList-"+floor_id+" ul.open").removeClass("open");
			$("li div.active").removeClass("active");
			$("#floor-"+floor_id+" #arrow-icon").removeClass("fa-caret-up");
			$("#floor-"+floor_id+" #arrow-icon").addClass("fa-caret-down");
		}
		else {
			$("#roomList-"+floor_id).addClass("open");
			$("#floor-"+floor_id+" #arrow-icon").addClass("fa-caret-up");
			$("#floor-"+floor_id+" #arrow-icon").removeClass("fa-caret-down");
		}
		$("#roomList-"+floor_id).toggle("slow");
	}

	function ShowDeviceList(room_id){
		if ($("#deviceList-"+room_id).hasClass("open")) {
			$("#deviceList-"+room_id).removeClass("open");
			$("li div.active").removeClass("active");
		}
		else {
			$("#deviceList-"+room_id).addClass("open");
		}
		$("#deviceList-"+room_id).toggle("slow");
	}

	function getDeviceOptions(room_id_device, id_smartcmd) {
		if (room_id_device > 0) {
			$.ajax({
				type: "GET",
				url: "/templates/default/form/form_user_scenarios_opt_list.php",
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
	
</script>';

?>