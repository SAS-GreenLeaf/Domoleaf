<?php 

include('header.php');

$request =  new Api();
$request -> add_request('monitorIp');
$result  =  $request -> send_request();

$listip = $result->monitorIp;

if (!empty($listip)){
echo '
	<table class="table table-bordered table-striped table-condensed">
		<thead>
			<tr>
				<th class="center">'._('Hostname').'</th>
				<th class="center">'._('Ip address').'</th>
				<th class="center">'._('Mac address').'</th>
			</tr>
		</thead>
		<tbody>';
		foreach($listip as $elem){
			echo '
			<tr>
				<td>'.$elem->hostname.'</td>
				<td>'.$elem->ip_addr.'</td>
				<td>'.$elem->mac_addr.'</td>
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
		'._('No Ip device.').'
	</div>';
}

?>