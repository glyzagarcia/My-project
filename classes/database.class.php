<?php
class database{
private $db;
	private $statement;
	
	function __construct($dbname, $user, $pass, $host){
		try{
			$this->db = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass);
		} catch(PDOException $e) {
            echo "Fatal MySQL error<br/>";
            echo "Error Message:<br/>".$e->getMessage();
        }
	}
	
	function executeQuery($sql, $val = array()){
		$this->statement = $this->db->prepare($sql);
		if(empty($val))
			return $this->statement->execute();
		else
			return $this->statement->execute($val);
	}
	
	function deleteQuery($table, $where){
		$sql = "DELETE FROM $table WHERE $where";
		$this->executeQuery($sql);
	}
	
	function selectQuery($table, $field, $where = "1", $add_sql=""){
		if(is_array($field)){
			foreach($field AS $k => $v){
				$f .= "`$v`,";
			}
			$f = substr($f,0,-1);
		}
		else{
			$f = $field;
		}
		$sql = "SELECT $f FROM $table $add_sql WHERE $where";
		//echo $sql;
		//exit();
		$this->executeQuery($sql);
		if(is_array($field) && count($field) == 1)
			return $this->statement->fetchAll(PDO::FETCH_COLUMN,0);
		else
			return $this->statement->fetchAll();
	}
	
	function selectSingleQuery($table, $field , $where, $add_sql=""){
		$sql = "SELECT $field FROM $table $add_sql WHERE $where LIMIT 1";
		//echo $sql;
		//exit();
		$this->executeQuery($sql);
		$result = $this->statement->fetchAll();
		return $result[0]["$field"];
	}
	
	function selectSingleQueryArray($table, $field , $where, $add_query=""){
		if(is_array($field)){
			$fields = implode(",", $field);
		}
		else{
			$fields = $field;
		}
		$sql = "SELECT $fields FROM $table $add_query WHERE $where LIMIT 1";
		$this->executeQuery($sql);
		$result = $this->statement->fetchAll();
		return $result[0];
	}
	
	function insertQuery($table, $array){
		$sql = "INSERT INTO $table";
		$xquery = array();
		foreach($array AS $k => $v){
			if(!empty($v)){
				$col .= "`$k`, ";
				if($v=="NOW()"){
					$val .= "NOW(), ";
				}
				else{
					$val .= ":$k, ";
					$xquery[":$k"] = "$v";
				}
			}
		}
		$col = " (".substr($col, 0, -2).") ";
		$val = "VALUES (".substr($val, 0, -2).") ";
		$sql .= $col.$val;
		if(empty($xquery)){
			return FALSE;
		}
		else{
			if($this->executeQuery($sql, $xquery)){
				return $this->db->lastInsertId();
			}
			else{
				return FALSE;
			}
		}
	}
	
	function updateQuery($table, $array, $where){
	$sql = "UPDATE $table SET ";
		$xquery = array();
		foreach($array AS $k => $v){
				if($v == "NOW()"){
					$col .= "`$k` = NOW(),";
				}
				else{
					$col .= "`$k` = :$k,";
					$xquery[":$k"] = "$v";
				}
		}
		$sql .= substr($col,0,-1)." WHERE $where";
		if(empty($array)){
			return FALSE;
		}
		else{
			return $this->executeQuery($sql, $xquery);
		}
	}
	
	function insertUpdateQuery($table, $array){
		$sql = "INSERT INTO $table";
		$xquery = array();
		foreach($array AS $k => $v){
				$col .= "`$k`, ";
				if($v == "NOW()"){
					$val .= "NOW(), ";
				}
				else{
					$val .= ":$k, ";
					if(is_array($v)){
						$xquery[":$k"] = "".implode(" | ", $v);
					}
					else{
						$xquery[":$k"] = "$v";
					}
				}
		}
		$col = " (".substr($col, 0, -2).") ";
		$val = "VALUES (".substr($val, 0, -2).") ";
		$sql .= $col.$val. "ON DUPLICATE KEY UPDATE ";
		foreach($array AS $k => $v){
			if($k == "id")
				continue;
			if($v == "NOW()")
				$up .= "`$k` = NOW(),";
			else
				$up .= "`$k` = :$k,";
		}
		$sql .= substr($up,0,-1);
		if(empty($xquery)){
			return FALSE;
		}
		else{
			$this->executeQuery($sql, $xquery);
			$id = $this->db->lastInsertId();
			if(empty($id))
				return $array['id'];
			else 
				return $id;
		}
	}
	
	function fetchAllArray(){
		return $this->statement->fetchAll();
	}
	
	function getNumRows(){
		return $this->statement->rowCount();
	}
	
	function getCountRows($table, $field="*", $where="1"){
		$sql="SELECT count($) FROM $table WHERE $where ";
		$this->statement = $this->db->prepare($sql);
		$this->statement->execute();
		$rows = $this->statement->fetch(PDO::FETCH_NUM);
		return $rows[0];
	}
	
}