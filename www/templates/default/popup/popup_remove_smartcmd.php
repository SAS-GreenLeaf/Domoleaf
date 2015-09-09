<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

if (empty($_GET['id_smartcmd']) || $_GET['id_smartcmd'] <= 0){
	redirect();
}

echo '
<div class="center">'.
	''._('Do you want to delete this Smartcommand ?').''.
'</div>'.
'<div class="controls center margin-top">'.
	'<button onclick="RemoveSmartcmd('.$_GET['id_smartcmd'].')" class="btn btn-success">'.
		''._('Yes').''.
		'<span class="glyphicon glyphicon-ok"></span>'.
	'</button> '.
	'<button onclick="popup_close_last()" class="btn btn-danger">'.
		''._('No').''.
		'<span class="glyphicon glyphicon-remove"></span>'.
	'</button>'.
'</div>';

?>