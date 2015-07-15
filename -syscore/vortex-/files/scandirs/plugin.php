<?php
$v = new vortex();
$__vtxPackModule__ = $v->vtxGetLicence($this->vconf);

################################################################################################
# get user prives	
################################################################################################
if(!isset($_SESSION['vtxUser'])):

	$scan = "4505 - Access denied";

	exit($this->vForceStop($scan));

endif;
	
# list invisible itens
	$vblind = array(".","..","vthumb");
	
# check current month dir exists
	$month = date("Y-m");
	$dirWay = "/";
	
# start path
	$vtxScanDir  = isset($_SESSION['userDIR']) ? $_SESSION['userDIR'] : "/conteudo/{$__vtxPackModule__}/publico/upps/{$month}/"; 

# get root dir
	$dirWay = explode("/", substr($vtxScanDir,0,-1));
	$dirWay = is_array($dirWay) ? end($dirWay) : "Raiz";
		
# set root path
	$scan =  "<li><a href='javascript:void(0)' rel='' class='toRound inFocus'>{$dirWay}</a></li>";
		
	$vtxScanWays = isset($_GET['dir']) ? $_GET['dir'] : $vtxScanDir ;

# check if user is navigation
	if( isset($_GET['dir']) ):
			
		$vtxScanSets  = str_replace($vtxScanDir, "", $vtxScanWays);
		$dirPath = explode("/", $vtxScanSets );
		
		# save current
		$save = "";
		
		# get current paths	
		while( list($k, $v) = each($dirPath) ):
		
			if( !empty($v) ):
				
				$save .= "/{$v}";
				$scan .=  "<li><a href='javascript:void(0)' rel='{$vtxScanDir}{$save}' id='{$save}' class='toRound inFocus'>{$v}</a></li>" ;
			
			endif;
			
		endwhile;
	
	endif;

	# recovery dir
	$vtxScanRoot = "{$this->vconf['rootpath']}/{$vtxScanWays}";
	$vtxScanRoot = str_replace("//","/",$vtxScanRoot);
	
	# if dir exists
	if ( is_dir($vtxScanRoot) ):
		
		$d = dir($vtxScanRoot);
		
		while (false !== ($e = $d->read())):

		   	   // check is folder			   
			   if( is_dir("{$vtxScanRoot}/{$e}") && !in_array($e,$vblind) ):
			   
			   		$scan .=  "<li><a href='javascript:void(0)' rel='{$vtxScanWays}/{$e}' class='toRound inside'>{$e}</a></li>";
			   
			   endif;
		
		endwhile;
		
		$d->close();
	
	else:
	
		$scan = "<li>4404 :: Directory not found in {$vtxScanDir} </li>";
	
	endif;
	
exit( $this->vForceStop("<ul>{$scan}</ul>") );
?>