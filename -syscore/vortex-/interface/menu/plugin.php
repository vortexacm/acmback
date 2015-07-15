<?php
if ( !isset($vlr[3]) || !isset($_SESSION['vtxGrupo']) || !is_numeric($_SESSION['vtxGrupo']) ):

	exit( $this->vForceStop("Não foi possivel carregar seus privilegios. Contate o suporte técnico"));

endif;

# check user caches
	$this->cached = "{$this->vconf['private']}/corecache/{$vlr[3]}/usrgroup/{$_SESSION['vtxGrupo']}";
	!isset($_GET['vcache']) ? $this->setCacheParams(1,60,1) : "";
	
	$this->vCacheDetect();
	
# get user privers
	$v = new vortex();
	$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);
	
	$spack = "{$this->vconf['source']}/{$__vtxPackModule__}";
	$v->vtxSystem  = $__vtxPackModule__;
	
	$vtkey = $this->clearNumeric($_SESSION['vtxUser']);
	
	# $usrGPR
	$inQuery   = "SELECT t2.module,t2.plugin,t2.app,t2.param,t3.nome,t2.action  ";
	$inQuery  .= "FROM vtx_system_prive AS t2 ";
	$inQuery  .= "LEFT JOIN vtx_system_group AS t1 ON t2.grupo=t1.id ";
	$inQuery  .= "LEFT JOIN vtx_system_access AS t3 ON t3.grupo = t1.id ";
	$inQuery  .= "WHERE t3.id='{$vtkey}' ";
	#$inQuery  .= "AND t2.action='vis' ";
	#$inQuery  .= "GROUP BY t2.app";
	
	$dquery = $this->adb->query($inQuery);
		
	# if error on query
	if ($this->adb->error):
		try {   
			throw new Exception("MySQL error {$this->adb->error} ", $this->adb->errno);   
		} catch(Exception $e ) {

			exit( $this->vForceStop("0|Error No: ".$e->getCode(). " - ". $e->getMessage() . "|var error = true"));
		}
	endif;
		
	$uprives = array();
	
	if( $dquery->num_rows > 0 ):

		$w = 0;
		while($dds = $dquery->fetch_array(MYSQLI_ASSOC)):
			
			if( $w == 0):
				
				$xname = $this->strPrint($dds['nome']);
				$_SESSION['userName'] = $xname;
			
			endif;
			
			$uprives[$dds['plugin']][$dds['app']] [$dds['module']] [$dds['action']][$dds['param']]  = 1 ;
			
		endwhile;
	
	
				
	endif;

	# get current prives	
	$_SESSION['vuserGrants'] = isset($uprives) ? $uprives : false;

	
	# execute plugin
	# $v->appReadModules($__vtxPackModule__,$spack,$uprives);	


	//print_r($uprives);
	$this->modules = $v->appReadModules($__vtxPackModule__,$spack,isset($_SESSION['vuserGrants']) ? $_SESSION['vuserGrants'] : array() );	
	
	
	$this->set("apps", $v->vGetUserPrives() , true );

	# get current privilleges
	if(isset($_GET['vcache'])):
	
		echo "<pre>";
	
		print_r($uprives);
		print_r($v->vGetUserPrives());

	endif;
	
	# force layout display
	$this->vForcePlugin($this->vconf['path'],'/interface/menu/');

?>