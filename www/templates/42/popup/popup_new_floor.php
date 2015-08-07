<?php 

include('header.php');

$request =  new Api();
$request -> add_request('confFloorNew');
$result  =  $request -> send_request();

echo '<div class="center"><strong>'._('Add a new floor').'</strong></div>';
echo '</div>
<div class="controls center">	
	<div class="input-group">
		<span class="input-group-addon">'._('New floor').'</span>'.
	  '<input type="text" id="newfloor" placeholder="'._('Enter the floor name').'" class="form-control">
	</div>
	<button  id="eventSave" onclick="FloorNew()" class="btn btn-greenleaf">'._('Save').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('Cancel').' <span class="glyphicon glyphicon-remove"></span></button>
</div>';

?>
