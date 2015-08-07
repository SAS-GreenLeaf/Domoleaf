<?php 

include('header.php');

$request =  new Api();
$request -> add_request('monitorEnocean');
$result  =  $request -> send_request();

$listEnocean = $result->monitorEnocean;

if (!empty($listEnocean)){
echo '
	<table class="table table-bordered table-striped table-condensed">
		<thead>
			<tr>
				<th class="center">'._('Type').'</th>
				<th class="center">'._('Address').'</th>
				<th class="center">'._('Date').'</th>
			</tr>
		</thead>
		<tbody>';
		foreach($listEnocean as $elem){
			echo '
			<tr>
				<td>'.$elem->type.'</td>
				<td>'.$elem->addr_src.'</td>
				<td>'.$request->date($elem->t_date, 3).'</td>
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
		'._('No Enocean device.').'
	</div>';
}

?>