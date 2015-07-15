<?php
# set path  to 
$this->rootResetPath = 1;

#$this->set("vtxLicense","",true);;

# check user file
$sysConfFile = "{$this->vconf['distro']}/vtxs.driver.php";

if( is_file($sysConfFile) ):

	include $sysConfFile;

	$this->vconf = array_merge($this->vconf, $vserver);

endif;

$vplugs[0] = array(
	0=>'/xuser/captcha/',
	1=>'/xuser/access/',
	2=>'/xuser/exit/',
	3=>'/xuser/forgot/',
	
	7=>'/enginer/pics/',
	
	10=>'/licences/invalid/',  # invalid lic
	11=>'/licences/domain/',   # domain not lic
	12=>'/licences/expired/',  #  expired lic
	14=>'/licences/notfound/', #  expired lic
		
	20=>'/data/pass/',
	21=>'/data/profile/',
	22=>'/data/privilleges/',
	
	
	100=>'/main/wellcome/',
	200=>'/data/login/',
	
	);

$vplugs[1] = array(

	0=>'/enginer/change/',
	3=>'/enginer/help/', 
	4=>'/enginer/images/', 

);


$vplugs[5] = array(

	0=>'/files/view/',
	1=>'/files/scandirs/',
	11=>'/files/scanfile/',
	2=>'/files/mkdirs/',
	3=>'/files/mkfile/',

);


$vplugs[100] = array(

	0=>'/interface/privileges/',
	1=>'/interface/menu/',	
	2=>'/alerts/alarm/',	
	3=>'/mailer/alerts/',
	4=>'/alerts/alerts/',
	5=>'/mailer/messages/',	 
);


$vplugs[500] = array(

	500=>'/data/session/',
);


####
$this->setWebArea(999);

####
#### $this->set("nrequest",time() );

$vtxConfig['version'] 		= "Vortex ACM 2.2.8";
$vtxConfig['compilation'] 	= "20/01/2015";
$vtxConfig['release'] 		= "2305";


?>