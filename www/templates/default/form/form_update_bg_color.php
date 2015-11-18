<?php

include('header.php');

if (!empty($_GET['color']) && !empty($_GET['idelem'])) {
	$request = new Api();
	
	if ($_GET['idelem'] == 1) {
		$request -> add_request('userUpdateBGColor', array($_GET['color'], $_GET['userid']));
	}
	else {
		$request -> add_request('userUpdateMenusBordersColor', array($_GET['color'], $_GET['userid']));
	}
	$result  = $request -> send_request();
	echo $_GET['color'];
}
else {
	echo '0';
}
?>