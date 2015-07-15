<?php	
if(isset($_POST['userInsert']) && isset($_FILES['userfile']) && is_uploaded_file($_FILES['userfile']['tmp_name']) ):

	$destine  	 = "{$this->vconf['rootpath']}{$_POST['direct']}/";
	
	$uptype = $_FILES['userfile']['type'];
	$uptype = str_replace('"','',$uptype);

	# check is valid file
	if( !in_array($uptype, $this->vconf['upfile']) ):
	
		exit( $this->vForceStop("0|2000 :: Formato de arquivo inválido :: {$uptype} para {$_POST['direct']} "));
		
	else:
	
		# check is valid destine dir
		if(!is_dir( $destine)):
			exit( $this->vForceStop("0|2000 :: Diretorio alvo não definido para :: {$destine} "));
		else:
			
			
			if( isset($_POST['tpo']) && $_POST['tpo'] == 1 ) :
			
			
				
				$arquivo = $this->clearName($_FILES['userfile']['name']);
				
				move_uploaded_file($_FILES['userfile']['tmp_name'],"{$destine}{$arquivo}");
			
			else:
			
				
				# import class to form validation
				$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.images.php");
				
				# start new class
				$ap = new vtxImages();
	
				$ap->larguraFoto   = 655;
				$ap->alturaFoto    = 655;
				$ap->qualidadeFoto = 100;
				
				# resize upp photo
				$ap->caminhoFoto = "{$destine}/";
				$arquivo = $this->clearName($_FILES['userfile']['name']);
				$ap->fotoMiniaturas($_FILES['userfile']['tmp_name'],$arquivo);
	
	
				
				# apply wattermark
				$ap->marcaDagua 		= "{$this->vconf['rootpath']}/conteudo/materias/brand/logo.png";
				$ap->posicaoMarcaDagua  = 3;
				$ap->fotoMarcar("{$destine}/{$arquivo}","{$destine}/{$arquivo}");
				
			
			endif;
			
			# finish	
			exit( $this->vForceStop("1|0"));
			
		endif;
	
	
	endif;
	
endif;

if(isset($_POST['userInsert']) && !isset($_FILES['userfile'])):

	exit($this->vForceStop("0|Arquvo Inválido ou acima do tamanho máximo permitido") );

endif;

$v = new vortex();
$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);

# check current month dir exists
$month = date("Y-m");
$pathFolder   = isset($_SESSION['userDIR']) ? $_SESSION['userDIR'] : "/conteudo/{$__vtxPackModule__}/publico/upps/"; 
$pathFolder  .= $month;

$typeAccept = isset($_GET['type']) && $_GET['type'] ==1 ? "gif|png|jpg|doc|xls|txt|pdf|zip|bmp" : "gif|png|jpg";

$this->set("dir",isset($_GET['dir']) ? $_GET['dir'] : $pathFolder);
$this->set("hd",isset($_GET['type']) && $_GET['type'] == 1 ? "" : 1, true);
$this->set("tpo",isset($_GET['type']) && $_GET['type'] == 1 ? 1 : 0, true);
$this->set("types", str_replace("|", ", ", $typeAccept) );

$this->set("uppTypes",$typeAccept);


$this->vForceTPL($this->vconf['path'],"/vortex.phar/files/mkfile/");

exit( $this->vForceStop("carga") );
?>