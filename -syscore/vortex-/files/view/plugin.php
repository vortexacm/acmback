<?php
!isset($_SESSION['vtxUser']) ? exit($this->vForceStop("Sessão Expirada. Faça o login novamente")) : "";

##############################################################################################################
## Vortex ACM 2.0 :: Make this month dir
##############################################################################################################
	$v = new vortex();
	$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);
	
	# get full path dir
	$vtxScanRoot = "{$this->vconf['rootpath']}/conteudo/{$__vtxPackModule__}/publico/upps/";

	# check destine dir exists
	!is_dir($vtxScanRoot) ? exit($this->vForceStop("4404 :: {$vtxScanRoot}")) : "";

	# check current month dir exists
	$month = date("Y-m");
	
	!is_dir("{$vtxScanRoot}{$month}") ? mkdir("{$vtxScanRoot}{$month}",0777) : "";
	is_dir("{$vtxScanRoot}{$month}") && !is_writable("{$vtxScanRoot}{$month}") ? chmod("{$vtxScanRoot}{$month}",0777) : "";
	
	# check user dir
	$vtxUserDir  = isset($_SESSION['userDIR']) ? $_SESSION['userDIR'] : "/conteudo/{$__vtxPackModule__}/publico/upps/{$month}/"; 
	$vtxUserDir  = "{$this->vconf['rootpath']}/{$vtxUserDir}";
	$vtxUserDir  = str_replace("//","/",$vtxUserDir);
	
	# check destine dir exists
	!is_dir($vtxUserDir) ? mkdir($vtxUserDir,0777) : "";
	is_dir($vtxUserDir) && !is_writable($vtxUserDir) ? chmod($vtxUserDir,0777) : "";

	# check destine ghost dir exists
	$vtxUserDir  = "{$vtxUserDir}{$month}";
	
	!is_dir($vtxUserDir) ? mkdir($vtxUserDir,0777) : "";
	is_dir($vtxUserDir) && !is_writable($vtxUserDir) ? chmod($vtxUserDir,0777) : "";


##############################################################################################################
## Vortex ACM 2.0 :: get user data
##############################################################################################################
	$enterID = $this->clearNumeric($_SESSION['vtxUser']);
	
	# check set btn
	$vtxBTN = isset($_GET['field']) ? "input#{$_GET['field']}" : "input.mce-textbox.mce-placeholder";
	
	# get req type
	$type = isset($_GET['type']) && $_GET['type'] == 'image' ? "2" : "1";
	
	# set btn value
	$this->set("vtxBtn",$vtxBTN);
	$this->set("userDir","/list-dir-system,5,1,manager.htm?");
	$this->set("userFile","/list-file-system,5,11,manager.htm?type={$type}&t={$type}");
	$this->set("vtxType",$type);


$this->vForceTPL($this->vconf['path'],"/vortex.phar/files/view/");
?>