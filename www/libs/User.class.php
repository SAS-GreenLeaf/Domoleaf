<?php 

/**
 * User class
 * @author virgil
 */
class User {
	private $id;
	private $level;
	private $language;
	
	/*** Get/Set ***/
	
	/**
	 * Constructor
	 * @param int $userId user id
	 */
	function __construct($userId) {
		$this->id = $userId;
	}
	
	/**
	 * Get user id
	 */
	function getId() {
		return $this->id;
	}
	
	/**
	 * Get user level
	 */
	function getLevel() {
		return $this->level;
	}
	
	/**
	 * Set user level
	 * @param int $level user level
	 */
	function setLevel($level) {
		$this->level = $level;
	}
	
	/**
	 * Get user language
	 * @return string language, EN is the default language
	 */
	function getLanguage() {
		if ($this->language != 'en') {
			return $this->language;
		}
		return '';
	}
	
	/**
	 * Set user language
	 * @param string $language user language
	 */
	function setLanguage($language) {
		$this->language = $language;
	}
	
	/**
	 * Update user's activity
	 */
	function activity() {
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE mcuser
		        SET activity= :activity
		        WHERE mcuser_id= :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':activity', $_SERVER['REQUEST_TIME'], PDO::PARAM_INT);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/*** Disconnect ***/
	
	/**
	 * Disconnect user
	 * @param string $token user's token
	 */
	function disconnect($token) {
		$link = Link::get_link('domoleaf');
		$sql = 'DELETE FROM mcuser_token
		        WHERE token= :token';
		$req = $link->prepare($sql);
		$req->bindValue(':token', $token, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/********************** Domotic configuration **********************/
	/**
	 * Load the D3 configuration from the Database
	 */
	function conf_load(){
		$link = Link::get_link('domoleaf');
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
	
	/**
	 * List all daemons/box
	 */
	function confDaemonList() {
		return null;
	}
	
	/**
	 * Add Daemon information of a new Box
	 * @param string $name Box name
	 * @param string $serial Box serial number
	 * @param string $skey Box AES key
	 * @return number Box id
	 */
	function confDaemonNew($name, $serial, $skey) {
		return null;
	}
	
	/**
	 * Modify wifi configuration
	 * @param int $daemon_id daemon id
	 * @param string $ssid SSID
	 * @param string $password password
	 * @param int $security 1 WEP, 2 WPA, 3 WPA2
	 * @param int $mode 0 disabled, 1 Client, 2 AP
	 */
	function confSaveWifi($daemon_id, $ssid, $password, $security = 3, $mode = 0){
		return null;
	}
	
	/**
	 * Delete a Box
	 * @param int $id Box id
	 */
	function confDaemonRemove($id) {
		return null;
	}
	
	/**
	 * Configure basic daemon information
	 * @param int $id Box/Daemon id
	 * @param string $name Box/Daemon name
	 * @param string $serial Box/Daemon hostname
	 * @param string $skey Box/Daemon secret AES key
	 */
	function confDaemonRename($id, $name, $serial, $skey='') {
		return null;
	}
	
	/**
	 * List protocols who require a specific daemon
	 */
	function confDaemonProtocolList() {
		return null;
	}
	
	/**
	 * Configure Interface on Slave Daemon
	 * @param int $daemon Box/Daemon id
	 * @param array $newProtocolList used protocols
	 * @param string $interface_knx KNX interface type
	 * @param string $interface_knx_arg KNX interface name
	 * @param number $daemon_knx KNX Programmation mode
	 * @param string $interface_EnOcean EnOcean interface type
	 * @param string $interface_EnOcean_arg EnOcean interface name
	 */
	function confDaemonProtocol($daemon, $newProtocolList=array(), $interface_knx='tpuarts', $interface_knx_arg='ttyAMA0', $daemon_knx=1, $interface_EnOcean='usb', $interface_EnOcean_arg='ttyUSB0') {
		return null;
	}
	
	/**
	 * Count configured daemon by protocol
	 */
	function confMenuProtocol() {
		return null;
	}
	
	/**
	 * Get information to draw a graphic
	 * @param int $id_device project device id
	 * @param int $id_option option id
	 * @param int $from begining date
	 * @param int $to ending date
	 */
	function getGraphicsInfo($id_device, $id_option, $from, $to) {
		$list = array(
			'config' => array(),
			'datas'  => array(),
			'infos'  => array()
		);
		if (empty($id_device)) {
			return $list;
		}
		if (empty($id_option)) {
			return $list;
		}
		if (empty($from)) {
			$from = date('Y-m-d');
		}
		if (empty($to)) {
			$to = date('Y-m-d');
		}
		$link = Link::get_link('domoleaf');
		$functions = array(
			1 => 'convert_temperature',
			2 => 'convert_hundred',
			3 => 'convert_float32'
		);
		if ($id_option == 399) {
			$sql = 'SELECT configuration_id, configuration_value 
			        FROM configuration 
			        WHERE configuration_id BETWEEN 14 AND 18';
			$req = $link->prepare($sql);
			$req->execute() or die (error_log('error : '.serialize($req->errorInfo())));
			while($do = $req->fetch(PDO::FETCH_OBJ)) {
				$list['config'][$do->configuration_id] = $do->configuration_value;
			}
			$high_cost = $list['config'][14];
			$low_cost = $list['config'][15];
			$time_slot_a = explode('-', $list['config'][16]);
			$time_slot_b = explode('-', $list['config'][17]);
		}
		$sql = 'SELECT if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name,
		               dpt.unit, room_device.protocol_id, room_device.daemon_id, room_device_option.addr,
		               function_answer
		        FROM room_device_option 
		        JOIN room_device ON room_device.room_device_id=room_device_option.room_device_id
		        JOIN dpt ON room_device_option.dpt_id = dpt.dpt_id 
		        JOIN optiondef ON room_device_option.option_id = optiondef.option_id 
		        JOIN dpt_optiondef ON dpt_optiondef.option_id= optiondef.option_id AND 
		                              dpt_optiondef.dpt_id=dpt.dpt_id AND 
		                              dpt_optiondef.protocol_id=room_device.protocol_id
		        WHERE room_device_option.room_device_id = :device 
		        AND room_device_option.option_id = :option';
		$req = $link->prepare($sql);
		$req->bindValue(':device', $id_device, PDO::PARAM_INT);
		$req->bindValue(':option', $id_option, PDO::PARAM_INT);
		$req->execute() or die (error_log('error : '.serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do)) {
			return $list;
		}
		$list['infos'] = clone $do;
		if($list['infos']->function_answer > 0) {
			$fct_current = $functions[$list['infos']->function_answer];
		}
		$from_timestamp = strtotime($from);
		$to_timestamp = strtotime($to) + 86399;
		$sql = 'SELECT graphic_log.date as date_time, graphic_log.value, 0 as price
		        FROM graphic_log 
		        WHERE graphic_log.room_device_id = :device 
		        AND graphic_log.option_id = :option 
		        AND graphic_log.date BETWEEN :from_date AND :to_date 
		        ORDER BY graphic_log.date';
		$req = $link->prepare($sql);
		$req->bindValue(':device', $id_device, PDO::PARAM_INT);
		$req->bindValue(':option', $id_option, PDO::PARAM_INT);
		$req->bindValue(':from_date', $from_timestamp, PDO::PARAM_INT);
		$req->bindValue(':to_date', $to_timestamp, PDO::PARAM_INT);
		$req->execute() or die (error_log('error : '.serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if (!empty($fct_current)) {
				$do->value = $fct_current($do->value);
			}
			if($id_option == 399) {
				if (!empty($prev_do)) {
					$inter = $do->date_time - $prev_do->date_time;
					if ($inter > 0) {
						$res = ($prev_do->value * $inter) / 3600;
						$h = date('h', $prev_do->date_time);
						if (($time_slot_a[0] < $time_slot_a[1] && $h >= $time_slot_a[0] && $h < $time_slot_a[1]) ||
							($time_slot_a[0] > $time_slot_a[1] && ($h >= $time_slot_a[0] || $h < $time_slot_a[1])) ||
							($time_slot_b[0] < $time_slot_b[1] && $h >= $time_slot_b[0] && $h < $time_slot_b[1]) ||
							($time_slot_b[0] > $time_slot_b[1] && ($h >= $time_slot_b[0] || $h < $time_slot_b[1]))) {
							$cost = $res * $low_cost;
						}
						else {
							$cost = $res * $high_cost;
						}
						$do->price = $prev_do->price + $cost;
					}
				}
				$prev_do = clone $do;
			}

			$list['datas'][] = clone $do;
		}
		//KNX
		if ($list['infos']->protocol_id == 1) {
			$sql = 'SELECT t_date as date_time, knx_value as value, 0 as price
			        FROM knx_log 
			        WHERE knx_log.daemon_id = :daemon 
			        AND knx_log.t_date < :date_limit
			        AND addr_dest=:addr
			        ORDER BY t_date ASC';
			$req = $link->prepare($sql);
			$req->bindValue(':daemon', $list['infos']->daemon_id, PDO::PARAM_INT);
			$req->bindValue(':date_limit', $to_timestamp, PDO::PARAM_INT);
			$req->bindValue(':addr', $list['infos']->addr, PDO::PARAM_STR);
			$req->execute() or die (error_log('error : '.serialize($req->errorInfo())));
			while($do = $req->fetch(PDO::FETCH_OBJ)) {
				if (!empty($fct_current)) {
					$do->value = $fct_current($do->value);
				}
				if($id_option == 399) {
					if (!empty($prev_do)) {
						$inter = $do->date_time - $prev_do->date_time;
						if ($inter > 0) {
							$res = ($prev_do->value * $inter) / 3600;
							$h = date('h', $prev_do->date_time);
							if (($time_slot_a[0] < $time_slot_a[1] && $h >= $time_slot_a[0] && $h < $time_slot_a[1]) ||
								($time_slot_a[0] > $time_slot_a[1] && ($h >= $time_slot_a[0] || $h < $time_slot_a[1])) ||
								($time_slot_b[0] < $time_slot_b[1] && $h >= $time_slot_b[0] && $h < $time_slot_b[1]) ||
								($time_slot_b[0] > $time_slot_b[1] && ($h >= $time_slot_b[0] || $h < $time_slot_b[1]))) {
								$cost = $res * $low_cost;
							}
							else {
								$cost = $res * $high_cost;
							}
							$do->price = $prev_do->price + $cost;
						}
					}
					$prev_do = clone $do;
				}
				$list['datas'][] = clone $do;
			}
		}
		return $list;
	}
	
	/**
	 * Modify Box date
	 * @param int $day day
	 * @param int $month month
	 * @param int $year year
	 * @param int $hour hour
	 * @param int $minute minute
	 */
	function confDateTime($day, $month, $year, $hour, $minute) {
		return null;
	}

	/*** Floors ***/
	/**
	 * List floors
	 */
	function confFloorList() {
		return null;
	}
	
	/**
	 * Create a new floor and its first room
	 * @param string $namefloor floor name
	 * @param string $nameroom room name
	 */
	function confFloorNew($namefloor, $nameroom = '') {
		return null;
	}
	
	/**
	 * Delete a floor
	 * @param int $idfloor floor id
	 */
	function confFloorRemove($idfloor) {
		return null;
	}
	
	/*** Rooms ***/
	
	/**
	 * List rooms for a floor, list all rooms by default
	 * @param number $floor floor id
	 */
	function confRoomList($floor) {
		return null;
	}
	
	/**
	 * Create a new room
	 * @param string $name room name
	 * @param int $floor floor id
	 */
	function confRoomNew($name, $floor) {
		return null;
	}
	
	/**
	 * Rename a room
	 * @param int $id room id
	 * @param string $name new room name
	 */
	function confRoomRename($id, $name) {
		return null;
	}
	
	/**
	 * Attribute a room to a floor
	 * @param int $id room id
	 * @param int $floor floor id
	 */
	function confRoomFloor($id, $floor) {
		return null;
	}
	
	/**
	 * Delete a room
	 * @param int $idroom room id
	 * @param int $idfloor floor id
	 */
	function confRoomRemove($idroom, $idfloor) {
		return null;
	}
	
	/*** Devices ***/
	
	/**
	 * Delete a device
	 * @param int $iddevice device id
	 * @param int $idroom room id
	 */
	function confRoomDeviceRemove($iddevice, $idroom) {
		return null;
	}
	
	/**
	 * Return basic information for a device
	 * @param int $iddevice project device id
	 */
	function confRoomDeviceInfo($iddevice){
		return null;
	}
	
	/**
	 * Save all device information
	 * @param int $idroom room id
	 * @param string $name device name
	 * @param int $daemon daemon id
	 * @param string $devaddr device address
	 * @param int $iddevice device id
	 * @param int $port device port
	 * @param string $login login
	 * @param string $pass password
	 * @param string $macaddr physical address
	 * @param string $password widget's password
	 * @return NULL
	 */
	function confDeviceSaveInfo($idroom, $name, $daemon=0, $devaddr, $iddevice, $port='', $login='', $pass='', $macaddr='', $password=''){
		return null;
	}
	
	/**
	 * Save device options
	 * @param int $room_device_id device id
	 * @param array $options option information
	 * @return NULL
	 */
	function confDeviceSaveOption($room_device_id, $options){
		return null;
	}

	/**
	 * Return all options from a device
	 * @param int $deviceroomid device id
	 * @return all options
	 */
	function confDeviceRoomOpt($deviceroomid) {
		$link = Link::get_link('domoleaf');
		$list = array();
		$sql = 'SELECT room_device_option.option_id, room_device_option.addr, 
		               addr_plus, room_device_option.dpt_id, status, opt_value, 
		               function_writing,
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name
		        FROM room_device_option
		        JOIN room_device ON room_device_option.room_device_id = room_device.room_device_id
		        JOIN optiondef ON room_device_option.option_id = optiondef.option_id
		        JOIN dpt_optiondef ON dpt_optiondef.option_id=optiondef.option_id AND dpt_optiondef.protocol_id=room_device.protocol_id AND dpt_optiondef.dpt_id=room_device_option.dpt_id
		        WHERE room_device_option.room_device_id=:room_device_id AND status=1
		        ORDER BY room_device_option.option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id',  $deviceroomid,  PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->option_id] = clone $do;
		}
		return $list;
	}
	
	/**
	 * List devices, all devices by default
	 * @param int $room room id
	 */
	function confRoomDeviceList($room){
		return null;
	}
	
	/**
	 * List all available protocol for a device
	 * @param int $device device id
	 */
	function confDeviceProtocol($device=0) {
		return null;
	}
	
	/**
	 * Add a new IP device
	 * @param string $name device name
	 * @param int $proto protocol id
	 * @param int $room room id
	 * @param int $device device id
	 * @param string $addr IP address
	 * @param int $port port
	 * @param string $login login
	 * @param string $pass password
	 * @param string $macaddr physical address
	 */
	function confDeviceNewIp($name, $proto, $room, $device, $addr, $port='80', $login='', $pass='', $macaddr=''){
		return null;
	}
	
	/**
	 * Add a new KNX device
	 * @param string $name device name
	 * @param int $proto protocol id
	 * @param int $room room id
	 * @param int $device device id
	 * @param string $addr physical address
	 * @param int $daemon daemon id
	 */
	function confDeviceNewKnx($name, $proto, $room, $device, $addr, $daemon){
		return null;
	}
	
	/**
	 * Add a new EnOcean device
	 * @param string $name device name
	 * @param int $proto protocol id
	 * @param int $room room id
	 * @param int $device device id
	 * @param string $addr physical address
	 * @param int $daemon daemon id
	 */
	function confDeviceNewEnocean($name, $proto, $room, $device, $addr, $daemon){
		return null;
	}
	
	/********************** Domotic definition **********************/
	
	/**
	 * Return all applications
	 * @return array : application list
	 */
	function confApplicationAll() {
		$link = Link::get_link('domoleaf');
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
		$link = Link::get_link('domoleaf');
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
		$link = Link::get_link('domoleaf');
		$list = array();
		$sql = 'SELECT device_id, protocol_id, application_id,
		               if(name'.$this->getLanguage().' = "", name, name'.$this->getLanguage().') as name
		        FROM device
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
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name
		        FROM device_option
		        JOIN optiondef ON optiondef.option_id=device_option.option_id';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if(!empty($list[$do->device_id])) {
				$list[$do->device_id]['protocol_option'][$do->option_id] = clone $do;
			}
		}
		return $list;
	}
	
	/********************** Users configuration **********************/
	
	/**
	 * List all users
	 */
	function profileList() { return null; }
	
	/**
	 * Get user information
	 * @param int $id not used
	 * @return object user information
	 */
	function profileInfo($id=0) {
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT mcuser_id, username, mcuser_mail, lastname, firstname,
		               gender, phone, language, timezone, design, bg_color, border_color
		        FROM mcuser
		        WHERE mcuser_id= :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		return $do;
	}
	
	/**
	 * Add a new user
	 * @param string $username user name
	 * @param string $password password
	 */
	function profileNew($username, $password) {
		return null;
	}
	
	/**
	 * Delete an user
	 * @param int $user_id user id
	 */
	function profileRemove($user_id) {
		return 0;
	}
	
	/**
	 * Modify user information
	 * @param string $lastname lastname
	 * @param string $firstname firstname
	 * @param int $gender gender 0 or 1
	 * @param string $email email
	 * @param string $phone phone number
	 * @param string $language language
	 * @param int $timeZone timeZone
	 * @param int $id user id, not used
	 */
	function profileRename($lastname, $firstname, $gender, $email, $phone, $language, $timeZone, $id=0) {
		$link = Link::get_link('domoleaf');
		if ($gender != 1) {
			$gender = 0;
		}
		$langList = Guest::language();
		if(empty($langList[$language])) {
			$language = $this->getLanguage();
		}
		if(empty($timeZone) || !($timeZone > 0 && $timeZone < 42)) {
			$timeZone = 1;
		}
		$sql = 'UPDATE mcuser
		        SET lastname= :lastname,
		            firstname= :firstname,
		            gender= :gender,
		            mcuser_mail= :email,
		            phone= :phone,
		            language= :language,
		            timezone= :timezone
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':lastname', $lastname, PDO::PARAM_STR);
		$req->bindValue(':firstname', $firstname, PDO::PARAM_STR);
		$req->bindValue(':gender', $gender, PDO::PARAM_INT);
		$req->bindValue(':email', $email, PDO::PARAM_STR);
		$req->bindValue(':phone', $phone, PDO::PARAM_STR);
		$req->bindValue(':language', $language, PDO::PARAM_STR);
		$req->bindValue(':timezone', $timeZone, PDO::PARAM_INT);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/**
	 * Modify user level
	 * @param int $id user id
	 * @param int $level user level
	 */
	function profileLevel($id, $level) {
		return null;
	}
	
	/**
	 * Rename an user
	 * @param int $id user id
	 * @param string $username new user name
	 */
	function profileUsername($id, $username) {
		return null;
	}
	
	/**
	 * Modify user password
	 * @param string $last old password
	 * @param string $new new password
	 * @param int $id user id (not used)
	 */
	function profilePassword($last, $new, $id=0) {
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT mcuser_id, mcuser_password
		        FROM mcuser
		        WHERE mcuser_id= :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		if($do->mcuser_password == hash('sha256', $do->mcuser_id.'_'.$last)) {
			$sql = 'UPDATE mcuser
			        SET mcuser_password=:user_password
			        WHERE mcuser_id=:user_id';
			$req = $link->prepare($sql);
			$req->bindValue(':user_password', hash('sha256', $do->mcuser_id.'_'.$new), PDO::PARAM_STR);
			$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
	}
	
	/**
	 * List available time zone
	 */
	function profileTime() {
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT timezone
		        FROM mcuser
		        WHERE mcuser_id= :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		$allTimeZone = array(
				2	=>	-46800,
				3	=>	-43200,
				4	=>	-39600,
				5	=>	-37800,
				6	=>	-36000,
				7	=>	-32400,
				8	=>	-28800,
				9	=>	-25200,
				10	=>	-21600,
				11	=>	-19800,
				12	=>	-18000,
				13	=>	-16200,
				14	=>	-14400,
				15	=>	-10800,
				16	=>	-7200,
				17	=>	-3600,
				1	=>	0,
				18	=>	3600,
				19	=>	7200,
				20	=>	9000,
				21	=>	10800,
				22	=>	12600,
				23	=>	14400,
				24	=>	16200,
				25	=>	17100,
				26	=>	21600,
				27	=>	23400,
				28	=>	25200,
				29	=>	28800,
				30	=>	30600,
				31	=>	31500,
				32	=>	32400,
				33	=>	34200,
				34	=>	36000,
				35	=>	37800,
				36	=>	39600,
				37	=>	41400,
				38	=>	43200,
				39	=>	45900,
				40	=>	46800,
				41	=>	50400
		);
	return $allTimeZone[$do->timezone];
	}
	
	/********************** Monitors **********************/
	
	/**
	 * Get last KNX events
	 */
	function monitorKnx() {
		return null;
	}
	
	/**
	 * List all IP device on the network
	 */
	function monitorIp() {
		return null;
	}
	
	/**
	 * Scan the IP network
	 */
	function monitorIpRefresh() {
		return null;
	}
	
	/**
	 * Get last 500 EnOcean
	 */
	function monitorEnocean() {
		return null;
	}
	/**
	 * List all available Bluetooth devices
	 */
	function monitorBluetooth() {
		return null;
	}
	
	/********************** User permission **********************/
	
	/**
	 * Modify floor order
	 * @param int $userid user id
	 * @param int $floorid floor id
	 * @param int $action increment or decrement order
	 */
	function SetFloorOrder($userid, $floorid, $action) {
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT floor_order, floor_id
		        FROM mcuser_floor
		        WHERE mcuser_id=:user_id AND floor_id=:floor_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id',$this->getId(), PDO::PARAM_INT);
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
			$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do2 = $req->fetch(PDO::FETCH_OBJ);
			if(!empty($do2)) {
				$sql = 'UPDATE mcuser_floor
				        SET floor_order=:order
				        WHERE mcuser_id=:user_id AND floor_id=:floor_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do->floor_order + $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
				$sql = 'UPDATE  mcuser_floor
				        SET floor_order=:order
				        WHERE floor_id=:floor_id AND mcuser_id=:user_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do2->floor_order - $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':floor_id', $do2->floor_id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
		}
	}
	
	/**
	 * Modify room order
	 * @param int $userid user id
	 * @param int $roomid room id
	 * @param int $action increment or decrement order
	 */
	function SetRoomOrder($userid, $roomid, $action){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT room_order, room_id
		        FROM mcuser_room
		        WHERE mcuser_id=:user_id AND room_id=:room_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
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
			$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do2 = $req->fetch(PDO::FETCH_OBJ);
			if(!empty($do2)){
				$sql = 'UPDATE mcuser_room
				        SET room_order=:order
				        WHERE mcuser_id=:user_id AND room_id=:room_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do->room_order + $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
				$sql = 'UPDATE  mcuser_room
				        SET room_order=:order
				        WHERE room_id=:room_id AND mcuser_id=:user_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do2->room_order - $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
				$req->bindValue(':room_id', $do2->room_id, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
			}
		}
	}
	
	/**
	 * Modify device order
	 * @param int $userid user id
	 * @param int $deviceid device id
	 * @param int $action increment or decrement order
	 */
	function SetDeviceOrder($userid, $deviceid, $action){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT  device_order, room_device_id
		        FROM mcuser_device
		        WHERE mcuser_id=:user_id AND room_device_id=:room_device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
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
			$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$do2 = $req->fetch(PDO::FETCH_OBJ);
			if(!empty($do2)){
				$sql = 'UPDATE mcuser_device
				        SET device_order=:order
				        WHERE mcuser_id=:user_id AND room_device_id=:room_device_id';
				$req = $link->prepare($sql);
				$req->bindValue(':order', $do->device_order + $action, PDO::PARAM_INT);
				$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
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
	
	/**
	 * Set the temperature of a project device
	 * @param int $iddevice project device id
	 * @param int $idoption option id
	 * @param int $action modification
	 */
	function mcValueDef($iddevice, $idoption, $action){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT  opt_value
		        FROM    room_device_option
		        WHERE   room_device_id=:iddevice AND option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':option_id', $idoption, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		$val = '0';
		if (!empty($do->opt_value)){
			$val = $do->opt_value + $action;
		}
		if ((int)$val == $val){
			$val = $val.'.0';
		}
		$sql = 'UPDATE  room_device_option
		        SET     opt_value =  :opt_value
		        WHERE   room_device_id=:iddevice AND option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':iddevice', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':option_id', $idoption, PDO::PARAM_INT);
		$req->bindValue(':opt_value', $val, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->mcTemperature($iddevice, $idoption, $val);
		return $val;
	}
	
	/**
	 * Return all allowed installation information
	 */
	function mcAllowed(){
		$link = Link::get_link('domoleaf');
		$listFloor = array();
		$listRoom = array();
		$listDevice = array();
		$listSmartcmd = array();
		$listApps= array();
		$res = $this->conf_load();
		$sql = 'SELECT floor_name, mcuser_floor.floor_id, mcuser_floor.floor_order
		        FROM mcuser_floor
		        JOIN floor ON mcuser_floor.floor_id=floor.floor_id
		        WHERE mcuser_id=:user_id AND mcuser_floor.floor_allowed = 1
		        ORDER BY floor_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listFloor[$do->floor_id] = array(
				'floor_name' => $do->floor_name,
				'floor_id' => $do->floor_id,
				'floor_order' => $do->floor_order
			);
		}
		$sql = 'SELECT room.room_name, room.room_id, mcuser_room.room_order, 
		               floor, mcuser_room.room_bgimg
		        FROM room
		        JOIN mcuser_room ON room.room_id=mcuser_room.room_id
		        JOIN mcuser_floor ON room.floor=mcuser_floor.floor_id AND
		                           mcuser_floor.mcuser_id=mcuser_room.mcuser_id
		        WHERE mcuser_room.mcuser_id=:user_id AND   mcuser_room.room_allowed = 1
		        ORDER BY mcuser_floor.floor_order ASC, room_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listRoom[$do->room_id] = array(
				'room_name'  => $do->room_name,
				'room_id'    => $do->room_id,
				'room_order' => $do->room_order,
				'room_bgimg'  => $do->room_bgimg,
				'floor_id'   => $do->floor
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
		        WHERE mcuser_device.mcuser_id=:user_id AND mcuser_device.device_allowed = 1
		        ORDER BY mcuser_floor.floor_order ASC, mcuser_room.room_order ASC, 
		                 mcuser_device.device_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$listDevice[$do->room_device_id] = array(
				'room_id'        => $do->room_id,
				'application_id' => $do->application_id,
				'device_id'      => $do->device_id,
				'protocol_id'    => $do->protocol_id,
				'name'           => $do->name,
				'room_device_id' => $do->room_device_id,
				'device_order'   => $do->device_order,
				'device_bgimg'   => $do->device_bgimg,
				'device_opt'     => array()
			);
			if(!in_array($do->application_id, $listApps)){
				$listApps[] = $do->application_id;
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
							'name' 		=> $do->name,
							'addr'		=> $do->addr,
							'addr_plus' => $do->addr_plus,
							'dpt_id'    => $do->dpt_id,
							'unit'      => $do->unit,
							'opt_value'	=> $do->opt_value,
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
							'name' 		=> $do->name,
							'addr'		=> $do->addr,
							'addr_plus' => $do->addr_plus,
							'dpt_id'    => $do->dpt_id,
							'unit'      => $do->unit,
							'opt_value'	=> $do->opt_value
					);
				}
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
	 * Return all visible MasterCommand elements
	 */
	function mcVisible(){
		$link = Link::get_link('domoleaf');
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
	 * Reset error on a device
	 * @param int $room_device_id project device id
	 * @param int $option_id option id
	 */
	function mcResetError($room_device_id, $option_id) {
		return null;
	}
	
	/**
	 * Connection to a protected popup
	 * @param int $room_device_id device id
	 * @param string $password password
	 */
	function popupPassword($room_device_id, $password) {
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT room_device_id, password
		        FROM room_device
		        WHERE room_device_id=:device_id';
		$req = $link->prepare($sql);
		$req->bindValue(':device_id', $room_device_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do->room_device_id) || $do->password != $password) {
			return 0;
		}
		else {
			return 1;
		}
	}
	
	/**
	 * Get all information of an installation for an user
	 * @param int $userid user id, current user by default
	 */
	function confUserInstallation($userid){
		$link = Link::get_link('domoleaf');
		if(empty($userid)){
			$userid = $this->getId();
		}
		$list = array();
		$sql = 'SELECT floor.floor_id, floor_name, floor_allowed, floor_order
		        FROM floor
		        JOIN mcuser_floor ON mcuser_floor.floor_id=floor.floor_id
		        WHERE mcuser_id=:user_id AND floor_allowed=1
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
		$sql = 'SELECT room.room_id, room_name, floor, room_order, room_allowed, mcuser_room.room_bgimg
		        FROM room
		        JOIN mcuser_room ON mcuser_room.room_id = room.room_id
		        WHERE mcuser_id=:user_id AND room_allowed=1
		        ORDER BY room_order ASC';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->floor]['room'][$do->room_id] = array(
				'room_id'     => $do->room_id,
				'room_name'   => $do->room_name,
				'room_allowed'=> $do->room_allowed,
				'room_bgimg'  => $do->room_bgimg,
				'room_order'  => $do->room_order,
				'devices'     => array()
			);
		}
		$sql = 'SELECT room_device.room_device_id, room_device.name,
		               room_device.room_id, room.floor, device_order,
		               device_allowed, device_bgimg, device_id
		        FROM room_device
		        JOIN room ON room_device.room_id = room.room_id
		        JOIN mcuser_device ON mcuser_device.room_device_id=room_device.room_device_id
		        WHERE mcuser_id=:user_id AND device_allowed=1
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
				'device_allowed'=> $do->device_allowed
			);
		}
		return $list;
	}
	
	/**
	 * Hide or Display a device
	 * @param int $userid user id
	 * @param int $deviceid device id
	 * @param int $status hide or show (0/1)
	 */
	function confUserVisibleDevice($userid, $deviceid, $status){
		$link = Link::get_link('domoleaf');
		if(empty($userid) || $this->getlevel() == 1){
			$userid = $this->getId();
		}
		$sql = 'SELECT mcuser_device.room_device_id, device_order, room_id
		        FROM mcuser_device
		        JOIN room_device ON mcuser_device.room_device_id = room_device.room_device_id
		        WHERE mcuser_device.room_device_id=:room_device_id AND mcuser_device.mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(!empty($do)){
			if($status == 1){
				if($do->device_order == 0){
					$sql = 'SELECT device_order
				 	        FROM mcuser_device
					        JOIN room_device ON mcuser_device.room_device_id = room_device.room_device_id
					        WHERE mcuser_id=:user_id AND room_id =:room_id
					        ORDER BY device_order DESC
					        LIMIT 1';
					$req = $link->prepare($sql);
					$req->bindValue(':room_id', $do->room_id, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
					$do2 = $req->fetch(PDO::FETCH_OBJ);
					$sql = 'UPDATE mcuser_device
				 	        SET device_order = :order
					        WHERE mcuser_id=:user_id AND room_device_id=:room_device_id';
					$req = $link->prepare($sql);
					$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
					$req->bindValue(':order', $do2->device_order + 1, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
			else {
				$sql = 'UPDATE mcuser_device
			 	        SET device_order = 0
				        WHERE mcuser_id=:user_id AND room_device_id=:room_device_id';
				$req = $link->prepare($sql);
				$req->bindValue(':room_device_id', $deviceid, PDO::PARAM_INT);
				$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
				if($do->device_order > 0){
					$sql = 'UPDATE room_device
					        JOIN mcuser_device ON mcuser_device.room_device_id = room_device.room_device_id
					        SET device_order = device_order - 1
					        WHERE room_device.room_id =:room_id AND mcuser_device.mcuser_id=:user_id AND
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
	 * Hide or Display a room
	 * @param int $userid user id
	 * @param int $roomid room id
	 * @param int $status hide or show (0/1)
	 */
	function confUserVisibleRoom($userid, $roomid, $status){
		$link = Link::get_link('domoleaf');
		if(empty($userid) || $this->getlevel() == 1) {
			$userid = $this->getId();
		}
		$sql = 'SELECT mcuser_room.room_id, room_order, floor
		        FROM mcuser_room
		        JOIN room ON mcuser_room.room_id = room.room_id
		        WHERE mcuser_room.room_id=:room_id AND mcuser_room.mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(!empty($do)){
			if($status == 1){
				if($do->room_order == 0){
					$sql = 'SELECT room_order
				 	        FROM mcuser_room
					        JOIN room ON mcuser_room.room_id = room.room_id
					        WHERE mcuser_id=:user_id AND floor =:floor
					        ORDER BY room_order DESC
					        LIMIT 1';
					$req = $link->prepare($sql);
					$req->bindValue(':floor', $do->floor, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
					$do2 = $req->fetch(PDO::FETCH_OBJ);
					$sql = 'UPDATE mcuser_room
				 	        SET room_order = :order
					        WHERE mcuser_id=:user_id AND room_id=:room_id';
					$req = $link->prepare($sql);
					$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
					$req->bindValue(':order', $do2->room_order + 1, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
			else {
				$sql = 'UPDATE mcuser_room
			 	        SET room_order = 0
				        WHERE mcuser_id=:user_id AND room_id=:room_id';
				$req = $link->prepare($sql);
				$req->bindValue(':room_id', $roomid, PDO::PARAM_INT);
				$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
				if($do->room_order > 0){
					$sql = 'UPDATE room
					        JOIN mcuser_room ON mcuser_room.room_id = room.room_id
					        SET room_order = room_order - 1
					        WHERE room.floor =:floor AND 
					              mcuser_room.mcuser_id=:user_id AND 
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
	 * Hide or Display a floor
	 * @param int $userid user id
	 * @param int $floorid floor id
	 * @param int $status hide or show (0/1)
	 */
	function confUserVisibleFloor($userid, $floorid, $status){
		$link = Link::get_link('domoleaf');
		if(empty($userid) || $this->getlevel() == 1) {
			$userid = $this->getId();
		}
		$list = array();
		$sql = 'SELECT floor_id, floor_order
		        FROM mcuser_floor
		        WHERE floor_id=:floor_id AND mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(!empty($do)){
			if($status == 1){
				if($do->floor_order == 0){
					$sql = 'SELECT floor_order
					        FROM mcuser_floor
					        WHERE mcuser_id=:user_id
					        ORDER BY floor_order DESC
					        LIMIT 1';
					$req = $link->prepare($sql);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
					$do2 = $req->fetch(PDO::FETCH_OBJ);
					$sql = 'UPDATE mcuser_floor
				 	        SET floor_order = :order
					        WHERE mcuser_id=:user_id AND floor_id=:floor_id';
					$req = $link->prepare($sql);
					$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
					$req->bindValue(':order', $do2->floor_order + 1, PDO::PARAM_INT);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
			else {
				$sql = 'UPDATE mcuser_floor
				        SET floor_order = 0
				        WHERE mcuser_id=:user_id AND floor_id=:floor_id';
				$req = $link->prepare($sql);
				$req->bindValue(':user_id',   $userid,   PDO::PARAM_INT);
				$req->bindValue(':floor_id', $floorid, PDO::PARAM_INT);
				$req->execute() or die (error_log(serialize($req->errorInfo())));
				if($do->floor_order > 0){
					$sql = 'UPDATE mcuser_floor
					        SET floor_order= floor_order - 1
					        WHERE mcuser_id=:user_id AND floor_order > :floor_order';
					$req = $link->prepare($sql);
					$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
					$req->bindValue(':floor_order', $do->floor_order, PDO::PARAM_INT);
					$req->execute() or die (error_log(serialize($req->errorInfo())));
				}
			}
		}
	}
	
	/**
	 * List devices information for an user
	 * @param number $userid user id (not used)
	 */
	function confUserDeviceEnable($userid){
		$userid = $this -> getId();
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
	
	/**
	 * Modify device permissions for an user
	 * @param int $iduser user id
	 * @param int $deviceid device id
	 * @param int $status allowed or not (0/1)
	 */
	function confUserPermissionDevice($iduser, $deviceid, $status){
		return null;
	}
	
	/**
	 * List rooms information
	 * @param number $userid user id (not used)
	 */
	function confUserRoomEnable($userid = 0){
		$userid = $this->getId();
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
	
	/**
	 * Modify room permissions for an user
	 * @param int $iduser user id
	 * @param int $roomid room id
	 * @param int $status allow or not (0/1)
	 */
	function confUserPermissionRoom($iduser, $roomid, $status){
		return null;
	}
	
	/**
	 * Modify floor permissions for an user
	 * @param int $iduser user id
	 * @param int $floorid floor id
	 * @param int $status allow or not (0/1)
	 */
	function confUserPermissionFloor($iduser, $floorid, $status){
		return null;
	}
	
	/*** User customisation ***/
	
	/**
	 * Set a background image for a widget
	 * @param int $iddevice device id
	 * @param string $bgimg image name
	 * @param int $userid user id, not used
	 */
	function confUserDeviceBgimg($iddevice, $bgimg, $userid=0){
		$userid = $this->getId();
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
	
	/**
	 * Set a background image for a room
	 * @param int $idroom room id
	 * @param string $bgimg image name
	 * @param int $userid user id, not used
	 */
	function confUserRoomBgimg($idroom, $bgimg, $userid=0){
		$userid = $this->getId();
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
	
	/**
	 * Update background color
	 * @param string $color color html code
	 * @param int $userid user id, not used
	 */
	function userUpdateBGColor($color, $userid = 0){
		$userid = $this->getId();
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE mcuser
		        SET bg_color=:color
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':color', $color, PDO::PARAM_STR);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/**
	 * Update menu color
	 * @param string $color color html code
	 * @param int $userid user id, not used
	 */
	function userUpdateMenusBordersColor($color, $userid = 0){
		$userid = $this->getId();
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE mcuser
		        SET border_color=:color
		        WHERE mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':color', $color, PDO::PARAM_STR);
		$req->bindValue(':user_id', $userid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/*** Smartcommand ***/
	
	/**
	 * Search a Smartcommand
	 * @param string $smartcmd_name Smartcommand name
	 * @return number Smartcommand id, 0 if not found
	 */
	function searchSmartcmdByName($smartcmd_name){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT smartcommand_id, mcuser_id
		        FROM smartcommand_list
		        WHERE name=:smartcmd_name AND mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_name', $smartcmd_name, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do->mcuser_id) || $do->mcuser_id != $this->getId()) {
			return 0;
		}
		return $do->smartcommand_id;
	}
	
	/**
	 * Get Smartcommand information
	 * @param int $smartcmd_id Smartcommand id
	 */
	function searchSmartcmdById($smartcmd_id){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT smartcommand_list.name, mcuser_id,
		               smartcommand_list.room_id AS room_id,
		               room.floor AS floor_id, floor.floor_name AS floor_name
		        FROM smartcommand_list
		        LEFT OUTER JOIN room ON room.room_id = smartcommand_list.room_id
		        LEFT OUTER JOIN floor ON floor.floor_id = room.floor
		        WHERE smartcommand_id=:smartcmd_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do->mcuser_id) || $do->mcuser_id != $this->getId()) {
			return 0;
		}
		return $do;
	}
	
	/**
	 * Count elements of a Smartcommand
	 * @param int $idsmartcmd Smartcommand id
	 */
	function countElemSmartcmd($idsmartcmd) {
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT COUNT(smartcommand_id) AS nb
		        FROM smartcommand_elems
		        WHERE smartcommand_id=:smartcommand_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcommand_id', $idsmartcmd, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		return $do->nb;
	}
	
	/**
	 * List all Smartcommands
	 */
	function listSmartcmd(){
		$link = Link::get_link('domoleaf');
		$list = array();
		$sql = 'SELECT smartcommand_id, name, room.room_name
		        FROM smartcommand_list
		        LEFT OUTER JOIN room ON smartcommand_list.room_id = room.room_id
		        WHERE mcuser_id=:user_id
		        ORDER BY name';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$list = array();
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			if (empty($do->room_name)) {
				$do->room_name = 0;
			}
			$list[$do->smartcommand_id] = array(
					'smartcommand_id'     => $do->smartcommand_id,
					'name'                => $do->name,
					'room_name'           => $do->room_name
			);
		}
		return $list;
	}
	
	/**
	 * Get Smartcommand information
	 * @param int $id_smartcmd Smartcommand id
	 */
	function getSmartcmdElems($id_smartcmd) {
		$link = Link::get_link('domoleaf');
		$red = 0;
		$green = 0;
		$blue = 0;
		$exec_id = 0;
		$list = array();
		$sql = 'SELECT exec_id, smartcommand_elems.room_device_id AS room_device_id,
		               optiondef.option_id, option_value, time_lapse,
		               room_device.name AS device_name, room_device.device_id AS device_id,
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') AS option_name
		        FROM smartcommand_elems
		        JOIN room_device ON room_device.room_device_id = smartcommand_elems.room_device_id
		        JOIN optiondef ON optiondef.option_id = smartcommand_elems.option_id
		        WHERE smartcommand_id=:smartcmd_id
		        ORDER BY exec_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $id_smartcmd, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
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
			if ($do->option_id == 410 && empty($white)) {
				$white = $do->option_value;
				$exec_id = $do->exec_id;
			}
			if (!empty($white) && !empty($red) && !empty($green) && !empty($blue) && !empty($exec_id)) {
				$hexa_color = convertRGBToHexa($red, $green, $blue, $white);
				$white = 0;
				$red = 0;
				$green = 0;
				$blue = 0;
				$list[$exec_id]['option_value'] = $hexa_color;
			}
			elseif (!empty($red) && !empty($green) && !empty($blue) && !empty($exec_id)) {
				$hexa_color = convertRGBToHexa($red, $green, $blue);
				$red = 0;
				$green = 0;
				$blue = 0;
				$list[$exec_id]['option_value'] = $hexa_color;
			}
		}
		return $list;
	}
	
	/**
	 * Create a new Smartcommand
	 * @param string $smartcmd_name Smartcommand name
	 * @param int $room_id room id, 0 if no room
	 */
	function createNewSmartcmd($smartcmd_name, $room_id){
		$link = Link::get_link('domoleaf');
		if ($this->searchSmartcmdByName($smartcmd_name) != 0) {
			return -1;
		}
		if ($room_id == 0) {
			$room_id = NULL;
		}
		$sql = 'INSERT INTO smartcommand_list
		        (name, mcuser_id, room_id)
				VALUES
				(:smartcmd_name, :user_id, :room_id)';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_name', $smartcmd_name, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->bindValue(':room_id', $room_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		return $link->lastInsertId();
	}
	
	/**
	 * Rename a Smartcommand
	 * @param int $smartcmd_id Smartcommand id
	 * @param string $smartcmd_name new Smartcommand name
	 */
	function updateSmartcmdName($smartcmd_id, $smartcmd_name){
		if ($this->searchSmartcmdByName($smartcmd_name) != 0) {
			return -1;
		}
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE smartcommand_list
		        SET name=:smartcmd_name
		        WHERE smartcommand_id=:smartcmd_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_name', $smartcmd_name, PDO::PARAM_STR);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		return $smartcmd_id;
	}
	
	/**
	 * Add a new element to a Smartcommand
	 * @param int $idsmartcmd Smartcommand id
	 * @param int $idexec exec order
	 * @param int $iddevice project device id
	 * @param int $idoption option id
	 * @param string $valoption option value
	 * @param int $timelapse time
	 * @param int $no_update insert before an other elemen ?
	 */
	function saveNewElemSmartcmd($idsmartcmd, $idexec, $iddevice, $idoption, $valoption, $timelapse, $no_update = 0){
		$link = Link::get_link('domoleaf');
		if ($no_update == 0) {
			$sql = 'UPDATE smartcommand_elems
			        SET exec_id=exec_id+1
			        WHERE smartcommand_id=:smartcommand_id AND exec_id >= :idexec';
			$req = $link->prepare($sql);
			$req->bindValue(':idexec', $idexec, PDO::PARAM_INT);
			$req->bindValue(':smartcommand_id', $idsmartcmd, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		
		$sql = 'INSERT INTO smartcommand_elems
		        (smartcommand_id, exec_id, room_device_id, option_id, option_value, time_lapse)
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
	
	/**
	 * Update the option value of a Smartcommand
	 * @param int $idsmartcmd Smartcommand id
	 * @param int $idexec element id
	 * @param int $optionval option value
	 * @param int $id_option option id
	 */
	function updateSmartcmdElemOptionValue($idsmartcmd, $idexec, $optionval, $id_option) {
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE smartcommand_elems
		        SET option_value=:option_val
		        WHERE smartcommand_id=:smartcmd_id AND exec_id=:exec_id AND 
		              option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':option_val', $optionval, PDO::PARAM_STR);
		$req->bindValue(':smartcmd_id', $idsmartcmd, PDO::PARAM_INT);
		$req->bindValue(':exec_id', $idexec, PDO::PARAM_INT);
		$req->bindValue(':option_id', $id_option, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/**
	 * Change element order of a Smartcommand
	 * @param int $smartcmd_id Smartcommand id
	 * @param int $old_exec_id old order
	 * @param int $new_exec_id new order
	 */
	function smartcmdChangeElemsOrder($smartcmd_id, $old_exec_id, $new_exec_id) {
		$link = Link::get_link('domoleaf');
		if ($old_exec_id == $new_exec_id) {
			return;
		}
		if ($new_exec_id > $old_exec_id) {
			$sql1 = 'UPDATE smartcommand_elems
			         SET exec_id=exec_id-1
			         WHERE smartcommand_id=:smartcommand_id AND exec_id <= :idexec';
			$sql2 = 'UPDATE smartcommand_elems
			         SET exec_id=:new_exec_id
			         WHERE smartcommand_id=:smartcmd_id AND exec_id=:old_exec_id-1';
			$sql3 = 'UPDATE smartcommand_elems
			         SET exec_id=exec_id+1
			         WHERE smartcommand_id=:smartcommand_id AND exec_id < :idexec';
		}
		else {
			$sql1 = 'UPDATE smartcommand_elems
			         SET exec_id=exec_id+1
			         WHERE smartcommand_id=:smartcommand_id AND exec_id >= :idexec';
			$sql2 = 'UPDATE smartcommand_elems
			         SET exec_id=:new_exec_id
			         WHERE smartcommand_id=:smartcmd_id AND exec_id=:old_exec_id+1';
			$sql3 = 'UPDATE smartcommand_elems
			         SET exec_id=exec_id-1
			         WHERE smartcommand_id=:smartcommand_id AND exec_id > :idexec';
		}
		$req = $link->prepare($sql1);
		$req->bindValue(':smartcommand_id', $smartcmd_id, PDO::PARAM_INT);
		$req->bindValue(':idexec', $new_exec_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$req = $link->prepare($sql2);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->bindValue(':old_exec_id', $old_exec_id, PDO::PARAM_INT);
		$req->bindValue(':new_exec_id', $new_exec_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$req = $link->prepare($sql3);
		$req->bindValue(':idexec', $old_exec_id, PDO::PARAM_INT);
		$req->bindValue(':smartcommand_id', $smartcmd_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/**
	 * Delete a Smartcommand
	 * @param id $smartcmd_id Smartcommand id
	 */
	function removeSmartcmd($smartcmd_id) {
		$link = Link::get_link('domoleaf');
		$sql = 'DELETE FROM smartcommand_list
		        WHERE smartcommand_id=:smartcmd_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/**
	 * Remove an element from a Smartcommand
	 * @param int $smartcmd_id Smartcommand id
	 * @param int $exec_id element id
	 */
	function removeSmartcmdElem($smartcmd_id, $exec_id) {
		$link = Link::get_link('domoleaf');
		$sql = 'DELETE FROM smartcommand_elems
		        WHERE smartcommand_id=:smartcmd_id AND exec_id=:exec_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->bindValue(':exec_id', $exec_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$sql = 'UPDATE smartcommand_elems
		        SET exec_id=exec_id-1
		        WHERE smartcommand_id=:smartcmd_id AND exec_id > :exec_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->bindValue(':exec_id', $exec_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/**
	 * Modify the delay before the element launching
	 * @param int $smartcmd_id Smartcommand id
	 * @param int $exec_id element id
	 * @param int $delay delay in seconds
	 */
	function smartcmdUpdateDelay($smartcmd_id, $exec_id, $delay) {
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE smartcommand_elems
		        SET time_lapse=:delay
		        WHERE smartcommand_id=:smartcmd_id AND exec_id=:exec_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_INT);
		$req->bindValue(':exec_id', $exec_id, PDO::PARAM_INT);
		$req->bindValue(':delay', $delay, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/**
	 * Link a Smartcommand to a room
	 * @param int $smartcmd_id Smartcommand id
	 * @param int $room_id room id
	 */
	function smartcmdSaveLinkedRoom($smartcmd_id, $room_id) {
		$link = Link::get_link('domoleaf');
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
	
	/*** Trigger Events ***/
	
	/**
	 * Search a Trigger
	 * @param string $trigger_name Trigger name
	 * @return number Trigger id, 0 if not found
	 */
	function searchTriggerByName($trigger_name){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT id_trigger, mcuser_id
		        FROM trigger_events_list
		        WHERE trigger_name=:trigger_name AND mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_name', $trigger_name, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do->mcuser_id) || $do->mcuser_id != $this->getId()) {
			return 0;
		}
		return $do->id_trigger;
	}
	
	/**
	 * Get Trigger information
	 * @param int $trigger_id Trigger id
	 */
	function searchTriggerById($trigger_id){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT trigger_name, mcuser_id
		        FROM trigger_events_list
		        WHERE id_trigger=:trigger_id';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_id', $trigger_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do->mcuser_id) || $do->mcuser_id != $this->getId()) {
			return 0;
		}
		return $do;
	}
	
	/**
	 * Return number of conditions in the trigger
	 * @param int $trigger_id trigger id
	 */
	function countTriggerConditions($trigger_id) {
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT COUNT(id_condition) AS nb
		        FROM trigger_events_conditions
		        WHERE id_trigger=:trigger_id';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_id', $trigger_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		return $do->nb;
	}
	
	/**
	 * List user's Triggers
	 */
	function listTriggers(){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT id_trigger, trigger_name
		        FROM trigger_events_list
		        WHERE mcuser_id=:user_id
		        ORDER BY trigger_name';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$list = array();
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->id_trigger] = array(
					'trigger_id'     => $do->id_trigger,
					'name'           => $do->trigger_name
			);
		}
		return $list;
	}
	
	/**
	 * Get Trigger elements
	 * @param int $id_trigger Trigger id
	 */
	function getTriggerElems($id_trigger) {
		$link = Link::get_link('domoleaf');
		$red = 0;
		$green = 0;
		$blue = 0;
		$id_condition = 0;
		$sql = 'SELECT id_condition, value, optiondef.option_id,
		               trigger_events_conditions.room_device_id AS room_device_id,
		               room_device.name AS device_name, operator,
		               room_device.device_id AS device_id,
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') AS option_name
		        FROM trigger_events_conditions
		        JOIN room_device ON room_device.room_device_id = trigger_events_conditions.room_device_id
		        JOIN optiondef ON optiondef.option_id = trigger_events_conditions.id_option
		        WHERE id_trigger=:trigger_id
		        ORDER BY id_condition';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_id', $id_trigger, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$list ='';
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->id_condition] = array(
					'trigger_id'        => $id_trigger,
					'condition_id'      => $do->id_condition,
					'room_device_id'    => $do->room_device_id,
					'option_id'         => $do->option_id,
					'option_value'      => $do->value,
					'device_name'       => $do->device_name,
					'device_id'         => $do->device_id,
					'option_name'       => $do->option_name,
					'operator'          => $do->operator
			);
			if ($do->option_id == 392 && empty($red)) {
				$red = $do->option_value;
				$id_condition = $do->id_condition;
			}
			if ($do->option_id == 393 && empty($green)) {
				$green = $do->option_value;
				$id_condition = $do->id_condition;
			}
			if ($do->option_id == 394 && empty($blue)) {
				$blue = $do->option_value;
				$id_condition = $do->id_condition;
			}
			if ($do->option_id == 410 && empty($white)) {
				$white = $do->option_value;
				$exec_id = $do->exec_id;
			}
			if (!empty($white) && !empty($red) && !empty($green) && !empty($blue) && !empty($exec_id)) {
				$hexa_color = convertRGBToHexa($red, $green, $blue, $white);
				$white = 0;
				$red = 0;
				$green = 0;
				$blue = 0;
				$list[$exec_id]['option_value'] = $hexa_color;
			}
			elseif (!empty($red) && !empty($green) && !empty($blue) && !empty($exec_id)) {
				$hexa_color = convertRGBToHexa($red, $green, $blue);
				$red = 0;
				$green = 0;
				$blue = 0;
				$list[$exec_id]['option_value'] = $hexa_color;
			}
		}
		return $list;
	}
	
	/**
	 * Create a new trigger
	 * @param string $trigger_name trigger name
	 * @return number trigger id
	 */
	function createNewTrigger($trigger_name) {
		if ($this->searchTriggerByName($trigger_name) != 0) {
			return -1;
		}
		$link = Link::get_link('domoleaf');
		$sql = 'INSERT INTO trigger_events_list
		        (trigger_name, mcuser_id)
				VALUES 
				(:trigger_name, :user_id)';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_name', $trigger_name, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateTriggersList();
		return $link->lastInsertId();
	}
	
	/**
	 * Rename a trigger
	 * @param int $trigger_id trigger id
	 * @param string $trigger_name new trigger name
	 */
	function updateTriggerName($trigger_id, $trigger_name){
		if ($this->searchTriggerByName($trigger_name) != 0) {
			return -1;
		}
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE trigger_events_list
		        SET trigger_name=:trigger_name
		        WHERE id_trigger=:trigger_id';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_name', $trigger_name, PDO::PARAM_STR);
		$req->bindValue(':trigger_id', $trigger_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		return $trigger_id;
	}
	
	/**
	 * Add a new condition to a Trigger
	 * @param int $idtrigger Trigger id
	 * @param int $idcondition Condition id
	 * @param int $iddevice project device id
	 * @param int $idoption option id
	 * @param string $valoption option value
	 * @param int $operator operator
	 * @param int $no_update insert before an other elemen ?
	 */
	function saveNewElemTrigger($idtrigger, $idcondition, $iddevice, $idoption, $valoption, $operator, $no_update = 0){
		$link = Link::get_link('domoleaf');
		if ($no_update == 0) {
			$sql = 'UPDATE trigger_events_conditions
			        SET id_condition=id_condition+1
			        WHERE id_trigger=:id_trigger AND id_condition >= :idexec';
			$req = $link->prepare($sql);
			$req->bindValue(':idexec', $idcondition, PDO::PARAM_INT);
			$req->bindValue(':id_trigger', $idtrigger, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
		}
		$sql = 'INSERT INTO trigger_events_conditions
		        (id_trigger, id_condition, room_device_id, id_option, operator, value)
		        VALUES
		        (:id_trigger, :id_condition, :room_device_id, :id_option, :operator, :option_value)';
		$req = $link->prepare($sql);
		$req->bindValue(':id_trigger', $idtrigger, PDO::PARAM_INT);
		$req->bindValue(':id_condition', $idcondition, PDO::PARAM_INT);
		$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':id_option', $idoption, PDO::PARAM_INT);
		$req->bindValue(':operator', $operator, PDO::PARAM_INT);
		$req->bindValue(':option_value', $valoption, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateTriggersList();
	}
	
	/**
	 * Get informations for an option in a trigger
	 * @param int $idtrigger Trigger id
	 * @param int $idcondition Condition id
	 */
	function triggerElemOption($idtrigger, $idcondition) {
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT value, operator
		        FROM trigger_events_conditions
		        WHERE id_trigger=:trigger_id AND id_condition=:condition_id';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_id', $idtrigger, PDO::PARAM_INT);
		$req->bindValue(':condition_id', $idcondition, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		return $do;
	}
	
	/**
	 * Update the required option value of a Trigger
	 * @param int $idtrigger Trigger id
	 * @param int $idcondition condition id
	 * @param int $optionval option value
	 * @param int $id_option option id
	 * @param int $operator comparison operator
	 */
	function updateTriggerElemOptionValue($idtrigger, $idcondition, $optionval, $id_option, $operator) {
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE trigger_events_conditions
		        SET value=:option_val, operator=:operator
		        WHERE id_trigger=:trigger_id AND id_condition=:condition_id AND id_option=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':option_val', $optionval, PDO::PARAM_STR);
		$req->bindValue(':trigger_id', $idtrigger, PDO::PARAM_INT);
		$req->bindValue(':condition_id', $idcondition, PDO::PARAM_INT);
		$req->bindValue(':option_id', $id_option, PDO::PARAM_INT);
		$req->bindValue(':operator', $operator, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateTriggersList();
	}
	
	/**
	 * Change the order of conditions in a Trigger
	 * @param int $id_trigger Trigger id
	 * @param int $old_condition_id condition element
	 * @param int $new_condition_id condition new order
	 */
	function triggerChangeElemsOrder($id_trigger, $old_condition_id, $new_condition_id) {
		$link = Link::get_link('domoleaf');
		if ($old_condition_id == $new_condition_id) {
			return;
		}
		if ($new_condition_id > $old_condition_id) {
			$sql1 = 'UPDATE trigger_events_conditions
			         SET id_condition=id_condition-1
			         WHERE id_trigger=:id_trigger AND id_condition <= :idexec';
			$sql2 = 'UPDATE trigger_events_conditions
			         SET id_condition=:new_condition_id
			         WHERE id_trigger=:trigger_id AND id_condition=:old_condition_id-1';
			$sql3 = 'UPDATE trigger_events_conditions
			         SET id_condition=id_condition+1
			         WHERE id_trigger=:id_trigger AND id_condition < :idexec';
		}
		else {
			$sql1 = 'UPDATE trigger_events_conditions
			         SET id_condition=id_condition+1
			         WHERE id_trigger=:id_trigger AND id_condition >= :idexec';
			$sql2 = 'UPDATE trigger_events_conditions
			         SET id_condition=:new_condition_id
			         WHERE id_trigger=:trigger_id AND id_condition=:old_condition_id+1';
			$sql3 = 'UPDATE trigger_events_conditions
			         SET id_condition=id_condition-1
			         WHERE id_trigger=:id_trigger AND id_condition > :idexec';
		}
		$req = $link->prepare($sql1);
		$req->bindValue(':id_trigger', $id_trigger, PDO::PARAM_INT);
		$req->bindValue(':idexec', $new_condition_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$req = $link->prepare($sql2);
		$req->bindValue(':trigger_id', $id_trigger, PDO::PARAM_INT);
		$req->bindValue(':old_condition_id', $old_condition_id, PDO::PARAM_INT);
		$req->bindValue(':new_condition_id', $new_condition_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$req = $link->prepare($sql3);
		$req->bindValue(':idexec', $old_condition_id, PDO::PARAM_INT);
		$req->bindValue(':id_trigger', $id_trigger, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateTriggersList();
	}
	
	/**
	 * Delete Trigger
	 * @param int $trigger_id Trigger id
	 */
	function removeTrigger($trigger_id) {
		$link = Link::get_link('domoleaf');
		$sql = 'DELETE FROM trigger_events_list
		        WHERE id_trigger=:trigger_id';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_id', $trigger_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateTriggersList();
	}
	
	/**
	 * Delete a condition on a Trigger
	 * @param int $trigger_id Trigger id
	 * @param int $condition_id Condition id
	 */
	function removeTriggerElem($trigger_id, $condition_id) {
		$link = Link::get_link('domoleaf');
		$sql = 'DELETE FROM trigger_events_conditions
		        WHERE id_trigger=:trigger_id AND id_condition=:condition_id';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_id', $trigger_id, PDO::PARAM_INT);
		$req->bindValue(':condition_id', $condition_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$sql = 'UPDATE trigger_events_conditions
		        SET id_condition=id_condition-1
		        WHERE id_trigger=:trigger_id AND id_condition > :condition_id';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_id', $trigger_id, PDO::PARAM_INT);
		$req->bindValue(':condition_id', $condition_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateTriggersList();
	}
	
	/**
	 * Ask to the master daemon to update the Trigger list
	 */
	function udpateTriggersList(){
		$socket = new Socket();
		$socket->send('triggers_list_update');
	}

	/*** Schedules ***/ 
	
	/**
	 * Search a Schedule
	 * @param string $schedule_name Schedule name
	 * @return number Schedule id, 0 if not found or not allowed
	 */
	function searchScheduleByName($schedule_name){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT id_schedule, mcuser_id
		        FROM trigger_schedules_list
		        WHERE schedule_name=:schedule_name AND mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':schedule_name', $schedule_name, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do->mcuser_id) || $do->mcuser_id != $this->getId()) {
			return 0;
		}
		return $do->id_schedule;
	}
	
	/**
	 * Get Schedule information
	 * @param int $schedule_id Schedule id
	 */
	function searchScheduleById($schedule_id){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT schedule_name, mcuser_id
		        FROM trigger_schedules_list
		        WHERE id_schedule=:schedule_id';
		$req = $link->prepare($sql);
		$req->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do->mcuser_id) || $do->mcuser_id != $this->getId()) {
			return 0;
		}
		return $do;
	}
	
	/**
	 * List user's Schedules
	 */
	function listSchedules(){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT id_schedule, schedule_name
		        FROM trigger_schedules_list
		        WHERE trigger_schedules_list.mcuser_id=:user_id
		        ORDER BY schedule_name';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$list = array();
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->id_schedule] = array(
					'schedule_id'     => $do->id_schedule,
					'name'           => $do->schedule_name,
			);
		}
		return $list;
	}
	
	/**
	 * Update Schedule date
	 * @param int $idschedule Schedule id
	 * @param int $months months
	 * @param int $weekdays week days
	 * @param int $days days
	 * @param int $hours hours
	 * @param int $mins minutes
	 */
	function updateSchedule($idschedule, $months, $weekdays, $days, $hours, $mins){
		$link = Link::get_link('domoleaf');
		$arrayHours = str_split(sprintf("%'.024s\n", decbin($hours)));
		$arrayMins = str_split($mins);
		$diffTime = $this->profileTime();
		if ($diffTime > 0){
			$nbHours = $diffTime / 3600;
			$nbMins = $diffTime % 3600 / 60;
			for ($i = 0 ; $i < $nbHours ; ++$i){
				array_unshift($arrayHours, array_pop($arrayHours));
			}
			for ($i = 0 ; $i < $nbMins ; ++$i){
				array_unshift($arrayMins, array_pop($arrayMins));
			}
			$hours = bindec(join($arrayHours));
			$mins = join($arrayMins);
		}
		else if ($diffTime < 0){
			$diffTime = $diffTime * -1;
			$nbHours = $diffTime / 3600;
			$nbMins = $diffTime % 3600 / 60;
			for ($i = 0 ; $i < $nbHours ; ++$i){
				array_push($arrayHours, array_shift($arrayHours));
			}
			for ($i = 0 ; $i < $nbMins ; ++$i){
				array_push($arrayMins, array_shift($arrayMins));
			}
			$hours = bindec(join($arrayHours));
			$mins = join($arrayMins);
		}
		$sql = 'UPDATE trigger_schedules_list
		        SET months=:months, weekdays=:weekdays, days=:days, 
		            hours=:hours, mins=:mins
		        WHERE id_schedule=:schedule_id AND mcuser_id = :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':months', $months, PDO::PARAM_INT);
		$req->bindValue(':weekdays', $weekdays, PDO::PARAM_INT);
		$req->bindValue(':days', $days, PDO::PARAM_INT);
		$req->bindValue(':hours', $hours, PDO::PARAM_INT);
		$req->bindValue(':mins', $mins, PDO::PARAM_STR);
		$req->bindValue(':schedule_id', $idschedule, PDO::PARAM_INT);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateSchedulesList();
	}
	
	/**
	 * Get Schedule information
	 * @param int $idschedule Schedule id
	 */
	function getSchedule($idschedule){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT months, weekdays, days, hours, mins
		        FROM trigger_schedules_list
		        WHERE id_schedule=:schedule_id AND mcuser_id = :user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':schedule_id', $idschedule, PDO::PARAM_INT);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		$arrayHours = str_split(sprintf("%'.024s\n", decbin($do->hours)));
		$arrayMins = str_split($do->mins);
		$diffTime = $this->profileTime();
		if ($diffTime > 0){
			$nbHours = $diffTime / 3600;
			$nbMins = $diffTime % 3600 / 60;
			for ($i = 0 ; $i < $nbHours - 1; ++$i){
				array_push($arrayHours, array_shift($arrayHours));
			}
			for ($i = 0 ; $i < $nbMins ; ++$i){
				array_push($arrayMins, array_shift($arrayMins));
			}
			$do->hours = bindec(join($arrayHours));
			$do->mins = join($arrayMins);
		}
		else if ($diffTime < 0){
			$diffTime = $diffTime * -1;
			$nbHours = $diffTime / 3600;
			$nbMins = $diffTime % 3600 / 60;
			for ($i = 0 ; $i < $nbHours + 1; ++$i){
				array_unshift($arrayHours, array_pop($arrayHours));
			}
			for ($i = 0 ; $i < $nbMins ; ++$i){
				array_unshift($arrayMins, array_pop($arrayMins));
			}
			$do->hours = bindec(join($arrayHours));
			$do->mins = join($arrayMins);
		}
		return $do;
	}
	
	/**
	 * Modify Schedule name
	 * @param int $schedule_id Schedule id
	 * @param string $schedule_name new Schedule name
	 */
	function updateScheduleName($schedule_id, $schedule_name){
		if ($this->searchScheduleByName($schedule_name) != 0) {
			return -1;
		}
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE trigger_schedules_list
		        SET schedule_name=:schedule_name
		        WHERE id_schedule=:schedule_id';
		$req = $link->prepare($sql);
		$req->bindValue(':schedule_name', $schedule_name, PDO::PARAM_STR);
		$req->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		return $schedule_id;
	}
	
	/**
	 * Create a new Schedule
	 * @param string $schedule_name Schedule name
	 * @return Schedule id or -1 if error
	 */
	function createNewSchedule($schedule_name){
		if ($this->searchScheduleByName($schedule_name) != 0) {
			return -1;
		}
		$link = Link::get_link('domoleaf');
		$sql = 'INSERT INTO trigger_schedules_list
		        (schedule_name, mcuser_id, months, weekdays, days, hours, mins)
		        VALUES
		        (:schedule_name, :user_id, :months, :weekdays, :days, :hours, :mins)';
		$req = $link->prepare($sql);
		$req->bindValue(':schedule_name', $schedule_name, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->bindValue(':months', intval(str_repeat("1", 12), 2), PDO::PARAM_INT);
		$req->bindValue(':weekdays', intval(str_repeat("1", 7), 2), PDO::PARAM_INT);
		$req->bindValue(':days', intval(str_repeat("1", 31), 2), PDO::PARAM_INT);
		$req->bindValue(':hours', intval(str_repeat("1", 24), 2), PDO::PARAM_INT);
		$req->bindValue(':mins', str_repeat("1", 60), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateSchedulesList();
		return $link->lastInsertId();
	}
	
	/**
	 * Delete a Schedule
	 * @param int $schedule_id Schedule id
	 */
	function removeSchedule($schedule_id) {
		$link = Link::get_link('domoleaf');
		$sql = 'DELETE FROM trigger_schedules_list
		        WHERE id_schedule=:schedule_id';
		$req = $link->prepare($sql);
		$req->bindValue(':schedule_id', $schedule_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateSchedulesList();
	}
	
	/**
	 * Ask to the master daemon to reload all Schedules
	 */
	function udpateSchedulesList(){
		$socket = new Socket();
		$socket->send('schedules_list_update');
	}
	
	/*** Scenarios ***/
	
	/**
	 * Search a scenario
	 * @param string $scenario_name scenario name
	 * @return number scenario id, 0 if not found
	 */
	function searchScenarioByName($scenario_name){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT id_scenario
		        FROM scenarios_list
		        WHERE name_scenario=:name_scenario AND mcuser_id=:user_id';
		$req = $link->prepare($sql);
		$req->bindValue(':name_scenario', $scenario_name, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		return $do->id_scenario;
	}
	
	/**
	 * Get scenario information
	 * @param int $scenario_id scenario id
	 */
	function searchScenarioById($scenario_id){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT name_scenario, mcuser_id
		        FROM scenarios_list
		        WHERE id_scenario=:scenario_id';
		$req = $link->prepare($sql);
		$req->bindValue(':scenario_id', $scenario_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		if ($req->rowCount() == 0) {
			return 0;
		}
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do->mcuser_id) || $do->mcuser_id != $this->getId()) {
			return 0;
		}
		return $do;
	}
	
	/**
	 * List all current user's scenarios
	 */
	function listScenarios(){
		$link = Link::get_link('domoleaf');
		$list = array();
		$sql = 'SELECT id_scenario, name_scenario, id_trigger, id_schedule, scenarios_list.id_smartcmd,
		               smartcommand_list.name AS name_smartcmd, activated, complete
		        FROM scenarios_list
		        LEFT OUTER JOIN smartcommand_list ON scenarios_list.id_smartcmd = smartcommand_list.smartcommand_id
		        WHERE scenarios_list.mcuser_id=:user_id
		        ORDER BY name_scenario';
		$req = $link->prepare($sql);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$list = array();
		while ($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->id_scenario] = array(
					'scenario_id'     => $do->id_scenario,
					'name'            => $do->name_scenario,
					'id_trigger'      => $do->id_trigger,
					'id_schedule'     => $do->id_schedule,
					'id_smartcmd'     => $do->id_smartcmd,
					'name_smartcmd'   => $do->name_smartcmd,
					'activated'       => $do->activated,
					'complete'        => $do->complete
			);
		}
		return $list;
	}
	
	/**
	 * Get scenario information
	 * @param int $idscenario scenario id
	 */
	function getScenario($idscenario){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT name_scenario, id_trigger, id_schedule, id_smartcmd, activated, complete, mcuser_id
		        FROM scenarios_list
		        WHERE id_scenario=:scenario_id';
		$req = $link->prepare($sql);
		$req->bindValue(':scenario_id', $idscenario, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(empty($do->mcuser_id) || $do->mcuser_id != $this->getId()) {
			return 0;
		}
		if (empty($do->id_trigger)) {
			$do->id_trigger = 0;
		}
		if (empty($do->id_schedule)) {
			$do->id_schedule = 0;
		}
		if (empty($do->id_smartcmd)) {
			$do->id_smartcmd = 0;
		}
		return $do;
	}
	
	/**
	 * Create a new scenario
	 * @param string $scenario_name scenario's name
	 */
	function createNewScenario($scenario_name){
		if ($this->searchScenarioByName($scenario_name) != 0) {
			return -1;
		}
		$link = Link::get_link('domoleaf');
		$sql = 'INSERT INTO scenarios_list
		        (name_scenario, mcuser_id)
		        VALUES
		        (:scenario_name, :user_id)';
		$req = $link->prepare($sql);
		$req->bindValue(':scenario_name', $scenario_name, PDO::PARAM_STR);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		return $link->lastInsertId();
	}
	
	/**
	 * Activate/Desactivate a scenario
	 * @param int $scenario_id scenario id
	 * @param int $state activated/desactivated
	 */
	function changeScenarioState($scenario_id, $state) {
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE scenarios_list
		        SET activated=:state
		        WHERE id_scenario=:id_scenario';
		$req = $link->prepare($sql);
		$req->bindValue(':state', $state, PDO::PARAM_INT);
		$req->bindValue(':id_scenario', $scenario_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateScenariosList();
	}
	
	/**
	 * Attribute a smartcommand to a scenario
	 * @param int $scenario_id scenario id
	 * @param int $smartcmd_id smartcommand id
	 */
	function updateScenarioSmartcmd($scenario_id, $smartcmd_id){
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE scenarios_list
		        SET id_smartcmd=:smartcmd_id
		        WHERE id_scenario=:scenario_id';
		$req = $link->prepare($sql);
		$req->bindValue(':smartcmd_id', $smartcmd_id, PDO::PARAM_STR);
		$req->bindValue(':scenario_id', $scenario_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateScenariosList();
	}
	
	/**
	 * Set a trigger for a scenario
	 * @param int $scenario_id scenario id
	 * @param int $trigger_id trigger id
	 */
	function updateScenarioTrigger($scenario_id, $trigger_id){
		$link = Link::get_link('domoleaf');
		if ($trigger_id == 0) {
			$trigger_id = null;
		}
		$sql = 'UPDATE scenarios_list
		        SET id_trigger=:trigger_id
		        WHERE id_scenario=:scenario_id';
		$req = $link->prepare($sql);
		$req->bindValue(':trigger_id', $trigger_id, PDO::PARAM_STR);
		$req->bindValue(':scenario_id', $scenario_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateScenariosList();
	}
	
	/**
	 * Set a schedule for a scenario
	 * @param int $scenario_id scenario id
	 * @param int $schedule_id schedule id
	 */
	function updateScenarioSchedule($scenario_id, $schedule_id){
		$link = Link::get_link('domoleaf');
		if ($schedule_id == 0) {
			$schedule_id = null;
		}
		$sql = 'UPDATE scenarios_list
		        SET id_schedule=:schedule_id
		        WHERE id_scenario=:scenario_id';
		$req = $link->prepare($sql);
		$req->bindValue(':schedule_id', $schedule_id, PDO::PARAM_STR);
		$req->bindValue(':scenario_id', $scenario_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateScenariosList();
	}
	
	/**
	 * Change scenario name
	 * @param int $scenario_id scenario id
	 * @param string $scenario_name new scenario name
	 */
	function updateScenarioName($scenario_id, $scenario_name){
		if ($this->searchScenarioByName($scenario_name) != 0) {
			return -1;
		}
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE scenarios_list
		        SET name_scenario=:scenario_name
		        WHERE id_scenario=:scenario_id';
		$req = $link->prepare($sql);
		$req->bindValue(':scenario_name', $scenario_name, PDO::PARAM_STR);
		$req->bindValue(':scenario_id', $scenario_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		return $scenario_id;
	}
	
	/**
	 * Set a scenario as completed
	 * @param int $scenario_id scenario id
	 */
	function completeScenario($scenario_id){
		$link = Link::get_link('domoleaf');
		$sql = 'UPDATE scenarios_list
		        SET complete=1
		        WHERE id_scenario=:scenario_id';
		$req = $link->prepare($sql);
		$req->bindValue(':scenario_id', $scenario_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
	}
	
	/**
	 * Delete a scenario
	 * @param int $scenario_id scenario id
	 */
	function removeScenario($scenario_id) {
		$link = Link::get_link('domoleaf');
		$sql = 'DELETE FROM scenarios_list
		        WHERE id_scenario=:scenario_id';
		$req = $link->prepare($sql);
		$req->bindValue(':scenario_id', $scenario_id, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$this->udpateScenariosList();
	}
	
	/**
	 * Ask to the master daemon to update the scenarios list
	 */
	function udpateScenariosList(){
		$this->udpateTriggersList();
		$this->udpateSchedulesList();
		$socket = new Socket();
		$socket->send('scenarios_list_update');
	}
	
	/*** KNX action ***/
	
	/**
	 * Launch a KNX write long (> 1bit)
	 * @param int $daemon daemon id
	 * @param string $addr KNX address
	 * @param int $value value
	 */
	function knx_write_l($daemon, $addr, $value=0){
		return null;
	}
	
	/**
	 * Launch a KNX write short (1bit)
	 * @param int $daemon daemon id
	 * @param string $addr KNX address
	 * @param int $value value
	 */
	function knx_write_s($daemon, $addr, $value=0){
		return null;
	}
	
	/**
	 * Launch a KNX read
	 * @param int $daemon daemon id
	 * @param string $addr KNX address
	 */
	function knx_read($daemon, $addr){
		return null;	
	}
	
	/*** KNX log ***/
	
	/**
	 * List all KNX physical address in the logs
	 */
	function confKnxAddrList(){
		return null;
	}
	

	/*** Backup Database ***/
	
	/**
	 * List local Database backups
	 */
	function confDbListLocal(){
		return NULL;
	}
	
	/**
	 * Create a local backup of the Database
	 */
	function confDbCreateLocal(){}
	
	/**
	 * Delete a backup on an USB key
	 * @param int $filename backup creation timestamp
	 */
	function confDbRemoveUsb($filename){
		return NULL;
	}
	
	/**
	 * Restore a backup on an USB key
	 * @param int $filename backup creation timestamp
	 */
	function confDbRestoreUsb($filename){
		return NULL;
	}
	
	/**
	 * Check if an USB key is connected to the Box
	 */
	function confDbCheckUsb(){
		return NULL;
	}
	
	/**
	 * List all backups on the USB key
	 */
	function confDbListUsb(){
		return NULL;
	}
	
	/**
	 * Create a backup on the USB key
	 */
	function confDbCreateUsb(){}
	
	/**
	 * Delete a local backup
	 * @param int $filename backup creation timestamp
	 */
	function confDbRemoveLocal($filename){}
	
	/**
	 * Restore a local backup
	 * @param int $filename backup creation timestamp
	 */
	function confDbRestoreLocal($filename){}
	
	/*** Optiondef ***/
	
	/**
	 * List all available options
	 */
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
	
	/*** Room_device_option ***/
	
	/**
	 * List the used option units by project device and option
	 */
	function listUnits(){
		$link = Link::get_link('domoleaf');
		$list = array();
		$sql = 'SELECT room_device_id,
		               option_id,
		               addr_plus
		        FROM room_device_option
		        WHERE addr_plus NOT LIKE \'\' AND addr_plus NOT LIKE ("%/%")';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->room_device_id][$do->option_id] = $do->addr_plus;
		}
		return $list;
	}
	
	/**
	 * List available options for a project device
	 * @param number $iddevice project device id
	 */
	function confOptionDptList($iddevice = 0){
		return null;
	}
	
	/*** Updates ***/
	
	/**
	 * Check if an update is available for the Domoleaf Software
	 */
	function confCheckUpdates() {
		$socket = new Socket();
		$socket->send('check_updates');
	}
	
	/**
	 * Update the Domoleaf Software
	 */
	function confUpdateVersion() {
		$socket = new Socket();
		$socket->send('update');
	}
	
	
	/*** Master command ***/
	
	/**
	 * List all devices
	 */
	function mcDeviceAll(){
		$link = Link::get_link('domoleaf');
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
		               room_device.device_id, 
		               optiondef.option_id, room_device_option.addr,
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name,
		               room_device_option.addr_plus, room_device_option.opt_value
		        FROM room_device
		        JOIN room_device_option ON room_device_option.room_device_id = room_device.room_device_id
		        JOIN optiondef ON room_device_option.option_id = optiondef.option_id
		        WHERE room_device_option.status = 1';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$list[$do->room_device_id]['device_opt'][$do->option_id] = array(
				'option_id'=> $do->option_id,
				'name'     => $do->name,
				'addr'     => $do->addr,
				'addr_plus'=> $do->addr_plus,
				'opt_value'   => $do->opt_value
			);
		}
		return $list;
	}
	
	/**
	 * Return all device information for display
	 * @param int $roomdeviceid project device id
	 */
	function mcDeviceInfo($roomdeviceid){
		$link = Link::get_link('domoleaf');
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
		               room_device.device_id, 
		               optiondef.option_id, room_device_option.addr,
		               if(optiondef.name'.$this->getLanguage().' = "", optiondef.name, optiondef.name'.$this->getLanguage().') as name,
		               room_device_option.addr_plus, room_device_option.opt_value
		        FROM room_device
		        JOIN room_device_option ON room_device_option.room_device_id = room_device.room_device_id
		        JOIN optiondef ON room_device_option.option_id = optiondef.option_id
		        WHERE room_device.room_device_id=:room_device_id AND 
		              room_device_option.status = 1';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $roomdeviceid, PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			$info['device_opt'][$do->option_id] = array(
				'option_id' => $do->option_id,
				'name'      => $do->name,
				'addr'      => $do->addr,
				'addr_plus' => $do->addr_plus,
				'opt_value'    => $do->opt_value
			);
		}
		return $info;
	}
	
	/**
	 * Check if access to device is allowed
	 * @param int $iddevice device id
	 * @return boolean
	 */
	function checkDevice($iddevice){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT device_allowed
		        FROM mcuser_device
		        WHERE mcuser_id=:user_id AND room_device_id=:iddevice';
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
	 * Send an action request to the master daemon
	 * @param int $iddevice project device id
	 * @param int $value value
	 * @param int $optionid option id
	 */
	function mcAction($iddevice, $value, $optionid){
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT device_allowed, room_device_option.addr_plus, 
		               room_device.device_id
		        FROM mcuser_device
		        JOIN room_device_option ON mcuser_device.room_device_id=room_device_option.room_device_id
		        JOIN room_device ON room_device.room_device_id=room_device_option.room_device_id
		        WHERE mcuser_id=:user_id AND 
		              mcuser_device.room_device_id=:room_device_id AND 
		              option_id=:option_id';
		$req = $link->prepare($sql);
		$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
		$req->bindValue(':option_id', $optionid, PDO::PARAM_INT);
		$req->bindValue(':user_id', $this->getId(), PDO::PARAM_INT);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
		if(!empty($do->device_id) &&
		   (!empty($do->device_allowed) || $this->getlevel() > 1)) {
			//Generic
			if($do->device_id == 86) {
				$value = $do->addr_plus;
			}
			$sql ='UPDATE room_device_option
			       SET opt_value=:opt_value
			       WHERE room_device_id=:room_device_id AND 
			             option_id=:option_id';
			$req = $link->prepare($sql);
			$req->bindValue(':room_device_id', $iddevice, PDO::PARAM_INT);
			$req->bindValue(':opt_value', $value, PDO::PARAM_INT);
			$req->bindValue(':option_id', $optionid, PDO::PARAM_INT);
			$req->execute() or die (error_log(serialize($req->errorInfo())));
			$socket = new Socket();
			$data = array(
				'room_device_id'=> $iddevice,
				'value'         => $value,
				'option_id'     => $optionid
			);
			$socket->send('send_to_device', $data);
		}
	}
	
	/**
	 * Get all information for the display interface
	 */
	function mcReturn(){
		if(apcu_exists('mcReturn_'.$this->getId())) {
			return unserialize(apcu_fetch('mcReturn_'.$this->getId()));
		}
		$link = Link::get_link('domoleaf');
		$res = $this->conf_load();
		$list = Array();
		$sql = 'SELECT room_device_option.room_device_id, option_id, opt_value, addr_plus, room_device.device_id, dpt_id
		        FROM   room_device_option
		        JOIN   room_device ON room_device_option.room_device_id=room_device.room_device_id';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		while($do = $req->fetch(PDO::FETCH_OBJ)) {
			if ($do->option_id == 399) {
				$highCost = $res[14]->configuration_value;
				$lowCost = $res[15]->configuration_value;
				$lowField1 = $res[16]->configuration_value;
				$lowField2 = $res[17]->configuration_value;
				$currency = checkCurrency($res[18]->configuration_value);
				$diffTime = $this->profileTime();
				$time = date('H', $_SERVER['REQUEST_TIME'] + $diffTime);
				$list[$do->device_id][$do->room_device_id][$do->option_id] = array(
						'device_id'     => $do->device_id,
						'addr_plus'     => $do->addr_plus,
						'room_device_id'=> $do->room_device_id,
						'option_id'     => $do->option_id,
						'opt_value'     => $do->opt_value,
						'highCost'      => $highCost,
						'lowCost'       => $lowCost,
						'lowField1'     => $lowField1,
						'lowField2'     => $lowField2,
						'currency'      => $currency,
						'time'          => $time
				);
			}
			else{
				$list[$do->device_id][$do->room_device_id][$do->option_id] = array(
						'device_id'     => $do->device_id,
						'addr_plus'     => $do->addr_plus,
						'room_device_id'=> $do->room_device_id,
						'option_id'     => $do->option_id,
						'opt_value'     => $do->opt_value
				);
			}
		}
		apcu_store('mcReturn_'.$this->getId(), serialize($list), 1);
		return $list;
	}
	
	/**
	 * Send an action to a camera
	 * @param int $iddevice project device id
	 * @param int $val value
	 * @param int $optionid option id
	 */
	function mcCamera($iddevice, $val, $optionid){
		if($this->checkDevice($iddevice)){
			$socket = new Socket();
			$data = array(
					'room_device_id' => $iddevice,
					'option_id'      => $optionid,
					'action'         => $val,
			);
			$socket->send('send_to_device', $data);
		}
	}

	/**
	 * 
	 * @param int $iddevice device id
	 * @param int $optionid option id
	 * @param int $val Temperature value
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
	
	/**
	 * Launch a Smart Command
	 * @param int $smartcmd_id the Smart Command ID
	 */
	function mcSmartcmd($smartcmd_id){
		$socket = new Socket();
		$socket->send('smartcmd_launch', $smartcmd_id);
	}
}

?>