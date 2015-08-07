<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confKnxAddrList');
$result  =  $request -> send_request();

$listknx = $result->confKnxAddrList;


echo '
<table class="table table-top table-bordered table-striped table-condensed">
	<thead>
		<th>'._('Physical Address').'</th>
	</thead>
	<tbody>';
	
	foreach ($listknx as $elem){
		echo '
		<tr class="cursor" onclick="SelectedRowKnx(\''.$elem.'\')">
			<td>'.$elem.'</td>
		</tr>';
	}
echo '
	</tbody>
</table>';

?>