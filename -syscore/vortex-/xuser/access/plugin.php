<?php
# get user connections
 $currentconnection = $this->getUserConnection();

# check if user are logged
if( isset($_SESSION['vtxUser']) && isset($_SESSION['vtxEnter']) && isset($_SESSION['vtxEnter'])  && $_SESSION['vtxEnter'] >  time() ):

	exit(header("location:/#") );

# display user data
endif;


if(isset($_POST['activeform'])):

		# verificar campos
		$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.forms.php");
		
		# start new class
		$v = new forms();
		
		$v->classeErro = 'requestError';
		 
		$falha[]= $v->validatorField('userCode'," + Digite o codigo da imagem");
		
		# check mail
		if(!empty($_POST['userMail']) ):
			$msgErro = $v->validatorMail('userMail',$this->strPut("Digite seu e-mail corretamente"));
			$falha[] = $v->validatorFieldSet('userMail',$msgErro);
		endif;
		
		# check pass valid
		if(!empty($_POST['userPass'])  ):
			$msgErro  = $v->validatorPassword($_POST['userPass'],6,$this->strPut("Senha inválida. "));
			$falha[] = $v->validatorFieldSet('userPass',$msgErro);
		endif;			
	
		# check pass e passtrue is equal
		if(!empty($_POST['userCode']) && isset($_SESSION['authValor']) ):
			
			$src = array("l","o","1","g","j");
			$dst = array("i","0","l","6","i");
			
			$pUserKey = str_replace($src,$dst, strtolower($_POST['userCode']));
			$pSessKey = str_replace($src,$dst, strtolower($_SESSION['authValor']));
					
			$msgErro  = $v->validatorEqual($pSessKey,$pUserKey,$this->strPut("Código de segurança não confere"));
			$falha[] = $v->validatorFieldSet('userCode',$msgErro);
		
		endif;

		# check a valid captcha
		if(!isset($_SESSION['authValor'])  ):
			$falha[] = $v->validatorFieldSet('authValor',"Chave de seguranca invalida");
		endif;

		# check if user mail exists
		if(!empty($_POST['userMail']) ):
			
			$msgErro = $v->validatorTotalTable('vtx_system_access',"mail1='{$_POST['userMail']}' ",$this->adb) <=0 ? "+ E-mail não cadastrado " : '';
			$falha[] = $v->validatorFieldSet('userMail',$msgErro);
			
		endif;	

			
		# check all failures
		$falhas = implode("",$falha);
	
		if( strlen($falhas) > 0 ):
		
			exit($this->vForceStop("0|".nl2br($v->errorForm)."|{$falhas}"));
	
		else:
	
			# prepare data
			$userKeyLog = trim($_POST['userMail']);
			$userKeyPas = sha1(trim($_POST['userPass']));
			
			# register from acess log
			$uconn = $this->getUserConnection();

			# data base preparemente
			$dryquery  = " SELECT @UAid :=t1.id AS tids, t1.nome,t1.grupo,t1.tipo,t1.vinculo,t2.level ";
			$dryquery .= " FROM vtx_system_access AS t1 ";
			$dryquery .= " LEFT JOIN vtx_system_group AS t2 ON t1.grupo = t2.id ";
			$dryquery .= " WHERE t1.mail1='{$userKeyLog}'  ";
			$dryquery .= " AND t1.userkey='{$userKeyPas}' ";
			$dryquery .= " AND t1.autorizado='1'; ";


			$dryquery .= " INSERT INTO vtx_system_alog( user, server, conection, `date` ) VALUES ";
			$dryquery .= " ( @UAid,'{$currentconnection}','{$uconn}','".time()."' ) ";

			# execute data
			$this->adb->multi_query($dryquery);
			
			$b = 0;
			do {
			
			if ($result = $this->adb->store_result()) :
			
			$a = 0;
			while ($dds = $result->fetch_array(MYSQLI_ASSOC)):
			
				switch($b):
									  
					# outras materias da coluna
					case(0):	
					
						$_SESSION['vtxName']  = $this->strPrint($dds['nome']);
						$_SESSION['vtxUser']  = $this->clearNumeric($dds['tids']);
						$_SESSION['vtxEnter'] = time() + ((60*60)*3);
						$_SESSION['vtxGrupo'] = $this->clearNumeric($dds['grupo']);
						$_SESSION['vtxLevel'] = $this->clearNumeric($dds['level']);
						$_SESSION['vtxType']  = $this->clearNumeric($dds['tipo']);
						$_SESSION['vtxLink']  = $this->clearNumeric($dds['vinculo']);
						
						setcookie("accessMail",$_POST['userMail'],time()+(60*60*24*365) );
						
					break;
				
				endswitch;
			
			endwhile;
			
			$result->free();
			endif;
			
			if ($this->adb->more_results()):
				$b++;
			endif;
			
			} while ($this->adb->more_results() && $this->adb->next_result());
			
			# if error
			if ($this->adb->errno):
				
				if ($b == 1) :
				
					exit($this->vForceStop( "0| 5520 :: Login ou senha invalidos. Tente Novamente."));
					
				else:
				
					exit($this->vForceStop( "0| Erro encontrado ao processar linha {$b} :: Query error = ".$this->adb->error));
				
				endif;
				
			endif;
			
			# check user login success
			echo isset($_SESSION['vtxUser']) ? "1|Carregando Vortex ACM. Aguarde..." : "0|5521 :: Login ou senha inválidos. Verifique seus dados.";
			
			
			
			
	endif;

endif;

## -----------------------------------
##
## 
## 
## -----------------------------------
	$this->set("navigator",strstr($_SERVER['HTTP_USER_AGENT'], "Safari") ? "jquery.js":"jquery.min.js.gz" );
	$this->set("captcha","captcha,0,0,0,figura.jpg?request=".time() );
	
	$this->set("siteAno",date('Y') );
	$this->set("siteBase1","{$_SERVER['HTTP_HOST']}");
	
	#$this->set("userconect",gethostbyaddr($currentconnection) );
	#$this->set("userip",$currentconnection );
	$this->set("userget",0);
	
	# force layout display
	# $this->vForcePlugin($this->vconf['viewfix'],'/xuser/access/');

	# set base form
	$this->vInclude("/vortex.phar/xuser/access/tpl/base.htm","incMidiax",$this->vconf['path']);

	# force layout display
	$this->vForcePlugin($this->vconf['path'],'/xuser/login/');


?>