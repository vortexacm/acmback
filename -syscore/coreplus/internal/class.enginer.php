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

class enginer extends ui{
	
	########################################################
	## construct method - to url analyser and make system
	########################################################
	public function vortexLoad($url){
		
		# clear domain types
		if(isset($this->vconf['vdomexts'])):
		
			$domain = $_SERVER['HTTP_HOST'];
			
			for($a = 0; $a <count($this->vconf['vdomexts']); $a++):
			
				$domain = preg_replace($this->vconf['vdomexts'][$a],"",$domain);
			
			endfor;
		
		else:
			
			#$domain = preg_replace("/\.br$/i", "", $_SERVER['HTTP_HOST']);
			$domain = preg_replace("/\.[a-z][a-z]{1,1}$/i", "", $_SERVER['HTTP_HOST']);
			
		endif;
		
		# prepare domain path
		$wildconf = array_reverse(explode(".",$domain));
		
		$vlrs = explode(",",$url);
		
		if(isset($_GET['vdisplay'])):
		
			echo $domain;
			
			print_r($wildconf);
		
		endif;
		
		
		# live debug
		if(isset($_GET['vxTrace'])):
		
			print_r($wildconf);
		
		endif;
		
		
		#echo is_file("../coreweb/vstatics/{$wildconf[2]}.php") ? "../coreweb/vstatics/{$wildconf[2]}.php" : "nao";
	
	if( isset($wildconf[2]) && is_file("{$this->vconf['controler']}/vstatics/{$wildconf[2]}.php")   ):

								
				$this->vconf['vpublic']  = $this->vconf['wildcard']['path'];
				$this->globalSetPath = $this->vconf['wildcard']['path'];
				$this->vCurrentTPL = $this->vconf['wildcard']['global'];
				

				$iconfig = "{$this->vconf['wildcard']['path']}/config.php";
				
				# configuration 
				if(is_file($iconfig)):
					
					include_once($iconfig);		
				
				else:
					//exit($this->vForceStop("VortexACM 2.0 Fail :: Config file not available in :: {$this->vconf['path']}/vortex.phar/config.php"));
					$fail = "Config file not available in :: {$this->vconf['wildcard']['path']}/config.php";
					include ("{$this->vconf['rootpath']}/corefix/temp/vtxErrorPlugin.php");
					exit($this->vForceStop(""));
					
				endif;

				# check plugin params exists
				$iplugin = "{$this->vconf['wildcard']['path']}/plugin.php";
				$iconfig = "{$this->vconf['wildcard']['path']}/config.php";

				is_file($iconfig) ? include($iconfig) : "";
				is_file($iplugin) ? include($iplugin) : "";
				
				# custom chanel configuration
				include ("{$this->vconf['controler']}/vstatics/{$wildconf[2]}.php");
				
				$this->vconf['plug']     = isset($vplugs) ? $vplugs : "";
				//$this->vconf['vpublic']  = $this->vconf['views'];
	

				# execute web site
				$this->vortexControler($vlrs, $this->vconf['wildcard']['source'],$this->vconf['vpublic']);	
				
		# check if subdomain requests
		elseif( isset($wildconf[2])  && !in_array($wildconf[2],$this->vconf['privated']) && (is_dir("{$this->vconf['controler']}/{$wildconf[2]}") or $wildconf[2] =='vortex') ):
		
			# set path to wildcard config
			$compConfig  = $wildconf[2] == 'vortex' ? "{$this->vconf['path']}/vortex.phar/plugin.php" : "{$this->vconf['viewfix']}/{$wildconf[2]}/plugin.php";
			
			# check if complentar config are available
			if(is_file($compConfig) ):
				
				# clear plugin paths
				unset($this->vconf['plug']);
				
				#include new configuration files
				include_once($compConfig);
				
				$iconfig =  $wildconf[2] == 'vortex' ? "{$this->vconf['path']}/vortex.phar/config.php" : "{$this->vconf['viewfix']}/{$wildconf[2]}/config.php";
				
				# configuration 
				if(is_file($iconfig)):
					
					include_once($iconfig);		
				
				else:
					
					//exit($this->vForceStop("VortexACM 2.0 Fail :: Config file not available in :: {$this->vconf['path']}/vortex.phar/config.php"));
					$fail = "Config file not available in :: {$this->vconf['path']}/vortex.phar/config";
					include ("{$this->vconf['rootpath']}/corefix/temp/vtxErrorPlugin.php");
					exit($this->vForceStop(""));
			
				endif;
				
				# reset urls params		
				#unset($this->vconf['plug']);
				
				$this->vconf['plug']     = $vplugs;
				$this->vconf['vpublic']  = $wildconf[2] == 'vortex' ? "{$this->vconf['path']}/vortex.phar" :  "{$this->vconf['views']}/{$wildconf[2]}";
				
				$this->vortexControler($vlrs,$wildconf[2]);
				
			else:
			
				//exit($this->vForceStop("VortexACM 2.0 Fail :: Can't be loaded in {$compConfig} "));
				$vtxFail = "Can't be loaded in {$compConfig}";
				
				include ("{$this->vconf['rootpath']}/corefix/temp/vtxErrorPlugin.php");
				exit($this->vForceStop(""));
			
			endif;
			
			
			
		else:
			
			$this->vortexControler($vlrs);
		
		endif;
		
	}

	########################################################
	## general system controler
	########################################################
	private function vortexControler($vlr=array(),$wildcard=false,$vpath=false){
		
		#############################################################################
		# check external redirects
		if( isset($vlr) && count($vlr) == 1 ):
			
			$vtxJumperFile = substr($vlr[0],1);
			
			# file redirect exists
			if(  is_file("{$this->vconf['private']}/coreurl/{$vtxJumperFile}.php") ):
			
				include "{$this->vconf['private']}/coreurl/{$vtxJumperFile}.php";
				
				exit(header("location:{$vtxJumper}"));
			
			endif;
		
		endif;
		#############################################################################

		#############################################################################		
		# set path to work
		#############################################################################
		$this->globalSetPath = !empty($wildcard) ? $wildcard == 'vortex' ? "{$this->vconf['path']}/vortex.phar" : "{$this->vconf['controler']}/{$wildcard}" : "{$this->vconf['controler']}/{$this->vconf['vpublic']}";
		
		$setUserPath = empty($vpath) ? $this->globalSetPath : "{$this->vconf['controler']}/{$vpath}";
		
		# check conditionals requests
		if( isset($vlr[1],$vlr[2],$this->vconf['plug'][$vlr[1]][$vlr[2]]) && is_file("{$setUserPath}{$this->vconf['plug'][$vlr[1]][$vlr[2]]}plugin.php") ):
	
		$setUserPlug = "{$setUserPath}{$this->vconf['plug'][$vlr[1]][$vlr[2]]}plugin.php";	
		$setUserConf = "{$setUserPath}{$this->vconf['plug'][$vlr[1]][$vlr[2]]}vconf.php";
		
			# check config exists
			if(is_file($setUserConf)):
				
				include $setUserConf;
			
			endif;
			
			# Start Apps
			$this->vCoreBegin();
			
					
			# include curret plugin
			include ($setUserPlug);
			
			$userTemplante = empty($vpath) && !empty($this->vCurrentTPL) ? $this->vCurrentTPL : "{$setUserPath}{$this->vconf['plug'][$vlr[1]][$vlr[2]]}tpl/base.htm";
			
			# read and execute template for plugins
			$view = file_get_contents($userTemplante);
			
			
			$this->set($this->vconf['merge'],$this->parse($view));
			
		# include default plugin
		else:
			
			
			# set plugin path
			$pluginToProcess = "{$this->globalSetPath}/index/plugin.php";
			
			#set plugin config path
			$cvonfig = "{$this->globalSetPath}/index/vconf.php"; 

			# check if plugins exists
			if( is_file($pluginToProcess) ):

				# include plugin conf
				if(is_file($cvonfig)):
					include $cvonfig;
				endif;				
				
				# start app
				$this->vCoreBegin();
								
				# process plugin
				include($pluginToProcess);

				# display index plugin
				$view = file_get_contents("{$this->globalSetPath}/index/tpl/base.htm");
				$this->set($this->vconf['merge'],$this->parse($view));	
				
			else:
				
				# plugin not load
				$this->setPluginFail(1);
				//exit($this->vForceStop("Vortex ACM 2.0 FAIL :: Plugin nao pode ser carregado em: {$pluginToProcess}"));
				
				$vtxFail = "Plugin nao pode ser carregado em: {$pluginToProcess}";
				include ("{$this->vconf['rootpath']}/corefix/temp/vtxErrorPlugin.php");
				exit($this->vForceStop(""));
				
				
				
			endif;
			
		endif;
		
	}
	
				
	#####################################################################
	# plugin not read
	#####################################################################
	public function setPluginFail($fail){
		return $this->pluginFailed = $fail;
	}
	
	public function getPluginFail(){
		return $this->pluginFailed;
	}
	
	#####################################################################
	# Open a database conection
	# $cfg = arraay('server','user','pass','db')
	#####################################################################
	public function vortexDataDB(){
		
		$this->adb = @new mysqli($this->vconf['dataserver'],
								$this->vconf['datauser'],
								$this->vconf['datapass'],
								$this->vconf['datadb']);


		if (mysqli_connect_errno()) :

				include ("{$this->vconf['rootpath']}/corefix/temp/vtxDBerror.html");
				exit($this->vForceStop(""));
		
		endif;
	
	}	

	#####################################################################	
	# get current conections
	#####################################################################
	public function vortexDBCon(){
	
		return $this->adb;
		// $this->adb;
		
	}

	#####################################################################
	# set globals tags
	#####################################################################
	public function setGlobalTags(){
		
			$this->BAldelim='<!--@ ';
			$this->BArdelim=' @-->';
			$this->EAldelim='<!--/@ ';
			$this->EArdelim=' @-->';
			$this->ldelim='<!-- ';
			$this->rdelim=' /-->';
		
	}

	
	#####################################################################
	# Verify is cache enable
	#####################################################################
	public function vCacheDetect(){
		
			if($this->cacheMake == 1 ):
			
				# current cache file name
				$cacheGen = md5($this->cacheURL).".htm";
				
				# path to cache files
				$cacheGenPath = "{$this->cached}/{$cacheGen}";
				
				# get file modified data if exists
				$cacheInfo = is_file($cacheGenPath) ? filemtime($cacheGenPath) : 0;
				
				# check cache life time
				$cacheTime = time() - $this->cacheLife;
				
				# cache exists ?
				if( is_file($cacheGenPath)  && ($cacheInfo > $cacheTime ) ):
					
					# clear actual cache
					clearstatcache();
					
					if( $this->cacheUsing <> 1):

						$this->setCacheUsing();
						
						# include current cache file
						include ($cacheGenPath );
						exit($this->vForceStop(""));
					
					endif;
				
				
				endif;	
	
			endif;
	
	}

	####################################################
	### make file from cache
	####################################################
	public function vCacheMake(){
		
			$cacheGen 		= md5($this->cacheURL).".htm";
			# echo $cacheGenPath 	= !empty($this->cached) ? "{$this->cached}/{$cacheGen}" : "{$this->vconf['cached']}/{$cacheGen}";
			$cacheGenPath 	= "{$this->cached}/{$cacheGen}";
			
			$cache = fopen($cacheGenPath,'w');
			fwrite($cache, $this->cacheContent );
			fclose($cache);
		
			clearstatcache();
			exit( include $cacheGenPath );
	}


	####################################################
	### make file from cache
	####################################################
	public function vSetGlobalView($view){
		
		return $this->cacheFile = "{$view}/tpl/base.htm";	
		
	}
	
	####################################################
	### make file from cache
	####################################################
	public function vSetGlobalPath($view){
		
		$this->globalSetPath = $view;
		$this->defaultTPL = "/tpl/base.htm";	
		
	}
	
	
	
	####################################################
	### verify cache in use
	####################################################	
	public function setCacheUsing(){
	
		$this->cacheUsing = 1;	
		
	}

	
	# se cache life
	public function setCacheLife($time){
		
		return $this->cacheLife = $time;
	
	}
	
	# se cache path
	public function setCachePath($pathx){
		
		return $this->cached = $pathx;
	
	}
	
	public function setCacheParams($cache,$life,$type = 1,$dir = false){
		
		switch($type):
		
			case(1):
				$time = $life * 60;
			break;
			
			case(2):
				$time = $life * (60*60);
			break;			

			case(3):
				$time = $life * ((60*60) *24);
			break;
			
			default;
				$time = $life * 60;
			break;			
					
		endswitch;
		
		$this->cacheMake = $cache;
		$this->setCacheLife($time);
				
	}

	####################################################
	### set web area
	####################################################	
	public function setWebArea($a){
	
		$this->webStage = $a;	
		
	}
	
	# get user web area
	public function getWebArea(){
	
		return $this->webStage;	
		
	}

}
?>