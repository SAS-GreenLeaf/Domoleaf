<?php 

include('header.php');

if (!empty($_GET['msg']) && !empty($_GET['btn']))
{
echo '
	<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
	<span class="sr-only">'._('Error:').'</span> '.$_GET['msg'];
}
else if (!empty($_GET['msg'])){

echo '
	<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>
	<span class="sr-only">'._('Error:').'</span> '.$_GET['msg'];
}

?>