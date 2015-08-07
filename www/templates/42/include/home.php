<?php 

include('templates/'.TEMPLATE.'/function/display_widget.php');

<<<<<<< HEAD
$devices_notPlaced = array(); $devices_placed = array();

if (isset($_GET['id']) && floor_exist($_GET['id'])){
	$devices_notPlaced = getDevices($_GET['id'], false);
	$devices_placed = getDevices($_GET['id'], true);
}

$displayBottom = (isset($_GET['id']) && floor_exist($_GET['id'])) ? '' : 'display: none';

echo '
<script type="text/javascript">global_template = "'.TEMPLATE.'"; var sentence_goBackFloor = "'._('Back to floor selection').'";</script>

<script type="text/javascript" src="templates/'.TEMPLATE.'/homePositioning.js"></script>

<div class="popContainer">
	<div id="popBottom" style="'.$displayBottom.'">
		<div id="popInnerBottom">
		<div class="innerSentence">'._('You can stock here the devices you don\'t know where to put.').'</div>'; 
			
			if (count($devices_notPlaced) > 0){
				foreach($devices_notPlaced as $device){
					$this_icon = (isset($icons_device[$device->device_id])) ? $icons_device[$device->device_id] : '';
					
					echo '
						<div class="box device_self draggable" data-id="'.$device->room_device_id.'" style="top:0; left: 0;">
							<div class="icon">
								<div class="image">
									<i class="'.$this_icon.'"></i>
								</div>
							</div>
							
							<p>'.$device->name.'</p>
						</div>';
				}
			}
			
			if (count($devices_placed) > 0){ 
				foreach($devices_placed as $device){
					$this_icon = (isset($icons_device[$device->device_id])) ? $icons_device[$device->device_id] : '';
					
					echo '
						<div class="box device_self draggable loadingPose" data-id="'.$device->room_device_id.'" data-x="'.$device->pos_x_icon.'" data-y="'.$device->pos_y_icon.'">
							<div class="icon">
								<div class="image">
									<i class="'.$this_icon.'"></i>
								</div>
							</div>
							
							<p>'.$device->name.'</p>
						</div>';
				}
			}
			
			if (count($devices_placed) > 0 || count($devices_notPlaced)){
				echo '<script type="text/javascript" src="templates/'.TEMPLATE.'/dragDevices.js"></script>';
			}
	
	echo '
		</div>
	</div>
</div>

<div class="popContainer zunder">
	<div id="popMiddle">
		<div id="innerPopMiddle">
			';
				
				// The user didn't choose any floor yet
			if (!isset($_GET['id']) || !floor_exist($_GET['id'])){ 
		
		echo '
			<h4>'._('Choose a floor to display').':</h4>
			<div id="home">
				<img src="templates/'.TEMPLATE.'/img/top_house.png" id="topHouse" />
				';
				
				foreach($floorallowed as $floor){
					echo  '<div class="floor_self" onclick="if (window.location.href.match(/\?/)){ window.location.href = window.location.href+\'&id='.$floor->floor_id.'\'; } else { window.location.href = window.location.href+\'?id='.$floor->floor_id.'\'; }"><img src="templates/'.TEMPLATE.'/img/part_house.png" class="partHouse" /><div class="floor_name">'.$floor->floor_name.'</div></div>';
				}
				
				echo '
				<img src="templates/'.TEMPLATE.'/img/base_house.png" id="baseHouse" />
			</div>';
			} else {
				$floor_id = $_GET['id'];
				$floor_background = getFloorBackground($floor_id);
				
				if (is_null($floor_background)){
					echo '<div id="glob_title" onclick="window.location.href=\'profile_user_installation\';">'._('You didn\'t choose any background image for this floor yet.').'<br />'._('Click here to add one.').'</div>';
					
					echo '<script type="text/javascript">posiAdditionalElement();</script>';
				} else {
					echo '<img src="'.$floor_background.'" id="globalFloorBackground" />';
				}
			}
		echo '
		</div>
	</div>
</div>';
=======
echo '
<input type="hidden" id="current-room" value="0">
<input type="hidden" id="current-application" value="0">
<div id="widget-container" class="col-lg-12 col-md-12 col-sm-12 col-xs-12 bhoechie-tab-container">
	<div class="col-md-2 col-md-offset-10 col-sm-2 col-sm-offset-10 col-xs-3 bhoechie-tab-menu-right sidebar">
		<div class="list-group-app">';
		foreach ($allapp as $elem){
			echo '
			<a href="#" id="app-'.$elem.'" onclick="displayApplication('.$elem.')" class="list-group-item text-center">
				<h4 class="'.$icons[$elem].' lg"></h4><br/>'.$app->$elem->name.'
			</a>';
		}
		echo '
		</div>
	</div>
	<div class="col-md-2 col-sm-2 col-xs-3 bhoechie-tab-menu sidebar">
		<div class="list-group">';
			foreach ($roomallowed as $room){
			echo '
			<a href="#" id="room-'.$room->room_id.'" onclick="displayRoom('.$room->room_id.')" class="list-group-item text-center">
				<h4 class="fa fa-cube lg"></h4><br/>'.$room->room_name.'
			</a>';
			}
			
		echo '
		</div>
	</div>
	<div id="bhoechie-tab" class="col-md-8 col-md-offset-2 col-sm-8 col-sm-offset-2 col-xs-9 col-xs-offset-3">';
		if (!empty($deviceallowed)){
			foreach ($deviceallowed as $elem){
				echo display_widget($elem);
			}
		}
	echo '
	</div>
</div>';

echo '
<script type="text/javascript">

	/*$(document).ready(function() {
    $("#colorpicker").farbtastic("#color");
  });*/

$("#current-room").val(0);
$("#current-application").val(0);
WidgetSize();

/*for (var i=0; i<360; i++) {
	var color = document.createElement("span")
	color.setAttribute("id", "d" + i)
	color.style.backgroundColor = "hsl(" + i + ", 100%, 50%)"
	color.style.msTransform = "rotate(" + i + "deg)"
	color.style.webkitTransform = "rotate(" + i + "deg)"
	color.style.MozTransform = "rotate(" + i + "deg)"
	color.style.OTransform = "rotate(" + i + "deg)"
	color.style.transform = "rotate(" + i + "deg)"
	document.getElementById("colorwheel").appendChild(color)
};*/
		
function WidgetSize(){
	$(".info").css("height", "auto");
	var height = 150;
		
	$(".info").each(function(index){
		if ($(this).height() > height){
			height = $(this).height();
		}
		
	});
	$(".info").css("height", (height+10)+"px");
}

function displayRoom(idroom){
	var val = parseInt($("#current-room").val());

	if (val != 0){
		$("#room-"+val).removeClass("active");
	}
	if (val == idroom){
		$("#current-room").val(0);
	}
	else {
		$("#current-room").val(idroom);
		$("#room-"+idroom).addClass("active");
	}
	displayUpdate();
}

function displayApplication(idapp){
	var val = parseInt($("#current-application").val());
	
	if (val != 0){
		$("#app-"+val).removeClass("active");
	}
	if (val == idapp){
		$("#current-application").val(0);
	}
	else {
		$("#current-application").val(idapp);
		$("#app-"+idapp).addClass("active");
	}
	displayUpdate();
}
		
function displayUpdate(){
	var room = parseInt($("#current-room").val());
	var app  = parseInt($("#current-application").val());
	
	$(".display-widget").hide();
	if (room == 0){
		if (app == 0){
			$(".display-widget").show();
		}
		else {
			$(".app-"+app).show();
		}
	}
	else {
		if (app == 0){
			$(".room-"+room).show();
		}
		else {
			$(".room-"+room+".app-"+app).show();
		}
	}
	WidgetSize();
}

function HandlePopup(type, iddevice){
	
	if (type == 0){
		var data = new Array("popup_camera_view.php", "'._('Camera view').'");	
	}
	else if (type == 1){
		var data = new Array("popup_audio_view.php", "'._('Audio view').' <i class=\'fa fa-volume-up\'></i>");
	}
	else if (type == 2){
		var data = new Array("popup_info_device.php", "'._('Information').'");
	}
	else if (type == 3){
		var data = new Array("popup_ChromaWheel.php", "'._('Information').'");
	}
	
	if (data){
		$.ajax({
			type:"GET",
			url: "templates/'.TEMPLATE.'/popup/"+data[0],
			data: "iddevice="+iddevice,
			success: function(msg) {
				BootstrapDialog.show({
					title: data[1],
					message: msg,
					onhide: function(){
						if ($("#cmd-camera-display").length){
							$("#cmd-camera-display").removeAttr("src");
						}
					}
				});
			}
		});
	}
}

setInterval(function() { GetReturn() }, 1000);

function GetReturn(){
	var token = document.cookie.split("=");

	$.ajax({
		type:"GET",
		url: "api.php",
		data: "token="+token[1]+"&request[mcReturn]=0",
		dataType: "json", 
		timeout: 999,
		success: function(msg) { /*
				$.each(msg.request.mcReturn, function(device_id, value){
					$.each(value, function(room_device_id, val){
						$.each(val, function(idopt, val2){
							if (val2.valeur){
								WidgetReturn(device_id, room_device_id, idopt, val2);
							}
						});
					});
				}); */
		} 
		});		
}

</script>';
>>>>>>> 0291d28... added new templates + added features to upload, delete, update the background image

?>