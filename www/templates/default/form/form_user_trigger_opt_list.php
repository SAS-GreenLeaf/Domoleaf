<?php 

include('header.php');

if (!empty($_GET['room_id_device']) || !empty($_GET['id_trigger'])) {
	$request =  new Api();
	$request -> add_request('confDeviceRoomOpt', array($_GET['room_id_device']));
	$request -> add_request('countTriggerConditions', array($_GET['id_trigger']));
	
	$result  =  $request -> send_request();
	$listoptdevice = $result->confDeviceRoomOpt;
	
	$idexec = $result->countTriggerConditions + 1;
	
	$available_opt = array(
		  6 => 6,
		 12 => 12,
		 54 => 54,
		 72 => 72,
		 73 => 73,
		 79 => 79,
		 96 => 96,
		 97 => 97,
		112 => 112,
		113 => 113,
		365 => 365,
		388 => 388,
		441 => 441
	);
	
	if (empty($listoptdevice)) {
		return;
	}
	
	foreach ($listoptdevice as $option) {
		if (!empty($available_opt[$option->option_id])) {
			echo '
			<li class="list-item">
				<div id="btn-option-'.$_GET['room_id_device'].'" class="box-scenar-devices cursor btn-draggable"
				     onclick="onclickDropNewElem('.$_GET['id_trigger'].', '.$_GET['room_id_device'].', '.$option->option_id.', '.$idexec.')">
					<input type="text" value="'.$option->option_id.'" hidden>
					'.$option->name.'
				</div>
			</li>';
		}
	}
	echo '
	<script type="text/javascript">
		$(".btn-draggable").draggable({
			appendTo: "#drop-conditions",
			helper: "clone",
			revert: "invalid",
			start: function() {
				dropZoneAnimate(1);
			},
			stop: function() {
				dropZoneStop(1);
			}
		});
	</script>';
}
?>