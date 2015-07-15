<?php
class ui extends text {

		public static $vPluginPath;
		public static $vPluginRequest;
	
		#####################################################################
		# Load CSS or JS to internal plugin
		# $plugin 	= /dir1/dir2/
		# $type 	= css or js
		# $target 	= string
		#####################################################################
		public function vParseStatic($plugin,$type,$target){
				
			if( is_file("{$plugin}tpl/base.{$type}") ):
			
				$source = file_get_contents("{$plugin}/tpl/base.{$type}");
				
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
		public function vForcePlugin($path,$plugin){
			
			if(is_file("{$path}{$plugin}tpl/base.htm")):
			$process = file_get_contents("{$path}{$plugin}tpl/base.htm");
	
			#$aa =  $this->parse($x);
			#echo $aa;
			
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
		public function vForceTPL($path,$plugin){
			
			if(is_file("{$path}{$plugin}tpl/base.htm")):

				$process = file_get_contents("{$path}{$plugin}tpl/base.htm");
				$this->defaultTPL = "{$plugin}/tpl/base.htm";
			
			endif;
			
		}

	
		#####################################################################
		# Force plugin load
		# $path = /dir
		# $type = /dir1/dir2/
		#####################################################################
		public function vParse($plugin,$target){
			
			$content = file_get_contents("{$plugin}tpl/base.htm");
			$content = $this->parse($content);	
			$this->set($target,$content);
		}
		
	
		#####################################################################
		# Force plugin load
		# $path 	= /dir
		# $type 	= /dir1/dir2/
		#####################################################################
		public function vForceStop($message){
			
			$this->currentMSG = $message;
			
			
			## PHP 5.3
			/*
			$this->globalFunction = function($r,$conf){
			
				exit( print($r->currentMSG) );
			
			};
			*/
			
			## PHP 5.2
			$this->globalFunction =	create_function('$r,$conf','exit( print($r->currentMSG));');
		}


		#####################################################################
		# Include Static File
		# $path = /dir
		# $plugin = /dir1/dir2/
		#####################################################################
		public function vInclude($plugin,$tgt){
			
				$process = is_file("{$this->vconf['pathinc']}{$plugin}") ? file_get_contents("{$this->vconf['pathinc']}{$plugin}") : "Include not found in: {$this->vconf['pathinc']}{$plugin}";
				
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
			  $ip=$_SERVER['HTTP_CLIENT_IP'];
			
			//to check ip is pass from proxy
			elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) :
			  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
			  
			else:
			  $ip=$_SERVER['REMOTE_ADDR'];
			endif;
			
			return $ip;
		}	
	
	
	############################################################################
	# make web pagination
	############################################################################
	public function vBrandTags($target = "navegacao", $breaker = " / ",$texto = ""){
		
		if( isset( $this->vtxNavigator['txt'] ) ) :
			
			$tags = $this->vtxNavigator;
					
			$texto = empty($texto) ? "Você esta em : " : $texto;
					
			$texto .= !isset($tags['url'][0]) ? "<a href='/'>" :"";
			$texto .= !isset($tags['txt'][0]) ? "Página Principal</a>" : "";
			$texto .= !isset($tags['txt'][0]) ? $breaker : "";
			
			while(list($k,$v) = each($tags['txt'])):
				
				$texto .= isset($tags['url'][$k]) ? "<a href='{$tags['url'][$k]}'>" : "";
				$texto .= "{$tags['txt'][$k]}";
				$texto .= isset($tags['url'][$k]) ? "</a> {$breaker} " : "  ";
				
			endwhile;
			
			$texto = substr($texto,0,-1);
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
			
			$texto = "";
			
			while(list($k,$v) = each($tags['txt'])):
				
				$texto .= isset($tags['url'][$k]) ? "<a href='{$tags['url'][$k]}'>" : "";
				$texto .= "{$tags['txt'][$k]}";
				$texto .= isset($tags['url'][$k]) ? "</a> {$this->breaker} " : "  ";
				
			endwhile;
			
			$texto = substr($texto,0,-1);
			$this->set($target,$texto,true);
			
		else:
		
			$this->set($target,"",true);
		
		endif;
		
	}		
	############################################################################
	# get fast item do select
	############################################################################
		public static $fastMask;
		public static $checked;
		
		public function fastSelectMenu($table,$id,$field,$target=false,$condition=false,$order=false){
			
			$orders = empty($order) ?  " ORDER BY {$field} ASC " : " ORDER BY {$order} ";
			$condition = !empty($condition) ? " WHERE {$condition} "  : " ";
			
			$q = " SELECT {$id},{$field} FROM {$table} {$condition} {$orders}  ";
			
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
		public function fastSelectArray($array,$target,$uid=false,$utxt=false,$limit=false){
			
			$vid = !empty($uid)   ? $uid  : 'id';
			$vtxt = !empty($utxt) ? $utxt : 'txt';
			
			while( list($k,$v) = each($array) ):
			
				$list[$k][$vid]  = $k;
				$list[$k][$vtxt] = $v;
			
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
	
	
	### resize elements
	### $markup = <img width='100' height='1000 />
	### $dimensions = array('width'=>$width, 'height'=>$height) 
	
	public function resizeMarkup($markup, $dimensions){
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
		
		return preg_replace($patterns, $replacements, $markup);
	}
	
	
	public function vSetLogPath($a,$b){
	
		$this->vPluginPath = $a;
		$this->vPluginRequest  = $b;
		
	}
	
	### register user actions for logs
	### $markup = <img width='100' height='1000 />
	### $dimensions = array('width'=>$width, 'height'=>$height)
	
	public function vRegisterLogs($autoregs=false){
		
		$agora = time();
		$conn  = $this->getUserConnection();
		
		$query  = "INSERT INTO `vtx_system_logs` (`user` ,`data` ,`plugin` ,`modulo` , `action`, `ip`) ";
		$query .= "VALUES ('{$_SESSION['vtxUser']}', '{$agora}' , '{$this->vPluginPath}', '{$this->vPluginRequest}',' testando', '{$conn}'); ";
				
		$this->adb->query($query);

		if ($this->adb->error):
			
			try {   
				throw new Exception("MySQL error {$this->adb->error} <br> Query:<br> {$query}", $this->adb->errno);   
			} catch(Exception $e ) {
				exit( $this->vForceStop("0|".$e->getCode(). " - ". $e->getMessage() ."|var e = 0"));
			}
		
		else:
			return true;
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
	
	
	public function vQuery($vquery){
		
		$this->vRegisterLogs();

		return $this->adb->query($vquery);
	
	}




## order array
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

	
}
?>