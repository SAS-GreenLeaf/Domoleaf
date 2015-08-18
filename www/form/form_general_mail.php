<?php 

include('header.php');

if (empty($_POST['fromMailval'])){
	$_POST['fromMailval'] = '';
}

if (empty($_POST['fromNameval'])){
	$_POST['fromNameval'] = '';
}

if (empty($_POST['smtpHostval'])){
	$_POST['smtpHostval'] = '';
}

if (empty($_POST['smtpSecureval'])){
	$_POST['smtpSecureval'] = -1;
}

if (empty($_POST['smtpPortval'])){
	$_POST['smtpPortval'] = 0;
}

if (empty($_POST['smtpUsernameval'])){
	$_POST['smtpUsernameval'] = '';
}

if (empty($_POST['smtpPasswordval'])){
	$_POST['smtpPasswordval'] = '';
}

$request =  new Api();
$request -> add_request('confMail', array($_POST['fromMailval'], $_POST['fromNameval'], $_POST['smtpHostval'], $_POST['smtpSecureval'], $_POST['smtpPortval'], $_POST['smtpUsernameval'], $_POST['smtpPasswordval']));
$result  =  $request -> send_request();

?>