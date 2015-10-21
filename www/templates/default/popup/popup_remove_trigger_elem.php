<?php

include('header.php');

if (empty($_GET['condition_id']) || $_GET['condition_id'] <= 0){
	redirect();
}

echo '
<div class="center">'.
	''._('Do you want to delete this condition ?').''.
'</div>'.
'<div class="controls center margin-top">'.
	'<button onclick="RemoveTriggerElem('.$_GET['condition_id'].')" class="btn btn-success">'.
		''._('Yes').''.
		'<span class="glyphicon glyphicon-ok"></span>'.
	'</button> '.
	'<button onclick="popup_close_last()" class="btn btn-danger">'.
		''._('No').''.
		'<span class="glyphicon glyphicon-remove"></span>'.
	'</button>'.
'</div>';

?>