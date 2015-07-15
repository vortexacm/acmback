<?php
# user not logged or login expire
	if( !isset($_SESSION['vtxUser']) || !isset($_SESSION['vtxType']) || !isset($_SESSION['vtxEnter']) || $_SESSION['vtxEnter'] < time() ):
	
		exit( $this->vForceTPL($this->vconf['path'],"/vortex.phar/data/login/") );

		
	endif;


# check if user are logged
if( isset($_SESSION['vtxUser']) ):
	
	# get user connections
	$currentconnection = $this->getUserConnection();
	$this->set("userconect",gethostbyaddr($currentconnection) );
	$this->set("userip",$currentconnection );
	
# ####################################################################	
	# display user data
# ####################################################################	
	$this->set("userDate", $this->showDate() .$this->showGreetings() );
	
	# $expireLicense = $v->getLicenceExpires();
	# $this->set("alertaLicenca",$expireLicense > 15 ? "" : $expireLicense,true);
	
		# start vortex config
		$v = new vortex();
		$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);


		if( isset($__vtxPackModule__) ):
			
			$this->set("name",isset($usrname) ? $usrname : "Gerente");
			$this->set("grupo",isset($usrgrps) ? $usrgrps : "Manager");
			$this->set("usrtotal",isset($usrtotal) 	 ? $this->zeroAdd(6,$usrtotal) : "Primeiro Acesso");
			$this->set("uservisit",isset($uservisit) ? date("d/m/Y - H:i:s",$uservisit) : "Hoje");
			
			unset($_SESSION['picturedst']);
			
			if(isset($dds['iud'])):
				$this->vconf['photodir'] = "{$this->vconf['private']}/corefiles/storepro/managers/{$dds['iud']}";
				$this->set("usrfoto","/vortex-picture,1,4,{$this->vconf['manager']['small']['w']},{$this->vconf['manager']['small']['h']},{$dds['iud']}/{$dds['foto']}");
			endif;
			
			$this->set("usrclass",isset($usrclass) && empty($usrfoto) ? $usrclass : "");
			
			$this->set("navOpen","?t=".time());
			$this->set("preview","http://{$this->vconf['domain']}");
			$this->set("setMessages", "/load-plugins,1,0,{$__vtxPackModule__},vtxacm,vmessages,mailer,add,0,90,charger.htm");
			$this->set("getMessages", "/load-plugins,1,0,{$__vtxPackModule__},vtxacm,vmessages,mailer,vis,0,90,charger.htm?sent=4");
			
			$_SESSION['vtxMainDir'] = $__vtxPackModule__;
			
			
		else:

			exit($this->vForceStop("<h3 class='vtxAlerts vtxSysImportant toScreen'> Vortex Error 0541 :: Não foi possivel carregar o sistema corretamente. Contate o suporte técnico!</h3>  "));
		
		endif;
		
		
		# display data
			$this->set("name",isset($usrname) ? $usrname : "Gerente");
			$this->set("grupo",isset($usrgrps) ? $usrgrps : "Manager");
			$this->set("usrtotal",isset($usrtotal) 	 ? $this->zeroAdd(6,$usrtotal) : "Primeiro Acesso");
			$this->set("uservisit",isset($uservisit) ? date("d/m/Y - H:i:s",$uservisit) : "Hoje");
		
endif;


$this->vForcePlugin($this->vconf['path'],"/main/wellcome/");

exit($this->vForceStop(""));
?>