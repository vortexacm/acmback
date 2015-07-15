<?php
# get user connections
$currentconnection = $this->getUserConnection();


# check if user are logged
if( isset($_SESSION['vtxUser']) && isset($_SESSION['vtxUser'])  ):

	exit(header("location:/#request=GlobalPanel()") );

# display user data
else:

	if(isset($_POST['activeform'])):
	
		# verificar campos
		$this->vLoadClass($this->vconf['path'],"//vActions.phar/class.forms.php");
		
		# start new class
		$v = new forms();
		
		$v->classeErro = 'requestError';
		 
		$falha[0]= $v->validatorField('userCode'," + Digite o codigo da imagem");
		
		# check mail
		if(!empty($_POST['userMail']) ):
			$msgErro = $v->validatorMail($_POST['userMail'],$this->strPut("Digite seu e-mail corretamente"));
			$falha[1] = $v->validatorFieldSet('userMail',$msgErro);
		endif;
		
		# check pass valid
		if(!empty($_POST['userPass'])  ):
			$msgErro  = $v->validatorPassword($_POST['userPass'],6,$this->strPut("Senha inválida. "));
			$falha[2] = $v->validatorFieldSet('userPass',$msgErro);
		endif;			
	
		# check pass e passtrue is equal
		if(!empty($_POST['userCode']) ):
			$msgErro  = $v->validatorEqual(strtolower($_SESSION['authValor']),strtolower($_POST['userCode']),$this->strPut("Código de segurança não confere"));
			$falha[3] = $v->validatorFieldSet('userCode',$msgErro);
		endif;		
	
		# check all failures
		$falhas = implode("",$falha);
	
		if( strlen($falhas) > 0 ):
		
			exit($this->vForceStop("0|".nl2br($v->errorForm)."|{$falhas}"));
	
		else:
	
			$userKeyLog = trim($_POST['userMail']);
			$userKeyPas = sha1(trim($_POST['userPass']));
			
			# data base preparemente
			$dryquery =  " SELECT id,nome FROM vtx_system_access ";
			$dryquery .= " WHERE mail1='{$userKeyLog}'  ";
			$dryquery .= " and userkey='{$userKeyPas}' ";
			
			$dquery = $this->adb->query($dryquery);
	
			if ($this->adb->error):
			
				try {   
					throw new Exception("MySQL error {$this->adb->error} <br> Query:<br> {$inQuery}", $this->adb->errno);   
				} catch(Exception $e ) {
					exit($this->vForceStop($e->getCode(). " - ". $e->getMessage() ));
				}		
			
			else:
					
			if( $dquery->num_rows > 0 ):
			
				$dds = $dquery->fetch_array(MYSQLI_ASSOC);
				
				$_SESSION['userNom'] = $this->strPrint($dds['nome']);
				$_SESSION['vtxUser'] = $this->clearNumeric($dds['id']);
				$_SESSION['vtxEnter'] = time();
				
				$this->set("formCod",1);
				$this->set("formMsg",md5('painel-do-usuario').',0,2,0,'.time().'.htm');
				$this->set("formFields",'0');
				$this->set("errologin",'',true); 
				
				$dquery->free_result();
				
				exit($this->vForceStop("1|Acesso Permitido|?request=GlobalPanel()"));
			else:
				
				exit($this->vForceStop("0|Login ou senha invalidos"));
					
			endif;
			
			endif;
			
		endif;
	
	endif;

	$this->set("navigator",strstr($_SERVER['HTTP_USER_AGENT'], "Safari") ? "jquery.js":"jquery.min.js.gz" );
	$this->set("captcha","captcha,0,0,0,figura.jpg");
	
	$this->set("siteAno",date('Y') );
	$this->set("siteBase","{$_SERVER['HTTP_HOST']}");
	$this->set("userconect",gethostbyaddr($currentconnection) );
	$this->set("userip",$currentconnection );

	# set base form
	$this->vInclude("/vortex.phar/xuser/forgot/tpl/base.htm","incMidiax",$this->vconf['path']);

	# $this->vForcePlugin($this->vconf['path'],'/vortex.phar

	# force layout display
	$this->vForcePlugin($this->vconf['path'],'/xuser/login/');
	
endif;
?>