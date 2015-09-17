<?php 

class User {
	private $id;
	private $level;
	private $language;
	
	/*** Get/Set ***/
	
	/**
	 * Constructor
	 * @param int : user id
	 */
	function __construct($userId) {
		$this->id = $userId;
	}
	
	function getId() {
		return $this->id;
	}
	
	function getLevel() {
		return $this->level;
	}
	
	function setLevel($level) {
		$this->level = $level;
	}
	
	function getLanguage() {
		return $this->language;
	}
	
	function setLanguage($language) {
		$this->language = $language;
	}
	
	/**
	 * Update user's activity
	 */
	function activity() {
		$link = Link::get_link('mastercommand');
			
		$sql = 'UPDATE user
		        SET activity= :activity
		        WHERE user_id= :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':activity', $_SERVER['REQUEST_TIME'], PDO::PARAM_INT);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/*** Disconnect ***/
	
	/**
	 * Disconnect user
	 * @param string : user's token
	 */
	function disconnect($token) {
		$link = Link::get_link('mastercommand');
		
		$sql = 'DELETE user_token
		        WHERE token= :token';
		$req = $link->prepare($sql);
		$req->bindValue(':token', $token, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/********************** Domotic configuration **********************/

	function conf_load(){
		$link = Link::get_link('mastercommand');
		$list = array();
		
		$sql = 'SELECT configuration_id, configuration_value
		        FROM configuration
		        ORDER BY configuration_id ASC';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->configuration_id] = clone $do;
		}
		
		return $list;
	}
	
	/*** Daemon management ***/
	
	function confDeamonList() {
		return null;
	}
	
	function confDeamonNew($name, $serial, $skey) {
		return null;
	}
	
	function confDeamonRemove($id) {
		return null;
	}
	
	function confDeamonRename($id, $name, $serial, $skey='') {
		return null;
	}
	
	function confDaemonProtocolList() {
		return null;
	}
	
	function confDaemonProtocol($daemon, $protocol=array()) {
		return null;
	}
	
	/*** Floors ***/
	function confFloorList() {
		return null;
	}
	
	function confFloorNew($name) {
		return null;
	}
	
	function confFloorRename($id, $name) {
		return null;
	}
	
	function confFloorRemove($id) {
		return null;
	}
	
	/*** Rooms ***/
	function confRoomList($floor) {
		return null;
	}
	
	function confRoomNew($name, $floor) {
		return null;
	}
	
	function confRoomRename($id, $name) {
		return null;
	}
	
	function confRoomFloor($id, $floor) {
		return null;
	}
	
	function confRoomRemove($idroom, $idfloor) {
		return null;
	}
	
	/*** Devices ***/
	function confRoomDeviceRemove($iddevice, $idroom) {
		return null;
	}
	
	function confRoomDeviceAll($iddevice){
		return null;
	}
	
	function confDeviceSaveInfo($idroom, $name, $daemon='', $devaddr, $iddevice){
		return null;
	}
	
	function confDeviceSaveOption($idroom, $options) {
		return null;
	}
	
	function confDeviceRoomOpt($deviceroomid) {
		return null;
	}
	
	function confRoomDeviceList($room){
		return null;
	}

	function confDeviceProtocol($device=0) {
		return null;
	}
	
	function confDeviceNewIp($name, $proto, $room, $device, $addr, $port='80', $login='', $pass='') {
		return null;
	}
	
	function confDeviceNewKnx($name, $proto, $room, $device, $daemon, $addr) {
		return null;
	}
	
	function confDeviceNewEnocean($name, $proto, $room, $device, $addr) {
		return null;
	}
	
	/********************** Domotic definition **********************/
	
	/**
	 * Return all applications
	 * @return array : application list
	 */
	function confApplicationAll() {
		$link = Link::get_link('mastercommand');
		$list = array();
		
		$sql = 'SELECT if(name'.$this->getLanguage().' = "", name, name'.$this->getLanguage().') as name,
		               application_id
		        FROM application
		        ORDER BY name'.$this->getLanguage();
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->application_id] = clone $do;
		}
		
		return $list;
	}
	
	/**
	 * Return all protocols
	 * @return array : protocol list
	 */
	function confProtocolAll() {
		$link = Link::get_link('mastercommand');
		$list = array();
		
		$sql = 'SELECT protocol_id, wired,
		               if(name'.$this->getLanguage().' = "", name, name'.$this->getLanguage().') as name
		        FROM protocol
		        ORDER BY name'.$this->getLanguage();
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->protocol_id] = clone $do;
		}
		
		return $list;
	}
	
	/**
	 * Return all devices
	 * @return array : device list
	 */
	function confDeviceAll() {
		$link = Link::get_link('mastercommand');
		$list = array();
		
		$sql = 'SELECT device_id, protocol_id, application_id,
		               if(name'.$this->getLanguage().' = "", name, name'.$this->getLanguage().') as name
		        FROM device
		        WHERE mc_element=1
		        ORDER BY name ASC';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->device_id] = array(
				'device_id'      => $do->device_id,
				'protocol_id'    => $do->protocol_id,
				'application_id' => $do->application_id,
				'name'           => $do->name,
				'protocol_option'=> array()
			);
		}
		
		$sql = 'SELECT device_option. device_id, device_option.option_id,
		               if(protocol_id IS NULL,0,protocol_id) as protocol_id,
		               hidden_arg, groupe, bydefault,
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name
		        FROM device_option
		        JOIN optiondef ON optiondef.option_id=device_option.option_id';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if(!empty($list[$do->device_id]) and $do->hidden_arg & 0x04) {
				$list[$do->device_id]['protocol_option'][$do->protocol_id][$do->option_id] = clone $do;
			}
		}
		
		return $list;
	}
	
	/********************** Users configuration **********************/

	function profileList() { return null; }
	
	/**
	 * Get user information
	 * @param int : not used
	 * @return object : user information
	 */
	function profileInfo($id=0) {
		$link = Link::get_link('mastercommand');
	
		$sql = 'SELECT user_id, username, user_mail, lastname, firstname,
		               gender, phone, language, design
		        FROM user
		        WHERE user_id= :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		return $do;
	}
	
	function profileNew($username, $password) {
		return null;
	}
	
	function profileRemove($user_id) {
		return 0;
	}
	
	/**
	 * 
	 * @param string : lastname
	 * @param string : firstname
	 * @param int : gender
	 * @param string : phone number
	 * @param string : language
	 * @param int : user id, not used
	 */
	function profileRename($lastname, $firstname, $gender, $phone, $language, $id=0) {
		$link = Link::get_link('mastercommand');
	
		if($gender != 1) {
			$gender = 0;
		}
	
		$langList = Guest::language();
		if(empty($langList[$language])) {
			$language = $this->getLanguage();
		}
	
		$sql = 'UPDATE user
		        SET lastname= :lastname,
		            firstname= :firstname,
		            gender= :gender,
		            phone= :phone,
		            language= :language
		        WHERE user_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':lastname', $lastname, PDO::PARAM_STR);
		$req->bindValue(':firstname', $firstname, PDO::PARAM_STR);
		$req->bindValue(':gender', $gender, PDO::PARAM_INT);
		$req->bindValue(':phone', $phone, PDO::PARAM_STR);
		$req->bindValue(':language', $language, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function profileLevel($id, $level) {
		return null;
	}
	
	function profileUsername($id, $username) {
		return null;
	}
	
	/**
	 * 
	 * @param unknown $last
	 * @param unknown $new
	 * @param number $id
	 */
	function profilePassword($last, $new, $id=0) {
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT user_id, user_password
		        FROM user
		        WHERE user_id= :id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		if($do->user_password == hash('sha256', $do->user_id.'_'.$last)) {
			$sql = 'UPDATE user
			        SET user_password=:user_password
			        WHERE user_id=:user_id';
			$req = $link->prepare($sql);
			$req->bindValue(':user_password', hash('sha256', $do->user_id.'_'.$new), PDO::PARAM_STR);
			$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
	}
	
	/********************** Monitors **********************/
	
	function monitorKnx() {
		return null;
	}
	
	function monitorIp() {
		return null;
	}
	
	function monitorIpRefresh() {
		return null;
	}
	
	function monitorEnocean() {
		return null;
	}
	
	function monitorBluetooth() {
		return null;
	}
	
	/********************** User permission **********************/
	
	/**
	 * Set floor order
	 * @param unknown $userid
	 * @param unknown $floorid
	 * @param unknown $action
	 */
	function SetFloorOrder($userid, $floorid, $action) {
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT floor_order, floor_id
		        FROM user_floor
		        WHERE user_id=:user_id AND floor_id=:floor_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id',$this->getId(), PDO::PARAM_INT);
		$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		$order = $do->floor_order + $action;
		if($order >= 1) {
			$sql = 'SELECT floor_order, floor_id
			        FROM user_floor
			        WHERE floor_order=:order AND user_id=:user_id';
			$req = $link->prepare($sql);
			$req->bindValue(':order', $order, PDO::PARAM_INT);
			$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do2 = $req->fetch(PDO::FETCH_OBJ);
			
			if(!empty($do2)) {
				$sql = 'UPDATE user_floor
				        SET floor_order=:order
				        WHERE user_id=:user_id AND floor_id=:floor_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do->floor_order + $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
				
				$sql = 'UPDATE  user_floor
				        SET floor_order=:order
				        WHERE floor_id=:floor_id AND user_id=:user_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do2->floor_order - $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':floor_id', $do2->floor_id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
			
		}
	}
	
	/**
	 * 
	 * @param unknown $userid
	 * @param unknown $roomid
	 * @param unknown $action
	 */
	function SetRoomOrder($userid, $roomid, $action){
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT room_order, room_id
		        FROM user_room
		        WHERE user_id=:user_id AND room_id=:room_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		$order = $do->room_order + $action;
		if($order >= 1){
			$sql = 'SELECT room_order, room_id
			        FROM user_room
			        WHERE room_order=:order AND user_id=:user_id';
			$req = $link->prepare($sql);
			$req->bindValue(':order', $order, PDO::PARAM_INT);
			$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do2 = $req->fetch(PDO::FETCH_OBJ);
		
			if(!empty($do2)){
				$sql = 'UPDATE user_room
				        SET room_order=:order
				        WHERE user_id=:user_id AND room_id=:room_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do->room_order + $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			
				$sql = 'UPDATE  user_room
				        SET room_order=:order
				        WHERE room_id=:room_id AND user_id=:user_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do2->room_order - $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':room_id', $do2->room_id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
		}
	}
	
	/**
	 * 
	 * @param unknown $userid
	 * @param unknown $deviceid
	 * @param unknown $action
	 */
	function SetDeviceOrder($userid, $deviceid, $action){
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT  device_order, room_device_id
		        FROM user_device
		        WHERE user_id=:user_id AND room_device_id=:room_device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		$order = $do->device_order + $action;
		if($order >= 1) {
			$sql = 'SELECT device_order, room_device_id
			        FROM user_device
			        WHERE device_order=:order AND user_id=:user_id';
			$req = $link->prepare($sql);
			$req->bindValue(':order', $order, PDO::PARAM_INT);
			$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do2 = $req->fetch(PDO::FETCH_OBJ);
			
			if(!empty($do2)){
				$sql = 'UPDATE user_device
				        SET device_order=:order
				        WHERE user_id=:user_id AND room_device_id=:room_device_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do->device_order + $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
				
				$sql = 'UPDATE  user_device
				        SET device_order=:order
				        WHERE room_device_id=:room_device_id AND user_id=:user_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do2->device_order - $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':room_device_id', $do2->room_device_id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
		}
	}
	
	
	function mcValueDef($iddevice, $idoption, $action){
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT  valeur
				FROM    room_device_option
				WHERE   room_device_id=:iddevice
				AND     option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':option_id', $idoption, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$do = $req->fetch(PDO::FETCH_OBJ);
		$val = '0';
		if (!empty($do->valeur)){
			$val = $do->valeur + $action;
		}
		if ((int)$val == $val){
			$val = $val.'.0';
		}
		$sql = 'UPDATE  room_device_option
				SET     valeur =  :valeur
				WHERE   room_device_id=:iddevice
				AND     option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':option_id', $idoption, PDO::PARAM_INT);
		$req->bindValue(':valeur', $val, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$this->mcTemperature($iddevice, $idoption, $val);
		
		return $val;
	}
	
	/**
	 * 
	 * @return multitype:multitype:NULL  multitype:multitype:NULL   Ambigous <multitype:multitype:multitype: NULL  , multitype:NULL >
	 */
	function mcAllowed(){
		$link = Link::get_link('mastercommand');
		
		$listFloor = array();
		$listRoom = array();
		$listDevice = array();
		$listSmartcmd = array();
		$listApps= array();
		
		$sql = 'SELECT floor_name, user_floor.floor_id, user_floor.floor_order
		        FROM user_floor
		        JOIN floor ON user_floor.floor_id=floor.floor_id
		        WHERE user_id=:user_id AND user_floor.floor_allowed = 1
		        ORDER BY floor_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listFloor[$do->floor_id] = array(
				'floor_name' => $do->floor_name,
				'floor_id' => $do->floor_id,
				'floor_order' 	=> $do->floor_order
			);
		}
		
		$sql = 'SELECT room.room_name, room.room_id, user_room.room_order, 
		               floor
		        FROM room
		        JOIN user_room ON room.room_id=user_room.room_id
		        JOIN user_floor ON room.floor=user_floor.floor_id AND
		                           user_floor.user_id=user_room.user_id
		        WHERE user_room.user_id=:user_id AND   user_room.room_allowed = 1
		        ORDER BY user_floor.floor_order ASC, room_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listRoom[$do->room_id] = array(
				'room_name' => $do->room_name,
				'room_id' => $do->room_id,
				'room_order'   => $do->room_order,
				'floor_id' => $do->floor
			);
		}
		$sql = 'SELECT room_device.name, room_device.room_device_id,
		               room_device.room_id, room_order,
		               user_device.device_order, application_id,
		               room_device.device_id, room_device.protocol_id,
		               user_device.device_bgimg
		        FROM room_device
		        JOIN device ON room_device.device_id=device.device_id
		        JOIN user_device ON room_device.room_device_id=user_device.room_device_id
		        JOIN user_room ON room_device.room_id=user_room.room_id AND 
		                          user_room.user_id=user_device.user_id
		        JOIN room ON room.room_id=room_device.room_id
		        JOIN user_floor ON room.floor=user_floor.floor_id AND
		                          user_floor.user_id=user_device.user_id
		        WHERE user_device.user_id=:user_id AND user_device.device_allowed = 1
		        ORDER BY user_floor.floor_order ASC, user_room.room_order ASC, 
		                 user_device.device_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listDevice[$do->room_device_id] = array(
				'room_id' => $do->room_id,
				'application_id' => $do->application_id,
				'device_id' => $do->device_id,
				'protocol_id' => $do->protocol_id,
				'name' => $do->name,
				'room_device_id' => $do->room_device_id,
				'device_order' => $do->device_order,
				'device_bgimg'  => $do->device_bgimg,
				'device_opt' => array()
			);
			if(!in_array($do->application_id, $listApps)){
				$listApps[] = $do->application_id;
			}
		}
		
		$sql = 'SELECT smartcommand_id, name, user_id, room_id
		        FROM smartcommand_list
		        WHERE user_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listSmartcmd[$do->smartcommand_id] = array(
					'smartcmd_id' => $do->smartcommand_id,
					'name'            => $do->name,
					'user_id'         => $do->user_id,
					'room_id'         => $do->room_id
			);
		}
		
		$sql = 'SELECT room_device.room_device_id, room_device.room_id, 
		               optiondef.hidden_arg, room_device.device_id, 
		               optiondef.option_id, optiondef.name, 
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name,
		               room_device_option.addr, room_device_option.addr_plus, 
		               room_device_option.valeur
		        FROM room_device
		        JOIN room_device_option ON room_device_option.room_device_id = room_device.room_device_id
		        JOIN optiondef ON room_device_option.option_id = optiondef.option_id
		        WHERE room_device_option.status = 1';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if($do->hidden_arg & 4 and !empty($listDevice[$do->room_device_id])) {
				$listDevice[$do->room_device_id]['device_opt'][$do->option_id] = array(
					'option_id' => $do->option_id,
					'name' 		=> $do->name,
					'addr'		=> $do->addr,
					'addr_plus' => $do->addr_plus,
					'valeur'	=> $do->valeur
				);
			}
		}
		
		return array(
			'ListFloor'    => $listFloor,
			'ListRoom'     => $listRoom,
			'ListDevice'   => $listDevice,
			'ListSmartcmd' => $listSmartcmd,
			'ListApp'      => $listApps
		);
	}
	
	/**
	 * 
	 * @return multitype:multitype:unknown  multitype:multitype:NULL
	 */
	function mcVisible(){
		$link = Link::get_link('mastercommand');
		
		$listFloor = array();
		$listRoom = array();
		$listDevice = array();
		$listSmartcmd = array();
		$listApps= array();
		
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
	
	/**
	 * 
	 * @param unknown $userid
	 * @return Ambigous <multitype:multitype:multitype: NULL  , multitype:NULL >
	 */
	function confUserInstallation($userid){
		$link = Link::get_link('mastercommand');
		
		if(empty($userid)){
			$userid = $this->getId();
		}
		$list = array();
		$sql = 'SELECT floor.floor_id, floor_name, floor_allowed, floor_order
		        FROM floor
		        JOIN user_floor ON user_floor.floor_id=floor.floor_id
		        WHERE user_id=:user_id AND floor_allowed=1
		        ORDER BY floor_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->floor_id] = array(
				'floor_id'      => $do->floor_id,
				'floor_name'    => $do->floor_name,
				'floor_allowed' => $do->floor_allowed,
				'floor_order'	=> $do->floor_order,
				'room' => array()
			);
		}
		
		$sql = 'SELECT room.room_id, room_name, floor, room_order, room_allowed
		        FROM room
		        JOIN user_room ON user_room.room_id = room.room_id
		        WHERE user_id=:user_id AND room_allowed=1
		        ORDER BY room_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->floor]['room'][$do->room_id] = array(
				'room_id'     => $do->room_id,
				'room_name'   => $do->room_name,
				'room_allowed'=> $do->room_allowed,
				'room_order'  => $do->room_order,
				'devices'     => array()
			);
		}
		
		$sql = 'SELECT room_device.room_device_id, room_device.name, 
		               room_device.room_id, room.floor, device_order, 
		               device_allowed, device_bgimg
		        FROM room_device
		        JOIN room ON room_device.room_id = room.room_id
		        JOIN user_device ON user_device.room_device_id=room_device.room_device_id
		        WHERE user_id=:user_id AND device_allowed=1
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
				'device_allowed'=> $do->device_allowed
			);
		}
		
		return $list;
	}
	
	/**
	 * 
	 * @param unknown $userid
	 * @param unknown $deviceid
	 * @param unknown $status
	 */
	function confUserVisibleDevice($userid, $deviceid, $status){
		$link = Link::get_link('mastercommand');
		
		if(empty($userid)){
			$userid = $this->getId();
		}
		
		$sql = 'SELECT user_device.room_device_id, device_order, room_id
		        FROM user_device
		        JOIN room_device ON user_device.room_device_id = room_device.room_device_id
		        WHERE user_device.room_device_id=:room_device_id AND user_device.user_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if(!empty($do)){
			if($status == 1){
				if($do->device_order == 0){
					$sql = 'SELECT device_order
				 	        FROM user_device
					        JOIN room_device ON user_device.room_device_id = room_device.room_device_id
					        WHERE user_id=:user_id AND room_id =:room_id
					        ORDER BY device_order DESC
					        LIMIT 1';
					$req = $link->prepare($sql);
					$req->bindValue(':room_id', $do->room_id, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
					$do2 = $req->fetch(PDO::FETCH_OBJ);
					
					$sql = 'UPDATE user_device
				 	        SET device_order = :order
					        WHERE user_id=:user_id AND room_device_id=:room_device_id';
					$req = $link->prepare($sql);
					$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
					$req->bindValue(':order', $do2->device_order + 1, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
			else {
				$sql = 'UPDATE user_device
			 	        SET device_order = 0
				        WHERE user_id=:user_id AND room_device_id=:room_device_id';
				$req = $link->prepare($sql);
				$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
				$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
		
				if($do->device_order > 0){
					$sql = 'UPDATE room_device
					        JOIN user_device ON user_device.room_device_id = room_device.room_device_id
					        SET device_order = device_order - 1
					        WHERE room_device.room_id =:room_id AND user_device.user_id=:user_id AND
					              device_order > :device_order';
					$req = $link->prepare($sql);
					$req->bindValue(':room_id', $do->room_id, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->bindValue(':device_order',$do->device_order, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
		}
	}
	
	/**
	 * 
	 * @param unknown $userid
	 * @param unknown $roomid
	 * @param unknown $status
	 */
	function confUserVisibleRoom($userid, $roomid, $status){
		$link = Link::get_link('mastercommand');
		
		if(empty($userid)) {
			$userid = $this->getId();
		}
		
		$sql = 'SELECT user_room.room_id, room_order, floor
		        FROM user_room
		        JOIN room ON user_room.room_id = room.room_id
		        WHERE user_room.room_id=:room_id AND user_room.user_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if(!empty($do)){
			if($status == 1){
				if($do->room_order == 0){
					$sql = 'SELECT room_order
				 	        FROM user_room
					        JOIN room ON user_room.room_id = room.room_id
					        WHERE user_id=:user_id AND floor =:floor
					        ORDER BY room_order DESC
					        LIMIT 1';
					$req = $link->prepare($sql);
					$req->bindValue(':floor', $do->floor, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
					$do2 = $req->fetch(PDO::FETCH_OBJ);
					
					$sql = 'UPDATE user_room
				 	        SET room_order = :order
					        WHERE user_id=:user_id AND room_id=:room_id';
					$req = $link->prepare($sql);
					$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
					$req->bindValue(':order', $do2->room_order + 1, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
			else {
				$sql = 'UPDATE user_room
			 	        SET room_order = 0
				        WHERE user_id=:user_id AND room_id=:room_id';
				$req = $link->prepare($sql);
				$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
				$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
				
				if($do->room_order > 0){
					$sql = 'UPDATE room
					        JOIN user_room ON user_room.room_id = room.room_id
					        SET room_order = room_order - 1
					        WHERE room.floor =:floor AND 
					              user_room.user_id=:user_id AND 
					              room_order > :room_order';
					$req = $link->prepare($sql);
					$req->bindValue(':floor', $do->floor, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->bindValue(':room_order',$do->room_order, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
		}
	}
	
	/**
	 * 
	 * @param unknown $userid
	 * @param unknown $floorid
	 * @param unknown $status
	 */
	function confUserVisibleFloor($userid, $floorid, $status){
		$link = Link::get_link('mastercommand');
		
		if(empty($userid)){
			$userid = $this->getId();
		}
		$list = array();
		
		$sql = 'SELECT floor_id, floor_order
		        FROM user_floor
		        WHERE floor_id=:floor_id AND user_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if(!empty($do)){
			if($status == 1){
				if($do->floor_order == 0){
					$sql = 'SELECT floor_order
					        FROM user_floor
					        WHERE user_id=:user_id
					        ORDER BY floor_order DESC
					        LIMIT 1';
					$req = $link->prepare($sql);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
					$do2 = $req->fetch(PDO::FETCH_OBJ);
		
					$sql = 'UPDATE user_floor
				 	        SET floor_order = :order
					        WHERE user_id=:user_id AND floor_id=:floor_id';
					$req = $link->prepare($sql);
					$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
					$req->bindValue(':order', $do2->floor_order + 1, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
			else {
				$sql = 'UPDATE user_floor
				        SET floor_order = 0
				        WHERE user_id=:user_id AND floor_id=:floor_id';
				$req = $link->prepare($sql);
				$req->bindValue(':user_id',   $userid,   PDO::PARAM_INT);
				$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
				
				if($do->floor_order > 0){
					$sql = 'UPDATE user_floor
					        SET floor_order= floor_order - 1
					        WHERE user_id=:user_id AND floor_order > :floor_order';
					$req = $link->prepare($sql);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->bindValue(':floor_order', $do->floor_order, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
		}
	}
	
	function confUserDeviceEnable($userid){
		return null;
	}
	
	function confUserPermissionDevice($iduser, $deviceid, $status){
		return null;
	}
	
	function confUserPermissionRoom($iduser, $roomid, $status){
		return null;
	}
	
	function confUserPermissionFloor($iduser, $floorid, $status){
		return null;
	}
	
	/*** User customisation ***/
	
	function confUserDeviceBgimg($iddevice, $bgimg, $userid=0){
		$userid = $this->getId();

		$link = Link::get_link('mastercommand');
		
		$sql = 'UPDATE user_device
		        SET device_bgimg=:bgimg
		        WHERE user_id=:user_id AND room_device_id=:iddevice';
		$req = $link->prepare($sql);
		$req->bindValue(':bgimg', $bgimg, PDO::PARAM_STR);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
	}
	
	/*** Smartcommand ***/
	
	function searchSmartcmdByName($smartcmd_name){
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT smartcommand_id, user_id
				FROM smartcommand_list
				WHERE name=:smartcmd_name';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_name', $smartcmd_name, PDO::PARAM_STR);
		
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		if($do->user_id != $this->getId()) {
			return 0;
		}
		return $do->smartcommand_id;
	}
	
	function searchSmartcmdById($smartcmd_id){
		$link = Link::get_link('mastercommand');
	
		$sql = 'SELECT name, user_id
				FROM smartcommand_list
				WHERE smartcommand_id=:smartcmd_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		if($do->user_id != $this->getId()) {
			return 0;
		}
		return $do->name;
	}
	
	function countElemSmartcmd($idsmartcmd) {
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT COUNT(smartcommand_id) AS nb
				FROM smartcommand
				WHERE smartcommand_id=:smartcommand_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcommand_id', $idsmartcmd, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		return $do->nb;
	}
	
	function listSmartcmd(){
		$link = Link::get_link('mastercommand');
	
		$sql = 'SELECT smartcommand_id, name
				FROM smartcommand_list
				WHERE user_id=:user_id
				ORDER BY name';
		
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->smartcommand_id] = array(
					'smartcommand_id'     => $do->smartcommand_id,
					'name'                => $do->name
			);
		}
		
		return $list;
	}
	
	function getSmartcmdElems($id_smartcmd) {
		$link = Link::get_link('mastercommand');
		$red = 0;
		$green = 0;
		$blue = 0;
		$exec_id = 0;
		
		$sql = 'SELECT exec_id, smartcommand.room_device_id AS room_device_id,
				       optiondef.option_id, option_value, time_lapse,
				       room_device.name AS device_name, room_device.device_id AS device_id,
				       optiondef.namefr AS option_name
				FROM smartcommand
				JOIN room_device ON room_device.room_device_id = smartcommand.room_device_id
				JOIN optiondef ON optiondef.option_id = smartcommand.option_id
				WHERE smartcommand_id=:smartcmd_id
				ORDER BY exec_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $id_smartcmd, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$list ='';
		
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			
			$list[$do->exec_id] = array(
					'smartcmd_id'       => $id_smartcmd,
					'exec_id'           => $do->exec_id,
					'room_device_id'    => $do->room_device_id,
					'option_id'         => $do->option_id,
					'option_value'      => $do->option_value,
					'time_lapse'        => $do->time_lapse,
					'device_name'       => $do->device_name,
					'device_id'         => $do->device_id,
					'option_name'       => $do->option_name
			);
			
			if ($do->option_id == 392 && empty($red)) {
				$red = $do->option_value;
				$exec_id = $do->exec_id;
			}
			if ($do->option_id == 393 && empty($green)) {
				$green = $do->option_value;
				$exec_id = $do->exec_id;
			}
			if ($do->option_id == 394 && empty($blue)) {
				$blue = $do->option_value;
				$exec_id = $do->exec_id;
			}
			
			if (!empty($red) && !empty($green) && !empty($blue) && !empty($exec_id)) {
				$hexa_color = convertRGBToHexa($red, $green, $blue);
				$red = 0;
				$green = 0;
				$blue = 0;
				$list[$exec_id]['option_value'] = $hexa_color;
			}
			
		}
		return $list;
	}
	
	function createNewSmartcmd($smartcmd_name){
	
		if ($this->searchSmartcmdByName($smartcmd_name) != 0) {
			return -1;
		}
		$link = Link::get_link('mastercommand');
		
		$sql = 'INSERT INTO smartcommand_list
		        (name, user_id)
				VALUES
				(:smartcmd_name, :user_id)';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_name', $smartcmd_name, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		return $link->lastInsertId();
	}
	
	function updateSmartcmdName($smartcmd_id, $smartcmd_name){
		if ($this->searchSmartcmdByName($smartcmd_name) != 0) {
			return -1;
		}
		$link = Link::get_link('mastercommand');
	
		$sql = 'UPDATE smartcommand_list
				SET name=:smartcmd_name
				WHERE smartcommand_id=:smartcmd_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_name', $smartcmd_name, PDO::PARAM_STR);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));

		return $smartcmd_id;
	}
	
	function saveNewElemSmartcmd($idsmartcmd, $idexec, $iddevice, $idoption, $valoption, $timelapse, $no_update = 0){
		$link = Link::get_link('mastercommand');
		
		if ($no_update == 0) {
			$sql = 'UPDATE smartcommand
					SET exec_id=exec_id+1
					WHERE smartcommand_id=:smartcommand_id AND exec_id >= :idexec';
		
			$req = $link->prepare($sql);
			$req->bindValue(':idexec', $idexec, PDO::PARAM_INT);
			$req->bindValue(':smartcommand_id', $idsmartcmd, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		$sql = 'INSERT INTO smartcommand
				VALUES
				(:smartcommand_id, :exec_id, :room_device_id, :option_id, :option_value, :time_lapse)';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcommand_id', $idsmartcmd, PDO::PARAM_INT);
		$req->bindValue(':exec_id', $idexec, PDO::PARAM_INT);
		$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':option_id', $idoption, PDO::PARAM_INT);
		$req->bindValue(':option_value', $valoption, PDO::PARAM_STR);
		$req->bindValue(':time_lapse', $timelapse, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function updateSmartcmdElemOptionValue($idsmartcmd, $idexec, $optionval, $id_option) {
		$link = Link::get_link('mastercommand');

		$sql = 'UPDATE smartcommand
				SET option_value=:option_val
				WHERE smartcommand_id=:smartcmd_id AND exec_id=:exec_id AND option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':option_val', $optionval, PDO::PARAM_STR);
		$req->bindValue(':smartcmd_id', $idsmartcmd, PDO::PARAM_INT);
		$req->bindValue(':exec_id', $idexec, PDO::PARAM_INT);
		$req->bindValue(':option_id', $id_option, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function smartcmdChangeElemsOrder($smartcmd_id, $old_exec_id, $new_exec_id) {
		$link = Link::get_link('mastercommand');
		
		if ($old_exec_id == $new_exec_id) {
			return;
		}
		
		if ($new_exec_id > $old_exec_id) {
			$sql = 'UPDATE smartcommand
					SET exec_id=exec_id-1
					WHERE smartcommand_id=:smartcommand_id AND exec_id <= :idexec';

			$req = $link->prepare($sql);
			$req->bindValue(':smartcommand_id', $smartcmd_id, PDO::PARAM_INT);
			$req->bindValue(':idexec', $new_exec_id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			
			$sql = 'UPDATE smartcommand
					SET exec_id=:new_exec_id
					WHERE smartcommand_id=:smartcmd_id AND exec_id=:old_exec_id-1';
			$req = $link->prepare($sql);
			$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
			$req->bindValue(':old_exec_id', $old_exec_id, PDO::PARAM_INT);
			$req->bindValue(':new_exec_id', $new_exec_id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			
			$sql = 'UPDATE smartcommand
					SET exec_id=exec_id+1
					WHERE smartcommand_id=:smartcommand_id AND exec_id < :idexec';
			
			$req = $link->prepare($sql);
			$req->bindValue(':idexec', $old_exec_id, PDO::PARAM_INT);
			$req->bindValue(':smartcommand_id', $smartcmd_id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		else {
			$sql = 'UPDATE smartcommand
					SET exec_id=exec_id+1
					WHERE smartcommand_id=:smartcommand_id AND exec_id >= :idexec';
			
			$req = $link->prepare($sql);
			$req->bindValue(':smartcommand_id', $smartcmd_id, PDO::PARAM_INT);
			$req->bindValue(':idexec', $new_exec_id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			
			$sql = 'UPDATE smartcommand
					SET exec_id=:new_exec_id
					WHERE smartcommand_id=:smartcmd_id AND exec_id=:old_exec_id+1';
			$req = $link->prepare($sql);
			$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
			$req->bindValue(':old_exec_id', $old_exec_id, PDO::PARAM_INT);
			$req->bindValue(':new_exec_id', $new_exec_id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			
			$sql = 'UPDATE smartcommand
					SET exec_id=exec_id-1
					WHERE smartcommand_id=:smartcommand_id AND exec_id > :idexec';
			
			$req = $link->prepare($sql);
			$req->bindValue(':idexec', $old_exec_id, PDO::PARAM_INT);
			$req->bindValue(':smartcommand_id', $smartcmd_id, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		
	}
	
	function removeSmartcmd($smartcmd_id) {
		$link = Link::get_link('mastercommand');
	
		$sql = 'DELETE FROM smartcommand_list
				WHERE smartcommand_id=:smartcmd_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function removeSmartcmdElem($smartcmd_id, $exec_id) {
		$link = Link::get_link('mastercommand');
	
		$sql = 'DELETE FROM smartcommand
				WHERE smartcommand_id=:smartcmd_id AND exec_id=:exec_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->bindValue(':exec_id', $exec_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	
		$sql = 'UPDATE smartcommand
				SET exec_id=exec_id-1
				WHERE smartcommand_id=:smartcmd_id AND exec_id > :exec_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->bindValue(':exec_id', $exec_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function smartcmdUpdateDelay($smartcmd_id, $exec_id, $delay) {
		$link = Link::get_link('mastercommand');
		
		$sql = 'UPDATE smartcommand
				SET time_lapse=:delay
				WHERE smartcommand_id=:smartcmd_id AND exec_id=:exec_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->bindValue(':exec_id', $exec_id, PDO::PARAM_INT);
		$req->bindValue(':delay', $delay, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	function smartcmdSaveLinkedRoom($smartcmd_id, $room_id) {
		$link = Link::get_link('mastercommand');
	
		if ($room_id == 0) {
			$room_id = NULL;
		}
		$sql = 'UPDATE smartcommand_list
				SET room_id=:room_id
				WHERE smartcommand_id=:smartcmd_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_id', $room_id, PDO::PARAM_INT);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/*** KNX action ***/
	
	function knx_write_l($daemon, $addr, $value=0){
		return null;
	}
	
	function knx_write_s($daemon, $addr, $value=0){
		return null;
	}
	
	function knx_read($daemon, $addr){
		return null;	
	}
	
	/*** KNX log ***/
	
	function confKnxAddrList(){
		return null;
	}
	

	/*** Backup Database ***/
	
	function confDbListLocal(){
		return NULL;
	}
	
	function confDbCreateLocal(){}
	
	function confDbRemoveUsb($filename){
		return NULL;
	}
	
	function confDbRestoreUsb($filename){
		return NULL;
	}
	
	function confDbCheckUsb(){
		return NULL;
	}
	
	function confDbListUsb(){
		return NULL;
	}
	
	function confDbCreateUsb(){}
	
	function confDbRemoveLocal($filename){}
	
	function confDbRestoreLocal($filename){}
	
	/*** Optiondef ***/
	
	function confOptionList(){
		return null;
	}
	
	/*** Master command ***/
	
	/**
	 * 
	 * @return Ambigous <multitype:multitype:multitype: NULL  , multitype:NULL >
	 */
	function mcDeviceAll(){
		$link = Link::get_link('mastercommand');
		$list = array();
		
		$sql = 'SELECT room_device_id, room_device.protocol_id, room_id, 
		               room_device.device_id, room_device.name, addr, plus1, 
		               plus2, plus3, device.application_id
		        FROM room_device
		        JOIN device ON room_device.device_id = device.device_id
		        ORDER BY name';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->room_device_id] = array(
				'application_id'=> $do->application_id,
				'room_device_id'=> $do->room_device_id,
				'room_id'       => $do->room_id,
				'device_id'     => $do->device_id,
				'protocol_id'   => $do->protocol_id,
				'name'          => $do->name,
				'addr'          => $do->addr,
				'plus1'         => $do->plus1,
				'plus2'         => $do->plus2,
				'plus3'         => $do->plus3,
				'device_opt'    => array()
			);
		}
		
		$sql = 'SELECT room_device.room_device_id, room_device.room_id, 
		               optiondef.hidden_arg, room_device.device_id, 
		               optiondef.option_id, room_device_option.addr,
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name,
		               room_device_option.addr_plus, room_device_option.valeur
		        FROM room_device
		        JOIN room_device_option ON room_device_option.room_device_id = room_device.room_device_id
		        JOIN optiondef ON room_device_option.option_id = optiondef.option_id
		        WHERE room_device_option.status = 1';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if($do->hidden_arg & 4) {
				$list[$do->room_device_id]['device_opt'][$do->option_id] = array(
					'option_id'=> $do->option_id,
					'name'     => $do->name,
					'addr'     => $do->addr,
					'addr_plus'=> $do->addr_plus,
					'valeur'   => $do->valeur
				);
			}
		}
		return $list;
	}
	
	/**
	 * 
	 * @param unknown $roomdeviceid
	 * @return NULL|Ambigous <multitype:NULL , multitype:multitype: NULL >
	 */
	function mcDeviceInfo($roomdeviceid){
		$link = Link::get_link('mastercommand');
		
		if(empty($roomdeviceid)){
			return null;
		}
		$sql = 'SELECT room_device_id, room_device.protocol_id, room_id, 
		               room_device.device_id, room_device.name, addr, plus1, 
		               plus2, plus3, device.application_id
		        FROM room_device
		        JOIN device ON room_device.device_id = device.device_id
		        WHERE room_device_id=:room_device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $roomdeviceid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if(empty($do)) {
			return null;
		}
		
		$info = array(
				'application_id'=> $do->application_id,
				'room_device_id'=> $do->room_device_id,
				'room_id'       => $do->room_id,
				'device_id'     => $do->device_id,
				'protocol_id'   => $do->protocol_id,
				'name'          => $do->name,
				'addr'          => $do->addr,
				'port'          => $do->plus1,
				'login'         => $do->plus2,
				'mdp'           => $do->plus3,
				'device_opt'    => array()
		);
		$sql = 'SELECT room_device.room_device_id, room_device.room_id, 
		               optiondef.hidden_arg, room_device.device_id, 
		               optiondef.option_id, room_device_option.addr,
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name,
		               room_device_option.addr_plus, room_device_option.valeur
		        FROM room_device
		        JOIN room_device_option ON room_device_option.room_device_id = room_device.room_device_id
		        JOIN optiondef ON room_device_option.option_id = optiondef.option_id
		        WHERE room_device.room_device_id=:room_device_id AND 
		              room_device_option.status = 1';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $roomdeviceid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if($do->hidden_arg & 4) {
				$info['device_opt'][$do->option_id] = array(
					'option_id' => $do->option_id,
					'name'      => $do->name,
					'addr'      => $do->addr,
					'addr_plus' => $do->addr_plus,
					'valeur'    => $do->valeur
				);
			}
		}
		return $info;
	}
	
	/**
	 * 
	 * @param unknown $iddevice
	 * @return boolean
	 */
	function checkDevice($iddevice){
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT device_allowed
		        FROM user_device
		        WHERE user_id=:user_id AND room_device_id=:iddevice';
		$req = $link->prepare($sql);
		$req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if(empty($do->device_allowed)) {
			return false;
		}
		
		return true;
	}
	
	/**
	 * 
	 * @param unknown $iddevice
	 * @param unknown $value
	 * @param unknown $optionid
	 */
	function mcAction($iddevice, $value, $optionid){
		$link = Link::get_link('mastercommand');
		
		$sql = 'SELECT device_allowed, room_device_option.addr_plus
		        FROM user_device
		        JOIN room_device_option ON user_device.room_device_id=room_device_option.room_device_id
		        WHERE user_id=:user_id AND 
		              user_device.room_device_id=:room_device_id AND 
		              option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':option_id', $optionid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		
		$do = $req->fetch(PDO::FETCH_OBJ);
		
		if(!empty($do->device_allowed)){
			if(!empty($do->addr_plus)){
				$sql ='UPDATE room_device_option
				       SET valeur=:valeur
				       WHERE room_device_id=:room_device_id AND 
				             option_id=:option_id';
				$req = $link->prepare($sql);
				$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
				$req->bindValue(':valeur', $value, PDO::PARAM_INT);
				$req->bindValue(':option_id', $optionid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
			$socket = new Socket();
			$data = array(
				'room_device_id'=> $iddevice,
				'value'         => $value,
				'option_id'     => $optionid
			);
			error_log(serialize($data));
			$socket->send('send_to_device', $data);
		}
	}

	/**
	 * 
	 * @param unknown $iddevice
	 * @param unknown $val
	 * @param unknown $optionid
	 */
	function mcAudio($iddevice, $val, $optionid){
		if($this->checkDevice($iddevice)){
			$socket = new Socket();
			$data = array(
				'room_device_id' => $iddevice,
				'option_id'      => $optionid,
				'action'         => $val
			);
			$socket->send('send_to_device', $data);
		}
	}
	
	/**
	 * 
	 * @return Ambigous <multitype:, multitype:NULL >
	 */
	function mcReturn(){
		$link = Link::get_link('mastercommand');
		$list = Array();
		
		$sql = 'SELECT room_device_option.room_device_id, option_id, valeur, addr_plus, room_device.device_id
		        FROM   room_device_option
		        JOIN   room_device ON room_device_option.room_device_id=room_device.room_device_id';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->device_id][$do->room_device_id][$do->option_id] = array(
				'device_id'     => $do->device_id,
				'addr_plus'     => $do->addr_plus,
				'room_device_id'=> $do->room_device_id,
				'option_id'     => $do->option_id,
				'valeur'        => $do->valeur
			);
		}
		return $list;
	}
	
	/**
	 * 
	 * @param unknown $iddevice
	 * @param unknown $optionid
	 * @param unknown $val
	 */
	function mcTemperature($iddevice, $optionid, $val){
		$socket = new Socket();
		$data = array(
				'room_device_id' => $iddevice,
				'option_id'      => $optionid,
				'value'          => $val
		);
		$socket->send('send_to_device', $data);
	}
	
	function mcSmartcmd($smartcmd_id){
		error_log("SEND SOCKET");
		$socket = new Socket();
		$socket->send('smartcmd_launch', $smartcmd_id);
	}
}

?>