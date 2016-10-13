<?php 

/**
 * Manage sockets
 * @author virgil
 */
class Socket {
	
	private $link;
	
	/**
	 * Build the socket connection
	 * @param number $port used port
	 */
	function __construct($port=4224) {
		try {
			$this->link = @fsockopen('localhost', $port, $errno, $errstr);
		} catch (Execption $e){
			throw _('fsockopen fail');
		}
	}
	
	/**
	 * Send information to socket/daemon
	 * @param string $packetType packet type
	 * @param array $data all datas
	 * @param int $sync Synchro or not
	 */
	function send($packetType, $data=array(), $sync=0) {
		$trame = array(
			'packet_type' => $packetType,
			'sync'       => $sync,
			'data'       => $data
		);
		try {
			if (empty($this->link)){
				return null;
			}
			fwrite($this->link, json_encode($trame));
		} catch (Execption $e) {
			throw _('Socket close');
		}
	}
	
	/**
	 * Receive information from socket/daemon
	 */
	function receive() {
		$packetrcv = '';
		try {
			if (empty($this->link)){
				return null;
			}
			while (($buffer = fgets($this->link, 256)) !== false){
				$packetrcv=$packetrcv.$buffer;
			}
		} catch (Execption $e){
			throw _('Socket close');
		}

		return $packetrcv;
	}
	
	function __destruct() {
		try {
			if (!empty($this->link)){
				fclose($this->link);
			}
		} catch (Execption $e) {
			throw _('Socket close');
		}
	}
}

?>