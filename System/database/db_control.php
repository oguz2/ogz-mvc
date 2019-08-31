<?php

class db_control
{
	public $dbValues = NULL;
	public $data_control = NULL;
	public $where = NULL;
	public $field = NULL;
	public $limit = NULL;
	public $asc = NULL;
	public $desc = NULL;
	public $on = NULL;
	public $error_message = NULL;
	
	public function __construct($dbvalue)
	{
		 if(is_array($dbvalue) === true && count($dbvalue) > 0)
		 {
			$this->dbValues = $dbvalue[0];
			 $this->data_control = $dbvalue[1];
			 $this->where = $dbvalue[2];
			 $this->field = $dbvalue[3];
			 $this->limit = $dbvalue[4];
			 $this->asc = $dbvalue[5];
			 $this->desc = $dbvalue[6];
			 $this->on = $dbvalue[7]; 
		 }
		 
	}
	
	
	public function data_control1($data1, $value1)
	{
		$form_class = class_load("form");
		
		if($form_class === false)
		{
			//class işlemlerinde hata var
			$this->error_message = lang("1.6")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("DBControl File Error", lang("2.title"), $this->error_message);
			return false;	
		}
		else
		{
			if(array_key_exists("field", $data1) !== FALSE || array_key_exists("name", $data1) !== FALSE)
			{
				$form_data = $form_class->data_control($data1, $value1, FALSE);
				if($form_data === false)
				{
					//veride hata var
					$this->error_message = lang("1.6")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
					log_save("DBControl File Error", lang("2.title"), $this->error_message);
					return false;	
				}
				else
					return $form_data;	//hata yok
			}
			else
			{
				return $value1;	
			}
		}
	}
	
	public function data_control($data, $value)
	{		
		try
		{
			if(is_array($data) === true && is_array($value) === true)
			{
				for($i = 0; $i < count($value); $i++)
				{
					if(array_key_exists("name", $data) === true)
					{
						if(is_array($value[$i]) === false)
						{
							$form = $this->data_control1($data, $value[$i]);							
						}
						else
						{
							for ($t = 0; $t < count($value[$i]); $t++)
							{
								$form = $this->data_control1($data, $value[$i][$t]);
								if($form === false)
								{
									throw new Exception ($this->error_message);
								}
							}
						}
					}	
					else
					{
						if(is_array($value[$i]) === false)
						{
							$form = $this->data_control1($data[$i], $value[$i]);
						}
						else
						{
							for ($t = 0; $t < count($value[$i]); $t++)
							{
								$form = $this->data_control1($data[$t], $value[$i][$t]);
								if($form === false)
								{
									throw new Exception ($this->error_message);
								}
							}
						}
					}
					if($form === false)
					{
						throw new Exception ($this->error_message);
					}
				}
			}	
		}
		catch (Exception $err)
		{
			$this->error_message = $err->getMessage()."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("DBControl File Error", lang("7.title"), $this->error_message);
			return false;	
		}
	}
	
	public function table_prefix($table)
	{
		$db = $this->dbValues;
		//tablo ön ekini ekle
		if(array_key_exists("prefix", $db) !== FALSE)
		{			
			$table = is_null($db["prefix"]) !== FALSE || $db["prefix"] != "" ? $db["prefix"].$table : $table;
		}	
		return $table;
	}
	
	public function where_edit()
	{
		$this->where = $this->where == "" ? NULL : $this->where;
		//echo "<pre>"; print_r($this->where); echo "</pre>";

		$this->data_control = $this->data_control !== TRUE ? FALSE : TRUE;
		$query_string = "";
		$db_values  = array();
		if(is_null($this->where) === FALSE || $this->where != "")
		{
			$query_string = "WHERE ";
			if(is_array($this->where) !== FALSE)
			{
				$i = 0;
				foreach($this->where as $keys => $value)
				{
					$key = "";
					$values = "";
					
					if(is_numeric($keys) === false){
						$key = $keys;
						$values = $value;
					}
					else
					{
						foreach($value as $a => $b)
						{
							$key = $a;
							$values	= $b;
						}
					}
				
					//where şart deyimine ilk kez başlanıyor ise "and", "or" vb ifadeleri ekleme
					if($i != 0)
					{
						$query_string .= $key . " ";

					}
					
					//değerleri ekle
					if(is_array($values) === FALSE)
					{
						$query_string .= $values . " ";
					}
					else
					{
						//$values[0] = tablo adı
						//$values[1] = tablo değeri
						//$values[2] = tablo adı ve değeri eşitlik durumu
						if(count($values) < 3 )
						{
							$values[2] = "=";	
						}
						
						if(is_array($values[0]) === true )
						{
							if(array_key_exists("table", $values[0]) === true)
								$field = $this->table_prefix($values[0]["table"]) . "." . $values[0]["field"];
							else
								$field = $values[0]["field"];
						}
						else
						{
							$field = $values[0];
						}	
						
						//tablo adı yaz
						if(is_array($values[0]) !== FALSE)
						{
							//model tablosundan bilgi gelmiş bu bilgilere göre kontrollerini yap
							if($this->data_control !== FALSE && is_array($values[1]) === FALSE && array_key_exists("field", $values[0]) === true)
							{
								//formdan gelen veriyi kontrol et ve form_data değişkenine ata
								$form_data = $this->data_control($values[0], $values[1]);									
								if($form_data === FALSE)
								{
									return $this->error_message;
								}
							}
							$query_string .= $field . " ";
						
						}
						else
						{
							$query_string .= $field . " ";	
						}	

						//tablo şart deyimi değerler bölümünün eklenmesi ve düzenlenmesi
						if(strtolower($values[2]) == "between" || strtolower($values[2]) == "not between")
						{
							if(count($values[1]) != 2) 
							{
								$this->error_message = lang("2.2")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
								log_save("DBCONTROL File Error", lang("2.title"), $this->error_message);
								return false;	
							}
							else{
								if(array_key_exists("table", $values[1][0]) === true )
									$values[1][0] = $this->table_prefix($values[1][0]["table"]) . "." . $values[1][0]["field"];
								
								if(array_key_exists("table", $values[1][1]) === true )
									$values[1][1] = $this->table_prefix($values[1][1]["table"]) . "." . $values[1][1]["field"];
									
								$query_string .= strtoupper($values[2]) . " ? AND ? ";	
								array_push ($db_values, $values[1][0]);
								array_push ($db_values, $values[1][1]);
																
							}									
						}
						else
						{
							if(is_array($values[1]) === true && array_key_exists("table", $values[1]) === true)
								$values[1] = $this->table_prefix($values[1]["table"]) . "." . $values[1]["field"];
								
							$query_string .= $values[2] . " ? ";
						    array_push ($db_values, $values[1]);
						}
					}
					$i = 1;
				}
			}
			else
			{
				$query_string .=  $this->where;
			}	
		}

		//echo $query_string ."<br>";
		return array("string" => $query_string . " ", "values" => $db_values);
	}
	
	public function on_edit()
	{
		$result = "";
		if($this->on !== NULL)
		{
			$result = "ON ";
			if(is_array($this->on) === false)
			{
				$result .= $this->on . "  ";	
			}
			else
			{
				$eq = "=";
				if(count($this->on) > 2)
					$eq = $this->on[2];
					
				$table1 = "";
				if(array_key_exists("field", $this->on[0]) === false)
				{
					$table1 = $this->on[0];	
				}
				else
				{
					$tbl = "";
					if(array_key_exists("table", $this->on[0]) === true)
						$tbl = $this->table_prefix($this->on[0]["table"]) . ".";	
					$table1 = $tbl . $this->on[0]["field"];	
				}
				
				$table2 = "";
				if(array_key_exists("field", $this->on[1]) === false)
				{
					$table2 = $this->on[1];	
				}
				else
				{
					$tbl = "";
					if(array_key_exists("table", $this->on[1]) === true)
						$tbl = $this->table_prefix($this->on[1]["table"]) . ".";	
					$table2 = $tbl . $this->on[1]["field"];	
				}
				$result .= $table1.$eq.$table2;
			}
		}
		return $result;
	}	
	
	public function order_by_edit()
	{
		$db_values = array();
		$query_string = NULL;
		if(is_null($this->asc) === FALSE || is_null($this->desc) === FALSE)
		{
			$query_string = "ORDER BY ";
			
			if(is_null($this->asc) === FALSE)
			{	
				$asc = $this->asc;				
					if(is_array($asc) !== FALSE && array_key_exists("field", $asc) !== false)
					{
						$tbl = "";
						if(array_key_exists("table", $this->asc) === true)
							$tbl = $this->table_prefix($this->asc["table"]) . ".";
						
						$asc = $tbl . $this->asc["field"];
					}
				$query_string .=  $asc . " ASC ";					
				//array_push ($db_values, $this->asc);
			}
			
			if(is_null($this->desc) === FALSE)
			{	
				$desc = $this->desc;
				if(is_array($desc) !== FALSE && array_key_exists("field", $desc) !== false)
				{
					$tbl = "";
					if(array_key_exists("table", $this->desc) === true)
						$tbl = $this->table_prefix($this->desc["table"]) . ".";
						
					$desc = $tbl . $this->desc["field"];	
				}
				$query_string .=  $desc . " DESC ";
				//array_push ($db_values, $this->desc);
			}
		}		
		
		return array("string" => $query_string, "values" => $db_values);	
	}
	
	public function limit_edit($row = false)
	{
		$db_values =array();
		$query_string = "";
		if(is_null($this->limit) === FALSE)
		{
			$query_string = "LIMIT ";
			
			if(is_array($this->limit) === FALSE){
				$query_string .= $this->limit;
			}
			else
			{
				$query_string .= $this->limit[0]. ", " . $this->limit[1];
			}
		}	
		
		if($row === false)
		{
			return array("string" => $query_string, "values" => $db_values);	
		}
		else
		{
			if(is_array($this->limit) === false)
			{
				return $this->limit;	
			}
			else
			{
				return $this->limit[1];	
			}
		}
	}
	
	public function field_edit($dd = false)
	{	
	
		$this->field = $this->field == "" || $this->field === NULL ? "*" : $this->field;
		
		if(is_array($this->field) === false)
		{
			return $this->field;	
		}
		else
		{
			$string = "";
			$p = "";
			if($dd === false)
				$p = ", ";
			
			if(array_key_exists("field", $this->field) === false)
			{
				if(is_array($this->field) !== false)
				{	
					for ($i = 0; $i < count($this->field); $i++)
					{
						$v = (count($this->field) - 1) == $i ? "" : $p;
						if(is_array($this->field[$i]) === true)
						{	
							if(array_key_exists("field", $this->field[$i]) === true)
							{
								$tbl = "";
								if(array_key_exists("table", $this->field[$i]) === true)
									$tbl = $this->table_prefix($this->field[$i]["table"]) . ".";
									
								$string .= $tbl . $this->field[$i]["field"]. $v;
							}
							else 
							{
								$string .= $this->field[$i]. $v;
							}
						}
						else
						{
							$string .= $this->field[$i]. $v;
						}
					}
				}
				else
				{
					$string .= $this->field[$i]. $v;
				}	
			}
			else
			{
				$string .= $this->field["field"];	
			}
					
			return $string;
		}
	}
		
}


?>