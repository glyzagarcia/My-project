<?php
class knowledgebase{
	
	var $db;
	
	function __construct($db){
		$this->db = $db;
	}
	
	function get_data($id, $field){
		$data = $this->db->selectSingleQuery("knowledgebase", $field, "id = $id");
		return nl2br($data);
	}
	
	function get_ids(){
		$ids = $this->db->selectQuery("knowledgebase", "id", "active = 'Y' ORDER BY level ASC, timestamp DESC");
		$arr = array();
		if(is_array($ids)){
			foreach($ids AS $v){
				$arr[] = $v['id'];
			}
		}
		return $arr;
	}
}
?>