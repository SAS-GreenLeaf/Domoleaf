<?php 

class Admin extends User {

	/*** Profile ***/
	function profileList() {
		$link = Link::get_link('domoleaf');
		$list = array();
	
		$sql = 'SELECT mcuser_id, username, mcuser_mail, lastname, firstname,
		               gender, phone, language, design, activity, mcuser_level,
		               bg_color, border_color
		        FROM mcuser';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->mcuser_id] = clone $do;
		}
		
		return $list;
	}
	
	function profileInfo($id=0) {
		$link = Link::get_link('domoleaf');
		
		if(empty($id)) {
			$id = $this->getId();
		}
		
		$sql = 'SELECT mcuser_id, username, mcuser_mail, lastname, firstname,
		               gender, phone, language, timezone, design, activity, mcuser_level,
		               bg_color, border_color
		        FROM mcuser
		        WHERE mcuser_id= :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		return $do;
	}
	
	function profileNew($username, $password) {
		$link = Link::get_link('domoleaf');
	
		$sql = 'SELECT mcuser_id
		        FROM mcuser
		        WHERE username= :username';
		$req = $link->prepare($sql);
		$req->bindValue(':username', $username, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		if(!empty($do->mcuser_id)) {
			return null;
		}
	
		$sql = 'INSERT INTO mcuser
		        (username)
		        VALUES
		        (:username)';
		$req = $link->prepare($sql);
		$req->bindValue(':username', $username, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	
		$id = $link->lastInsertId();
		
		if(empty($id)) {
			return null;
		}
		
		$sql = 'UPDATE mcuser
		        SET mcuser_password= :pass
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':pass', hash('sha256', $id.'_'.$password), PDO::PARAM_STR);
		$req->bindValue(':user_id', $id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
			
		$sql = 'INSERT INTO mcuser_floor
		        (mcuser_id, floor_id)
		        SELECT '.$id.', floor_id
		        FROM floor';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$sql = 'INSERT INTO mcuser_room
		        (mcuser_id, room_id)
		        SELECT '.$id.', room_id
		        FROM room';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$sql = 'INSERT INTO mcuser_device
		        (mcuser_id, room_device_id)
		        SELECT '.$id.', room_device_id
		        FROM room_device';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		return $id;
	}
	
	function profileRemove($user_id) {
		$link = Link::get_link('domoleaf');
	
		$sql = 'DELETE FROM mcuser
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		return $req->rowCount();
	}
	
	function profileRename($lastname, $firstname, $gender, $email, $phone, $language, $timezone, $user_id=0) {
		$link = Link::get_link('domoleaf');
	
		if(empty($user_id)) {
			$user_id = $this->getId();
		}
		
		$sql = 'SELECT mcuser_id
		        FROM mcuser
		        WHERE mcuser_id= :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $user_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		if(!empty($do->mcuser_id)) {
			$user = new User($do->mcuser_id);
			$user-> profileRename($lastname, $firstname, $gender, $email, $phone, $language, $timezone);
		}
	}
	
	function profileLevel($id, $level) {
		$link = Link::get_link('domoleaf');
		
		//only 3 lvl for the moment
		if(($level != 1 && $level != 2 && $level != 3) || $id == $this->getId()) {
			return;
		}
		
		$sql = 'UPDATE mcuser
		        SET mcuser_level=:level
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':level',   $level, PDO::PARAM_INT);
		$req->bindValue(':user_id', $id,    PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function profileUsername($id, $username) {
		$link = Link::get_link('domoleaf');
	
		$sql = 'SELECT mcuser_id
		        FROM mcuser
		        WHERE username= :username';
		$req = $link->prepare($sql);
		$req->bindValue(':username', $username, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		if(!empty($do->mcuser_id)) {
			return null;
		}
	
		$sql = 'UPDATE mcuser
		        SET username=:username
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':username', $username, PDO::PARAM_STR);
		$req->bindValue(':user_id', $id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function passwordRename($last, $new, $id=0) {
		if(empty($id)) {
			parent::passwordRename($last, $new);
		}
		else {
			$link = Link::get_link('domoleaf');
			
			$sql = 'SELECT mcuser_id, mcuser_password
			        FROM mcuser
			        WHERE mcuser_id= :user_id';
			$req = $link->prepare($sql);
			$req->bindValue(':user_id', $id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do = $req->fetch(PDO::FETCH_OBJ);
	
			if(!empty($do->mcuser_id)) {
				$sql = 'UPDATE mcuser
				        SET mcuser_password=:user_password
				        WHERE mcuser_id=:user_id';
				$req = $link->prepare($sql);
				$req->bindValue(':user_password', hash('sha256', $do->mcuser_id.'_'.$new), PDO::PARAM_STR);
				$req->bindValue(':user_id', $do->mcuser_id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
		}
	}
	
	function confRemote($http, $https, $securemode){
		$link = Link::get_link('domoleaf');
		
		$conf = $this->conf_load();
		
		if ($http != $conf[1]->configuration_value or $https != $conf[2]->configuration_value){
			$data = array();
		
			if ($conf[1]->configuration_value != 0){
				$data[] = Array(
									'action' => 'close',
									'configuration_id' => '1',
									'protocol' => 'TCP'
								  );
			}
			if ($conf[2]->configuration_value != 0){
				$data[] = Array(
						'action' => 'close',
						'configuration_id' => '2',
						'protocol' => 'TCP'
				);
			}
			if (sizeof($data) > 0){
				$socket1 = new Socket();
				$socket1->send('cron_upnp', $data);
			}
			
			$sql = 'UPDATE configuration
		       	 	SET configuration_value= :value
					WHERE configuration_id = 1';
			$req = $link->prepare($sql);
			$req->bindValue(':value', $http, PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			
			$sql = 'UPDATE configuration
		        SET configuration_value= :value
				WHERE configuration_id = 2';
			$req = $link->prepare($sql);
			$req->bindValue(':value', $https, PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			
			$data = array();
			if ($http != 0){
				$data[] = Array(
						'action' => 'open',
						'configuration_id' => '1',
						'protocol' => 'TCP'
				);
			}
			if ($https != 0){
				$data[] = Array(
						'action' => 'open',
						'configuration_id' => '2',
						'protocol' => 'TCP'
				);
			}
			if (sizeof($data) > 0){
				$socket2 = new Socket();
				$socket2->send('cron_upnp', $data);
			}
		}
		
		$sql = 'UPDATE configuration
			        SET configuration_value= :value
					WHERE configuration_id = 3';
		$req = $link->prepare($sql);
		$req->bindValue(':value', $securemode, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
	}

	function confMail($fromMail, $fromName, $smtpHost, $smtpSecure, $smtpPort, $smtpUsername, $smtpPassword){
		$link = Link::get_link('domoleaf');

		if (empty($fromMail) or filter_var($fromMail, FILTER_VALIDATE_EMAIL) == false){
			$fromMail = '';
		}
		if (!empty($fromMail) && $fromMail != '' && filter_var($fromMail, FILTER_VALIDATE_EMAIL) == true){
			$sql = 'UPDATE configuration
					SET configuration_value = :fromMail
					WHERE configuration_id = 5';
			$req = $link->prepare($sql);
			$req->bindValue(':fromMail', $fromMail, PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		if (!empty($fromName) && $fromName != ''){
			$sql = 'UPDATE configuration
					SET configuration_value = :fromName
					WHERE configuration_id = 6';
			$req = $link->prepare($sql);
			$req->bindValue(':fromName', $fromName, PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		if (!empty($smtpHost) && $smtpHost != ''){
			$sql = 'UPDATE configuration
					SET configuration_value = :smtpHost
					WHERE configuration_id = 7';
			$req = $link->prepare($sql);
			$req->bindValue(':smtpHost', $smtpHost, PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}

		if (empty($smtpSecure)){
			(int)$smtpSecure = 0;
		}
		if ($smtpSecure >= 0 && $smtpSecure < 3){
			$sql = 'UPDATE configuration
					SET configuration_value = :smtpSecure
					WHERE configuration_id = 8';
			$req = $link->prepare($sql);
			$req->bindValue(':smtpSecure', (int)$smtpSecure, PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		if (!empty($smtpPort) && $smtpPort > 0 and $smtpPort <= 65535){
			$sql = 'UPDATE configuration
					SET configuration_value = :smtpPort
					WHERE configuration_id = 9';
			$req = $link->prepare($sql);
			$req->bindValue(':smtpPort', (int)$smtpPort, PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		if (empty($smtpUsername)){
			$smtpUsername = '';
			$smtpPassword = '';
		}
			$sql = 'UPDATE configuration
					SET configuration_value = :smtpUsername
					WHERE configuration_id = 10';
			$req = $link->prepare($sql);
			$req->bindValue(':smtpUsername', $smtpUsername, PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		
		if (!empty($smtpPassword) || empty($smtpUsername)){
			$sql = 'UPDATE configuration
					SET configuration_value = :smtpPassword
					WHERE configuration_id = 11';
			$req = $link->prepare($sql);
			$req->bindValue(':smtpPassword', $smtpPassword, PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		$socket = new Socket();
		$socket->send('reload_d3config');
	}

	function confPreConfigurationMail($fromMail){
		if (empty($fromMail) or filter_var($fromMail, FILTER_VALIDATE_EMAIL) == false){
			return '2';
		}
		$parse = explode("@", $fromMail)[1];
		$fromName = explode("@", $fromMail)[0];
		$smtpHost = "";
		if ($parse == "greenleaf.fr"){
			$smtpHost = "smtp.free.fr";
			$smtpSecure = 0;
			$smtpPort = 25;
		}
		else if ($parse == "sfr.fr"){
			$smtpHost = "smtp.sfr.fr";
			$smtpSecure = 1;
			$smtpPort = 465;
		}
		else if ($parse == "bbox.fr"){
			$smtpHost = "smtp.bbox.fr";
			$smtpSecure = 1;
			$smtpPort = 587;
		}
		else if ($parse == "free.fr"){
			$smtpHost = "smtp.free.fr";
			$smtpSecure = 0;
			$smtpPort = 25;
		}
		else if ($parse == "orange.fr"){
			$smtpHost = "smtp.orange.fr";
			$smtpSecure = 1;
			$smtpPort = 465;
		}
		else if ($parse == "gmail.com"){
			$smtpHost = "smtp.gmail.com";
			$smtpSecure = 2;
			$smtpPort = 587;
		}
		if ($smtpHost == ""){
			return '1';
		}
		else{
			$this->confMail($fromMail, $fromName, $smtpHost, $smtpSecure, $smtpPort, '', '');
			return '|'.$fromMail.'|'.$fromName.'|'.$smtpHost.'|'.$smtpSecure.'|'.$smtpPort;
		}
	}

	function confSendTestMail(){
		$destinatorMail = $this->profileInfo()->user_mail;
		if (empty($destinatorMail)){
			$destinatorMail = $this->conf_load()[5]->configuration_value;
		}
		$objectMail = _('The test mail worked');
		$messageMail = _('Your mail is configured from your D3 machine.');
		return $this->confSendMail($destinatorMail, $objectMail, $messageMail);
	}

	function confSendMail($destinatorMail, $objectMail, $messageMail){
		if (empty($destinatorMail) || sizeof($destinatorMail) < 1 || filter_var($destinatorMail, FILTER_VALIDATE_EMAIL) == false){
			return NULL;
		}
		if (empty($objectMail) || sizeof($objectMail) < 1){
			$objectMail = '';
		}
		if (empty($messageMail) || sizeof($messageMail) < 1){
			$messageMail = '';
		}
		$data = array(
				'destinator' => $destinatorMail,
				'object' => $objectMail,
				'message' => $messageMail
		);
		$socket = new Socket();
		$socket->send('send_mail', $data);
		return $socket->receive();
		
	}

	function confPriceElec($highCost=0, $lowCost=0, $lowField1=0, $lowField2=0, $currency=0){
		$link = Link::get_link('domoleaf');

		if (!is_numeric($highCost)){
			$highCost = '0';
		}
		if (!is_numeric($lowCost)){
			$lowCost = '0';
		}
		
		if (!empty($lowField1) && !empty(explode('-', $lowField1)[0])) {
			$lowField1_1 = explode('-', $lowField1)[0];
		}
		else {
			$lowField1_1 = 0;
		}
		if (!empty($lowField1) && !empty(explode('-', $lowField1)[0])) {
			$lowField1_2 = explode('-', $lowField1)[0];
		}
		else {
			$lowField1_2 = 0;
		}
		
		if (!empty($lowField2) && !empty(explode('-', $lowField2)[0])) {
			$lowField2_1 = explode('-', $lowField2)[0];
		}
		else {
			$lowField2_1 = 0;
		}
		if (!empty($lowField2) && !empty(explode('-', $lowField2)[0])) {
			$lowField2_2 = explode('-', $lowField2)[0];
		}
		else {
			$lowField2_2 = 0;
		}
		
		if (!($lowField1_1 >= 0 && $lowField1_1 < 24) || !($lowField1_2 >= 0 && $lowField1_2 < 24)){
			$lowField1 = '0-0';
		}
		if (!($lowField2_1 >= 0 && $lowField2_1 < 24) || !($lowField2_2 >= 0 && $lowField2_2 < 24)){
			$lowField2 = '0-0';
		}
		
		
		if (!is_numeric($currency)){
			$currency = '1';
		}
		
		$sql = 'UPDATE configuration
				SET configuration_value = :highCost
				WHERE configuration_id = 14';
		$req = $link->prepare($sql);
		$req->bindValue(':highCost', $highCost, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$sql = 'UPDATE configuration
				SET configuration_value = :lowCost
				WHERE configuration_id = 15';
		$req = $link->prepare($sql);
		$req->bindValue(':lowCost', $lowCost, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$sql = 'UPDATE configuration
				SET configuration_value = :lowField1
				WHERE configuration_id = 16';
		$req = $link->prepare($sql);
		$req->bindValue(':lowField1', $lowField1, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$sql = 'UPDATE configuration
				SET configuration_value = :lowField2
				WHERE configuration_id = 17';
		$req = $link->prepare($sql);
		$req->bindValue(':lowField2', $lowField2, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		if (checkCurrency($currency) == NULL){
			$currency = 1;
		}
		
		
		$sql = 'UPDATE configuration
				SET configuration_value = :currency
				WHERE configuration_id = 18';
		$req = $link->prepare($sql);
		$req->bindValue(':currency', $currency, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$socket = new Socket();
		$socket->send('reload_d3config');
	}
	
	/*** Floors ***/
	function confFloorList() {
		$link = Link::get_link('domoleaf');
		$list = array();
	
		$sql = 'SELECT floor_id, floor_name
		        FROM floor
		        ORDER BY floor_name ASC';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->floor_id] = clone $do;
		}
		
		return $list;
	}
	
	function confFloorNew($namefloor, $nameroom = 0) {
		$link = Link::get_link('domoleaf');
		
		$tmpNameFloor = $namefloor;
		if (empty($namefloor)) {
			$tmpNameFloor = 'floor';
		}
		
		$sql = 'INSERT INTO floor
		        (floor_name)
		        VALUES
		        (:name)';
		$req = $link->prepare($sql);
		$req->bindValue(':name', $tmpNameFloor, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$newfloorid = $link->lastInsertId();
		
		if(!empty($newfloorid)){
			$sql = 'INSERT INTO mcuser_floor
			        (mcuser_id, floor_id)
			        SELECT mcuser_id, '.$newfloorid.'
			        FROM mcuser';
			$req = $link->prepare($sql);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			
			if (empty($namefloor)){
				$LOCALE = langToLocale($this->getLanguage());
				putenv("LC_ALL=".$LOCALE);
				setlocale(LC_ALL, $LOCALE);
				bind_textdomain_codeset("messages", "UTF-8");
				bindtextdomain("messages", "/etc/domoleaf/www/locales");
				textdomain("messages");
				$name = _('Floor').' '.$newfloorid;
				$this->confFloorRename($newfloorid, $name);
			}
			$this->confRoomNew($nameroom, $newfloorid);
		}

		$sql = 'SELECT mcuser_id
			    FROM mcuser
				WHERE mcuser_level>=2';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$this->confUserVisibleFloor($do->mcuser_id, $newfloorid, 1);
		}

		return $newfloorid;
	}
	
	function confFloorRename($id, $name) {
		$link = Link::get_link('domoleaf');
		
		if(!empty($name)) {
			$sql = 'UPDATE floor
			        SET floor_name=:name
			        WHERE floor_id=:floor_id';
			$req = $link->prepare($sql);
			$req->bindValue(':name', $name, PDO::PARAM_STR);
			$req->bindValue(':floor_id', $id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
	}
	
	function confFloorRemove($idfloor) {
		
		$link = Link::get_link('domoleaf');
		
		$sql = 'UPDATE mcuser_floor
		        JOIN floor ON mcuser_floor.floor_id = floor.floor_id
		        JOIN mcuser_floor as uf ON uf.floor_id =:floor_id AND mcuser_floor.mcuser_id= uf.mcuser_id
		        SET mcuser_floor.floor_order = mcuser_floor.floor_order - 1  
		        WHERE mcuser_floor.floor_order > uf.floor_order';
		$req = $link->prepare($sql);
		$req->bindValue(':floor_id', $idfloor, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$sql = 'DELETE FROM floor
		        WHERE floor_id=:floor_id';
		$req = $link->prepare($sql);
		$req->bindValue(':floor_id', $idfloor, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$this->udpateScenariosList();
	}
	
	/*** Rooms ***/
	function confRoomAll(){
		$list = array();
		$link = Link::get_link('domoleaf');
		
		$sql = 'SELECT room_id, room_name, floor as id_floor
				FROM room
		        ORDER BY room_name ASC';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->room_id] = clone $do;
		}
		
		return $list;
	}
	
	function confRoomList($floor=0) {
		$list = array();
		$link = Link::get_link('domoleaf');
		
		if(!empty($floor)){
			$sql = 'SELECT room.room_id, room_name, floor, floor_name
			        FROM room
			        JOIN floor ON floor.floor_id=floor
			        WHERE floor_id=:floor_id
			        ORDER BY floor, room_name';
			$req = $link->prepare($sql);
			$req->bindValue(':floor_id', $floor, PDO::PARAM_INT);
		}
		else {
			$sql = 'SELECT room.room_id, room_name, floor, floor_name
			        FROM room
			        JOIN floor ON floor.floor_id=floor
			        ORDER BY floor, room_name';
			$req = $link->prepare($sql);
		}
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->room_id] = clone $do;
		}
	
		return $list;
	}
	
	function confRoomNew($name, $floor) {
		$link = Link::get_link('domoleaf');
		$floorList = $this->confFloorList();
		if(empty($floorList[$floor])) {
			return null;
		}
		$tmpName = $name;
		if(empty($name)) {
			$tmpName = 'room';
		}
	
		$sql = 'INSERT INTO room
		        (room_name, floor)
		        VALUES
		        (:name, :floor)';
		$req = $link->prepare($sql);
		$req->bindValue(':name',  $tmpName,  PDO::PARAM_STR);
		$req->bindValue(':floor', $floor, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$newroomid = $link->lastInsertId();
		
		if(!empty($newroomid)){
			$sql = 'INSERT INTO mcuser_room
			        (mcuser_id, room_id)
			        SELECT mcuser_id, '.$newroomid.'
			        FROM mcuser';
			$req = $link->prepare($sql);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			if (empty($name)){
				$LOCALE = langToLocale($this->getLanguage());
				putenv("LC_ALL=".$LOCALE);
				setlocale(LC_ALL, $LOCALE);
				bind_textdomain_codeset("messages", "UTF-8");
				bindtextdomain("messages", "/etc/domoleaf/www/locales");
				textdomain("messages");
				$name = _('Room').' '.$newroomid;
				$this->confRoomRename($newroomid, $name);
			}
		}
		
		$sql = 'SELECT mcuser_id
			    FROM mcuser
				WHERE mcuser_level>=2';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$this->confUserVisibleRoom($do->mcuser_id, $newroomid, 1);
		}
	}
	
	function confRoomRename($id, $name) {
		$link = Link::get_link('domoleaf');
	
		$sql = 'SELECT room_id, room_name
		        FROM room
		        WHERE room_id=:room_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_id', $id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		if(!empty($do->room_id)) {
			$sql = 'UPDATE room
			        SET room_name=:name
			        WHERE room_id=:room_id';
			$req = $link->prepare($sql);
			$req->bindValue(':room_id', $do->room_id, PDO::PARAM_INT);
			$req->bindValue(':name',    $name,        PDO::PARAM_STR);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
	}
	
	function confRoomFloor($id, $floor) {
		$link = Link::get_link('domoleaf');
		$floorList = $this->confFloorList();
		
		$sql = 'SELECT room_id, room_name
		        FROM room
		        WHERE room_id=:room_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_id', $id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		if(!empty($do->room_id) && !empty($floorList[$floor])) {
			$sql = 'UPDATE room
			        SET floor=:floor
			        WHERE room_id=:room_id';
			$req = $link->prepare($sql);
			$req->bindValue(':room_id', $do->room_id, PDO::PARAM_INT);
			$req->bindValue(':floor',   $floor,       PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
	}
	
	function confRoomRemove($idroom, $idfloor) {
		$link = Link::get_link('domoleaf');
	
		$sql = 'UPDATE mcuser_room
		        JOIN room ON mcuser_room.room_id = room.room_id
		        JOIN mcuser_room as ur ON ur.room_id =:room_id AND mcuser_room.mcuser_id= ur.mcuser_id
		        SET mcuser_room.room_order = mcuser_room.room_order - 1
		        WHERE room.floor=:floor_id AND mcuser_room.room_order > ur.room_order';
		
		$req = $link->prepare($sql);
		$req->bindValue(':floor_id', $idfloor, PDO::PARAM_INT);
		$req->bindValue(':room_id', $idroom, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$sql = 'DELETE FROM room
		        WHERE room_id=:room_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_id', $idroom, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$this->udpateScenariosList();
	}
	
	/*** Devices ***/
	
	function confRoomDeviceAll($iddevice){
		$list = array();
		$link = Link::get_link('domoleaf');
		
		$sql = 'SELECT room_device_id, name
		        FROM room_device
		        WHERE room_device_id=:room_device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->room_device_id] = array(
				'room_device_id' => $do->room_device_id,
				'name'           => $do->name
			);
		}
		return $list;
	}
	
	function confRoomDeviceRemove($iddevice, $idroom){
		$listSmartcmd = array();
		
		$link = Link::get_link('domoleaf');
		
		$sql = 'UPDATE mcuser_device
		        JOIN room_device ON mcuser_device.room_device_id = room_device.room_device_id
		        JOIN mcuser_device as ud ON ud.room_device_id =:room_device_id AND 
		                                  mcuser_device.mcuser_id= ud.mcuser_id
		        SET mcuser_device.device_order = mcuser_device.device_order - 1
		        WHERE room_device.room_id=:room_id AND 
		              mcuser_device.device_order > ud.device_order';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':room_id', $idroom, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		
		$sql = 'SELECT smartcommand_id, exec_id
				FROM smartcommand_elems
				WHERE room_device_id=:room_device_id
				ORDER BY smartcommand_id, exec_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$sql2 = 'UPDATE smartcommand_elems
		         SET exec_id=exec_id-1
		         WHERE smartcommand_id=:smartcmd_id AND exec_id > :exec_id';
		$req2 = $link->prepare($sql2);
		$req2->bindParam(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req2->bindParam(':exec_id', $exec_id, PDO::PARAM_INT);
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$smartcmd_id = $do->smartcommand_id;
			$exec_id = $do->exec_id;
			
			$listSmartcmd[] = $smartcmd_id;
			
			$req2->execute() or die (error_log(serialize($req2->errorInfo())));
		}
		
		
		$sql = 'DELETE FROM room_device
		        WHERE room_device_id=:room_device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		if(sizeof($listSmartcmd) > 0) {
			$listSmartcmd = array_unique($listSmartcmd);
			$listSmartcmd = implode(', ', $listSmartcmd);
			$sql = 'DELETE smartcommand_list
					FROM smartcommand_list
					LEFT JOIN smartcommand_elems ON smartcommand_list.smartcommand_id = smartcommand_elems.smartcommand_id
					WHERE smartcommand_elems.smartcommand_id IS NULL AND smartcommand_list.smartcommand_id IN ('.$listSmartcmd.')';
			$req = $link->prepare($sql);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		$this->udpateScenariosList();
	}
	
	/**
	 * Save all device information
	 * @param int: room id
	 * @param string : device name
	 * @param int : daemon id
	 * @param string : device address
	 * @param int : device id
	 * @param int : device port
	 * @param string : login
	 * @param string : password
	 * @return NULL
	 */
	function confDeviceSaveInfo($idroom, $name, $daemon=0, $devaddr, $iddevice, $port='', $login='', $pass='', $macaddr='', $password=''){
		$link = Link::get_link('domoleaf');
		
		if(empty($idroom) or empty($name) or empty($devaddr) or empty($iddevice)) {
			return null;
		}
		
		if(empty($daemon) or $daemon == 'undefined'){
			$daemon = null;
		}
		
		$sql = 'SELECT room_id
		        FROM room_device
		        WHERE room_device.room_device_id=:room_device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if($do->room_id != $idroom){
			$sql = 'UPDATE mcuser_device
			        JOIN room_device ON mcuser_device.room_device_id = room_device.room_device_id
			        JOIN mcuser_device as ud ON ud.room_device_id =:room_device_id AND 
			                                  mcuser_device.mcuser_id= ud.mcuser_id
			        SET mcuser_device.device_order = mcuser_device.device_order - 1
			        WHERE room_device.room_id=:room_id AND 
			              mcuser_device.device_order > ud.device_order AND 
			              mcuser_device.device_order > 1';
			$req = $link->prepare($sql);
			$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
			$req->bindValue(':room_id', $do->room_id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			
			$sql = 'UPDATE mcuser_device
			        SET device_order = 0, device_allowed = 0
			        WHERE room_device_id=:room_device_id';
			$req = $link->prepare($sql);
			$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}

		if(empty($pass)){
			$sql = 'UPDATE room_device
			        SET name=:name, daemon_id=:daemon_id, addr=:addr, 
			            room_id=:room_id, plus1=:plus1, plus2=:plus2, 
			            plus4=:plus4, password=:password
			        WHERE room_device_id=:room_device_id';
			$req = $link->prepare($sql);
			$req->bindValue(':name',  ucfirst($name),  PDO::PARAM_STR);
			$req->bindValue(':daemon_id', $daemon, PDO::PARAM_INT);
			$req->bindValue(':addr', $devaddr, PDO::PARAM_STR);
			$req->bindValue(':room_id', $idroom, PDO::PARAM_INT);
			$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
			$req->bindValue(':plus1', $port, PDO::PARAM_STR);
			$req->bindValue(':plus2', $login, PDO::PARAM_STR);
			$req->bindValue(':plus4', $macaddr, PDO::PARAM_STR);
			$req->bindValue(':password', $password, PDO::PARAM_STR);
		}
		else {
			$sql = 'UPDATE room_device
			        SET name=:name, daemon_id=:daemon_id, addr=:addr, 
			            room_id=:room_id, plus1=:plus1, plus2=:plus2, 
			            plus3=:plus3, plus4=:plus4, password=:password
			        WHERE room_device_id=:room_device_id';
			$req = $link->prepare($sql);
			$req->bindValue(':name',  ucfirst($name),  PDO::PARAM_STR);
			$req->bindValue(':daemon_id', $daemon, PDO::PARAM_INT);
			$req->bindValue(':addr', $devaddr, PDO::PARAM_STR);
			$req->bindValue(':room_id', $idroom, PDO::PARAM_INT);
			$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
			$req->bindValue(':plus1', $port, PDO::PARAM_STR);
			$req->bindValue(':plus2', $login, PDO::PARAM_STR);
			$req->bindValue(':plus3', $pass, PDO::PARAM_STR);
			$req->bindValue(':plus4', $macaddr, PDO::PARAM_STR);
			$req->bindValue(':password', $password, PDO::PARAM_STR);
		}
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		if(empty($daemon)){
			$socket = new Socket();
			$socket->send('reload_camera');
		}
	}
	
	/**
	 * Save device options
	 * @param int : device id
	 * @param array : option information
	 * @return NULL
	 */
	function confDeviceSaveOption($room_device_id, $options){
		if (empty($room_device_id) or empty($options)){
			return null;
		}
		
		$listdpt = $this->confOptionDptList($room_device_id);
		$tmp = 0;
		if (isset($listdpt[$options['id']])) {
			foreach ($listdpt[$options['id']] as $list){
				if ($tmp == 0){
					$tmp = $list->dpt_id; 
				}
				if ($list->dpt_id == $options['dpt_id']){
					$tmp = $list->dpt_id; 
					break;
				}
			}
		}
		
		$options['dpt_id'] = $tmp;
		
		$link = Link::get_link('domoleaf');
		
		$sql = 'SELECT room_device_id
		        FROM room_device_option
		        WHERE room_device_id=:room_device_id AND option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $room_device_id, PDO::PARAM_INT);
		$req->bindValue(':option_id',  $options['id'],  PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if(empty($options['status']) or $options['status'] == 'false'){
			$status = 0;
		}
		else {
			$status = 1;
		}
		
		if (!empty($do->room_device_id)) {
			$sql = 'UPDATE room_device_option
			        SET option_id=:option_id, addr=:addr, addr_plus=:addr_plus, dpt_id=:dpt_id, 
			            status=:status
			        WHERE room_device_id=:room_device_id AND 
			              option_id=:option_id';
			$req = $link->prepare($sql);
			$req->bindValue(':room_device_id', $room_device_id, PDO::PARAM_INT);
			$req->bindValue(':option_id', $options['id'], PDO::PARAM_INT);
			$req->bindValue(':addr', $options['addr'], PDO::PARAM_STR);
			$req->bindValue(':addr_plus', $options['addr_plus'], PDO::PARAM_STR);
			$req->bindValue(':dpt_id', $options['dpt_id'], PDO::PARAM_INT);
			$req->bindValue(':status', $status, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		else {
			$sql = 'INSERT INTO room_device_option
			        (room_device_id, option_id, addr, addr_plus, dpt_id, status)
			        VALUES
			        (:room_device_id, :option_id, :addr, :addr_plus, :dpt_id, :status)';
			$req = $link->prepare($sql);
			$req->bindValue(':room_device_id', $room_device_id, PDO::PARAM_INT);
			$req->bindValue(':option_id', $options['id'], PDO::PARAM_INT);
			$req->bindValue(':addr', $options['addr'], PDO::PARAM_STR);
			$req->bindValue(':addr_plus', $options['addr_plus'], PDO::PARAM_STR);
			$req->bindValue(':dpt_id', $options['dpt_id'], PDO::PARAM_INT);
			$req->bindValue(':status', $status, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		$this->udpateScenariosList();
	}
	
	function confRoomDeviceList($room){
		$link = Link::get_link('domoleaf');
		$list = array();
		
		if(!empty($room)){
			$sql = 'SELECT room_device_id, room_device.name, 
			               room_device.protocol_id, room_id, 
			               if(device.name'.$this->getLanguage().' = "", device.name, device.name'.$this->getLanguage().') as device_name, 
			               room_device.device_id, daemon_id, addr, 
			               device.application_id, password
			        FROM room_device
			        JOIN device ON room_device.device_id = device.device_id
			        WHERE room_id=:room_id
			        ORDER BY name ASC';
			$req = $link->prepare($sql);
			$req->bindValue(':room_id', $room, PDO::PARAM_INT);
		}
		else {
			$sql = 'SELECT room_device_id, room_device.name, 
			               room_device.protocol_id, room_id, 
			               if(device.name'.$this->getLanguage().' = "", device.name, device.name'.$this->getLanguage().') as device_name, 
			               room_device.device_id, daemon_id, addr, plus1, plus2,
			               plus3, plus4, device.application_id, password
			        FROM room_device
			        JOIN device ON room_device.device_id = device.device_id
			        ORDER BY name ASC';
			$req = $link->prepare($sql);
		}
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->room_device_id] = clone $do;
		}
		
		return $list;
	}
	
	function confDeviceProtocol($device=0) {
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT protocol_id
		        FROM device_protocol
		        WHERE device_id=:device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':device_id', $device, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[] = $do->protocol_id;
		}
		
		return $list;
	}
	
	function confDeviceNewIp($name, $proto, $room, $device, $addr, $port='80', $login='', $pass='', $macaddr=''){
		$link = Link::get_link('domoleaf');
		
		if(empty($name) or empty($proto) or 
		   empty($room) or empty($device) or empty($addr)) {
			return 0;
		}
		
		if(empty($port) || !is_numeric($port)){
			$port = '80';
		}
		
		if($port < 0 || $port > 65535){
			$port = '80';
		}

		$addr = gethostbyname($addr);

		if (!(filter_var($addr, FILTER_VALIDATE_IP))){
			return;
		}

		$sql = 'INSERT INTO room_device
		        (name, protocol_id, room_id, device_id, addr, plus1, plus2, plus3, plus4)
		        VALUES
		        (:name, :proto, :room, :device, :addr, :port, :login, :pass, :macaddr)';
		$req = $link->prepare($sql);
		$req->bindValue(':name',  ucfirst($name),  PDO::PARAM_STR);
		$req->bindValue(':proto', $proto, PDO::PARAM_INT);
		$req->bindValue(':room', $room, PDO::PARAM_INT);
		$req->bindValue(':device', $device, PDO::PARAM_INT);
		$req->bindValue(':addr', $addr, PDO::PARAM_STR);
		$req->bindValue(':port', $port, PDO::PARAM_STR);
		$req->bindValue(':login', $login, PDO::PARAM_STR);
		$req->bindValue(':pass', $pass, PDO::PARAM_STR);
		$req->bindValue(':macaddr', $macaddr, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	
		$newdeviceid = $link->lastInsertId();
		
		if(!empty($newdeviceid)){
			$sql = 'INSERT INTO mcuser_device
			        (mcuser_id, room_device_id)
			        SELECT mcuser_id, '.$newdeviceid.'
			        FROM mcuser';
			$req = $link->prepare($sql);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}

		$socket = new Socket();
		$socket->send('reload_camera');

		$sql = 'SELECT mcuser_id
			    FROM mcuser
				WHERE mcuser_level>=2';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$this->confUserVisibleDevice($do->mcuser_id,  $newdeviceid, 1);
		}

		return $newdeviceid;
	}
	
	function confDeviceNewKnx($name, $proto, $room, $device, $addr, $daemon){
		$link = Link::get_link('domoleaf');
		
		if(empty($name) or empty($proto) or empty($room) or 
		   empty($device) or empty($addr) or empty($daemon)) {
			return 0;
		}
		
		$sql = 'INSERT INTO room_device
		        (name, protocol_id, room_id, device_id, addr, daemon_id)
		        VALUES
		        (:name, :proto, :room, :device, :addr, :dae)';
		$req = $link->prepare($sql);
		$req->bindValue(':name',  ucfirst($name),  PDO::PARAM_STR);
		$req->bindValue(':proto', $proto, PDO::PARAM_INT);
		$req->bindValue(':room', $room, PDO::PARAM_INT);
		$req->bindValue(':device', $device, PDO::PARAM_INT);
		$req->bindValue(':addr', $addr, PDO::PARAM_STR);
		$req->bindValue(':dae', $daemon, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$newdeviceid = $link->lastInsertId();
		
		if(!empty($newdeviceid)){
			$sql = 'INSERT INTO mcuser_device
			        (mcuser_id, room_device_id)
			        SELECT mcuser_id, '.$newdeviceid.'
			        FROM mcuser';
			$req = $link->prepare($sql);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		$sql = 'SELECT mcuser_id
			    FROM mcuser
				WHERE mcuser_level>=2';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$this->confUserVisibleDevice($do->mcuser_id,  $newdeviceid, 1);
		}

		return $newdeviceid;
	}
	
	function confDeviceNewEnocean($name, $proto, $room, $device, $addr){
		$link = Link::get_link('domoleaf');
		
		if(empty($name) or empty($proto) or 
		   empty($room) or empty($device) or empty($addr)) {
			return 0;
		}
		
		$sql = 'INSERT INTO room_device
		        (name, protocol_id, room_id, device_id, addr)
		        VALUES
		        (:name, :proto, :room, :device, :addr)';
		$req = $link->prepare($sql);
		$req->bindValue(':name',  ucfirst($name),  PDO::PARAM_STR);
		$req->bindValue(':proto', $proto, PDO::PARAM_INT);
		$req->bindValue(':room', $room, PDO::PARAM_INT);
		$req->bindValue(':device', $device, PDO::PARAM_INT);
		$req->bindValue(':addr', $addr, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$newdeviceid = $link->lastInsertId();
		
		if(!empty($newdeviceid)){
			$sql = 'INSERT INTO mcuser_device
			        (mcuser_id, room_device_id)
			        SELECT mcuser_id, '.$newdeviceid.'
			        FROM mcuser';
			$req = $link->prepare($sql);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}

		$sql = 'SELECT mcuser_id
			    FROM mcuser
				WHERE mcuser_level>=2';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$this->confUserVisibleDevice($do->mcuser_id,  $newdeviceid, 1);
		}

		return $newdeviceid;
	}

	function confManufacturerList($room_device_id){
		$link = Link::get_link('domoleaf');
		$list = array();

		$sql = 'SELECT device_id, protocol_id
				FROM room_device
				WHERE room_device_id=:room_device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $room_device_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);

		$device_id = $do->device_id;
		$protocol_id = $do->protocol_id;

		$sql = 'SELECT DISTINCT manufacturer.manufacturer_id, manufacturer.name
				FROM manufacturer
				JOIN product ON manufacturer.manufacturer_id=product.manufacturer_id
				WHERE product.device_id=:device_id AND product.protocol_id=:protocol_id 
				ORDER BY manufacturer.name ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':device_id', $device_id, PDO::PARAM_INT);
		$req->bindValue(':protocol_id', $protocol_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->manufacturer_id] = array(
				'manufacturer_id' => $do->manufacturer_id,
				'name'            => $do->name
			);
		}
		return $list;
	}

	function confProductList($room_device_id, $manufacturer_id){
		$link = Link::get_link('domoleaf');
		$list = array();

		$sql = 'SELECT device_id, protocol_id
				FROM room_device
				WHERE room_device_id=:room_device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $room_device_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		$device_id = $do->device_id;
		$protocol_id = $do->protocol_id;

		$sql = 'SELECT product_id, name
				FROM product
				WHERE device_id=:device_id AND protocol_id=:protocol_id AND manufacturer_id=:manufacturer_id
				ORDER BY name ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':device_id', $device_id, PDO::PARAM_INT);
		$req->bindValue(':protocol_id', $protocol_id, PDO::PARAM_INT);
		$req->bindValue(':manufacturer_id', $manufacturer_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->product_id] = array(
					'product_id' => $do->product_id,
					'name'       => $do->name
			);
		}
		return $list;
	}

	function confProductOptionList($product_id){
		$link = Link::get_link('domoleaf');
		$list = array();
	
		$sql = 'SELECT option_id, addr, dpt_id 
				FROM product_option 
				WHERE product_id=:product_id';
		$req = $link->prepare($sql);
		$req->bindValue(':product_id', $product_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[] = array(
					'option_id' => $do->option_id,
					'addr'      => $do->addr,
					'dpt_id'    => $do->dpt_id
			);
		}
		return $list;
	}

	/*** Daemon management ***/
	function confDaemonList() {
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT daemon_id, name, serial, validation, version
		        FROM daemon
		        ORDER BY name';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->daemon_id] = array(
				'daemon_id' => $do->daemon_id,
				'name'      => $do->name,
				'serial'    => $do->serial,
				'validation'=> $do->validation,
				'version'   => $do->version,
				'protocol'  => array()
			);
		}
		
		$sql = 'SELECT daemon_id, protocol_id, interface, interface_arg
		        FROM daemon_protocol';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->daemon_id]['protocol'][$do->protocol_id] = clone $do;
		}
		
		return $list;
	}
	
	function confDaemonNew($name, $serial, $skey) {
		$link = Link::get_link('domoleaf');	
		if(empty($name) or empty($serial) or empty($skey)) {
			return 0;
		}
		
		$sql = 'INSERT INTO daemon
		        (name, serial, secretkey)
		        VALUES
		        (:name, :serial, :skey)';
		$req = $link->prepare($sql);
		$req->bindValue(':name', mb_strtoupper($name),  PDO::PARAM_STR);
		$req->bindValue(':serial', mb_strtoupper($serial), PDO::PARAM_STR);
		$req->bindValue(':skey', $skey, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		return $link->lastInsertId();
	}
	
	function confSaveWifi($daemon_id, $ssid, $password, $security = 3, $mode = 0) {
		if (!($mode == 0 || $mode == 1 || $mode == 2)){
			return;
		}
		
		if (!($security > 0 && $security <= 3)){
			return;
		}
		
		if (!(preg_match('/^[[:alnum:]\-_]{4,32}$/', $ssid))){
			return;
		}

		$socket = new Socket();
		$data = array(
				'daemon_id' => $daemon_id,
				'ssid'      => $ssid,
				'password'  => $password,
				'security'  => $security,
				'mode'      => $mode
		);
		$socket->send('wifi_update', $data, 1);

		$res = $socket->receive();

		if (empty($res)){
			error_log('No answer from slave for confSaveWifi');
			return;
		}

		$link = Link::get_link('domoleaf');

		$sql = 'UPDATE daemon
			    SET wifi_ssid=:ssid, wifi_password=:password, wifi_security=:security, wifi_mode=:mode
			    WHERE daemon_id=:daemon_id';
		$req = $link->prepare($sql);
		$req->bindValue(':ssid', $ssid, PDO::PARAM_STR);
		$req->bindValue(':password', $password, PDO::PARAM_STR);
		$req->bindValue(':security', $security, PDO::PARAM_INT);
		$req->bindValue(':mode', $mode, PDO::PARAM_INT);
		$req->bindValue(':daemon_id', $daemon_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		return;
	}
	
	function confWifi($daemon_id) {
		$link = Link::get_link('domoleaf');
		
		$sql = 'SELECT wifi_ssid, wifi_password, wifi_security, wifi_mode
		        FROM daemon
				WHERE daemon_id=:daemon_id';
		$req = $link->prepare($sql);
		$req->bindValue(':daemon_id', $daemon_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		$list = array(
				'ssid'     => $do->wifi_ssid,
				'password' => $do->wifi_password,
				'security' => $do->wifi_security,
				'mode'     => $do->wifi_mode
		);
		return $list;
	}	
	
	function confD3Reboot($iddaemon, $opt=1) {
		$socket = new Socket();
		$data = array(
				'daemon_id' => $iddaemon
		);
		if ($opt == 1){
			$socket->send('reboot_d3', $data, 1);
		}
		else{
			$socket->send('shutdown_d3', $data, 1);
		}
		return $socket->receive();
	}
	
	function confDaemonRemove($id) {
		$link = Link::get_link('domoleaf');
	
		$sql = 'DELETE FROM daemon
		        WHERE daemon_id=:daemon_id';
		$req = $link->prepare($sql);
		$req->bindValue(':daemon_id', $id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function confDaemonRename($id, $name, $serial, $skey='') {
		$link = Link::get_link('domoleaf');
		
		if(!empty($name) && !empty($serial)) {
			$sql = 'SELECT serial
			        FROM daemon
			        WHERE daemon_id=:daemon_id';
			$req = $link->prepare($sql);
			$req->bindValue(':daemon_id', $id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do = $req->fetch(PDO::FETCH_OBJ);
			if(empty($do)){
				return null;
			}
			
			$currentserial = $do->serial;
			
			$sql = 'UPDATE daemon
			        SET name=:name, serial=:serial
			        WHERE daemon_id=:daemon_id';
			$req = $link->prepare($sql);
			$req->bindValue(':name', $name, PDO::PARAM_STR);
			$req->bindValue(':serial', $serial, PDO::PARAM_STR);
			$req->bindValue(':daemon_id', $id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			if(!empty($skey)){
				$sql = 'UPDATE daemon
				        SET secretkey=:skey, validation=0
				        WHERE daemon_id=:daemon_id';
				$req = $link->prepare($sql);
				$req->bindValue(':skey', $skey, PDO::PARAM_STR);
				$req->bindValue(':daemon_id', $id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
			else if($currentserial != $serial){
				$sql = 'UPDATE daemon
				        SET validation=0
				        WHERE daemon_id=:daemon_id';
				$req = $link->prepare($sql);
				$req->bindValue(':daemon_id', $id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
		}
	}
	
	function confDaemonProtocolList() {
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT protocol_id, wired,
		               if(name'.$this->getLanguage().' = "", name, name'.$this->getLanguage().') as name
		        FROM protocol
		        WHERE specific_daemon=1
		        ORDER BY name'.$this->getLanguage();
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->protocol_id] = clone $do;
		}
		
		return $list;
	}
	
	function confDaemonProtocol($daemon, $newProtocolList=array(), $interface_knx='ttyAMA0', $interface_knx_arg='', $interface_EnOcean='ttyUSB0', $interface_EnOcean_arg='') {
		if ($interface_knx == "tpuarts"){
			if ($interface_knx_arg != "ttyS0" && $interface_knx_arg != "ttyS1" && $interface_knx_arg != "ttyS2"){
				$interface_knx_arg = "ttyAMA0";
			}	
		}
		else if ($interface_knx == "ipt"){
			if (!(filter_var($interface_knx_arg, FILTER_VALIDATE_IP))){
				$interface_knx = "tpuarts";
				$interface_knx_arg = "ttyAMA0";
			}
		}
		else{
			$interface_knx = "tpuarts";
			$interface_knx_arg = "ttyAMA0";
		}
		
		if ($interface_EnOcean == "usb"){
			if ($interface_EnOcean_arg != "ttyUSB0" && $interface_EnOcean_arg != "ttyUSB1"){
				$interface_EnOcean_arg = "ttyUSB0";
			}
		}
		else if ($interface_EnOcean == "tpuarts"){
			if ($interface_EnOcean_arg != "ttyS0" && $interface_EnOcean_arg != "ttyS1" && $interface_EnOcean_arg != "ttyS2"){
				$interface_EnOcean_arg = "ttyAMA0";
			}
		}
		else if ($interface_EnOcean == "ipt"){
			if (!(filter_var($interface_EnOcean_arg, FILTER_VALIDATE_IP))){
				$interface_EnOcean = "usb";
				$interface_EnOcean_arg = "ttyUSB0";
			}
		}
		else{
			$interface_EnOcean = "usb";
			$interface_EnOcean_arg = "ttyUSB0";
		}
		
		$socket = new Socket();
		$data = array(
				'daemon_id' => $daemon,
				'interface_knx' => $interface_knx,
				'interface_arg_knx' => $interface_knx_arg,
				'interface_EnOcean' => $interface_EnOcean,
				'interface_arg_EnOcean' => $interface_EnOcean_arg
				
		);
		$socket->send('send_interfaces', $data, 1);
		
		$res = $socket->receive();
		
		if (empty($res)){
			error_log('No answer from slave for confDaemonProtocol');
			return;
		}
		
		$link = Link::get_link('domoleaf');
		
		$daemonList = $this->confDaemonList();
		$protocolList = $this->confDaemonProtocolList();
		
		if(empty($daemonList) or empty($daemonList[$daemon])) {
			return null;
		}
		
		$sql = 'DELETE FROM daemon_protocol
		        WHERE daemon_id=:daemon_id';
		$req = $link->prepare($sql);
		$req->bindValue(':daemon_id', $daemon, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));

		if(!empty($newProtocolList) && sizeof($newProtocolList) > 0) {
			foreach ($newProtocolList as $protocol) {
				if(!empty($protocolList[$protocol])) {
					if ($protocol == 1 || $protocol == 2){
						$sql = 'INSERT INTO daemon_protocol
					        	(daemon_id, protocol_id, interface, interface_arg)
					        	VALUES
					        	(:daemon_id, :protocol_id, :interface, :interface_arg)';
						$req = $link->prepare($sql);
						$req->bindValue(':daemon_id',   $daemon,   PDO::PARAM_INT);
						$req->bindValue(':protocol_id', $protocol, PDO::PARAM_INT);
						if ($protocol == 1){
							$req->bindValue(':interface', $interface_knx, PDO::PARAM_STR);
							$req->bindValue(':interface_arg', $interface_knx_arg, PDO::PARAM_STR);
						}
						else{
							$req->bindValue(':interface', $interface_EnOcean, PDO::PARAM_STR);
							$req->bindValue(':interface_arg', $interface_EnOcean_arg, PDO::PARAM_STR);
						}
						$req->execute() or die (error_log(serialize($req->errorInfo())));						
					}
					else{
						$sql = 'INSERT INTO daemon_protocol
					        	(daemon_id, protocol_id)
					        	VALUES
					        	(:daemon_id, :protocol_id)';
						$req = $link->prepare($sql);
						$req->bindValue(':daemon_id',   $daemon,   PDO::PARAM_INT);
						$req->bindValue(':protocol_id', $protocol, PDO::PARAM_INT);
						$req->execute() or die (error_log(serialize($req->errorInfo())));
					}
				}
			}
		}
	}
	
	function confDaemonSendValidation($iddaemon){
		$socket = new Socket();
		$data = array(
			'daemon_id' => $iddaemon
		);
		$socket->send('check_slave', $data, 1);
		return $socket->receive();
	}
	
	function confMenuProtocol() {
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT count(protocol_id) as nb, protocol_id
		        FROM daemon_protocol
		        GROUP BY protocol_id';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->protocol_id] = $do->nb;
		}
		
		return $list;
	}
	
	/*** User permission ***/
	function mcAllowed(){
		$link = Link::get_link('domoleaf');
		
		$listFloor = array();
		$listRoom  = array();
		$listDevice= array();
		$listSmartcmd = array();
		$listApps  = array();

		$res = $this->conf_load();

		$sql = 'SELECT floor_name, mcuser_floor.floor_id, mcuser_floor.floor_order
		        FROM mcuser_floor
		        JOIN floor ON mcuser_floor.floor_id=floor.floor_id
		        WHERE mcuser_id=:user_id
		        ORDER BY floor_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listFloor[$do->floor_id] = array(
				'floor_name' => $do->floor_name,
				'floor_id'   => $do->floor_id,
				'floor_order'=> $do->floor_order
			);
		}
		
		$sql = 'SELECT room.room_name, room.room_id, mcuser_room.room_order,
		               floor, mcuser_room.room_bgimg
		        FROM room
		        JOIN mcuser_room ON room.room_id=mcuser_room.room_id
		        JOIN mcuser_floor ON room.floor=mcuser_floor.floor_id AND
		                           mcuser_floor.mcuser_id=mcuser_room.mcuser_id
		        WHERE mcuser_room.mcuser_id=:user_id
		        ORDER BY mcuser_floor.floor_order ASC, room_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listRoom[$do->room_id] = array(
				'room_name' => $do->room_name,
				'room_id'   => $do->room_id,
				'room_order'=> $do->room_order,
				'room_bgimg'=> $do->room_bgimg,
				'floor_id'  => $do->floor
			);
		}
		
		$sql = 'SELECT room_device.name, room_device.room_device_id,
		               room_device.room_id, room_order,
		               mcuser_device.device_order, application_id,
		               room_device.device_id, room_device.protocol_id,
		               mcuser_device.device_bgimg
		        FROM room_device
		        JOIN device ON room_device.device_id=device.device_id
		        JOIN mcuser_device ON room_device.room_device_id=mcuser_device.room_device_id
		        JOIN mcuser_room ON room_device.room_id=mcuser_room.room_id AND 
		                          mcuser_room.mcuser_id=mcuser_device.mcuser_id
		        JOIN room ON room.room_id=room_device.room_id
		        JOIN mcuser_floor ON room.floor=mcuser_floor.floor_id AND
		                          mcuser_floor.mcuser_id=mcuser_device.mcuser_id
		        WHERE mcuser_device.mcuser_id=:user_id
		        ORDER BY mcuser_floor.floor_order ASC, mcuser_room.room_order ASC, 
		                 mcuser_device.device_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listDevice[$do->room_device_id] = array(
				'room_id'       => $do->room_id,
				'application_id'=> $do->application_id,
				'device_id'     => $do->device_id,
				'protocol_id'   => $do->protocol_id,
				'name'          => $do->name,
				'room_device_id'=> $do->room_device_id,
				'device_order'  => $do->device_order,
				'device_bgimg'  => $do->device_bgimg,
				'device_opt'    => array()
			);
			if(!in_array($do->application_id, $listApps)){
				$listApps[] = $do->application_id;
			}
		}
		
		$sql = 'SELECT room_device.room_device_id, room_device.room_id, 
		               room_device.device_id, optiondef.option_id,
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name,
		               room_device_option.addr, room_device_option.addr_plus,
		               dpt.dpt_id,
		               dpt.unit,
		               room_device_option.opt_value
		        FROM room_device
		        JOIN room_device_option ON room_device_option.room_device_id = room_device.room_device_id
		        JOIN optiondef ON room_device_option.option_id = optiondef.option_id
		        LEFT JOIN dpt ON room_device_option.dpt_id = dpt.dpt_id
		        WHERE room_device_option.status = 1';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if(!empty($listDevice[$do->room_device_id])) {
				if ($do->option_id == 399){
					$highCost = $res[14]->configuration_value;
					$lowCost = $res[15]->configuration_value;
					$lowField1 = $res[16]->configuration_value;
					$lowField2 = $res[17]->configuration_value;
					$currency = checkCurrency($res[18]->configuration_value);
					$diffTime = $this->profileTime();
					$time = date('H', $_SERVER['REQUEST_TIME'] + $diffTime);
					$listDevice[$do->room_device_id]['device_opt'][$do->option_id] = array(
						'option_id' => $do->option_id,
						'name'      => $do->name,
						'addr'      => $do->addr,
						'addr_plus' => $do->addr_plus,
						'dpt_id'    => $do->dpt_id,
						'unit'      => $do->unit,
						'opt_value'    => $do->opt_value,
						'highCost'  => $highCost,
						'lowCost'   => $lowCost,
						'lowField1' => $lowField1,
						'lowField2' => $lowField2,
						'currency'  => $currency,
						'time'      => $time
					);
				}
				else{
					$listDevice[$do->room_device_id]['device_opt'][$do->option_id] = array(
						'option_id' => $do->option_id,
						'name'      => $do->name,
						'addr'      => $do->addr,
						'addr_plus' => $do->addr_plus,
						'dpt_id'    => $do->dpt_id,
						'unit'      => $do->unit,
						'opt_value'    => $do->opt_value
					);
				}
			}
		}
		
		$sql = 'SELECT smartcommand_id, name, mcuser_id, room_id
		        FROM smartcommand_list
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listSmartcmd[$do->smartcommand_id] = array(
					'smartcmd_id' => $do->smartcommand_id,
					'name'            => $do->name,
					'user_id'         => $do->mcuser_id,
					'room_id'         => $do->room_id
			);
		}
		
		return array(
			'ListFloor'    => $listFloor,
			'ListRoom'     => $listRoom,
			'ListDevice'   => $listDevice,
			'ListSmartcmd' => $listSmartcmd,
			'ListApp'      => $listApps
		);
	}
	
	function mcVisible(){
		$link = Link::get_link('domoleaf');
		
		$listFloor = array();
		$listRoom = array();
		$listDevice = array();
		$listApps= array();
		$listSmartcmd = array();
		
		$listall = $this->mcAllowed();
		
		foreach ($listall['ListFloor'] as $elem) {
			if($elem['floor_order'] > 0) {
				$listFloor[$elem['floor_id']] = $elem;
			}
		}
		
		foreach ($listall['ListRoom'] as $elem) {
			if($elem['room_order'] > 0) {
				$listRoom[$elem['room_id']] = $elem;
			}
		}
		
		foreach ($listall['ListDevice'] as $elem) {
			if($elem['device_order'] > 0) {
				$listDevice[$elem['room_device_id']] = $elem;
				if(!in_array($elem['application_id'], $listApps)) {
					$listApps[] = $elem['application_id'];
				}
			}
		}
		
		foreach ($listall['ListSmartcmd'] as $elem) {
			if(!empty($elem['room_id'])) {
				$listSmartcmd[$elem['smartcmd_id']] = $elem;
			}
		}

		if (!empty($listSmartcmd)) {
			$listApps[] = 7;
		}
		return array(
				'ListFloor'    => $listFloor,
				'ListRoom'     => $listRoom,
				'ListDevice'   => $listDevice,
				'ListSmartcmd' => $listSmartcmd,
				'ListApp'      => $listApps
		);
	}
	
	function mcResetError($room_device_id, $option_id) {
		$link = Link::get_link('domoleaf');

		$sql = 'UPDATE room_device_option
		        SET opt_value = "0"
		        WHERE room_device_id=:room_device_id AND option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $room_device_id, PDO::PARAM_INT);
		$req->bindValue(':option_id', $option_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function confUserInstallation($userid) {
		$link = Link::get_link('domoleaf');
		
		if(empty($userid)) {
			$userid = $this->getId();
		}
		
		$list = array();
	
		$sql = 'SELECT floor.floor_id, floor_name, floor_allowed, floor_order
		        FROM floor
		        JOIN mcuser_floor ON mcuser_floor.floor_id=floor.floor_id
		        WHERE mcuser_id=:user_id
		        ORDER BY floor_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->floor_id] = array(
				'floor_id'     => $do->floor_id,
				'floor_name'   => $do->floor_name,
				'floor_allowed'=> $this->getLevel() >= 2 ? 1 : $do->floor_allowed,
				'floor_order'  => $do->floor_order,
				'room'         => array()
			);
		}
		
		$sql = 'SELECT room.room_id, room_name, floor, room_allowed, room_order,
		               mcuser_room.room_bgimg
		        FROM room
		        JOIN mcuser_room ON mcuser_room.room_id = room.room_id
		        WHERE mcuser_id=:user_id
		        ORDER BY room_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->floor]['room'][$do->room_id] = array(
				'room_id'     => $do->room_id,
				'room_name'   => $do->room_name,
				'room_allowed'=> $this->getLevel() >= 2 ? 1 : $do->room_allowed,
				'room_order'  => $do->room_order,
				'room_bgimg'  => $do->room_bgimg,
				'devices'     => array()
			);
		}
		
		$sql = 'SELECT room_device.room_device_id, room_device.name, 
		               room_device.room_id, room.floor, device_allowed, device_order,
		               device_bgimg, device_id
		        FROM room_device
		        JOIN room ON room_device.room_id = room.room_id
		        JOIN mcuser_device ON mcuser_device.room_device_id=room_device.room_device_id
		        WHERE mcuser_id=:user_id
		        ORDER BY device_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->floor]['room'][$do->room_id]['devices'][$do->room_device_id] = array(
				'room_device_id'=> $do->room_device_id,
				'name'          => $do->name,
				'device_order'  => $do->device_order,
				'device_bgimg'  => $do->device_bgimg,
				'device_id'     => $do->device_id,
				'device_allowed'=> $this->getLevel() >= 2 ? 1 : $do->device_allowed
			);
		}
		
		return $list;
	}
	
	//device
	
	function confUserDeviceEnable($userid){
		if (empty($userid)) {
			$userid = $this -> getId();
		}
		$link = Link::get_link('domoleaf');
		$list = array();

		$sql = 'SELECT mcuser_id, room_device_id, device_allowed, device_order,
		               device_bgimg
		        FROM mcuser_device
		        WHERE mcuser_id=:user_id
		        ORDER BY room_device_id ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->room_device_id] = array(
				'user_id'       => $do->mcuser_id,
				'room_device_id'=> $do->room_device_id,
				'device_allowed'=> $do->device_allowed,
				'device_bgimg'  => $do->device_bgimg,
				'device_order'  => $do->device_order
			);
		}
		
		return $list;
	}

	function confUserPermissionDevice($userid, $deviceid, $status){
		$link = Link::get_link('domoleaf');
		
		$sql = 'SELECT mcuser_device.room_device_id, device_order, room_id
		        FROM mcuser_device
		        JOIN room_device ON mcuser_device.room_device_id = room_device.room_device_id
		        WHERE mcuser_device.room_device_id=:room_device_id AND 
		              mcuser_device.mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if($status == 1){
			$sql = 'UPDATE mcuser_device
			        SET device_allowed = 1
			        WHERE mcuser_id=:user_id AND 
			              room_device_id=:room_device_id';
			$req = $link->prepare($sql);
			$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
			$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		else {
			$sql = 'UPDATE mcuser_device
			        SET device_allowed = 0
			        WHERE mcuser_id=:user_id AND room_device_id=:room_device_id';
			$req = $link->prepare($sql);
			$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
			$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
	}
	
	//room
	
	function confUserRoomEnable($userid = 0){
		if (empty($userid)) {
			$userid = $this -> getId();
		}
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT mcuser_id, room_id, room_allowed, room_order, room_bgimg
		        FROM mcuser_room
		        WHERE mcuser_id=:user_id
		        ORDER BY room_id ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->room_id] = array(
				'user_id'     => $do->mcuser_id,
				'room_id'     => $do->room_id,
				'room_allowed'=> $do->room_allowed,
				'room_bgimg'  => $do->room_bgimg,
				'room_order'  => $do->room_order
			);
		}
		
		return $list;
	}
	
	function confUserPermissionRoom($userid, $roomid, $status){
		$link = Link::get_link('domoleaf');
		
		$sql = 'SELECT mcuser_room.room_id, room_order, floor
		        FROM mcuser_room
		        JOIN room ON mcuser_room.room_id = room.room_id
		        WHERE mcuser_room.mcuser_id=:user_id AND 
		              mcuser_room.room_id=:room_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if($status == 1){
			$sql = 'UPDATE mcuser_room
			        SET room_allowed = 1
			        WHERE mcuser_id=:user_id AND room_id=:room_id';
			$req = $link->prepare($sql);
			$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
			$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		else {
			$sql = 'UPDATE mcuser_room
			        SET room_allowed = 0
			        WHERE mcuser_id=:user_id AND room_id=:room_id';
			$req = $link->prepare($sql);
			$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
			$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
	}
	
//Floor

	function confUserFloorEnable($userid){
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT mcuser_id, floor_id, floor_allowed, floor_order
		        FROM mcuser_floor
		        WHERE mcuser_id=:user_id
		        ORDER BY floor_id ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->floor_id] = array(
				'user_id'      => $do->mcuser_id,
				'floor_id'     => $do->floor_id,
				'floor_allowed'=> $do->floor_allowed,
				'floor_order'  => $do->floor_order
			);
		}
		return $list;
	}
	
	function confUserPermissionFloor($userid, $floorid, $status){
		$link = Link::get_link('domoleaf');
		
		$sql = 'SELECT floor_id
		        FROM mcuser_floor
		        WHERE floor_id=:floor_id AND mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
		$req->bindValue(':user_id',  $userid,  PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if($status == 1){
			$sql = 'UPDATE mcuser_floor
			        SET floor_allowed = 1
			        WHERE mcuser_id=:user_id AND floor_id=:floor_id';
			$req = $link->prepare($sql);
			$req->bindValue(':user_id',  $userid,  PDO::PARAM_INT);
			$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		else {
			$sql = 'UPDATE mcuser_floor
			        SET floor_allowed = 0
			        WHERE mcuser_id=:user_id AND floor_id=:floor_id';
			$req = $link->prepare($sql);
			$req->bindValue(':user_id',  $userid,  PDO::PARAM_INT);
			$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
	}

	/*** Backup Database ***/
	
	function confDbListLocal(){
		$socket = new Socket();
		$socket->send('backup_db_list_local');
		$listDb = $socket->receive();
		if (!empty ($listDb)){
			return json_decode($listDb);
		}
		else{
			return NULL;
		}
	}
	
	function confDbCreateLocal(){
		$socket = new Socket();
		$socket->send('backup_db_create_local');
		
		$socket->receive();
	}
	
	function confDbRemoveUsb($filename){
		if (empty($filename) || sizeof($filename) < 1){
			return NULL;
		}
		$socket = new Socket();
		$socket->send('backup_db_remove_usb', $filename);
		
		$socket->receive();
	}
	
	function confDbRestoreUsb($filename){
		if (empty($filename) || sizeof($filename) < 1){
			return NULL;
		}
		$socket = new Socket();
		$socket->send('backup_db_restore_usb', $filename);
		
		$socket->receive();
		
		$socket = new Socket();
		$socket->send('reload_d3config');
	}
	
	function confDbCheckUsb(){
		$socket = new Socket();
		$socket->send('check_usb');

		$res = $socket->receive();
		return $res;
	}
	
	function confDbListUsb(){
		$socket = new Socket();
		$socket->send('backup_db_list_usb');
		$listBackupUsb = $socket->receive();
		if (!empty ($listBackupUsb)){
			return json_decode($listBackupUsb);
		}
		else{
			return NULL;
		}
	}

	function confDbCreateUsb(){
		$socket = new Socket();
		$socket->send('backup_db_create_usb');
	
		$socket->receive();
	}
	
	function confDbRemoveLocal($filename){
		if (empty($filename) || sizeof($filename) < 1){
			return NULL;
		}
		$socket = new Socket();
		$socket->send('backup_db_remove_local', $filename);
	
		$socket->receive();
	}
	
	function confDbRestoreLocal($filename){
		if (empty($filename) || sizeof($filename) < 1){
			return NULL;
		}
		$socket = new Socket();
		$socket->send('backup_db_restore_local', $filename);
	
		$socket->receive();
		
		$socket = new Socket();
		$socket->send('reload_d3config');
	}

	/*** Option ***/
	function confOptionList(){
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT option_id, 
		               if(name'.$this->getLanguage().' = "", name, name'.$this->getLanguage().') as name 
		        FROM optiondef
		        ORDER BY name'.$this->getLanguage();
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->option_id] = clone $do;
		}
		
		return $list;
	}
	
	function confOptionDptList($iddevice = 0){
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT dpt_optiondef.dpt_id, option_id, unit
		        FROM dpt_optiondef
		        JOIN dpt
		        ON dpt.dpt_id=dpt_optiondef.dpt_id
		        JOIN room_device
		        ON room_device.protocol_id = dpt_optiondef.protocol_id
		        WHERE room_device_id=:iddevice
		        ORDER BY dpt_optiondef.dpt_id';
		$req = $link->prepare($sql);
		$req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->option_id][] = clone $do;
		}
		/*
		 * JOIN room_device
		        ON room_device.protocol_id = dpt_optiondef.protocol_id
		        WHERE room_device_id=:iddevice
		        $req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		 */
		return $list;
	}

	function checkDevice($iddevice){
		$link = Link::get_link('domoleaf');
	
		$sql = 'SELECT mcuser_id
		        FROM mcuser_device
		        WHERE mcuser_id=:user_id AND room_device_id=:iddevice';
		$req = $link->prepare($sql);
		$req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		if(empty($do->mcuser_id)) {
			return false;
		}
	
		return true;
	}

	function monitorEnocean() {
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT type, addr_src, addr_dest, eo_value, t_date, daemon_id
		        FROM enocean_log
		        ORDER BY t_date DESC
		        LIMIT 1000';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[] = clone $do;
		}
	
		return $list;
	}
	
	function monitorKnx() {
		$link = Link::get_link('domoleaf');
		$listDevices = array();
		$list = array();
		
		$sql = 'SELECT room_device_option.room_device_id, room_device.name,
		               room_device.device_id, daemon_id, room_device_option.addr, addr_plus
		        FROM room_device_option
		        JOIN room_device ON room_device.room_device_id = room_device_option.room_device_id';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			if(!empty($do->daemon_id)) {
				$listDevices[$do->daemon_id][$do->addr] = $do->name;
				if(!empty($do->addr_plus)) {
					$listDevices[$do->daemon_id][$do->addr_plus] = $do->name;
				}
			}
		}
		
		$sql = 'SELECT type, addr_src, addr_dest, knx_value, t_date, daemon_id
		        FROM knx_log
		        ORDER BY t_date DESC
		        LIMIT 1000';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if(!empty($do->daemon_id) && (!empty($listDevices[$do->daemon_id])) && (!empty($listDevices[$do->daemon_id][$do->addr_dest]))) {
				$name = $listDevices[$do->daemon_id][$do->addr_dest];
			}
			else{
				$name = '';
			}
			$list[] = array(
				'type'        => $do->type,
 				'addr_src'    => $do->addr_src,
				'addr_dest'   => $do->addr_dest,
				'knx_value'   => $do->knx_value,
				't_date'      => $do->t_date,
				'daemon_id'   => $do->daemon_id,
				'device_name' => $name 
			);
		}
		
		return $list;
	}
	
	function monitorIp() {
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT mac_addr, ip_addr, hostname, last_update
		        FROM ip_monitor
		        ORDER BY hostname';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[] = clone $do;
		}
		
		return $list;
	}
	
	function monitorIpRefresh(){
		$socket = new Socket();
		
		$socket->send("monitor_ip");
	}
	
	/********************** User permission **********************/
	
	/**
	 * Set floor order
	 * @param int : user id
	 * @param int : floor id
	 * @param int : -1 ou 1
	 */
	function SetFloorOrder($userid, $floorid, $action) {
		$link = Link::get_link('domoleaf');
		
		if(empty($userid)){
			$userid = $this->getId();
		}
		
		$sql = 'SELECT floor_order, floor_id
		        FROM mcuser_floor
		        WHERE mcuser_id=:user_id AND floor_id=:floor_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		$order = $do->floor_order + $action;
		if($order >= 1) {
			$sql = 'SELECT floor_order, floor_id
			        FROM mcuser_floor
			        WHERE floor_order=:order AND mcuser_id=:user_id';
			$req = $link->prepare($sql);
			$req->bindValue(':order', $order, PDO::PARAM_INT);
			$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do2 = $req->fetch(PDO::FETCH_OBJ);
				
			if(!empty($do2)) {
				$sql = 'UPDATE mcuser_floor
				        SET floor_order=:order
				        WHERE mcuser_id=:user_id AND floor_id=:floor_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do->floor_order + $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
				$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
		
				$sql = 'UPDATE  mcuser_floor
				        SET floor_order=:order
				        WHERE floor_id=:floor_id AND mcuser_id=:user_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do2->floor_order - $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
				$req->bindValue(':floor_id', $do2->floor_id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
				
		}
	}
	
	/**
	 * Set room order
	 * @param int : user id
	 * @param int : room id
	 * @param int : -1 ou 1
	 */
	function SetRoomOrder($userid, $roomid, $action){
		$link = Link::get_link('domoleaf');
	
		if(empty($userid)){
			$userid = $this->getId();
		}
	
		$sql = 'SELECT room_order, room_id
		        FROM mcuser_room
		        WHERE mcuser_id=:user_id AND room_id=:room_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		$order = $do->room_order + $action;
		if($order >= 1){
			$sql = 'SELECT room_order, room_id
			        FROM mcuser_room
			        WHERE room_order=:order AND mcuser_id=:user_id';
			$req = $link->prepare($sql);
			$req->bindValue(':order', $order, PDO::PARAM_INT);
			$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do2 = $req->fetch(PDO::FETCH_OBJ);
	
			if(!empty($do2)){
				$sql = 'UPDATE mcuser_room
				        SET room_order=:order
				        WHERE mcuser_id=:user_id AND room_id=:room_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do->room_order + $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
				$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
					
				$sql = 'UPDATE  mcuser_room
				        SET room_order=:order
				        WHERE room_id=:room_id AND mcuser_id=:user_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do2->room_order - $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
				$req->bindValue(':room_id', $do2->room_id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
		}
	}
	
	/**
	 * Set device order
	 * @param int : user id
	 * @param int : device id
	 * @param int : -1 ou 1
	 */
	function SetDeviceOrder($userid, $deviceid, $action){
		$link = Link::get_link('domoleaf');
	
		if(empty($userid)){
			$userid = $this->getId();
		}
		
		$sql = 'SELECT  device_order, room_device_id
		        FROM mcuser_device
		        WHERE mcuser_id=:user_id AND room_device_id=:room_device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		$order = $do->device_order + $action;
		if($order >= 1) {
			$sql = 'SELECT device_order, room_device_id
			        FROM mcuser_device
			        WHERE device_order=:order AND mcuser_id=:user_id';
			$req = $link->prepare($sql);
			$req->bindValue(':order', $order, PDO::PARAM_INT);
			$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do2 = $req->fetch(PDO::FETCH_OBJ);
				
			if(!empty($do2)){
				$sql = 'UPDATE mcuser_device
				        SET device_order=:order
				        WHERE mcuser_id=:user_id AND room_device_id=:room_device_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do->device_order + $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
				$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
	
				$sql = 'UPDATE  mcuser_device
				        SET device_order=:order
				        WHERE room_device_id=:room_device_id AND mcuser_id=:user_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do2->device_order - $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':room_device_id', $do2->room_device_id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
		}
	}
	
	/*** User customisation ***/
	
	function confUserDeviceBgimg($iddevice, $bgimg, $userid = 0){
		if (empty($userid)) {
			$userid = $this->getId();
		}

		$link = Link::get_link('domoleaf');
		
		$sql = 'UPDATE mcuser_device
		        SET device_bgimg=:bgimg
		        WHERE mcuser_id=:user_id AND room_device_id=:iddevice';
		$req = $link->prepare($sql);
		$req->bindValue(':bgimg', $bgimg, PDO::PARAM_STR);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function confUserRoomBgimg($idroom, $bgimg, $userid = 0){
		if (empty($userid)) {
			$userid = $this->getId();
		}
		
		$link = Link::get_link('domoleaf');
	
		$sql = 'UPDATE mcuser_room
		        SET room_bgimg=:bgimg
		        WHERE mcuser_id=:user_id AND room_id=:idroom';
		$req = $link->prepare($sql);
		$req->bindValue(':bgimg', $bgimg, PDO::PARAM_STR);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->bindValue(':idroom', $idroom, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function userUpdateBGColor($color, $userid = 0){
		if (empty($userid)) {
			$userid = $this->getId();
		}
	
		$link = Link::get_link('domoleaf');
	
		$sql = 'UPDATE mcuser
		        SET bg_color=:color
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':color', $color, PDO::PARAM_STR);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function userUpdateMenusBordersColor($color, $userid = 0){
		if (empty($userid)) {
			$userid = $this->getId();
		}
	
		$link = Link::get_link('domoleaf');
	
		$sql = 'UPDATE mcuser
		        SET border_color=:color
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':color', $color, PDO::PARAM_STR);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/*** KNX action ***/
	
	function knx_write_l($daemon, $addr, $value=0){
		$socket = new Socket();
		$tab = array(
			'daemon' => $daemon,
			'addr'   => $addr,
			'value'  => $value
		);
		
		$socket->send('knx_write_l', $tab);
	}
	
	function knx_write_s($daemon, $addr, $value=0){
		$socket = new Socket();
		$tab = array(
			'daemon' => $daemon,
			'addr'   => $addr,
			'value'  => $value
		);
		
		$socket->send('knx_write_s', $tab);
	}
	
	function knx_read($daemon, $addr){
		$socket = new Socket();
		$tab = array(
			'daemon' => $daemon,
			'addr'   => $addr
		);
		
		$socket->send('knx_read', $tab);
	}
	
	/*** KNX log ***/
	function confKnxAddrList(){
		$link = Link::get_link('domoleaf');
		$list = array();
		
		$sql = 'SELECT DISTINCT(addr_src) as addr_src
		        FROM knx_log';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[] = $do->addr_src;
		}
		
		return $list;
	}
}

?>
