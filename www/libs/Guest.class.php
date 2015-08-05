<?php 

class Guest {
	
	/**
	 * Return id if it's ok, 0 else
	 	* @param token
	 	*/
	 public static function connect($token) {
	 	$token = trim($token);
		
	 	$link = Link::get_link('mastercommand');
	 	$sql = 'SELECT user.user_id, user_level, language, design, obfuscation
		        FROM user_token
	 			JOIN user ON user_token.user_id=user.user_id
		        WHERE token= :token';
	 	$req = $link->prepare($sql);
	 	$req->bindValue(':token', $token, PDO::PARAM_STR);
	 	$req->execute() or die (error_log(serialize($req->errorInfo())));
	 	$do  = $req->fetch(PDO::FETCH_OBJ);
	
	 	if(!empty($do->user_id)) {
	 		return array(
	 			'id' => $do->user_id,
	 			'level' => $do->user_level,
	 			'language' => $do->language,
	 			'design' => $do->design,
 				'obfuscation' => $do->obfuscation
	 		);
	 	}
	 	else {
	 		return array(
	 			'id' => 0,
	 			'level' => 0,
	 			'language' => '',
	 			'design' => 0,
	 			'obfuscation' => 0
	 		);
	 	}
	 }
	 

	/**
	 * Return id and token
	 * @param mail
	 * @param password
	 */
	public static function connexion($name, $pass) {
		$return = array('id' => 0, 'token' => '', 'error' => 0, 'level' => 0, 
		                'language' => '', 'design' => 0, 'obfuscation' => 0);
		$error  = 0;
	
		$username = addslashes(trim($name));
		$password = trim($pass);
		$link = Link::get_link('mastercommand');
	
		$sql = 'SELECT user_id, user_password, user_level, language, design,
		               obfuscation
		        FROM user
		        WHERE username= :username';
		$req = $link->prepare($sql);
		$req->bindValue(':username', $username, PDO::PARAM_STR);
		$req->execute() or die (error_log(serialize($req->errorInfo())));
		$do = $req->fetch(PDO::FETCH_OBJ);
	
		if(!empty($do->user_id)) {
			if($do->user_password == hash('sha256', $do->user_id.'_'.$password)) {
				$return['id']         = $do->user_id;
				$return['level']      = $do->user_level;
				$return['language']   = $do->language;
				$return['design']     = $do->design;
				$return['obfuscation']= $do->obfuscation;
				
				//New token
				$token = self::create_token();
				$sql = 'INSERT INTO user_token
				        (token, user_id, lastupdate)
				        VALUES
				        (:token, :user, :lastupdate)';
				$req = $link->prepare($sql);
				$req->bindValue(':token', $token, PDO::PARAM_STR);
				$req->bindValue(':user',  $do->user_id, PDO::PARAM_INT);
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
}

?>