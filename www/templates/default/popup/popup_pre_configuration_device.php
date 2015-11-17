<?php

include('header.php');

if (empty($_GET['device_id'])){
	echo '<script type="text/javascript">popup_close();</script>';
}

$request =  new Api();
$request -> add_request('confManufacturerList', array($_GET['device_id']));
$result  =  $request -> send_request();

$manufacturerList = $result->confManufacturerList;

echo '<div class="clearfix"></div>'.
	'<div class="control-group" >'.
		'<label class="control-label" for="manufacturerList">'._('Manufacturer list').'</label>'.
		'<select class="selectpicker form-control" id="manufacturerList" onchange="ProductList('.$_GET['device_id'].')">'.
			'<option value="0">-- '._('Nothing selected').' --</option>';
			foreach ($manufacturerList as $manufacturer){
				echo '<option value="'.$manufacturer->manufacturer_id.'">'.$manufacturer->name.'</option>';
			}
echo 	'</select>'.
	'</div>';

echo '<div class="control-group" id="div-productList"></div>';

echo '<br/><br/><div class="controls center">'.
		'<button onclick="PreConfigurationDevice()" id="PreConfigurationDevice" class="btn btn-success">'._('Pre-configure').' <span class="glyphicon glyphicon-ok"></span></button> '.
		'<button onclick="popup_close()" class="btn btn-danger">'._('Close').' <span class="glyphicon glyphicon-remove"></span></button>'.
	 '</div>';

echo 

'<script type="text/javascript">'.

'$(".selectpicker").selectpicker();'.

'</script>';

?>
