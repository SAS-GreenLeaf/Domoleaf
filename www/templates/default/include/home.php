<?php 

include('templates/'.TEMPLATE.'/function/display_widget.php');
include('templates/'.TEMPLATE.'/function/display_smartcmd.php');

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
		if (!empty($smartcmdLinked)){
			foreach ($smartcmdLinked as $elem){
				echo display_smartcmd($elem, $icons[7]);
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
	$(".info-bg").css("height", (height+10)+"px");
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
			url: "/templates/'.TEMPLATE.'/popup/"+data[0],
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
		url: "/api.php",
		data: "token="+token[1]+"&request[mcReturn]=0",
		dataType: "json",
		timeout: 999,
		success: function(msg) {
				$.each(msg.request.mcReturn, function(device_id, value){
					$.each(value, function(room_device_id, val){
						$.each(val, function(idopt, val2){
							if (val2.valeur){
								WidgetReturn(device_id, room_device_id, idopt, val2);
							}
						});
					});
				});
		}
		});		
}

</script>';

?>