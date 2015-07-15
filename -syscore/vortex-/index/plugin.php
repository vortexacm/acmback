<?php
/* ############################################################### Vortex 2.0.2  ############################################################### /*
/* Licenca para uso de cliente final 
/# 
/# Software protegido pela legislacao brasileira conforme rege
/# lei dos direitos autorais nº 6910 de 19 de Fevereiro de 1998
/# Proibida distribuicao nao autorizada
/# 
/* ############################################################### Vortex 2.0.2  ###############################################################*/

# get user connections
$currentconnection = $this->getUserConnection();


# user not logged or login expire
	if( !isset($_SESSION['vtxUser']) || !isset($_SESSION['vtxType']) || !isset($_SESSION['vtxEnter']) || $_SESSION['vtxEnter'] < time() ):
		
		//exit( $this->vForcePlugin("{$this->vconf['path']}/vortex.phar","/data/login/") );
		$this->set("navigator",strstr($_SERVER['HTTP_USER_AGENT'], "Safari") ? "jquery.js":"jquery.min.js.gz" );
		$this->set("captcha","captcha,0,0,0,figura.jpg?request=".time() );
	
		$this->set("siteAno",date('Y') );
		$this->set("siteBase1","{$_SERVER['HTTP_HOST']}");
	
		# get user connections
		$this->set("userconect",gethostbyaddr($currentconnection) );
		$this->set("userip",$currentconnection );
		$this->set("userget",1);
	
		//exit( header("location:"));
		$this->vInclude("/vortex.phar/xuser/access/tpl/base.htm","incMidiax",$this->vconf['path']);
		$this->vForcePlugin($this->vconf['path'],'/xuser/login/');
	
	endif;
	
	if ( isset($_SERVER['REQUEST_URI']) && strlen($_SERVER['REQUEST_URI']) > 9 && !isset($vlr[1]) && !isset($vlr[2])  ):
	
		exit($this->vForceStop("<h3 class='vtxAlerts vtxSysBreak toScreen'> Vortex Error 0540 :: Requisição inválida!</h3>  "));
	
	endif;
	
	
# ####################################################################	
# load users plugins
# ####################################################################	
	$v = new vortex();
	$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);


# ####################################################################	
# load users plugins
# ####################################################################	
	$spack = "{$this->vconf['source']}/{$__vtxPackModule__}";
	$v->vtxSystem = $__vtxPackModule__;

	$vtxUserLicense = 	$v->getLicenceActive();

	#$vtxUserLicense = isset($_GET['d']) ? $vtxUserLicense - $_GET['d'] :$vtxUserLicense;
	
	if($vtxUserLicense <= 30 && $vtxUserLicense > 15 ):
	
		$vtxLicxsNotify = "Vortex ACM 2.0 :: Atenção!";
		$vtxLicxsAlerts = "Sua Licença para uso do Vortex ACM 2.0 expira dentro de {$vtxUserLicense} dias. <a href='' class='toBold'>Renovar Agora</a> ";
	
	elseif( $vtxUserLicense < 15 && $vtxUserLicense > 7 ):
		
		$vtxLicxsNotify = "Vortex ACM 2.0 :: Muito Importante!";
		$vtxLicxsAlerts = "Sua Licença para uso do Vortex ACM 2.0 expira dentro de {$vtxUserLicense} dias. <a href='' class='toBold'>Renovar Agora</a> ";
	
	elseif( $vtxUserLicense == 7):
		
		$vtxLicxsNotify = "Vortex ACM 2.0 :: Urgente";
		$vtxLicxsAlerts = "Sua Licença para uso do Vortex ACM 2.0 expira dentro de uma semana. <a href='' class='toBold'>Renovar Agora</a> ";
	
	elseif($vtxUserLicense < 7 && $vtxUserLicense > 1 ):

		$vtxLicxsNotify = "Vortex ACM 2.0 :: Urgentíssimo";
		$vtxLicxsAlerts = "Sua Licença para uso do Vortex ACM 2.0 expira dentro de uma semana. <a href='' class='toBold'>Renovar Agora</a> ";
	
	elseif($vtxUserLicense ==1):
		
		$vtxLicxsNotify = "Vortex ACM 2.0 :: Urgentíssimo";
		$vtxLicxsAlerts = "Sua Licença para uso do Vortex ACM 2.0 expira amanhã. <a href='' class='toBold'>Renovar Agora. Não perca tempo</a> ";

	elseif($vtxUserLicense <= 0):	
	
		$vtxLicxsNotify = "Vortex ACM 2.0 :: Licença Expirada";
		$vtxLicxsAlerts = "Sua Licença para uso do Vortex ACM 2.0 expirou. <a href='' class='toBold'>Clique aqui para Renovar Agora e reativar seu Vortex ACM 2.0</a> ";

	endif;
	
	//vtxLicenseAlert
	$this->set("vtxLicenseAlert",isset($vtxLicxsNotify) ? $vtxLicxsNotify : "");
	$this->set("vtxLicense",isset($vtxLicxsAlerts) ? $vtxLicxsAlerts : "",true);

	$this->set("vtxLicenseEnabled",isset($vtxUserLicense) && $vtxUserLicense > 0 ? 1 : "",true);
	
	
# check plugin exists
	if(!is_dir($spack)):
			
		//exit($this->vForceStop("<h3 class='vtxAlerts vtxSysImportant toScreen' style='width:70%'> Vortex Error 01010 :: Plugins nao puderam ser carregados de: {$spack} </h3>  "));
					
		  $vtxFail = "01010 :: Plugins nao puderam ser carregados de: {$spack}";
		  require_once ("{$this->vconf['rootpath']}/corefix/temp/vtxErrorPlugin.php");
		  
		  exit($this->vForceStop(""));
		
	endif;
	
// filemtime	


	$this->set("UserName", $this->strPrint($_SESSION['vtxName']) );
	$this->set("preview", "http://{$this->vconf['domain']}");
	$this->set("vCurrentPackager", $__vtxPackModule__);

?>