<?php
class content{
	
	var $db;
	
	function __construct($db){
		$this->db = $db;
	}
	
	function get_content($parameter){
		$content = $this->db->selectSingleQuery("contents", "value", "parameter = '$parameter'");
		$content = nl2br($content);
		echo <<<EOF
			<div class='content'>
				$content
			</div>
EOF;
	}
}