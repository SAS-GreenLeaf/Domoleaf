<?php

include('header.php');

$request =  new Api();
$request -> add_request('confDbBackupUSB');
$result  =  $request -> send_request();

$obj2[] = array('FILENAME' => "mastercommand_backup_1438251631.01", 'SIZE' => 42);
$obj2[] = array('FILENAME' => "mastercommand_backup_1438251905.26", 'SIZE' => 84);

$listBackupUSB = $result->confDbBackupUSB;

if (!empty($listBackupUSB)){
	$obj2=json_decode($listBackupUSB);
}

$obj2=json_decode(json_encode($obj2));

		if (!empty($obj2)){
			echo '<table class="table table-bordered table-striped table-condensed">'.
				'<thead>'.
					'<th>'._('Fichier').'</th>'.
					'<th>'._('Size').'</th>'.
					'<th>'._('Action').'</th>'.
				'</thead>'.
				'<tbody>';
			
				foreach($obj2 as $elem){
					$parse = explode("_", $elem->FILENAME);
					echo '<tr>'.
						'<td>'.
							format_date($parse[2]).
						'</td>'.
						'<td>'.
							format_size($elem->SIZE).
						'</td>'.
						'<td class="center">'.
						'<button type="button" title="'._('Restore backup').'" class="btn btn-warning" onclick="RestoreDbUsb(\''.$elem->FILENAME.'\')">'.
						'<i class="fa fa-reply"></i>'.
						'</button> '.
						'<button type="button" title="'._('Delete backup').'" class="btn btn-danger" onclick="RemoveDbUsb(\''.$elem->FILENAME.'\')">'.
						'<i class="fa fa-trash-o"></i>'.
						'</button>'.
						'</td>'.
					'</tr>';
				}
				
				echo '</tbody>'.
			'</table>.';
		};
		echo '<div class="center">'.
			'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
		'</div>';
?>