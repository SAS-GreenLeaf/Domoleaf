<?php 

include('header.php');

if (!empty($_GET['room'])){
	
	$request =  new Api();
	$request -> add_request('confRoomDeviceList', array($_GET['room']));
	$result  =  $request -> send_request();
	
	$listroomdevice = $result->confRoomDeviceList;
	
	$listproto = array(
		6 => 'Ip',
		1 => 'KNX',
		2 => 'Enocean'
	);

	if(!empty($listroomdevice)) {
		echo '
		<table class="table table-bordered table-striped table-condensed">
			<thead>
				<th>'._('Name').'</th>
				<th>'._('Protocol').'</th>
				<th>'._('Device').'</th>
				<th>'._('Address').'</th>
				<th>&nbsp</th>
			</thead>
			<tbody>';
			foreach($listroomdevice as $elem){
				echo '
				<tr>
					<td>
						<a href="/conf_installation/'.$_GET['floor'].'/'.$_GET['room'].'/'.$elem->room_device_id.'">
							'.$elem->name.'
						</a>
					</td>
					<td>'.$listproto[$elem->protocol_id].'</td>
					<td>'.$elem->device_name.'</td>
					<td>'.$elem->addr.'</td>
					<td class="center">
						<a title="'._('Edit').'" class="btn btn-primary" id="btn-edit-'.$elem->room_device_id.'" 
						   href="/conf_installation/'.$_GET['floor'].'/'.$_GET['room'].'/'.$elem->room_device_id.'">
							<span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
						</a>
						<button title="'._('Delete').'" onclick="PopupRemoveDevice('.$elem->room_device_id.')" class="btn btn-danger">
							<span aria-hidden="true" class="glyphicon glyphicon-trash"></span>
						</button>
					</td>
				</tr>';
			}
		echo'
			</tbody>
		</table>';
	}
}

?>