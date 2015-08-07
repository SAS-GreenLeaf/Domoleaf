<?php 

include('configuration-menu.php');

echo '<div class="col-md-10 col-md-offset-2 col-sm-10 col-sm-offset-2 col-xs-10 col-xs-offset-2">';
echo '<div class="center"><h2>'._('Room configuration').'</h2></div>';
echo '<div><a href="/conf_installation/'.$_GET['floor'].'" class="btn btn-greenleaf"><span class="fa fa-reply"></span> '._('Back').'</a></div>
		<div class="btn-group btn-group-greenleaf decalage-droite block-right">
				<a href="/conf_device_new/'.$_GET['floor'].'/'.$_GET['room'].'" title="'._('Add a device').'" class="btn btn-greenleaf">
					<span class="glyphicon glyphicon-plus" aria-hidden="true"></span> '._('New Device').'
				</a>
		</div>';

echo '	<div class="col-xs-12" id="listdevice">
		</div>
		<div class="alert alert-hidden alert-warning center col-xs-6 col-xs-offset-3 col-lg-10 col-lg-offset-1" role="alert">
			
		</div>
	</div>';

echo '
<script type="text/javascript">
	
GetDeviceList();

function RemoveDevice(iddevice){
	$.ajax({
		type:"GET",
		url: "/form/form_device_remove.php",
		data: "iddevice="+iddevice+"&idroom="+'.$_GET['room'].',
		success: function(result) {
			location.href="/conf_installation/'.$_GET['floor'].'/'.$_GET['room'].'";
		},
		error: function(status, result){
			location.href="/conf_installation/'.$_GET['floor'].'/'.$_GET['room'].'";
		}			
	});
}
		
function PopupRemoveDevice(iddevice){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/popup/popup_remove_device.php",
		data: "iddevice="+iddevice,
		success: function(result) {
			BootstrapDialog.show({
				title: "'._('Remove device').'",
           		message: result
        	});
		}
	});	
}
		
function CatchError(msg){
	$.ajax({
		type:"GET",
		url: "/templates/'.TEMPLATE.'/form/form_conf_alert_error.php",
		data: "msg="+encodeURIComponent(msg)+"&btn=no",
		success: function(result) {
			$(".alert").html(result).show("slow");
		}
	});
}

function GetDeviceList(){
		var room = '.$_GET['room'].';
		var floor = '.$_GET['floor'].';
		$.ajax({
				type:"GET",
				url: "/templates/'.TEMPLATE.'/form/form_conf_room_device_list.php",
				data: "room="+room,
				success: function(result) {
					$("#listdevice").html(result);
					if (result.split("</td>").length == 1) {
						 CatchError(\''._('No device').'\');
					}
				},
				error: function(result, status, error){

				}
			});
}

function SelectRow(id){
	location.href=\'/conf_installation/'.$_GET['floor'].'/'.$_GET['room'].'/\'+id;
}
						
</script>';

?>