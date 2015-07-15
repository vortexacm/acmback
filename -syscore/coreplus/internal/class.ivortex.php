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

class ivortex  extends enginer {
	
	public $vconf;
	public $cached = "";
	public $cacheFile;
	public $cachePath;	
	public $cacheLife;	
	public $cacheMake=0;
	public $cacheUsing = 0;
	
	public $pluginFailed;	
	public $globalSetPath = '';
	public $globalFunction = '';
	
	public $cacheURL = '';
	public $globalPointer = 0;
	
	public $currentTPL;
	public $currentMSG;
	
	public $webStage = 0;
	public $vtxdb;
	
	public $defaultTPL;
	public $vCurrentTPL;


	# start aplication method
	public function __construct($config,$url){
		
			
			$this->vconf     = $config;
			$this->cacheFile = "/global/tpl/base.htm";
			$this->setCacheUrl($url);
			$this->vortexLoad($url);
			
	}
	
	
	// start controlers
	public function vCoreBegin(){
			
			$this->vCacheDetect();
			
			$this->vortexDataDB();
			
			$this->vortexDBCon();
			$this->setGlobalTags();
			
			$this->cacheLife = $this->vconf['filelife'];
			
			# $this->cached = $this->vconf['cached'];

			$this->userCoreBegin();
			
	}
	
	// finish controlers
	public function vCoreFinish(){

		$this->userCoreFinish();
		
	}		
	
	
	// update cache url
	public function setCacheUrl($url){
		
		$this->cacheURL = $url;
		
	}
	#######################################################
	# finish aplication method
	#######################################################
	public function __destruct(){
	
		//$this->coreFinish();
		
		if($this->cacheMake ==1 ):
				
					# current cache file name
					$cacheGen = md5($this->cacheURL).".htm";
					
					# path to cache files
					$cacheGenPath = "{$this->vconf['cached']}/{$cacheGen}";
					
					# get file modified data if exists
					$cacheInfo = is_file($cacheGenPath) ? filemtime($cacheGenPath) : 0;
					
					# check cache life time
					$cacheTime = time() - $this->cacheLife ;
					
					# cache exists ?
					if( !is_file($cacheGenPath)  || ($cacheInfo < $cacheTime ) ):
					
						$systemTPL = !empty($this->defaultTPL) ? $this->defaultTPL: $this->cacheFile;
						
						
						$this->liveExecute();	
						$this->vCoreFinish();
						$this->adb->close();	

						$this->cacheContent = $this->fetch($this->globalSetPath.$systemTPL);
						$this->vCacheMake();
						
						exit($general);
					
					else:
					
						if( $this->cacheUsing <> 1)
						exit( include($cacheGenPath));
						
					endif;
					
			else:
				
				$this->liveExecute();				
				$this->vCoreFinish();
				
				$this->adb;
				
				$this->adb->close();	
				
				$systemTPL = !empty($this->defaultTPL) ? $this->defaultTPL: $this->cacheFile;
				
				
				$prime = $this->fetch($this->globalSetPath.$systemTPL);
				
				exit($prime);
				
			endif;
		
	}

	
	private function liveExecute(){
		
		if(isset($this->globalFunction) && is_callable($this->globalFunction)):
			call_user_func_array($this->globalFunction,array($this,$this->vconf));
		endif;		
	}
}
?>