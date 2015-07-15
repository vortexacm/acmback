<?php
	//header("Cache-Control: private, max-age=10800, pre-check=10800");
	header("Pragma: private");

	// Set to expire in 2 days
	header("Expires: " . date(DATE_RFC822,strtotime("1 week")));
	
	// if the browser has a cached version of this image, send 304
	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
	  
	  exit(header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304));
	  
	}
	
	# "{$this->vconf['private']}/corefiles"
	$imagepath   = isset($_SESSION['picturedst']) ? $_SESSION['picturedst'] : "";
	$imagepics   = isset($vlr[5]) ? $vlr[5] : "";
	
	$currentPics = "{$imagepath}/{$imagepics}";
	
	if( is_file($this->vconf['rootpath'].$currentPics)	):
	
		$ftype =  getimagesize($this->vconf['rootpath'].$currentPics);
		
		!isset($_GET['vcache']) ? header("Content-type:{$ftype['mime']}") : ""; 
	
		# import class to form validation
		$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.images.php");
		
		# start new class
		$v = new vtxImages();
		
		$v->larguraFoto			= $vlr[3];
		$v->alturaFoto			= $vlr[4];
		$v->qualidadeFoto		= 75;
		$v->caminhoFoto			= '';
		
		$pic  = is_file($this->vconf['rootpath'].$currentPics) ? $this->vconf['rootpath'].$currentPics : "";
		
		$v->fotoMiniaturas($pic,"");
	
	else:
	
	endif;
	
	exit($this->vForceStop(""));
?>