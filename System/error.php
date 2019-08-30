<?php
defined("systemPath") or die("Access Denied!!! ERROR");

class error
{
	public $error = 0;
	public $log_save = true; //hataların hepsini log klasörüne veya veritabanına kayıt eder.
	public $show_error_control = false; // hataların hepsini ekrana bastırır kendinden sonraki kodlar çalışmaz error_control için
	public $show_error = false; // hataların hepsini ekrana bastırır kendinden sonraki kodlar çalışmaz error_report için
	
	public function error_control($value, $info = "",  $data = "")
	{	
		if(is_array($value) !== FALSE)
		{
			if(array_key_exists("error_report_code", $value) === TRUE)
			{
				if($value[0] === FALSE)
				{
					$this->error_report($value, $info, $data);				
					return false;
				}
			}
		}
		else
		{
			if($value === false)
			{
				$this->error_report($value, $info, $data);				
				return false;
			}
		}
		
		return $value;
	}
	
	/*
		error_report işlevi ü değer almaktadır.
		Bu işlev ekrana hata kayıtları basmak veya log kayıtları tutmak için kullanılmaktadır.
		Birinci değerdizi olarak gelir ve dizinin ilk elemanı => false ise ve dizi içerisinde 
			error_report_code dizi anahtarı varsa hata işlemine devam eder. Bu değerler yok ise yapılan işlemde hatayla karşılaşılmamıştır. 
			üstte işlev içinde bu yazı geçerlidir. 
		İkinci değer ise alınan hataya yönelik ekrana basılacak messajdır.
			Dizi olarak model sayfasından değer gelirse model dosyasındaki değerlere göre hata mesajı verir.
			metin olarak bir değer girilmiş ise direk bu değeri döndürür
			
		Üçüncü değer ise sadece bu dosya içerisinde kullanılacaktır Log kayıtlarının tutulmasına izin verilmiş ise 
			bu değer log_save() işlevinde true olarak tanımlanmıştır(false olması hata sebebidir.). ve log kayıtları tutulur.
			log kayıtlarının tutulması için diğer dosyalarda ayrıca bir tanımlamaya gerek yoktur.
			error_report işlevindeki ilk iki değişkene yapılacak değer ataması ile bu kayıtlar verilen izne bağlı olarak otomatik olarak yapılır.			
	*/
	
	
	
	public function error_report($value, $info = "", $data = "")
	{
		
		$error_report_code = 0;
		$message = "";
		if(is_array($value) !== FALSE)
		{
			if($value[0] === FALSE && array_key_exists("error_report_code", $value) === TRUE)
			{
				$error_report_code = $value["error_report_code"];
			}
			
		}
		else 
		{
			if($value !== false)
			{
				return false;	
			}
		}
		
		if($error_report_code == 0) $message = lang("a");
		
		$error_report_code = $this->error_code_exp($error_report_code);
		
		if(is_array($data) === FALSE && $data != "")
		{
			if($data != "")
				$message =  $data;
			else
			{
				$message = lang($error_report_code);
			}
		}
		else
		{
			if($error_report_code >= 1 && $error_report_code < 2)
			{
				if(array_key_exists("length", $data) === TRUE)
				{
					$message = $data["length"]["error_message"];	
				}
				else
				{
					$message = lang($error_report_code);
					$message = $this->edit_data($message, $info);	
				}
			}
			elseif($error_report_code >= 2 && $error_report_code < 3)
			{
				$message = lang($error_report_code);
				$message = $this->edit_data($message, $info);				
			}
			
			elseif($error_report_code >= 3 && $error_report_code < 4)
			{
				if(array_key_exists("type", $data) === TRUE)
				{
					$message = $data["type"]["error_message"];	
				}
				else
				{
					$message = lang($error_report_code);
					$message = $this->edit_data($message, $info);
				}
			}
			else if($error_report_code >= 6 && $error_report_code < 7)
			{
				if(array_key_exists("required", $data) === TRUE)
				{
					$message = $data["required"]["error_message"];	
				}
				else
				{
					$message = lang($error_report_code);
					$message = $this->edit_data($message, $info);	
				}
			}			
			else 
			{
				$message = lang($error_report_code);
					$message = $this->edit_data($message, $info);
			}
			
		}
		
		if($this->log_save === false)
		{
			if($this->show_error === TRUE)
				echo $message;
		}
		else
		{
			//burası için log save fonksiyonu tanımlanacaktır.
			$title = "";
			$inf = "";
			if(is_array($error_report_code) === true)
			{
				$error_report_code1[0] = $error_report_code[0];
				$error_report_code1[1] = "title";	
				$title = lang($error_report_code1);
				$inf = "ERROR";
				
			}
			else
			{
				$title = lang(array(8,"title"));
				$inf = "User ERROR";
			}
			
			log_Save($title, $inf, $message);			
		}
			
		if($error_report_code[0] >=2 && $error_report_code[0] <=5 && $this->show_error === FALSE)
		{
			//ölümcül hatalar her ne sebeple olursa olsun ekrana bas
			echo $message;
		}
		return $message;
	}
	
	private function error_code_exp($value)
	{
		//double
		if(stristr($value, ".") !== false)
		{
			$exp = @explode(".", $value);
			return $exp;
		}
		else if(stristr($value, ",") !== false)
		{
			$exp = @explode(",", $value);
			return $exp;
		}
		else 
		{
			return $value;	
		}
	}
	
	private function edit_data($data, $info)
	{
		$result = "";
		if(is_array($info) === true)
		{
			
			foreach($info as $key => $value)
			{
				
				$result = preg_replace("#{".$key."}#si", $value, $data);	
			}
		}
		else {
			$result = $data;}
			
		return $result;
	}
		
}

?>