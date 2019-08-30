<?php

//die(phpinfo());

class cnf_db
{
	public $dbValues = array(
	0 => 
		array(
			'host'			=>'127.0.0.1',
			'driver' 		=> 'PDO',
			'dbUser'		=> 'root',
			'dbPass' 		=> '',
			'dbName'		=> '',
			'car_Set'		=> '',
			'dbcheck' 		=> '',	
			'ssl' 			=> false,
			'opt' 			=> array(),
			'prefix'		=> "",
		)
	);
	
	
	private function __cunstruct()
	{
		if(is_array($this->dbValues) === FALSE)
		{
			log_Save("DB Error", "Error", "Not defined, any database");	
		}
	}
}
?>