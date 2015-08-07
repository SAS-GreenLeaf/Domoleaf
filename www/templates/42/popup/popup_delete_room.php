<?php 

include('header.php');

if (!empty($_GET['roomid']) && !empty($_GET['floorid'])){
	$request =  new Api();
	$request -> add_request('confRoomList');
	$result  =  $request -> send_request();
	
	echo '
	<div class="center">'._('Are you sure you want to delete this room ?').'</div>
	<div class="controls center">
		<button id="eventSave" onclick="RemoveRoom('.$_GET['roomid'].','.$_GET['floorid'].')" class="btn btn-success">'._('Yes').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('No').' <span class="glyphicon glyphicon-remove"></span></button>
	</div>';
}
?>
