<?php 

include('profile-menu.php');

$dir_device = "/templates/default/custom/device/";
$dir_room = "/templates/default/custom/room/";

echo '
<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';
	echo '
	<div class="center">
		<h2>'._('User Installation').'
		<button id="colorUserInstallBg" class="btn" onclick="popupChromaWheel(1, 1, 0)">
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
						<div id="floor-panel-'.$floor->floor_id.'" class="timeline-panel">';
							if ($floor->floor_allowed == 1){
								echo '
							<div id="floor-heading-'.$floor->floor_id.'"
							     onclick="ShowTimeline(\'floor-body-'.$floor->floor_id.'\', 1, '.$floor->floor_id.')"
							     class="timeline-heading cursor col-xs-8">';
							}
							else {
								echo '
							<div id="floor-heading-'.$floor->floor_id.'" class="timeline-heading cursor col-xs-8">';
							}
							echo '
								<h4 class="timeline-title">'.$floor->floor_name.'</h4>
								<p>
									<small class="text-muted">
										<i class="glyphicon glyphicon-home"></i>
										'.$room.' '._('rooms').'
									</small>
								</p>
							</div>
							<div class="col-xs-4 center">
								<div class="checkbox btn-group">
									<input id="floor-visible-'.$floor->floor_id.'"
									       type="checkbox"
									       onchange="SetVisibleFloor(\''.$floor->floor_id.'\')" ';
									if (!empty($floor->floor_order)){
										echo '
									       class="visi-floor-floor-'.$floor->floor_id.'"
									       checked ';
									}
									echo '
									       data-on-color="primary"
									       data-label-width="0"
									       data-on-text="<i class=\'fa fa-eye\'></i>"
									       data-off-text="<i class=\'fa fa-eye-slash\'></i>" />
								</div>
								<script type="text/javascript">
									$("#floor-visible-'.$floor->floor_id.'").bootstrapSwitch();
								</script>
								<div class="btn-group">
									<button type="button"
									        class="btn btn-warning"
									        onclick="SetOrder(\''.$floor->floor_id.'\', -1, 0, \''.$floor->floor_order.'\')">
										<i class="glyphicon glyphicon-arrow-up"></i>
									</button>
									<button type="button"
									        class="btn btn-warning"
									        onclick="SetOrder(\''.$floor->floor_id.'\', 1, 0, \''.$floor->floor_order.'\')">
										<i class="glyphicon glyphicon-arrow-down"></i>
									</button>
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
										<div class="timeline-panel" id="timeline-room-'.$room->room_id.'">
											<div id="room-heading-'.$room->room_id.'"';
											if ($room->room_allowed == 1){
												echo '
											    onclick="ShowTimeline(\'room-body-'.$room->room_id.'\', 2, '.$room->room_id.')"';
											}
											echo '
											     class="timeline-heading cursor col-xs-6 z-index-50">
												<h4 class="timeline-title">'.$room->room_name.'</h4>
												<p>
													<small class="text-muted">
														<i class="fa fa-cube"></i>
														'.$device.' '._('device').'
													</small>
												</p>
											</div>
											<div class="col-xs-6 center z-index-50">
												<div class="checkbox btn-group">
													<input class="visi-floor-room-'.$floor->floor_id.'"
													       id="room-visible-'.$room->room_id.'"
													       type="checkbox"
													       onchange="SetVisibleRoom(\''.$room->room_id.'\')"';
												if (!empty($room->room_order)){
													echo '
													       checked';
												}
													echo '
													       data-on-color="primary"
													       data-label-width="0"
													       data-on-text="<i class=\'fa fa-eye\'></i>"
													       data-off-text="<i class=\'fa fa-eye-slash\'></i>" />
												</div>
												<script type="text/javascript">
													$("#room-visible-'.$room->room_id.'").bootstrapSwitch();
												</script>
												<div class="btn-group">
													<button type="button" class="btn btn-warning"
													        onclick="SetOrder(\''.$room->room_id.'\', -1, 1, \''.$room->room_order.'\')">
														<i class="glyphicon glyphicon-arrow-up"></i>
													</button>
													<button type="button" class="btn btn-warning"
													        onclick="SetOrder(\''.$room->room_id.'\', 1, 1, \''.$room->room_order.'\')">
														<i class="glyphicon glyphicon-arrow-down"></i>
													</button>
												</div>
												<div class="btn-group">
													<button title="'._('Custom').'"
													        onclick="CustomPopup(2, '.$room->room_id.', '.$userid.')"
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
																        onclick="CustomPopup(1, '.$device->room_device_id.', '.$userid.')"
																        class="btn btn-greenleaf"
																        type="button">
																        <span class="fa fa-paint-brush md"></span>
																</button>
															</div>
															<h3 class="title">'.$device->name.'</h3>
															<div class="checkbox">
																<input class="visi-room-device-'.$room->room_id.'"
																       id="device-visible-'.$device->room_device_id.'"
																       type="checkbox"
																       onchange="SetVisibleDevice(\''.$device->room_device_id.'\')"';
																if (!empty($device->device_order)){
																	echo '
																       checked=""';
																}
																echo '
																       data-on-color="primary"
																       data-label-width="0"
																       data-on-text="<i class=\'fa fa-eye\'></i>"
																       data-off-text="<i class=\'fa fa-eye-slash\'></i>" />
															</div>
															<script type="text/javascript">
																$("#device-visible-'.$device->room_device_id.'").bootstrapSwitch();
															</script>
															<div class="padding-bottom btn-group">
																<button type="button" class="btn btn-warning"
																        onclick="SetOrder(\''.$device->room_device_id.'\', -1, 2, \''.$device->device_order.'\')">
																	<i class="glyphicon glyphicon-arrow-up rotate--90"></i>
																</button>
																<button type="button" class="btn btn-warning"
																        onclick="SetOrder(\''.$device->room_device_id.'\', 1, 2, \''.$device->device_order.'\')">
																	<i class="glyphicon glyphicon-arrow-down rotate--90"></i>
																</button>
															</div>
														</div>
														<div id="widget-bg-'.$device->room_device_id.'" class="installation-device-bg bg-image" ';
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
		
echo '<script type="text/javascript">

$(document).ready(function(){
	WidgetSize();
	activateMenuElem(\'installation\');
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

function WidgetSize(){
	$(".info").css("height", "auto");
	var height = 150;
		
	$(".info").each(function(index){
		if ($(this).height() > height){
			height = $(this).height();
		}
		
	});
	$(".info").css("height", (height+10)+"px");
	$(".installation-device-bg").css("height", (height+10)+"px");
}

</script>';

?>