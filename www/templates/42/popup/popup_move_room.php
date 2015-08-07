<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confRoomList');
$request -> add_request('confFloorList');
$result  =  $request -> send_request();

if(empty($_GET['room']) or empty($result->confRoomList) or empty($result->confRoomList->$_GET['room'])){
	exit();
}

$floorlist = $result->confFloorList;
$room = $result->confRoomList->$_GET['room'];

echo '<div class="center">';printf(_('Move %s to an other floor'), '<strong>'.$room->room_name.'</strong>');echo '</div>';
echo '<div class="controls center">
		<select class="form-control" id="listroom" name="select">';
		foreach ($floorlist as $elem){
			if ($_GET['floor'] == $elem->floor_id){
				echo '<option value="'.$elem->floor_id.'" selected>'.$elem->name.'</option>';
			}
			else{
				echo '<option value="'.$elem->floor_id.'">'.$elem->name.'</option>';
			}
		}
echo	'</select><br/><br/>
  <button id="eventSave" onclick="MoveRoom('.$_GET['room'].')" class="btn btn-success">'._('Yes').' <span class="glyphicon glyphicon-ok"></span></button><button onclick="popup_close()" class="btn btn-danger">'._('No').' <span class="glyphicon glyphicon-remove"></span></button>
</div>';

?>
