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

class vtxImages{

	public $larguraFoto;
	public $alturaFoto;
	public $qualidadeFoto;
	
	public $marcaDagua;
	public $posicaoMarcaDagua;	
	public $caminhoFoto;
	
	public $fotoTransparencia = false;
	

#############################################################################################################
#### RETURN IMAGE MIME
#############################################################################################################

private function getImageMime($arquivo){

#Associa valores de retorno;
$par = array();


#verifica se nao e pasta
$figura = getimagesize($arquivo);

# identifica o tipo de imagem 
switch($figura['mime']):
	
	case ("image/gif"):
		$par['mime'] 	= $figura['mime'];
		$par['height'] 	= $figura[1];
		$par['width'] 	= $figura[0];
		$par['bits'] 	= $figura['bits'];
	break; 
	
	case ('image/png'):
		$par['mime'] 	= $figura['mime'];
		$par['height'] 	= $figura[1];
		$par['width'] 	= $figura[0];
		$par['bits'] 	= $figura['bits'];
	break;

	case ('image/x-png'):
		$par['mime'] 	= $figura['mime'];
		$par['height'] 	= $figura[1];
		$par['width'] 	= $figura[0];
		$par['bits'] 	= $figura['bits'];	
	break;

	case ('image/jpeg'):
		$par['mime'] 	= $figura['mime'];
		$par['height'] 	= $figura[1];
		$par['width'] 	= $figura[0];
		$par['bits'] 	= $figura['bits'];	
	break;

	case ('image/pjpeg'):
		$par['mime'] 	= $figura['mime'];
		$par['height'] 	= $figura[1];
		$par['width'] 	= $figura[0];
		$par['bits'] 	= $figura['bits'];	 
	break;	
	
	case ('image/bmp'):
		$par['mime'] 	= $figura['mime'];
		$par['height'] 	= $figura[1];
		$par['width'] 	= $figura[0];
		$par['bits'] 	= $figura['bits'];	 
	break;
				
	default:
		$par['mime'] 	= $figura['mime'];
		$par['height'] 	= $figura[1];
		$par['width'] 	= $figura[0];
		$par['bits'] 	= $figura['bits'];	
	break;
	
endswitch;
	return $par;

}

#############################################################################################################
#### RETURN IMAGE PROPERTIES
#############################################################################################################
public function getImageInfo($arquivo){

	#Associa valores de retorno;
	$par = array();
	
	ini_set('memory_limit', '-1');
	
	#obtem dados da imagem
	$figura = getimagesize($arquivo);
	
	
	# identifica o tipo de imagem 
	switch($figura['mime']):
	
		case ("image/gif"):
			$par['create'] 	= imagecreatefromgif($arquivo); 
			$par['mime'] 	= $figura['mime'];
			$par['height'] 	= $figura[1];
			$par['width'] 	= $figura[0];
			$par['bits'] 	= $figura['bits'];
		break; 
		
		case ('image/png'):
			$par['create'] 	= imagecreatefrompng($arquivo);
			$par['mime'] 	= $figura['mime'];
			$par['height'] 	= $figura[1];
			$par['width'] 	= $figura[0];
			$par['bits'] 	= $figura['bits'];
		break;
	
		case ('image/x-png'):
			$par['mime'] 	= $figura['mime'];
			$par['height'] 	= $figura[1];
			$par['width'] 	= $figura[0];
			$par['bits'] 	= $figura['bits'];	
		break;
	
		case ('image/jpeg'):
			$par['create'] 	= imagecreatefromjpeg($arquivo); 
			$par['mime'] 	= $figura['mime'];
			$par['height'] 	= $figura[1];
			$par['width'] 	= $figura[0];
			$par['bits'] 	= $figura['bits'];	
		break;
	
		case ('image/pjpeg'):
			$par['create'] 	= imagecreatefromjpeg($arquivo);
			$par['mime'] 	= $figura['mime'];
			$par['height'] 	= $figura[1];
			$par['width'] 	= $figura[0];
			$par['bits'] 	= $figura['bits'];	 
		break;	
		
		case ('image/bmp'):
			$par['create'] 	= imagecreatefromwbmp($arquivo);
			$par['mime'] 	= $figura['mime'];
			$par['height'] 	= $figura[1];
			$par['width'] 	= $figura[0];
			$par['bits'] 	= $figura['bits'];	 
		break;
					
		default:
			$par['create'] 	= imagecreatefromjpeg($arquivo); 
			$par['mime'] 	= $figura['mime'];
			$par['height'] 	= $figura[1];
			$par['width'] 	= $figura[0];
			$par['bits'] 	= $figura['bits'];	
		break;
	
	endswitch;

	return $par;

}

#############################################################################################################
#### PUBLIC CALL FROM NEW IMAGE
#############################################################################################################
//public function miniatura($arquivo,$largura,$altura,$qualidade,$destino=false){
public function miniatura($arquivo, $largura, $altura,$qualidade, $destino){
	

	$this->larguraFoto			= $largura;
	$this->alturaFoto			= $altura;
	$this->qualidadeFoto		= $qualidade;

	$this->vtxResizeImagem($arquivo,$destino);

	return true;
}

#############################################################################################################
#### MAKE A NEW IMAGE
#############################################################################################################
private function vtxResizeImagem ($arquivo, $fotoNome, $base64=false) {

	$fotoLargura 		= $this->larguraFoto;
	$fotoAltura 		= $this->alturaFoto;
	$fotoQualidade  	= $this->qualidadeFoto;
	$fotoNome 			= $fotoNome;
	$fotoOpacidade 		= $this->fotoTransparencia != "" ? $this->fotoTransparencia : FALSE;

	 
    $gerar = $this->getImageInfo($arquivo);
	
	$old_x = $gerar['width'];
    $old_y = $gerar['height'];
	
	$img = $gerar['create'];
	
/*	
	# check props
	if($old_x < $fotoLargura && $old_y < $fotoAltura):
        
		$thumb_w = $old_x;
        $thumb_h = $old_y;
		
    elseif ($old_x > $old_y):
        
		$thumb_w = $fotoLargura;
        $thumb_h = floor(($old_y*($fotoAltura/$old_x)));
		
    elseif ($old_x < $old_y):
        
		$thumb_w = floor($old_x*($fotoLargura/$old_y));
        $thumb_h = $fotoAltura;
		
    elseif ($old_x == $old_y):
        
		$thumb_w = $fotoLargura;
        $thumb_h = $fotoAltura;
		
    endif;
	
    $thumb_w = ($thumb_w<1) ? 1 : $thumb_w;
    $thumb_h = ($thumb_h<1) ? 1 : $thumb_h;
    
	$new_img = ImageCreateTrueColor($thumb_w, $thumb_h);
*/
###########################################################################

	$ratio = $gerar['width']/$gerar['height']; // width/height
	
	if( $ratio > 1) {
		$thumb_w  = $this->larguraFoto;
		$thumb_h = $this->alturaFoto/$ratio;
	}
	else {
		$thumb_w  = $this->larguraFoto*$ratio;
		$thumb_h = $this->alturaFoto;
	}
	
	# $src = imagecreatefromstring(file_get_contents($fn));
	# $dst = imagecreatetruecolor($width,$height);
	
	# imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);

	$new_img = ImageCreateTrueColor($thumb_w, $thumb_h);
	
###########################################################################   
    if($fotoOpacidade==false || $fotoOpacidade== 0 || $fotoOpacidade=="" ):
        
		if( $gerar['mime']=="image/png" || $gerar['mime']=="image/x-png"  ):
            
			imagealphablending($new_img, false);
            $colorTransparent = imagecolorallocatealpha($new_img, 0, 0, 0, 127);
            imagefill($new_img, 0, 0, $colorTransparent);
            imagesavealpha($new_img, true);
			
        elseif($gerar['mime'] == "image/gif"):
		
            $trnprt_indx = imagecolortransparent($img);
			
            if ($trnprt_indx >= 0):
                
				//its transparent
                $trnprt_color = imagecolorsforindex($img, $trnprt_indx);
                $trnprt_indx = imagecolorallocate($new_img, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
                imagefill($new_img, 0, 0, $trnprt_indx);
                imagecolortransparent($new_img, $trnprt_indx);
				
            endif;
			
        endif;
		
    else:
        
		Imagefill($new_img, 0, 0, imagecolorallocate($new_img, 255, 255, 255));
		
    endif;
   
    imagecopyresampled($new_img,$img, 0,0,0,0, $thumb_w, $thumb_h, $old_x, $old_y);
    
	$fotoNome = !empty($fotoNome) ? $fotoNome : '';
	
    if($base64):
        
		ob_start();
        imagepng($new_img);
        $img = ob_get_contents();
        ob_end_clean();
        
		$return = base64_encode($img);
    
	else:
	
		if($gerar['mime']=="image/jpeg" || $gerar['mime']=="image/pjpeg"):
		
			if(!empty($fotoNome)):
            	imagejpeg($new_img, $fotoNome, $fotoQualidade);
			else:
				imagejpeg($new_img,NULL,75);
			endif;
			$return = true;
        
		elseif($gerar['mime']=="image/png" || $gerar['mime']=="image/x-png"):
		
            
			if(!empty($fotoNome)):
            	imagepng($new_img, $fotoNome );
			else:
				imagepng($new_img,NULL);
			endif;			
			
			$return = true;
			
        elseif($gerar['mime'] == "image/gif"):

			if(!empty($fotoNome)):
            	imagegif($new_img, $fotoNome, $fotoQualidade);
			else:
				imagegif($new_img,NULL,75);
			endif;			
            
            $return = true;
			
        endif;
    
	endif;
	
    imagedestroy($new_img);
    imagedestroy($img);
    
	return $return;
}

########################################################################
# merge images
########################################################################
public function fotosMarcaDagua($grande,$pequeno,$nome,$posicao,$trans=false) {
	
	$sourcefile = $grande;
	$insertfile = $pequeno;
	$targetfile = $nome;
	$pos		= $posicao;
	$transition	= $trans;
	
	//$pos          = Position where $insertfile will be inserted in $sourcefile
	//                0 = middle
	//                1 = top left
	//                2 = top right
	//                3 = bottom right
	//                4 = bottom left
	//                5 = top middle
	//                6 = middle right
	//                7 = bottom middle
	//                8 = middle left

if(is_file($insertfile)):  
	$data1 = $this->getImageInfo($insertfile);
	$data2 = $this->getImageInfo($sourcefile);

	if($data1['mime']=="image/png" || $data1['mime']=="image/x-png"):

    $insertfile_id = imagecreatefrompng($insertfile);
    imageAlphaBlending($insertfile_id, false);
    imageSaveAlpha($insertfile_id, true);
    $sourcefile_id = $data2['create'];

	else:	
	$insertfile_id = $data1['create'];
    $sourcefile_id = $data2['create'];
	endif;

    $sourcefile_width=imagesx($sourcefile_id);
    $sourcefile_height=imagesy($sourcefile_id);
	
    $insertfile_width=imagesx($insertfile_id);
    $insertfile_height=imagesy($insertfile_id);

//middle
    if( $pos == 0 ):
        $dest_x = ( $sourcefile_width / 2 ) - ( $insertfile_width / 2 );
        $dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 );
    endif;

//top left
    if( $pos == 1 ):
        $dest_x = 0;
        $dest_y = 0;
    endif;

//top right
    if( $pos == 2 ):
        $dest_x = $sourcefile_width - $insertfile_width;
        $dest_y = 0;
    endif;

//bottom right
    if( $pos == 3 ):
        $dest_x = $sourcefile_width - $insertfile_width;
        $dest_y = $sourcefile_height - $insertfile_height;
    endif;

//bottom left   
    if( $pos == 4 ):
        $dest_x = 0;
        $dest_y = $sourcefile_height - $insertfile_height;
    endif;

//top middle
    if( $pos == 5 ):
        $dest_x = ( ( $sourcefile_width - $insertfile_width ) / 2 );
        $dest_y = 0;
    endif;

//middle right
    if( $pos == 6 ):
        $dest_x = $sourcefile_width - $insertfile_width;
        $dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 );
    endif;
       
//bottom middle   
    if( $pos == 7 ):
        $dest_x = ( ( $sourcefile_width - $insertfile_width ) / 2 );
        $dest_y = $sourcefile_height - $insertfile_height;
    endif;

//middle left
    if( $pos == 8 ):
        $dest_x = 0;
        $dest_y = ( $sourcefile_height / 2 ) - ( $insertfile_height / 2 );
    endif;
   
if(empty($transition)):
    imagecopy($sourcefile_id,
				   $insertfile_id,
				   $dest_x,
				   $dest_y,
				   0,
				   0,
				   $insertfile_width,
				   $insertfile_height);
else:

    imagecopymerge($sourcefile_id,
				   $insertfile_id,
				   $dest_x,
				   $dest_y,
				   0,
				   0,
				   $insertfile_width,
				   $insertfile_height,
				   $transition);
endif;
    
	imagejpeg ($sourcefile_id,"{$targetfile}");
	
	return true;
	else:
	return false;
	endif;
}

########################################################################
# funcao aberta para gerar miniaturas com marca dagua
########################################################################
public function fotoMiniaturas($arquivo,$nome){
		
	if(!empty($this->caminhoFoto) && !is_dir($this->caminhoFoto)):
	
		#print "alert('{$this->caminhoFoto} : nao e uma pasta valida')";
		exit(print("0|".$this->caminhoFoto." : nao e uma pasta valida"));
	endif;
	
	
		$this-> miniatura($arquivo,
						  $this->larguraFoto,
						  $this->alturaFoto,
						  $this->qualidadeFoto,
						  $this->caminhoFoto.$nome);
	
		
	}
	
########################################################################
# funcao aberta para gerar miniaturas com marca dagua, definindo destino
########################################################################
public function fotoMarcar($origem,$destino){

	$this->fotosMarcaDagua($origem,
						   $this->marcaDagua,
						   $destino,
						   $this->posicaoMarcaDagua,
						   false);
	}	
	
	
########################################################################
# funcao que faz varredura de imagens em uma pasta
########################################################################
public function imageLisContent($dir,$protected){


ob_start();

if(is_dir($dir)) :

$picts = array();

	
	$d = dir($dir);

		while (false !== ($e = $d->read())):
		
		$ext = strtolower(substr($e,-3,3));
		
		
			#check is valid entry
			if( in_array($ext,$protected) ):
				
				# check is dir
				if(is_dir("{$dir}/{$e}")  ):
				  $this->imageLisContent("{$dir}/{$e}",$protected);
				else:
				
				  $fname = $this->clearFileName($e);
				  
				  $this->fotoMiniaturas("{$dir}/{$e}",$fname);
					#$picts[] = "{$dir}/{$e}";
					
				endif;
			
			elseif(is_dir("{$dir}/{$e}") && !in_array($e,array(".","..","__MACOSX")) ):
			  $this->imageLisContent("{$dir}/{$e}",$protected);
			else:
			
			endif;
		
		endwhile;

	$d->close();

ob_end_flush();

endif;

return isset($picts) ? $picts: "";
 
}


########################################################################
# aplica carimbo as fotos
########################################################################
public function imageListMerge($dir,$protected){

ob_start();	
$d = dir($dir);

while (false !== ($e = $d->read())):
   
   $ext = substr($e,-3,3);
  
   #check is valid entry
   if(in_array($ext,$protected) ):
	   
	   # check is dir
	   if(is_dir($dir.$e)):
			$this->imageListMerge($dir.$e,$protected);
	   else:
			$this->fotoMarcar("{$dir}/{$e}","{$dir}/{$e}");
	   endif;
   
   endif;
   
endwhile;
$d->close();

ob_end_flush();
}



	public  function clearFileName($s,$espaco='_'){
			
		$s = preg_replace('[ááâãÂÃÁÀ]',  'a', $s); 
		$s = preg_replace('[ÉÈÊËéèêë]',  'e', $s); 
		$s = preg_replace('[íìîïÌÍÎÏ]',  'i', $s); 
		$s = preg_replace('[ÚÙÜÛúùüû]',  'u', $s); 
		$s = preg_replace('[óòôõöÒÓÔÕÖ]','o', $s); 
		$s = str_replace('[ñÑ]', 'n', $s); 
		$s = str_replace('[çÇ]', 'c', $s); 
		$s = preg_replace('/\ \//','', $s); 
		$s = preg_replace('/\s+/','', $s); 
	
		$s = stripslashes($s);
		
		return preg_replace('([^a-z0-9\_\.]*)','', strtolower($s));
		
	}


}
?>