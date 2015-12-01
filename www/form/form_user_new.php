<?php

include('header.php');

if (!empty($_GET['username']) && !empty($_GET['password']) && !empty($_GET['lastname']) && !empty($_GET['firstname'])){
	$request =  new Api();
	$request -> add_request('profileNew', array($_GET['username'], $_GET['password']));
	$result  =  $request -> send_request();
	$new = $result->profileNew;
	if (!empty($new)){
		$request =  new Api();
		$request -> add_request('profileRename', array($_GET['lastname'], $_GET['firstname'], '', '', '', '', $new));
		$request -> send_request();
		echo $new;
	}else{
		echo '0';
	}
}else {
	echo '0';
}

?>
