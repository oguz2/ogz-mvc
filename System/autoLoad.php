<?php
defined("systemPath") or die("Access Denied!!! => 'AUTOLOAD'");
defined("configPath") or die("Access Denied!!! => 'AUTOLOAD'");
defined("libraryPath") or die("Access Denied!!! => 'AUTOLOAD'");
defined("basePath") or die("Access Denied!!! => 'AUTOLOAD'");

if(file_exists(configPath . "autoLoad.php") === false)
{
	die("'config/autoload' file did not found on your system.");	
}
else
{
	require_once(configPath . "autoLoad.php");
}

class autoLoad extends cnf_autoLoad
{
	public $error_message = NULL;
	public function url_page_load()
	{
		$this->class_load("url");
	}
	
	public function page_load($filename, $folder = NULL )
	{
		/*
			$folder değişkeni tanımlanır ise sisytem üzerinde dosyayı konuma göre ara
			$folder değişkeni tanımlanmaz ise
				config dosyası içerisindeki $in_system_file_map değişkenine bakar dizi ilk anahtar kelimeleri ile girilen değer aynı ise
				o dizideki değerlere yönelik olarak 
				library, system ve config klasörleri içersindeki dosyaları yükler
			çıktı	
				dosya bulunursa dosyayı yükler ve herhangi bir değer döndürmez
				dosya bulunmaz ise hata mesajı basar ve false değerini döndürür.
				
		*/
			
		if($folder === NULL)
		{
			$values = $this->in_system_file_map($filename);
			if($values !== FALSE)
			{
				$system_file_name = $filename;
				$config_file_name = $filename;
				$library_file_name = $filename;
				
				
				if(array_key_exists("config_file_name", $values) !== false && empty($values["config_file_name"]) !== true)
				{
					$config_file_name = $values["config_file_name"].".php";	
					if(file_exists(configPath.$config_file_name) === TRUE)
					{
						//sadece config içerisindeki dosyaları yüklemek için
						require_once(configPath.$config_file_name);				
					}
					else
					{
						$this->error_message = $filename . " File did not found" . "File : " . __FILE__ . "Line : " . __LINE__;
						log_save("Auto Load page", "File Not Fount", $this->error_message);
						die($this->error_message);
						return false;
					}
				}
				else
				{
					//değer config / autoload dosyası içerisinde tanımlanmamış ise $filename değerindeki isimlere bak 
					//dosya yok ise hiçbir işlem yapma
					$config_file_name = $filename.".php";	
					if(file_exists(configPath.$config_file_name) === TRUE)
					{
						//sadece config içerisindeki dosyaları yüklemek için
						require_once(configPath.$config_file_name);				
					}	
				}
				//-----------------------------------------------------------------------
				//config dosyası dahil edildi
				
				if(array_key_exists("system_file_name", $values) !== false && empty($values["system_file_name"]) !== true)
				{
					$system_file_name = $values["system_file_name"].".php";	
					if(file_exists(systemPath.$system_file_name) === TRUE)
					{
						//system ve config dosyalarını yüklemek için
						require_once(systemPath.$system_file_name);
					}
					else
					{
						$this->error_message = $filename . " File did not found" . "File : " . __FILE__ . "Line : " . __LINE__;
						log_save("Auto Load page", "File Not Fount", $this->error_message);
						die($this->error_message);
						return false;
					}
				}
				else
				{
					//değer config / autoload dosyası içerisinde tanımlanmamış ise $filename değerindeki isimlere bak 
					//dosya yok ise hiçbir işlem yapma
					$system_file_name = $filename.".php";	
					if(file_exists(systemPath.$system_file_name) === TRUE)
					{
						//system ve config dosyalarını yüklemek için
						require_once(systemPath.$system_file_name);
					}
				}
				//-----------------------------------------------------------------------
				//sistem dosyası dahil edildi
				
				
				if(array_key_exists("library_file_name", $values) !== false && empty($values["library_file_name"]) !== true)
				{
					$library_file_name = $values["library_file_name"].".php";
					if(file_exists(libraryPath.$library_file_name) === TRUE)
					{
						//library içerisindeki dosyaları yüklemek için
						require_once(libraryPath.$library_file_name);
					}	
					else
					{
						$this->error_message = $filename . " File did not found" . "File : " . __FILE__ . "Line : " . __LINE__;
						log_save("Auto Load page", "File Not Fount", $this->error_message);
						die($this->error_message);
						return false;
					}
				}
				else
				{
					//değer config / autoload dosyası içerisinde tanımlanmamış ise $filename değerindeki isimlere bak 
					//dosya yok ise hiçbir işlem yapma
					$library_file_name = $filename.".php";
					if(file_exists(libraryPath.$library_file_name) === TRUE)
					{
						//library içerisindeki dosyaları yüklemek için
						require_once(libraryPath.$library_file_name);
					}	
				}
				//-----------------------------------------------------------------------
				//library dosyası dahil edildi
				
			}
			else
			{
				//belirtilen dosya in_system_file_map değişkeni içerisinde yok ve folder değişkenide tanımlanmamış
				$this->error_message = $filename . " File did not found" . "File : " . __FILE__ . "Line : " . __LINE__;
				log_save("Auto Load page", "File Not Fount", $this->error_message);
				die($this->error_message);
				return false;
			}	
		}
		else
		{
			//folder ve dosya adı birlikte belirtilmiş ise
			if(file_exists($folder.$filename) === FALSE)
			{
				//belirtilen dosya bulunamıyor
				$this->error_message = $filename . " File did not found" . "File : " . __FILE__ . "Line : " . __LINE__;
				log_save("Auto Load page", "File Not Fount", $this->error_message);
				die($this->error_message);
				return false;
			}
			else
			{
				require_once($folder.$filename);
			}
		}
		
	}
	
	public function class_load($fileName, $data = NULL, $folder = NULL, $className = NULL)
	{
		$values = $this->in_system_file_map($fileName);
		if($values !== FALSE && $folder === NULL)
		{
			$loadfile = $this->page_load($fileName);
			if($loadfile === FALSE)
			{
				$this->error_message = $filename . " File did not found" . "File : " . __FILE__ . "Line : " . __LINE__;				
				log_save("Auto Load page", "File Not Fount", $this->error_message);
				return false;
			}
			else
			{
				if(array_key_exists("class_name", $values) === false) 
					$className = $fileName;
				else
					$className = $values["class_name"];
				
				if(class_exists($className) !== FALSE)
				{		
					if($data === false ||$data == NULL || $data == "")
					{
						return new $className;		
					}
					else
					{
						return new $className($data);	
					}
				}
				else
				{
					$this->error_message = $className . "Class did not defined" . "File : " . __FILE__ . "Line : " . __LINE__;
					log_save("Auto Load page", "Class Not Defined", $this->error_message);
					return false;
				}
			}
		}
		else
		{
			if (file_exists($folder.$fileName) !== FALSE)
			{
				require_once($folder.$fileName);
					if($className === NULL) $className = $fileName;
					
				if($data === false || $data == NULL || $data == "")
				{
					return new $className;		
				}
				else
				{	
					return new $className($data);	
				}
			}
			else
			{
				//belirtilen dosya bulunamıyor
				$this->error_message = $fileName . " did not defined" . "File : " . __FILE__ . "Line : " . __LINE__;
				log_save("Auto Load page", "File Not found", $this->error_message);
				return false;
			}
		}
	}
	
}

?>