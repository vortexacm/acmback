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

class calendar extends ui{
	
public $maxDay = '';	
public $vcm   = "";
public $vcy   = "";
public $vcd   = "";
public $vfd   = 0;

public $cvlnk = "";

public $navigator = array();
# meses e dias
public $meses   = array(1=>"Janeiro","Fevereiro","Mar&ccedil;o","Abril","Maio","Junho","Julho","Agosto","Setembro","Outubro","Novembro","Dezembro");
public $dm 		= array(1=>31,29,31,30,31,30,31,31,30,31,30,31);
public $cor  	= array(1=>"preto","laranja","verde","amarelo","azul","fuzica","marron","vermelho");
public $evtz 	= "";

public function makeCalendar(){
	
	# obtem primeiro dia do mes
	$mcp =  !empty($this->vcm) && !empty($this->vcy) ? mktime(0,0,0,$this->vcm,1,$this->vcy ) : mktime(0,0,0,date('m'),1,date('Y')) ;
	
	$cdia 		  = date('n',$mcp);
	$this->maxDay = $this->dm[$cdia];
	$this->vcd    = $this->vfd > 0 ? $this->vfd : date('d');

	
	# retona o primeiro dia do mes
	# current month start
	$cms = date('w',$mcp);
	
	# ultimo dia do mes
	if(!empty($this->vcm) && isset($this->vcm) ):
		$cmf = $this->dm[$this->vcm];
	else:
		$cmf = $this->dm[date('n')];
	endif;
	
	
	$timeStart=$mcp;
	$timeEnd = !empty($this->vcm) && !empty($this->vcy) ?  mktime(23,59,59,$this->vcm,$cmf,$this->vcy ) :  mktime(23,59,59,date('n'),$cmf,date('Y') );
	
	# mes do calendario
	$clm =  date("n",$mcp);
	$cla =  date("Y",$mcp);
	
	# mes atual
	$vam = $this->vfd > 0 ? date('n',$mcp) : date("n");
	
	# dias do mes
	$i = 1;
	
	# controle de linhas
	$p = 0;
	
	# 35 = 7 days x 5 weeks
	for($a = 1; $a <= 42; $a++):
	
		# seta os dias do mes
		if ( $a > $cms ):
		
			if ($i <= $this->maxDay ):
			
				$dias[$a]['dia'] 	=  $i;
				$dias[$a]['url']    =  isset($this->lnk[$i]) ? $this->lnk[$i] : "d={$i}&m={$clm}&a={$cla}";
				$dias[$a]['classe'] =  !isset($this->evtz[$i]) ? 
									   $i == $this->vcd && $vam == $clm ? "current" : "normal" : $this->evtz[$i];
			$i++;
			endif;
		
		else:
			$dias[$a]['dia']    = '';
			$dias[$a]['classe'] = '';
			$dias[$a]['url'] 	= '';
		endif;
		
		# quebra linhas de semana
		if ($p == 6 ):
			$dias[$a]['pulo'] =  '</tr><tr>';
			$p = 0;
		else:
			$dias[$a]['pulo'] = '';
			$p++;
		endif;
	
	endfor;
	
	############################################################################################3
	# parametros de controle para avanco de mes e ano
	############################################################################################3
	if(!empty($this->vcm) && $this->vcm < 12 && $this->vcm > 1 && is_numeric($this->vcm) ):
	
		# echo  1;
		# meses
		$this->navigator['mRev'] = $this->vcm-1;
		$this->navigator['mFor'] = $this->vcm+1;
		
		$this->navigator['yRev'] = !empty($this->vcy) ? $this->vcy	: date("Y");
		$this->navigator['yFor'] = !empty($this->vcy) ? $this->vcy	: date("Y");
		$this->navigator['yCur'] = !empty($this->vcy) ? $this->vcy	: date("Y");

		# set current month	
		$this->navigator['text'] = $this->meses[$this->vcm];
			
	elseif(!empty($this->vcm) && $this->vcm == '1' && is_numeric($this->vcm) ):
	
		# echo  2;
		# meses
		$this->navigator['mRev'] = 12;
		$this->navigator['mFor'] = 2;

		$this->navigator['yRev'] = !empty($this->vcy) ? $this->vcy-1: date("Y")-1;
		$this->navigator['yFor'] = !empty($this->vcy) ? $this->vcy	: date("Y");
		$this->navigator['yCur'] = !empty($this->vcy) ? $this->vcy	: date("Y");
		
		# set current month	
		$this->navigator['text'] = $this->meses[$this->vcm];

			
	elseif(!empty($this->vcm) && $this->vcm == 12 && is_numeric($this->vcm)):
	
		# echo  3;
		
		# meses
		$this->navigator['mRev'] = 11;
		$this->navigator['mFor'] = 1;		

		# ano
		$this->navigator['yRev'] = !empty($this->vcy) ? $this->vcy		: date("Y");
		$this->navigator['yFor'] = !empty($this->vcy) ? $this->vcy+1	: date("Y")+1;
		$this->navigator['yCur'] = !empty($this->vcy) ? $this->vcy		: date("Y");
		
		# set current month	
		$this->navigator['text'] = $this->meses[$this->vcm];

	
	elseif(empty($this->vcm) && date('n') == 12 ):

		# echo  4;
			
		# meses
		$this->navigator['mRev'] = 11;
		$this->navigator['mFor'] = 1;		
		
		# anos
		$this->navigator['yRev'] = date("Y");
		$this->navigator['yFor'] = date("Y")+1;
		$this->navigator['yCur'] = date("Y");
		
		# set current month	
		$this->navigator['text'] = $this->meses[date('n')];

	
	elseif(empty($this->vcm) && date('n') == 1 ):

		# echo  5;
			
		# meses
		$this->navigator['mRev'] = 12;
		$this->navigator['mFor'] = 2;		
		
		# anos
		$this->navigator['yRev'] = date("Y")-1;
		$this->navigator['yFor'] = date("Y");
		$this->navigator['yCur'] = date("Y");
	
		# set current month	
		$this->navigator['text'] = $this->meses[date('n')];

	else:

		$this->navigator['mRev'] = date('n') - 1;
		$this->navigator['mFor'] = date('n') + 1;		
		
		# anos
		$this->navigator['yRev'] = date("Y");
		$this->navigator['yFor'] = date("Y");
		$this->navigator['yCur'] = date("Y");

		# set current month	
		$this->navigator['text'] = $this->meses[date('n')];

		# echo  6;
	endif;

	return $dias;

}

	# get current values
	public function getNavigator(){
			
		return $this->navigator;	
			
	}

}

?>