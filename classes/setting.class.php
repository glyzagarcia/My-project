<?php
class setting{
	
	var $db;
	
	function __construct($db){
		$this->db = $db;
	}
	
	function get_setting($parameter){
		$setting = $this->db->selectSingleQuery("settings", "value", "parameter = '$parameter'");
		return $setting;
	}
	
}
?>