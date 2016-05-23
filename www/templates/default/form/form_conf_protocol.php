<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confProtocolAll');
$request -> add_request('confDeviceAll');
$request -> add_request('confDeviceProtocol', array($_GET['iddevice']));
$result  =  $request -> send_request();

$alldevice = $result->confDeviceAll;
$allproto = $result->confProtocolAll;
$protodevice =  $result->confDeviceProtocol;

foreach ($allproto as $elem){
	if (in_array($elem->protocol_id, $protodevice)){
		if ($elem->protocol_id == $alldevice->{$_GET['iddevice']}->protocol_id){
			echo '<option value="'.$elem->protocol_id.'" selected>'.$elem->name.'</option>';
		}
		else{
			echo '<option value="'.$elem->protocol_id.'">'.$elem->name.'</option>';
		}
	}
}

?>