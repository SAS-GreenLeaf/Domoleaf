<?php 

$request =  new Api();
$request -> add_request('confFloorList');
$request -> add_request('confRoomList', array(0));
$result  =  $request -> send_request();

echo '<title>'._('Installation').'</title>';

$floorlist = $result->confFloorList;
$roomlist = $result->confRoomList;

$roomlist = array();

foreach($result->confFloorList as $elem){
	if (empty($_GET['floor']) or $_GET['floor'] == $elem->floor_id){
		$roomlist[$elem->floor_id] = array(
			'id'   => $elem->floor_id,
			'name' => $elem->floor_name,
			'room' => array()
		);
		if (!empty($_GET['floor'])){
			break;
		}
	}
}

foreach ($result->confRoomList as $elem){
	if (!empty($roomlist[$elem->floor])){
		$roomlist[$elem->floor]['room'][] = array(
			'id'   => $elem->room_id,
			'name' => $elem->room_name
		);
	}
}

?>