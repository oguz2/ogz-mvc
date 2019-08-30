<?php
defined("systemPath") or die("ACCESS DENIED!!! FORM");

class form_element
{
	public $error_message = NULL;
	private $error_title = "FORM File Error";
	
	public function data_control ($data, $value, $req = true)
	{
		
		if(is_array($value) === TRUE)
		{
			return array(false, 3);
		}
		$max = -1;
		$min = -1;
		if(array_key_exists("length", $data) !== FALSE)
		{
			$max = array_key_exists("max_len", $data["length"]) !== false ? $data["length"]["max_len"] : -1;
			$min = array_key_exists("min_len", $data["length"]) !== false ? $data["length"]["min_len"] : -1;
		}
		
		if(array_key_exists("type", $data) !== FALSE)
		{
			if(is_array($data["type"]) === FALSE)
			{
				$type = $data["type"];
			}
			else
			{
				$type = $data["type"][0];	
			}
		}
		
		if(array_key_exists("required", $data) !== false && $req === TRUE)
		{
			if(is_array($data["required"]) === false)
			{
				if($data["required"] === true)
				{
					if(is_null($value) !== FALSE || $value == "")
					{
						$this->error_message = lang("1.0")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
						log_save($this->error_title, lang("1.title"), $this->error_message);
						return false;
					}
				}
			}
			else
			{
				if($data["required"][0] === true)
				{
					if(is_null($value) === FALSE || $value == "")
					{
						$this->error_message = lang("1.0")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
						log_save($this->error_title, lang("1.title"), $this->error_message);
						return false;
					}
				}
			}
		}
		
		//type of $value
		if($type_control = $this->type_of_value($value, $type) === false)
		{
			return $type_control;
		}
		
		//length size of $value
		$len_control = $this->data_len($value, $max, $min);		
		if($len_control === false)
		{
			log_save($this->error_title, lang("1.title"), $this->error_message);
			return false;
		}
		return $value;
	}
		
	public function type_of_value($value, $type = "string")
	{
		if(strtolower($type) == "int" || strtolower($type) == "integer")
		{
			if(filter_var($value, FILTER_VALIDATE_INT) === FALSE && is_numeric($value) === FALSE)
			{
				$this->error_message = lang("1.1")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save($this->error_title, lang("1.title"), $this->error_message);
				return false;
			}
			return "int";
		}
		else if(strtolower($type) == "mail" || strtolower($type) == "email")
		{
			if(filter_var($value, FILTER_VALIDATE_EMAIL) === FALSE)
			{
				$this->error_message = lang("1.1")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save($this->error_title, lang("1.title"), $this->error_message);
				return false;
			}
			return "mail";
		}
		else if (strtolower($type) == "url")
		{
			if(filter_var($value, FILTER_VALIDATE_URL) === FALSE)
			{
				$this->error_message = lang("1.1")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save($this->error_title, lang("1.title"), $this->error_message);
				return false;	
			}
			return "url";
		}
		else if (strtolower($type) == "ip")
		{
			if(filter_var($value, FILTER_VALIDATE_IP) === false)
			{
				$this->error_message = lang("1.1")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save($this->error_title, lang("1.title"), $this->error_message);
				return false;
			}
			return "ip";
		}
		else if(strtolower($type) == "bool" || strtolower($type) == "boolean")
		{
			if(filter_var($value, FILTER_VALIDATE_BOOLEAN) === false)
			{
				$this->error_message = lang("1.1")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save($this->error_title, lang("1.title"), $this->error_message);
				return false;
			}
			return "boolean";
		}
		else
		{
			return "string";
		}
	}
	
	public function data_len ($value, $max = -1, $min = -1)
	{
			$max_val = $max > 0 ? lang("1.3")." " .$max : " ";
			$min_val = $min > 0 ? " ".lang("1.4")." " . $min : " ";
			
		if((is_numeric($min) === FALSE && $min != NULL) || (is_numeric($max) === FALSE && $max != NULL) )
		{
			//show_error("Max and Min Length may be only numeric char.");
			return array(false, "error_report_code" => "1.2", array("values" => $min_val.$max_val));
		}
		else if($min == -1 && $max == -1)
		{
			return strlen($value);
		}
		else if($max != -1 && ($min == -1 || $min == NULL))
		{
			if(strlen($value) > $max)
			{				
				$this->error_message = lang("1.2")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save($this->error_title, lang("1.title"), $this->error_message);
				return false;
			}
			return strlen($value);
		}
		else if(($max == -1 || $max == NULL) && $min != -1)
		{
			if(strlen($value) < $min)
			{
				$this->error_message = lang("1.2")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save($this->error_title, lang("1.title"), $this->error_message);
				return false;
			}
			return strlen($value);
		}
		else if ($max != -1 && $min != -1)
		{
			if(strlen($value) > $max || strlen($value) < $min )
			{
				$this->error_message = lang("1.2")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
				log_save($this->error_title, lang("1.title"), $this->error_message);
				return false;
			}			
			return strlen($value);
		}
		else
		{
			$this->error_message = lang("1.2")."<br>File : " . __FILE__ . "<br>Line : " . __LINE__;
			log_save($this->error_title, lang("1.title"), $this->error_message);
			return false;
		}
	}
}

?>