<?php

include('header.php');

if (!empty($_GET['product_id'])){
	$request =  new Api();
	$request -> add_request('confProductOptionList', array($_GET['product_id']));
	$result  =  $request -> send_request();
	if(!empty($_GET['password'])) {
		//Replace password
		$confProductOptionList = str_replace('[-password-]', $_GET['password'], json_encode($result->confProductOptionList));
	}
	else {
		$confProductOptionList = json_encode($result->confProductOptionList);
	}
	
	echo $confProductOptionList;
}

?>
