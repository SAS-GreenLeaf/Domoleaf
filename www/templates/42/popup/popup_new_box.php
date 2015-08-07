<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confProtocolAll');
$result  =  $request -> send_request();

$listproto = $result->confProtocolAll;

echo '</div><div class="controls">
			<div class="input-group">
				<span class="input-group-addon special-size">'._('New Box').'</span>'.
				'<input type="text" id="newbox" placeholder="'._('Enter the Box name').'" class="form-control form-control-popup">
			</div>
			<div class="input-group">
				<span class="input-group-addon special-size">'._('Serial number').'</span>'.
				'<input type="text" id="newserial" placeholder="'._('Enter the serial number').'" class="form-control form-control-popup">
			</div>
		<div class="input-group">
			<span class="input-group-addon special-size">'._('Ip').'</span>'.
			'<input type="text" id="newserial" placeholder="'._('Enter the Ip (optional) ').'" class="form-control form-control-popup">
		</div><div class="input-group">
				';
		foreach ($listproto as $elem){
			echo '<div class="col-lg-4 col-lg-offset-2 col-sm-6 col-xs-12"><label class="checkbox"><input type="checkbox" value="proto-'.$elem->protocol_id.'">'.$elem->name.'</label></div>';
		}
echo '</div>
	<div class="center"><button  id="eventSave" onclick="" class="btn btn-greenleaf">'._('Save').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('Cancel').' <span class="glyphicon glyphicon-remove"></span></button></div>'.
'</div>';

?>