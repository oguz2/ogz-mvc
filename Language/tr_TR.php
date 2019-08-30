<?php
defined("lang") or die("ACCES DENIED!!! LANG");

class language
{
	public $data =array();
	
	public function error_info($array_number)
	{
		$data[0] = "Hata Bilgisi Tanımlanmamıştır.";
		$data[1]["title"] = "Form Hataları.";//form
			$data[1][0] = "Form Alanındaki gerekli verilerin girişini yapınız";
			$data[1][1] = "İzin verilen karakter tipi => {type}";
			$data[1][2] = "Verinin izin verilen karakterler arasında olması gereklidir.";
			$data[1][3] = "En Fazla";
			$data[1][4] = "En az";
			$data[1][5] = 'Form $_POST tanımlanması yapılmamıştır.';
			$data[1][6] = 'Veri tipi uyuşmazlığı';
	 	$data[2]["title"] = "Veritabanı Hataları";//veritabanı
			$data[2][0] = "Belirtilen veritabanı numarası ile ilgili veritabanı bilgisi tanımlanmamıştır.(database)";
			$data[2][1] = "Veritabanı sorgu metninde hata yapılmıştır.";
			$data[2][2] = "db()->where değişkeni en az iki değişkenli bir dizi olmak zorundadır veya direk where metini yazılmalıdır.";
			$data[2][3] = "SYSTEM/DATABASE/{driver} dosyası bulunamamıştır. ";
			$data[2][4] = "Veritabanı Bulunamamıştır.{database}";
			$data[2][5] = "Veritabanı veri kayıt hatası.";			
		$data[3]["title"] = "Model Dosyası Hatları";//model
			$data[3][0] = "Modellerde Fonksiyon içlerinde tablo name değişkeni mutlaka belirtilmesi gerekmektedir.";
		$data[4]["title"] = "System/controller hatası.";//controller
			$data[4][0] = "VIEW/{view_file} dosyası bulunamamıştır.";
			$data[4][1] = "MODEL dosyası bulunamamıştır.";
			$data[4][2] = "Belirtmiş Olduğunuz class dosya içerisinde tanımlanmamıştır.";
		$data[5]["title"] = "SYSTEM VIEW dosya hatası.";//view
			$data[5][0] = "VIEW/ dosyası sistem üzerinde bulunamamıştır.";
			$data[5][1] = "VIEW/LAYOUTS/{file_name} dosyası bulunamamıştır.";
			$data[5][2] = "PUBLIC/GLOBALS dosyası belirtilen dizinde bulunamamıştır.";
		$data[6]["title"] = "Lütfen formdaki gerekli alanları doldurunuz";
		$data[7]["title"] = "SYSTEM/URL dosya hatası.";
			$data[7][0] = "view/URL_config Klasöründe index değişkeninin tanımlanması gerekmektedir.";//url
			$data[7][1] = "VIEW/URL_CONFIG dosyası bulunamadı.(URL)";
			$data[7][2] = "URL_CONFIG Controller işlevi tanımlanmamıştır.";
			$data[7][3] = "VIEW/URL_CONFIG class tanımlanmamıştır.";
			$data[7][4] = "CONTROLLER/{file_name} dosyası bulunamamıştır.";
		$data[8]["title"] = "Kullanıcı işlem Hatası.";
		$data[9][0] = "Dosya Bulunamamıştır.";
		$data[9][1] = "class_load işlev hatası.";
		
		
		if($array_number == "a")
		{
			return $data[0];
		}
		
		if(is_array($array_number) === FALSE)
		{
			$array_number = $this->error_code_exp($array_number);
		}
		
		if(array_key_exists($array_number[0], $data) !== FALSE)
		{
			if(array_key_exists($array_number[1], $data[$array_number[0]]) !== FALSE)
			{
				return $data[$array_number[0]][$array_number[1]];	
			}
			else
			{
				return $data[$array_number[0]]["title"];	
			}	
		}
		else
		{
			return "Belirtilen numara için Türkçe Dil dosaysında ilgili değişken tanımlanmamıştır. Dil Dosya Numarası =>" . $array_number;
		}
		
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
	
	public function lang($value)
	{
		$data[0] = "";
		$data[1] = "Giridiğiniz kullanıcı adı ve şifre sistemde bulunamamıştır..";
		$data[2] = "Kullanıcı adı hatası.";
		$data[3] = "Kullanıcı şifresi hatası.";
		$data[4] = "Kullanıcı işlemleri işlenirken hata oluştu.";
		$data[5] = "Sayfayı Görüntülemek için yetkinz yoktur.";
		$data[6] = "Sayfayı görüntüleyebilmek için giriş yapmanız gerekmektedir.";
		
		return $data[$value];
	}
	
	
}/*
0 => hata bulunmuyor
1 => Form Data veri uzunluğu hatası
	 1,1 => Maksimum karakter uzunluğu hatası
	 1,2 => Minimum karakteruzunluğu hatası 
	 1,3 => Hem Minimum Hem Maksimum karakter uzunluğu hatası
2 => veri tabanı Hatası		
3 => Form Data Veritpi Uyuşmazlığı		
4 => dosya Bulunamadı
5 => class ismi tanımlanan dosya içerisinde bulunamamıştır.
6 => form alanının doldurulması gerekiyor İnput boş geçilemez
7 => Url Hatları
*/
?>