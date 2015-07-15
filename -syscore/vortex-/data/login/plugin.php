<?php
# get user connections
$currentconnection = $this->getUserConnection();

# check if user are logged
if( isset($_SESSION['vtxUser']) || isset($_SESSION['vtxEnter']) || isset($_SESSION['vtxGrupo'])  ):

	# exit($this->vForceStop("<div class=''>Você já está logado</div>") );

# display user data
endif;

	if(isset($_POST['activeform'])):

		# verificar campos
		$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.forms.php");
		
		# start new class
		$v = new forms();
		
		$v->classeErro = 'requestError';
		 
		$falha[]= $v->validatorField('userMail'," + Digite o seu email <br />");
		$falha[]= $v->validatorField('userPass'," + Digite sua senha <br />");
		
		# check mail
		if(!empty($_POST['userMail']) ):
			$msgErro = $v->validatorMail('userMail',$this->strPut("Digite seu e-mail corretamente <br />"));
			$falha[] = $v->validatorFieldSet('userMail',$msgErro);
		endif;
		
		# check pass valid
		if(!empty($_POST['userPass'])  ):
			$msgErro  = $v->validatorPassword($_POST['userPass'],6,$this->strPut("Senha inválida. <br />"));
			$falha[] = $v->validatorFieldSet('userPass',$msgErro);
		endif;			
	

		# check if user mail exists
		if(!empty($_POST['userMail']) ):
			$msgErro = $v->validatorTotalTable('vtx_system_access',"mail1='{$_POST['userMail']}' ",$this->adb) <=0 ? "+ E-mail não cadastrado <br />" : '';
			$falha[] = $v->validatorFieldSet('userMail',$msgErro);
		endif;	

			
		# check all failures
		$falhas = implode("",$falha);
	
		if( strlen($falhas) > 0 ):
		
			exit($this->vForceStop("0|".nl2br($v->errorForm)."|{$falhas}"));
	
		else:
	
			$userKeyLog = trim($_POST['userMail']);
			$userKeyPas = sha1(trim($_POST['userPass']));
			
			# data base preparemente
			$dryquery =  " SELECT t1.id,t1.nome,t1.grupo,t1.tipo,t1.vinculo,t2.level ";
			$dryquery .= " FROM vtx_system_access AS t1 ";
			$dryquery .= " LEFT JOIN vtx_system_group AS t2 ON t1.grupo = t2.id ";
			$dryquery .= " WHERE t1.mail1='{$userKeyLog}'  ";
			$dryquery .= " AND t1.userkey='{$userKeyPas}' ";
			$dryquery .= " AND t1.autorizado='1' ";
			$dryquery .= " AND t1.grupo=t2.id ";
			
			$dquery = $this->adb->query($dryquery);
	
			if ($this->adb->error):
			
				try {   
					throw new Exception("MySQL error {$this->adb->error} <br> Query:<br> {$inQuery}", $this->adb->errno);   
				} catch(Exception $e ) {
					exit($this->vForceStop($e->getCode(). " - ". $e->getMessage() ));
				}		
			
			else:
			
			# if found data	
			if( $dquery->num_rows > 0 ):
			
				$dds = $dquery->fetch_array(MYSQLI_ASSOC);
				
				$_SESSION['alerts'] = array();
				
				$_SESSION['vtxName']  = $this->strPrint($dds['nome']);
				$_SESSION['vtxUser']  = $this->clearNumeric($dds['id']);
				$_SESSION['vtxEnter'] = time() + ((60*60)*3);
				$_SESSION['vtxGrupo'] = $this->clearNumeric($dds['grupo']);
				$_SESSION['vtxLevel'] = $this->clearNumeric($dds['level']);
				$_SESSION['vtxType']  = $this->clearNumeric($dds['tipo']);
				$_SESSION['vtxLink']  = $this->clearNumeric($dds['vinculo']);
				
				//$dquery->free_result();
				
				# register from acess log
				$uconn = $this->getUserConnection();
				
				$drySQL = "INSERT INTO vtx_system_alog (user,server,conection,date) ";
				$drySQL .= "VALUES('{$dds['id']}','{$currentconnection}','{$uconn}','".time()."');";
				
				$dquery = $this->adb->query($drySQL);
				
				if ($this->adb->error):
				
					try {   
						throw new Exception("MySQL error {$this->adb->error} <br> Query:<br> {$drySQL}", $this->adb->errno);   
					} catch(Exception $e ) {
						exit($this->vForceStop($e->getCode(). " - ". $e->getMessage() ));
					}		
				
				else:

					# confirm user login
					exit($this->vForceStop("1|Acesso Permitido|?request=GlobalPanel()"));
					
				endif;
			
			else:
				
				exit($this->vForceStop("0|Login ou senha invalidos"));
					
			endif;
			
			endif;
			
		endif;
	
	endif;

	$this->set("navigator",strstr($_SERVER['HTTP_USER_AGENT'], "Safari") ? "jquery.js":"jquery.min.js.gz" );
	$this->set("captcha","captcha,0,0,0,figura.jpg?request=".time() );
	
	$this->set("siteAno",date('Y') );
	$this->set("siteBase","{$_SERVER['HTTP_HOST']}");
	
	$this->set("userconect",gethostbyaddr($currentconnection) );
	$this->set("userip",$currentconnection );
	
	# force layout display
	# $this->vForcePlugin($this->vconf['viewfix'],'/xuser/access/');

	# set base form
	$this->vInclude("/vortex.phar/xuser/access/tpl/base.htm","incMidiax",$this->vconf['path']);

	# force layout display
	$this->vForcePlugin($this->vconf['path'],'/data/login/');

?>