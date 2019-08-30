<?php
defined("systemPath") or die("Access Denied!!! => 'URI'");
defined("appPath") or die("Access Denied!!! => 'URL'");

class class_url
{
 	public $url = "";
	public $http = "";
	public $URLvalue = "";
	public $url_config_file = "url_config";
	public $error_message = NULL;
	
	public function __construct()
	{
		try
		{
			$this->url  =  $this->URLhttp();
			$this->url .=  $this->URLhost();
			$this->url .=  $this->URLquery(); 
			$this->url =  $this->editURL($this->url);
			
			switch ($this->url_config_values())
			{
				case "error_1":
					throw new Exception("CONFIG/url_config file did not found.");
					break;
				
				case "error_2":
					throw new Exception ("in CONFIG/url_config file, homepage did not defined.");
					break;
				
				default:
					if(isset($this->url_config_values()->construct_url) !== false)
						$this->URLvalue = $this->url_config_values()->construct_url;
					else
						$this->URLvalue = "";
					break;
			}
			
			$this->loader_controller($this->inc_page());
		}
		catch (Exception $e)
		{
			$this->error_message = $e->getMessage()."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("URL File Error", lang("7.title"), $this->error_message);
			return false;
		}
	}
	
	
	
	private function url_config_values()
	{
		if(file_exists(appPath.$this->url_config_file.".php") !== false)
		{
			require_once(appPath.$this->url_config_file.".php");
			$url_con =  new cons_url;
			
			//url içerisince index dizini sayfa numarası mutlaka olması gerekmektedir.
			if(array_key_exists("index", $url_con->construct_url) === FALSE || isset($url_con->construct_url) === FALSE)
			{
				$this->error_message = lang("7.0")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save("URL File Error", lang("7.title"), $this->error_message);
				return false;	
			}
			
			return new cons_url;
			
		}
		else
		{
			$this->error_message = lang("7.1")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("URL File Error", lang("7.title"), $this->error_message);
			return false;	
		}
	}
	public function editURL($val)
	{
		$val = preg_replace("#<script(.*?)>(.*?)</script>#is", "remove", $val);
		$val = strip_tags($val);
		$val = preg_replace("~([^A-Za-z0-9/-\\?=&\.\[\]%#_])~is", "+", $val);
		return strip_tags($val);
	}
 	
	public function URLhttp()
	{
		if($this->http != NULL)	
		{
			return $this->http;	
		}
		else
		{
			if(isset($_SERVER['HTTPS']) !== FALSE)
			{
				return "http://";	
			}
			else
			{
				return "https://";	
			}
		}
	}
	
	public function URLhost()
	{
		if(isset($_SERVER["SERVER_NAME"]) !== FALSE)
		{
			return $_SERVER['SERVER_NAME'];	
		}
		else
		{
			return $_SERVER['HTTP_HOST'];	
		}
	}
	
	public function URLpath()
	{
		//scripttin yüklü olduğu klasör adını verir home/public_html/(dizin/dizin/...)/index.php parantez içerisi
		$val = "";
		if(isset($_SERVER['SCRIPT_NAME']) !== FALSE)
		{
			$val =  $_SERVER['SCRIPT_NAME'];	
		}
		else
		{
			$val =  $_SERVER['PHP_SELF'];	
		}
		
		$val = (string) preg_replace("#[^/]+\.php#mi", "", $val);
		
		return $val;
	}
	
	public function URLquery()
	{
		if(isset($_SERVER['REQUEST_URI']) !== FALSE)
		{
			
			return preg_replace("#[^/]+\.php\?#si", "", $_SERVER['REQUEST_URI']);
		}
		else
		{
			return (string) preg_replace("#[^/]+\.php#i", "", $_SERVER['SCRIPT_NAME']).$_SERVER['QUERY_STRING'];
		}
	}
	
	
	
	public function URLparse()
	{
		$patern = "#".$this->URLhttp().$this->URLhost().$this->URLpath()."([a-zA-Z0-9\-_]*)?/?(.*)#i";
			preg_match($patern, $this->url, $result);
			$val["controller"] = empty($result[1])? NULL : $result[1];
			$val["query"] = empty($result[2])? NULL : $result[2];
			if(stristr($result[2], "&") !== false)
			{
				$exp = @explode("&", $result[2]);
				$t = 0;
				for ($i = 0; $i< count($exp); $i++) 
				{
					if(stristr($exp[$i], "=") !== false)
					{
						$path = @explode("=", $exp[$i], 2);
						$val[$this->editURL($path[0])] = $this->editURL($path[1]);
					}
					else
					{
						$val[$t] = $exp[$i];	
						$t++;
					}
				}
			}
			else if(stristr($result[2], "/") !== false)
			{
				$exp = @explode("/", $result[2]);
				for ($x = 0; $x < count($exp); $x++)
				{
					$val[$x] = $exp[$x];
						$expp = @explode("&", $exp[$x]);
					$t = 0;
					for ($i = 0; $i< count($expp); $i++) 
					{
						if(stristr($expp[$i], "=") !== false)
						{
							$path = @explode("=", $expp[$i], 2);
							$val[$this->editURL($path[0])] = $this->editURL($path[1]);
						}
						else
						{
							$val[$t] = $expp[$i];	
							$t++;
						}
					}
				}
			}	
			else 
			{
				if(stristr($result[2], "=") !== false)
				{
					$path = @explode("=", $result[2], 2);
					$val[$this->editURL($path[0])] = $this->editURL($path[1]);
				}
			}
			return $val;
	}
	
	public function errorPage($data)
	{
		//hata sayfası mevcut ise hata sayfasını yükler mevcut değilse 
		//index sayfasını yükler
		$cont = "";
		if(is_null($this->URLvalue) !== false)
		{
			 if(array_key_exists("errorPage", $this->URLvalue) === false)
			 {
				 $cont = "index";
			 }
			 else
			 {
				$cont = "errorPage"; 
			 }
		}
		else
		{
			$cont = "index";
		}
		
		$file_name = 	array_key_exists("file_name", $this->URLvalue[$cont]) 
					 	&& !is_null($this->URLvalue[$cont]["file_name"]) ? 
					 	$this->URLvalue[$cont]["file_name"] : $cont;
		$file_name .=	".php";
					 
		$class_name = 	array_key_exists("class_name", $this->URLvalue[$cont]) 
				 	  	&& !is_null($this->URLvalue[$cont]["class_name"])? 
				 		$this->URLvalue[$cont]["class_name"] : $cont."_class";
			
		$func_name = 	array_key_exists("function_name", $this->URLvalue[$cont]) &&
						!is_null($this->URLvalue[$cont]["function_name"]) ?
						$this->URLvalue[$cont]["function_name"] : $cont . "_func";
		
		if(file_exists(appPath."Controller".DIRECTORY_SEPARATOR.$file_name) !== FALSE 
			&& file_exists(systemPath."controller.php") !== FALSE 
			&& file_exists(appPath."globals.php") !== FALSE)
		{
			require_once(appPath."globals.php");
			require_once(systemPath."controller.php");
			require_once(appPath."Controller".DIRECTORY_SEPARATOR.$file_name);
			if(class_exists($class_name) !== FALSE)
			{
				$controller_class = new $class_name($data);
				if(method_exists($controller_class, $func_name) !== FALSE)
				{
					return $controller_class->$func_name();
				}
				else
				{
					$this->error_message = lang("7.2")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
					log_save("URL File Error", lang("7.title"), $this->error_message);
					die($this->error_message);
					return false;	
				}
			}
			else
			{
				$this->error_message = lang("7.3")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save("URL File Error", lang("7.title"), $this->error_message);
				die($this->error_message);
				return false;
			}
			
		}
		else
		{
			$this->error_message = lang("7.4")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("URL File Error", lang("7.title"), $this->error_message);
			die($this->error_message);
			return false;	
		}
				
	}
	
	public function inc_page()
	{
		$controller = "";
		$file_name = "";
		$class_name = "";
		$function_name = "";
			
			$values = $this->URLparse();
		//url2deki conrollerdan sonraki sorgu bölülerini ilgili değişkene ata
		//bu değişken controller sayfasındaki clasa gönderilecek		
		$url_query = $values["query"];
			
		if($this->URLvalue != "")
		{
			if(is_null($values["controller"]) === FALSE)
			{
				//controller değeri null değil gösterilecek sayfayı 
				//url config dosyasındaki değerlerden uygun olan kontroller seçilmesi için 
				//değişkeni ayarla
				$controller = trim($values["controller"]);
			}
			else
			{
				$controller = "index"; 
			}
			
			//hata sayfasının belirlenmesi 
			//public/url_config içersindek dizide ilgili kontrol yoksa
			// errorPage ismini ara bu isim de yoksa home değişkenine yönlendir
			if(array_key_exists($controller, $this->URLvalue) === false)
			{
				if(array_key_exists("errorPage", $this->URLvalue) !== false)
				{
					$controller = "errorPage"; 
				}
				else
				{
					$controller = "index";	
				}
			}	
			
			//dosya, class ve method isimlerini ara
			//ilgili isimler yoksa dizideki değişkenlerde yoksa 
			//controller ismini bu değerlere ata
			//dosya ismi => file_name
			//class_name
			//function_name
			//file_name
			if( array_key_exists("file_name", $this->URLvalue[$controller]) !== false 
				&& is_null($this->URLvalue[$controller]["file_name"]) === false)
				$file_name = $this->URLvalue[$controller]["file_name"];
			else
				$file_name = $controller;
			
			//class_name
			if(array_key_exists("class_name", $this->URLvalue[$controller]) !== false 
					&&  is_null($this->URLvalue[$controller]["class_name"]) === false)
				$class_name = $this->URLvalue[$controller]["class_name"];
			else
				$class_name = $controller."_class";
			
			//function_name
			if(array_key_exists("function_name", $this->URLvalue[$controller]) !== false && 
				is_null($this->URLvalue[$controller]["function_name"]) === false)
				$function_name = $this->URLvalue[$controller]["function_name"];
			else
				$function_name = $controller."_func";
			
		}
		else
		{
			$controller = is_null($values["controller"])? "index":$values["controller"];
			$file_name = is_null($values["controller"])? "index":$values["controller"];
			$class_name = is_null($values["controller"])? "index":$values["controller"];
			$function_name = is_null($values["controller"])? "index":$values["controller"];
			
			$class_name .= "_class";
			$function_name .= "_func";
		}
		
		return array(
			"controller" => $controller,
			"file_name" => $file_name,
			"class_name" => $class_name,
			"function_name" => $function_name,
			"urlQuery" => $url_query
		);
	}
	
	public function loader_controller($values = array())
	{
		if(is_null($values) === true || is_array($values) === false)
		{
			$values = array(
				"controller" 	=> "index",
				"file_name" 	=> "index",
				"class_name" 	=> "index_class",
				"function_name" => "index_func",
				"urlQuery" 		=> ""
			);
		}
		
		if(array_key_exists("controller", $values) === false)
			$values["controller"] = "index";
		
		if(array_key_exists("file_name", $values) === false || is_null($values["file_name"]) === true)
			$values["file_name"] = "index";
		
		if(array_key_exists("class_name", $values) === false || is_null($values["class_name"]) === true)
			$values["class_name"] = "index_class";
			
		if(array_key_exists("function_name", $values) === false || is_null($values["function_name"]) === true)
			$values["function_name"] = "index_func";
			
		//public/controller/sayfa sını dahil et
		$values["file_name"] .= ".php";
		
		if(file_exists(appPath."Controller/".$values["file_name"]) !== false 
			&& file_exists(systemPath."controller.php") !== false
			&& file_exists(appPath."globals.php") !== false)
		{
			require_once(appPath."globals.php");
			require_once(systemPath."controller.php");
			require_once(appPath."Controller".DIRECTORY_SEPARATOR.$values["file_name"]);
			
			
		 	if(class_exists($values["class_name"]) !== FALSE)
			{				
				$result_class = new $values["class_name"]($this->URLparse());
				if(method_exists($result_class, $values["function_name"]) !== false)
				{
					$funcNAME = $values["function_name"];
					return $result_class->$funcNAME();	
				}
				else
				{
					$this->error_message = lang("7.2")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
					log_save("URL File Error", lang("7.title"), $this->error_message);
					return false;
				}
			}
			else
			{
				$this->error_message = lang("7.3")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save("URL File Error", lang("7.title"), $this->error_message);
				return false;	
			}
			
		}
		else
		{
			//hiçbirşey bulunamadıerrorPage Sayfasını Yükle
			//error Page sayfası yoksa index'i yükle
			
			$error = $this->errorPage($this->URLparse());
			
			if($error === FALSE)
			{
				$this->error_message = lang("7.title")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save("URL File Error", lang("7.title"), $this->error_message);
				return false;
			}
		}
	}
}


?>