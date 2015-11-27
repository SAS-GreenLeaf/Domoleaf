<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confFloorList');
$result  =  $request -> send_request();

$floorlist = $result->confFloorList;

echo '<div class="center"><strong>'._('Add a new room').'</strong></div>';

echo '<div class="controls center">
		<select class="selectpicker form-control" id="floorsel" name="select">';
		foreach ($floorlist as $elem){
			echo '<option value="'.$elem->floor_id.'">'.$elem->floor_name.'</option>';
		}
echo	'</select><br/><br/>'.
		'<div class="input-group margin-bottom">'.
			'<span class="input-group-addon">'._('New room').'</span>'.
			'<input type="text" id="newroom" placeholder="'._('Enter the room name').'" class="form-control">'.
		'</div>'.
		'<button id="eventSave"'.
		        'onclick="RoomNew()"'.
		        'class="btn btn-greenleaf margin-right">'.
			_('Yes').
			' <span class="glyphicon glyphicon-ok"></span>'.
		'</button>'.
		'<button onclick="popup_close()" class="btn btn-danger">'.
			_('No').
			' <span class="glyphicon glyphicon-remove"></span>'.
		'</button>'.
		'</div>'.
		'<script type="text/javascript">'.
			'$(document).ready(function(){'.
				'$(".selectpicker").selectpicker();'.
				'setTimeout(function(){'.
					'$("#newroom").focus();'.
				'}, 400);'.
			'});'.
		'</script>';

?>
