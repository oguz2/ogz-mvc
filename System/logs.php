<?php
defined("logsPath") or die("Access Denied =>'SYSTEMLOGS'");

class errorLogs extends logs_conf
{
	
	public function errorSave($title, $info, $message)
	{
		if($this->is_Error_Save != 0)
		{
			$this->error_save_file($title, $info, $message);			
		}
	}
	
	public function show_error($message)
	{
		if($this->show_error_message ===  true)
		{
			echo  "<br>".$message."<br>";	
		}
	}
	
	private function error_save_file($title, $info, $message)
	{
		if(!is_dir($this->logFolderPath) or !isset($this->logFolderPath))
		{
			echo "LOG folder path is not valid.";die();			
		}
		else
		{
			if(!isset($title) or !isset($message) )
			{
				echo "Error info does not save, firstly be define error info";		
			}
			else
			{
				$fileName = date("Y_m_d").$this->logFileExt;
				if(!file_exists($this->logFolderPath.$fileName))
				{
					if(touch($this->logFolderPath.$fileName) === false)
					{
						echo "The Error File did not create on your system.";	
						die;
					}					
				}
				
				$file = @fopen($this->logFolderPath.$fileName, "a");
				$write = date("Y.m.d H:i:s")." <---> ". $title. "<--->".$info."\n       ". $message."\n\n";
				if(fwrite($file, $write) === false)
				{
					echo "File does not writeable";	
				}
				fclose($file);
					//if($info != "info"){exit;}
			}
		}
		
	}
	
	public function removeFile($fileName)
	{
		if(unlink($this->logFolderPath.$fileName))
		{
			return true;	
		}
		else
		{
			return false;
		}
	}
	
	
	
}

?>