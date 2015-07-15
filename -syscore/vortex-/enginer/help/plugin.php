<?php
##############################################################################################################
## save data
##############################################################################################################
if( isset($_GET['set']) && isset($_GET['video']) ):

	# import class to form validation
	$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.forms.php");
	
	# start new class
	$v = new forms();

	$inQuery = "SET @vid :=0; ";
	
	# start save data
	$tableF[1] = 'video';
	$tableF[2] = 'user';
	$tableF[3] = 'data';
	$tableF[4] = 'duration';
	$tableF[5] = 'module';
	$tableF[6] = 'type';
	
	$tablev[1] = $this->clearNumeric($_GET['video']);
	$tablev[2] = $this->clearNumeric($_SESSION['vtxUser']);
	$tablev[3] = time();
	$tablev[4] = '61';
	$tablev[5] = "assim ó";
	$tablev[6] = $this->clearNumeric($_GET['call']);
		
	# prepare query1	
	$inQuery .= $v->tableSingleInsert('vtx_system_video',$tableF,$tablev);
	
	# get current id
	$inQuery .= "SET @vid = LAST_INSERT_ID(); ";
	$inQuery .= "SELECT  @vid AS getVideo; ";

	# prepare query 2
	unset($tableF,$tablev);
	
	$tableF[1] = 'video';
	$tableF[2] = 'start';
	$tableF[3] = 'end';

	$tablev[1] = 'oxid';
	$tablev[2] = 0;
	$tablev[3] = time();
			
	$inQuery .= $v->tableSingleInsert('vtx_system_videoview',$tableF,$tablev);
	
	
	$inQuery = str_replace("'oxid'","@vid",$inQuery);
	
	$dsql = $this->vQuery($inQuery,"",1);
		
	exit($this->vForceStop( $this->adb->insert_id ));
	exit($this->vForceStop(""));


endif;

if( isset($_GET['put']) && isset($_GET['video']) ):

	# import class to form validation
	$this->vLoadClass($this->vconf['path'],"/vActions.phar/class.forms.php");
	
	# start new class
	$v = new forms();

		$tableF[1] = 'video';
		$tableF[2] = 'start';
		$tableF[3] = 'end';
	
		$tablev[1] = $this->clearNumeric($_GET['serie']);
		$tablev[2] = $this->clearNumeric($_GET['watch']);
		$tablev[3] = time();
		
		
		$inQuery = $v->tableSingleInsert('vtx_system_videoview',$tableF,$tablev);
		$dsql = $this->vQuery($inQuery);
		
		exit($this->vForceStop( $this->adb->insert_id ));


endif;


##############################################################################################################
## get user data
##############################################################################################################
$helper = isset($_GET['h']) ? str_replace(",","/",$_GET['h']) : "";

$helpFile = "/{$helper}/tpl/base.htm";
$helpConf = "/{$helper}/tpl/conf.htm";

is_file($this->vconf['source'].$helpFile) ? $this->vInclude($helpFile,"helpinc",$this->vconf['source']) : $this->set("helpinc","",true);;
is_file($this->vconf['source'].$helpConf) ? $this->vInclude($helpConf,"helpconf",$this->vconf['source']) : $this->set("helpconf","",true);;


$this->vForceTPL($this->vconf['path'],"/vortex.phar/enginer/help/");
$this->vForceStop("");
?>