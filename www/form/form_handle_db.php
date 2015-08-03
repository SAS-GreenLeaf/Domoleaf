<?php 

include('header.php');

if (!empty($_GET['filename']) && !empty($_GET['status'])){
	if ($_GET['status'] == 1){
	$request =  new Api();
	$request -> add_request('confDbRestore');
	$result  =  $request -> send_request();
	}

	else if ($_GET['status'] == 2){
		$request =  new Api();
		$request -> add_request('confDbRemove');
		$result  =  $request -> send_request();
	}
}
?>
