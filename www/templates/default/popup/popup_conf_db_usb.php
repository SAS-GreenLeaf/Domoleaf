<?php

include('header.php');

$request =  new Api();
$result  =  $request -> send_request();

echo '
	<div class="col-xs-12 center">'.
		'<button type="button" id="createBackupUsb" class="btn btn-greenleaf" onclick="CreateBackupUsb()">'.
			'<i class="fa fa-floppy-o"></i> '.
			_('Create Backup').
		'</button>'.
	'</div>'.
	'<br/>'.
	'<br/>'.
'<div id="listDbUsb"><div class="center"><br/>'._('Loading in progress...').'<br/><br/></div></div>';

echo '<script type="text/javascript">ListDbUsb()</script>';

?>
