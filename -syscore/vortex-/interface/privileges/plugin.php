<?php
#if( !isset($_SESSION['vuserGrants']) || !isset($_SESSION['VTX_MOD_ALARM']) ):

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
			
			$uprives[$dds['plugin']][$dds['app']] [$dds['module']] [$dds['action']] [$dds['param']]  = 1 ;
			
		endwhile;
	
			
	endif;

	
	# get current prives	
	!isset($_SESSION['vuserGrants']) ? $_SESSION['vuserGrants'] = isset($uprives) ? $uprives : array() : $_SESSION['vuserGrants'];

	if(isset($_GET['vcache'])):
	
	echo "<pre>";
	print_r($uprives);
	
	endif;
	
	
	# execute plugin
	$v->appReadModules($__vtxPackModule__,$spack,$_SESSION['vuserGrants']);	


#endif;

exit($this->vForceStop(""));
?>