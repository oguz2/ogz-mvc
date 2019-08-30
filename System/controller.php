<?php
defined("systemPath") or die("Access Denied!!! => 'SYSTEM/Controller'");
defined("viewPath") or die("Access Denied!!! => 'SYSTEM/Controller'");
defined("modelPath") or die("Access Denied!!! => 'SYSTEM/Controller'");
class controller extends globals_Var
{
	public $url_query = array();
	public $error_message = NULL;
	public function __construct($url = "")
	{	
		$this->url_query = $url;
		parent::__construct(); 	
	}
	
	public function load_view($view_name, $data = array())
	{
		$view_file_path = viewPath.$view_name.".php";
		if(file_exists($view_file_path) === false || file_exists(systemPath."view.php") === false)
		{
			$this->error_message = lang("5.0") . " <br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("Controller File", "View File Not Found", $this->error_message);
			return false;
		}
		else
		{	$data["url_query"] = $this->url_query;
			require_once(systemPath."view.php");
			$view = new view_class($view_name, $data);
		}
	}
	
	public $security = true;
	public $sec_js = 2;
	public $sec_html_control = 2;
	public $sec_html_tags = 2;
	public $sec_edit_chars = 2;
	public $sec_chars_control = 2;
	
	public function is_post($value, $control = "")
	{
		if(is_array($value) === false)
			$value = $value;
		else
		{
			if(array_key_exists("name", $value) === true)
			{
				$value = $value["name"];	
			}
			else
			{
				if(count($value) > 0)
					$value = $value[0];
				else{
					$this->error_message =  lang("1.5") . "<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
					log_save("Controller File", lang("1.title"), $this->error_message);
					return false;
				}
			}
		}
		
		if($this->security === FALSE)
		{
			if($control === "")
			{
				return trim($_POST[$value]);
			}
			else
			{
				$sonuc = isset($_POST[$value]) === true ? true : false;	
				return $sonuc;
			}
		}
		else
		{
			$data["sec_js"] = $this->sec_js;
			$data["sec_html_control"] = $this->sec_html_control;
			$data["sec_html_tags"] = $this->sec_html_tags;
			$data["sec_edit_chars"] = $this->sec_edit_chars;
			$data["sec_chars_control"] = $this->sec_chars_control;
			
			$sec = class_load("security", $data);		
			if($control == "")
			{
				if(isset($_POST[$value]) !== false)
				{
					return trim($sec->xss_secure($_POST[$value]));
				}
				else
				{	
					$this->error_message =  lang("1.5") . "<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
					log_save("Controller File", lang("1.title"), $this->error_message);
					return false;
				}
			}
			else
			{
				$sonuc = isset($_POST[$value]) === true ? true : false;	
				return $sonuc;
			}
		}
	}
	
	public function load_model ($file_name, $method_name = NULL)
	{
		$model_file = modelPath.$file_name.".php";
		
		if(file_exists($model_file) === false)
		{
			$this->error_message =  lang("4.1") . "<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save("Controller File", lang("4.title"), $this->error_message);
			return false;
		}
		else
		{
			require_once(systemPath."Model.php");
			require_once(modelPath.$file_name.".php");	
			$class = $file_name;
			if(class_exists($class) !== false)
			{
				$model_class = new $class;
				if($method_name == NULL)
					return $model_class;
				else
				{	
					if(method_exists($model_class, $method_name) === false)
					{
						$this->error_message =  lang("4.1") . "<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
						log_save("Controller File", lang("4.title"), $this->error_message);
						return false;		
					}
					else {
						return $model_class->$method_name();
					}
				}
			}
			else
			{
				$this->error_message =  lang("4.2") . "<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save("Controller File", lang("4.title"), $this->error_message);
				return false;			
			}		
		}
	}
	
	public function form_element ($data, $values)
	{
		$form = class_load("form");
		return $form->data_control($data, $values);
	}
	
	public function go_page($page_name)
	{
		header("Location: " . $page_name . "");	
	}
	
	
	public function __destruct()
	{
		$this->security = true;
		$this->sec_js = 2;
		$this->sec_html_control = 2;
		$this->sec_html_tags = 2;
		$this->sec_edit_chars = 2;
		$this->sec_chars_control = 2;
	}
}
?>