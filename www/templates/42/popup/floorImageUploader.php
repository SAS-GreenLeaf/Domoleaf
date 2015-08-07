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
	
	if (!is_numeric($id)){ exit; }
	
		$api_request = new Api();
		$api_result  = $api_request -> send_request();
		
		$link = Link::get_link('mastercommand');
	
	if (!$api_request->is_co()){
		echo json_encode(array('error'=>_('You have to be logged on to do that.')));
		exit;
	}
	
	
	$user_id = $api_request->getId();
	
	$sql = 'SELECT * FROM user_floor WHERE user_id = :user_id AND floor_id = :floor_id';
	$req = $link->prepare($sql);
	$req->bindValue(':user_id', $user_id, PDO::PARAM_STR);
 	$req->bindValue(':floor_id', $id, PDO::PARAM_STR);
	$req->execute() or die (error_log(serialize($req->errorInfo())));
	$r = $req->rowCount();
	
	if ($r != 1){ echo json_encode(array('error'=>_('This floor doesn\'t belong to you.'))); exit; }
	
	$sql = 'SELECT floor_background_url
		        FROM floor
		        WHERE floor_id= :floor_id';
	$req = $link->prepare($sql);
	$req->bindValue(':floor_id', $id, PDO::PARAM_STR);
	$req->execute() or die (error_log(serialize($req->errorInfo())));
	$do = $req->fetch(PDO::FETCH_OBJ);
	
	if (!empty($do->floor_background_url)){
		unlink('/'.$do->floor_background_url);
	}
	
	$allowed = array('png', 'jpg', 'gif','zip');
	$target_dir = '../img/user_upload/';
	$dir = 'templates/'.TEMPLATE.'/img/user_upload/';
	
	$this_file = $_FILES[0];
	
	$extension = pathinfo($this_file['name'], PATHINFO_EXTENSION);

	if(!in_array(strtolower($extension), $allowed)){
		echo json_encode(array('error'=>_('The sent file does not exist')));
		exit;
	}
	
	$new_name = random_string(32).'.'.$extension;
	move_uploaded_file($this_file['tmp_name'], $target_dir.$new_name);
	
	$sql = 'UPDATE floor
		        SET floor_background_url= :floor_background_url
		        WHERE floor_id=:floor_id';
	$req = $link->prepare($sql);
	$req->bindValue(':floor_background_url', $dir.$new_name, PDO::PARAM_STR);
	$req->bindValue(':floor_id', $id, PDO::PARAM_INT);
	$req->execute() or die (error_log(serialize($req->errorInfo())));
	
	echo json_encode(array('success'=>_('Your upload have been successful.')));
	exit;
	
	
	function random_string($length) {
		$key = '';
		$keys = array_merge(range(0, 9), range('a', 'z'));

		for ($i = 0; $i < $length; $i++) {
			$key .= $keys[array_rand($keys)];
		}

		return $key;
	} 