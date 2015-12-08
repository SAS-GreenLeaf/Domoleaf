<?php 

class Api {
	private $id=0;
	private $token   = '';
	private $request = array();
	private $level = 0;
	private $language = '';
	private $design = 0;
	
	function __construct() {
		if(!empty($_COOKIE['token'])) {
			$this->token = $_COOKIE['token'];
		}
	}
	
	function add_request($action, $parameters=array('')) {
		$this->request[$action] = $parameters;
	}
	
	/**
	 * Send requests
	 * Get all answers
	 */
	function send_request() {
		$data = array('token'   => $this->token,
		              'request' => $this->request);
		
		$result = Api::action($this->token, $this->request);
		
		$result = json_decode(json_encode($result));

		if(!empty($result)) {
			if(!empty($result->level)) {
				$this->level = $result->level;
			}
			if(!empty($result->language) && $result->language != 'en') {
				$this->language = $result->language;
				if (!defined('LOCALE')) {
					defineLocale($this->language);
				}
			}
			else {
				if (!defined('LOCALE')) {
					defineLocale(detect_language());
				}
			}
			if(!empty($result->design)) {
				$this->design = $result->design;
			}
			if(isset($result->id) && $result->id == 0) {
				//Destruct cookies
				setcookie('token', '', 0, '/');
			//	redirect();
			}
			elseif(!empty($_COOKIE['token'])) {
				//2 weeks
				setcookie('token', $_COOKIE['token'],
				          ($_SERVER['REQUEST_TIME']+3600*24*14), '/');
				if(!empty($result->id)) {
					$this->id = $result->id;
				}
			}
		}
		else {
			$result = null;
			if (!defined('LOCALE')) {
				defineLocale(detect_language());
			}
		}
		
		if (!defined('TEMPLATE')) {
			$this->setDesign();
		}
		
		return $result->request;
	}
	
	function getId() {
		return $this->id;
	}
	
	function getLevel() {
		return $this->level;
	}
	
	function getLanguage() {
		return $this->language;
	}
	
	function setDesign() {
		switch ($this->design) {
			case 1: 
				define('TEMPLATE', 'legacy');
			break;
			default:
				define('TEMPLATE', 'default');
			break;
		}
	}
	
	function is_co() {
		if($this->id != 0) {
			return true;
		}
		else {
			return false;
		}
	}
	
	function date($time=0, $type=0) {
		if(empty($time)) {
			$time = $_SERVER['REQUEST_TIME'];
		}
		
		switch (LOCALE) {
			case 'fr_FR':
				switch ($type){
					case 1:  return date('H:i', $time); break;
					case 2:  return date('d/m/Y H:i', $time); break;
					case 3:  return date('d/m/Y H:i:s', $time); break;
					default: return date('d/m/Y', $time); break;
				}
			break;
		}
		
		return NULL;
	}
	
	static function action($token, $request) {

		$answer  = array(
				'request' => array(),
				'id'      => 0,
				'level'   => 0,
				'language'=> '',
				'design'  => 0
		);

		if(!empty($token)) {
			$co = Guest::connect($token);
			$answer['id']       = $co['id'];
			$answer['level']    = $co['level'];
			$answer['language'] = $co['language'];
			$answer['design']   = $co['design'];
		}
		elseif(!empty($request)) {
			if(!empty($request['connection']) &&
			   !empty($request['connection'][0]) &&
			   !empty($request['connection'][1])) {
				$rep = Guest::connexion($request['connection'][0], $request['connection'][1]);
				$answer['request']['connection'] = $rep;
	
				if(!empty($rep) && $rep['id'] > 0) {
					$answer['id']       = $rep['id'];
					$answer['level']    = $rep['level'];
					$answer['language'] = $rep['language'];
					$answer['design']   = $rep['design'];
				}
			}
			elseif(!empty($request['confCheckResetKey'])){
				if (!empty($request['confCheckResetKey'][0])){
					$res = Guest::confCheckResetKey($request['confCheckResetKey'][0]);
				}
				else{
					$res = False;
				}
				$answer['request']['confCheckResetKey'] = $res;
			}
			elseif(!empty($request['confResetPassword']) &&
				   !empty($request['confResetPassword'][0]) &&
				   !empty($request['confResetPassword'][1])){
				$res = Guest::confResetPassword($request['confResetPassword'][0], $request['confResetPassword'][1]);
				$answer['request']['confResetPassword'] = $res;
			}		
		}

		if($answer['id'] > 0) {
			//Fix pour ne pas détruire la réponse lors de la connexion
			if(!empty($request['connection'])) {
				unset($request['connection']);
			}
		
			/**
			 * 1: User
			 * 2: Admin
			 * 3: SuperAdmin
			 */
			switch ($answer['level']) {
				case 1:
					$user = new User($answer['id']);
				break;
				
				case 2:
					$user = new Admin($answer['id']);
				break;
		
				case 3:
					$user = new Root($answer['id']);
				break;
				
				default:
					//Error
					exit();
				break;
			}
		
			$user -> setLevel($answer['level']);
			$user -> setLanguage($answer['language']);
			
			if(empty($request) || (!empty($request['mcReturn']) && $_SERVER['REQUEST_TIME']%60 == 0)) {
				$user -> activity();
			}
			
			if(!defined('LOCALE')) {
				defineLocale($answer['language']);
			}
			
			if(!empty($request) && is_array($request)) {
				foreach ($request as $action => $var) {
					$res = null;
						
					switch ($action) {
						/*** Disconnect ***/
						
						case 'disconnect':
							$res = $user->disconnect($token);
						break;
						
						/*** Profile ***/
						
						case 'profileList':
							$res = $user->profileList();
						break;
						
						case 'profileInfo':
							if(empty($var[0])) {
								$var[0] = 0;
							}
							$res = $user->profileInfo($var[0]);
						break;
						
						case 'profileNew':
							if(!empty($var[0]) && !empty($var[1])) {
								$res = $user->profileNew($var[0], $var[1]);
							}
						break;
						
						case 'profileRemove':
							if(empty($var[0])) {
								$var[0] = 0;
							}
							$res = $user->profileRemove($var[0]);
						break;
						
						case 'profileRename':
							if(empty($var[0])) { $var[0] = ''; }
							if(empty($var[1])) { $var[1] = ''; }
							if(empty($var[2])) { $var[2] = ''; }
							if(empty($var[3])) { $var[3] = ''; }
							if(empty($var[4])) { $var[4] = ''; }
							if(empty($var[5])) { $var[5] = ''; }
							if(empty($var[6])) { $var[6] = 0;  }
							$res = $user->profileRename(ucfirst(trim($var[0])), ucfirst(trim($var[1])), $var[2], $var[3], $var[4], $var[5], $var[6]);
						break;
						
						case 'profileLevel':
							if(!empty($var[0]) && !empty($var[1])) {
								$res = $user->profileLevel($var[0], $var[1]);
							}
						break;
						
						case 'profileUsername':
							if(!empty($var[0]) && !empty($var[1])) {
								$res = $user->profileUsername($var[0], $var[1]);
							}
						break;
						
						case 'profilePassword':
							if(!empty($var[0]) && !empty($var[1])) {
								if(empty($var[2])) {
									$var[2] = 0;
								}
								$res = $user->profilePassword($var[0], $var[1], $var[2]);
							}
						break;
						
						/*** Language ***/
						case 'language':
							$res = Guest::language();
						break;
						
						/*** Design ***/
						case 'design':
							$res = Guest::design();
						break;
						
						case 'conf_load':
							$res = $user->conf_load();
						break;
						
						case 'confRemote':
							if (empty($var[0]) or !$var[0] > 0){
								$var[0] = 0;
							}
							if (empty($var[1]) or !$var[1] > 0){
								$var[1] = 0;
							}
							if (empty($var[2])){
								$var[2] = 0;
							}
							else {
								$var[2] = 1;
							}
							$res = $user->confRemote((int)$var[0], (int)$var[1], $var[2]);
						break;
						
						case 'confMail':
							if (empty($var[0])){
								$var[0] = '';
							}
							if (empty($var[1])){
								$var[1] = '';
							}
							if (empty($var[2])){
								$var[2] = '';
							}
							if (empty($var[3]) or !($var[3] >= 0)){
								$var[3] = 0;
							}
							if (empty($var[4]) or !($var[4] > 0)){
								$var[4] = 0;
							}
							if (empty($var[5])){
								$var[5] = '';
								$var[6] = '';
							}
							if (empty($var[6])){
								$var[6] = '';
							}
							$res = $user->confMail($var[0], $var[1], $var[2], $var[3], $var[4], $var[5], $var[6]);
						break;

						case 'confPreConfigurationMail':
							if (empty($var[0])){
								$var[0] = '';
							}
							$res = $user->confPreConfigurationMail($var[0]);
						break;
						
						case 'confSendTestMail':
							$res = $user->confSendTestMail();
						break;

						case 'confSendMail':
							if (empty($var[0])){
								$var[0] = '';
							}
							if (empty($var[1])){
								$var[1] = '';
							}
							if (empty($var[2])){
								$var[2] = '';
							}
							$res = $user->confSendMail($var[0], $var[1], $var[2]);
						break;

						/*** Floor ***/
						
						case 'confFloorList':
							$res = $user->confFloorList();
						break;
						
						case 'confFloorNew':
							if (!empty($var[1])) {
								$res = $user->confFloorNew(ucfirst(trim($var[0])), ucfirst(trim($var[1])));
							}
							else {
								$res = $user->confFloorNew(ucfirst(trim($var[0])));
							}	
						break;
						
						case 'confFloorRename':
							if(!empty($var[0]) && !empty($var[1])) {
								$res = $user->confFloorRename($var[0], ucfirst(trim($var[1])));
							}
						break;
						
						case 'confFloorRemove':
							if(!empty($var[0])) {
								$res = $user->confFloorRemove($var[0]);
							}
						break;

						/*** Rooms ***/
						case 'confRoomAll':
							$res = $user-> confRoomAll();
						break;
						
						case 'confRoomList':
							if(empty($var[0])) {
								$var[0] = 0;
							}
							$res = $user->confRoomList($var[0]);
						break;
						
						case 'confRoomNew':
							if(!empty($var[1])) {
								$res = $user->confRoomNew(ucfirst(trim($var[0])), $var[1]);
							}
						break;
						
						case 'confRoomRename':
							if(!empty($var[0]) && !empty($var[1])) {
								$user->confRoomRename($var[0], ucfirst(trim($var[1])));
							}
						break;
						
						case 'confRoomFloor':
							if(!empty($var[0]) && !empty($var[1])) {
								$user->confRoomFloor($var[0], $var[1]);
							}	
						break;
						
						case 'confRoomRemove':
							if(!empty($var[0]) && !empty($var[1])) {
								$res = $user->confRoomRemove($var[0], $var[1]);
							}
						break;
						
						/*** Devices selectors ***/
						case 'confApplicationAll':
							$res = $user->confApplicationAll();
						break;
						
						/*** Protocol ***/
						case 'confProtocolAll':
							$res = $user->confProtocolAll();
						break;
						
						/*** Devices ***/
						case 'confDeviceSaveInfo':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[3]) && !empty($var[4])){
								$res = $user->confDeviceSaveInfo($var[0], $var[1], $var[2], $var[3], $var[4], $var[5], $var[6], $var[7], $var[8]);
							}
						break;
						
						case 'confDeviceSaveOption':
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confDeviceSaveOption($var[0], $var[1]);
							}
						break;
						
						case 'confDeviceRoomOpt';
							if (!empty($var[0])){
								$res = $user->confDeviceRoomOpt($var[0]);
							}
						break;
						
						case 'confDeviceAll':
							$res = $user->confDeviceAll();
						break;
						
						case 'confRoomDeviceAll':
							if (!empty($var[0])){
								$res = $user->confRoomDeviceAll($var[0]);
							}
						break;
						
						case 'confRoomDeviceRemove':
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confRoomDeviceRemove($var[0], $var[1]);
							}
						break;
						
						case 'confRoomDeviceList':
							$res = $user->confRoomDeviceList($var[0]);
						break;
						
						case 'confDeviceProtocol':
							if(!empty($var[0]) && $var[0] > 0) {
								$res = $user->confDeviceProtocol($var[0]);
							}
						break;
						
						case 'confDeviceNewIp':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2]) && !empty($var[3]) && !empty($var[4])){
								$res = $user->confDeviceNewIp($var[0], $var[1], $var[2], $var[3], $var[4], $var[5], $var[6], $var[7], $var[8]);
							}
						break;

						case 'confDeviceNewKnx':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2]) && !empty($var[3]) && !empty($var[4]) && !empty($var[5])){
								$res = $user->confDeviceNewknx($var[0], $var[1], $var[2], $var[3], $var[4], $var[5]);
							}
						break;

						case 'confDeviceNewEnocean':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2]) && !empty($var[3]) && !empty($var[4])){
								$res = $user->confDeviceNewEnocean($var[0], $var[1], $var[2], $var[3], $var[4]);
							}
						break;

						case 'confManufacturerList':
							if (!empty($var[0])){
								$res = $user->confManufacturerList($var[0]);
							}
						break;
						
						case 'confProductList':
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confProductList($var[0], $var[1]);
							}
						break;

						case 'confProductOptionList':
							if (!empty($var[0])){
								$res = $user->confProductOptionList($var[0]);
							}
						break;

						/*** Monitor ***/
						case 'monitorKnx':
							$res = $user->monitorKnx();
						break;
						
						case 'monitorIp':
							$res = $user->monitorIp();
						break;
						
						case 'monitorIpRefresh':
							$res = $user->monitorIpRefresh();
						break;
						
						case 'monitorEnocean':
							$res = $user->monitorEnocean();
						break;
						
						case 'monitorBluetooth':
							$res = $user->monitorBluetooth();
						break;
						
						/*** Daemon management ***/
						case 'confDaemonList':
							$res = $user->confDaemonList();
						break;
						
						case 'confDaemonNew':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2])){
								$res = $user->confDaemonNew($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'confSaveWifi':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2]) && !empty($var[3])){
								if (empty($var[4])){
									$var[4] = 0;
								}
								$res = $user->confSaveWifi($var[0], $var[1], $var[2], round($var[3]), $var[4]);
							}
						break;
						
						case 'confWifi':
							if (!empty($var[0])){
								$res = $user->confWifi($var[0]);
							}
						break;
						
						case 'confD3Reboot':
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confD3Reboot($var[0], $var[1]);
							}
						break;
						
						case 'confDaemonRemove':
							if (!empty($var[0])){
								$res = $user->confDaemonRemove($var[0]);
							}
						break;
						
						case 'confDaemonRename':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2])){
								if (empty($var[3])){
									$var[3] = '';
								}
								$res = $user->confDaemonRename($var[0], $var[1], $var[2], $var[3]);
							}
						break;
						
						case 'confDaemonProtocolList':
							$res = $user->confDaemonProtocolList();
						break;
						
						case 'confDaemonProtocol':
							if(empty($var[1])) {
								$var[1] = '';
							}
							if(empty($var[2])) {
								$var[2] = '';
							}
							if(!empty($var[0])) {
								$res = $user->confDaemonProtocol($var[0], $var[1], $var[2], $var[3], $var[4], $var[5]);
							}
						break;
						
						case 'confDaemonSendValidation':
							if (!empty($var[0])){
								$res = $user->confDaemonSendValidation($var[0]);
							}
						break;
						
						case 'confDaemonRcvValidation':
								$res = $user->confDaemonRcvValidation();
						break;
						
						/*** User permission ***/
						
						case 'SetFloorOrder':
							if (empty($var[0])){
								$var[0] = 0;
							}
							if (!empty($var[1]) && ($var[2] == -1 or $var[2] == 1)){
								$res = $user->SetFloorOrder($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'SetRoomOrder':
							if (empty($var[0])){
								$var[0] = 0;
							}
							if (!empty($var[1]) && ($var[2] == -1 or $var[2] == 1)){
								$res = $user->SetRoomOrder($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'SetDeviceOrder':
							if (empty($var[0])){
								$var[0] = 0;
							}
							if (!empty($var[1]) && ($var[2] == -1 or $var[2] == 1)){
								$res = $user->SetDeviceOrder($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'confUserInstallation':
							$res = $user->confUserInstallation($var[0]);
						break;
						
						case 'confUserVisibleDevice':
							if (empty($var[2])){
								$var[2] = 0;
							}
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confUserVisibleDevice($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'confUserDeviceEnable':
							if (empty($var[0])) {
								$var[0] = 0;
							}
							$res = $user->confUserDeviceEnable($var[0]);
						break;
						
						case 'confUserPermissionDevice':
							if (empty($var[2])){
								$var[2] = 0;
							}
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confUserPermissionDevice($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'confUserRoomEnable':
							if (empty($var[0])) {
								$var[0] = 0;
							}
							$res = $user->confUserRoomEnable($var[0]);
						break;
							
						case 'confUserVisibleRoom':
							if (empty($var[2])){
								$var[2] = 0;
							}
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confUserVisibleRoom($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'confUserPermissionRoom':
							if (empty($var[2])){
								$var[2] = 0;
							}
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confUserPermissionRoom($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'confUserVisibleFloor':
							if (empty($var[2])){
								$var[2] = 0;
							}
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confUserVisibleFloor($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'confUserPermissionFloor':
							if (empty($var[2])){
								$var[2] = 0;
							}
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->confUserPermissionFloor($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'confUserDeviceBgimg':
							if (empty($var[1])){
								$var[1] = '';
							}
							if (empty($var[2])){
								$var[2] = 0;
							}
							if (!empty($var[0])){
								$res = $user->confUserDeviceBgimg($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'confUserRoomBgimg':
							if (empty($var[1])){
								$var[1] = '';
							}
							if (empty($var[2])){
								$var[2] = 0;
							}
							if (!empty($var[0])){
								$res = $user->confUserRoomBgimg($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'userUpdateBGColor':
							if (empty($var[1])){
								$var[1] = 0;
							}
							if (!empty($var[0])){
								$res = $user->userUpdateBGColor($var[0], $var[1]);
							}
						break;
						
						case 'userUpdateMenusBordersColor':
							if (empty($var[1])){
								$var[1] = 0;
							}
							if (!empty($var[0])){
								$res = $user->userUpdateMenusBordersColor($var[0], $var[1]);
							}
						break;
						
						case 'searchSmartcmdById' :
							if (!empty($var[0])){
								$res = $user->searchSmartcmdById($var[0]);
							}
						break;
						
						case 'countElemSmartcmd' :
							if (!empty($var[0])){
								$res = $user->countElemSmartcmd($var[0]);
							}
						break;
						
						case 'listSmartcmd' :
							$res = $user->listSmartcmd();
						break;
						
						case 'getSmartcmdElems' :
							if (!empty($var[0])){
								if (!empty($user->searchSmartcmdById($var[0]))) {
									$res = $user->getSmartcmdElems($var[0]);
								}
							}
						break;
						
						case 'createNewSmartcmd' :
							if (empty($var[1])) {
								$var[1] = 0;
							}
							if (!empty($var[0])){
								$res = $user->createNewSmartcmd(ucfirst(trim($var[0])), $var[1]);
							}
						break;
							
						case 'updateSmartcmdName' :
							if (!empty($var[0]) && !empty($var[1])){
								if (!empty($user->searchSmartcmdById($var[0]))) {
									$res = $user->updateSmartcmdName($var[0], ucfirst(trim($var[1])));
								}
							}
						break;
	
						case 'saveNewElemSmartcmd' :
							if (empty($var[4])){
								$var[4] = 0;
							}
							if (empty($var[5])){
								$var[5] = 0;
							}
							
							if (!empty($var[0]) && !empty($var[2]) && !empty($var[3])){
								if (!empty($user->searchSmartcmdById($var[0]))) {
									
									if ($var[3] == 392 || $var[3] == 393 || $var[3] == 394) {
										list($red, $green, $blue) = convertHexaToRGB($var[4]);
										$res = $user->saveNewElemSmartcmd($var[0], $var[1], $var[2], 392, $red, $var[5]);
										$res = $user->saveNewElemSmartcmd($var[0], $var[1], $var[2], 393, $green, $var[5], 1);
										$res = $user->saveNewElemSmartcmd($var[0], $var[1], $var[2], 394, $blue, $var[5], 1);
									}
									else {
										$res = $user->saveNewElemSmartcmd($var[0], $var[1], $var[2], $var[3], $var[4], $var[5]);
									}
								}
							}
						break;
						
						case 'updateSmartcmdElemOptionValue' :
							if (!empty($var[0]) && !empty($var[1])){
								if (!empty($user->searchSmartcmdById($var[0]))) {
									
									if ($var[3] == 392 || $var[3] == 393 || $var[3] == 394) {
										list($red, $green, $blue) = convertHexaToRGB($var[2]);
										$res = $user->updateSmartcmdElemOptionValue($var[0], $var[1], $red, 392);
										$res = $user->updateSmartcmdElemOptionValue($var[0], $var[1], $green, 393);
										$res = $user->updateSmartcmdElemOptionValue($var[0], $var[1], $blue, 394);
									}
									else {
										$res = $user->updateSmartcmdElemOptionValue($var[0], $var[1], $var[2], $var[3]);
									}
								}
							}
						break;
						
						case 'smartcmdChangeElemsOrder' :
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2])){
								if (!empty($user->searchSmartcmdById($var[0]))) {
									$res = $user->smartcmdChangeElemsOrder($var[0], $var[1], $var[2]);
								}
							}
						break;
						
						case 'removeSmartcmd' :
							if (!empty($var[0])){
								if (!empty($user->searchSmartcmdById($var[0]))) {
									$res = $user->removeSmartcmd($var[0]);
								}
							}
						break;
						
						case 'removeSmartcmdElem' :
							if (!empty($var[0]) && !empty($var[1])){
								if (!empty($user->searchSmartcmdById($var[0]))) {
									$res = $user->removeSmartcmdElem($var[0], $var[1]);
								}
							}
						break;
						
						case 'smartcmdUpdateDelay' :
							if (empty($var[2])) {
								$var[2] = 0;
							}
							if (!empty($var[0]) && !empty($var[1])) {
								if (!empty($user->searchSmartcmdById($var[0]))) {
									$res = $user->smartcmdUpdateDelay($var[0], $var[1], $var[2]);
								}
							}
						break;
						
						case 'smartcmdSaveLinkedRoom' :
							if (!empty($var[0])) {
								if (!empty($user->searchSmartcmdById($var[0]))) {
									$res = $user->smartcmdSaveLinkedRoom($var[0], $var[1]);
								}
							}
						break;
						
						case 'searchTriggerById' :
							if (!empty($var[0])){
								$res = $user->searchTriggerById($var[0]);
							}
						break;
						
						case 'countTriggerConditions' :
							if (!empty($var[0])){
								$res = $user->countTriggerConditions($var[0]);
							}
						break;
						
						case 'listTriggers' :
							$res = $user->listTriggers();
						break;
						
						case 'createNewTrigger' :
							if (!empty($var[0])){
								$res = $user->createNewTrigger(ucfirst(trim($var[0])));
							}
						break;
						
						case 'getTriggerElems' :
							if (!empty($var[0])){
								if (!empty($user->searchTriggerById($var[0]))) {
									$res = $user->getTriggerElems($var[0]);
								}
							}
						break;

						case 'updateTriggerName' :
							if (!empty($var[0]) && !empty($var[1])){
								if (!empty($user->searchTriggerById($var[0]))) {
									$res = $user->updateTriggerName($var[0], ucfirst(trim($var[1])));
								}
							}
						break;
						
						case 'saveNewElemTrigger' :
							if (empty($var[4])){
								$var[4] = 0;
							}
							if (empty($var[5])){
								$var[5] = 0;
							}
								
							if (!empty($var[0]) && !empty($var[2]) && !empty($var[3])){
								if (!empty($user->searchTriggerById($var[0]))) {
										
									if ($var[3] == 392 || $var[3] == 393 || $var[3] == 394) {
										list($red, $green, $blue) = convertHexaToRGB($var[4]);
										$res = $user->saveNewElemTrigger($var[0], $var[1], $var[2], 392, $red, $var[5]);
										$res = $user->saveNewElemTrigger($var[0], $var[1], $var[2], 393, $green, $var[5], 1);
										$res = $user->saveNewElemTrigger($var[0], $var[1], $var[2], 394, $blue, $var[5], 1);
									}
									else {
										$res = $user->saveNewElemTrigger($var[0], $var[1], $var[2], $var[3], $var[4], $var[5]);
									}
								}
							}
						break;
						
						case 'updateTriggerElemOptionValue' :
							if (empty($var[4])) {
								$var[4] = 0;
							}
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[3])){
								if (!empty($user->searchTriggerById($var[0]))) {
										
									if ($var[3] == 392 || $var[3] == 393 || $var[3] == 394) {
										list($red, $green, $blue) = convertHexaToRGB($var[2]);
										$res = $user->updateTriggerElemOptionValue($var[0], $var[1], $red, 392);
										$res = $user->updateTriggerElemOptionValue($var[0], $var[1], $green, 393);
										$res = $user->updateTriggerElemOptionValue($var[0], $var[1], $blue, 394);
									}
									else {
										$res = $user->updateTriggerElemOptionValue($var[0], $var[1], $var[2], $var[3], $var[4]);
									}
								}
							}
						break;
						
						case 'triggerChangeElemsOrder' :
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2])){
								if (!empty($user->searchTriggerById($var[0]))) {
									$res = $user->triggerChangeElemsOrder($var[0], $var[1], $var[2]);
								}
							}
						break;
						
						case 'removeTrigger' :
							if (!empty($var[0])){
								if (!empty($user->searchTriggerById($var[0]))) {
									$res = $user->removeTrigger($var[0]);
								}
							}
						break;
						
						case 'removeTriggerElem' :
							if (!empty($var[0]) && !empty($var[1])){
								if (!empty($user->searchTriggerById($var[0]))) {
									$res = $user->removeTriggerElem($var[0], $var[1]);
								}
							}
						break;
						
						case 'searchScheduleById' :
							if (!empty($var[0])){
								$res = $user->searchScheduleById($var[0]);
							}
						break;
						
						case 'listSchedules' :
							$res = $user->listSchedules();
						break;
						
						case 'getSchedule' :
							$res = $user->getSchedule($var[0]);
						break;
						
						case 'createNewSchedule' :
							if (!empty($var[0])){
								$res = $user->createNewSchedule(ucfirst(trim($var[0])));
							}
						break;
						
						case 'updateScheduleName' :
							if (!empty($var[0]) && !empty($var[1])){
								if (!empty($user->searchScheduleById($var[0]))) {
									$res = $user->updateScheduleName($var[0], ucfirst(trim($var[1])));
								}
							}
						break;
						
						case 'updateSchedule' :
							if (!empty($var[0])){
								$res = $user->updateSchedule($var[0], $var[1], $var[2], $var[3], $var[4], $var[5]);
							}
						break;
						
						case 'removeSchedule' :
							if (!empty($var[0])){
								if (!empty($user->searchScheduleById($var[0]))) {
									$res = $user->removeSchedule($var[0]);
								}
							}
						break;
						
						case 'searchScenarioById' :
							if (!empty($var[0])){
								$res = $user->searchScenarioById($var[0]);
							}
						break;
						
						case 'listScenarios' :
							$res = $user->listScenarios();
						break;
						
						case 'createNewScenario' :
							if (!empty($var[0])){
								$res = $user->createNewScenario(ucfirst(trim($var[0])));
							}
						break;
						
						case 'getScenario' :
							$res = $user->getScenario($var[0]);
						break;
						
						case 'changeScenarioState' :
							if (empty($var[1])) {
								$var[1] = 0;
							}
							if (!empty($var[0])){
								if (!empty($user->searchScenarioById($var[0]))) {
									$res = $user->changeScenarioState($var[0], $var[1]);
								}
							}
						break;
						
						case 'updateScenarioSmartcmd' :
							if (!empty($var[0]) && !empty($var[1])){
								if (!empty($user->searchScenarioById($var[0]))) {
									$res = $user->updateScenarioSmartcmd($var[0], $var[1]);
								}
							}
						break;
						
						case 'updateScenarioTrigger' :
							if (empty($var[1])) {
								$var[1] = 0;
							}
							if (!empty($var[0])){
								if (!empty($user->searchScenarioById($var[0]))) {
									$res = $user->updateScenarioTrigger($var[0], $var[1]);
								}
							}
						break;
						
						case 'updateScenarioSchedule' :
							if (empty($var[1])) {
								$var[1] = 0;
							}
							if (!empty($var[0])){
								if (!empty($user->searchScenarioById($var[0]))) {
									$res = $user->updateScenarioSchedule($var[0], $var[1]);
								}
							}
						break;
						
						case 'updateScenarioName' :
							if (!empty($var[0]) && !empty($var[1])){
								if (!empty($user->searchScenarioById($var[0]))) {
									$res = $user->updateScenarioName($var[0], ucfirst(trim($var[1])));
								}
							}
						break;
						
						case 'completeScenario' :
							if (!empty($var[0])){
								if (!empty($user->searchScenarioById($var[0]))) {
									$res = $user->completeScenario($var[0]);
								}
							}
						break;
						
						case 'removeScenario' :
							if (!empty($var[0])){
								if (!empty($user->searchScenarioById($var[0]))) {
									$res = $user->removeScenario($var[0]);
								}
							}
						break;
						
						case 'confDbListLocal':
							$res = $user->confDbListLocal();
						break;
						
						case 'confDbCreateLocal':
							$res = $user->confDbCreateLocal();
						break;
							
						case 'confDbRemoveLocal':
							if (!empty($var[0])){
								$res = $user->confDbRemoveLocal($var[0]);
							}
						break;
								
						case 'confDbRestoreLocal':
							if (!empty($var[0])){
								$res = $user->confDbRestoreLocal($var[0]);
							}
						break;

						case 'confDbCheckUsb':
							$res = $user->confDbCheckUsb();
						break;

						case 'confDbListUsb':
							$res = $user->confDbListUsb();
						break;

						case 'confDbCreateUsb':
							$res = $user->confDbCreateUsb();
						break;
								
						case 'confDbRemoveUsb':
							if (!empty($var[0])){
								$res = $user->confDbRemoveUsb($var[0]);
							}
						break;
						
						case 'confDbRestoreUsb':
							if (!empty($var[0])){
								$res = $user->confDbRestoreUsb($var[0]);
							}
						break;
						
						case 'confCheckUpdates':
							$res = $user->confCheckUpdates($var[0]);
						break;
						
						case 'confUpdateVersion':
							$res = $user->confUpdateVersion();
						break;

						/*** KNX action ***/
						case 'knx_write_l':
							if (!empty($var[0]) && !empty($var[1])){
								if (empty($var[2])){
									$var[2] = 0;
								}
								$res = $user->knx_write_l($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'knx_write_s':
							if (!empty($var[0]) && !empty($var[1])){
								if (empty($var[2])){
									$var[2] = 0;
								}
							$res = $user->knx_write_s($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'knx_read':
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->knx_read($var[0], $var[1]);
							}
						break;
						
						/*** KNX log ***/
						
						case 'confKnxAddrList':
							$res = $user->confKnxAddrList();
						break;
						
						/*** Optiondef ***/
						
						case 'confOptionList':
							$res = $user->confOptionList();
						break;
						
						/*** Room_device_option ***/
						
						case 'listUnits':
							$res = $user->listUnits();
						break;
						
						case 'confOptionDptList':
							$res = $user->confOptionDptList();
						break;
						
						/*** Master command ***/
						case 'mcValueDef':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2])){
								$res = $user->mcValueDef($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'mcAllowed':
							$res = $user->mcAllowed();
						break;
						
						case 'mcVisible':
							$res = $user->mcVisible();
						break;
						
						case 'mcDeviceAll':
							$res = $user->mcDeviceAll();
						break;

						case 'mcDeviceUser':
							if(!empty($var[0])){
								$res = $user->mcDeviceUser($var[0]);
							}
						break;
						
						case 'mcAction':
							if (!empty($var[0]) && !empty($var[2])){
								if (empty($var[1]) || $var[1] != 1){
									$var[1] = 0;
								}
								$res = $user->mcAction($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'mcVarie':
							if (!empty($var[0]) && !empty($var[2])){
								if (empty($var[1]) || !($var[1] > 0)){
									$var[1] = 0;
								}
								$res = $user->mcAction($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'mcRGB':
							if (!empty($var[0]) && !empty($var[1])){
								list($red, $green, $blue) = convertHexaToRGB($var[1]);
								
								$res = $user->mcAction($var[0], $red, 392);
								$res = $user->mcAction($var[0], $green, 393);
								$res = $user->mcAction($var[0], $blue, 394);
							}
						break;
						
						case 'mcSmartcmd':
							if (!empty($var[0])){
								if (!empty($user->searchSmartcmdById($var[0]))) {
									$res = $user->mcSmartcmd($var[0]);
								}
							}
						break;
						
						case 'mcDeviceInfo':
							if (!empty($var[0])){
								$res = $user->mcDeviceInfo($var[0]);
							}
						break;
						
						case 'mcAudio':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2])){
								$res = $user->mcAudio($var[0], $var[1], $var[2]);
							}
						break;
						
						case 'mcSetVolume':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2])){
								$res = $user->mcAudio($var[0], 'set_volume', $var[2], $var[1]);
							}
						break;
						
						case 'mcReturn':
							$res = $user->mcReturn();
						break;
						
						case 'mcCamera':
							if (!empty($var[0]) && !empty($var[1]) && !empty($var[2])){
								$res = $user->mcCamera($var[0], $var[1], $var[2]);
							}
						break;

						case 'mcResetError':
							if (!empty($var[0]) && !empty($var[1])){
								$res = $user->mcResetError($var[0], $var[1]);
							}
						break;

					}
					$answer['request'][$action] = $res;
				}
			}
		}
		
		return $answer;
	}
}

?>