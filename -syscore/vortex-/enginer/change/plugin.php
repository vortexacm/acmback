<?php
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");


# check user access login
if( count($_POST) <= 0 && (!isset( $_SESSION['vtxUser'] ) || !isset( $_SESSION['vtxEnter'] ) || $_SESSION['vtxEnter'] < ( time() ) )):	

	exit($this->vForceStop("<script>$(this).vtxRemove(); setPageNavigation('/inside-access,0,200,login.htm');</script>"));

	# set base form
	//$this->vInclude("/vortex.phar/xuser/access/tpl/base.htm","incMidiax",$this->vconf['path']);

	# force layout display
	//$this->vForcePlugin($this->vconf['path'],'/data/login/');

elseif( count($_POST) > 0 && (!isset( $_SESSION['vtxUser'] ) || !isset( $_SESSION['vtxEnter'] ) || ( $_SESSION['vtxEnter'] < ( time() - (60*60) ))) ):

	exit($this->vForceStop("199991|Sessão expirada. Faça o login novamente"));

else:

endif;

# get work dir
	$v = new vortex();
	
	$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);
	$vtxUserLicense    = $v->getLicenceActive();	
	
	# user license as enabled ??
	if( isset($vtxUserLicense) && $vtxUserLicense <= 0):
	
			exit($this->vForceStop("<h3 class='vtxAlerts vtxSysBreak toScreen'> 0700 :: Licença expirada. Impossível carregar e utilizar os plugins!</h3>  "));

	endif;	
	
	$this->vtxSkey = $v->vGetPrivateKey();
	
	$priv = isset($vlr) && isset($vlr[7]) ? $v->vPriveCheck($vlr,$vlr[7],1) : "";
		
	# set current page	
	$vUserURICall =  explode("?",$_SERVER['REQUEST_URI']);
	
	# get current url
	$serverRequestType = $__geturl__ = isset($vUserURICall[0]) ? $vUserURICall[0] : $vUserURICall;
		 
	# check privers	
	if( !isset($_SESSION['vuserGrants'][$vlr[4]][$vlr[5]][$vlr[6]][$vlr[7]]) && $_SESSION['vtxType'] != 1 ):
	
		exit($this->vForceStop("<h3 class='vtxAlerts vtxSysImportant toScreen'> 0531 :: Sem permissão para acesso a este modulo!</h3>  "));
	
	endif;
	
	# set url to pages
	$this->set("requestedURI",isset($_SERVER['QUERY_STRING']) ? "{$serverRequestType}?{$_SERVER['QUERY_STRING']}" : $serverRequestType);
	
	# set extra paramns
	$this->set("pluginExtras",'');
	$this->set("incEditor","");

	# set system user path
	$vtxPathsSystem = "{$this->vconf['source']}";
	
	# set plugin paths
	$vtxPaths  = isset($vlr[3]) ? "{$vlr[3]}/" : '';
	$vtxPaths .= isset($vlr[4]) ? "{$vlr[4]}/" : '';
	$vtxPathsCont = "{$vtxPaths}";
	
	$vtxPaths .= isset($vlr[5]) ? "{$vlr[5]}/" : '';
	$vtxPathsPlugin = "{$vtxPaths}";
	
	$vtxPaths .= isset($vlr[6]) ? "{$vlr[6]}/" : '';
	$vtxPathsModule = "{$vtxPaths}";	
	
	$vtxPaths .= isset($vlr[7]) ? "{$vlr[7]}/" : '';
	$vtxPathsApps   = "{$vtxPathsSystem}/{$vtxPaths}";
	
	# check is dir	
	if( !is_dir($vtxPathsApps)  ):
	
		exit($this->vForceStop("<h3 class='vtxAlerts vtxSysImportant toScreen'> 0533 :: Modulo não existe!</h3>  "));

	else:
		
		# set form targets
		$this->set("formurl",$_SERVER['REQUEST_URI']);
		

		# include service plugin
		include "{$vtxPathsSystem}/{$vtxPathsCont}plugin.php";
		$curPlugin = $app['name'];
		
		# include module system
		include "{$vtxPathsSystem}/{$vtxPathsPlugin}plugin.php";
		include "{$vtxPathsSystem}/{$vtxPathsModule}/module.php";
		
		# reset path to plugins
		$module['paths'] = "{$vlr[3]},{$vlr[4]},{$module['paths']}";

		# set global user enter ID
		$enterID = isset($vlr[8]) && is_numeric($vlr[8]) ? $this->clearNumeric($vlr[8]) : "";
		
		# check plugin config exists
		if(is_file("{$vtxPathsSystem}/{$vtxPathsModule}/mconfig.php")):
		
			include "{$vtxPathsSystem}/{$vtxPathsModule}/mconfig.php";
		
		endif;
	
		# include label plugin
		include "{$vtxPathsApps}/app.php";
		
		# check if dinamic link $app['lnk'][0]['name']
		$pageTitle  = $app['name'];
		$pageTitle .= isset($app['lnk'][$vlr[8]]['conf'])  && isset($app['lnk'][$vlr[8]]['name'] ) ? " :: {$app['lnk'][$vlr[8]]['name']} " : ""; 
		
		# full user path = 
		$this->vcon['vPluginRequest'] = "{$curPlugin} :: {$plugin['name']} :: {$module['name']} :: {$pageTitle}  ";
		$this->vcon['vPluginPath']    = $module['paths'];
		
		$this->vSetLogPath($this->vcon['vPluginRequest'], $this->vcon['vPluginPath']);		
					
		#set app name
		$this->set("appName",$this->vcon['vPluginRequest']);

		# include work file
		include "{$vtxPathsApps}/plugin.php";


		# check if add plugin exists
		if(isset($vlr[6])):
		
			$addLiveDir = "{$vlr[3]}/{$vlr[4]}/{$vlr[5]}/{$vlr[6]}/add";
			$addLiveDir = is_dir("{$vtxPathsSystem}/{$addLiveDir}") ? "load-plugins,1,0,{$vlr[3]},{$vlr[4]},{$vlr[5]},{$vlr[6]},add,0,90,charger.htm": '';
			
			$this->set("vPluginAdd",$addLiveDir);
		
		else:
			
			$this->set("vPluginAdd",0);

		endif;


		# check if views mode
		if( isset($app['view']) && $app['view'] == 1  ):
					
			$this->set("vPluginViewer",1, true );
			$this->set("vxPageCurrent",implode(",",$vlr) );
			
			$this->vRegisterLogs(1);
			

		else:
			$this->set("vPluginViewer",'', true );
		endif;
		
			$this->set("vPluginHelper","/online-help,1,3,suport.htm?h={$module['paths']},help" );
			$this->set("vPluginTicket","/load-plugins,1,0,{$__vtxPackModule__},vtxacm,vmessages,mailer,add,0,90,charger.htm");
			
			

		# check if js
		if( count($_FILES) <=0  && count($_POST) <=0 ):
			
			$this->vParseStatic("{$vtxPathsSystem}/{$vtxPathsModule}",'js','pluginExtras');
			$this->vParseStatic("{$vtxPathsSystem}/{$vtxPathsModule}",'css','pluginExtras');
				
			# if js file from module
			$cway =  "{$vtxPathsSystem}/{$vtxPathsModule}tpl/{$vlr[7]}.js";
	
			if(isset($vlr[7]) && is_file($cway) ): 
				
				$this->vParseStatic("{$vtxPathsSystem}/{$vtxPathsModule}",'js','pluginExtras',"{$vlr[7]}.js");
			endif;
			
		endif;
		
		#include "{$vtxPathWork}/plugin.php";
		if(is_file("{$vtxPathsSystem}/{$vtxPathsModule}/tpl/class.php") ):			
			include ("{$vtxPathsSystem}/{$vtxPathsModule}/tpl/class.php");
		endif;

		# include plugin tpl		
		if(is_file("{$vtxPathsApps}/tpl/base.htm")):
			
			$this->vParse($vtxPathsApps,'appParse');
		
		endif;		

		# execute form
		exit($this->vForcePlugin("{$this->vconf['private']}/{$__vtxPackModule__}","/enginer/change/"));;
		
			
endif;
?>