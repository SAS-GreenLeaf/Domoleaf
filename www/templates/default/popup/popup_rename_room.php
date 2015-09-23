<?php

include('header.php');

$request =  new Api();
$request -> add_request('confRoomList');
$request -> add_request('confFloorList');
$result  =  $request -> send_request();

$roomlist = $result->confRoomList;
$floorlist = $result->confFloorList;

echo '<div class="center">';
	printf(_('Do you want to rename %s?'), '<strong>'.$roomlist->$_GET['idroom']->room_name.'</strong>'); 
echo'</div
<div class="controls center">	
	<div class="input-group">
	<span class="input-group-addon">'._('New name').'</span>'.
	  '<input type="text" id="newroomname" class="form-control" value="'.$roomlist->$_GET['idroom']->room_name.'" aria-describedby="basic-addon1">
	</div>
	<select class="selectpicker form-control" id="changefloor">';
	  	foreach($floorlist as $elem){
	  		if ($elem->floor_name == $roomlist->$_GET['idroom']->floor_name){
	  			echo '<option value="'.$elem->floor_id.'" selected>'.$elem->floor_name.'</option>';
	  		}
	  		else {
	  			echo '<option value="'.$elem->floor_id.'">'.$elem->floor_name.'</option>';
	  		}
	  	}	
echo
	'</select><br/><br/>'.
	'<button id="eventSave"'.
	        'onclick="RoomManager('.$_GET['idroom'].')"'.
	        'class="btn btn-greenleaf">'.
		''._('Save').''.
		'<span class="glyphicon glyphicon-ok"></span>'.
	'</button>'.
	'<button onclick="popup_close()" class="btn btn-danger">'.
		''._('Cancel').''.
		'<span class="glyphicon glyphicon-remove"></span>'.
	'</button>'.
'</div>'.
'<script type="text/javascript">'.
	'$(".selectpicker").selectpicker();'.
'</script>';

?>