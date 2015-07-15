<?php
// $this->setCacheParams(1,15,1);
class vortex extends enginer {
	
	public $dirRestrict = array(".","..");
	public $vtxSystem  = "";
	public $vtxReverse = 3;
	public $modules;
	public $alarm = array();
	public $alertLicense = '';
	
	var $vtxMod = array();
	
#############################################################		
# read all modules on app
#############################################################	
public function appReadModules($vsrc,$dir,$prive=array() ){
		

	if(!is_dir($dir) ):
	
		exit($this->vForceStop("<h3 class='vtxAlerts vtxSysImportant toScreen'> 1015 :: Plugins desta aplicação não puderam ser carregados </h3>  "));
	
	else:
	
	if ($D1 = opendir($dir) ):
		
			$x = 0;
			$w = 0;
			$k = 0;
			while (false !== ($a = readdir($D1))):
			
				if( is_dir("{$dir}/{$a}") 
					&& is_file("{$dir}/{$a}/plugin.php")
					&& !is_file("{$dir}/{$a}/hidden.aur")
					&& !in_array($a,$this->dirRestrict)
					&& ( isset($prive[$a]) || $_SESSION['vtxType'] == 1  )
				):
				
					
					
					include "{$dir}/{$a}/plugin.php";
					# isset($prive[$a]) ?  print ("entrou em 1 {$app['name']} <br />") :  "";
					
					if( !isset($app['include']) || ( $app['include'] == 1 && isset($_SESSION['vadmin']) )) :
					
					$this->vtxMod[$x]['ptitle'] = $app['name'];
					$this->vtxMod[$x]['icon']  = "load-icons,0,7,0,{$vsrc},now.htm?icon={$a}/tpl/ico.png";
					
					## ready modules from app
					#############################################################
					$path2 = "{$dir}/{$a}";
					
					if ($D2 = opendir($path2) ):
					
						$y = 0;
						while (false !== ($b = readdir($D2))):
						
						
						
						# 	
						if( is_dir("{$path2}/{$b}") && 
						is_file("{$path2}/{$b}/plugin.php") &&
						!is_file("{$path2}/{$b}/hidden.aur")
						&& !in_array($b,$this->dirRestrict)
						&& (  is_array($prive) && isset($prive[$a][$b]) || ( $_SESSION['vtxType'] == 1 ) )
						):		
						
						# # # echo"{$a} - {$b} <br>";
									
						
							include "{$path2}/{$b}/plugin.php";
							# isset($prive[$a][$b]) ?  print ("entrou em  2 {$plugin['name']} <br />") :  "";
							
							$this->vtxMod[$x]['plugins'][$y]['icon']   = "load-icons,0,7,0,{$vsrc},now.htm?icon={$a}/{$b}/tpl/ico.png";
							$this->vtxMod[$x]['plugins'][$y]['modulo'] = $plugin['name'] ; 
							$this->vtxMod[$x]['plugins'][$y]['cls']    = $x > $this->vtxReverse ? "" : "";
							
							//-----------------------------------------
							$path3 = "{$path2}/{$b}";
							if ($D3 = opendir($path3) ):
					
							$w = 0;
							while (false !== ($c = readdir($D3))):
																
								if( is_dir("{$path3}/{$c}")
								&& is_file("{$path3}/{$c}/module.php")
								&& !is_file("{$path3}/{$c}/hidden.aur")
								&& !in_array($c,$this->dirRestrict)
								&& ( is_array($prive) && isset($prive[$a][$b][$c]) || $_SESSION['vtxType'] == 1 ) ):
								
								
								
								include "{$path3}/{$c}/module.php";
								
								# isset($prive[$a][$b][$c]) ?  print ("entrou em  3 {$module['name']} <br />") :  "";	
								
								$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['apps'] = $module['name']; 
								//$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['class']= $k > $this->vtxReverse ? "" : "";
								
								### -----------------------------
								$path4 = "{$path3}/{$c}";
								if ($D4 = opendir($path4) ):
									
									$z = 0;
									while (false !== ($d = readdir($D4))):
									
										if( is_dir("{$path4}/{$d}")
										&& is_file("{$path4}/{$d}/app.php")
										&& !is_file("{$path4}/{$d}/hidden.aur")
										&& !in_array($z,$this->dirRestrict)
										&& ( is_array($prive) && isset($prive[$a][$b][$c][$d]) || $_SESSION['vtxType'] == 1 )
										):
																				
											include "{$path4}/{$d}/app.php";
											
											# isset($prive[$a][$b][$c][$d]) ?  print ("entrou em  4 {$d} <br />") :  "";	
											
											# make complementar params
											$conf  = isset($app['conf']) ? "{$app['conf']}," : "0,";
											$pconf = isset($app['conf']) ? $app['conf'] : 1;


											$vModAppt = $app;
											
											unset($app);
																						
											# list acess to plugins
											if( is_array($prive) &&  isset($prive[$a][$b][$c][$d][$pconf]) || $_SESSION['vtxType'] == 1 ):
																							
												# check if alert exists
												if( isset($vModAppt['alert']) ):
												
													$this->alarm['sqlq'][]  = $vModAppt['alert'];
													#$this->alarm['mode'][] = $plugin['name'] . " - " . $vModAppt['name'];
													$this->alarm['mode'][]  = $vModAppt['name'];
													$this->alarm['urls'][]  = "load-plugins,1,0,{$vsrc},{$a},{$b},{$c},{$d},{$conf}90,loader.htm?sent=1";
													
													unset($vModAppt['alert']);
													
												endif;
												
												# make urls
												$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['plugs'][$z]['name']   = $vModAppt['name']; 
												$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['plugs'][$z]['tgt']    = "load-plugins,1,0,{$vsrc},{$a},{$b},{$c},{$d},{$conf}90,charger.htm?sent=4"; 
											
											
												#isset($prive[$a][$b][$c][$d]) ?  print ("entrou em  4 {$d}  - {$vModAppt['name']} <br />") :  "";	
											else:
												
												$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['plugs'][$z]['name']   = $vModAppt['name']; 
												$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['plugs'][$z]['tgt']    = "load-plugins,1,0,{$vsrc},{$a},{$b},{$c},{$d},{$conf}90,charger.htm?sent=5"; 
											
											endif;
											
											# check adtional params
											if(isset($vModAppt['lnk']) && is_array($vModAppt['lnk'])  ):
										
											
											# list all aditional links
											for($t=0; $t< count($vModAppt['lnk']); $t++):
												
												
											
												$confx  = isset($vModAppt['lnk'][$t]['conf']) ? "{$vModAppt['lnk'][$t]['conf']}," : "0,";
												$pconfx = isset($vModAppt['lnk'][$t]['conf']) ? $vModAppt['lnk'][$t]['conf'] : 1;
												
													# check privers
													# echo"{$a} {$b} {$c} {$d} {$pconfx} <br >";
													if( is_array($prive) &&  isset($prive[$a][$b][$c][$d][$confx]) || $_SESSION['vtxType'] == 1 ):

														# check aditional params														
													 	$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['plugs'][$w+$t+101]['name'] =  $vModAppt['lnk'][$t]['name'];
														$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['plugs'][$w+$t+101]['tgt']  = "load-plugins,1,0,{$vsrc},{$a},{$b},{$c},{$d},{$confx}90,manager.htm?sent=3";

														# check if alert exists
														if( isset($vModAppt['lnk'][$t]['alert']) ):
															
															$this->alarm['sqlq'][] = $vModAppt['lnk'][$t]['alert'];
															$this->alarm['mode'][] =  $vModAppt['lnk'][$t]['name'];
															$this->alarm['urls'][] = "load-plugins,1,0,{$vsrc},{$a},{$b},{$c},{$d},{$confx}{$t},90,manager.htm?sent=3&v={$t}";
															
															//unset($vModAppt['alert']);
														
														endif;
													
													else:
														
														# check aditional params														
													 	$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['plugs'][$z+$t+101]['name'] =  $vModAppt['lnk'][$t]['name'];
														$this->vtxMod[$x]['plugins'][$y]['actions'][$w]['plugs'][$z+$t+101]['tgt']  = "load-plugins,1,0,{$vsrc},{$a},{$b},{$c},{$d},{$confx}90,manager.htm?sent=6";

													endif;
												
											endfor;
										
											unset($vModAppt['lnk']);
										endif;
										
										
										endif;
										$z++;
										
									
									endwhile;
									
									closedir($D4);
									
								endif;
								
								### ----------------------------
								endif;
							
							$w++;
							endwhile;
							closedir($D3);
								
							endif;
							### ----------------------------
						
						endif;
						### ----------------------------
						
						$y++;
						endwhile;
						closedir($D2);
											
					endif;
					### ----------------------------
					#############################################################
						
				endif;
				
			endif;
			$x++;	
					
			endwhile;
		
		closedir($D1);
		
	
	endif;
	
	endif;

	#############################################################

	$_SESSION['VTX_MOD_ALARM'] = $this->alarm ;
	

	}

#############################################################		
	public function vGetUserPrives(){


			// Obtain a list of columns
			foreach ($this->vtxMod as $key => $row) {
				
				$mid[$key]  = $row['ptitle'];;
			}
			
			// Sort the data with mid descending
			// Add $data as the last parameter, to sort by the common key
			array_multisort($mid, SORT_ASC, $this->vtxMod);				

			
		//array_multisort($this->vtxMod, SORT_DESC);
		
		return $this->vtxMod;	
	}

	
#############################################################		
	public function vGetAlarms(){
		
		return isset($this->alarm) ? $this->alarm : "";	
	}
	
#############################################################

	public function subval_sort($a,$subkey) {
	
		foreach($a as $k=>$v) {
			$b[$k] = strtolower($v[$subkey]);
		}
		asort($b);
		
		foreach($b as $key=>$val) {
			$c[] = $a[$key];
		}
		
		return $c;
	}

	var $vtxLicenseActive = 0;
#############################################################
	public function vtxGetLicence($vconf,$canvas=false){
		
		
		# get domain
		$vtxUnpackDom = strrev(base64_decode(strrev($vconf['lisccon'])));
		
		# get user key
		$vtxUnpackKey = base64_decode($vconf['lisckey']);
		
		# get vortex domains
		$vtxMasterDom = str_replace("vortex.","",$_SERVER['HTTP_HOST']);
		$vtxMtrDomKey = strrev(base64_encode(strrev($vtxMasterDom)));
	
		# detect licence file
		$vtxFileLic = "phar://{$vconf['private']}/vortexacm/coredist/{$vconf['syspack']}/{$vconf['vtxlics']}";

		# check license exists
		if( !is_file($vtxFileLic) ):
			
			
			return 	"ERROR: Licen&ccedil;a nn&atilde;oo foi encontrada em {$vtxFileLic} ";
			exit();
				
			# exit($this->vForceStop("aqui"));
			# exit(header('location:/charge,0,14,14,notkey.htm?notlicense='.time() ));
		
		endif;
			
			
			
				$vtxAuth = file_get_contents($vtxFileLic);
				$vtxAuth = explode("/*",$vtxAuth);				
				$vtximp = base64_decode(strrev($vtxAuth[5]));
				
				//echo $vtximp . "<br> " . $vconf['listkey'];
				
				if( sha1(strlen($vtximp)) <> $vconf['listkey'] ):

					return 	"ERROR: Domínio n&atilde;o est&aacute; licenciado para usar VORTEX ACM 2.0 ";
					exit();

					#exit(header('location:/charge,0,10,10,notkey.htm?faildom='.time()));
				
				endif;
				
				# validate a security licence
				$this->vtxSkey  	= $vtxUnpackKey;
				$this->vSetPrivateKey($vtxUnpackKey);
				
				
				# uncompress
				$vlicFinal = $this->vxDencoder(rtrim($vtximp));
				
				
				# validate keys
				@eval($vlicFinal);
							
				if( !isset($____vlicsyskey)  || !isset($____vlicKeyunloc) || !isset($vtxUnpackKey) 
				     && ($vtxUnpackKey <> $____vlicsyskey)
					 && (sha1($vtxMtrDomKey) <> $____vlicKeyunloc)
					 && !in_array($_SERVER['HTTP_HOST'], $____vlicsdomains)  ):
									
					
					return 	"ERROR: Este site nn&atilde;oo esta licenciado para usar VORTEX ACM 2.0 ";
					exit();

					#exit(header("location:/charge,0,11,12,notkey.htm?d={$_SERVER['HTTP_HOST']}&unlicense=".time() ));
					
				endif;
	
					# check validate to system
					$validade = $____vlicvalidate - time();
					$vlicdays = (int)floor( $validade / (60 * 60 * 24) );
					
					$this->vtxLicenseActive = $vlicdays;
				
					# active use days
					if($vlicdays <= 0 ):
					
					return 	"ERROR: A licen&ccedil;a para uso do VORTEX ACM 2.0 expirou";
					
					exit();

						#exit(header('location:/charge,0,12,14,notkey.htm?expired='.time()));
					
					elseif($vlicdays <=15 ):
					
						$this->setLicenceExpires("Sua licen&ccedil;a de uso para o Vortex 2.0 expira dentro de {$vlicdays} dias");
						
					endif;
					

		
		return !empty($canvas) ? $vlicFinal : $____vlicsbacks;
	
	}
#############################################################

	public function setLicenceExpires($val){
		
		$this->alertLicense = $val;	
		
	}
	
	public function getLicenceExpires(){
		
		return $this->alertLicense;	
		
	}

	public function getLicenceActive(){
		
		return $this->vtxLicenseActive;	
		
	}




	#####################################################################
	# verify if string exists
	# $srt = $a =''
	# $value = $b = ''
	#####################################################################
	public function vPriveCheck($vr,$mod, $bk = false){			
			
			# check prive			
			$ox =   isset($_SESSION['vuserGrants'][$vr[4]][$vr[5]][$vr[6]][$mod]) || $_SESSION['vtxType'] == 1 ?  1 : "0"; 
			
			if( $ox == 0 ):
				  
				  //exit($this->vForceStop("Sem privilégios para acessar")) ;
				return $ox;	
			
			endif;
			
					
	}

	#####################################################################
	# verify if string exists
	# $srt = $a =''
	# $value = $b = ''
	#####################################################################
	public function vSetPrivateKey($vtxUnpackKey){
		
		$this->vtxSkey  = $vtxUnpackKey;
		
	}

	#####################################################################
	# verify if string exists
	# $srt = $a =''
	# $value = $b = ''
	#####################################################################
	public function vGetPrivateKey(){
		
		return $this->vtxSkey;
		
	}


	#####################################################################
	# order arary by colun
	# $array = array()
	# $cols = string
	#####################################################################
	public function sortArray( $data, $field ) {
    
	$field = (array) $field;
    uasort( $data, function($a, $b) use($field) {
        $retval = 0;
        foreach( $field as $fieldname ) {
            if( $retval == 0 ) $retval = strnatcmp( $a[$fieldname], $b[$fieldname] );
        }
        return $retval;
    } );
    return $data;
	}


}
?>