<?php
$v = new vortex();
$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);

################################################################################################
# get user prives	
################################################################################################
if(!isset($_SESSION['vtxUser'])):

	$scan = "4505 - Access denied";

	exit($this->vForceStop($scan));

else:
	
# remove specified files	
if( isset($_POST['del']) && isset($_POST['files']) && is_array($_POST['files']) ):

	while(list($k, $v) = each($_POST['files'])):
	
		$this->vRemoveFile("{$this->vconf['rootpath']}{$v}");
	
	endwhile;
	
	
	exit($this->vForceStop("1|1"));

endif;
	
	
# list invisible itens
	$vblind = array(".","..","vthumb");
	$vImgCan = array("jpg","gif","png","jpeg");
	$vFlsCan = array("doc","pdf","zip","jpg","gif","png","txt","zip","xls");
	$vFlstyp = array('doc'=>'Documento Word','zip'=>'Compactado ZIP','pdf'=>'Documento PDF','jpg'=>'Imagem','gif'=>'imagem','png'=>'Imagem','xls'=>'Planilha','txt'=>'Arquivo Texto');

	# check current month dir exists
	$month = date("Y-m");
	
	# list dir
	$mdir = isset($_GET['dir']) ? str_replace("//","/",$_GET['dir']) : "";

	# get paht dir
	$vtxScanDir  = isset($_SESSION['userDIR']) ? $_SESSION['userDIR'] : "/conteudo/{$__vtxPackModule__}/publico/upps/{$month}/"; 
		
	# get full path dir
	$vtxScanRoot = isset($_GET['dir']) ? "{$this->vconf['rootpath']}/{$mdir}" : "{$this->vconf['rootpath']}/{$vtxScanDir}";
	$vtxScanPath = isset($_GET['dir']) ? $mdir: $vtxScanDir;

	# adjuste path
	$vtxScanRoot = str_replace("//","/",$vtxScanRoot);
	$vtxScanPath = str_replace("//","/",$vtxScanPath);
		
	# check destine dir exists
	!is_dir($vtxScanRoot) ? exit($this->vForceStop("4404 :: {$vtxScanRoot}")) : "";
	
	# check request
	if( !isset($_GET['dir']) ):
	
		//$vtxScanRoot = "{$vtxScanRoot}/{$month}";

		# start brande tags
		$urlNav['txt'][0] = "Raiz";
		$urlNav['url'][0] = $vtxScanPath;
	
		# path to current dir
		$urlNav['txt'][1] = $month;
	
	else:
	
		$pathx = str_replace($vtxScanDir,"",$mdir);
		$pathxx = explode("/",$pathx);
		
		$z = count($pathxx);
		
		$i = 2;
		while(list($k,$v) = each( $pathxx ) ):
		
			//$vtxScanRoot .= "{$v}/";
			
			$urlNav['txt'][$i] = $v;
			$i <= $z ? $urlNav['url'][$i] = str_replace("//","/",$vtxScanRoot) : "";
			
		$i++;
		
		endwhile;
		
	endif;
	
	# check permissions type
	$blocks = $_GET['t'] == 1 ? $vFlsCan :  $vImgCan ;
		
	# list dir
	$vtxScanRoot = str_replace("//","/",$vtxScanRoot);
			
	if ( is_dir($vtxScanRoot) ):
		
		$d = dir($vtxScanRoot);
				
		while (false !== ($e = $d->read())):

			   
			   	# get file exts
				$vext = !is_dir($vtxScanRoot.$e) ? substr($e,-3,3) : "fail";
				
				   if( !is_dir("{$vtxScanRoot}/{$e}") && !in_array($e,$vblind)  && in_array($vext, $blocks) ):

						$outs[] = $e;
				  
				   endif;

		endwhile;
		
		$d->close();
	
	# target folder not exists
	else:
		!is_dir($vtxScanRoot) ? exit($this->vForceStop("4404 :: {$vtxScanRoot}")) : "";
	endif;

endif;

# check file exists
if( isset($outs) ):

# execute pagination
	$total  = count($outs);
    $pages  = 15; 
    $start  = isset($_GET['p']) && is_numeric($_GET['p']) ? $_GET['p'] * $pages : 0;
    $finish = $start + $pages;
    $pagination = ceil ($total/$pages);
    
    for($a = $start; $a < $finish; $a++):
    	
		if( isset($outs[$a]) ):
			
			$gtype = substr( $outs[$a],-3,3);
			
			$out[$a]['insert'] 	= str_replace("//","/","{$vtxScanPath}/{$outs[$a]}");
			$out[$a]['name'] 	= $outs[$a];
			$out[$a]['tipo'] 	= $vFlstyp[$gtype];
			$out[$a]['date'] 	= date('d/m/Y - H:i:s', filemtime("{$vtxScanRoot}/{$outs[$a]}"));
			
			if( isset($_GET['type']) && $_GET['type'] == 2 ):
			
				$out[$a]['thumb'] 	= "/vortex-picture,1,4,100,100,{$vtxScanPath}/{$outs[$a]}";
			
			endif;
		
		endif;
    
    endfor;
    
    # make pagination
	$setURL = "/list-file-system,5,11,manager.htm?v=1";
	$setURL .= isset($_GET['dir']) ? "&dir={$_GET['dir']}" : "";
	$setURL .= isset($_GET['t']) ? "&t={$_GET['t']}" : "";
	$setURL .= isset($_GET['type']) ? "&type={$_GET['type']}" : "";
	
    for($x = 0; $x < $pagination; $x++):
    
    	 $pags[$x]['u'] = "{$setURL}&p={$x}";
		 $pags[$x]['n'] = ($x+1);
    
    endfor;

endif;

# set files
$this->set("file",isset($out) ? $out : "", true);	

# set pagination
$this->set("pages",isset($pags) ? $pags : "", true);	

# update brand tags
$this->updateURL($urlNav);

# load views to files
if( isset($_GET['type']) && $_GET['type'] == 2 ):
	
	$this->vForceTPL($this->vconf['path'],"/vortex.phar/files/scanpics/");
	
else:

	$this->vForceTPL($this->vconf['path'],"/vortex.phar/files/scanfile/"); 

endif;
?>