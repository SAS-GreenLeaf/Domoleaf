<?php 

include('header.php');

if (empty($_POST['highCostval'])){
	$_POST['highCostval'] = '';
}

if (empty($_POST['lowCostval'])){
	$_POST['lowCostval'] = '';
}

if (empty($_POST['lowField1val'])){
	$_POST['lowField1val'] = '';
}

if (empty($_POST['lowField2val'])){
	$_POST['lowField2val'] = '';
}

if (empty($_POST['currencyval'])){
	$_POST['currencyval'] = '';
}

$request =  new Api();
$request -> add_request('confPriceElec', array($_POST['highCostval'], $_POST['lowCostval'], $_POST['lowField1val'], $_POST['lowField2val'], $_POST['currencyval']));
$result  =  $request -> send_request();

?>