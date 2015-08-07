<?php
	if ($_SERVER['REQUEST_METHOD'] != 'POST'){ exit; }
	
	$id = $_POST['id'];
	
	if (empty($id) || !is_numeric($id)){
		exit;
	}
	
	include('../../../config.php');
	include('../../../libs/Link.class.php');
	
	$link = Link::get_link('mastercommand');
	
	$sql = 'SELECT floor_background_url, floor_name
		        FROM floor
		        WHERE floor_id= :floor_id';
	$req = $link->prepare($sql);
	$req->bindValue(':floor_id', $id, PDO::PARAM_INT);
	$req->execute() or die (error_log(serialize($req->errorInfo())));
	$do = $req->fetch(PDO::FETCH_OBJ);
	
	$result = $do->floor_background_url;
	
	echo _('Update '.$do->floor_name.'\'s background');
	echo '|||';
	
	if (empty($result)){
		echo _('You don\'t have any background image for this floor.');
		echo '|||';
		echo _('Load one');
	} else {
		echo _('This floor already has a background image:');
		echo '|||';
		echo $result;
		echo '|||';
		echo _('Load another one');
		echo '|||';
		echo _('Delete it');
	}
	
	echo '|||';
	echo _('Close');