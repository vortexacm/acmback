<?php
/* ############################################################### Vortex ACM 2.0  ############################################################### /*
/* Licenca para uso de cliente final - Proibida distribuicao nao autorizada
/# 
/# Software protegido pela legislacao brasileira conforme rege
/# lei dos direitos autorais nº 6910 de 19 de Fevereiro de 1998
/# Proibida distribuicao nao autorizada
/# 
/# www.eminuto.com
/# 
/* ############################################################### Vortex ACM 2.0  ###############################################################*/

class forms{

	public $errorForm;
	public $classeErro;

	# check specified field
	public function validatorField($campo,$failure=false){
	
		if( !isset($_POST[$campo]) || strlen($_POST[$campo]) <= 0 ):
		
			$this->errorForm .= $failure;
			
			return "$(\"input[name='{$campo}']\").addClass('{$this->classeErro}'); ";
		
		
		endif;	
	}
	
	# set error do returm message
	public function validatorFieldSet($campo,$check){
	
			$this->errorForm .= $check ;
			return !empty($check) ? "$(\"input[name='{$campo}']\").addClass('{$this->classeErro}'); " : '';
	}


	# verificar e-mail
	public function validatorMail($email,$msg=false){
	
		if(empty($_POST[$email])):
			return (!empty($msg) ? " + {$msg} \n" : "+ Informe o e-mail corretamente \n" );
		endif;
		
		if (!preg_match("{^([a-zA-Z,0-9]+)([.,_,-]([a-zA-Z,0-9]+))*[@]([a-zA-Z,0-9]+)([.,_,-]([a-zA-Z,0-9]+))*[.]([a-zA-Z,0-9]){2}([a-zA-Z,0-9])?$}", $_POST[$email])):
			return (!empty($msg) ? "+ {$msg} \n" : "+ Informe um e-mail valido \n" );
		endif;
		
		$a = explode("@",$_POST[$email]);
		
		if(isset($a[1])):	
			
			$host = str_replace(".","",gethostbyname($a[1]));
			if(!is_numeric($host)):
				return (!empty($msg) ? "+ {$msg} \n" : "+ Informe o e-mail corretamente. \n " );
			endif;
		
		else: 
		
			return (!empty($msg) ? "+ {$msg} \n" : "+ Informe o e-mail corretamente;  \n" );
		
		endif;
	
	}


	# verificar url
	public function validatorURL($url,$alerta){
	$erro = false;
	
	if(!empty($url)):
		if( !eregi("^(http|https)+(:\/\/)+[a-z0-9_-]+\.+[a-z0-9_-]", $url )):
			$erro = "+ {$alerta} \n";
		else:
			if(!@fopen($url,"r")):
			$erro = "+ {$alerta} \n";
			endif;
		endif;
	endif;
	
	return $erro;
	}
	
	# check is valid date
	public function validatorDate($data,$alerta=false){

	if(strlen($data) == 10): 
	
	# Verifica se a data esta em um formato inv&aacute;lido
	if (!preg_match("{^([0-9]+)([\/]([0-9])+)([\/]([0-9]){4})?$}", $data)):
		return $alerta = "+ {$alerta} \n";
	else:
		$a = explode("/",$data);
		
		if (!checkdate($a[1], $a[0], $a[2])):	
		return $alerta = "+ {$alerta} \n";
		endif;
	
	endif;
	
	else:
		return $alerta = "+ {$alerta} \n";
	endif;
	}

	
	# verifica campos iguais
	public function validatorEqual($campo1,$campo2,$alerta){
	if ($campo1 <> $campo2):
	return $erro = "+ {$alerta} \n";
	else:
	return false;
	endif;
	}

	# Verficar caracteres validos em senha
	public function validatorPassword($campo,$min){

	if (empty($campo)):
		return $erro = "+ Digite uma senha  \n";
	else:
	
		if (!preg_match("/^([a-zA-Z,0-9])/",$campo) ): 
			return $erro = "+ Senha com caracteres inv&aacute;lidos   \n";
		endif;
		
		if( strlen($campo) < $min):
			return $erro = "+ Informe uma senha v&aacute;lida com minimo de {$min} caracteres  \n";
		endif;
		
		if (preg_match("/ /", $campo)):
			return $erro .= "+ N&atilde;o use espa&ccedil;os na senha  \n";
		endif;	
	
	endif;
	}
	


# acept on numbert
	public function validatorEnterNumber($str){
	
		return preg_replace("{[^0-9]}", "", $str);	
		
	}



# format to money insert
public function validatorEnterPrice($campo){
	$campo = str_replace(".","",$campo);
	$campo = str_replace(",",".",$campo);
	return $campo;
}

# format to date insert
public function validatorEnterDate($data,$delim=false){
	
		$delim = empty($delim)? "/" : $delim;
		
		if(!empty($data)):
			$a = explode($delim,$data);
			
			if(count($a) > 2):
				return "{$a[2]}-{$a[1]}-{$a[0]}";
			endif;
			
	endif;
	}
	
		
## brazil comercial functions
public function validatorCPF($cpf,$erro=false){
	
	$nulos = array("12345678909","11111111111","22222222222","33333333333","44444444444","55555555555","66666666666","77777777777","88888888888","99999999999","00000000000");
	
	# Retira todos os caracteres que nao sejam 0-9 
	$cpf =$this->validatorEnterNumber($cpf);

	#Retorna falso se houver letras no cpf
	if (!(preg_match("{[0-9]}",$cpf)))
		return "+ N&uacute;mero do CPF Inv&aacute;lido \n";

	# Retorna falso se o cpf for nulo 
	if( in_array($cpf, $nulos) )
		return "+ Digite os 11 n&uacute;mero do CPF \n";

	# Calcula o penúltimo dígito verificador
	$acum=0;
	for($i=0; $i<9; $i++):
	  $acum+= $cpf[$i]*(10-$i);
	endfor;

	$x=$acum % 11;
	$acum = ($x>1) ? (11 - $x) : 0;

	# Retorna falso se o digito calculado eh diferente do passado na string 
	if ($acum != $cpf[9]):
	  return "+ N&uacute;mero do CPF Inv&aacute;lido ";
	endif;

	# Calcula o último dígito verificador*/
	$acum=0;
	for ($i=0; $i<10; $i++):
	  $acum+= $cpf[$i]*(11-$i);
	endfor;  
	
	$x=$acum % 11;
	$acum = ($x > 1) ? (11-$x) : 0;

	# Retorna falso se o digito calculado eh diferente do passado na string 
	if ( $acum != $cpf[10]):
	  return "+ N&uacute;mero do CPF Inv&aacute;lido \n";
	endif;

	}

public function validatorCNPJ($cnpj,$erro=false){
	
$cnpj = preg_replace( "@[./-]@", "", $cnpj );

if( strlen( $cnpj ) <> 14 or !is_numeric( $cnpj ) ):
	return "+ Digite os 14 n&uacute;meros do CNPJ \n";
else:

	$k = 6;
	$soma1 = "";
	$soma2 = "";
	
	for( $i = 0; $i < 13; $i++ ):
		$k = $k == 1 ? 9 : $k;
		$soma2 += ( $cnpj{$i} * $k );
		$k--;
		if($i < 12):
			if($k == 1):
				$k = 9;
				$soma1 += ( $cnpj{$i} * $k );
				$k = 1;
			else:
				$soma1 += ( $cnpj{$i} * $k );
			endif;
		endif;
	endfor;
	
	$digito1 = $soma1 % 11 < 2 ? 0 : 11 - $soma1 % 11;
	$digito2 = $soma2 % 11 < 2 ? 0 : 11 - $soma2 % 11;
	
	return ( $cnpj{12} == $digito1 and $cnpj{13} == $digito2 ) ? '' : "+ N&uacute;mero do CNPJ Inv&aacute;lido \n"; 

endif;

} 	


## single insert
public function tableSingleInsert($tabela,$campos,$valores,$condicao=false,$multi=false){
	
	# caso esteja em modo editar dados
	if(!empty($condicao)):

		$c = false;
		$d = count($campos);
		$e = 1;
		
		
		while(list($k,$v) = each($campos)):
			if($e==$d):
				$c .= "`{$v}`='{$valores[$k]}' \n";
			else:
				$c .= "`{$v}`='{$valores[$k]}', \n";
			endif;
			$e++;	
		endwhile;
		
		return 	 "UPDATE {$tabela} SET {$c}  WHERE {$condicao};";

	# em modo de cadastro
	else:
	
		$c = false;
		$d = false;
		$e = 1;
		$f = count($campos);
		
		while(list($k,$v) = each($campos)):
			
			if($e==$f):
				$c .= "`{$v}` ";
				$d .= "'{$valores[$k]}'";
			else:
				$c .= "`{$v}`,";
				$d .= "'{$valores[$k]}',";
			endif;
			$e++;	
		
		endwhile;
	
		
		return !empty($multi) ? "($d)," :  "INSERT INTO {$tabela} ($c) values ($d);";
		
		
		
	endif;

}

## check total off fields on table
public function validatorTotalTable($tabela,$condicao,$res){
	
	try{
	
		$d ="SELECT count(*) AS total FROM {$tabela} WHERE {$condicao} ";
	
		$cons = $res->query($d);
		
		if ($res->error):
				
					try {   
						throw new Exception("MySQL error {$res->error}", $res->errno);   
					} catch(Exception $e ) {
						exit(print ("0|Sistema temporareamente indisponível - Error No: {$e->getCode()} - {$e->getMessage()} "));
					}
				
		else:
				
			$row  = $cons->fetch_assoc();
			
			return $row['total'] > 0 ? $row['total'] : '';
			
			$cons->close();

		endif;
		
	} catch (mysqli_sql_exception $e) {	
	
		exit(print("0|0010 - Sistema indisponível temporareamente :: {$e->getMessage()}|var error = null"));
	}
	
}
	

}
?>