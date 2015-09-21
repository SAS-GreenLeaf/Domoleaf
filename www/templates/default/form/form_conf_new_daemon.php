<?php
 
include('header.php');

$request =  new Api();
$request -> add_request('monitorIp');
$request -> add_request('confDaemonList');
$result  =  $request -> send_request();

$listip = $result->monitorIp;
$listmonitor = $result->confDaemonList;

echo '
<table id="tableinfo" class="table table-bordered table-striped table-condensed">
	<thead>
		<tr>
			<th class="center">'._('Serial number').'</th>
			<th class="center">'._('Ip address').'</th>
		</tr>
	</thead>
	<tbody>';
	foreach($listip as $elem){
			
		$pos = strpos($elem->hostname, 'MD3');
		$pos1 = strpos($elem->hostname, 'SD3');
		if ($pos !== false 	or $pos1 !== false){
			echo '	
			<tr>'.
				'<td class="cursor" onclick="AutoFill(\''.$elem->hostname.'\')">'.$elem->hostname.'</td>'.
				'<td>'.$elem->ip_addr.'</td>'.
			'</tr>';
		}
	
	}
echo	'
	</tbody>
</table>	';

?>