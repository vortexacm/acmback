<?php
	
	# get work plugin
	if(!isset($_SESSION['vtxMainDir'])):
	
		$v = new vortex();
		$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);
		
		$spack = "{$this->vconf['source']}/{$__vtxPackModule__}";
		$v->vtxSystem  = $__vtxPackModule__;

		$_SESSION['vtxMainDir'] = $__vtxPackModule__;
		
	endif;

	# make service container
	$plugAlerts[''] = array();
	
	# check if especial plugins exists
	$setDir = "{$this->vconf['source']}/{$_SESSION['vtxMainDir']}/vtxacm";
	
	if( is_dir($setDir)):
	
		
		if ($handle = opendir($setDir)):
			
			while (false !== ($entry = readdir($handle))):
				
				if ($entry != "." && $entry != ".." && !is_dir("{$setDir}/{$entry}") && is_file("{$setDir}/{$entry}/plugin.php")):
					
					include "{$setDir}/{$entry}/plugin.php";
					
					isset($plugin['query']) ? $plugAlerts[$a]['names'] = "<li>{$plugin['name']}</li>" : "";
					isset($plugin['query']) ? $plugAlerts[$a]['query'] =  "" : "";
					
					$a++;
				
				endif;
				
			endwhile;
			closedir($handle);
		endif;
	
	endif;

$this->vForceStop( "" );

/*# detect logged user
if(isset($_SESSION['vtxGrupo'])):

	################################################################################################
	# get user prives
	################################################################################################	
		$inQuery  = "SELECT plugin,app,module,action,param FROM vtx_system_prive WHERE grupo='{$_SESSION['vtxGrupo']}' ";
		
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
		
			while($dds = $dquery->fetch_array(MYSQLI_ASSOC)):
			
				$uprives[$dds['plugin']] [$dds['app']] [$dds['module']] [$dds['action']] [$dds['param']]  = 1 ;	
				
			endwhile;
		
		endif;
	
	
	################################################################################################
	# read user plugins
	################################################################################################
	
	$pendings = $_SESSION['VTX_MOD_ALARM'];
	
	if( isset($pendings['sqlq'])):
	
		$query = implode(";",$pendings['sqlq']);

		$query = implode(";",$pendings['sqlq']);
		$query = str_replace("TABX",$this->vconf['vprefix'],$query);
		
		$this->adb->multi_query($query);
		
		# if error on query
		if ($this->adb->error):
				try {   
					throw new Exception("MySQL error {$this->adb->error} ", $this->adb->errno);   
				} catch(Exception $e ) {
					exit($this->vForceStop("0|Error No: ".$e->getCode(). " - ". $e->getMessage() . " <br /> {$query}|var error = true"));
				}
		else:
			
			$b = 0;
			do {
				
				if ($result = $this->adb->store_result()) :
					
					$a = 0;
					while ($row = $result->fetch_array(MYSQLI_ASSOC)):
					   
								$alerts[$b]['class'] 		= ($b+$a)%2==0 ? "" : "lightrow";
		
								$alerts[$b]['mode'] 		= $pendings['mode'][$b];
								$alerts[$b+$a]['total'] 	= $row['total'];
								$alerts[$b+$a]['go'] 		= $pendings['urls'][$b];;
								
					endwhile;
					
					$result->free_result();
				endif;
				
				if ($this->adb->more_results()):
				   $b++;
				endif;
			
			} while ($this->adb->more_results() && $this->adb->next_result());
		
			if ($this->adb->errno):
			  exit($this->vForceStop( "2002 :: Erro encontrado ao processar linha {$b} :: Query error = ".$this->adb->error));
			endif;
			
		endif;
	
	endif;

endif;

	$this->set("messages","",true);
	$this->set("alerts",isset($alerts) ? $alerts : "", true);

*/
	//$this->vForceStop("---");
	
	//echo 1;
	exit($this->vForceTPL($this->vconf['path'],"/vortex.phar/mailer/alerts/"));

?>