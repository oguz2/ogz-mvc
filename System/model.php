<?php
defined("systemPath") or die("Access Denied!!! => 'MODEL'");

class model
{
	
	public $name = NULL;
	public $length = -1;
	public $type = "string";
	public $required = false;
	
	public function model_data()
	{
		$result = array();
		if($this->name === NULL || $this->name == "")	
		{
			error_control(array(false, "error_report_code" => "3.0"));
			return array(false, "error_report_code" => "3.0");
		}
		else
		{
			$result["name"] = $this->name;	
		}
		
		if($this->length !== NULL || empty($this->length) === FALSE)
		{
			if(is_array($this->length) === TRUE && $this->length != -1)
			{
			   $result["lenght"] =array_key_exists("min_len" ,$this->length)? array("min_len" => $this->length["min_len"]): array("min_len" => -1);
			   $result["lenght"] =array_key_exists("max_len" ,$this->length)? array("max_len" => $this->length["max_len"]): array("max_len" => -1);
			   $result["lenght"] =array_key_exists("error_message" ,$this->length)? array("error_message" => $this->length["error_message"]): "";				
			}
			else
			{
				$result["lenght"] = array("max_len" => $this->length);
			}
		}
		
		if($this->type === NULL || empty($this->type) === TRUE || is_int($this->type) === false)
		{
			$result["type"] = "string";	
		}
		else
		{
			$result["type"] = $this->type;	
		}
		
		if(is_bool($this->required) === true )
		{
			$result["required"] = $this->required;	
		}
		else 
		{
			$result["required"] = is_array($this->required)? $this->required : false;	
		}
		
		return false;
	}
	
}

?>