<?php

class cnf_autoLoad
{	
	
	public function in_system_file_map ($value)
	{
		$in_system_file_map = array(
			"gnr" => array(
				"config_file_name" 			=> "general",
				"class_name" 				=> "general_variables"
			),
			"db" => array(
				"system_file_name" 			=> "database",
				"config_file_name" 			=> "database",
				"class_name"				=> "database"
				),
			
			"security" => array(
				/*"system_file_name" 			=> "security",
				"config_file_name" 			=> "security",
				"class_name"				=> "security"*/
				),
			
			"autoLoad" => array(
				"system_file_name" 			=> "autoLoad",
				"config_file_name" 			=> "autoLoad",
				"class_name"				=> "autoLoad"
				),
			
			"log" => array(
				"system_file_name" 			=> "logs",
				"config_file_name" 			=> "logs",
				"class_name"				=> "errorLogs" 
				),
				
			"form" => array(
				"system_file_name" 			=> "form",
				"config_file_name" 			=> "",
				"class_name"				=> "form_element"
			),
			
			"url" => array(
				"system_file_name"			=> "url",
				"class_name"				=> "class_url"
			),
			
			"view" => array(
				"system_file_name"			=> "view",
				"class_name"				=> "view_class"
			),
			
			"model" => array(
				"system_file_name"			=> "model",
				"class_name"				=> "model"
			)
			
		);	
		
		if(array_key_exists($value, $in_system_file_map) === FALSE)
			return false;
		else
			return $in_system_file_map[$value];
	}
}


?>