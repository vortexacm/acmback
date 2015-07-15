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

class paginator{

//class paginacao{

 public $urlAtual; 		# urlAtual em que a classe esta sendo executada
 public $separador; 	# normal use ? - se Apache MD use / ou o seu delimitador
 public $delimitador; 	# normal use ? - se Apache MD use / ou o seu delimitador

 public $vlrQuery; 		# valor passado pela url para paginar

 public $maxResultados		= 20; 		 # resultados por paginas
 public $maxLinks 			= 10 ; 		 # links por pagina
 public $avancar 			= " [ >> ]"; # valor usado para navecao avancar, pode ser usado uma img
 public $voltar 			= "[ << ] "; # valor usado para navecao voltar, pode ser usado uma img
 public $totalFound			= ""; # numero de resultados encontrados
 

 // 
 public $masks;
 public $campo; 	# quais os campos a serem procurados
 public $tabela; 	# tabela ou tabelas a ser
 public $condicao;
 public $grupo;
 public $ordem;
 public $debug = 0;
 public $match;
 public $matchgrp;
 public $cached = "SQL_CACHE";
 
 public $resultadoArray;
 public $returnOnArray;
 public $resultadoString;
 public $paginacaoCriada;
 public $paginasTotal;

 protected $paginasMostradas = 0;
 
 public $lnk;
 public $totalRegisters = 0;
 
 public $jsPages = 0;

#-------------------------------------------------------#
# paginar()
#
# Executa a query para obter o numero de paginas
# deve ser o primeiro parametro a ser executado
#
# $a =  new paginacao
# $a->paginar;
#
#-------------------------------------------------------#
public function paginar(){
	$this->vtxGetResults();
	}

#-------------------------------------------------------#
# Paginacao() 
#
# Retorna os indices criados para montar a 
# paginacao.
#
# Exemplo
# << voltar 1 2 3 4 avacar >>
#
# o valor é devolvido em uma string e pode ser printado
# ou aplicado a qualquer ponto da pagina
#
# Para chamar paginacao execute antes paginar
# $a =  new paginacao
# $a->paginar;
# print $a-> paginacao
#
#
#-------------------------------------------------------#
public function paginacao(){
		return $this->paginacaoCriada;
 	}

#-------------------------------------------------------#
# paginacaoConteudo() 
#
# Retorna um array com o resultado da
# execucao da query
#
# Exemplo
# se na query foram enviados os valores: 
# nome,telefone,email a funcao retornara um array com os 
# seguintes valores e formato
# 
# array  
# indice = 0 
# valor = nome
#
# array[0][valor]
#
# como o valor retornado sera um array o retorno podera ser
# usado como por exemplo
# 
# $a =  new paginacao
# $a->paginar;
# $dados = $this->paginacaoConteudo
#
# for ($a=0; $a<count($dados); $a++):
#
# print $dados[$a]['nome'];
#
# endfor;
#
# print $a-> paginacao
#
#
#-------------------------------------------------------#
public function paginacaoConteudo(){
	return $this->resultadoArray;
	}

public function returnDataContent(){

	return $this->returnOnArray;

}

#-------------------------------------------------------#
# totalPaginacao()
#
# Retorna o total de resultados obtidos ao executar a query
#
# $a =  new paginacao
# $a->paginar;
# print $a->totalPaginacao()
#-------------------------------------------------------#
public function totalPaginacao(){
	return $this->paginasTotal;
	}


public function getMergeUrl(){

	$comURL = "a=1&sent=4";
	while(list($k,$v) = each($_GET)):
	
		if( $k != 'a' && $k !='sent'):
			$comURL .= "&{$k}={$v}";
		endif;
	
	endwhile;
	
	return $comURL;
}
#-------------------------------------------------------#
#
# Responsavel pela execucao da paginacao
#
#-------------------------------------------------------#

protected function vtxGetResults(){
	
	if ( empty($this->vlrQuery) ):
		$param = 0;
		$temp = 0;
	else:
		$temp 	= $this->vlrQuery;
		$param 	= ($this->vlrQuery - 1) * $this->maxResultados;
	endif;
	
	# get first fild do count
	$vFSTField = explode(",",$this->campo);
	
	# verifica o numero total de registros
	$buscaQ1  = "SELECT {$this->cached} COUNT( DISTINCT {$vFSTField[0]} ) AS total ";
	$buscaQ1 .= !empty($this->match) ? $this->match : " ";
	$buscaQ1 .= "FROM {$this->tabela} ";
	$buscaQ1 .= !empty($this->condicao) ? " WHERE {$this->condicao}" : ' WHERE 1=1 ' ;
	$buscaQ1 .= !empty($this->match) ? " GROUP BY {$this->matchgrp} " : " ";
	$buscaQ1 .= !empty($this->grupo) ? " GROUP BY {$this->grupo} " : '';
	$buscaQ1 .=";";
	
	# executa query para conteudo da paginacao
	$buscaQ2  = !empty($this->masks) ? "SELECT  {$this->cached} {$this->masks},{$this->campo} " : "SELECT {$this->cached} {$this->campo} ";
	$buscaQ2 .= !empty($this->match) ? $this->match : " ";
	$buscaQ2 .= " FROM {$this->tabela} ";
	$buscaQ2 .= !empty($this->condicao) ? " WHERE {$this->condicao} " : ' WHERE 1=1 ' ;
	$buscaQ2 .= !empty($this->grupo) ? " GROUP BY {$this->grupo} " : '';	
	$buscaQ2 .= !empty($this->ordem) ? " ORDER BY {$this->ordem} " : '';
	$buscaQ2 .= " LIMIT {$param},{$this->maxResultados} ";

	# executa a query da paginacao
	#$res2 	= $this->midia_query($buscaQ2) or die("2 - ".$this->midia_error());	

	  if($this->debug == 1):
	  
	  	echo "{$buscaQ1} <hr /> {$buscaQ2} ";
	  
	  endif;

###########################################################
##---------------------------------------------------------
	$aQuery = $buscaQ1.$buscaQ2;
	
	# if success on query
	if ($this->lnk->multi_query($aQuery) ):
	
		$a = 0;
		do {
			/* store first result set */
			if ($result = $this->lnk->store_result()):
				
				switch($a):
				
				case(0):
					
					$row = $result->fetch_row();
					$this->totalRegisters = $totalRegs = $row[0];	
					$this->paginasTotal   = $registros = $results_tot = $totalRegs;

				break;
	
				case(1):
					
					# word with returns					
					$proximo = $result->num_rows;
					
					$result_div = $results_tot/$this->maxResultados;
					
					$n_inteiro = (int)$result_div;
					
					if ($n_inteiro < $result_div) :
						$n_paginas = $n_inteiro + 1;
					else:
						$n_paginas = $result_div;
					endif;
					
					$pg_atual 	 = $param/$this->maxResultados+1;
					$reg_inicial = $param + 1;
					$pg_anterior = $pg_atual - 1;
					$pg_proxima  = $pg_atual + 1;
					
			
					$c = 0;		
					while( $dds = $result->fetch_array() ):
						
						foreach($dds as $a => $b):
						
							$campo = $a;
							$this->resultadoArray[$c][$a] = $b;
						
						endforeach;
					
						$c++;
					
					endwhile;
					
					$reg_final = $param + $c;
			
				break;
							
				endswitch;
				
				$result->free();
			
			endif;
			
			/* print divider */
			if ($this->lnk->more_results()):
				$a++;
			endif;
			
		} while ( $this->lnk->more_results() && $this->lnk->next_result());
	
	endif;

	# if error on query
	if ($this->lnk->error):
		try {   
			throw new Exception("MySQL error {$this->lnk->error} <br> Query:<br> {$aQuery}", $this->lnk->errno);   
		} catch(Exception $e ) {
			print(exit( "Error No: ".$e->getCode(). " - ". $e->getMessage() ));
		}
	endif;
	
## --------------------------------------------------------
###########################################################
if ($this->paginasTotal > $this->maxResultados):
	
	$this->paginasMostradas = 0;
	
	
		if(isset($this->vlrQuery)):
			if ($this->vlrQuery > 1) :
				
				# fist register
				$this->paginacaoCriada.= $this->jsPages == 1 ?
				"<a href='javascript:void(0)' rel='{$this->urlAtual}{$this->separador}0{$this->delimitador}' title='Primeira Pagina'>+</a>"
				:"<a href='{$this->urlAtual}{$this->separador}0{$this->delimitador}' title='Primeira Pagina'>+</a>";
				
				# start pagination
				$this->paginacaoCriada.= $this->jsPages == 1 ?
				"<a href='javascript:void(0)' rel='{$this->urlAtual}{$this->separador}{$pg_anterior}{$this->delimitador}'>{$this->voltar}</a>"
				:"<a href='{$this->urlAtual}{$this->separador}{$pg_anterior}{$this->delimitador}'>{$this->voltar}</a>";
			
				

			endif;
		endif;

		if ($temp > $this->maxLinks):
			if ($n_paginas >=$this->maxLinks):
				$this->maxLinks = $temp + 4;
				$this->paginasMostradas = $temp - 6;
			endif;
		endif;
		
	while(($this->paginasMostradas < $n_paginas) and ($this->paginasMostradas < $this->maxLinks)):
			
			$this->paginasMostradas ++;
			
			if ($pg_atual != $this->paginasMostradas):
				$this->paginacaoCriada.= $this->jsPages == 1 ?
				"<a href='javascript:void(0)' rel='{$this->urlAtual}{$this->separador}{$this->paginasMostradas}{$this->delimitador}'>"
				:
				"<a href='{$this->urlAtual}{$this->separador}{$this->paginasMostradas}{$this->delimitador}'>";
			endif;
			
			if ($pg_atual == $this->paginasMostradas):
				$this->paginacaoCriada.= "<strong>{$this->paginasMostradas}</strong>";
			else :
				$this->paginacaoCriada.= $this->paginasMostradas . "</a> ";
			endif;
	
	endwhile;
	
	if ($reg_final < $results_tot) :
		
		$this->paginacaoCriada.= $this->jsPages == 1 ? 
		"<a href='javascript:void(0)' rel='{$this->urlAtual}{$this->separador}{$pg_proxima}{$this->delimitador}'> {$this->avancar} </a>"
		:
		"<a href='{$this->urlAtual}{$this->separador}{$pg_proxima}{$this->delimitador}'> {$this->avancar} </a>";
	
		# go to last page
		$lastPage = ceil($this->paginasTotal/$this->maxResultados );
		
		$this->paginacaoCriada.= $this->jsPages == 1 ? 
		"<a href='javascript:void(0)'rel='{$this->urlAtual}{$this->separador}{$lastPage}{$this->delimitador}' title='Ultima Pagina'> + </a>" 
		: 
		"<a href='{$this->urlAtual}{$this->separador}{$lastPage}{$this->delimitador}' title='Ultima Pagina'> + </a>" ;
	
	endif;
	
	
endif;


}



public function getStringReturn(){

	return $this->resultadoString;	
	
}

}
?>