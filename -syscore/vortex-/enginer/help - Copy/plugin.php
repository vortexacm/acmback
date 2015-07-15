<?php
##############################################################################################################
## get user data
##############################################################################################################
$helper = isset($_GET['h']) ? str_replace(",","/",$_GET['h']) : "";

$helpFile = "/{$helper}/tpl/base.htm";
$helpConf = "/{$helper}/tpl/conf.htm";

is_file($this->vconf['source'].$helpFile) ? $this->vInclude($helpFile,"helpinc",$this->vconf['source']) : $this->set("helpinc","",true);;
is_file($this->vconf['source'].$helpConf) ? $this->vInclude($helpConf,"helpconf",$this->vconf['source']) : $this->set("helpconf","",true);;

# load default help	
	$v = new vortex();
		
		$__vtxPackValue__ = $v->vtxGetLicence($this->vconf,1);
	
		@eval($__vtxPackValue__);

		# set sys params
		$this->set("vsysVersion",$____vlicsversion);
		$this->set("vsysMode",$____vlicsmodules);
		$this->set("vsysID",$____vlicsmodpack);
		$this->set("vsysContract",$____vlicsconcode);
		$this->set("vsysCompany",$____vlicscompany);
		$this->set("vsysCNPJ",$____vlicscodcomp);
		$this->set("vsysAddress",!empty($____vlicsaddres2) ? "{$____vlicsaddres1} <br /> {$____vlicsaddres2} " : $____vlicsaddres1);
		$this->set("vsyDomain",implode("<br>",$____vlicsdomains));
		$this->set("vsyExpire",date('d/m/Y',$____vlicvalidate) );
		$this->set("vsyActivate",date('d/m/Y',$____vlicactivate) );
		

		$keyLicense = "phar://{$this->vconf['private']}/coredist/{$this->vconf['syspack']}/{$this->vconf['vtxlics']}";
		$keyLicense = file_get_contents($keyLicense);
		
		$this->set("vsysKey",rtrim($keyLicense) );
		

	



$this->vForceTPL($this->vconf['path'],"/vortex.phar/enginer/help/");
$this->vForceStop("");
?>