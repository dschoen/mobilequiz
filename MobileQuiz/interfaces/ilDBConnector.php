<?php 

class ilDBConnector {
		
	var $currentScript;
	var $iliasRoot;
	var $iliasIniFile;
	var $iliasIniPath;
	
	// ------------------------------------------------------------------
	
	function __construct() {
//  		$this->currentScript 	= dirname(__FILE__);
// 		$this->iliasRoot 		= $this->currentScript . "/../../../../../../../..";
		$this->iliasRoot 		= "/var/opt/ILIAS-5.2.5";
 		$this->iliasIniFile		= "ilias.ini.php";
 		$this->iliasIniPath 	= $this->iliasRoot."/".$this->iliasIniFile;
	}
	
	/**
	 * Returns array of db-credentials from client.ini.php.
	 * If no client ist given, default client is used.
	 * 
	 * @param String $clientName
	 * @return array()
	 */
	public function getDatabaseCredentials($clientName = null) {
		
		if ( !isset($clientName) ) {
			$clientName = $this->getDefautClientName();
		}
		
		$iliasIni = parse_ini_file($this->iliasIniPath, true);
		$iniClients = $iliasIni['clients'];		
		
		$path = $iniClients['path'];
		$clientIniFile = $iniClients['inifile'];
		$clientIniPath = $this->iliasRoot."/".$path."/".$clientName."/".$clientIniFile;
		
		$clientIni = parse_ini_file($clientIniPath, true);
		$clientDb = $clientIni['db'];
		
		$db = array(
			'host' => $clientDb['host'],
			'user' => $clientDb['user'],
			'pass' => $clientDb['pass'],
			'name' => $clientDb['name'],
			'port' => $clientDb['port'],
		);		
		return $db;
	}
	
	// -----------------------------------------------------
	
	private function getDefautClientName() {
		$ini = parse_ini_file($this->iliasIniPath, true);
		$iniClients = $ini['clients'];
		
		return $iniClients['default'];		
	}	
}


