<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confFloorList');
$result  =  $request -> send_request();

$floorlist = $result->confFloorList;

echo '
<div class="center">';
printf(_('Do you want to delete %s?'), '<strong>'.$floorlist->$_GET['id']->floor_name.'</strong>'); 
echo '</div>
<div class="controls center">
	<button  id="eventSave" onclick="floorRemove('.$_GET['id'].')" class="btn btn-greenleaf">'._('Yes').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('No').' <span class="glyphicon glyphicon-remove"></span></button>
</div>';

?>
