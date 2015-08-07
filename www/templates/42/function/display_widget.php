<?php
	function floor_exist($id){
		if (!isset($id) || empty($id) || !is_numeric($id)){ return false; }
		
		$link = Link::get_link('mastercommand');
		$request = new Api();
		$result = $request->send_request();
	
		$sql = 'SELECT *
		        FROM user_floor
		        WHERE user_id= :user_id AND floor_id = :floor_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $request->getId(), PDO::PARAM_STR);
		$req->bindValue(':floor_id', $id, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$test = $req->rowCount();
		
		if ($test == 0){ return false; }
		
		return true;
	}
	
	function getDevices($id, $done){
		if (!floor_exist($id)){ return false; }
		
		$link = Link::get_link('mastercommand');
		$add = ($done) ? 'AND pos_x_icon NOT LIKE \'%/0\'' : 'AND pos_x_icon LIKE \'%/0\'';
		
		$sql = 'SELECT room_id
		        FROM room
		        WHERE floor=:floor';
		$req = $link->prepare($sql);
		$req->bindValue(':floor', $id, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$result = array(); 
		
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$this_room_id = $do->room_id;
			
			$sql_device = 'SELECT *
		        FROM room_device
		        WHERE room_id=:room_id '.$add;
			$req_device = $link->prepare($sql_device);
			$req_device->bindValue(':room_id', $do->room_id, PDO::PARAM_STR);
			$req_device->execute() or die (error_log(serialize($req_device->errorInfo())));
			
			if ($req_device->rowCount() > 0){
				$result[] = $req_device->fetch(PDO::FETCH_OBJ);
			}
		}
		
		return $result; 
	}
	
	function getFloorBackground($id){
		if (!floor_exist($id)){ return false; }
		
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT floor_background_url
		        FROM floor
		        WHERE floor_id=:floor_id';
		$req = $link->prepare($sql);
		$req->bindValue(':floor_id', $id, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if (empty($do->floor_background_url) || is_null($do->floor_background_url)){
			return null;
		} 
		
		return $do->floor_background_url;
	}
	
	$icons_device = array(
			1  => 'fa fa-question',
			2  => 'fa fa-video-camera',
			3  => 'fa fa-lightbulb-o',
			4  => 'fa fa-lightbulb-o',
			5  => 'fa fa-tachometer',
			6  => 'fa fa-lightbulb-o',
			7  => 'fa fa-question',
			8  => 'fa fa-question',
			9  => 'fa fa-question',
			10 => 'fa fa-bars',
			11 => 'fa fa-bars',
			12 => 'fi flaticon-snowflake149',
			13 => 'fi flaticon-snowflake149',
			14 => 'fa fa-volume-up',
			15 => 'fa fa-volume-up',
			17 => 'fa fa-volume-up',
			18 => 'fa fa-tree',
			19 => 'fi flaticon-winds4',
			20 => 'fa fa-fire lg',
			21 => 'fa fa-question',
			22 => 'fi flaticon-engineering',
			23 => 'fa fa-question',
			24 => 'fa fa-question',
			25 => 'fi flaticon-wind34',
			26 => 'fi flaticon-wind34',
			27 => 'fi flaticon-person206',
			28 => 'fa fa-question',
			29 => 'fa fa-video-camera',
			30 => 'fi flaticon-sign35',
			31 => 'fa fa-sort-amount-asc rotate--90',
			32 => 'fa fa-question',
			33 => 'fa fa-question',
			34 => 'fi flaticon-snowflake149',
			35 => 'fa fa-question',
			36 => 'fa fa-question',
			37 => 'fa fa-question',
			38 => 'fa fa-question',
			39 => 'fa fa-question',
			40 => 'fa fa-question',
			41 => 'fa fa-question',
			42 => 'fa fa-question',
			43 => 'fa fa-question',
			44 => 'fa fa-question',
			45 => 'fa fa-question',
			46 => 'fa fa-question',
			47 => 'fa fa-bolt',
			48 => 'fa fa-question',
			49 => 'flaticon-thermometer2',
			50 => 'fa fa-volume-up',
			51 => 'fa fa-question',
			52 => 'fa fa-sort-amount-asc rotate--90',
			53 => 'fa fa-wifi',
			55 => 'fa fa-question',
			56 => 'fa fa-question',
			57 => 'fa fa-question',
			58 => 'fa fa-question',
			59 => 'fa fa-question',
			60 => 'fa fa-question',
			61 => 'flaticon-measure20',
			62 => 'fa fa-question',
			63 => 'fa fa-question',
			65 => 'fa fa-question',
			66 => 'fa fa-question',
			67 => 'fa fa-question',
			68 => 'fa fa-question',
			69 => 'fa fa-question',
			70 => 'fa fa-question',
			71 => 'fa fa-question',
			72 => 'fa fa-question',
			73 => 'fa fa-question',
			75 => 'fa fa-question',
			76 => 'fa fa-question',
			77 => 'fa fa-question',
			78 => 'fa fa-question',
			79 => 'fa fa-question',
			80 => 'fa fa-question',
			81 => 'fa fa-question'
	);
	?>