<?php 

include('configuration-menu.php');

echo '<div class="col-md-10 col-md-offset-2 col-sm-9 col-sm-offset-3 col-xs-11 col-xs-offset-1">';

echo '<div class="center"><h2>'._('Bluetooth monitor').'</h2></div>';

	echo '
			<div id="monitor_Bluetooth">
			</div>
			
<script type="text/javascript">

ListBluetooth();
setInterval(function() { ListBluetooth() }, 10000);
			
function ListBluetooth(){
		$.ajax({
			type:"GET",
			url: "/templates/'.TEMPLATE.'/form/form_conf_monitor_bluetooth.php",
			success: function(result) {
				$("#monitor_Bluetooth").html(result);				
			}
		});
}
						
</script>';

?>