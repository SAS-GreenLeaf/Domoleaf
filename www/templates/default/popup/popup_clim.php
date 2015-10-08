<?php 

include('header.php');

if (!empty($_GET['iddevice'])){
	
	$request = new Api();
	$request->send_request();
	$result = $request->send_request();
	
		echo '<div class="center">'.
			 '<div class="btn-group">';
		echo '<button onclick="" class="btn btn-info">'._('Confort').'</button>';
		echo '<button onclick="" class="btn btn-info">'._('Nuit').'</button>';
		echo '<button onclick="" class="btn btn-info">'._('Eco').'</button>';
		echo '<button onclick="" class="btn btn-info">'._('Hors Gel').'</button>';
		
		echo '</div>';
		
		echo '<br/><br/><b>'._('Fans').'</b><br/>';
		
		echo '<button onclick="" class="btn btn-info">'._('1').'</button> ';
		echo '<button onclick="" class="btn btn-info">'._('2').'</button> ';
		echo '<button onclick="" class="btn btn-info">'._('3').'</button> ';
		echo '<button onclick="" class="btn btn-info">'._('4').'</button> ';
		
		/*
		if (!empty($device->device_opt->{367})){
			echo '<button onclick="RemoteAudio(\'prev\', \''.$device->room_device_id.'\', \''.$device->device_opt->{367}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-backward"></span></button>';
		}
		if (!empty($device->device_opt->{364})){
			echo '<button onclick="RemoteAudio(\'pause\', \''.$device->room_device_id.'\', \''.$device->device_opt->{364}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-pause"></span></button>';
		}
		if (!empty($device->device_opt->{363})){
		 echo '<button onclick="RemoteAudio(\'play\', \''.$device->room_device_id.'\', \''.$device->device_opt->{363}->option_id.'\')" class="btn btn-info"><span class="glyphicon glyphicon-play"></span></button>';
		}
		*/
	 echo '</div>'.
		'</div>
		<div class="center">'.
			'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
		'</div>';
}

?>