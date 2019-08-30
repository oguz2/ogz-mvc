<?Php

class cnf_scrty
{
	//js değişeni
	// null => <script..>....<script> siler
	// true => <script> tagını siler
	// false => hiçbir kontrol yapmaz
	public $js = NULL;
	//html_control true => html kontrolü yap; false => ise html kontrolü yapma
	public $html_control = true;
	//html_tags değeri null ise hiç bir html tagı izin verilmez
	//html_tags değeri içerik girilmiş ise bu karakterler izin verilen karakterleri oluşturur
	//html_tags değeri true veya 2 ise allow_html_tag değişkenindeki değerleri alır 
	public $html_tags = NULL;
	public $edit_chars = TRUE;
	public $chars_control = TRUE;
	
	public $edit_char = array(
		'<!--'    	 => '&lt;!--',
		'-->'        => '--&gt;',
		'<?php'   	 => '&lt;&#63;php',
		'<?'   	 	 => '&lt;&#63;',
		'?>'		 => '&#63;&gt;',
		'<%'		 => '&lt;&#37;',
		'%>'		 => '&#37;&gt;',
		
	);	
	
	public $uri_Edit_char = array(
		'/\s+/si' 	 => '+',
		'$'	  		 => '%24',
		'"'			 => '',
		'\''		 => '%2F',
		'('			 => '%28',
		')'			 => '%29',	
		'<'			 => '%3C',
		'>'			 => '%3E',
		';'			 => '%3B',
		'\''		 => '%91',
		'’'			 => '%92',
		'^'			 => '%5E',
		'['			 => '%5B',
		']'			 => '%5D',
		'`'			 => '%60'
	);
	
	//bütün html etiketlerini silmek için null bırakabilirsiniz.
	public $allow_html_tag = array(
		"a", 		"br", 		"div", 		"span", 		"table", 		"tr", 
		"td", 		"tbody",	"img", 		"meta",			"link", 		"li",
		"ol", 		"ul"
	);
	
	
	public $js_att = 
		array(
			"onClick",			"onDblClick",
			"onKeyDown",		"onKeyPress",
			"onKeyUp",			"onMouseDown",
			"onMouseMove",		"onMouseOut",
			"onMouseOver", 		"onMouseUp",
			"onBlur",			"onChange",
			"onFocus",			"onSelect"
		);
	
}	
?>