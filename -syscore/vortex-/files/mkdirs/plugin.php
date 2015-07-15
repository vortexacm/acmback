<?php	
if(isset($_POST['userInsert'])):

	# import class to form validation
	$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.forms.php");
	
	# start new class
	$v = new forms();
	
	$v->classeErro = 'vtxFormReqs';

	$falha[4]= $v->validatorField('tagular',"Digite o nome da pasta");
	
		
### proced to register	
	$falhas = implode("",$falha);

	if( strlen($falhas) > 0 ):
	
		exit( $this->vForceStop("0|".nl2br($v->errorForm)));

	else:

			$v = new vortex();
			$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);

			# check current month dir exists
			$month = date("Y-m");
			
			$pathNew 	 = $this->clearName($_POST['tagular']); 
			$pathFolder  = isset($_SESSION['userDIR']) ? $_SESSION['userDIR'] : "/conteudo/{$__vtxPackModule__}/publico/upps/{$month}"; 
			$pathFolder  = isset($_POST['dir']) && strlen($_POST['dir']) > 0 ? "{$this->vconf['rootpath']}{$_POST['dir']}/{$pathNew}" : "{$this->vconf['rootpath']}/{$pathFolder}/{$pathNew}";
			
			umask(0);
			
			# make new dir
			!is_dir($pathFolder) ? mkdir($pathFolder,0777) : "";
			chmod($pathFolder,0777);

			# make folder to invisible
			is_dir($pathFolder) && !is_dir("{$pathFolder}/vthumb") ? mkdir("{$pathFolder}/vthumb",0777) : "";
			is_dir("{$pathFolder}/vthumb") && is_writable("{$pathFolder}/vthumb") ?  chmod("{$pathFolder}/vthumb",0777) : "";
			
			$this->vForceStop("1|{$pathFolder}");

	endif;

endif;

$this->set("dir",isset($_GET['dir']) ? $_GET['dir'] : "");
$this->vForceTPL($this->vconf['path'],"/vortex.phar/files/mkdirs");


exit( $this->vForceStop("carga") );
?>