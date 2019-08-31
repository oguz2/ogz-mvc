<?php
defined("systemPath") or die("Access Denied!!! => 'DP_PDO_CLass'");

class  pdoClass extends PDO
{
	public $db = NULL;
	public $where = NULL;
	public $field = NULL;
	public $limit = NULL;
	public $order = NULL;
	public $func = "SELECT";
	public $control = FALSE;
	public $select_query = TRUE;
	public $values = NULL;
	public $on = NULL;
	public $error_message = NULL;

	public function __construct($dbvalues)
	{
		$this->db = $dbvalues[0];
		$dns = "mysql:host=".($dbvalues[0]["host"] == "" ? "localhost": $dbvalues[0]["host"]).";dbname=".$dbvalues[0]["dbName"]."";
		
		try
		{
			parent::__construct($dns, $dbvalues[0]["dbUser"], $dbvalues[0]["dbPass"]);	
			parent::query("SET CHARACTER SET ".($dbvalues[0]["car_Set"] == "" ? "utf-8" :  $dbvalues[0]["car_Set"]));			
		}
		catch (PDOException $e)
		{
			//error_control(array(false, "error_report_code" => "2.4"), "", $e->getMessage());
			log_Save("DB ERROR", "PDO Error", $e->getMessage(). "<br>File Name : " . __FILE__ . "<br>Line : " . $e->getLine());
			die($e->getMessage());
		}
		
		$this->func = $dbvalues[1];
		$this->control = $dbvalues[2];
		$this->select_query = $dbvalues[3];
		$this->where = $dbvalues[4];
		$this->field = $dbvalues[5];
		$this->limit = $dbvalues[6];
		$this->order = $dbvalues[7];
		$this->values = $dbvalues[8];
		$this->on = $dbvalues[9];
	}
	
	private function data_control($data, $value)
	{
		$class =  class_load(	
			"db_control.php", 
			1, 
			systemPath."database/", 
			"db_control"
		);		
		
		if($class === false)
		{
			$this->error_message = lang("9.1"). "<br>File : " . __FILE__ . "<br>Line : " .__LINE__;
			log_save("DBPDO ERROR", lang("2.title"), $this->error_message);
			return false;
		}
		else
		{
			$v = $class->data_control($data, $value);
			if($v = false)
			{
				$this->error_message = lang("1.6"). "<br>File : " . __FILE__ . "<br>Line : " .__LINE__;
				log_save("DBPDO ERROR", lang("2.title"), $this->error_message);
				return false;	
			}
			else
			{
				return true;	
			}
		}
	}	
	
	private function table_prefix($table)
	{
		//tablo ön ekini ekle
		if(array_key_exists("prefix", $this->db) !== FALSE)
		{			
			$table = is_null($this->db["prefix"]) !== FALSE || $this->db["prefix"] != "" ? $this->db["prefix"].$table : $table;
		}	
		return $table;
	}
	/*
		kullanım örnekleri
		(select|insert|update|delete * from tbl_nm where id=?|:id,
		1. Kullınım => sadece değer (sorguda sadece bir adet soru işareti kullanılmış ise)
		2. Kullanım => array(1 => "değer", 2 => "deger" ....) sorguda bir veya daa fazla soru işareti kullanılmış
		3. Kullanım => array(":id" => "deger",  "diğersutun" => "diğedeger" ....) birden çok özel isimli parametreler kullanılıyor ise
		4. Kullanım => array(":id" => array("deger"), ":diğersutun"=>"diğerdeger" ... ) 3. kullaım ile neredeyse aynı
		5. Kullanım => array(":id" => array("val"=>"deger", (opsiyonel)type="tip", (opsiyonel)"len"=>"uzunluk")) parametre sayısı kadar çoğaltılabilir 
		)	
	*/	
	public function queryDB($query, $data = NULL, $out = "object", $res = 2)
	{
		$out = strtolower($out) == "object" ?  PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
		try
		{
			$result = parent::prepare($query);
			if($data !== NULL && is_array($data) === TRUE)
			{
				//data parametreleri array olarak tanımlanmış ise
				foreach($data as $key => $values)
				{
					if(is_int($key))
					{
						//sorgu için gerekli parametlererin sadece değerleri vaerilmiştir.
						$result->bindParam(($key+1), $values, PDO::PARAM_STR);
					}
					else
					{
						//sorgu içerisinde gerekli parametlereler için değerlere özel isimler verilmiştir.
						if(is_array($values) === FALSE)
						{
							$result->bindParam($key, $values);	
						}
						else if(count($values) == 1 )
						{
							$result->bindParam($key, $values[0]);	
						}	
						else
						{
							//sorgu için gerekli parametrelere ait özel isimler ve uzunluk/değer tipi gibi özelliklerde verilmiştir.
							if(array_key_exists("val", $values) !== FALSE)
							{	
								$type = PDO::PARAM_STR;
								if(array_key_exists("type", $values) !== FALSE)
								{
									switch(strtolower($values["type"]))
									{
										case "int":
											$type = PDO::PARAM_INT;
											break;
										
										case "bool":
											$type = PDO::PARAM_BOOL;
											break;	
										
										case "":
											$type = PDO::PARAM_NULL;
											break;
										default:
											$type = PDO::PARAM_STR;	
									}
								}
									
								if(array_key_exists("len", $values) !== FALSE)
								{	
									$result->bindParam($key, $values["val"], $type, $values["len"]);	
								}
								else
								{
									$result->bindParam($key, $values["val"], $type);									
								}
							}//if(array_key_exists("val", $values) !== FALSE)
							else
							{
								$result->bindParam($key, $values[0]);
							}	
						}
					}//if(is_null($values))
				}	
			}
			//data parametleri array olarak tanımlanmamış ise
			if($data !== NULL && is_array($data) === FALSE)
			{
				$result->bindParam(1, $data, PDO::PARAM_STR);	
			}/*echo "<pre>";
			print_r($data); echo "</pre>";*/
			$result->execute($data);
			
			$exp = @explode(" ", $query, 2);
			switch(strtolower($exp[0]))
			{
				case "insert":
					return parent::lastInsertId();
					break;
				
				case "update":
					return $result->rowCount();
					break; 
				
				case "delete":
					return $result->rowCount();
					break;
					
				case "select": 
					if($this->func == "COUNT")
						return $result->rowCount();	
					else
					{
						$count = $result->rowCount();
						if($count > 0)
						{
							if($count == 1 && $res == 1)
							{
								return $result->fetch($out)."---";
							}						
							else 
							{
								return $result->fetchAll($out);
							}
							
						}
						else
						{
							return 0;	
						}	
					}						
					break;
					
				default:
					$error = $result->errorInfo();
					log_save("DBPDO ERROR", lang("2.title"), $error[2]. "<br>File : " . __FILE__ . "<br>Line : " .__LINE__);
					return false;
					break;
			}
		}
		catch (PDOException $e)
		{
			log_save("DBPDO ERROR", lang("2.title"), $e->getMessage(). "<br>File : " . __FILE__ . "<br>Line : " . $e->getLine());
			return false;
		}	
	}
	
	/*
		bu fonksiyon üstteki limit_edit(), where_edit(), order_by_edit(), field_edit() fonksiyonlarından yardım alır
		data_select fonksiyonunun kullanımı
		fonksiyon dört parametre almaktadır 
			Birinci paramete tablo adıdır ve zorunludur.
			
			İkinci parametre ise yapılacak işlemdir.
					Eğer veritabanından veri çekmek isteniyorsa "SELECT" ifadesi kullanılabilr veya boş bırakılabilir
					Eğer veritabanındaki tablo sayısı isteniyorsa İkinci parametreye "COUNT" ifadesi yazılmalıdır.
				tablo şartlarını oluşturmak için (WHERE)
				clas içerisindeki $where değişkenine değerler girilmesi gerekmektedir. 
				birden çok değerler dizi olarak girilmesi gerekmektedir. tek bir değer için string olarak tanımlama yapılabilir
				birden çok değerler için dizi tanımlamaları aşağıdaki şekilde yapılabilir.
					1) "and" veya "or" ifasei => "tabloadi = değer(gibi)" her değer için bu şekilde tanımlama yapılabilir
					2) "and" veya "or" ifadesi => "array" array ifadesi en az 2 en fazla 3 değer alabilir. Bunlar
						a) tablo adı (model içerisindeki tanımlama kullanılmış ise) bu tanımlamaya göre değerlerin uygunluğu araştırılır.
						   tablo adı string olarak verilmiş ise değerle ilgili herhangi bir işlem yapılmaz
						b) değer (string, NULL, integer) olabilir (like durumu için dğer ifadeye uygun biçimde tanımlanır %değer% gibi)
						c) tablo adı ve değer eşitli durumu (=, !=, <>, <, >, like gibi) tanımlanmaz ise değer "=" olarak kabul edilir.	
			
			Ücüncü paremetre ise model dosyasındaki bilgilere göre verilerin kontrol edilip edilmeyeceğidir
			bu değer varsayılan olarak kontrol edilmeyecek şeklindedir. Kontrol eidlmesi isteniyorsa TRUE değeri verilebilir.
			
			Dördüncü değer is veritabanı sargusunun yapılıp yapılmayacağıdır. varsayılan olarak TRUE değeri vardır
			eğer false değeri verilir ise veri tabanı sorgusu yapılmaz ve çıktı olarak sorgu stringini döndürür.
		
			$res = 1 ise pdo::fetch() olarak değer döndürür
	*/
	
	
	public function select($table, $out = "OBJECT", $res = 2)
	{		
		try
		{
			if(is_array($table) === false)
				$table = $this->table_prefix($table);
			else
			{
				$inner = "INNER JOIN ";
				if(count($table) > 2 )
					$inner = $table[2] . " ";
				
				$table = $this->table_prefix($table[0]) . " " . $inner . $this->table_prefix($table[1]);
			}		
			
			$db_values = array();
			$query_string = "SELECT " . $this->field[0] . " FROM " . $table . " " . $this->on . " ";
			if(is_null($this->where) !== FALSE || $this->where != "")
			{
				$whr = $this->where;
				$query_string .= $whr["string"];
				$db_values = array_merge($db_values, $whr["values"]);
				
			}
			//where şart bölümü tamamlanmıştır.----------------------------------------------------------------
			
			//oder bölümü başlıyor
			if(is_null($this->order) === FALSE)
			{
				$ord = $this->order;
				$query_string .= $ord["string"];
				$db_values = array_merge($db_values, $ord["values"]);
			}		
			
			//limit başlıyor
			if(is_null($this->limit) === FALSE)
			{
				$lmt = $this->limit;
				$query_string .= $lmt["string"];
				$db_values = array_merge($db_values, $lmt["values"]);
			}
			
			//boşlukları sil 
			$query_string = trim($query_string);
			if($this->select_query === FALSE)
			{
				return $query_string;	
			}
			else
			{	
				$result = $this->queryDB($query_string, $db_values, $out, $res);
				return $result;						
			}
		}
		catch (PDOException $e)
		{
			log_save("DBPDO ERROR", lang("2.title"), $e->getMessage() . "<br>Dile : " . __FILE__ . "<br>Line : " . $e->getLine());
			return false;
		}
	}
	
	/*
		insert işlevi üç değer almaktadır birinci değer tablo adıdır ve değeri metin olarak belirtilmesi gerekmektedir.
		
		ikinci değer veritabanında kayıt yapılacak tablo isimleridir.
			bu değerler dizi olabileceği gibi virgül ile ayrılmış olarak tablo isimleri tek tek de yazıla bilir.
			bu değeler model talbosundaki bilgilere göre geliyorsa data kontrol çalışacaktır aksi takdirde kontrol işlemi yapılmayacaktır.
			
		üçüncü değer veritabanına kayıt edilecek verilerdir.
			bu değer dizi olarak kayıt edilebilir veya değerler insert ifadesine uygun şekilde tek tek yazılabilir.
			veritabanındaki aynı tabloya birden fazla satır veri girilecek ise values değişkeni çok boyutlu dizi olarak tanımlanabilir.
			array(1, 2, 3 ...), array(4,5,6...) veya 0=> array(1,2,3...), 1 => array(4,5,6...)
			
		
		bu fonksiyonda ilk işlem control değişkeni true yapılmış ise verilerin değerlerini kontrol edecektir.
	
	*/
	
	
	public function insert($table, $fields, $values)
	{
		if(array_key_exists("prefix", $this->db) !== FALSE)
		{			
			$table = is_null($this->db["prefix"]) !== FALSE || $this->db["prefix"] != "" ? $this->db["prefix"].$table : $table;
		}
		
		try
		{
			$form = 1;			
			if($this->control === true)
			{
				$form = $this->data_control($fields, $values);
				
				if($form === false)
				{
					throw new Exception ($this->error_message."------");
				}
			}
			
			$query_string = "INSERT INTO " . $this->table_prefix($table) . " (";
			//eğer tablo sutun isimleri değişkeni dizi ise kontrol et
			$ff  = $fields;
			if(is_array($fields) === true)
			{
				$ff = "";
				if(array_key_exists("field", $fields) === false)
				{
					for ($x = 0; $x < count($fields); $x++)
					{
						if(is_array($fields[$x]) !== false)
						{
							if(array_key_exists("field", $fields[$x]) === false )
							{
								if(($x + 1) == count($fields))
									$ff .= $fields[$x];
								else
									$ff .= $fields[$x].", ";	
							}
							else
							{
								if(($x + 1) == count($fields))
									$ff .= $fields[$x]["field"];							
								else
									$ff .= $fields[$x]["field"].", ";
							}
						}
						else
						{
							if(($x + 1) == count($fields))
								$ff .= $fields[$x];
							else
								$ff .= $fields[$x].", ";
						}
					}						
				}
				else
				{
					$ff .= $fields["field"];
				}
			}
				
			$query_string .= $ff. ") VALUES ";
			
			//eğer tabloya kayıt edilecek değerler dizi ise değişkene ata
			$vvv = array();
			$vv = "(?)";
			
			if(is_array($values) === true)
			{
				$vv = "(";
				for ($x = 0; $x < count($values); $x++)
				{
					if(($x + 1) == count($values))
					{
						if(is_array($values[$x]) === true)
						{
							for ($y = 0; $y < count($values[$x]);  $y++)
							{
								if(($y + 1) == count($values[$x])){
									$vvv[] = $values[$x][$y];
									$vv .=  "?)";
								}
								else
								{
									$vvv[] = $values[$x][$y];
									$vv .= "?, ";
								}
							}
						}
						else
						{
							$vvv[] = $values[$x];
							$vv .= "?)";
						}
					}
					else
					{
						if(is_array($values[$x]) === true)
						{
							for ($y = 0; $y < count($values[$x]);  $y++)
							{
								if(($y + 1) == count($values[$x])){
									$vv .= "?), ";
									$vvv[] = $values[$x][$y];
								}
								else{
									$vvv[] = $values[$x][$y];
									$vv .=  "?, ";
								}
							}
							
								$vv .= "(";
						}
						else
						{
							$vv .= "?, ";
							$vvv[] = $values[$x];
						}
					}
				}	
			}
			else {
				$vvv[] = $values;}
			
			$query_string .= $vv;

			if($this->select_query === false)
			{
				return $query_string;
			}
		
			$save = parent::prepare($query_string);
			$query = $save->execute($vvv);
			
			if($query === false)
			{	
				$error = $save->errorInfo();			
				throw new Exception($error[2]);
			}
			else
			{
				return parent::lastInsertId();
			}
			
		}
		catch (Exception $err)
		{
			$this->error_message = $this->error_message . "<br>File : " . __FILE__ . "<br>Line : " . $err->getLine();
			log_save("DBPDO ERROR", lang("2.title"), "<br>Error Message :" . $err->getMessage());
			return false;
		}
		
	}
	/**
		veritabanı bilgi güncelleme sadece tablo değer ismini metin ifadesi şeklinde almaktadır 
		bunun haricinde $this->values değeri ile güncellenecek yeni değerlerin tanımlanması yapılmaktadır.
		$this->field değişkeni ile de güncelleme yapılacak tablo sutun bilgisi isimleri gelmektedir.
			filed değeri model sayfasındaki bilgilerden alınıyorsa ve values değişkeni dizi ise 
			data-control değişkeni ile bilgiler model sayfasındaki özelliklere göre uygunluğu kontrol edilmektedir. 
				tabi $this->data_control değeri true olması gerekmektedir.
		$this->where_edit() fonksiyonu ile şart ifadesi tanımlanabilmektedir.
	*/
	public function update($table)
	{
		try
		{
			
			$form = 1;
			
			$value = $this->values;
			$form = 1;			
			if($this->control === true)
			{
				$form = $this->data_control($this->field[1], $value);
				if($form === false)
				{
					throw new Exception ($this->error_message);
				}
			}

			$query_string = "UPDATE " . $this->table_prefix($table) . " SET ";			
			$ff  = "";
			//eğer tablo sutun isimleri değişkeni dizi ise kontrol et
				
			if(is_array($this->field[1]) === true)
			{
				if(array_key_exists("field", $this->field[1]) === false)
				{
					for ($x = 0; $x < count($this->field[1]); $x++)
					{
						if(is_array($this->field[1][$x]) === true)
						{
							if(array_key_exists("field", $this->field[1][$x]) === false)
							{
								if(($x + 1) == count($this->field[1]))
									$ff .= $this->field[1][$x] . "=? ";
								else
									$ff .= $this->field[1][$x] . "=? , ";	
							}
							else
							{
								$table = array_key_exists("table", $this->field[1][$x]) != false ? $this->table_prefix($this->field[1][$x]["table"]) ."." : "";

								if(($x + 1) == count($this->field[1]))
									$ff .= $table.$this->field[1][$x]["field"] . "=? ";							
								else
									$ff .= $table.$this->field[1][$x]["field"] . "=? , ";
							}
						}
						else
						{
							if(($x + 1) == count($this->field[1]))
								$ff .= $this->field[1][$x] . "=? ";
							else
								$ff .= $this->field[1][$x] . "=? , ";
						}
					}			
				}
				else
				{
					$table = array_key_exists("table", $this->field[1]) != false ? $this->table_prefix($this->field[1]["table"]) ."." : "";
					$ff .= $table . $this->field[1]["field"] . "=? ";
				}
			}	
			else{
				$ff .= 	$this->field[1] . "=? ";
			}
			
			$query_string .= $ff;
			
			if($this->where === false)
			{
				return 	$this->where;
			}
			
			$wheree = $this->where;
			if(array_key_exists("string", $this->where) === true)
			{
				$query_string .= $wheree["string"];
			}


			$values = array();
			
			if(is_array($value) === true)
				$values = array_merge($values, $value);							
			else
				$values[] = $value;	
			
			if(count($wheree["values"]) > 0 && array_key_exists("values", $this->where) === true)
			{
				$values = array_merge($values, $wheree["values"]);		
			}
			
			if($this->select_query === false)
			{
				return $query_string;	
			}
			else
			{
				$save = parent::prepare($query_string);
				$save->execute($values);
				
				if($save === false)
				{
					$error = $save->errorInfo();
					throw new Exception ($error[2]);
				}
				else
				{
					return $save->rowCount();
				}	
			}
			
		}
		catch (Exception $err)
		{
			$this->error_message = $err->getMessage(). "<br>File : " . __FILE__ . "<br>Line : " . $err->getLine();
			log_save("DBPDO ERROR", lang("2.title"), $this->error_message);
			return false;
		}
	}	
	/*
		veritabanında bilgi silme değeri tablo isimini almaktadır sadece
		
		$this->where_edit() işlevi ile silincecek satır bilgilerini almaktadır.
	
	*/
	public function delete($table)
	{			
		$field = "";
		if(is_array($table) === false)
		{
			$table = $this->table_prefix($table);			
		}
		else
		{
			if(count($table) < 3)
				$table[] = "INNER JOIN ";
			
			$field = $this->table_prefix($table[0]) . ", " . $this->table_prefix($table[1]) . " ";
			$table = $this->table_prefix($table[0]) . " " . $table[2] . " " . $this->table_prefix($table[1]) . " ";	
			
		}
		
		$query_string = "DELETE " . $field . "FROM " . $table . " " . $this->on . " ";
		
		$where = $this->where;
		
		if($where === false || array_key_exists("string", $where) === false)
		{			
			return false;
		}
		
		$query_string .= $where["string"];
		
		$save = parent::prepare($query_string);
		$save->execute($where["values"]);
		
		if($save === false)
		{
			$error = $save->errorInfo();
			$this->error_message = $error[2]. "<br>File : " . __FILE__ . "<br>Line : " . $err->getLine();
			log_save("DBPDO ERROR", lang("2.title"), $this->error_message);
			return false;
		}
		else
		{
			if($this->select_query === false)
				return $query_string;
			else
				return $save->rowCount();
		}
	}
	
	
	
	function __destruct()
	{
			$this->where = NULL;
			$this->field = NULL;
			$this->limit = NULL;
			$this->asc = NULL;
			$this->desc = NULL;
			$this->func = "SELECT";
			$this->control = false;
			$this->select_query = TRUE;
			$this->on = NULL;
			
		parent::ATTR_ORACLE_NULLS;
	}
	
}

?>