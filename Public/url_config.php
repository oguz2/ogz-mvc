<?php
defined("systemPath") or die("Access Denied!!! => 'URLCONFIG");

/*
index indeksi mutlaka tanımlanması gerekmektedir. içerisi boş olabilir içerisi boş ise bütün değerler index şeklindedir.
404 gibi hata sayfaları tanımlamak için errorPage değeri kullanılmalıdır.

"url kısmındaki değer" => array(
	file_name => (null ise index değerini alır)(tanımlanmaz ise url değerini alır) veya isteğ bağlı bir değer
	
	class_name => (null ise index değerini alır)(tanımlanmaz ise url değerini alır)
	(nul ve tanımsız bırakılırsa gelen değerlerin sonuna _class eki eklenir)
	veya isteğe bağlı bir değer 
	
	function_name => (null ise index değerini alır)(tanımlanmaz ise url değerini alır)
	(null veya tanımsız bırakılırsa değerin sonuna _func eki eklenir
	veya isteğe bağlı bir değer
)
*/


class cons_url
{	
	public $construct_url = array(
		"index" => array(
			"file_name" => "index",
			"class_name" => "homepage",
			"function_name" => "home"
		),//homepage info (not delete) 
		
		"errorPage" => array(
			"file_name"=>"error", "class_name" => "error_class", "function_name" => "error" ), //error 404 page info 
		
		"giris" => array(
			"file_name"=>"admin_login", "class_name" => "login_user", "function_name" => "login_form" ),
		
		"alanlar" => array(
			"file_name"=>"KP_alanlar", "class_name" => "alanlar", "function_name" => "goruntule" ),
		
		"alanKayit" => array(
			"file_name"=>"KP_alanlar", "class_name" => "alanlar", "function_name" => "islem" ),
		
		"alan_sil" => array(
			"file_name"=>"KP_alanlar", "class_name" => "alanlar", "function_name" => "sil" ),
		
		"programlar" => array(
			"file_name"=>"KP_programlar", "class_name" => "program", "function_name" => "goruntule" ),
		
		"progKayit" => array(
			"file_name"=>"KP_programlar", "class_name" => "program", "function_name" => "kayit" ),
			
		"progSil" => array(
			"file_name"=>"KP_programlar", "class_name" => "program", "function_name" => "sil" ),
		
		"progDurum" => array(
			"file_name"=>"KP_programlar", "class_name" => "program", "function_name" => "durumGun" ),
			
		"moduller" => array(
			"file_name"=>"KP_modul", "class_name" => "modul", "function_name" => "goruntule" ),
		
		"modulKayit" => array(
			"file_name"=>"KP_modul", "class_name" => "modul", "function_name" => "kayit" ),
			
		"modulTur" => array(
			"file_name"=>"KP_modul", "class_name" => "modul", "function_name" => "tur" ),
		
		"modulSil" => array(
			"file_name"=>"KP_modul", "class_name" => "modul", "function_name" => "modulsil" ),
		
		"kaydet" => array(
			"file_name"=>"KP_modul", "class_name" => "modul", "function_name" => "kayit_ve_guncelleme" ),
		
		"icerikSil" => array(
			"file_name"=>"KP_modul", "class_name" => "modul", "function_name" => "Iceriksil" ),
		
		"progModulList" => array(
			"file_name"=>"KP_prog_Modul_Listesi", "class_name" => "p_Modul_Listesi", "function_name" => "goruntule" ),
		
		"PML_sira" => array(
			"file_name"=>"KP_prog_Modul_Listesi", "class_name" => "p_Modul_Listesi", "function_name" => "sira_Guncelle"),
		
		"modulListKayit" => array(
			"file_name"=>"KP_prog_Modul_Listesi", "class_name" => "p_Modul_Listesi", "function_name" => "PL_Kayit"),
		
		"modulListSil" => array(
			"file_name"=>"KP_prog_Modul_Listesi", "class_name" => "p_Modul_Listesi", "function_name" => "L_sil"),		
			
		"kullanici_giris" => array(
			"file_name"=>"user_login", "class_name" => "user_login", "function_name" => "login"),
		
		"login" => array(
			"file_name"=>"user_login", "class_name" => "user_login", "function_name" => "giris_kontrol"),
		
		"kursProgrami" => array(
			"file_name"=>"user_kurs_hazirla", "class_name" => "kurs_listesi", "function_name" => "kursListee"),
		
		"kursBilgi" => array(
			"file_name"=>"user_kurs_hazirla", "class_name" => "kurs_listesi", "function_name" => "kursbilgi"),
		
		"planKayit" => array(
			"file_name"=>"user_kurs_hazirla", "class_name" => "kurs_listesi", "function_name" => "plan_kayit"),	
		
		"planHazirla" => array(
			"file_name"=>"user_KursPlani", "class_name" => "kursPlani", "function_name" => "PlanHazirla"),	
		
		"kursPlanlarim" => array(
			"file_name"=>"user_kurs_hazirla", "class_name" => "kurs_listesi", "function_name" => "user_kurs_plani"),
			
		"planSil" => array(
			"file_name"=>"user_kurs_islemleri", "class_name" => "kursIslem", "function_name" => "plan_Sil"),	
		
		"planOnayla" => array(
			"file_name"=>"user_kurs_islemleri", "class_name" => "kursIslem", "function_name" => "plan_Onay"),	
	
		"kisiselBilgiler" => array(
			"file_name"=>"user_kullanici_islemleri", "class_name" => "user_islemler", "function_name" => "kisisel_bilgiler"),	
		
		"kisiselbilgi_kayit" => array(
			"file_name"=>"user_kullanici_islemleri", "class_name" => "user_islemler", "function_name" => "kisiselbilgi_kayit"),	
		
		"sifre_guncelle" => array(
			"file_name"=>"user_kullanici_islemleri", "class_name" => "user_islemler", "function_name" => "sifre_guncelle"),	
		
		"kullanici_islem" => array(
			"file_name"=>"user_kullanici_islemleri", "class_name" => "user_islemler", "function_name" => "log_goruntule"),	
		
		"yukle" => array(
			"file_name"=>"jquery_load", "class_name" => "jquery_load", "function_name" => "load"),	
		
		"kayit" => array(
			"file_name"=>"user_kayit", "class_name" => "user_kayit", "function_name" => "kayit"),	

		"p_talep" => array(
			"file_name"=>"jquery_load", "class_name" => "jquery_load", "function_name" => "p_talep"),	
		
		"destek" => array(
			"file_name"=>"user_destek", "class_name" => "user_destek", "function_name" => "talepler"),	
		
		"mesaj" => array(
			"file_name"=>"user_destek", "class_name" => "user_destek", "function_name" => "mesajlar"),	
		
		"mesaj_kayit" => array(
			"file_name"=>"user_destek", "class_name" => "user_destek", "function_name" => "kayit"),	
		
		"yeni_destek" => array(
			"file_name"=>"user_destek", "class_name" => "user_destek", "function_name" => "yeni"),	
		
		"destekTalebi" => array(
			"file_name"=>"admin_destek", "class_name" => "admin_destek", "function_name" => "uye_destek"),	
		
		"talepCevap" => array(
			"file_name"=>"admin_destek", "class_name" => "admin_destek", "function_name" => "cevap"),	

		"sss" => array(
			"file_name"=>"user_destek", "class_name" => "user_destek", "function_name" => "sss"),

		"cikis" => array(
			"file_name"=>"index", "class_name" => "homepage", "function_name" => "cikis"),

		"sifreHatirlat" => array(
			"file_name"=>"index", "class_name" => "homepage", "function_name" => "sifreHatirlat"),

		"sifreSifirla" => array(
			"file_name"=>"index", "class_name" => "homepage", "function_name" => "sifreSifirla"),

		"iletisim" => array(
			"file_name"=>"index", "class_name" => "homepage", "function_name" => "iletisim"),


	);
}

?>
