<?php 

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

echo '<div class="alert alert-danger alert-hidden alert-dismissible" role="alert" id ="signerr" >'
			.'<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'
			.'<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'
			.'<span class="sr-only">'._('Error:').'</span> '._('Username already exist').'</div>
	<div class="controls center">
	<div class="input-group">
		<label class="input-group-addon">'.
			'<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
		</label>'.
		'<input type="text" id="newusername" placeholder="'._('Enter the Username').'" class="form-control">
	</div>
	<div class="input-group">
		<label class="input-group-addon">'.
			'<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
		</label>'.
		'<input type="text" id="newlastname" placeholder="'._('Enter the Lastname').'" class="form-control">
	</div>
	<div class="input-group">
		<label class="input-group-addon">'.
			'<span class="glyphicon glyphicon-user" aria-hidden="true"></span>
		</label>'.
		'<input type="text" id="newfirstname" placeholder="'._('Enter the Firstname').'" class="form-control">
	</div>
	<div class="input-group">
		<label class="input-group-addon">'.
			'<span class="glyphicon glyphicon-lock" aria-hidden="true"></span>
		</label>'.
		'<input type="password" id="newpassword" placeholder="'._('Enter the Password').'" class="form-control">
	</div>
	<button  id="eventSave" onclick="NewUser()" class="btn btn-greenleaf">'._('Save').' <span class="glyphicon glyphicon-ok"></span></button> <button onclick="popup_close()" class="btn btn-danger">'._('Cancel').' <span class="glyphicon glyphicon-remove"></span></button>
</div>'.
'<script type="text/javascript">'.
	'$(document).ready(function(){'.
		'setTimeout(function(){'.
			'$("#newusername").focus();'.
		'}, 400);'.
	'});'.
'</script>';

?>
