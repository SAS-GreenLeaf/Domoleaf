<?php
	if ($_SERVER['REQUEST_METHOD'] != 'POST'){ exit; }
	
	include('../../../config.php');
	include('../../../functions.php');
	include('../../../libs/Link.class.php');
	
	include('../../../libs/Guest.class.php');
	include('../../../libs/User.class.php');
	include('../../../libs/Admin.class.php');
	include('../../../libs/Root.class.php');
	include('../../../libs/Api.class.php');
	
	$id = $_POST['id'];
	$data_x = $_POST['x'];
	$data_y = $_POST['y'];
	
	if (!is_numeric($id)){ echo json_encode(array("error"=>_('An error occured, please reload the page.'))); exit; }
	
		$api_request = new Api();
		$api_result  = $api_request -> send_request();
		
		$link = Link::get_link('mastercommand');
	
	if (!$api_request->is_co()){
		echo json_encode(array('error'=>_('You have to be logged on to do that.')));
		exit;
	}
	
	$user_id = $api_request->getId();
	
	$sql = 'SELECT room_id FROM room_device WHERE room_device_id = :room_device_id';
	$req = $link->prepare($sql);
	$req->bindValue(':room_device_id', $id, PDO::PARAM_INT);
	$req->execute() or die (error_log(serialize($req->errorInfo())));
	
	if ($req->rowCount() != 1){ echo json_encode(array('error'=>_('This device doesn\'t exist to you.'))); exit; }
	
	$do = $req->fetch(PDO::FETCH_OBJ);
	
	$sql = 'SELECT * FROM user_room WHERE user_id = :user_id AND room_id = :room_id';
	$req = $link->prepare($sql);
	$req->bindValue(':user_id', $user_id, PDO::PARAM_INT);
	$req->bindValue(':room_id', $do->room_id, PDO::PARAM_INT);
	$req->execute() or die(error_log(serialize($req->errorIngo())));
	
	if ($req->rowCount() != 1){ echo json_encode(array('error' => _('This device doesn\'t belong to you.'))); exit; }
	
	if (!preg_match("#[0-9]{1,3}\/[0-9]{1,3}#", $data_x) || !preg_match("#[0-9]{1,3}\/[0-9]{1,3}#", $data_y)){
		echo json_encode(array('error' => _('The given position isn\'t correct.'))); exit;
	}
	
	$sql = 'UPDATE room_device
		       SET pos_x_icon= :pos_x_icon , pos_y_icon = :pos_y_icon
		       WHERE room_device_id=:room_device_id';
	$req = $link->prepare($sql);
	$req->bindValue(':pos_x_icon', $data_x, PDO::PARAM_STR);
	$req->bindValue(':pos_y_icon', $data_y, PDO::PARAM_STR);
	$req->bindValue(':room_device_id', $id, PDO::PARAM_INT);
	$req->execute() or die (error_log(serialize($req->errorInfo())));
	
	echo json_encode(array('success'=>_('The position have been successfully updated.'))); exit;