<?php
/* ############################################################### Vortex ACM 2.0  ############################################################### /*
/* Licenca para uso de cliente final - Proibida distribuicao nao autorizada
/# 
/# Software protegido pela legislacao brasileira conforme rege
/# lei dos direitos autorais nº 6910 de 19 de Fevereiro de 1998
/# Proibida distribuicao nao autorizada
/# 
/# www.eminuto.com
/# 
/* ############################################################### Vortex ACM 2.0  ###############################################################*/


class ui extends text {

		public $vPluginPath;
		public $vPluginRequest;
		public $rootResetPath;
	
		#####################################################################
		# Load CSS or JS to internal plugin
		# $plugin 	= /dir1/dir2/
		# $type 	= css or js
		# $target 	= string
		#####################################################################
		public function vParseStatic($plugin,$type,$target,$file=false){
			
			$pway = !empty($file) ? $file : "base.{$type}";
			
			if( is_file("{$plugin}tpl/{$pway}") ):
			
				$source = file_get_contents("{$plugin}tpl/{$pway}");
				
				if($type == 'css'):
					$file  = "<style type='text/css'>\n{$source}\n</style>\n\n";
				else:
					$file  = "<script type='text/javascript' charset='UTF-8'>\n{$source}\n</script>\n\n";
				endif;
				
				$this->append($target,$file);
	
			endif;
			
		}
	
		#####################################################################
		# Load CSS or JS to internal plugin
		# $plugin 	= /dir1/dir2/
		# $type 	= css or js
		# $target 	= string
		#####################################################################
		public function vAddStatic($plugin,$type,$target){
			
				if($type == 'css'):
					$file  = "<link rel='stylesheet' type='text/css' href='{$plugin}'>\n";
				else:
					$file  = "<script type='text/javascript' charset='UTF-8' src='{$plugin}'></script>\n";
				endif;
				
				$this->append($target,$file);
				
		
		}
	
		#####################################################################
		# Force plugin load
		# $path 	= /dir
		# $type 	= /dir1/dir2/
		#####################################################################
		public function vLoadClass($path,$class){
			
			if(is_file($path.$class)):
			
				include $path.$class;
			
			else:
			
				exit("1000 :: Failure to load class in {$path}{$class}" );
				
			endif;
		}
		
		#####################################################################
		# Force plugin load
		# $path 	= /dir
		# $type 	= /dir1/dir2/
		#####################################################################
		public function vForcePlugin($pathv,$plugin){
			
			is_dir($pathv) && $this->rootResetPath == 1 ? $this->globalSetPath = $pathv : "";
						
			if(is_file("{$pathv}{$plugin}tpl/base.htm")):
			
			$process = file_get_contents("{$pathv}{$plugin}tpl/base.htm");
			
			$this->cacheFile = "{$plugin}/tpl/base.htm";
			exit();
			
			else:
				$this->cacheFile = "{$plugin}/tpl/base.htm";
				exit();
			endif;
			
		}

		#####################################################################
		# Force template load
		# $path = /dir
		# $plugin = /dir1/dir2/
		#####################################################################
		public function vForceTPL($path,$plugin,$globalpath=false){
			
			$this->defaultTPL = "{$plugin}/tpl/base.htm";
			$this->globalSetPath = $path;
			
			exit();
			
		}
		
		#####################################################################
		# Force template fro plugin
		# $path = /dir
		# $plugin = /dir1/dir2/
		#####################################################################
		
		public function vSetCurrentTPL($path,$plugin){
			
			
		 	$this->vCurrentTPL = "{$path}{$plugin}tpl/base.htm";	
			
		}

	
		#####################################################################
		# Force plugin load
		# $path = /dir
		# $type = /dir1/dir2/
		#####################################################################
		public function vParse($plugin,$target,$vxBack = false){
			
			!is_file("{$plugin}tpl/base.htm") ? exit($this->vForceStop("Arquivo não encontrado em: {$plugin}")) : "";
			
			$content = file_get_contents("{$plugin}tpl/base.htm");
			$content = $this->parse($content);	
			
			if( !empty($vxBack) ):
				 return $content;
			else:
				$this->set($target,$content);
			endif;
			
		}
		
	
		#####################################################################
		# Force plugin load
		# $path 	= /dir
		# $type 	= /dir1/dir2/
		#####################################################################
		public function vForceStop($message){
						
			$this->currentMSG = $message;
			
			$this->globalFunction =	create_function('$r,$conf','exit(print($r->currentMSG)); ;');
		}


		#####################################################################
		# Include Static File
		# $path = /dir
		# $plugin = /dir1/dir2/
		#####################################################################
		public function vInclude($plugin,$tgt,$path=false){
			
				$incPath = empty($path) ? $this->vconf['pathinc'] : $path;
				
				$process = is_file("{$incPath}{$plugin}") ? file_get_contents("{$incPath}{$plugin}") : "Include not found in: {$incPath}{$plugin}";
				
				$this->set($tgt,$process,true);
		}


		
	############################################################################
	# change path to imagem
	############################################################################
	public function getImageThumb($file,$sep=false){
			
			$fileinfo = str_replace("jpeg","jpg",$file);
			$filesave = substr($fileinfo,0,-4); 
			$fileinfo = substr($fileinfo,-4);
		
			$sep = !empty($this->vconf['imageBreaker']) ? $this->vconf['imageBreaker'] : "";
			
			if(is_file("{$this->vconf['rootpath']}{$filesave}{$sep}{$fileinfo}" )):
			
				return "{$filesave}{$sep}{$fileinfo}";	
			
			else:
				return "/corefix/image/nopics.png";
			endif;
	}
	
	public function makeImageThumb($file){
		
			$fileinfo = str_replace("jpeg","jpg",$file);
			$filesave = substr($fileinfo,0,-4); 
			$fileinfo = substr($fileinfo,-4);
		
			$sep = $this->vconf['imageBreaker'];
			
			#return "{$filesave}{$sep}{$fileinfo}";
			return "{$filesave}{$sep}{$fileinfo}";
	}
	
	############################################################################
	# update text from title
	############################################################################
	public function updateTitle($t){
		$this->set("siteTitle",$t);
	}
	
	############################################################################
	# get real user ip
	############################################################################
	
		public function getUserConnection(){
			
			//check ip from share internet
			if (!empty($_SERVER['HTTP_CLIENT_IP'])) :
			  return $_SERVER['HTTP_CLIENT_IP'];
			
			//to check ip is pass from proxy
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) :
			  return $_SERVER['HTTP_X_FORWARDED_FOR'];
			  
			else:
			  return $_SERVER['REMOTE_ADDR'];
			endif;
			
		}	
	
	
	############################################################################
	# make web pagination
	############################################################################
	public function vBrandTags($target = "navigator",$texto = ""){
		
		if( isset( $this->vtxNavigator['txt'] ) ) :
			
			$tags = $this->vtxNavigator;
					
			$texto = empty($texto) ? "Você está em : " : $texto;
					
			$texto .= !isset($tags['url'][0]) ? "<a href='/'>" :"";
			$texto .= !isset($tags['txt'][0]) ? "Página Principal </a>" : "";
			$texto .= !isset($tags['txt'][0]) ? $this->breaker : "";
			
			$ttags = count($tags['txt']);
			$x = 1;
			while(list($k,$v) = each($tags['txt'])):
				
				$texto .= isset($tags['url'][$k]) ? "<a href='{$tags['url'][$k]}'>" : "";
				$texto .= "{$tags['txt'][$k]}";
				$texto .= isset($tags['url'][$k]) ? "</a> " : "  ";
				$texto .= ($x+1) < $ttags ? $this->breaker : ""; 
				
				$x++;
			endwhile;
			
			$this->set($target,$texto,true);
			
		else:
		
			$this->set($target,"",true);
		
		endif;
		
	}
	
	public function updateURL($url){
		
		return $this->vtxNavigator = $url;
	
	}


	## update internal header
	public $breaker = " + ";
	
	public function vMakeHeaders($target = "navigator"){
		
		if( isset( $this->vtxNavigator['txt'] ) ) :
			
			$tags = $this->vtxNavigator;
			
			$tlen = count($tags['txt']);
			
			$texto = "";
			
			$y = 0;
			$ct = isset($tags['url']) ? count($tags['url']) : 1;
			
			while(list($k,$v) = each($tags['txt'])):
				
				//if($y < ( $tlen ) ):
				
					$texto .= isset($tags['url'][$k]) && !empty($tags['url'][$k]) ? "<a href='{$tags['url'][$k]}'>" : "";
					$texto .= $tags['txt'][$k];
					$texto .= isset($tags['url'][$k]) && !empty($tags['url'][$k]) ? "</a>" : "";
					$texto .= ($y+1) < $ct ? $this->breaker : "";
				
				//endif;
			
			$y++;	
			endwhile;
			
			$this->set($target,$texto,true);
			
		else:
		
			$this->set($target,"",true);
		
		endif;
		
	}		
	############################################################################
	# get fast item do select
	############################################################################
		public  $fastMask;
		public  $checked;
		
		public function fastSelectMenu($table,$id,$field,$target=false,$condition=false,$order=false){
			
			$orders = empty($order) ?  " ORDER BY {$field} ASC " : " ORDER BY {$order} ";
			$condition = !empty($condition) ? " WHERE {$condition} "  : " ";
			
			$q = " SELECT SQL_CACHE {$id},{$field} FROM {$table} {$condition} {$orders}  ";
			
			$dquery = $this->adb->query($q);
	
			if ($this->adb->error):
				
				try {   
					throw new Exception("MySQL error {$this->adb->error} <br> Query:<br> {$q}", $this->adb->errno);   
				} catch(Exception $e ) {
					exit(print("0|".$e->getCode(). " - ". $e->getMessage() ."|var e = 0"));
				}
			
			else:
			
			
			$a=0;
			while( $dds = $dquery->fetch_array(MYSQLI_ASSOC)  ):
			
				$list[$a][$id]    = $dds[$id];
				$list[$a][$field] = !empty($this->fastMask) ? $this->strMask($this->fastMask,$dds[$field]) : $this->strPrint($dds[$field]);
			
				$list[$a]['check']=  isset($this->checked) && is_array($this->checked) && in_array($dds[$id],$this->checked) ? "checked='checked'" : "";
				
			$a++;
			endwhile;
		
			endif;
			
			$this->set($target,isset($list) ? $list : '' );	
		
		}
		
	
	############################################################################
	# format item from menu
	############################################################################
		public function fastSelectArray($array,$target,$begin=false,$stop=false,$join=1,$uid=false,$utxt=false){
			
			$vid  = !empty($uid)   ? $uid  : 'id';
			$txt = !empty($utxt) ? $utxt : 'txt';
			
			$vStart = !empty($begin) ? $begin : 0;
			$vStop  = !empty($stop) ? $stop : count($array);
			
				
				$a = 0;
				while(list($k,$v) = each($array)):
				
						$display = !empty($join) ? $this->zeroAdd(3,$k) : $k;
						
						if(  !empty($begin) && !empty($stop) ):
						
							if($a > $vStart &&  $a < $vStop ):
							
								$list[$a][$vid]  = $k;
								$list[$a][$txt] = !empty($join) ? "{$display} - {$v}" : $v ;
							
							endif;
							
						elseif(!empty($begin) && empty($stop) ):
							
							if($a < $vStart  ):
							
								$list[$a][$vid]  = $k;
								$list[$a][$txt] = !empty($join) ? "{$display} - {$v}" : $v ;
							
							endif;
					
						else:
							$list[$a][$vid]  = $k;
							$list[$a][$txt] = !empty($join) ? "{$display} - {$v}" : $v ;
						endif;
				
				$a++;		
				endwhile;
		
			
			$this->set($target,isset($list) ? $list : '' );	
			
		}		
	
	
	
	# show current date
	public function showDate($onlyDay = 1){
		
		$dia[0]='Domingo';
		$dia[1]='Segunda-feira';
		$dia[2]='Ter&ccedil;a-feira';
		$dia[3]='Quarta-feira';
		$dia[4]='Quinta-feira';
		$dia[5]='Sexta-feira';
		$dia[6]='S&aacute;bado';
		
		$meses[1]='Janeiro';
		$meses[2]='Fevereiro';
		$meses[3]='Março';
		$meses[4]='Abril';
		$meses[5]='Maio';
		$meses[6]='Junho';
		$meses[7]='Julho';
		$meses[8]='Agosto';
		$meses[9]='Setembro';
		$meses[10]='Outubro';
		$meses[11]='Novembro';
		$meses[12]='Dezembro';
		
		$diaSemana = $dia[date('w')];
		$hoje = date('d');
		$mes = $meses[date('n')];
		$ano = date('Y');
		
		
		return $onlyDay == 1 ? "{$diaSemana}, {$hoje} de {$mes} de {$ano}." : "{$hoje} de {$mes} de {$ano}.";
		
		}
	
	###	
		public function showGreetings($tempo=false){
		
			$tempo = (!empty($tempo)?$tempo:time());
		
			$hora = date('H',$tempo);
			if($hora >=6 && $hora <12):
			return " Bom Dia!";
			elseif($hora >= 12 && $hora <18):
			return " Boa Tarde! ";
			elseif($hora >= 18 && $hora <00):
			return " Boa Noite!";
			else:
			return " Boa Noite!";
			endif;		
		
		}
	
	# alias to resizeMarkup
	public function resizeMarkup($markup, $dimensions,$extraPar=false){
		
		return $this->resetEmbeds($markup, $dimensions,$extraPar=false);

	}
	
	
	### resize elements
	### $markup = <img width='100' height='1000 />
	### $dimensions = array('width'=>$width, 'height'=>$height) 
	### $params = add params: ?rel=0&showinfo=0

	public function resetEmbeds($markup, $dimensions,$extraPar=false){
		
		$w = $dimensions['width'];
		$h = $dimensions['height'];
		
		$patterns = array();
		$replacements = array();
		if( !empty($w) )
		{
		$patterns[] = '/width="([0-9]+)"/';
		$patterns[] = '/width:([0-9]+)/';
		
		$replacements[] = 'width="'.$w.'"';
		$replacements[] = 'width:'.$w;
		}
		
		if( !empty($h) )
		{
		$patterns[] = '/height="([0-9]+)"/';
		$patterns[] = '/height:([0-9]+)/';
		
		$replacements[] = 'height="'.$h.'"';
		$replacements[] = 'height:'.$h;
		}
		
		$finalRUL =  preg_replace($patterns, $replacements, $markup);
		
		$finalURL =  !empty($extraPar) ? $html=preg_replace('/(?<=src=")[^"]+/',"$0{$extraPar}",$finalRUL) : $finalRUL;
		
		return $finalURL;
		
	}
	
	### resize elements
	### $a = /root/coreweb
	### $b = /plugin/tpl.htm

	public function vSetLogPath($a,$b){
	
		$this->vPluginPath = $a;
		$this->vPluginRequest  = $b;
		
	}
	
	
	# disable register log
	public function disableLogs(){
		
		$this->noAutoregs = 1;
		
	}
	### register user actions for logs
	###  $autoregs = 1 auto register, 0 not register
	var $__vtxDBLink__ = "";
	var $vRegisterAction = "";
	
	public function vRegisterLogs($multi=false){
		
		$agora = time();
		$conn  = $this->getUserConnection();
		$dbcom = !empty($this->__vtxDBLink__) ? $this->__vtxDBLink__ : $this->adb;
		
		$query  = "INSERT INTO `{$this->vconf['datadb']}`.`vtx_system_logs` (`user` ,`data` ,`plugin` ,`modulo` , `action`, `ip`) VALUES ";
		$query .= "('".$_SESSION['vtxUser']."',";
		$query .= "'{$agora}' , ";
		$query .= "'".$this->vxEncoder($this->vPluginPath)."',";
		$query .= "'".$this->vxEncoder($this->vPluginRequest)."',";
		$query .= "'{$this->vRegisterAction}',";
		$query .= "'{$conn}' ); ";

		# single insert
		if( empty($multi) ):
		
			return $dbcom->query( $query );			
		
		# or multi insert
		else:
		
			return $query;
	
		endif;
		
		# $_SESSION['vtxUser']
		# time()
		# plugin
		# modulo
		# action
		# acao 
		# ip = $_SERVER['REMOTE_ADDR'];
		# remote = getUserConnection();
		# acao $this->vcon['vPluginPath']
	}
	

	# custom vortex sql query execute 
	
	public function vQuery($vquery,$history=false,$multi=false,$debug=false){
		
		
		#save logs
		$logs = $this->vRegisterLogs($multi);
		$this->vRegisterAction = $history;
		
		if( $multi == false ):
			
			# execute querys
			return $this->adb->query( $vquery );		
		
		else:
			
			# prepare query
			$vquery = substr($vquery,0,-1);
			$vquery = "{$vquery};{$logs}";
			
			echo $debug == 1 ? $vquery : "";
			
			$this->adb->multi_query( $vquery );

			if ($this->adb->error):
				
				try {   
					throw new Exception("MySQL error {$this->adb->error} ", $this->adb->errno);   
				} catch(Exception $e ) {
					exit($this->vForceStop("0|Error No: ".$e->getCode(). " - ". $e->getMessage() ));
				}
			
			endif;
		
		endif;
		
		
	
	}
	
	## order array
	## $a default array
	## $subkey array to order
	public function vOderArray($a,$subkey) {
		
		foreach($a as $k=>$v) {
			$b[$k] = strtolower($v[$subkey]);
		}
		asort($b);
		
		foreach($b as $key=>$val) {
			$c[] = $a[$key];
		}
		
		return $c;
		
	}


public function calcula_juros_compostos($total,$meses,$taxa){
	 /*
	 M = C x (1 + i)t 
	 C = Capital inicial
	 i = taxa % por período de tempo
	 t = número de períodos de tempo
	 M = montante final = (capital + juros)
	 */
	 $formata_i = $taxa/100;
	 $i = pow((1+$formata_i),$meses);
	 $m = $total *$i;
	 return ($m);
}
}
?>