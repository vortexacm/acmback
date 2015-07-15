<?php
# echo $ar = $this->vxEncoder($_SESSION['vtxUser']);
# echo $this->vxDencoder($ar);
# echo $this->vxUserHash($_SESSION['vtxUser'],2);

##############################################################################################################
## get user data
##############################################################################################################
	
##########################################################################################################################################
## start edit user data
##########################################################################################################################################

if(isset($_POST['userInsert'])):

	$enterID = $this->clearNumeric($_SESSION['vtxUser']);

	# import class to form validation
	$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.forms.php");
	
	# start new class
	$v = new forms();
	
	$v->classeErro = 'formPointer';

	$falha[]= $v->validatorField('passact',"Digite senha atual");

	if(!empty($_POST['pass']) ):
		$msgErro = $v->validatorEqual($_POST['pass'],$_POST['passt'],"Senhas não conferem");
		$falha[] = $v->validatorFieldSet('pass',$msgErro);
	endif;

	if(isset($_POST['pass']) && strlen($_POST['pass']) < 6 ):
		$falha[] = $v->validatorFieldSet('pass',"+ Digite uma com 6 caracteres \n");
	endif;

	
	if(!empty($_POST['passact']) ):
		
		$pass = sha1(trim($_POST['passact']));
		
		$msgErro = $v->validatorTotalTable('vtx_system_access',"userkey='{$pass}' and id='{$enterID}' ",$this->adb) <= 0 ? "+ Senha atual invalida" : '';
		$falha[] = $v->validatorFieldSet('passact',$msgErro);
	
	endif;	
		
		
### proced to register	
	$falhas = implode("",$falha);

	$logs =  strlen($falhas);
	
	if( strlen($falhas) > 0 ):
	
		exit( $this->vForceStop("0|".nl2br($v->errorForm)."|{$falhas}") );

	else:

			$vtableF1[] = 'userkey';
			$vtablev1[] = sha1(trim($_POST['pass']));
			
			$inQuery = $v->tableSingleInsert('vtx_system_access',$vtableF1,$vtablev1,"id='".$this->clearNumeric($_SESSION['vtxUser'])."'");
			$this->vquery($inQuery);

			exit( $this->vForceStop("1|Senha Atualizada com sucesso"));
						
	endif;
	
endif;

$enterID = $this->clearNumeric($_SESSION['vtxUser']);
$this->set("formact",'/load-user-pass,0,20,change.htm?r='.time() );

$this->vForceTPL($this->vconf['path'],"/vortex.phar/data/pass/");
?>