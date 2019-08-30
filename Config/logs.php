<?php
defined("logsPath") or die("Access Denied =>'SYSTEMLOGS'");


class logs_conf
{
	public $logFolderPath = logsPath;
	public $logFileExt = ".lg";
	public $is_Error_Save = 1; 
	public $show_error_message = true;
}

?>