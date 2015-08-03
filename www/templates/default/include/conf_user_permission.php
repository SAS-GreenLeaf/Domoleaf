<?php 

include('configuration-menu.php');

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';
echo '
	<div class="center"><h2>'._('User permission:').' '.$listuser->$_GET['userid']->username.'</h2></div><br/><br/>
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
								<div id="floor-heading-'.$floor->floor_id.'" onclick="ShowTimeline(\'floor-body-'.$floor->floor_id.'\')" class="timeline-heading cursor col-xs-8">';
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
							<div class="col-xs-4">';
							if (!empty($floor->floor_order)){
									echo '
									<div class="checkbox col-xs-3">
										<input class="visi-floor-floor-'.$floor->floor_id.'" data-toggle="toggle" checked data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="floor-visible-'.$floor->floor_id.'" type="checkbox" onchange="SetVisibleFloor(\''.$floor->floor_id.'\')" />
									</div>';
							}
							else {
								if ($floor->floor_allowed == 1){
									echo '
									<div class="checkbox col-xs-3">
										<input data-toggle="toggle" data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="floor-visible-'.$floor->floor_id.'" type="checkbox" onchange="SetVisibleFloor(\''.$floor->floor_id.'\')" />
									</div>';
								}
								else {
									echo '
									<div class="checkbox col-xs-3">
										<input disabled data-toggle="toggle" data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="floor-visible-'.$floor->floor_id.'" type="checkbox" onchange="SetVisibleFloor(\''.$floor->floor_id.'\')" />
									</div>';
								}
							}
							if ($floor->floor_allowed == 1){
								echo '
								<div class="checkbox col-xs-3">
									<input checked data-toggle="toggle" data-onstyle="greenleaf" data-offstyle="danger" data-on="<i class=\'fa fa-check\'></i>" data-off="<i class=\'fa fa-times\'></i>" data-style="slow" id="floor-allow-'.$floor->floor_id.'" type="checkbox" onchange="OnOffFloor(\''.$floor->floor_id.'\')" />
								</div>';
							}
							else {
								echo '
								<div class="checkbox col-xs-3">
									<input data-toggle="toggle" data-onstyle="greenleaf" data-offstyle="danger" data-on="<i class=\'fa fa-check\'></i>" data-off="<i class=\'fa fa-times\'></i>" data-style="slow" id="floor-allow-'.$floor->floor_id.'" type="checkbox" onchange="OnOffFloor(\''.$floor->floor_id.'\')" />
								</div>';
							}
							echo '
							<div class="padding-top btn-group col-xs-5">
								<button type="button" class="btn btn-warning" onclick="SetOrder(\''.$floor->floor_id.'\', -1, 0, \''.$floor->floor_order.'\')"><i class="glyphicon glyphicon-arrow-up"></i></button>
								<button type="button" class="btn btn-warning" onclick="SetOrder(\''.$floor->floor_id.'\', 1, 0, \''.$floor->floor_order.'\')"><i class="glyphicon glyphicon-arrow-down"></i></button>
							</div>';
							echo '
							</div>
							<div id="floor-body-'.$floor->floor_id.'" class="timeline-body col-xs-12">';
								foreach ($floor->room as $room){
									echo '
									<ul id="current-room-'.$room->room_id.'" class="timeline">';
										$device = count((array)$room->devices);
										echo '
										<li>
											<div class="timeline-badge">
												<i class="glyphicon glyphicon-home"></i>
											</div>
											<div class="timeline-panel">';
												if ($room->room_allowed == 1){
													echo '
													<div id="room-heading-'.$room->room_id.'" onclick="ShowTimeline(\'room-body-'.$room->room_id.'\')" class="timeline-heading cursor col-xs-6">';
												}
												else {
													echo '
													<div id="room-heading-'.$room->room_id.'" class="timeline-heading cursor col-xs-4">';
												}
														echo '
														<h4 class="timeline-title">'.$room->room_name.'</h4>
														<p><small class="text-muted"><i class="fa fa-cube"></i> '.$device.' '._('device').'</small></p>
													</div>
													<div class="col-xs-6">';
														if (!empty($room->room_order)){
														echo '
														<div class="checkbox col-xs-3">
															<input class="visi-floor-room-'.$floor->floor_id.'" data-toggle="toggle" checked data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="room-visible-'.$room->room_id.'" type="checkbox" onchange="SetVisibleRoom(\''.$room->room_id.'\')" />
														</div>';
														}
														else {
															if ($room->room_allowed == 1){
																echo '
																<div class="checkbox col-xs-3">
																	<input class="visi-floor-room-'.$floor->floor_id.'" data-toggle="toggle" data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="room-visible-'.$room->room_id.'" type="checkbox" onchange="SetVisibleRoom(\''.$room->room_id.'\')" />
																</div>';
															}
															else {
																echo '
																<div class="checkbox col-xs-3">
																	<input disabled class="visi-floor-room-'.$floor->floor_id.'" data-toggle="toggle" data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="room-visible-'.$room->room_id.'" type="checkbox" onchange="SetVisibleRoom(\''.$room->room_id.'\')" />
																</div>';
															}
														}
														if ($room->room_allowed == 1){
															echo '
															<div class="checkbox col-xs-3">
																<input class="enable-floor-room-'.$floor->floor_id.'" checked data-toggle="toggle" checked data-onstyle="greenleaf" data-offstyle="danger" data-on="<i class=\'fa fa-check\'></i>" data-off="<i class=\'fa fa-times\'></i>" data-style="slow" id="room-allow-'.$room->room_id.'" type="checkbox" onchange="OnOffRoom(\''.$room->room_id.'\')" />
															</div>';
															}
															else {
																echo '
																<div class="checkbox col-xs-3">
																	<input class="enable-floor-room-'.$floor->floor_id.'" data-toggle="toggle" data-onstyle="greenleaf" data-offstyle="danger" data-on="<i class=\'fa fa-check\'></i>" data-off="<i class=\'fa fa-times\'></i>" data-style="slow" id="room-allow-'.$room->room_id.'" type="checkbox" onchange="OnOffRoom(\''.$room->room_id.'\')" />
																</div>';
															}
															echo '
															<div class="padding-top btn-group col-xs-5">
																<button type="button" class="btn btn-warning" onclick="SetOrder(\''.$room->room_id.'\', -1, 1, \''.$room->room_order.'\')"><i class="glyphicon glyphicon-arrow-up"></i></button>
																<button type="button" class="btn btn-warning" onclick="SetOrder(\''.$room->room_id.'\', 1, 1, \''.$room->room_order.'\')"><i class="glyphicon glyphicon-arrow-down"></i></button>
															</div>';
													echo '
													</div>
													<div id="room-body-'.$room->room_id.'" class="timeline-body col-xs-12">';
														foreach ($room->devices as $device){
															echo '
															<div id="widget-'.$device->room_device_id.'" class="box col-md-3 col-sm-6 col-xs-12">
																<div class="icon">
																	<div class="image"><i class="fa fa-cube"></i></div>
																	<div class="info">
																		<h3 class="title margin-top">'.$device->name.'</h3>
																		<div>';
																			if (!empty($device->device_order)){		
																				echo '
																				<div class="checkbox">
																					<input class="visi-room-device-'.$room->room_id.'" checked data-toggle="toggle" checked data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="device-visible-'.$device->room_device_id.'" type="checkbox" onchange="SetVisibleDevice(\''.$device->room_device_id.'\')" />
																				</div>';
																			}
																			else {
																				if ($device->device_allowed == 1){
																					echo '
																					<div class="checkbox">
																						<input class="visi-room-device-'.$room->room_id.'" data-toggle="toggle" data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="device-visible-'.$device->room_device_id.'" type="checkbox" onchange="SetVisibleDevice(\''.$device->room_device_id.'\')" />
																					</div>';
																				}
																				else {
																					echo '
																					<div class="checkbox">
																						<input class="visi-room-device-'.$room->room_id.'" disabled data-toggle="toggle" data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="device-visible-'.$device->room_device_id.'" type="checkbox" onchange="SetVisibleDevice(\''.$device->room_device_id.'\')" />
																					</div>';
																				}
																			}
																			if ($device->device_allowed == 1){
																				echo '
																				<div class="checkbox">
																					<input class="enable-room-device-'.$room->room_id.'" checked data-toggle="toggle" checked data-onstyle="greenleaf" data-offstyle="danger" data-on="<i class=\'fa fa-check\'></i>" data-off="<i class=\'fa fa-times\'></i>" data-style="slow" id="device-allow-'.$device->room_device_id.'" type="checkbox" onchange="OnOffDevice(\''.$device->room_device_id.'\')" />
																				</div>';
																			}
																			else {
																				echo '
																				<div class="checkbox">
																					<input class="enable-room-device-'.$room->room_id.'" data-toggle="toggle" data-onstyle="greenleaf" data-offstyle="danger" data-on="<i class=\'fa fa-check\'></i>" data-off="<i class=\'fa fa-times\'></i>" data-style="slow" id="device-allow-'.$device->room_device_id.'" type="checkbox" onchange="OnOffDevice(\''.$device->room_device_id.'\')" />
																				 </div>';
																			}
																			echo '
																			<div class="padding-top btn-group">
																				<button type="button" class="btn btn-warning" onclick="SetOrder(\''.$device->room_device_id.'\', -1, 2, \''.$device->device_order.'\')"><i class="glyphicon glyphicon-arrow-up rotate--90"></i></button>
																				<button type="button" class="btn btn-warning" onclick="SetOrder(\''.$device->room_device_id.'\', 1, 2, \''.$device->device_order.'\')"><i class="glyphicon glyphicon-arrow-down rotate--90"></i></button>
																			</div>';
																		echo '
																		</div>&nbsp;
																	</div>
																</div>
															</div>';
														}
													echo '
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
		$("#floor-heading-"+idfloor).attr("onclick", "ShowTimeline(\'floor-body-"+idfloor+"\')");
		$("#floor-visible-"+idfloor).prop("checked", true).change();
		$("#floor-visible-"+idfloor).prop("disabled", false).change();
			
		$(".enable-floor-room-"+idfloor).prop("checked", true).change();
	}
	else {
		$("#collapse-"+idfloor).removeClass("panel-collapse collapse in");
		$("#collapse-"+idfloor).addClass("panel-collapse collapse");
		$("#floor-visible-"+idfloor).prop("checked", false).change();
		$("#floor-visible-"+idfloor).prop("disabled", true).change();
		$("#floor-heading-"+idfloor).removeAttr("onclick");
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
		$("#room-heading-"+idroom).attr("onclick", "ShowTimeline(\'room-body-"+idroom+"\')");
		$("#room-visible-"+idroom).prop("checked", true).change();
		$("#room-visible-"+idroom).prop("disabled", false).change();
		$(".enable-room-device-"+idroom).prop("checked", true).change();
	}
	else {
		$("#room-heading-"+idroom).removeAttr("onclick");
		$("#room-body-"+idroom).hide();
		$("#room-visible-"+idroom).prop("checked", false).change();
		$("#room-visible-"+idroom).prop("disabled", true).change();
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
		$("#device-visible-"+iddevice).prop("disabled", false).change();
	}
	else {
		$("#device-visible-"+iddevice).prop("checked", false).change();
		$("#device-visible-"+iddevice).prop("disabled", true).change();
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
							
</script>';

?>