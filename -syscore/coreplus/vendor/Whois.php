<?php
//Author: de77.com
//Homepage: http://de77.com
//Version: 17.09.2010
//Licence: MIT
//Visit: http://de77.com/php/fast-php-whois-class-get-information-about-domains


class Whois
{
	public function query($domain, $raw = false)
	{
		$tld = substr($domain, strrpos($domain, '.') + 1);

		$data = array();
				
		$f = fsockopen($tld . '.whois-servers.net', 43);
		
		if ($tld == 'com')
		{
			fputs($f, '=' . $domain . "\r\n");
		}
		else
		{
			fputs($f, $domain . "\r\n");		
		}

		while (!feof($f))
		{
			$data[] = fgets($f);
		}
		
		if ($raw === true)
		{
			return $data;
		}
		
		 isset($data[0]['domain']) ? "sim " : "nao" ;
		
		//print_r( $data );
		
		$fun = 'parse_' . $tld;		
		return $this->$fun($data, $domain);
	}
	
	private function parse($data, $domain, $domainWord, $keywords, $breakOnEnter)
	{
		$found = false;					
		$domainWordLen = strlen($domainWord);
		
		$res = array();
											
		foreach ($data AS $d)
		{
			$d = trim($d);
			
			if ($d == '')
			{
				if ($breakOnEnter)
				{
					$found = false;
				}
				continue;				
			}	
			
			$pos = strpos($d, $domainWord);
			if ($pos !== false)
			{
				$dom = strtolower(trim(substr($d, $pos + $domainWordLen)));
				if ($dom == $domain)
				{
					$found = true;
				}
			}
			
			if ($found)
			{
				$pos = strpos($d, ':');
				if ($pos !== false)
				{
					$keyword = substr($d, 0, $pos);
					
					if (isset($keywords[$keyword]))
					{
						$t = trim(substr($d, $pos+1));
						if ($t != '')
						{
							$res[$keywords[$keyword]][] = $t;
						}
					}
					else
					{
						$keyword = '';
					} 
				}
				else if ($keyword)
				{
					$res[$keywords[$keyword]][] = $d;
				}
			}
		}
		
		return $res;		
	}
	
	private function parse_pl($data, $domain)
	{
		$domainWord = 'DOMAIN:';
		$keywords = array(	'DOMAIN'		=> 'domain',
							'nameservers'	=> 'dns',
							'created'		=> 'created'
						);
		return $this->parse($data, $domain, $domainWord, $keywords, true);										
	}	
	
	private function parse_de($data, $domain)
	{
		$domainWord = 'Domain:';
		$keywords = array(	'Domain'		=> 'domain', 
						);
						
		return $this->parse($data, $domain, $domainWord, $keywords, true);		
	}
	
	private function parse_fr($data, $domain)
	{
		$domainWord = 'domain:';
		$keywords = array(	'domain'		=> 'domain',
							'nserver'		=> 'dns',
							'created'		=> 'created',
						);
						
		return $this->parse($data, $domain, $domainWord, $keywords, false);		
	}
	
	private function parse_org($data, $domain)
	{
		$domainWord = 'Domain Name:';
		$keywords = array(	'Domain Name'		=> 'domain',
							'Name Server'		=> 'dns',
							'Created On'		=> 'created',
							'Expiration Date'	=> 'expires' 
						);
						
		return $this->parse($data, $domain, $domainWord, $keywords, true);		
	}
	
	private function parse_net($data, $domain)
	{
		$domainWord = 'Domain Name:';
		$keywords = array(	'Domain Name'		=> 'domain',
							'Name Server'		=> 'dns',
							'Creation Date'		=> 'created',
							'Expiration Date'	=> 'expires' 
						);
						
		return $this->parse($data, $domain, $domainWord, $keywords, true);		
	}
		
	private function parse_com($data, $domain)
	{
		$domainWord = 'Domain Name:';
		$keywords = array(	'Domain Name'		=> 'domain',
							'Name Server'		=> 'dns',
							'Creation Date'		=> 'created',
							'Expiration Date'	=> 'expires' 
						);
										
		return $this->parse($data, $domain, $domainWord, $keywords, true);		
	}
	
	private function parse_eu($data, $domain)
	{
		$domain = substr($domain, 0, strpos($domain, '.eu'));
		
		$domainWord = 'Domain:';
		$keywords = array(	'Domain'			=> 'domain',
							'Nameservers'		=> 'dns',
							'Creation Date'		=> 'created',
							'Expiration Date'	=> 'expires' 
						);	
									
		return $this->parse($data, $domain, $domainWord, $keywords, false);		
	}	
}