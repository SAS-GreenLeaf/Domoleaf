<?php 

include('header.php');

$request =  new Api();
$request -> add_request('monitorbluetooth');
$result  =  $request -> send_request();

$listbluetooth = $result->monitorbluetooth;

if (!empty($listbluetooth)){
echo '
	<table class="table table-bordered table-strbluetoothed table-condensed">
		<thead>
			<tr>
				<th class="center">'._('Hostname').'</th>
				<th class="center">'._('bluetooth address').'</th>
				<th class="center">'._('Mac address').'</th>
			</tr>
		</thead>
		<tbody>';
		foreach($listbluetooth as $elem){
			echo '	<tr>
						<td>'.$listbluetooth->hostname.'</td>
						<td>'.$listbluetooth->bluetooth_addr.'</td>
						<td>'.$listbluetooth->mac_addr.'</td>
					</tr>';
		}
echo '
		</tbody>
	</table>';
}
else{
	echo '
	<div id="warningspan" class="alert alert-warning center col-xs-6 col-xs-offset-3 col-lg-10 col-lg-offset-1" role="alert">
		<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
		<span class="sr-only">Error:</span>
		'._('No bluetooth device.').'
	</div>';
}

?>