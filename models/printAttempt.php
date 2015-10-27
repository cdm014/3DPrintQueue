<?php 

class printAttempt extends Model {
	public $dbh;
	public $id;
	public $printingID;
	public $columns;
	
	function __construct(&$config) {
		parent::__construct($config);
		$this->columns = array("id","3dprinting_id","Machine","Started","Hours","Minutes","Grams","color","successful");
		$this->tableName = "3dprint_attempts";
		$this->dbh = new PDO('mysql:host=localhost;dbname=test','root','chester');
		$this->stmt = null;
		$this->conditions = array();
	}
	
	function create($printing_id,$Machine,$Started,$Grams,$color) {
		//*
		$this->sql = "insert into `".$this->tableName."` (`3dprinting_id`,`Machine`,`Started`,`Grams`,`color`) values  ( :3dprinting_id , :Machine, :Started , :Grams , :color )";
		$this->stmt = $this->dbh->prepare($this->sql);
		$this->stmt->bindParam(":3dprinting_id",$printing_id,PDO::PARAM_INT);
		$this->stmt->bindParam(":Machine",$Machine,PDO::PARAM_STR);
		$this->stmt->bindParam(":Started",$Started,PDO::PARAM_STR);
		$this->stmt->bindParam(":Grams",$Grams,PDO::PARAM_INT);
		$this->stmt->bindParam(":color",$color,PDO::PARAM_STR);
		$this->params = array();
		$this->params[":3dPrinting_id"] = $printing_id;
		$this->params[":Machine"] = $Machine;
		$this->params[":Started"] = $Started;
		$this->params[":Grams"] = $Grams;
		$this->params[":color"] = $color;
		return $this->stmt->execute();
		//*/
		/*
		$this->sql = "insert into `".$this->tableName."` (`3dprinting_id`,`Machine`,`Started`,`Grams`,`color`) values ($printing_id,'$Machine','$Started',$Grams,'$color')";
		return $this->dbh->exec($this->sql);
		//*/
	}
	
	function stmt_error () {
		$error = array();
		$error['code'] = $this->stmt->errorCode();
		$error['info'] = $this->stmt->errorInfo();
		$error['sql'] = $this->sql;
		$error['params'] = $this->params;
		
		return $error;
		
	}
	
	function error() {
		$error = array();
		$error['code'] = $this->dbh->errorCode();
		$error['info'] = $this->dbh->errorInfo();
		$error['sql'] = $this->sql;
		return $error;
	}
		
	
	function retrieve($attempt_id) {
		$this->sql = "Select * from ".$this->tableName." where id=".$attempt_id;
		$this->stmt = $this->dbh->query($this->sql);
		$this->stmt->setFetchMode(PDO::FETCH_ASSOC);
		return $this->stmt->fetch();
	}
	
	function update($attempt_id, $printing_id, $Machine, $Started, $Hours, $Minutes, $Grams, $color, $successful) {
		$prevValues = $this->retrieve($attempt_id);
		$newValues = array('id' => $attempt_id, '3dprinting_id' => $printing_id, 'Machine' => $Machine, 'Started' => $Started, 'Hours' => $Hours, 'Minutes' => $Minutes, 'Grams' => $Grams, 'color' => $color, 'successful' => $successful);
		$newValues = array_merge($prevValues,$newValues);
		
		$this->params['newValues'] = $newValues;
		$final = array();
		$this->sql = "Update ".$this->tableName." set ";
		foreach ($this->columns as $column) {
			$this->sql .= "$column = :".$column." , ";
			$final[$column] = $newValues[$column];
		}
		$this->sql = substr($this->sql, 0, -2);
		$this->sql .= " where id =".$attempt_id;
		$this->stmt = $this->dbh->prepare($this->sql);
		return $this->stmt->execute($final);
	}
	
	function delete ($attempt_id) {
		$this->sql = "Delete from ".$this->tableName." where id = ".$attempt_id;
		$this->dbh->exec($this->sql);
	}
	
	function fetch_all() {
		$this->stmt = $this->dbh->prepare( "Select * from ".$this->tableName);
		$this->stmt->execute();
		$result = $this->stmt->fetchAll();
		return $result;
	}
	
	function fetch_successful() {
		$this->sql = "Select * from ".$this->tableName." where successful = 1";
		$this->stmt = $this->dbh->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->fetchAll();
		return $result;
	}
	
	function fetch_unsuccessful() {
		$this->sql = "Select * from ".$this->tableName." where successful = 0 or successful is NULL";
		$this->stmt = $this->dbh->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->fetchAll();
		return $result;
	}
	
	function fetch_for_submission($id) {
		$this->sql = "select * from ".$this->tableName." where 3dprinting_id = ".$id;
		$this->stmt = $this->dbh->prepare($this->sql);
		$this->stmt->execute();
		$result = $this->stmt->fetchAll();
		return $result;
	} 
		
		
}
