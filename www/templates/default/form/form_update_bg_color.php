<?php

include('header.php');

if (!empty($_GET['color'])) {
	$request = new Api();
	$request -> add_request('userUpdateBGColor', array($_GET['color'], $_GET['userid']));
	$result  = $request -> send_request();
	echo $_GET['color'];
}
else {
	echo '0';
}
?>