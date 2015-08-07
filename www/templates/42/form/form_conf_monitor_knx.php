<?php 

include('header.php');

$request =  new Api();
$request -> add_request('monitorKnx');
$request -> add_request('confDaemonList');
$result  =  $request -> send_request();

$listknx = $result->monitorKnx;
$listDaemon = $result->confDaemonList;

if (!isset($_GET['id'])){
	exit();
}

if (!empty($listknx)){
	$listtype = array(_('Read'), _('Answer'), _('Write (short)'), _('Write (long)'));
echo '
	<table class="table table-bordered table-striped table-condensed">
		<thead>
			<tr>
				<th>'._('Daemon').'</th>
				<th class="center">'._('Type').'</th>
				<th class="center">'._('Source').'</th>
				<th class="center">'._('Destination').'</th>
				<th class="center">'._('Value').'</th>
				<th class="center">'._('Date').'</th>
			</tr>
		</thead>
		<tbody>';
		foreach($listknx as $elem){
			if ($_GET['id'] == -1 or $elem->daemon_id == $_GET['id']){
			echo '
				<tr>
					<td>';
					if (!empty($elem->daemon_id) && !empty($listDaemon) && !empty($listDaemon->{$elem->daemon_id})){
						echo $listDaemon->{$elem->daemon_id}->name;
					}
					else{
						echo _('Unknow');
					}
			echo '
					</td>
					<td>'.$listtype[$elem->type].'</td>
					<td>'.$elem->addr_src.'</td>
					<td>'.$elem->addr_dest.'</td>
					<td>'.$elem->knx_value.'</td>
					<td>'.$request->date($elem->t_date, 3).'</td>
				</tr>';
			}
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
		'._('No monitor KNX.').'
	</div>';
}

?>