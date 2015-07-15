<?php
/*
How to use:

	include("whois.class.php"); 
	$whois = new Whois_class();

	if($whois->checkDomain("test", "info") == TRUE){
		echo "Available";
	}else{
		echo "Taken";
	}
*/
class Whois_class{
    var $serverList = array(
		'com'	=> array(
			'server'	=> 'whois.crsnic.net',
			'response'	=> 'No match for'
		),
		'net'	=> array(
			'server'	=> 'whois.crsnic.net',
			'response'	=> 'No match for'
		),
		'org'	=> array(
			'server'	=> 'whois.publicinterestregistry.net',
			'response'	=> 'NOT FOUND'
		),
		'info'	=> array(
			'server'	=> 'whois.afilias.net',
			'response'	=> 'NOT FOUND'
		),
		'name'	=> array(
			'server'	=> 'whois.nic.name',
			'response'	=> 'No match'
		),
		'eu'	=> array(
			'server'	=> 'whois.nic.biz',
			'response'	=> 'Not found'
		),
		'lt'	=> array(
			'server'	=> 'whois.domreg.lt',
			'response'	=> 'available'
		),
		'eu'	=> array(
			'server'	=> 'whois.eu',
			'response'	=> 'FREE'
		)
	);

	function checkDomain($name, $top){
		$domain		= $name . "." . $top;
		$server		= $this->serverList[$top]["server"];
		$findText	= $this->serverList[$top]["response"];
		
		//echo "checking domain: " . $domain . " @[" . $server . "]" . chr(10);
		//echo "looking for: " . $findText . chr(10);
		
		try{
			$con = fsockopen($server, 43);
		}
		catch(Exception $e){
			return FALSE;
		}
		
		fputs($con, $domain."\r\n");
			
		$response = "";
		while(!feof($con)){
			$response .= fgets($con, 128); 
		}
		
		// removing all comments from the response
		// this is needed due to some *smart* whois, who have same text saying the domain is availible
		// along with the same text in comments, even if the domain is NOT availible (-;
		$response = preg_replace("/%.*\n/", "", $response);
		
		//echo $response . chr(10);
		
		fclose($con);
			
		if(strpos($response, $findText)){
			return TRUE;
		}else{
			return FALSE;
		}
	}
}
?>