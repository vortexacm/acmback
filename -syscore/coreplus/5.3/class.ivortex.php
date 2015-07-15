<?php
class ivortex  extends enginer {
	
	public $vconf;
	
	public $cacheFile;
	public $cachePath;	
	public $cacheLife;	
	public $cacheMake=0;
	public $cacheUsing = 0;
	
	public $pluginFailed;	
	
	public static $cacheURL = '';
	public static $globalSetPath = '';
	public static $globalPointer = 0;
	
	public static $globalFunction = '';
	public static $currentTPL;
	public static $currentMSG;
	
	public $webStage = 0;
	public static $defaultTPL;

	# start aplication method
	public function __construct($config,$url){
		
			
			$this->vconf     = $config;
			$this->cacheFile = "/global/tpl/base.htm";
			
			$this->cacheURL = $url;
			$this->vortexLoad($url);
			
	}
	
	public function vCoreBegin(){
			
			$this->vCacheDetect();
			$this->vortexDataDB();
			$this->vortexDBCon();
			$this->setGlobalTags();
			
			
			$this->cacheLife = $this->vconf['filelife'];
			
			$this->userCoreBegin();
			
	}
	
	
	public function vCoreFinish(){

		$this-> userCoreFinish();
		
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
				$this->adb->close();	
				
				$systemTPL = !empty($this->defaultTPL) ? $this->defaultTPL: $this->cacheFile;
				
				
				$prime = $this->fetch($this->globalSetPath.$systemTPL);
				
				exit($prime);
				
				#exit();
				
			endif;
		
	}

	
	private function liveExecute(){
		
		if(isset($this->globalFunction) && is_callable($this->globalFunction)):
			call_user_func_array($this->globalFunction,array($this,$this->vconf));
		endif;		
	}
}
?>