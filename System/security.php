<?php
defined("systemPath") or die("Access Denied!!! 'SECURITY'");
class security extends cnf_scrty
{
	public $sec_js = 2;
	public $sec_html_control = 2;
	public $sec_html_tags = 2;
	public $sec_edit_chars = 2;
	public $sec_chars_control = 2;
	
	public function __construct($data = array())
	{
		if(count($data) > 0)
		{
			$this->sec_js = $data["sec_js"] ;	
			$this->sec_html_control = $data["sec_html_control"];
			$this->sec_html_tags = $data["sec_html_tags"];
			$this->sec_edit_chars = $data["sec_edit_chars"];
			$this->sec_chars_control = $data["sec_chars_control"];
		}
		
			$this->sec_js = $this->sec_js === 2 ? $this->js : $this->sec_js;			
			$this->sec_html_control = $this->sec_html_control === 2 ? $this->html_control : $this->sec_html_control;			
			$this->sec_html_tags = $this->sec_html_tags === 2 ? $this->html_tags : $this->sec_html_tags;			
			$this->sec_edit_chars = $this->sec_edit_chars === 2 ? $this->edit_chars : $this->sec_edit_chars;			
			$this->sec_chars_control = $this->sec_chars_control === 2 ? $this->chars_control : $this->sec_chars_control;			
	}
		
	public function xss_secure($value) 
	//değer, izin verilen html tagları, javascript işlem çeşidi
	{
		$this->sec_html_tags = $this->sec_html_tags == "" || $this->sec_html_tags === NULL ? NULL : $this->sec_html_tags;
		$this->sec_edit_chars = $this->sec_edit_chars == ""  || $this->sec_edit_chars === NULL ? NULL : $this->sec_edit_chars;
		
		//js değişeni
		// null => <script..>....<script> siler
		// true => <script> tagını siler
		// false => hiçbir kontrol yapmaz
		$value = $this->js_secure($value); 
		
		//html_control true => html kontrolü yap; false => ise html kontrolü yapma
		$value = $this-> edit_char($value);
		
		//html_control true => html kontrolü yap; false => ise html kontrolü yapma
		//js değişkeni false=> javascript kontrolü yapma diğer bütün değerlerde kontrol edilir ve html js event2leri silinir
		$value = $this->xss_html_Tags ($value);
		
		return $value;
	}
	
	public function string_preg_edit($str)
	{
		$str = str_replace("#", "\#", $str);
		$str = str_replace("(", "\(", $str);
		$str = str_replace(")", "\)", $str);
		$str = str_replace("?", "\?", $str);
		$str = str_replace("|", "\|", $str);
		$str = str_replace("^", "\^", $str);
		$str = str_replace("\$", "\\$", $str);
		$str = str_replace("&", "\&", $str);
		$str = str_replace("*", "\*", $str);
		$str = str_replace("+", "\+", $str);
		$str = str_replace(".", "\.", $str);
		
		
		return $str;	
	}
	
	public function js_secure($value) //değer, javascript işlem çeşidi, izin verilen html tagları
	{
		
		//$this->js = true ise sadece <script> tagını sil, içeriği kalsın
		//$this->js = null ise <script>....<\/script> tagları ve içeriğindeki sil
		//$this->js = false ise scriptlerle ilgili hiçbir işlem yapma
		if($this->sec_js === FALSE)
		{
			return $value;	
		}
		else
		{
			$i = 0;		
			while(preg_match("/<\/?script(.*?)([^<])>/si", $value) !== 0)
			{
				if($this->sec_js === NULL)
				{
					$value = preg_replace("/<script(.*?)>(.*?)<\/?script>/si", "", $value);										
				}
				
				if ($this->sec_js == TRUE || $i > 15)
				{
					$value = preg_replace("/<\/?script(.*?)>/si", "", $value);
				}
				$i++;
			}
			return $value;
		}
	}
	
	//değer, javascript işlem çeşidi, izin verilen html tagları
	//html_control true ise html tagları ile ilgili filtrelemeyi uygular
	//js değeri true ise javascript kontrolü yapar; null ise javascript kontrolü yapmaz
	//html_tags değeri null ise config security dosyasından izin verilen html tagları alır
	//html tagslara değer atanmış ise buradaki değerleri dikkate alır
	public function xss_html_Tags ($value) 
	{
		//izin verilen html taglarını kontrol et	
		//htm_tags = null ise config dosyasından değerleri al
		//html tags tanımlanmış ise buradaki değerlere izin ver
		//html_tag = 0 ise her şeyi sil
		//hiç bir işlem yapamıyorsan yine herşeyi sil
			
		$html_tags = $this->sec_html_tags;
		
		if(isset($this->allow_html_tag) !== false && ($html_tags === 2 || $html_tags === true))//config dosyasından geliyor		
		{
			$html_tags = $this->allow_html_tag;	
		}		
		else if (is_array($html_tags) === true && count($html_tags) > 0)
		{
			$html_tags = $html_tags;	
		}		
		else if ($html_tags !== "")		
		{
			if (stristr($html_tags, ","))
				$html_tags = explode(",", $html_tags);		
			else
				$html_tags = explode(" ", $html_tags);
		}
		else
		{
			$html_tags = "";	
		}
		//tagları al		
		preg_match_all('#<(?<tag>[^<>]*)\s*/?\s*>#i', $value, $str, PREG_SET_ORDER);
		
		//kontroller başlıyor
		for ($i = 0; $i < count($str); $i++)
		{
			if($str[$i]["tag"] === NULL)
			{
				continue;	
			}
			
			
			$edit_html = trim(preg_replace("#[^a-z0-9]#i", " ", $str[$i]["tag"]));
			
			if(stristr($edit_html, " "))
				$edit_html = substr($edit_html, 0, strpos($edit_html, " "));
			
			if($edit_html == "script") continue;
			
			//ilk olarak tagsların geçerliliğini kontrol et
			//html control değeri true ise izin verilen html taglarını kontrol eder
			//htmlcontrol false ise bütün html taglarına izin verir
			
			if($html_tags == "" && is_array($html_tags) === false)
			{
				//hiçbir karaktere izin verilmiyor o yüzden sil
				$value = preg_replace("#<".$this->string_preg_edit($str[$i]["tag"]).">#si", "", $value);
			}
			else
			{
				if(in_array($edit_html, $html_tags) === FALSE && $this->sec_html_control !== FALSE)
				{
					//kontrol edilen html tags'ı izin verilen bir tags değil o yüzden sil
					$value = preg_replace("#<".$this->string_preg_edit($str[$i]["tag"]).">#si", "", $value);
				}
			}
		}
		for ($i = 0; $i < count($str); $i++)
		{	
			//kontrol edilen html tags'ı izin verilen tags'lar arasında ama JS durumunu kontrol et
			//$this->js = true ise on..="" ifadelerini sil
			//$this->js = FALSE ise hiçbir ieşlem yapma
			if($this->sec_js !== FALSE)
			{
				
				//javascript eventlerinı sil
				for($t = 0; $t < count($this->js_att); $t++)
				{
					$value = preg_replace("#(".$this->js_att[$t]."\s*=\s*['|\"]([^<>]*)['|\"])#is", "", $value);
				}
				
				//küçük düzenlemeler
				$value = preg_replace("#( ){2,}#i", "", $value);
			}
		}
		return $value;
	}
	
	public function edit_char($value)
	{
		//kontrol etmek istenmiyorsa bizde konrol etmeyiz
		if($this->sec_chars_control === FALSE) 
		{
			return $value;
		}
		$edit_chars = $this->sec_edit_chars;
		
		if(($edit_chars === 2 || $edit_chars === TRUE) && is_array($edit_chars) === false)
		{
			$edit_chars = $this->edit_char;
		}
		
		if($edit_chars !== NULL || is_array($edit_chars) !== false)
		{		
			foreach($edit_chars as $char => $new_char)
			{
				$value = preg_replace("#".$this->string_preg_edit($char)."#is", $new_char, $value);
			}
		}
		return $value;
	}
	
	public function __destruct()
	{
		$this->sec_js = 2;
		$this->sec_html_control = 2;
		$this->sec_html_tags = 2;
		$this->sec_edit_chars = 2;
		$this->sec_chars_control = 2;
	}
	
}

?>