<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confDaemonList');
$request -> add_request('confDaemonProtocolList');
$result  =  $request -> send_request();

$listdaemon = $result->confDaemonList;
$listproto = $result->confDaemonProtocolList;

$daemon =  $listdaemon->$_GET['id'];

echo '<div class="controls">';
echo '<div class="center">';printf(_('Do you want to rename %s?'), $daemon->name);
echo	'
		
		</div><div class="input-group">
			<label class="input-group-addon">'.
			'<span class="glyphicon glyphicon-hdd" aria-hidden="true"></span>
			</label>'.
			'<input type="text" id="redaemon" placeholder="'._('Enter daemon name').'" value="'.$daemon->name.'" class="form-control">
		</div>
		<div class="input-group">
			<label class="input-group-addon">'.
			'<span class="glyphicon glyphicon-console" aria-hidden="true"></span>
			</label>'.
			'<input type="text" id="reserial" placeholder="'._('Enter the serial number').'" value="'.$daemon->serial.'" class="form-control">
		</div>			
		<div class="input-group">
			<label class="input-group-addon">'.
			'<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
			</label>'.
			'<input type="password" id="resercretkey" placeholder="'._('Enter the secret key').'" class="form-control">
		</div>';
		foreach ($listproto as $elem){
			if (!empty($daemon->protocol->{$elem->protocol_id})){
				echo '<div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">'.
							'<label class="checkbox">'.
								'<input id="checkbox-protocol-'.$elem->protocol_id.'" type="checkbox"'.
									'onclick="CheckKNXTP()" checked="checked" class="checkbox-daemon" value="'.$elem->protocol_id.'">'.
								''.$elem->name.''.
							'</label>'.
						'</div>';
			}
			else{
				echo '<div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12">'.
						'<label class="checkbox">'.
							'<input id="checkbox-protocol-'.$elem->protocol_id.'" type="checkbox"'.
								'onclick="CheckKNXTP()" class="checkbox-daemon" value="'.$elem->protocol_id.'">'.
							''.$elem->name.''.
						'</label>'.
					'</div>';
			}
		}

echo '<div id="div-interface">'.
		'<select id="select-interface" class="selectpicker form-control" onchange="CheckKNXTPIP()">';
			if ($daemon->protocol->{1}->interface == "ttyS1"){
				echo '<option value="ttyS0">'._('Serial 0').'</option>'.
					 '<option selected value="ttyS1">'._('Serial 1').'</option>'.
					 '<option value="IP">'._('IP').'</option>';
			}
			else if (filter_var($daemon->protocol->{1}->interface, FILTER_VALIDATE_IP)){
				echo '<option value="ttyS0">'._('Serial 0').'</option>'.
					 '<option value="ttyS1">'._('Serial 1').'</option>'.
					 '<option selected value="IP">'._('IP').'</option>';				
			}
			else{
				echo '<option selected value="ttyS0">'._('Serial 0').'</option>'.
					 '<option value="ttyS1">'._('Serial 1').'</option>'.
					 '<option value="IP">'._('IP').'</option>';				
			}
 
		echo '</select>'.
	'</div>'.
	'<div id="div-interface-IP" class="input-group">'.
		'<label class="input-group-addon" for="label-interface-IP">'.
			'<span class="glyphicon glyphicon-user" aria-hidden="true"></span>'.
		'</label>';
		if (filter_var($daemon->protocol->{1}->interface, FILTER_VALIDATE_IP)){
			echo '<input type="text" id="input-interface-IP" placeholder="'._('Enter IP').'" value="'.$daemon->protocol->{1}->interface.'" class="form-control">';
		}
		else{
			echo '<input type="text" id="input-interface-IP" placeholder="'._('Enter IP').'" value="" class="form-control">';
		}
	echo '</div>'.
	'<script type="text/javascript">'.
		'CheckKNXTPIP();'.
		'$(".selectpicker").selectpicker();'.
		'CheckKNXTP();'.
	'</script>';

echo '		
	<div class="center"><button id="eventSave" onclick="RenameDaemon('.$daemon->daemon_id.')" class="btn btn-greenleaf">'._('Save').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('Cancel').' <span class="glyphicon glyphicon-remove"></span></button></div>'.
'</div>';

?>