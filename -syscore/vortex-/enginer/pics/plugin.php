<?php
header("Cache-Control: private, max-age=10800, pre-check=10800");
header("Pragma: private");
// Set to expire in 2 days
header("Expires: " . date(DATE_RFC822,strtotime("1 week")));
if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
  // if the browser has a cached version of this image, send 304
  header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304);
  exit;
}

	
	$currentIcon     = isset($_GET['icon']) ? $_GET['icon'] : "";
	$currentMudule   = isset($vlr[4]) ? $vlr[4] : "";
	$currentSysWay = "{$this->vconf['source']}/{$currentMudule}/";
	
	
	$iconSet = $currentSysWay.$currentIcon;
	
	!isset($_GET['vcache']) ? header("Content-type: image/png") : ""; 

	# import class to form validation
	$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.images.php");
	
	# start new class
	$v = new vtxImages();
	
	$v->larguraFoto			= 20;
	$v->alturaFoto			= 20;
	$v->qualidadeFoto		= 60;
	$v->caminhoFoto			= '';
	
	$pic  = is_file($iconSet) ? $iconSet : "{$currentSysWay}/tpl/ico.png";
	
	$v->fotoMiniaturas($pic,"");
	
	exit($this->vForceStop(""));
?>