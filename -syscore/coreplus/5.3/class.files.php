<?php
class files extends template{

	### -------------------------------------------
	### alias to vRemoveDir
	### -------------------------------------------
	public function vtxRemoveFile($caminho){
		
		$this->vRemoveFile($caminho);
	}
	
	### -------------------------------------------
	### remove file from system	
	### -------------------------------------------
	public function vRemoveFile($caminho){
		
			if(is_file($caminho)):
			
				if(is_dir($caminho)):
					$this->vRemoveDir($caminho);
				else:
					unlink($caminho);
				endif;
				
			endif; 
		
	}

	### -------------------------------------------
	### alias to vRemoveDir
	### -------------------------------------------
	public function vtxRemoveDir($dir){
		
		$this->vRemoveDir($dir);
	}
	
	### -------------------------------------------
	### remove dir from system
	### -------------------------------------------
	public function vRemoveDir($dir){

	ob_start();
	
	if(is_dir($dir)):
	
		if ($handle = opendir($dir)):
		
			while (false !== ($file = readdir($handle))):
			ob_flush();
				
				if ($file != "." && $file != ".."):
					if(is_dir("{$dir}/{$file}")):
						$this->vRemoveDir("{$dir}/{$file}");
					else:
						$this->vRemoveFile("{$dir}/{$file}");
					endif;
				endif;
	
				flush();
			
			endwhile;
	
			closedir($handle);
	
			endif;
	
			@rmdir($dir);
	
	endif;
	
	ob_end_flush();

	}

	### -------------------------------------------
	### open files form url
	### -------------------------------------------
	function vRemoteOpen($url,$post=false, $timeout=0) {
		
		if($this->vconf['rServer'] ==1):
		
			$ch = curl_init();
			
			curl_setopt($ch,CURLOPT_URL, $url);
			curl_setopt($ch,CURLOPT_HEADER, 0);
			curl_setopt($ch,CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeout);
			
			if($post<>0):
				curl_setopt($ch,CURLOPT_POST,4);
			endif;
			
			$conteudo = curl_exec($ch);
			curl_close($ch);
			
			return $conteudo;
		
		endif;
  }


}
?>