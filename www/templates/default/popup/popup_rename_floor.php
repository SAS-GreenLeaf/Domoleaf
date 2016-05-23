<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confFloorList');
$result  =  $request -> send_request();

$floorlist = $result->confFloorList;

echo '
<div class="center">';
printf(_('Do you want to rename %s?'), '<strong>'.$floorlist->{$_GET['id']}->floor_name.'</strong>');
echo '</div>
<div class="controls center">	
	<div class="input-group">
	<span class="input-group-addon">'._('New name').'</span>'.
	  '<input type="text" id="newfloorname" class="form-control" value="'.$floorlist->{$_GET['id']}->floor_name.'" aria-describedby="basic-addon1">
	</div>
	<button  id="eventSave" onclick="FloorRename('.$_GET['id'].')" class="btn btn-success">'._('Save').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('Cancel').' <span class="glyphicon glyphicon-remove"></span></button>
</div>'.

'<script type="text/javascript">'.
	'$(document).ready(function(){'.
		'setTimeout(function(){'.
			'$("#newfloorname").focus();'.
		'}, 400);'.
	'});'.
'</script>';

?>
