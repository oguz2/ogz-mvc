<?php
defined("systemPath") or die("Access Denied!!! => 'database'");
class database extends cnf_db
{	
	public $func = "SELECT";
	public $data_control = false;
	public $query = TRUE;
	public $where = NULL;
	public $field = NULL;
	public $limit = NULL;
	public $asc = NULL;
	public $desc = NULL;
	public $values = NULL;
	public $on = NULL;
	public $db_no = NULL;
	public $error_message = NULL;
	
	public function db($dbNumber = 0)
	{		
		if(count($this->dbValues) == 0)
		{
			$this->error_message = lang("2.0"). "<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("DB ERROR", lang("2.title", $this->error_message));
			die($this->error_message);
		}
		else
		{
			$this->db_no = $dbNumber;
			if($dbNumber == '' or  !is_numeric($dbNumber) or $dbNumber > count($this->dbValues)-1 ) $dbNumber = 0;
			
			
			if($this->dbValues[$dbNumber]["host"] == '' || !isset($this->dbValues[$dbNumber]["host"])){
				$this->dbValues[$dbNumber]["host"] == "localhost";
			}
			if($this->dbValues[$dbNumber]["driver"] == '' || !isset($this->dbValues[$dbNumber]["driver"])){
				$this->dbValues[$dbNumber]["driver"] == "PDO";
			}			
			if($this->dbValues[$dbNumber]["dbUser"] == '' || !isset($this->dbValues[$dbNumber]["dbUser"])){
				show_error("DB User Name is not null.");
				return false;
			}
			if($this->dbValues[$dbNumber]["dbName"] == '' || !isset($this->dbValues[$dbNumber]["dbName"])){
				show_error("DB Name is not null.");
				return false;
			}
			if($this->dbValues[$dbNumber]["dbPass"] == '' || !isset($this->dbValues[$dbNumber]["dbPass"])){
				show_error("DB Password is not null.");
				return false;
			}
			if($this->dbValues[$dbNumber]["car_Set"] == '' || !isset($this->dbValues[$dbNumber]["car_Set"])){
				$this->dbValues[$dbNumber]["car_Set"] = 'utf-8';
			}
		}
		
		
		if(file_exists(systemPath."database\db_".$this->dbValues[$dbNumber]["driver"]."_class.php") === FALSE)
		{
			$this->error_message = lang("2.3"). "<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("DB ERROR", lang("2.title", $this->error_message));
			die($this->error_message);	
		}
		else
		{
			$result =  class_load(	
				"db_".$this->dbValues[$dbNumber]["driver"]."_class.php", 
				array($this->dbValues[$dbNumber], $this->func, $this->data_control, $this->query, $this->db_control()->where_edit(), 
				array($this->db_control()->field_edit(), $this->field), $this->db_control()->limit_edit(),
				$this->db_control()->order_by_edit(), $this->values, $this->db_control()->on_edit()), 
				systemPath."database\\", 
				$this->dbValues[$dbNumber]["driver"]."Class"
			);
			$this->func = "SELECT";
			$this->data_control = false;
			$this->query = TRUE;
			$this->where = NULL;
			$this->field = NULL;
			$this->limit = NULL;
			$this->asc = NULL;
			$this->desc = NULL;
			$this->values = NULL;
			$this->on = NULL;
			if($result === false)
			{
				$this->error_message = $this->dbValues[$dbNumber]["driver"]. lang("9.0"). "<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save("DB ERROR", lang("2.title", $this->error_message));
				die($this->error_message);		
			}
			
			return $result;
		}
			
	}
	
	private function db_control()
	{
		$classLoad =  class_load(	
			"db_control.php", 
			array($this->dbValues[$this->db_no], $this->data_control, $this->where, 
			$this->field, $this->limit,$this->asc, $this->desc, $this->on), 
			systemPath."database\\", 
			"db_control"
		);	
		if($classLoad === false)
		{
			$this->error_message = "db_control" . lang("9.0"). "<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("DB ERROR", lang("2.title", $this->error_message));
			die($this->error_message);		
		}
		return $classLoad;
	}	
	
}

?>