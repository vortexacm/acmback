<?php
/* -----------------------------------------------------------------------------------------#
|		TORPIX.NET - GALAXY FRAMEWORD														|
|																							|
| 		www.torpix.net																		|
|		about@torpix.net																	|
|																							|
|		[ 2 Samuel, 22.3																	|
|																							|
|		"Deus é o meu rochedo, nele confiarei; o meu escudo, e a força da 					|
|		minha salvacao, o meu alto retiro, e o meu refugio. O meu Salvador, da violencia    |
|		me salvas."																			|
|																							|
|		[ Ageu 2.9  																		|
|		A glória desta última casa será maior do que a da primeira, diz o Senhor dos 		|
|		exércitos; e neste lugar darei a paz, diz o Senhor dos exércitos.					|
|																							|
| Este e um sistema fechado e nao pode ser aberto a terceiros. A utilizacao destes codigos  |
| sem a devida autorizacao da Midia Prata Design e Internet Ltda esta sujeito a penalidades |
| previstas na Lei nº 6910 de 19 de Fevereiro de 1998.										|
|																							|
| ------------------------------------------------------------------------------------------*/
class text extends files{
	
	# print string formated
	public function strPrint($texto,$formatado=false,$uft8=false)	{
		
		$texto = stripslashes($texto);
		
		if (!empty($formatado)):
			$texto = htmlspecialchars($texto);
			$texto = nl2br($texto);
			$texto = html_entity_decode($texto);
			$texto = strip_tags($texto);
		else:
			$texto = html_entity_decode($texto);
		endif;
		
		$texto = utf8_encode($texto);	
		$texto = stripslashes($texto);	
		
		return $texto;
	}
	
	# format str to input
	public function strPut($texto){
		
		$texto = htmlentities($texto);
		$texto = addslashes($texto);
		$texto = trim($texto);
		$texto = urldecode($texto);
		$texto = utf8_decode($texto);
		
		return $texto;
	
	}


	public function strMoney($str,$dec=",",$mil = "."){
		
		if( is_numeric($this->clearNumeric($str)) ):
		
			$texto = number_format($str,2,$dec,$mil);
		
		else:
			$texto = false;
		endif;
		
		
		return $texto;
	
	}
	
	
		public function putMoney($str){
		
		$texto = $str;
		$texto = str_replace(",",".",$texto);
		
		return $texto;
	
	}	

	#########################################################################
	#
	#########################################################################
	function stripHtmlAttr($txt,$allow) {
     
	 		return strip_tags($txt,$allow);
	}

	#######################################################################################	
	# 
	#######################################################################################
	public function formatDate($data=false){
	
		if(is_numeric($data)):
			$data = date('d/m/Y',$data);
		endif;
	
		$data = $this->limpaNome($data);
		ereg ("([0-9]{1,2})([0-9]{2})([0-9]{4})", $data, $registros);
		$n = strlen ($registros[1]);
		
		if($n=='1'):
			$r1 = "0".$registros[1];
		else:
			$r1 = $registros[1];
		endif;
		
		return $r1."/".$registros[2]."/".$registros[3];
		
	}

	# make date from db register
	public function enterDate($data,$delim=false){
	
		$delim = empty($delim)? "/" : $delim;
		
		if(!empty($data)):
			$a = explode($delim,$data);
			
			if(count($a) > 2):
				return "{$a[2]}-{$a[1]}-{$a[0]}";
			endif;
			
	endif;
	}
	
	# 
	public function exitDate($data,$delim=false,$separ=false){
		if(!empty($data)):
		
			$delim = empty($delim)? "/" : $delim;
			$separ = empty($separ)? "/" : $separ;
			
			$data = str_replace($delim,$separ,$data);
			
			$data = explode("-",$data);
			
			if(count($data) > 2):
				return "{$data[2]}/{$data[1]}/{$data[0]}";
			endif;
			
		endif;
	}
	
	
	# 
	public function printDate($data,$hours=false){
	
		if(!empty($data)):
		
			$parse = !empty($hours) ? "d/m/Y H:i:s" : "d/m/Y";
			$text = date($parse,strtotime($data)); 
			
			return $text;
			
		endif;
	}
	
	# force a line breaker
	public function strBreaker($str, $chars){ 
	
		$str = preg_replace('/([^\s\<\>]{'.$chars.','.$chars.'})/', '/\¬/', $str); 
		$str = preg_replace('/\¬/','<br />',$str);
		
		return $str;
	
	} 

	# make time from a date
	public function makeDate($data,$explode=false){
		
		$explode = !empty($explode) ? $explode : "/";
		
		if(!empty($data)):
			$a = explode($explode,$data);
			
			if(count($a) > 2):
				return mktime(0,0,0,$a[1],$a[0],$a[2]);
			endif;
			
		endif;
	}


	# gerar nova senha
	public function makePass($size = 6, $pass=false,$source=false){
		$senha = empty($source) ? "abcdefghijlkmnopqrstuvxzwyABCDEFGHIJLKMNOPQRSTUVXZYW0123456789" : $source; 
		
		srand ((double)microtime()*1000000); 
		for ($i=0; $i<$size; $i++):
			$pass .= $senha[rand()%strlen($senha)]; 
		endfor;
		
		return $pass;
	}
	
	
	
	# anti sql injection alias
	public function vtxInject($query,$adicionaBarras=false){
	
		return $this->strInject($query,$adicionaBarras=false);	
		
	}
	
	# anti sql injection
	public function strInject($query,$adicionaBarras=false){

		// remove palavras que contenham sintaxe sql
        $campo = preg_replace("/(from|alter table|select|insert|delete|update|
								where|drop table|show tables|#|\*|--|\\\\)/i","",$query);
        
		//limpa espaços vazio
		$campo = trim($campo);
		//tira tags html e php
        $campo = strip_tags($campo);
        
		if($adicionaBarras || !get_magic_quotes_gpc())
			//Adiciona barras invertidas a uma string
			$campo = addslashes($campo);
        return $campo;		
		
	}

	# completa com zeros a esquerda
	# $zerosADD = qtd de zeros a ser inserida
	# $numCasas = numero de casas que o numero final devera ter
	# $varialvel = numero inteiro
	#alias
	public function zeroAdd($n,$v){
		
		return $this->strZeros($n,$v);
		
	}
	
	public function addZero($n,$v){
		
		return $this->strZeros($n,$v);
		
	}
	
	public function strZeros($numCasas,$variavel,$addZeros=false){
	
		$totallen = strlen($variavel);
		
		if( $totallen < $numCasas):
			
			for($w=0; $w< ($numCasas-$totallen); $w++): 
				$addZeros .= '0';
			endfor;
		
		return $addZeros.$variavel;
		
		else:
			return $variavel;
		endif;
	}


	public  function clearName($s,$espaco='-'){
	
		$s = $this->strPrint($s);
		$s = $this->listaimagemRemoveAcentos($s);
		
		$s = preg_replace('[ááâãÂÃÁÀ]',  'a', $s); 
		$s = preg_replace('[ÉÈÊËéèêë]',  'e', $s); 
		$s = preg_replace('[íìîïÌÍÎÏ]',  'i', $s); 
		$s = preg_replace('[ÚÙÜÛúùüû]',  'u', $s); 
		$s = preg_replace('[óòôõöÒÓÔÕÖ]','o', $s); 
		$s = str_replace('[ñÑ]', 'n', $s); 
		$s = str_replace('[çÇ]', 'c', $s); 
		$s = preg_replace('/\ \//',$espaco, $s); 
		$s = preg_replace('/\s+/',$espaco, $s); 
	
		$s = stripslashes($s);
		
		return preg_replace('([^a-z0-9\-\.\{$espaco}]*)','', strtolower($s)); 
	}

	
	# clear special chars on str
	public function strClear($s,$espaco='-'){
	
	//$s = html_entity_decode($s);
	$s = $this->strPrint($s);
	$s = $this->post_slug($s);
	
	return $s;
         
	}	
	
	# strClear alias
	public function clearText($s,$v=false){
		return $this->strClear($s,$v);
	}


	## validator only number
	public function clearNumeric($str){
		return preg_replace("{[^0-9]}", "", $str);
	}
	
	## clear file names
	public function clearFile($s){
		
		$s = preg_replace('[ááâãÂÃÁÀ]',  'a', $s); 
		$s = preg_replace('[ÉÈÊËéèêë]',  'e', $s); 
		$s = preg_replace('[íìîïÌÍÎÏ]',  'i', $s); 
		$s = preg_replace('[ÚÙÜÛúùüû]',  'u', $s); 
		$s = preg_replace('[óòôõöÒÓÔÕÖ]','o', $s); 
		$s = str_replace('[ñÑ]', 'n', $s); 
		$s = str_replace('[çÇ]', 'c', $s); 
		$s = preg_replace('/\ \//','', $s); 
		$s = preg_replace('/\s+/','', $s); 
	
		$s = stripslashes($s);
		
		return preg_replace('([^a-z0-9\_\.]*)','', strtolower($s)); 
		
	}


# format string
public function strMask($mascara,$string){
   
   if(!empty($string)):
   
	   $string = str_replace(" ","",$string);
	   
	   for($i=0;$i<strlen($string);$i++):
	   
		  $mascara[strpos($mascara,"#")] = $string[$i];
	   
	   endfor;
	   
	   return $mascara;
   
   endif;
}

# alias
public function textMask($mask,$str){
	return $this->strMask($mask,$str);	
}

########################################################################
	var $skey = "minhoca";

public  function safe_b64encode($string) {
        $data = base64_encode($string);
        $data = str_replace(array('+','/','='),array('-','_',''),$data);
        return $data;
    }

    public function safe_b64decode($string) {
        $data = str_replace(array('-','_'),array('+','/'),$string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public  function vxEncode($value){ 
        if(!$value){return false;}
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->skey, $text, MCRYPT_MODE_ECB, $iv);
        $final =  trim($this->safe_b64encode($crypttext)); 
		
		return strrev($final);
		
    }

    public function vxDecode($value){
        if(!$value){return false;}
		
		$value = strrev($value) ;
		
        $crypttext = $this->safe_b64decode($value); 
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->skey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

	public function vxUserHash($key,$type){
		
		switch($type):
		
			case(1):
				$fkey = hash('sha512', $key);
			break;
			
			case(2):
				$fkey = hash('whirlpool', $key);
			break;
			
			case(3):
				$fkey = hash('haval256,5', $key);
			break;


			case(4):
				$fkey = hash('sha256', $key);
			break;

			case(5):
				$fkey = hash('sha1', $key);
			break;
		
		endswitch;
		
		return $fkey;
		
	}
########################################################################
# clear texts
public function remove_accent($str){
		$a = array('À','Á','Â','Ã','Ä','Å','Æ','Ç','È','É','Ê','Ë','Ì','Í','Î','Ï','Ð','Ñ','Ò','Ó','Ô','Õ','Ö','Ø','Ù','Ú','Û','Ü','Ý','ß','à','á','â','ã','ä','å','æ','ç','è','é','ê','ë','ì','í','î','ï','ñ','ò','ó','ô','õ','ö','ø','ù','ú','û','ü','ý','ÿ','Ā','ā','Ă','ă','Ą','ą','Ć','ć','Ĉ','ĉ','Ċ','ċ','Č','č','Ď','ď','Đ','đ','Ē','ē','Ĕ','ĕ','Ė','ė','Ę','ę','Ě','ě','Ĝ','ĝ','Ğ','ğ','Ġ','ġ','Ģ','ģ','Ĥ','ĥ','Ħ','ħ','Ĩ','ĩ','Ī','ī','Ĭ','ĭ','Į','į','İ','ı','Ĳ','ĳ','Ĵ','ĵ','Ķ','ķ','Ĺ','ĺ','Ļ','ļ','Ľ','ľ','Ŀ','ŀ','Ł','ł','Ń','ń','Ņ','ņ','Ň','ň','ŉ','Ō','ō','Ŏ','ŏ','Ő','ő','Œ','œ','Ŕ','ŕ','Ŗ','ŗ','Ř','ř','Ś','ś','Ŝ','ŝ','Ş','ş','Š','š','Ţ','ţ','Ť','ť','Ŧ','ŧ','Ũ','ũ','Ū','ū','Ŭ','ŭ','Ů','ů','Ű','ű','Ų','ų','Ŵ','ŵ','Ŷ','ŷ','Ÿ','Ź','ź','Ż','ż','Ž','ž','ſ','ƒ','Ơ','ơ','Ư','ư','Ǎ','ǎ','Ǐ','ǐ','Ǒ','ǒ','Ǔ','ǔ','Ǖ','ǖ','Ǘ','ǘ','Ǚ','ǚ','Ǜ','ǜ','Ǻ','ǻ','Ǽ','ǽ','Ǿ','ǿ');
		$b = array('A','A','A','A','A','A','AE','C','E','E','E','E','I','I','I','I','D','N','O','O','O','O','O','O','U','U','U','U','Y','s','a','a','a','a','a','a','ae','c','e','e','e','e','i','i','i','i','n','o','o','o','o','o','o','u','u','u','u','y','y','A','a','A','a','A','a','C','c','C','c','C','c','C','c','D','d','D','d','E','e','E','e','E','e','E','e','E','e','G','g','G','g','G','g','G','g','H','h','H','h','I','i','I','i','I','i','I','i','I','i','IJ','ij','J','j','K','k','L','l','L','l','L','l','L','l','l','l','N','n','N','n','N','n','n','O','o','O','o','O','o','OE','oe','R','r','R','r','R','r','S','s','S','s','S','s','S','s','T','t','T','t','T','t','U','u','U','u','U','u','U','u','U','u','U','u','W','w','Y','y','Y','Z','z','Z','z','Z','z','s','f','O','o','U','u','A','a','I','i','O','o','U','u','U','u','U','u','U','u','U','u','A','a','AE','ae','O','o');
		return str_replace($a, $b, $str);
	}

	public function post_slug($str){
		return strtolower(preg_replace(array('/[^a-zA-Z0-9 -]/', '/[ _]+/', '/^-|-$/'), array('', '-', ''), $this->remove_accent($str)));
	}

	public function listaimagemRemoveAcentos($str, $enc = "UTF-8"){
	
	$acentos = array(
		'A' => '/&Agrave;|&Aacute;|&Acirc;|&Atilde;|&Auml;|&Aring;/',
		'a' => '/&agrave;|&aacute;|&acirc;|&atilde;|&auml;|&aring;/',
		'C' => '/&Ccedil;/',
		'c' => '/&ccedil;/',
		'E' => '/&Egrave;|&Eacute;|&Ecirc;|&Euml;/',
		'e' => '/&egrave;|&eacute;|&ecirc;|&euml;/',
		'I' => '/&Igrave;|&Iacute;|&Icirc;|&Iuml;/',
		'i' => '/&igrave;|&iacute;|&icirc;|&iuml;/',
		'N' => '/&Ntilde;/',
		'n' => '/&ntilde;/',
		'O' => '/&Ograve;|&Oacute;|&Ocirc;|&Otilde;|&Ouml;/',
		'o' => '/&ograve;|&oacute;|&ocirc;|&otilde;|&ouml;/',
		'U' => '/&Ugrave;|&Uacute;|&Ucirc;|&Uuml;/',
		'u' => '/&ugrave;|&uacute;|&ucirc;|&uuml;/',
		'Y' => '/&Yacute;/',
		'y' => '/&yacute;|&yuml;/',
		'a.' => '/&ordf;/',
		'o.' => '/&ordm;/',
		'"'  => '/&quot;/',
		'\'' => '/&acute;/');
	
	  return preg_replace($acentos, array_keys($acentos),  htmlentities($str,ENT_NOQUOTES, $enc));
	   
	
	}	

	# converter entradas em html strings value
	function convertLatin1ToHtml($str) {
		$html_entities = array (
			"&" =>  "&amp;",
			"á" =>  "&aacute;",
			"Á" =>  "&Aacute;",
			"Â" =>  "&Acirc;",
			"â" =>  "&acirc;",
			"Æ" =>  "&AElig;",
			"æ" =>  "&aelig;",
			"À" =>  "&Agrave;",
			"à" =>  "&agrave;",
			"Å" =>  "&Aring;",
			"å" =>  "&aring;",
			"Ã" =>  "&Atilde;",
			"ã" =>  "&atilde;",
			"Ä" =>  "&Auml;",
			"ä" =>  "&auml;",
			
			"Ç" =>  "&Ccedil;",
			"ç" =>  "&ccedil;",
			
			"É" =>  "&Eacute;",
			"é" =>  "&eacute;",
			"Ê" =>  "&Ecirc;",
			"ê" =>  "&ecirc;",
			"È" =>  "&Egrave;",
			"è" =>  "&egrave;",
			
			"Ì" =>  "&Igrave;",
			"ì" =>  "&igrave;",
			"í" =>  "&iacute;",
			"Í" =>  "&Iacute;",
			"î" =>  "&icirc;",
			"Î" =>  "&Icirc;",
			
			"Ò" =>  "&Ograve;",
			"ò" =>  "&ograve;",
			"ó" =>  "&oacute;",
			"Ó" =>  "&Oacute;",
			"ô" =>  "&ocirc;",
			"Ô" =>  "&Ocirc;",
			
			"û" =>  "&ucirc;",
			"Û" =>  "&Ucirc;",
			"Ù" =>  "&Ugrave;",
			"ù" =>  "&ugrave;",
			"ú" =>  "&uacute;",
			"Ú" =>  "&Uacute;",
			
			"Ü" =>  "&Uuml;",
			"ü" =>  "&uuml;",
			"Ý" =>  "&Yacute;",
			"ý" =>  "&yacute;",
			"ÿ" =>  "&yuml;",
			"Ÿ" =>  "&Yuml;",
		);
	
		foreach ($html_entities as $key => $value) {
			$str = str_replace($key, $value, $str);
		}
		return $str;
	} 
	
}
?>