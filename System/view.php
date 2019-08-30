<?php
defined("systemPath") or die("Access Denied!!! => 'SYSTEM/View'");
defined("viewPath") or die("Access Denied!!! => 'SYSTEM/View'");
class view_class extends globals_Var
{
	public $data = "";
	function __construct($file, $values = "")
	{
		$this->data = $values;
		if(file_exists(viewPath.$file.".php") !== false)
		{
			require_once(viewPath.$file.".php");
		}
		else
		{
			error_control(array(false, "error_report_code" => "5.0"), array("file_name" => $file));
			return array(false, "error_report_code" => "5.0");	
		}
	}
	
	function inc_page($fileName)
	{		
		//dosya layouts klasörünün içerisinde bulunmalıdır
		//layouts klasörü içerisinde tek dosya ise dosya ismi yazılması yeterli
		//layouts klasörü içerisinde farklı bir alt klasörde ise klasör ismi ile belirtilmesi gerekmektedir.
		$file_PATH = viewPath."layouts/".$fileName.".php";
		
		if(file_exists($file_PATH) !== false)
		{
			require_once($file_PATH) ;
		}
		else
		{
			log_Save("layouts File ", "File_error",  "layouts/(".$fileName.") did not found.");
			return array(false, "error_report_code" => "5.1");	
		}
		$data = NULL;
	}
	
	public function site_title()
	{
		echo "<title>";
		if(array_key_exists("site_title", $this->data) !== false)
		{
			echo $this->data["site_title"];	
		}
		else
		{
			echo $this->site_default_title;
		}
		echo "</title>";
	}
	
	public function url()
	{
		if(array_key_exists("url_query", $this->data) !== false)
		{
			return $this->data["url_query"];	
		}
	}
	
	public function inc_css($fileName)
	{
		$folder = $this->content_folder_name;
		$fileName .= ".css";
		echo '<link rel="stylesheet" type="text/css" href="'.$this->site_address.$folder."css/".$fileName.'">';
	}
	
	public function inc_js($fileName)
	{
		$folder = $this->content_folder_name;
		$fileName .= ".js";
		echo '<script type="text/javascript" src="'.$this->site_address.$folder."js/".$fileName.'"></script>';
	}
	
	public function meta()
	{
		if(array_key_exists("meta_tags", $this->data) === false)
		 return false;
		 
		 if(is_array($this->data["meta_tags"]) === false)
		 	return false;
			
		echo "\n";
		foreach ($this->data["meta_tags"] as $key => $values)
		{
			$meta = '<meta ';						
			if($key == "charset"){
				$meta .= $key .'="'.$values.'" ';}
			if($key == "words"){
				$meta .= 'name="keywords" content="'.$values.'"';}
			if($key == "desc"){
				$meta .= 'name="description" content="'.$values.'"';}
			
			$meta .= ">";	
			echo $meta."\n";
		}
	}
	
	public function load_model($file_name, $class_name = NULL)
	{
		if(file_exists(systemPath."Model.php") !== false)
		{
			require_once(systemPath."Model.php");
			if(file_exists(modelPath.$file_name.".php") !== false)
			{
				require_once(modelPath.$file_name.".php");	
				//page_load($file_name.".php", modelPath);
				$class = $class_name === NULL ? $file_name: $class_name;
				if(class_exists($class) !== false)
				{
					return new $class;
				}
				else
				{
					log_Save("Model Class", "Error",  "Model class (".$class.") did not found.");					
				}
			}
			else
			{
				log_Save("Model Class", "Error", $file_name . " file did not found.");		
			}
		}
		else{
			log_Save("Model File", "Error", "System Model file did not found.");	
		}
	}
	
	public function js($fileName, $path = NULL)
	{
		$default_content_folder = $this->site_address.$this->content_folder_name;
		$fileName = is_null($fileName) === FALSE  || empty($fileName) === FALSE ? $fileName : "index";
		$path = is_null($path) === FALSE  || empty($path) === FALSE? $path.$fileName.".js" : $default_content_folder."js/".$fileName.".js";
		echo '<script src="'.$path.'" type="text/javascript"></script>';
		
	}
	
	public function image($fileName, $path = NULL)
	{
		$default_content_folder = $this->site_address.$this->content_folder_name;		
		$fileName = is_null($fileName) === FALSE  || empty($fileName) === FALSE ? $fileName : "index";		
		$path = is_null($path) === FALSE  || empty($path) === FALSE? $path.$fileName : $default_content_folder."images/".$fileName;		
		echo $path;
		
	}
	
	public $items = NULL;
	public function textBox($data, $array = "")
	{
		$arr = "";
		if($this->items != NULL && array_key_exists("name", $this->items) !== false)
		{
			$arr = $this->items["name"];	
		}
		else
		{
			if($array !== "")
			{
				if(gettype($array) == "boolean" )
				{
					$arr = $data["name"]."[]";	
				}
				else
				{
					$arr = $data["name"]."[".$array."]";
				}
			}	
			else
			{
				$arr = $data["name"];
			}
		}
		
		echo "<input";
		if(is_array($data) !== false)
		{
			if(array_key_exists("name", $data) !== false)
			{
			 	echo ' name="'.$arr.'"';
				$id = $data["name"];
				if($this->items !== NULL && is_array($this->items) === true)
					$id = array_key_exists("id", $this->items) !== false ? "" : ' id="'.$data["name"].'"';
				
			}
			
			if(array_key_exists("type", $data) !== false)
			{
				switch ($data["type"])
				{
					case "string": echo ' type="text"'; break;
					case "mail": echo ' type="email"'; break;
					case "number": echo ' type="number"'; break;
					case "tel": echo ' type="tel"'; break;
					case "file": echo ' type="file"'; break;
					case "url": echo ' type="url"'; break;
					case "date": echo ' type="date"'; break;
					case "time": echo ' type="time"'; break;
					case "datetime": echo ' type="datetime"'; break;
					case "pass": echo ' type="password"'; break;
					case "password": echo ' type="password"'; break;
					case "int": echo ' type="number"'; break;
					default: echo ' type="text"';break;
				}
			}
			
			if(array_key_exists("required", $data) !== false)
			{
				echo ' required';
			}
			
			if(array_key_exists("desc", $data) !== false)
			{
				echo ' placeholder="'.$data["desc"].'"';
			}
			
			if(array_key_exists("length", $data) !== false)
			{
				$max_len = array_key_exists("max_len", $data["length"]) !== FALSE ? $data["length"]["max_len"] : "";
				$min_len = array_key_exists("min_len", $data["length"]) !== FALSE ? $data["length"]["min_len"] : "0";
				echo ' pattern=".{'.$min_len.','.$max_len.'}"';
			}
			
			if(is_array($this->items) !== false)
			{
				foreach($this->items as $attribue=>$value)
				{
					echo ' ' . $attribue.'="'.$value.'"';	
				}
			}			
			
		}
		echo '>';
	}
	
	public function textarea($values, $array = "")
	{
		$arr = "";
		if($this->items != NULL && array_key_exists("name", $this->items) !== false)
		{
			$arr = $this->items["name"];	
		}
		else
		{
			if($array !== "")
			{
				if(gettype($array) == "boolean" )
				{
					$arr = $values["name"]."[]";	
				}
				else
				{
					$arr = $values["name"]."[".$array."]";
				}
			}	
			else
			{
				$arr = $values["name"];
			}
		}
		
		echo '<textarea';	
		if(is_array($values) !== false)
		{
			$req = array_key_exists("required", $values) !== false ? " required" : "";
			$desc = array_key_exists("desc", $values) !== false ? ' placeholder="'.$values["desc"].'"' : "";
			$name = array_key_exists("name", $values) !== false ? ' name="'.$arr.'" id="'.$values["name"].$arr.'"' : "";
		}
		
		echo $req.$desc.$name;
		$v = false;
		if($this->items != NULL)
		{
			foreach($this->items as $key => $value)
			{
				if($key != "value")
					echo ' ' . $key . '="' . $value . '"';	
				else
					$v = true;
			}
		}
		if($v !== false)
			echo '>'.$this->items["value"].'</textarea>';
		else
			echo '></textarea>';			
	}
	
	public function select($values, $select = NULL, $array = NULL)
	{
		$atr = "";
		if($this->items != NULL && array_key_exists("name", $this->items) !== false)
			$atr = $this->items["name"];
		else
		{
			if($array !== NULL)
			{
				if(gettype($array) == "boolean" )
					$atr = $values["name"]."[]";					
				else
					$atr = $values["name"]."[".$array."]";				
			}	
			else
				$atr = $values["name"];
		}
		
		if(is_array($values) !== false)
		{
			$req = array_key_exists("required", $values) !== false ? " required" : "";
			$name = array_key_exists("name", $values) !== false ? ' name="'.$atr.'" id="'.$values["name"].$atr.'"' : "";
		}
		
		if(is_null($this->items)  === false && array_key_exists("opt", $this->items) !== FALSE)
		{
			echo '<select'.$req.$name;
			foreach($this->items as $att => $val)			
			{
				if($att != "opt")
					echo ' '.$att.'="'.$val.'"';
			}
			echo '>';
			foreach ($this->items["opt"] as $k=>$v)
			{
				$sel = $k==$select ? " selected" : "";
				echo '<option value="'.$k.'"'.$sel.'>'.$v.'</option>';	
			}
			echo "</select>";
		}
	}
	
	public function go_page($page_name)
	{
		header("Location: " . $page_name . "");	
	}
	
	public function __destruct()
	{
		$this->items = NULL;	
	}
}

?>