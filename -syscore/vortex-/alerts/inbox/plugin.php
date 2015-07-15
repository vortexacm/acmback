<?php
################################################################################################
# get user prives	
################################################################################################
if(isset($_SESSION['vtxUser'])):
	
	$inQuery   = "SELECT t2.plugin,t2.app,t2.module,t2.action,t2.param  ";
	$inQuery  .= "FROM vtx_system_prive AS t2 ";
	$inQuery  .= "LEFT JOIN vtx_system_group AS t1 ON t2.grupo=t1.id ";
	$inQuery  .= "LEFT JOIN vtx_system_access AS t3 ON t3.grupo = t1.id ";
	$inQuery  .= "WHERE t3.id='{$_SESSION['vtxUser']}' ";

	
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
# total alarms
################################################################################################
$alarms = 0;
	
# retorna pendencias encontradas
$pendings = $_SESSION['VTX_MOD_ALARM'];

if( !empty( $pendings['sqlq'] )) :

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
							
					$alarms += $row['total'];
							
				endwhile;
				
				$result->free_result();
			endif;
			
			if ($this->adb->more_results()):
			   $b++;
			endif;
		
		} while ($this->adb->more_results() && $this->adb->next_result());
	
		
		if ($this->adb->errno):
		  exit($this->vForceStop( "2001 :: Erro encontrado ao processar linha {$b} :: Query error = ".$this->adb->error));
		endif;
		
		
	endif;
	
	endif;

endif;
	
	$message = isset($alarms) && $alarms > 0 ? "[+] {$alarms} Alerta(s)" : "" ;
	
exit( $this->vForceStop($message) );
?>