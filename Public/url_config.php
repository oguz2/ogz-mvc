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
		
	);
}

?>
