<?php 

class SubmissionStatement {
	public $pdostmt;
	function __construct($stmt) {
		$this->pdostmt = $stmt;
	}
	
	function fetch_assoc() {
		if($this->pdostmt) {
			return $this->pdostmt->fetch(PDO::FETCH_ASSOC);
		} else {
			return false;
		}
	}
	
	public function __call($name, $arguments) {
		return call_user_func_array(array($this->pdostmt,$name),$arguments);
	}
}


class Submission extends Model {
	public $columns;
	public $id;
	public $dbh;
	public $conditions;
	public $colors;
	function __construct(&$config) {
		parent::__construct($config);
		$this->columns = array("submitted","patron_name","library_card","phone","email","tosAgreement","print_attempts","printed","contacted","picked_up","final_location","Color","Infill","estimated_minutes","estimated_hours","actual_hours","actual_minutes","Grams");
		$this->colors = array("ANY","White","Red","Orange","Yellow","Green","Blue","Purple","Gray","Black");
		$this->tableName = "3dprinting";	
		$this->dbh = new PDO('mysql:host=localhost;dbname=test','root','chester');
		$this->conditions = array();
		$this->stmt = null;
		if(!array_key_exists('Submission_debug',$this->config)) {
			$this->config['Submission_debug'] = array();
		}
		$this->debug = $this->config['Submission_debug'];
	}
	
	function set_id($id) {
		$this->id = $id;
	}
	
	function attempt_print($id) { 
		$rowsUpdated = $this->dbh->exec("Update ".$this->tableName." set print_attempts = print_attempts + 1 where id = ".$id);
		if ($rowsUpdated == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	function printed($id) {
		$rowsUpdated = $this->dbh->exec("Update ".$this->tableName." set printed = 1 where id = ".$id);
		if ($rowsUpdated == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	function stmt_error () {
		$error = array();
		$error['code'] = $this->stmt->errorCode();
		$error['info'] = $this->stmt->errorInfo();
		$error['sql'] = $this->sql;
		ob_start();
		$this->stmt->debugDumpParams();
		$error['params'] = ob_get_contents();
		ob_end_clean();
		//$error['params'] = $this->params;
		
		return $error;
		
	}
	
	function error() {
		$error = array();
		$error['code'] = $this->dbh->errorCode();
		$error['info'] = $this->dbh->errorInfo();
		$error['sql'] = $this->sql;
		return $error;
	}
	
	function retrieve($id) {
		$this->sql = "Select * from ".$this->tableName." where id=".$id;
		$this->stmt = $this->dbh->query($this->sql);
		return $this->fetch_assoc();
	}
	
	function retrieveByFile($file) {
		$this->sql = "Select * from ".$this->tableName." where final_location like '%$file%'";
		$this->config['Submission_debug'][] = $this->sql;
		$this->stmt = $this->dbh->query($this->sql);
		return $this->fetch_assoc();
		
	}
	
	function update($id, $dataarray) {
		$prevValues = $this->retrieve($id);
		$newValues = array_merge($prevValues,$dataarray);
		$newValues['estimated_hours'] = $newValues['estimated_hours'] ? $newValues['estimated_hours'] : 0;
		$newValues['estimated_minutes'] = $newValues['estimated_minutes'] ? $newValues['estimated_minutes'] : 0;
		$newValues['actual_hours'] = $newValues['actual_hours'] ? $newValues['actual_hours'] : 0;
		$newValues['actual_minutes'] = $newValues['actual_minutes'] ? $newValues['actual_minutes'] : 0;
		$newValues['Grams'] = $newValues['Grams'] ? $newValues['Grams'] : 0;
		$final = array();
		$this->sql = "Update ".$this->tableName." set ";
		
		foreach ($this->columns as $column) {
			
			
			$this->sql .= "$column = :".$column.", ";
			$final[$column] = $newValues[$column];
		}
		$this->sql = substr($this->sql, 0, -2);
		$this->sql .= " where id =".$id;
		$this->stmt = $this->dbh->prepare($this->sql);
		return $this->stmt->execute($final);
	}
	
	function create($data) {
		$this->config['Submission_debug'][] = "Function: create";
		$setup = array();
		$query_fields = array("patron_name","library_card","phone","tosAgreement","email","final_location","submitted","Color","Infill");
		
		foreach($this->columns as $column) {
			if (!array_key_exists($column,$data)){
				$data[$column] = "";
				switch($column) {
					case "Grams":
					case "estimated_hours":
					case "actual_hours":
					case "estimated_minutes":
					case "actual_minutes":
					case "tosAgreement":
						$data[$column] = 0;
						break;
					case "Infill":
						$data[$column] = "auto";
						break;
					case "Color":
						$data[$column] = "ANY";
						break;
				}
			}
		
				
			$this->config['Submission_debug'][] = "Column: $column";
			if(array_key_exists($column,$data)) {
				$this->config['Submission_debug'][] = "Data: ".$data[$column];
			} else {
				$this->config['Submission_debug'][] = "Data for $column not found";
			}
		}
		
		$this->sql = "Insert into 3Dprinting(patron_name, library_card, phone, tosAgreement, email, final_location, submitted, Color, Infill) values ( ";
		
		foreach($query_fields as $field) {
			$ph = ":".$field.", ";
			$this->sql .= $ph;
		}
		$this->sql = substr($this->sql,0,-2);
		$this->sql .= ")";
		$this->config['Submission_debug'][] = "sql = ".$this->sql;
		$this->config['Submission_debug'][] = $setup;
		$this->stmt = $this->dbh->prepare($this->sql);
		foreach ($query_fields as $field) {
			if ($field != "tosAgreement") {
				$this->stmt->bindParam(":".$field,$data[$field],PDO::PARAM_STR);
			} else {
				$this->stmt->bindParam(":".$field,$data[$field],PDO::PARAM_INT);
			}
		}
		if (!$this->stmt->execute()){
			return false;
		} else {
			return true;
		}
		
		
		
	}
	
	function fetch_assoc() {
		if ($this->stmt == null) {
			return false;
		} else {
			return $this->stmt->fetch(PDO::FETCH_ASSOC);
		}
	}
	
	function query($statement) {
		$stmt = $this->dbh->query($statement);
		if ($stmt) {
			$newstmt = new SubmissionStatement($stmt);
			return $newstmt;
		} else {
			return false;
		}
	}
	
	function reset($id) {
		$this->config['SQL'] = "Update ".$this->tableName." set print_attempts = 0, printed = NULL, contacted = 0, picked_up = 0 where id = $id";
		$rowsUpdated = $this->dbh->exec("Update ".$this->tableName." set print_attempts = 0, printed = NULL, contacted = 0, picked_up = 0 where id = $id");
		if ($rowsUpdated == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	function set_contacted($id) {
		$this->config['SQL'] = "Update ".$this->tableName." set contacted = 1 where id = $id";
		$rowsUpdated = $this->dbh->exec($this->config['SQL']);
		if ($rowsUpdated == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	function picked_up($id) {
		$this->config['SQL'] = "Update ".$this->tableName." set picked_up = 1 where id = $id";
		$rowsUpdated = $this->dbh->exec($this->config['SQL']);
		if ($rowsUpdated == 1) {
			return true;
		} else {
			return false;
		}
	}
	
	function delete($id) {
		$this->config['SQL'] = "Delete from ".$this->tableName." where id = $id";
		$rowsUpdates = $this->dbh->exec($this->config['SQL']);
		if($rowsUpdates ==1) {
			return true;
		} else {
			return false;
		}
	}
	
	function reset_all() {
		$this->dbh->exec("Update ".$this->tableName." set print_attempts = 0, printed = NULL, contacted = NULL, picked_up = NULL where 1");
		return true;
	}
}

