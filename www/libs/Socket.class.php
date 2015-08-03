<?php 

class Socket {
	
	private $link;
	
	function __construct($port=4224) {
		try {
			$this->link = @fsockopen('localhost', $port, $errno, $errstr);
		} catch (Execption $e){
			throw _('fsockopen fail');
		}
	}
	
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