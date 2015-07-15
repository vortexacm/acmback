<?php
$target  = "/conteudo/users/accounts/";
$destine = $this->vconf['rootpath'].$target;

$enterID = $this->clearNumeric($_SESSION['vtxUser']);


##########################################################################################################################################
## crop image files
##########################################################################################################################################
if (isset($_POST['arquivo']) && isset($_POST['x1'])  && isset($_POST['y1'])  && strlen($_POST['arquivo']) > 5):

	# import class to form validation
	$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.images.php");
			
	# start new class
	$ap = new vtxImages();

	// The file
	$fileThumb = $_POST['arquivo'];
	$filename  = "{$destine}{$enterID}/{$_POST['arquivo']}";
	$fileFinal = "{$destine}{$enterID}/{$fileThumb}";
	
	// Set a maximum height and width
	$width  = 140;
	$height = 140;

	// Resample
	$image_p = imagecreatetruecolor($width, $height);
	$image   = $ap->getImageInfo($filename);

	imagecopyresampled($image_p,$image['create'],0,0,$_POST['x1'],$_POST['y1'], $width, $height,$_POST['w'],$_POST['h']);
	
	// Output
	imagejpeg($image_p,$fileFinal,80);

	// clear temp file
	imagedestroy($image_p);
					
	# finish	
	exit( $this->vForceStop("1| Texto publicado com sucesso"));

endif;

##########################################################################################################################################
## form upload foto
##########################################################################################################################################
if( isset($_FILES['userfile']['tmp_name']) && is_uploaded_file($_FILES['userfile']['tmp_name']) && isset($_POST['userdir']) ):

	$uptype = $_FILES['userfile']['type'];

	# check is valid file
	if( !in_array($uptype,$this->vconf['uppics']) ):
	
		exit( $this->vForceStop("0|2001 :: Formato de arquivo inválido "));
		
	else:
	
		# check is valid destine dir
		if(!is_dir( $destine)):
			exit( $this->vForceStop("0|2002 :: Diretorio alvo não definido {$_POST['userdir']} "));
		else:
			
			# check if user dir exists
			if(!is_dir($destine.$enterID)):
			
				mkdir($destine.$enterID,0777);
				chmod($destine.$enterID,0777);
			
			endif;
			
			# import class to form validation
			$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.images.php");
			
			# start new class
			$ap = new vtxImages();

			$ap->larguraFoto 	= 500;
			$ap->alturaFoto  	= 500;
			$ap->qualidadeFoto  = 100;
			
			# resize upp photo
			$ap->caminhoFoto = "{$destine}{$enterID}/";
			$arquivo = $this->clearName($_FILES['userfile']['name']);
			$ap->fotoMiniaturas($_FILES['userfile']['tmp_name'],$arquivo);
						
			# import class to form validation
			$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.forms.php");
			
			# start new class
			$v = new forms();
			$v->classeErro = 'formPointer';
	
			# update data with image info
			$tableFF[1] = 'foto';
			
			$tablevv[1] = $arquivo;
								
			$inQuery = $v->tableSingleInsert('vtx_system_access',$tableFF,$tablevv,"id='{$enterID}'");
			$dsql = $this->vquery($inQuery);

			exit( $this->vForceStop("1|{$arquivo}|{$_POST['userdir']}|{$target}{$enterID}/{$arquivo}"));
			
		endif;
	
	
	endif;

endif;



##########################################################################################################################################
## start insert new user
##########################################################################################################################################

if(isset($_POST['userInsert'])):

	# import class to form validation
	$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.forms.php");
	//$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.images.php");
	
	# start new class
	$v = new forms();
	
	$v->classeErro = 'formPointer';

	$falha[1]= $v->validatorField('nome',"Digite o nome do técnico");
	$falha[2]= $v->validatorField('email',"Digite o e-mail");

	# check mail
	if(!empty($_POST['email']) ):
		$msgErro = $v->validatorMail('email',$this->strPut("Digite o e-mail administrativo corretamento"));
		$falha[4] = $v->validatorFieldSet('email',$msgErro);
	endif;


	if(!empty($_POST['email']) ):
		$msgErro = $v->validatorTotalTable('vtx_system_access',"mail1='{$_POST['email']}' and id!='{$enterID}' ",$this->adb) > 0 ? "+ E-mail já cadastrado <br />" : '';
		$falha[6] = $v->validatorFieldSet('email',$msgErro);
	endif;	

	
	if(!empty($_POST['cpf']) ):
		$doc = $this->clearNumeric($_POST['cpf']);
		
		$msgErro  = $v->validatorCPF($doc,"+ CPF Invalido");
		$falha[7] = $v->validatorFieldSet('cpf',$msgErro);
	endif;
	
	if(!empty($_POST['cpf']) ):
		$doc = $this->clearNumeric($_POST['cpf']);
		
		$msgErro = $v->validatorTotalTable('vtx_system_access',"cpf='{$doc}'  and id!='{$enterID}' ",$this->adb) > 0 ? "+ CPF já cadastrado <br />" : '';
		$falha[8] = $v->validatorFieldSet('cpf',$msgErro);
	endif;	
	
		
### proced to register	
	$falhas = implode("",$falha);

	if( strlen($falhas) > 0 ):
	
		exit( $this->vForceStop("0|".nl2br($v->errorForm)."|{$falhas}"));

	else:


		$tableF[1] = 'nome';
		$tableF[2] = 'nascimento';
		$tableF[3] = 'mail1';
		$tableF[5] = 'cadastro';
		$tableF[6] = 'cpf';
		$tableF[7] = 'mail2';
		$tableF[9] = 'sexo';
		
		$tablev[1] = $this->strPut($_POST['nome']);
		$tablev[2] = $this->clearNumeric($_POST['nascimento']);
		$tablev[3] = $this->strPut($_POST['email']);
		$tablev[5] = time();
		$tablev[6] = $this->clearNumeric($_POST['cpf']);
		$tablev[7] = $this->strPut($_POST['email2']);
		$tablev[9] = $this->clearNumeric($_POST['sexo']);
		
		$inQuery = $v->tableSingleInsert('vtx_system_access',$tableF,$tablev,"id='{$enterID}'");
		$dsql = $this->adb->query($inQuery);
		
		# if error on query
		if ($this->adb->error):
			try {   
				throw new Exception("MySQL error {$this->adb->error} ", $this->adb->errno);   
			} catch(Exception $e ) {
	
				exit( $this->vForceStop("0|Error No: ".$e->getCode(). " - ". $e->getMessage() . "|var error = true"));
			}
		else:
		
			$tuser = $_POST['userID'];
			exit( $this->vForceStop("1|{$tuser}"));
	
		endif;
		
	endif;
	
endif;

##############################################################################################################
## get user data
##############################################################################################################
	
	$inQuery  = "SELECT * FROM vtx_system_access WHERE id='{$enterID}' ";
	
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
			
				$this->set('sexo',$dds['sexo'],true);
				$this->set('nome',$this->strPrint($dds['nome']));
				$this->set('nascimento',$this->strMask("##/##/####",$dds['nascimento']));
				$this->set('cpf',$this->strMask("###.###.###-##",$dds['cpf']));
				$this->set('email',$this->strPrint($dds['mail1']));
				$this->set('email2',$this->strPrint($dds['mail2']));
				
				$this->set('foto',is_file("{$destine}{$dds['id']}/{$dds['foto']}") ? "{$target}{$dds['id']}/{$dds['foto']}?t=".time() : "/conteudo/users/foto.jpg" );
				
				$dquery->free_result();
		
	endif;

$this->set("formAct",'/load-user-data,0,21,update.htm?r='.time() );
$this->vForceTPL($this->vconf['path'],"/vortex.phar/data/profile/");
?>