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

class captcha{ 

   // Matriz para criar o texto para imagem 
	private $maxChars 		= 4;
	private $fontSize 		= 15;
	private $larguaImagem 	= 180; 
	private $alturaImagem 	= 70;
	private $corDeFundo 	= "#FFCC00";
	private $corRetangulos 	= "#EFEFEF";
	public  $gerarValor		= "123456789abcdefghijklmnopqrstuvxwyzABCDEFGHIJKLMNOPQRSTUVXWYZ"; 
	private $fonts = array('titam.ttf','umberto.ttf','Bordu.ttf','cornfed.ttf','grobold.ttf','dorado.ttf');  
	private $serverRoot ='';
	private $path = "/corefix/misc/ttf/";
	private $noSquare;
	private $rotateImg;

   // Cores no formato hexadecimal               
   private $hexcolors = array("#FF9900", 
   						  "#000000", 
						  "#FF0000", 
						  "#FF00FF", 
						  "#808080", 
						  "#008000", 
						  "#336699", 
						  "#000080", 
						  "#800000", 
						  "#008080", 
						  "#800080", 
						  "#0000FF", 
						  "#660099", 
						  "#808000", 
						  "#CC9900"); 

   private $image; 
   
   // Gera uma semente para ser utilizada pela função srand 
   private function make_seed() { 
       list($usec, $sec) = explode(' ', microtime()); 
       return (float) $sec + ((float) $usec * 100000); 
   } 

   // Converte hexadecimal para rgb 
   private function hex2rgb($hex){ 
       $hex = str_replace('#','',$hex); 
       $rgb = array('r' => hexdec(substr($hex,0,2)), 
                    'g' => hexdec(substr($hex,2,2)), 
                    'b' => hexdec(substr($hex,4,2))); 
       return $rgb; 
   } 
    
   // Aloca uma cor para imagem 
  private function color($value){ 
       $rgb = $this->hex2rgb($value); 
       return ImageColorAllocate($this->image, $rgb['r'], $rgb['g'], $rgb['b']); 
   } 
    
   // Aloca uma cor aleatória para imagem 
  private function randcolor(){ 
       srand($this->make_seed()); 
       shuffle($this->hexcolors); 
       return $this->color($this->hexcolors[0]);    
   } 
    
   // Cria uma linha em  posição e cor aleatória 
  private function randline(){ 
       srand($this->make_seed()); 
       shuffle($this->hexcolors); 
       $i=rand(0, $this->larguaImagem); 
       $k=rand(0, $this->larguaImagem); 
       imagesetthickness ($this->image, 2); 
       imageline($this->image,$i,0,$k,$this->alturaImagem,$this->randcolor());    
   } 
    
   // Cria um quadrado 10X10 em posição e cor aleatória 
  private function randsquare(){ 
       imagesetthickness ($this->image, 1); 
       srand($this->make_seed()); 
       $x=rand(0, ($this->larguaImagem-15)); 
       $y=rand(0, ($this->alturaImagem-15)); 
       imageFilledRectangle( $this->image, $x, $y, $x+10, $y+10, $this->color($this->corRetangulos)); 
       imagerectangle ( $this->image, $x-10, $y, $x+10, $y+10, $this->randcolor()); 
   } 
    
	// Cria uma imagem com texto aleatório e retorno o texto 
	private function output($saveStr = false){ 
	
	$this->image = ImageCreate($this->larguaImagem,$this->alturaImagem); 
	
	$background = $this->color($this->corDeFundo);   
	
	imageFilledRectangle($this->image, 0,0,$this->larguaImagem , $this->alturaImagem, $background); 
	
	srand($this->make_seed()); 
	
	# space in chars
	$percent80 = $this->larguaImagem - ($this->larguaImagem * 0.15 );
	#$textSpace = ceil(($this->larguaImagem-40)/$this->maxChars) ;
	
	$textSpace = ceil($percent80/$this->maxChars) ;
	$topMargim = $this->alturaImagem > 20 ? $this->alturaImagem/2.2 : 5;
	
	# generate chars
	for ($i=0;$i < $this->maxChars ;$i++) :
		
		$this->gerarValor = str_shuffle($this->gerarValor); 
		
		shuffle($this->hexcolors); 
		shuffle($this->fonts); 
		
		# select a char on array
		$char=$this->gerarValor[0]; 
		
		# rotarion of chars
		$rotate = $this->rotateImg > 1 ? rand(-9,10) : 0;
		
		# join chars value
		$saveStr.=$char; 
		
		# make captcha		
		imagettftext($this->image,$this->fontSize,$rotate,(10+($i*$textSpace)), rand($this->alturaImagem,$topMargim),$this->randcolor(),"{$this->serverRoot}{$this->path}{$this->fonts[0]}",$char); 

	endfor; 
	
	for ($k=0;$k < ($this->maxChars/2) ;$k++):
	
		if($this->noSquare == 0):
			$this->randsquare(); 
		endif;
		$this->randline(); 
	
	endfor;
	
	header("Content-type: image/jpeg"); 
	imagejpeg($this->image); 
	imagedestroy($this->image,'',100); 
	
	return $saveStr; 
	} 


	public function __construct($ch,$wt,$ht,$bg,$server,$square,$d=false,$textSize=false,$textUse=false,$rotate=false){
		
		$this->larguaImagem    = $wt; 
		$this->alturaImagem    = $ht;
		$this->maxChars  	   = $ch;
		$this->corDeFundo 	   = $bg;
		$this->corRetangulos   = $square;
		$this->serverRoot	   = $server;
		$this->noSquare		   = $d <= 0 ? 0 : 1;
		$this->fontSize		   = !empty($textSize) ? $textSize : 40;
		$this->gerarValor	   = !empty($textUse) ? $textUse : "123456789abcdefghijklmnopqrstuvxwyzABCDEFGHIJKLMNOPQRSTUVXWYZ"; ;
		$this->rotateImg	   = $rotate >= 1 ? $rotate : 5;
		
		$_SESSION["authValor"]  = $this->output(); 
	}

} 

?>