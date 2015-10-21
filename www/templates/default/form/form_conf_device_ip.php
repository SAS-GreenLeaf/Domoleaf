<?php 

include('header.php');

$request =  new Api();
$request -> add_request('monitorIp');
$result  =  $request -> send_request();

$listip = $result->monitorIp;

echo '
<table class="table table-top table-bordered table-striped table-condensed">
	<thead>
		<th>'._('Hostname').'</th>
		<th>'._('Ip').'</th>
	</thead>
	<tbody>';
	foreach ($listip as $elem) {
		$pos = strpos($elem->hostname, 'MD3');
		$pos1 = strpos($elem->hostname, 'SD3');
		if ($pos === false 	&& $pos1 === false) {
		echo '
			<tr class="cursor" onclick="SelectedRowIp(\''.$elem->hostname.'\', \''.$elem->ip_addr.'\', \''.$elem->mac_addr.'\')">
				<td>'.$elem->hostname.'</td>
				<td>'.$elem->ip_addr.'</td>
			</tr>';
		}
	}
	echo '
	</tbody>
</table>';

?>