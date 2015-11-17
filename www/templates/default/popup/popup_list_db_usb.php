<?php

include('header.php');

$request =  new Api();
$request -> add_request('confDbListUsb');
$result  =  $request -> send_request();

$listBackupUsb = $result->confDbListUsb;

	if (!empty($listBackupUsb && sizeof($listBackupUsb > 0))){
		echo '<table class="table table-bordered table-striped table-condensed">'.
			'<thead>'.
				'<th>'._('File').'</th>'.
				'<th>'._('Size').'</th>'.
				'<th>'._('Action').'</th>'.
			'</thead>'.
			'<tbody>';

			foreach($listBackupUsb as $elem){
				$parse = explode("_", $elem->name);
				echo '<tr>'.
					'<td>';
						if (!empty($parse[2]) && $parse[2] > 0){
							echo format_date($parse[2]);
						}
						else{
							echo _('Unknown');
						}
				echo '</td>'.
					'<td class="center">'.
						format_size($elem->size).
					'</td>'.
					'<td class="center">'.
					'<button type="button" title="'._('Restore backup').'" class="btn btn-warning" onclick="PopupRestoreDbUsb(\''.$parse[2].'\')">'.
					'<i class="fa fa-reply"></i>'.
					'</button> '.
					'<button type="button" title="'._('Delete backup').'" class="btn btn-danger" onclick="PopupRemoveDbUsb(\''.$parse[2].'\')">'.
					'<i class="fa fa-trash-o"></i>'.
					'</button>'.
					'</td>'.
				'</tr>';
			}

			echo '</tbody>'.
		'</table>.';
	}
	else {
		echo '
		<div class="clearfix"></div>
		<br/>
		<div class="alert alert-warning alert-dismissible col-xs-3 col-xs-offset-4 alert-backup center" role="alert" id ="signerr" >
			<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'.
					 _('No backup created').'
		</div>';
	};
	echo '<div class="center">'.
		'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
	'</div>';
?>