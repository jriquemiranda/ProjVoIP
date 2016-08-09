<?php

class Boletim{
	private $handler;
	private $suap;
	
	public function __construct($tools){
		//$this->handler = $tools['handler'];
		$this->suap = $tools['suap'];
	}
	
	function minerar ($url, $cod_dici){
		echo "dentro de minerar";
		$consulta ="!<table class=\"borda\">(.*?)</table>!is";
		preg_match_all($consulta, $url, $matches);
		
		foreach ($matches[0] as $val1){
			$resultado2 = "!<tr class=\"\">(.*?)</tr>!is";
			preg_match_all($resultado2, $val1, $matches2);
			//$boletim = $resultado2[0];
		}
		
		foreach ($matches2[0] as $val2){
			$resultado3 = "!<td(.*?)</td>!is";
			preg_match_all($resultado3, $val2, $matches3);
			$boletim[]=$matches3[0];
		}
		
		foreach ($boletim as $val) {
			if(stripos($val[1], $cod_dici)){
				$disc=$val;
			}
		}
		
		$n1=$disc[7];
		$selec='|<[^>]+>(.*)</[^>]+>|U';
		$matches4=array();
		
		$teste=preg_match_all($selec, $n1 , $matches4, PREG_PATTERN_ORDER);
		
		$notas[0]=$matches4[1][0]; // Nota 1
		
		$n2=$disc[9];
		$selec='|<[^>]+>(.*)</[^>]+>|U';
		$matches5=array();
		
		$teste=preg_match_all($selec, $n2 , $matches5, PREG_PATTERN_ORDER);
		
		$notas[1]=$matches5[1][0]; // nota 2
		
		return $notas;
	} // Fim da função minerar
	
	function get_notas($matricula, $cod_disci){
		echo "dentro de get notas";
		$boletim = $this->suap->csuap("https://suap.ifrn.edu.br/edu/aluno/" . $matricula . "/?tab=boletim");
		
		echo "boletim";
		
		$result = $this->minerar($boletim, $cod_disci);
		$n1 = $result[0];
		$n2 = $result[1];
		echo "n1 $n1 n2 $n2";
		//$this->handler->execute_agi("SAY NUMBER $n1 \"\"");
		//$this->handler->execute_agi("SAY NUMBER $n2 \"\"");
	} // Fim da função get_boletim
} // Fim da classe Boletim
		
		
		
		
