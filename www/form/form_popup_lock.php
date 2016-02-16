<?php
session_start();
include('header.php');

if(!empty($_GET['iddevice'])) {
	$_SESSION['widget'][$_GET['iddevice']] =0;
}

?>