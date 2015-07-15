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

/**
 * x64Template
 * @author Atomo64 (www.atomo64.tk)
 * @package x64Template
 * @version 0.1.6
 * @copyright Raphael Geissert 2006-2007
 *
 * Based on code by Brian Lozier (bTemplate: Copyright 2002 Brian Lozier)
 *
 */
class template {
	/**
	 * When is set to true it makes the class clean the loops and cloops data
	 * The value of this variable can be set while creating the object
	 *
	 * @var bool
	 * @access public
	 */
	public $reset = TRUE;

	/**
	 * The beginning of the normal tags
	 *
	 * @var string
	 * @access public
	 */
	public $ldelim = '{';
	/**
	 * The end of the normal tags
	 *
	 * @var string
	 * @access public
	 */
	public $rdelim = '}';

	/**
	 * The beginning of the opening tag for blocks
	 *
	 * @var string
	 * @access public
	 */
	public $BAldelim = '{';
	/**
	 * The end of the opening tag for blocks
	 *
	 * @var string
	 * @access public
	 */
	public $BArdelim = '}';
	/**
	 * The beginning of the closing tag for blocks
	 *
	 * @var string
	 * @access public
	 */
	public $EAldelim = '{/';
	/**
	 * The end of the closing tag for blocks
	 *
	 * @var string
	 * @access public
	 */
	public $EArdelim = '}';

	/**
	 * Array containing the data for the tag: tags
	 *
	 * @var array
	 * @access private
	 */
	private $_scalars = array();
	/**
	 * Array containing the data for the loop: tags
	 *
	 * @var array
	 * @access private
	 */
	private $_arrays = array();
	/**
	 * Array containing the information about which array tags
	 *  should be enabled for if: tags and which elements should be used
	 *  since trying to find ifs for each array element and
	 *  for each array entry would take a lot of time.
	 *
	 * @var array
	 * @access private
	 */
	private $_arrays_ifs = array();
	/**
	 * Array containing the data for the cloop: tags
	 *
	 * @var array
	 * @access private
	 */
	private $_carrays = array();
	/**
	 * Array containing the data for the if: tags
	 *
	 * @var array
	 * @access private
	 */
	private $_ifs = array();
	/**
	 * Array containing the information of the plugins to be loaded
	 *
	 * @var array
	 * @access private
	 * @see $this->add_plugin
	 */
	private $_plugins = array();

	/**
	 * Object constructor, set $reset to reset the arrays after processing
	 *
	 * @access public
	 * @param bool $reset
	 * @return x64Template
	 */
	public function x64Template($reset = TRUE)
	{
		$this->reset = $reset;
	}
	

	/**
	 * Set a tag with a value, $var can be a string or an array
	 * Set $if to true if you want to be able to use that tag with if: conditions
	 *
	 * @param string $tag
	 * @param mixed $var
	 * @param bool $if
	 */
	public function set($tag, $var, $if = NULL)
	{
		if(is_array($var))
		{
			$this->_arrays[$tag] = $var;
			if($if!==null)
			{
				if(!is_array($if))
				{
					$result = $var ? TRUE : FALSE;
					$this->_ifs[] = $tag;
					$this->_scalars[$tag] = $result;
				}
				else
				{
					$result = $var ? TRUE : FALSE;
					$this->_ifs[] = $tag;
					$this->_scalars[$tag] = $result;
					$this->_arrays_ifs[$tag]=$if;
				}
			}
		}
		else
		{
			$this->_scalars[$tag] = $var;
			if($if) $this->_ifs[] = $tag;
		}
	}

	/**
	 * Sets a case loop
	 *
	 * @access public
	 * @param string $tag The tag name
	 * @param array $array The array containing the data
	 * @param array $cases The array telling about the cases that it should check for
	 */
	public function set_cloop($tag, $array, $cases)
	{
		$this->_carrays[$tag] = array(
		'array' => $array,
		'cases' => $cases);
	}

	/**
	 * Reset the template variables
	 *
	 * @access public
	 * @param bool $scalars If the scalars data should be cleaned
	 * @param bool $arrays If the arrays data should be cleaned
	 * @param bool $arrays_ifs If the arrays_ifs data should be cleaned
	 * @param bool $carrays If the case arrays data should be cleaned
	 * @param bool $ifs If the ifs data should be cleaned
	 * @param bool $plugins If the plugins data should be cleaned
	 */
	public function reset($scalars=false, $arrays=false, $arrays_ifs=false, $carrays=false, $ifs=false,$plugins=false)
	{
		if($scalars)    $this->_scalars    = array();
		if($arrays)     $this->_arrays     = array();
		if($arrays_ifs) $this->_arrays_ifs = array();
		if($carrays)    $this->_carrays    = array();
		if($ifs)        $this->_ifs        = array();
		if($plugins)    $this->_plugins    = array();
	}

	/**
	 * Formats the tags & returns a two-element array,
	 * the opening and closing tags
	 *
	 * @access public
	 * @param string $tag The tag name
	 * @param string $directive The directive name
	 * @return array
	 */
	public function get_tags($tag, $directive)
	{
		$tags['b'] = $this->BAldelim . $directive . ':' . $tag . $this->BArdelim;
		$tags['e'] = $this->EAldelim . $directive . ':' . $tag . $this->EArdelim;
		return $tags;
	}

	/**
	 * Formats a simple tag
	 *
	 * @access public
	 * @param string $tag The tag name
	 * @param string $directive The tag directive
	 * @return string The formated tag
	 */
	public function get_tag($tag,$directive='tag')
	{
	return $this->ldelim . $directive . ':' . $tag . $this->rdelim;
	}

	/**
	 * Extracts a portion of a template( or a string) according to the
	 *  opening ($tags['b']) and closing ($tags['e']) tags
	 *
	 * @param array $tags The opening and closing tags/delimeters
	 * @param string $contents The content from where it is going to extract
	 * @return string The extracted content
	 */
	public function get_statement($tags, $contents)
	{
		// Locate the statement
		$tag_length = strlen($tags['b']);
		$fpos = $tags['b']===null? 0 : strpos($contents, $tags['b']);
		$lpos = $tags['e']===null? strlen($contents) : strpos($contents, $tags['e']);

		if($fpos===false||$lpos===false)
		return false;

		$fpos += $tag_length;
		$length = $lpos - $fpos;

		// Extract & return the statement
		return substr($contents, $fpos, $length);
	}

	/**
	 * Parse the template with the variables
	 *
	 * @access public
	 * @param string $contents The template
	 * @return string The parsed template
	 */
	public function parse($contents)
	{
		/**
		 * Process the ifs (if any)
		 */
		foreach($this->_ifs as $value)
		{
			$contents = $this->_parse_if($value, $contents);
		}

		/**
		 * Process the scalars (if any)
		*/
		foreach($this->_scalars as $key => $value)
		{
			$contents = str_replace($this->get_tag($key), $value, $contents);
		}

		/**
		 * Process the arrays (if any)
		 */
		foreach($this->_arrays as $key => $array)
		{
			$contents = $this->_parse_loop($key, $array, $contents);
		}

		/**
		 * Process the case arrays (if any)
		 */
		foreach($this->_carrays as $key => $array)
		{
			$contents = $this->_parse_cloop($key, $array, $contents);
		}

		$plugins_count=count($this->_plugins);
		/**
		 * Process the plugins (if any)
		 */
		for ($n=0;$n<$plugins_count;$n++)
		{
			if($this->_plugins[$n]['object']===null)
			$obj=new $this->_plugins[$n]['function']();
			else
			$obj=&$this->_plugins[$n]['object'];

			//
			//Now we check what protocol version the plugin was designed for
			//
			switch ($obj->version('protocol'))
			{
				case "1.0":
					{
						//
						//Now we give the object itself to the function, so it can access the current settings
						//
						$contents=$obj->parse($contents,$this);
					}break;
			}
		}

		/**
		 * Reset the data according to the settings
		 */
		if($this->reset) $this->reset(FALSE, TRUE, TRUE, TRUE);

		return $contents;
	}

	/**
	 * Parses an if statement
	 *
	 * @access private
	 * @param string $tag The tag name
	 * @param string $contents The current as-processed template
	 * @param string $replace If the function should consider as $replace without checking the real value
	 * @return string The parsed template
	 */
	private function _parse_if($tag, $contents, $replace=null)
	{
		//
		// Get the tags
		//
		$t = $this->get_tags($tag, 'if');
		
		//
		//We loop this so we can process all the ifs for this tag
		//
		while (($entire_statement = $this->get_statement($t, $contents))!==false)
		{

			// Get the else tag
			$tags['b'] = NULL;
			$tags['e'] = $this->get_tag($tag, 'else');

			// See if there's an else statement
			if(($else = strpos($entire_statement, $tags['e'])))
			{
				// Get the if statement
				$if = $this->get_statement($tags, $entire_statement);

				// Get the else statement
				$else = substr($entire_statement, $else + strlen($tags['e']));
			}
			else
			{
				$else = NULL;
				$if = $entire_statement;
			}

			//
			//If the function wasn't called with a value for $replace we check the _scalars array
			//
			if($replace===null||!is_bool($replace))
			$replace=!empty($this->_scalars[$tag])?true:false;

			//
			//If the condition is valid then we use the 'if' (first) part, if not, then we use 'else'
			//
			$replace=($replace) ? $if :  $else;

			//
			// Parse the template
			//
			$contents = str_replace($t['b'] . $entire_statement . $t['e'], $replace, $contents);
		}

		//
		//Return the template
		//
		return $contents;
	}

	/**
	 * Parses a loop
	 *
	 * @access private
	 * @param string $tag Tag name
	 * @param array $array The array containing the loop data
	 * @param string $contents The current as-processed template
	 * @return string The parsed template
	 */
	private function _parse_loop($tag, $array, $contents)
	{
		// Get the tags & loop
		$t = $this->get_tags($tag, 'loop');

		while (($loop = $this->get_statement($t, $contents))!==false)
		{
			$parsed = NULL;
			$if_key_exists=isset($this->_arrays_ifs[$tag]);

			// Process the loop
			foreach($array as $key => $value)
			{
				/**
				 * We create a copy of the loop so we can keep the original loop
				 *  but work on this one
				 */
				$i = $loop;

				if($if_key_exists&&isset($this->_arrays_ifs[$tag][$key]))
				$i=$this->_parse_if($tag . '.' . $key,$i,!empty($value)?true:false);
				/**
				 * array(1=>array('key_name'=>'value','some_key'=>'value'))
				 * {tag:tag_name[].key_name},{tag:tag_name[].some_key}
				 * {tag:tag_name[].key_name[]},{tag:tag_name[].some_key[].some_subkey}
				 */
				if(is_numeric($key) && is_array($value))
				{
					foreach($value as $key2 => $value2)
					{
						if($if_key_exists&&isset($this->_arrays_ifs[$tag][$key2]))
						$i=$this->_parse_if($tag . '[].' . $key2,$i,!empty($value2)?true:false);
						if(!is_array($value2))
						{
							// Replace associative array tags
							$i = str_replace($this->get_tag($tag . '[].' . $key2), $value2, $i);
						}
						else
						{
							// Check to see if it's a nested loop
							$i = $this->_parse_loop($tag . '[].' . $key2, $value2, $i);
						}
					}
				}
				/**
				 * array('tsgsgs'=>'sgsgdgg')
				 * {tag:tag_name.key_name}
				 */
				elseif(is_string($key) && !is_array($value))
				{
					$i = str_replace($this->get_tag($tag . '.' . $key), $value, $i);
				}
				/**
				 * array(1=>'fff')
				 * {tag:tag_name[]}
				 */
				elseif(!is_array($value))
				{
					$i = str_replace($this->get_tag($tag . '[]'), $value, $i);
				}

				// Add the parsed iteration
				if(isset($i)) $parsed .= rtrim($i);
			}

			//
			// Parse the template
			//
			$contents=str_replace($t['b'] . $loop . $t['e'], $parsed, $contents);
		}

		//
		//Return the template
		//
		return $contents;
	}

	/**
	 * Parse a case loop
	 *
	 * @access private
	 * @param string $tag The tag name that is going to be parsed
	 * @param array $array Array with the loop elements
	 * @param string $contents The current as-processed template
	 * @return string The parsed template
	 */
	private function _parse_cloop($tag, $array, $contents)
	{
		// Get the tags & loop
		$t = $this->get_tags($tag, 'cloop');
		while (($loop = $this->get_statement($t, $contents))!==false)
		{
			// Set up the cases
			$array['cases'][] = 'default';
			$case_content = array();
			$parsed = NULL;

			// Get the case strings
			foreach($array['cases'] as $case)
			{
				$ctags[$case] = $this->get_tags($case, 'case');
				$case_content[$case] = $this->get_statement($ctags[$case], $loop);
			}

			// Process the loop
			foreach($array['array'] as $key => $value)
			{
				if(is_numeric($key) && is_array($value))
				{
					// Set up the cases
					if(isset($value['case'])) $current_case = $value['case'];
					else $current_case = 'default';
					unset($value['case']);
					$i = $case_content[$current_case];

					// Loop through each value
					foreach($value as $key2 => $value2) {
						$i = str_replace($this->get_tag($tag . '[].' . $key2), $value2, $i);
					}
				}

				// Add the parsed iteration
				$parsed .= rtrim($i);
			}

			// Parse & return the final loop
			$contents=str_replace($t['b'] . $loop . $t['e'], $parsed, $contents);
		}
		return $contents;
	}

	/**
	 * Parses the file $file_name as a template
	 *
	 * @access public
	 * @param string $file The template file name
	 * @return string The processed template
	 */
	public function fetch($file){
		
		// Open the file
		$fp = fopen($file, 'rb');
		
		if(!$fp) return FALSE;

		// Read the file
		$contents = fread($fp, filesize($file));

		// Close the file
		fclose($fp);

		// Parse and return the contents
		return $this->parse($contents);

	}

	/**
	 * Works the same way as set() excepting that if the tag already exists
	 *  it doesn't replaces it, it appends the new value (if it is an string)
	 *  or it merges the content (if it is an array).
	 * Please note that this function will not make any change
	 *  to the array_ifs data, to update that information,
	 *  set() has to be used instead
	 *
	 * @param string $tag
	 * @param mixed $var
	 * @param bool $if
	 */
	public function append($tag, $var, $if = NULL)
	{
		if(is_array($var))
		{
			if(!isset($this->_arrays[$tag]))
			$this->_arrays[$tag]= $var;
			else
			$this->_arrays[$tag]=array_merge($this->_arrays[$tag],$var);
			if($if)
			{
				$result = $var ? TRUE : FALSE;
				$this->_ifs[] = $tag;
				$this->_scalars[$tag] = $result;
			}
		}
		else
		{
			if(!isset($this->_scalars[$tag]))
			$this->_scalars[$tag] = $var;
			else
			$this->_scalars[$tag] .= $var;
			if($if) $this->_ifs[] = $tag;
		}
	}

	/**
	 * Adds a plugin to be called when a template is being parsed
	 * The $plugin_name is the name of the class which is the plugin
	 *
	 * The $setup var may contain any type of data, because it is pased directly to the $plugin_name::setup() function of the plugin class
	 * The arguments passed to the $plugin_name::parse() function will be $contents and the object itself ($this), in that order, and the function will only return the parsed template.
	 *
	 * The $plugin_name::setup() function may return an array with some settings
	 *
	 * Notes:
	 * 	The plugin class must have the next functions, which are going to be called by the template engine:
	 * 		-$plugin_name::setup() This function can be used to setup either the template engine when calling the plugin or to setup the plugin itself, in the last case, the $setup param can be used to give some settings to the plugin
	 * 		-$plugin_name::parse() This function is called when the class 'executes' the plugin
	 * 		-$plugin_name::version() This function must have one argument, which specifies the version of what is being requested: 'protocol' is the current plugins protocol, for the current version is 1.0; 'plugin' is the plugin version, it can be useful to debug
	 *
	 * @access public
	 * @param string $plugin_name The name of the function or class to be called
	 * @param mixed $setup Special settings that can be given to the plugin
	 */
	public  function add_plugin($plugin_name,$setup=null){
		$n=count($this->_plugins);
		$this->_plugins[$n]['function']=$plugin_name;
		$this->_plugins[$n]['setup']=$setup;

		$obj=new $plugin_name();
		$this->_plugins[$n]=array_merge($this->_plugins[$n],$obj->setup($setup));

		if (!isset($this->_plugins[$n]['refresh_object'])||$this->_plugins[$n]['refresh_object']===false)
		$this->_plugins[$n]['object']=&$obj;
		else
		$this->_plugins[$n]['object']=null;
	}
}
?>