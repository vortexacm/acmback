<?php

##########################################################################################################################################
## start insert new user
##########################################################################################################################################

if(isset($_POST['userInsert'])):

	# import class to form validation
	$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.forms.php");
	
	# start new class
	$v = new forms();
	
	$v->classeErro = 'formPointer';

	$falha[1]= $v->validatorField('userPath',"Diretorio não definido");
	
		
### proced to register	
	$falhas = implode("",$falha);

	if( strlen($falhas) > 0 ):
	
		exit( $this->vForceStop("0|".nl2br($v->errorForm)."|{$falhas}"));

	else:

		$enterID1 = $this->clearNumeric($_SESSION['vtxUser']);
		$enterID2 = $this->clearNumeric($_SESSION['vtxGrupo']);

		$file = md5("/vortex-menu,100,1,{$_POST['userPath']},request.htm");
		$path = "{$this->vconf['private']}/corecache/{$_POST['userPath']}/usrgroup/{$enterID2}/{$file}.htm";
		$uppd = is_file($path) ? unlink($path) : "";

		exit( $this->vForceStop("1|Atualizado com sucesso"));
	
		
	endif;
	
endif;

##############################################################################################################
## get user data
##############################################################################################################
	$enterID = $this->clearNumeric($_SESSION['vtxUser']);
	
	$inQuery  =  "SELECT t1.nome,t1.grupo AS grp, t2.grupo ";
	$inQuery  .= "FROM vtx_system_access AS t1 ";
	$inQuery  .= "LEFT JOIN  vtx_system_group AS t2 ON t1.grupo = t2.id  ";
	$inQuery  .= "WHERE t1.id='{$enterID}' ";
	
	$dquery = $this->adb->query($inQuery);
		
	# if error on query
	if ($this->adb->error):
		try {   
			throw new Exception("MySQL error {$this->adb->error} ", $this->adb->errno);   
		} catch(Exception $e ) {

			exit( $this->vForceStop("0|Error No: ".$e->getCode(). " - ". $e->getMessage() . "|var error = true"));
		}
	endif;
		
	
	if( $dquery->num_rows > 0 ):
			
				$dds = $dquery->fetch_array(MYSQLI_ASSOC);
			
				$this->set('nome',$this->strPrint($dds['nome']));
				$this->set('grupo',$this->strPrint($dds['grupo']));


				# get current module
				$v = new vortex();
				$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);
				
				$file = md5("/vortex-menu,100,1,{$__vtxPackModule__},request.htm");
				$path = "{$this->vconf['private']}/corecache/{$__vtxPackModule__}/usrgroup/{$dds['grp']}/{$file}.htm";
				$uppd = is_file($path) ? filemtime($path) : 0;
				
				$this->set('atualizacao', $uppd > 0 ? date("d/m/Y - H:i:s", $uppd) : "Nunca");
				$this->set('path',$__vtxPackModule__);
				
				$dquery->free_result();
		
	endif;

$this->set("formAct",'/load-user-config,0,22,privileges.htm?r='.time() );
$this->vForceTPL($this->vconf['path'],"/vortex.phar/data/privilleges/");
?>