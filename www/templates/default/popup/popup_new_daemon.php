<?php 

include('header.php');

$request =  new Api();
$request -> add_request('monitorIp');
$request -> add_request('confDaemonList');
$result  =  $request -> send_request();

$listip = $result->monitorIp;
$listmonitor = $result->confDaemonList;

echo '<div class="center"><strong>'._('Create a new box').'</strong></div>';
echo '	<div class="col-xs-6">
			<div class="input-group">
				<label class="input-group-addon">'.
				'<span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
				</label>'.
				'<input type="text" id="newdaemon" placeholder="'._('Enter the Box name').'" class="form-control">
			</div>
			<div class="input-group">
				<label class="input-group-addon">'.
				'<span class="glyphicon glyphicon-console" aria-hidden="true"></span>
				</label>'.
				'<input type="text" id="newserial" placeholder="'._('Enter the serial number').'" class="form-control">
			</div>			
			<div class="input-group">
				<label class="input-group-addon">'.
				'<span class="fa fa-key" aria-hidden="true"></span>
				</label>'.
				'<input type="password" id="newsercretkey" placeholder="'._('Enter the secret key').'" class="form-control">
			</div>
		</div>'.
		'<div id="tableinfo" class="col-xs-6">'.
			'<table id="tableinfo" class="table table-bordered table-striped table-condensed">'.
				'<thead>'.
					'<tr>'.
					'<th class="center">'._('Serial number').'</th>'.
					'<th class="center">'._('Ip address').'</th>'.
					'</tr>'.
				'</thead>'.
				'<tbody>';
			foreach($listip as $elem){
					
				$pos = strpos($elem->hostname, 'MD3');
				$pos1 = strpos($elem->hostname, 'SD3');
				if ($pos !== false or $pos1 !== false or $elem->ip_addr == '127.0.0.1') {
					echo '	<tr>'.
							'<td class="cursor" onclick="AutoFill(\''.$elem->hostname.'\')">'.$elem->hostname.'</td>'.
							'<td>'.$elem->ip_addr.'</td>'.
							'</tr>';
				}
			
			}
		echo	'</tbody>'.
			'</table>'.
		'</div>'.
		'<div class="col-xs-12"><div class="alert alert-danger alert-hidden alert-dismissible" role="alert" id ="signerr" >'.
			'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'.
			'<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'.
			'<span class="sr-only">'._('Error:').'</span> '._('Empty field').'</div>'.
		'</div>'.
	'<div class="clearfix"></div>'.
	'<div class="controls center">		
		<button  id="eventSave" onclick="NewDaemon()" class="btn btn-greenleaf">'._('Save').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('Cancel').' <span class="glyphicon glyphicon-remove"></span></button>
	</div>'.
	'<script type="text/javascript">setInterval(function() { ListDaemon() }, 2000);</script>';

?>