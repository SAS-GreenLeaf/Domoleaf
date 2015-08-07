<?php

include('header.php');

$request =  new Api();
$request -> add_request('confDbListLocal');
$result  =  $request -> send_request();

$listDbLocal = $result->confDbListLocal;

if (!empty($listDbLocal) && sizeof($listDbLocal) > 0) {
	echo '
			<table class="table table-bordered table-striped table-condensed">
				<thead>
					<th>'._('Date').'</th>
					<th>'._('Poids').'</th>
					<th>'._('Action').'</th>
				</thead>
				<tbody>';
	foreach($listDbLocal as $elem){
		$parse = explode("_", $elem->name);
		echo '
					<tr>
					<td>
						'.format_date($parse[2]).'
					</td>
					<td>
						'.format_size($elem->size).'
					</td>
					<td class="center">
						<button type="button" title="'._('Restore backup').'" class="btn btn-warning" onclick="RestoreDbLocal(\''.$parse[2].'\')">
							<i class="fa fa-reply"></i>
						</button>
						<button type="button" title="'._('Delete backup').'" class="btn btn-danger" onclick="PopupRemoveDbLocal(\''.$parse[2].'\')">
							<i class="fa fa-trash-o"></i>
						</button>
					</td>
					</tr>';
	}

	echo '
				</tbody>
			</table>';
}
else {
	echo '
			<div class="clearfix"></div>
			<br/>
			<div class="alert alert-warning alert-dismissible col-xs-3 col-xs-offset-4 alert-backup center" role="alert" id ="signerr" >
				<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'.
				_(' No backup created').'
			</div>';
}

?>