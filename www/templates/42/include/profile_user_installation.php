<?php 

include('profile-menu.php');

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';
	echo '
	<div class="center"><h2>'._('User Installation').'</h2></div><br/><br/>
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
						<div id="floor-panel-'.$floor->floor_id.'" class="timeline-panel">';
							if ($floor->floor_allowed == 1){
								echo '
								<div id="floor-heading-'.$floor->floor_id.'" onclick="ShowTimeline(\'floor-body-'.$floor->floor_id.'\')" class="timeline-heading cursor col-xs-8">';
							}
							else {
								echo '
								<div id="floor-heading-'.$floor->floor_id.'" class="timeline-heading cursor col-xs-8">';
							}
									echo '
									<h4 class="timeline-title">'.$floor->floor_name.'</h4>
									<p><small class="text-muted"><i class="glyphicon glyphicon-home"></i> '.$room.' '._('rooms').'</small></p>
								</div>
							<div class="col-xs-4">
								<div class="float-right">
									<button type="button" class="btn btn-info"  onclick="$(this).floorUpload({id: '.$floor->floor_id.'});">'._('Update this floor\'s').'<br />'._('background').'</button>
								</div>
							';
							if (!empty($floor->floor_order)){
								echo '
								<div class="checkbox col-xs-3">
									<input class="visi-floor-floor-'.$floor->floor_id.'" data-toggle="toggle" checked data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="floor-visible-'.$floor->floor_id.'" type="checkbox" onchange="SetVisibleFloor(\''.$floor->floor_id.'\')" />
								</div>';
							}
							else {
								echo '
								<div class="checkbox col-xs-3">
									<input data-toggle="toggle" data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="floor-visible-'.$floor->floor_id.'" type="checkbox" onchange="SetVisibleFloor(\''.$floor->floor_id.'\')" />
								</div>';
							}
							echo '
							<div class="padding-top btn-group padding-left">
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
												echo '
												<div class="checkbox col-xs-3">
													<input class="visi-floor-room-'.$floor->floor_id.'" data-toggle="toggle" data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="room-visible-'.$room->room_id.'" type="checkbox" onchange="SetVisibleRoom(\''.$room->room_id.'\')" />
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
															<div class="info-widget custom-icon">
																<button title="'._('Custom').'"
																        onclick="CustomPopup(0, '.$device->room_device_id.')"
																        class="btn btn-greenleaf"
																        type="button">
																        <span class="fa fa-paint-brush md"></span>
																</button>
															</div>
															<h3 class="title">'.$device->name.'</h3>';
															if (!empty($device->device_order)){
																echo '
																<div class="checkbox">
																	<input class="visi-room-device-'.$room->room_id.'" checked data-toggle="toggle" checked data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="device-visible-'.$device->room_device_id.'" type="checkbox" onchange="SetVisibleDevice(\''.$device->room_device_id.'\')" />
																</div>';
															}
															else {
																echo '
																<div class="checkbox">
																	<input class="visi-room-device-'.$room->room_id.'" data-toggle="toggle" data-onstyle="primary" data-off="<i class=\'fa fa-eye-slash\'></i>" data-on="<i class=\'fa fa-eye\'></i>" id="device-visible-'.$device->room_device_id.'" type="checkbox" onchange="SetVisibleDevice(\''.$device->room_device_id.'\')" />
																</div>';
															}
															echo '
															<div class="padding-bottom btn-group">
																<button type="button" class="btn btn-warning" onclick="SetOrder(\''.$device->room_device_id.'\', -1, 2, \''.$device->device_order.'\')"><i class="glyphicon glyphicon-arrow-up rotate--90"></i></button>
																<button type="button" class="btn btn-warning" onclick="SetOrder(\''.$device->room_device_id.'\', 1, 2, \''.$device->device_order.'\')"><i class="glyphicon glyphicon-arrow-down rotate--90"></i></button>
															</div>
														</div>';
													echo '
													</div>&nbsp;
												</div>';
											}
										echo '
									</li>
								</ul>';
							}
						echo '
						</div>
					</li>
				</ul>';
			}
		echo '
		</div>';
	echo '
	</div>
</div>';

echo '<script type="text/javascript">

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
		$("#floor-body-"+idfloor).children("ul").find("input").each(function() {
			$(this).prop("disabled", false).change();
		});
	}
	else {
		$(".visi-floor-room-"+idfloor).prop("checked", false).change();
		$("#floor-body-"+idfloor).children("ul").find("input").each(function() {
			$(this).prop("disabled", true).change();
		});
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
		$("#room-body-"+idroom).children().find("input").each(function() {
			$(this).prop("disabled", false).change();
		});
	}
	else {
		$(".visi-room-device-"+idroom).prop("checked", false).change();
		$("#room-body-"+idroom).children().find("input").each(function() {
			$(this).prop("disabled", true).change();
		});
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

var globalTemplate = "'.TEMPLATE.'";
var sentence_onUpload = "'._('Select the wanted background image below:').'";
var sentence_onConfirmRemoval = "'._('Are you sure you want to delete the background image of this floor?').'";
				
</script>
<script type="text/javascript" src="templates/'.TEMPLATE.'/floorUpload.js"></script>
';

?>