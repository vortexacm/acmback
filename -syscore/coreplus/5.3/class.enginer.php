<?php
/* -----------------------------------------------------------------------------------------#
|		TORPIX.NET - AURAPLUS FRAMEWORK														|
|																							|
| 		www.torpix.net																		|
|		about@torpix.net																	|
|																							|
|		[ 2 Samuel, 22.3																	|
|																							|
|		"Deus é o meu rochedo, nele confiarei; o meu escudo, e a força da 					|
|		minha salvacao, o meu alto retiro, e o meu refugio. O meu Salvador, da violencia    |
|		me salvas."																			|
|																							|
|		[ Ageu 2.9  																		|
|		A glória desta última casa será maior do que a da primeira, diz o Senhor dos 		|
|		exércitos; e neste lugar darei a paz, diz o Senhor dos exércitos.					|
|																							|
| Este e um sistema fechado e nao pode ser aberto a terceiros. A utilizacao destes codigos  |
| sem a devida autorizacao da Midia Prata Design e Internet Ltda esta sujeito a penalidades |
| previstas na Lei nº 6910 de 19 de Fevereiro de 1998.										|
|																							|
| ------------------------------------------------------------------------------------------*/

class enginer extends ui{

	########################################################
	## construct method - to url analyser and make system
	########################################################
	public function vortexLoad($url){
		
		if(isset($this->vconf['vdomexts'])):
		
			$domain = $_SERVER['HTTP_HOST'];
			
			for($a = 0; $a <count($this->vconf['vdomexts']); $a++):
			
				$domain = str_replace($this->vconf['vdomexts'][$a],"",$domain);
			
			endfor;
		
		else:
			
			$domain = str_replace(".br","",$_SERVER['HTTP_HOST']);
		
		endif;
		
		$wildconf = array_reverse(explode(".",$domain));
		
		$vlrs = explode(",",$url);
		
		if( count($wildconf) > 2 && isset($wildconf[2]) && !in_array($wildconf[2],$this->vconf['privated'])):
		
			# set path to wildcard config
			$compConfig  = $wildconf[2] == 'vortex' ? "{$this->vconf['source']}/vortex/plugin.php" : "{$this->vconf['viewfix']}/{$wildconf[2]}/plugin.php";
			
			# check if complentar config are available
			if(is_file($compConfig) ):
				
				# clear plugin paths
				unset($this->vconf['plug']);
				
				#include new configuration files
				include_once($compConfig);
				
				$iconfig =  $wildconf[2] == 'vortex' ? "{$this->vconf['source']}/{$wildconf[2]}/config.php" : "{$this->vconf['viewfix']}/{$wildconf[2]}/config.php";
				
				# configuration 
				if(is_file($iconfig)):
					
					include_once($iconfig);		
				endif;
				
				# reset urls params		
				#unset($this->vconf['plug']);
				
				$this->vconf['plug']     = $vplugs;
				$this->vconf['vpublic']  = $wildconf[2] == 'vortex' ? "{$this->vconf['source']}/{$wildconf[2]}" :  "{$this->vconf['views']}/{$wildconf[2]}";
				
				$this->vortexControler($vlrs,$wildconf[2]);
				
			else:
			
				exit($this->vForceStop("Vortex 2.0 can't be laoded"));
			
			endif;
			
			
			
		else:
			
			$this->vortexControler($vlrs);
		
		endif;
		
	}

	########################################################
	## general system controler
	########################################################
	private function vortexControler($vlr=array(),$wildcard=false){
		
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
		$this->globalSetPath = !empty($wildcard) ? $wildcard == 'vortex' ? "{$this->vconf['source']}/{$wildcard}" : "{$this->vconf['controler']}/{$wildcard}" : "{$this->vconf['controler']}/{$this->vconf['vpublic']}";
		
			
		
		# check conditionals requests
		if( isset($vlr[1]) && isset($vlr[2]) && 
			isset($this->vconf['plug'][$vlr[1]][$vlr[2]]) && 
			is_file("{$this->globalSetPath}{$this->vconf['plug'][$vlr[1]][$vlr[2]]}plugin.php") 
		):
		
			# check config exists
			$cvonfig = "{$this->globalSetPath}{$this->vconf['plug'][$vlr[1]][$vlr[2]]}/vconf.php"; 
			
			if(is_file($cvonfig)):
				
				include $cvonfig;
			
			endif;
			
			# Start Apps
			$this->vCoreBegin();
			
			# include curret plugin
			include "{$this->globalSetPath}{$this->vconf['plug'][$vlr[1]][$vlr[2]]}plugin.php";
						
			$view = file_get_contents("{$this->globalSetPath}{$this->vconf['plug'][$vlr[1]][$vlr[2]]}tpl/base.htm");
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
				exit($this->vForceStop("Plugin nao pode ser carregado"));
				
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
		
		@$this->adb = new mysqli($this->vconf['dataserver'],
								$this->vconf['datauser'],
								$this->vconf['datapass'],
								$this->vconf['datadb']);

	if (mysqli_connect_errno()) :
	
		 exit($this->vForceStop( "Error 00011-".$this->adb->connect_errno." :: Servidor não reponde a conexao. Contate o suporte técnico "));
	
	endif;
	
	}	

	#####################################################################	
	# get current conections
	#####################################################################
	public function vortexDBCon(){
	
		return $this->adb;
		
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
				$cacheGenPath = "{$this->vconf['cached']}/{$cacheGen}";
				
				# get file modified data if exists
				$cacheInfo = is_file($cacheGenPath) ? filemtime($cacheGenPath) : 0;
				
				# check cache life time
				$cacheTime = time() - $this->cacheLife ;
				
				# cache exists ?
				if( is_file($cacheGenPath)  && ($cacheInfo > $cacheTime ) ):
					
					# clear actual cache
					clearstatcache();
					
					if( $this->cacheUsing <> 1):

						$this->setCacheUsing();
						
						# include current cache file
						exit( include $cacheGenPath );
					
					else:
						echo "Not using";
					endif;
				
				endif;	
	
			endif;
	
	}

	####################################################
	### make file from cache
	####################################################
	public function vCacheMake(){
		
			$cacheGen 		= md5($this->cacheURL).".htm";
			$cacheGenPath 	= "{$this->vconf['cached']}/{$cacheGen}";
			
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
	### verify cache in use
	####################################################	
	public function setCacheUsing(){
	
		$this->cacheUsing = 1;	
		
	}

	
	# se cache life
	public function setCacheLife($time){
		
		return $this->cacheLife = $time;
	
	}
	
	
	public function setCacheParams($cache,$life,$type = 1){
		
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