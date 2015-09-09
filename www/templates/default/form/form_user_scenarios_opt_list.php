<?php 

include('header.php');

if (!empty($_GET['room_id_device'])) {
	$request =  new Api();
	$request -> add_request('confDeviceRoomOpt', array($_GET['room_id_device']));
	
	$result  =  $request -> send_request();
	$listoptdevice = $result->confDeviceRoomOpt;
	
	foreach ($listoptdevice as $option) {
		echo '
			<div id="btn-option-'.$_GET['room_id_device'].'" class="btn btn-greenleaf btn-draggable">
				<input type="text" value="'.$option->option_id.'" hidden>
				'.$option->namefr.'
			</div>';
	}
	echo '
		<script type="text/javascript">
			$(".btn-draggable").draggable({
				appendTo: "#drop-smartcmd",
				helper: "clone",
				revert: true,
				start: function() {
					dropZoneAnimate();
				},
				stop: function() {
					dropZoneStop();
				}
			});
		</script>';
			
}
?>