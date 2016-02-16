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
			'<input type="text" id="redaemon" placeholder="'._('Enter the Box name').'" value="'.$daemon->name.'" class="form-control">
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

echo '<div id="div-interface-KNXTP">'.
		'<label class="control-label" for="label-interface-KNXTP">'._('KNX TP Interface').'</label>'.
		'<select id="select-interface-KNXTP" class="selectpicker form-control" title="KNX TP" onchange="CheckKNXTPType()">'.
			'<option value="tpuarts">'._('Serial Interface').'</option>'.
			'<option value="ipt">'._('IP Interface').'</option>'.
		'</select>'.
		'<div id="div-interface-KNXTP-Serial" class="margin-top-4">'.
			'<select id="select-interface-KNXTP-Serial" class="selectpicker form-control" onchange="">'.
				'<option value="ttyAMA0">'._('Serial').'</option>'.
				'<option value="ttyS0">'._('Serial 0').'</option>'.
				'<option value="ttyS1">'._('Serial 1').'</option>'.
				'<option value="ttyS2">'._('Serial 2').'</option>'.
			'</select>'.
		'</div>'.
		'<div id="div-interface-KNXTP-IP" class="margin-top-4 input-group">'.
			'<label class="input-group-addon" for="label-interface-KNXTP-IP">'.
				'<span class="glyphicon glyphicon-user" aria-hidden="true"></span>'.
			'</label>';
		if (!empty($daemon->protocol->{1}->interface) && filter_var($daemon->protocol->{1}->interface_arg, FILTER_VALIDATE_IP)){
			echo '<input type="text" id="input-interface-KNXTP-IP" placeholder="'._('Enter IP Adress').'" value="'.$daemon->protocol->{1}->interface_arg.'" class="form-control">';
		}
		else{
			echo '<input type="text" id="input-interface-KNXTP-IP" placeholder="'._('Enter IP Adress').'" value="" class="form-control">';
		}

echo 	'</div>'.
	 '</div>'.
	 '<br/>'.
	 '<div id="div-interface-EnOcean">'.
	 '<label class="control-label" for="label-interface-EnOcean">'._('EnOcean Interface').'</label>'.
		'<select id="select-interface-EnOcean" class="selectpicker form-control" title="EnOcean" onchange="CheckEnOceanType()">'.
			'<option value="usb">'._('USB Interface').'</option>'.
			'<option value="tpuarts">'._('Serial Interface').'</option>'.
			'<option value="ipt">'._('IP Interface').'</option>'.
		'</select>'.
		'<div id="div-interface-EnOcean-usb" class="margin-top-4">'.
			'<select id="select-interface-EnOcean-usb" class="selectpicker form-control" onchange="">'.
				'<option value="ttyUSB0">'._('USB 0').'</option>'.
				'<option value="ttyUSB1">'._('USB 1').'</option>'.
			'</select>'.
		'</div>'.
		'<div id="div-interface-EnOcean-Serial" class="margin-top-4">'.
			'<select id="select-interface-EnOcean-Serial" class="selectpicker form-control" onchange="">'.
				'<option value="ttyAMA0">'._('Serial').'</option>'.
				'<option value="ttyS0">'._('Serial 0').'</option>'.
				'<option value="ttyS1">'._('Serial 1').'</option>'.
				'<option value="ttyS2">'._('Serial 2').'</option>'.
			'</select>'.
		'</div>'.
		'<div id="div-interface-EnOcean-IP" class="margin-top-4 input-group">'.
			'<label class="input-group-addon" for="label-interface-EnOcean-IP">'.
				'<span class="glyphicon glyphicon-user" aria-hidden="true"></span>'.
			'</label>';
		if (!empty($daemon->protocol->{2}->interface) && filter_var($daemon->protocol->{2}->interface_arg, FILTER_VALIDATE_IP)){
			echo '<input type="text" id="input-interface-EnOcean-IP" placeholder="'._('Enter IP Adress').'" value="'.$daemon->protocol->{2}->interface_arg.'" class="form-control">';
		}
		else{
			echo '<input type="text" id="input-interface-EnOcean-IP" placeholder="'._('Enter IP Adress').'" value="" class="form-control">';
		}		

echo	'</div>'.
	 '</div>'.
	 '<script type="text/javascript">'.
	 	'$("#select-interface-KNXTP").selectpicker();'.
	 	'$("#select-interface-KNXTP-Serial").selectpicker();'.
	 	'$("#select-interface-EnOcean").selectpicker();'.
	 	'$("#select-interface-EnOcean-usb").selectpicker();'.
		'$("#select-interface-EnOcean-Serial").selectpicker();';
	
	if (!empty($daemon->protocol->{1}->interface)){
		if ($daemon->protocol->{1}->interface == 'tpuarts'){
			echo '$("#select-interface-KNXTP").selectpicker(\'val\', \'tpuarts\');';
			echo '$("#select-interface-KNXTP-Serial").selectpicker(\'val\', \''.$daemon->protocol->{1}->interface_arg.'\');';
		}
		else if (filter_var($daemon->protocol->{1}->interface_arg, FILTER_VALIDATE_IP)){
			echo '$("#select-interface-KNXTP").selectpicker(\'val\', \'ipt\');';
		}
	}
	else{
		echo '$("#select-interface-KNXTP").selectpicker(\'val\', \'\');';
	}
	
	if (!empty($daemon->protocol->{2}->interface)){
		if ($daemon->protocol->{2}->interface == 'usb'){
			echo '$("#select-interface-EnOcean").selectpicker(\'val\', \'usb\');';
			echo '$("#select-interface-EnOcean-usb").selectpicker(\'val\', \''.$daemon->protocol->{2}->interface_arg.'\');';
		}
		else if ($daemon->protocol->{2}->interface == 'tpuarts'){
			echo '$("#select-interface-EnOcean").selectpicker(\'val\', \'tpuarts\');';
			echo '$("#select-interface-EnOcean-Serial").selectpicker(\'val\', \''.$daemon->protocol->{2}->interface_arg.'\');';
		}
		else if (filter_var($daemon->protocol->{2}->interface_arg, FILTER_VALIDATE_IP)){
			echo '$("#select-interface-EnOcean").selectpicker(\'val\', \''.$daemon->protocol->{2}->interface_arg.'\');';
			echo '$("#select-interface-EnOcean").selectpicker(\'val\', \'ipt\');';
		}
	}
	else{
		echo '$("#select-interface-EnOcean").selectpicker(\'val\', \'\');';
	}
	
	echo 'CheckKNXTPType();'.
		 'CheckKNXTP();'.
		 'CheckEnOceanType();'.
		 'CheckEnOcean();'.
	 '</script>';

echo '		
	<div class="center"><button id="eventSave" onclick="RenameDaemon('.$daemon->daemon_id.')" class="btn btn-greenleaf">'._('Save').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('Cancel').' <span class="glyphicon glyphicon-remove"></span></button></div>'.
'</div>';

?>