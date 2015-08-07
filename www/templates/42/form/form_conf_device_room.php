<?php 

include('header.php');

if (!empty($_GET['floor'])){
	
	$request =  new Api();
	$request -> add_request('confRoomList', array($_GET['floor']));
	$result  =  $request -> send_request();
	
	$devroomlist = $result->confRoomList;
	
	foreach($devroomlist as $elem){
		echo '<option value="'.$elem->room_id.'">'.$elem->room_name.'</option>';
	}
}

?>