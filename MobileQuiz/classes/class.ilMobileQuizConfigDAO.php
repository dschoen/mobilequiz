<?php 

class ilMobileQuizConfigDAO {
	
	var $ilDB;
	
	var $configItems = [			
    		"SHORTENER_ACTIVE" 		=> 0,
			"SHORTENER_USERNAME" 	=> "",
			"SHORTENER_PASSWORD" 	=> "",
			"SHORTENER_URL" 		=> "",
			"SHORTENER_FORMAT" 		=> "simple",
			"LATEX_ACTIVE" 			=> 1,	//0 = false, 1 = true	
			];

	public function __construct()
	{
		global $ilDB;
		$this->ilDB = $ilDB;
		$this->initConfigIfNotExist();
	}
	
	
	public function getConfigItem($key)
	{
		$queryResult = $this->ilDB->query("SELECT value FROM rep_robj_xuiz_config WHERE item LIKE ".$this->ilDB->quote($key, "text"));
		$row = $this->ilDB->fetchObject($queryResult);		
		return $row->value;
	}
	
	
	public function setConfigItem($key, $value)
	{
		$this->ilDB->manipulateF("UPDATE rep_robj_xuiz_config SET value= %s WHERE item LIKE %s ;",
				array("text", "text",),
				array($value, $key,) );
	}
	
	
	private function initConfigIfNotExist()
	{
		if (!$this->configExists()) {
			$this->initConfigItems();
		}
	}
	
	
	private function initConfigItems()
	{
		foreach ($this->configItems as $itemKey => $itemValue) {
			$this->ilDB->manipulateF("INSERT INTO rep_robj_xuiz_config (item, value) VALUES ".
					" (%s,%s)",
					array("text", "text",),
					array($itemKey, $itemValue));
		}
	}
		
	
	private function configExists() 
	{		
		$queryResult = $this->ilDB->query("SELECT * FROM rep_robj_xuiz_config;");		
		if ($this->ilDB->numRows($queryResult) >= 1) {
			return true;
		} else {
				return false;
		}
	}
}