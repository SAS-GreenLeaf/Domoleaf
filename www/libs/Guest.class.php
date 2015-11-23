<?php 

class Guest {
	
	/**
	 * Return id if it's ok, 0 else
	 	* @param token
	 	*/
	 public static function connect($token) {
		$token = trim($token);
		
		$link = Link::get_link('domoleaf');
		$sql = 'SELECT mcuser.mcuser_id, mcuser_level, language, design
		        FROM mcuser_token
		        JOIN mcuser ON mcuser_token.mcuser_id=mcuser.mcuser_id
		        WHERE token= :token';
		$req = $link->prepare($sql);
		$req->bindValue(':token', $token, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do  = $req->fetch(PDO::FETCH_OBJ);
	
	 	if(!empty($do->mcuser_id)) {
	 		return array('id' => $do->mcuser_id, 'level' => $do->mcuser_level,
	 				'language' => $do->language, 'design' => $do->design);
	 	}
	 	else {
	 		return array('id' => 0, 'level' => 0, 'language' => '', 'design' => 0);
	 	}
	 }
	 

	 /**
	  * Return id and token
	  * @param mail
	  * @param password
	  */
	 public static function connexion($name, $pass) {
	 	$return = array('id' => 0, 'token' => '', 'error' => 0, 'level' => 0, 'language' => '', 'design' => 0);
	 	$error  = 0;
	 
	 	$username = addslashes(trim($name));
	 	$password = trim($pass);
	 	$link = Link::get_link('domoleaf');
	 
	 	$sql = 'SELECT mcuser_id, mcuser_password, mcuser_level, language, design
		        FROM mcuser
		        WHERE username= :username';
	 	$req = $link->prepare($sql);
	 	$req->bindValue(':username', $username, PDO::PARAM_STR);
	 	$req->execute() or die (error_log(serialize($req->errorInfo())));
	 	$do = $req->fetch(PDO::FETCH_OBJ);
	 
	 	if(!empty($do->mcuser_id)) {
	 		if($do->mcuser_password == hash('sha256', $do->mcuser_id.'_'.$password)) {
	 			$return['id']       = $do->mcuser_id;
	 			$return['level']    = $do->mcuser_level;
	 			$return['language'] = $do->language;
	 			$return['design']   = $do->design;
	 			//New token
	 			$token = self::create_token();
	 			$sql = 'INSERT INTO mcuser_token
	 			        (token, mcuser_id, lastupdate)
	 			        VALUES
	 			        (:token, :user, :lastupdate)';
	 			$req = $link->prepare($sql);
	 			$req->bindValue(':token', $token, PDO::PARAM_STR);
	 			$req->bindValue(':user',  $do->mcuser_id, PDO::PARAM_INT);
	 			$req->bindValue(':lastupdate', $_SERVER['REQUEST_TIME'], PDO::PARAM_INT);
	 			$req->execute() or die (error_log(serialize($req->errorInfo())));
	 			$req->fetch(PDO::FETCH_OBJ);
	 			
	 			$return['token'] = $token;
	 		}
	 		else {
	 			$error = 2;
	 		}
	 	}
	 	else {
	 		$error = 1;
	 	}
	 
	 	$return['error'] = $error;
	 
		return $return;
	}
	 
	/**
	 * Create token
	 */
	public static function create_token() {
		return trim(hash('sha256', mt_rand()));
	}
	
	public static function language() {
		$list = array(
			'de' => _('Deutsch'),
			'en' => _('English'),
			'es' => _('Español'),
			'fr' => _('Français'),
			'it' => _('Italiano'),
		);
		return $list;
	}
	
	public static function design() {
		return array(
			0 => 'Default',
			1 => 'Legacy'
		);
	}

	/**
	 * Check reset key
	 */
	public static function confCheckResetKey($resetKey){
		$link = Link::get_link('domoleaf');
		
		$sql = 'SELECT configuration_value
		        FROM configuration
		        WHERE configuration_id=12';
		$req = $link->prepare($sql);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);

		if (!empty($resetKey) && !empty($do->configuration_value) && $resetKey == $do->configuration_value){
			return True;
		}
		else{
			return False;
		}
	}
	
	public static function confResetPassword($resetKey, $newPassword){
		if (self::confCheckResetKey($resetKey)){
			$admin = new Admin(1);
			$admin->passwordRename('', $newPassword, 1);
			return '1'.$admin->profileInfo(1)->username;
		}
		return False;
	}
}

?>