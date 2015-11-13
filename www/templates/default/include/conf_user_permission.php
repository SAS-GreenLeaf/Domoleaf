<?php 

include('configuration-menu.php');

$dir_device = "/templates/default/custom/device/";
$dir_room = "/templates/default/custom/room/";

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';
echo '
	<div class="center">
		<h2>
			'._('User permission:').' '.$listuser->$_GET['userid']->username.'
			<button id="colorUserInstallBg" class="btn" onclick="popupChromaWheel(1, 1, '.$_GET['userid'].')">
				<span class="fa fa-paint-brush md colorUserInstall"></span>
			</button>
		</h2>
	</div><br/><br/>
	<div class="col-md-12">
		<div class="panel-group" id="accordion">';
			foreach ($accordioninfo as $floor){
				echo '
				<ul id="current-floor-'.$floor->floor_id.'" class="timeline">';
				$room = count((array)$floor->room);
					echo '
					<li>
						<div class="timeline-badge">
							<i class="glyphicon glyphicon-th-list"></i>
						</div>
						<div class="timeline-panel">';
							if ($floor->floor_allowed == 1){
								echo '
								<div id="floor-heading-'.$floor->floor_id.'"
								     onclick="ShowTimeline(\'floor-body-'.$floor->floor_id.'\', 1, '.$floor->floor_id.')"
								     class="timeline-heading cursor col-xs-8">';
							}
							else {
								echo 
								'<div id="floor-heading-'.$floor->floor_id.'" class="timeline-heading cursor col-xs-8">';
							}
							echo '
							<h4 class="timeline-title">'.$floor->floor_name.'</h4>
							<p>
								<small class="text-muted"><i class="glyphicon glyphicon-home">
								</i> '.$room.' '._('rooms').'</small>
							</p>
						</div>
						<div class="col-xs-4 center">';
							if (!empty($floor->floor_order)){
									echo '
									<div class="checkbox btn-group">
										<input class="visi-floor-floor-'.$floor->floor_id.'"
										       id="floor-visible-'.$floor->floor_id.'"
										       type="checkbox"
										       checked=""
										       data-on-color="primary"
						 				       data-label-width="0"
										       data-on-text="<i class=\'fa fa-eye\'></i>"
										       data-off-text="<i class=\'fa fa-eye-slash\'></i>" />
									</div>
									<script type="text/javascript">
										$("#floor-visible-'.$floor->floor_id.'").bootstrapSwitch();
										$("#floor-visible-'.$floor->floor_id.'").bootstrapSwitch(\'onSwitchChange\',
												function(event, state) { SetVisibleFloor(\''.$floor->floor_id.'\'); });
									</script>';
							}
							else {
								echo '
								<div class="checkbox btn-group">
									<input id="floor-visible-'.$floor->floor_id.'"
									       type="checkbox"';
									       if ($floor->floor_allowed != 1){
									        	echo 'disabled=""';
									       }
									       echo '
									       data-on-color="primary"
					 				       data-label-width="0"
									       data-on-text="<i class=\'fa fa-eye\'></i>"
									       data-off-text="<i class=\'fa fa-eye-slash\'></i>" />
								</div>
								<script type="text/javascript">
									$("#floor-visible-'.$floor->floor_id.'").bootstrapSwitch();
									$("#floor-visible-'.$floor->floor_id.'").bootstrapSwitch(\'onSwitchChange\',
										function(event, state) { SetVisibleFloor(\''.$floor->floor_id.'\'); });
								</script>';
							}
							echo '
							<div class="checkbox btn-group">
								<input id="floor-allow-'.$floor->floor_id.'"
								       type="checkbox"';
								       if ($floor->floor_allowed == 1){
								        	echo 'checked=""';
								       }
								       echo '
								       data-on-color="greenleaf"
								       data-off-color="danger"
				 				       data-label-width="0"
								       data-on-text="<i class=\'fa fa-check\'></i>"
								       data-off-text="<i class=\'fa fa-times\'></i>" />
							</div>
							<script type="text/javascript">
								$("#floor-allow-'.$floor->floor_id.'").bootstrapSwitch();
								$("#floor-allow-'.$floor->floor_id.'").bootstrapSwitch(\'onSwitchChange\',
										function(event, state) { OnOffFloor(\''.$floor->floor_id.'\'); });
							</script>';
							echo '
							<div class="btn-group">
								<button type="button"
								        class="btn btn-warning"
								        onclick="SetOrder(\''.$floor->floor_id.'\', -1, 0, \''.$floor->floor_order.'\')">
								        	<i class="glyphicon glyphicon-arrow-up"></i>
								</button>
								<button type="button" class="btn btn-warning" onclick="SetOrder(\''.$floor->floor_id.'\', 1, 0, \''.$floor->floor_order.'\')"><i class="glyphicon glyphicon-arrow-down"></i></button>
							</div>';
							echo '
						</div>
						<div id="floor-body-'.$floor->floor_id.'" class="timeline-body col-xs-12">';
							foreach ($floor->room as $room){
								echo '
								<ul id="current-room-'.$room->room_id.'" class="timeline timeline-rooms">';
									$device = count((array)$room->devices);
									echo '
									<li>
										<div class="timeline-badge">
											<i class="glyphicon glyphicon-home"></i>
										</div>
										<div class="timeline-panel" id="timeline-room-'.$room->room_id.'">';
											if ($room->room_allowed == 1){
												echo '
												<div id="room-heading-'.$room->room_id.'"
												     onclick="ShowTimeline(\'room-body-'.$room->room_id.'\', 2, '.$room->room_id.')"
												     class="timeline-heading z-index-50 cursor col-xs-6">';
											}
											else {
												echo '
												<div id="room-heading-'.$room->room_id.'" class="timeline-heading z-index-50 cursor col-xs-4">';
											}
													echo '
													<h4 class="timeline-title">'.$room->room_name.'</h4>
													<p><small class="text-muted"><i class="fa fa-cube"></i> '.$device.' '._('device').'</small></p>
												</div>
												<div class="col-xs-6 center z-index-50">';
													if (!empty($room->room_order)){
														echo '
														<div class="checkbox btn-group">
															<input class="visi-floor-room-'.$floor->floor_id.'"
															       id="room-visible-'.$room->room_id.'"
															       type="checkbox"
															       checked=""
															       data-on-color="primary"
						 									       data-label-width="0"
															       data-on-text="<i class=\'fa fa-eye\'></i>"
															       data-off-text="<i class=\'fa fa-eye-slash\'></i>" />
														</div>
														<script type="text/javascript">
															$("#room-visible-'.$room->room_id.'").bootstrapSwitch();
															$("#room-visible-'.$room->room_id.'").bootstrapSwitch(\'onSwitchChange\',
																	function(event, state) { SetVisibleRoom(\''.$room->room_id.'\'); });
														</script>';
													}
													else {
														echo '
														<div class="checkbox btn-group">
															<input class="visi-floor-room-'.$floor->floor_id.'"
															       id="room-visible-'.$room->room_id.'"
															       type="checkbox"';
															       if ($room->room_allowed != 1){
															        	echo 'disabled=""';
															       }
															       echo '
															       data-on-color="primary"
				 											       data-label-width="0"
															       data-on-text="<i class=\'fa fa-eye\'></i>"
															       data-off-text="<i class=\'fa fa-eye-slash\'></i>" />
														</div>
														<script type="text/javascript">
															$("#room-visible-'.$room->room_id.'").bootstrapSwitch();
															$("#room-visible-'.$room->room_id.'").bootstrapSwitch(\'onSwitchChange\',
																	function(event, state) { SetVisibleRoom(\''.$room->room_id.'\'); });
														</script>';
													}
													echo '
													<div class="checkbox btn-group">
														<input class="enable-floor-room-'.$floor->floor_id.'"
														       id="room-allow-'.$room->room_id.'"
														       type="checkbox"';
														       if ($room->room_allowed == 1){
														        	echo 'checked=""';
														       }
														       echo '
														       data-on-color="greenleaf"
														       data-off-color="danger"
										 				       data-label-width="0"
														       data-on-text="<i class=\'fa fa-check\'></i>"
														       data-off-text="<i class=\'fa fa-times\'></i>" />
													</div>
													<script type="text/javascript">
														$("#room-allow-'.$room->room_id.'").bootstrapSwitch();
														$("#room-allow-'.$room->room_id.'").bootstrapSwitch(\'onSwitchChange\',
																	function(event, state) { OnOffRoom(\''.$room->room_id.'\'); });
													</script>';
													echo '
													<div class="btn-group">
														<button type="button"
														        class="btn btn-warning"
														        onclick="SetOrder(\''.$room->room_id.'\', -1, 1, \''.$room->room_order.'\')">
															<i class="glyphicon glyphicon-arrow-up"></i>
														</button>
														<button type="button"
														        class="btn btn-warning"
														        onclick="SetOrder(\''.$room->room_id.'\', 1, 1, \''.$room->room_order.'\')">
															<i class="glyphicon glyphicon-arrow-down"></i>
														</button>
													</div>
													<div class="btn-group">
														<button title="'._('Custom').'"
														        onclick="CustomPopup(2, '.$room->room_id.', '.$_GET['userid'].')"
														        class="btn btn-greenleaf"
														        type="button">
														        <span class="fa fa-paint-brush md"></span>
														</button>
													</div>
												</div>
												<div id="room-body-'.$room->room_id.'" class="timeline-body col-xs-12">';
													foreach ($room->devices as $device){
														echo '
														<div id="widget-'.$device->room_device_id.'" class="box col-md-3 col-sm-6 col-xs-12">
															<div class="icon">
																<div class="image"><i class="fa fa-cube"></i></div>
																<div class="info col-xs-12">
																	<div class="info-widget">
																		<button title="'._('Custom').'"
																		        onclick="CustomPopup(1, '.$device->room_device_id.', '.$_GET['userid'].')"
																		        class="btn btn-greenleaf"
																		        type="button">
																		        <span class="fa fa-paint-brush md"></span>
																		</button>
																	</div>
																	<h3 class="title">'.$device->name.'</h3>';
																	if (!empty($device->device_order)){
																		echo '
																		<div class="checkbox">
																			<input class="visi-room-device-'.$room->room_id.'"
																			       checked=""
																			       id="device-visible-'.$device->room_device_id.'"
																			       type="checkbox"
																			       data-on-color="primary"
								 											       data-label-width="0"
																			       data-on-text="<i class=\'fa fa-eye\'></i>"
																			       data-off-text="<i class=\'fa fa-eye-slash\'></i>" />
																		</div>
																		<script type="text/javascript">
																			$("#device-visible-'.$device->room_device_id.'").bootstrapSwitch();
																			$("#device-visible-'.$device->room_device_id.'").bootstrapSwitch(\'onSwitchChange\',
																					function(event, state) { SetVisibleDevice(\''.$device->room_device_id.'\'); });
																		</script>';
																	}
																	else {
																		echo '
																		<div class="checkbox">
																			<input class="visi-room-device-'.$room->room_id.'"
																			       id="device-visible-'.$device->room_device_id.'"
																			       type="checkbox"';
																			       if ($device->device_allowed != 1){
																			       		echo 'disabled ';
																			       }
																			       echo '
																			       data-on-color="primary"
							 												       data-label-width="0"
																			       data-on-text="<i class=\'fa fa-eye\'></i>"
																			       data-off-text="<i class=\'fa fa-eye-slash\'></i>" />
																		</div>
																		<script type="text/javascript">
																			$("#device-visible-'.$device->room_device_id.'").bootstrapSwitch();
																			$("#device-visible-'.$device->room_device_id.'").bootstrapSwitch(\'onSwitchChange\',
																					function(event, state) { SetVisibleDevice(\''.$device->room_device_id.'\'); });
																		</script>';
																	}
																	echo '
																	<div class="checkbox">
																		<input class="enable-room-device-'.$room->room_id.'"';
																		       if ($device->device_allowed == 1){
																		       		echo 'checked ';
																		       }
																		       echo '
																		       id="device-allow-'.$device->room_device_id.'"
																		       type="checkbox"
																		       data-on-color="greenleaf"
																		       data-off-color="danger"
														 				       data-label-width="0"
																		       data-on-text="<i class=\'fa fa-check\'></i>"
																		       data-off-text="<i class=\'fa fa-times\'></i>" />
																	</div>
																	<script type="text/javascript">
																		$("#device-allow-'.$device->room_device_id.'").bootstrapSwitch();
																		$("#device-allow-'.$device->room_device_id.'").bootstrapSwitch(\'onSwitchChange\',
																				function(event, state) { OnOffDevice(\''.$device->room_device_id.'\'); });
																	</script>';
																	echo '
																	<div class="padding-bottom btn-group">
																		<button type="button"
																		        class="btn btn-warning"
																		        onclick="SetOrder(\''.$device->room_device_id.'\', -1, 2, \''.$device->device_order.'\')">
																			<i class="glyphicon glyphicon-arrow-up rotate--90"></i>
																		</button>
																		<button type="button"
																		        class="btn btn-warning"
																		        onclick="SetOrder(\''.$device->room_device_id.'\', 1, 2, \''.$device->device_order.'\')">
																			<i class="glyphicon glyphicon-arrow-down rotate--90"></i>
																		</button>
																	</div>
																</div>
																<div id="widget-bg-'.$device->room_device_id.'" class="info-bg" ';
																	if (!empty($device->device_bgimg)) {
																		echo 'style="background-image: url(\''.$dir_device.$device->device_bgimg.'\');"';
																	}
																echo '>
																</div>
															</div>&nbsp;
														</div>';
												}
												echo '
												</div>
											<div id="room-bg-'.$room->room_id.'" class="installation-room-bg bg-image" ';
											if (!empty($room->room_bgimg)) {
												echo 'style="background-image: url(\''.$dir_room.$room->room_bgimg.'\');"';
											}
											echo '>
											</div>
									</li>
								</ul>';
							}
						echo '
						</div>
					</li>
				</ul>';
			}
		echo '
		</div>
	</div>
</div>';

echo '
<script type="text/javascript">

$(document).ready(function(){
	WidgetSize();
	$("#colorUserInstallBg").css(\'background-color\', "'.$bg_color.'");
});

function swap(elem, action){
	if (action == 1){
			if ($(elem).next()){
				$(elem).insertAfter($(elem).next());
			}
		}
		else {
			if ($(elem).prev()){
				$(elem).insertBefore($(elem).prev());
			}
		}
}
		
function SetOrder(id, action, type, order){
	if (order > 0){
		var data = "userid="+'.$userid.';
	
		if (type == 0){
			data+="&floorid="+id;
			swap("#current-floor-"+id, action);
		}
		else if (type == 1){
			data+="&roomid="+id;
			swap("#current-room-"+id, action);
		}
		else if (type == 2) {
			data+="&deviceid="+id;
			swap("#widget-"+id, action);
		}
	
		data+="&action="+action;
		$.ajax({
				type:"GET",
				url: "/form/form_installation_order.php",
				data: data,
				success: function(result) {
				},
				error: function(result, status) {
				}
			});
	}
}		
		
function SetVisibleFloor(idfloor){
	var status = $("#floor-visible-"+idfloor).prop("checked") ? 1 : 0;
	var userid = "'.$userid.'";
	
	if (status == 1){
		$(".visi-floor-room-"+idfloor).prop("checked", true).change();
	}
	else {
		$(".visi-floor-room-"+idfloor).prop("checked", false).change();
	}	
			
	if (idfloor != ""){
		$.ajax({
			type:"GET",
			url: "/form/form_user_permission.php",
			data: "userid="+userid+"&vfloorid="+idfloor+"&status="+status,
			success: function(result) {
			},
			error: function(result, status) {
			}
		});
	}
}

function SetVisibleRoom(idroom){
	
	var status = $("#room-visible-"+idroom).prop("checked") ? 1 : 0;
	var userid = "'.$userid.'";
	
	if (status == 1){
		$(".visi-room-device-"+idroom).prop("checked", true).change();
	}
	else {
		$(".visi-room-device-"+idroom).prop("checked", false).change();
	}
			
	if (idroom != ""){
		$.ajax({
			type:"GET",
			url: "/form/form_user_permission.php",
			data: "userid="+userid+"&vroomid="+idroom+"&status="+status,
			success: function(result) {
			},
			error: function(result, status) {
			}
		});
	}
}
		
function SetVisibleDevice(iddevice){
			
	var status = $("#device-visible-"+iddevice).prop("checked") ? 1 : 0;
	var userid = "'.$userid.'";
			
	if (iddevice != ""){
		$.ajax({
			type:"GET",
			url: "/form/form_user_permission.php",
			data: "userid="+userid+"&vdeviceid="+iddevice+"&status="+status,
			success: function(result) {
			},
			error: function(result, status) {
			}
		});
	}
}
			
function OnOffFloor(idfloor){
	var allow = $("#floor-allow-"+idfloor).prop("checked") ? 1 : 0;	
	var userid = "'.$userid.'";
	
	if (allow == 1){
		$("#floor-heading-"+idfloor).attr("onclick", "ShowTimeline(\'floor-body-"+idfloor+"\', 1, idfloor)");
		$("#floor-visible-"+idfloor).prop("checked", true).change();
		$("#floor-visible-"+idfloor).bootstrapSwitch(\'toggleDisabled\', \'false\', \'true\');
		$(".enable-floor-room-"+idfloor).prop("checked", true).change();
	}
	else {
		$("#collapse-"+idfloor).removeClass("panel-collapse collapse in");
		$("#collapse-"+idfloor).addClass("panel-collapse collapse");
		$("#floor-visible-"+idfloor).prop("checked", false).change();
		$("#floor-visible-"+idfloor).bootstrapSwitch(\'toggleDisabled\', \'true\', \'true\');
		$("#floor-body-"+idfloor).hide();	
		$(".enable-floor-room-"+idfloor).prop("checked", false).change();
	}
	if (idfloor != ""){
		$.ajax({
				type:"GET",
				url: "/form/form_user_permission.php",
				data: "userid="+userid+"&floorid="+idfloor+"&allow="+allow,
				success: function(result) {
				},
				error: function(result, status) {
				}
		});
	}
}
		
function OnOffRoom(idroom){
	var allow = $("#room-allow-"+idroom).prop("checked") ? 1 : 0;
	var userid = "'.$userid.'";

	if (allow == 1){
		$("#room-heading-"+idroom).attr("onclick", "ShowTimeline(\'room-body-"+idroom+"\', 2, idroom)");
		$("#room-visible-"+idroom).prop("checked", true).change();
		$("#room-visible-"+idroom).bootstrapSwitch(\'toggleDisabled\', \'false\', \'true\');
		$(".enable-room-device-"+idroom).prop("checked", true).change();
	}
	else {
		$("#room-heading-"+idroom).removeAttr("onclick");
		$("#room-body-"+idroom).hide();
		$("#room-visible-"+idroom).prop("checked", false).change();
		$("#room-visible-"+idroom).bootstrapSwitch(\'toggleDisabled\', \'true\', \'true\');
		$(".enable-room-device-"+idroom).prop("checked", false).change();
	}
				
	if (idroom != ""){
		$.ajax({
				type:"GET",
				url: "/form/form_user_permission.php",
				data: "userid="+userid+"&roomid="+idroom+"&allow="+allow,
				success: function(result) {
				},
				error: function(result, status) {
				}
		});
	}
}
		
function OnOffDevice(iddevice){
	var allow = $("#device-allow-"+iddevice).prop("checked") ? 1 : 0;
	var userid = "'.$userid.'";

	if (allow == 1){
		$("#device-visible-"+iddevice).prop("checked", true).change();
		$("#device-visible-"+iddevice).bootstrapSwitch(\'toggleDisabled\', \'false\', \'true\');
	}
	else {
		$("#device-visible-"+iddevice).prop("checked", false).change();
		$("#device-visible-"+iddevice).bootstrapSwitch(\'toggleDisabled\', \'true\', \'true\');
	}
	if (iddevice != ""){
		$.ajax({
			type:"GET",
			url: "/form/form_user_permission.php",
			data: "userid="+userid+"&deviceid="+iddevice+"&allow="+allow,
			success: function(result) {
					//location.href="/conf_users/"+userid+"/"+'.$_GET['lvl'].';
			},
			error: function(result, status) {
			}
		});
	}
}

function WidgetSize(){
	$(".info").css("height", "auto");
	var height = 200;
		
	$(".info").each(function(index){
		if ($(this).height() > height){
			height = $(this).height();
		}
		
	});
	$(".info").css("height", (height+10)+"px");
	$(".info-bg").css("height", (height+10)+"px");
}
</script>';

?>