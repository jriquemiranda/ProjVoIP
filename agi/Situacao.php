<?php
/* 
 *	Situacao.php
 *
 *	.SYNOPSIS
 *		Esta classe terá métodos para consultar as notas dos 
 *		usuários na disciplina selecionada informando a situacao atual.
 *
*/
	
class Situacao{
	private $handler;
	private $suap;
	
	public function __construct($tools){
		$this->handler = $tools['handler'];
		$this->suap = $tools['suap'];
	}
	
/*
 *	FUNCAO MINERAR
 *
 *  Retornará um array com 16 posições referentes as informações de determinada disciplina
 *	selecionada a partir do número do diário. 
 *
 *	PARAMETROS
 *	$boletim_html - variavel contendo uma string com o html da página do boletim no suap.
 *  $diario - variavel contendo uma string com o número do diário daquela disciplina.
 *
 *	RETORNOS
 *	Se bem sucedida, retonrará o array preenchido com os dados da disciplina
 *	Se mal sucedida, retornará false
 *
*/
	function minerar ($boletim_html, $diario){
		$this->handler->log_agi("SITUACAO.PHP tentando minerar o boletim");
		libxml_use_internal_errors(true);
		$codigo_fonte = new DOMDocument();
		
		if ($codigo_fonte->loadHTML($boletim_html)){
			$tabelas = $codigo_fonte->getElementsByTagName('table');			
			foreach($tabelas as $tabela_boletim){
				if ($tabela_boletim->getAttribute('class') === "borda"){
					$disciplinas = $tabela_boletim->getElementsByTagName('tr');
					foreach($disciplinas as $disciplina){
						$informacoes = $disciplina->getElementsByTagName('td');						
						$disciplina_diario = $informacoes->item(0)->nodeValue;
						$disciplina_diario = trim($disciplina_diario);					
						if ($disciplina_diario === $diario){
							for ($i=0; $i<=15; $i++){
								$info[$i] = $informacoes->item($i)->nodeValue;
							}
							return $info;
						}
					}
				}
			}
		}else{
			$this->handler->log_agi("SITUACAO.PHP nao foi possivel minerar o boletim");
			$this->handler->log_agi("SITUACAO.PHP possivel erro durante o download");
			return false;
		}
	} //Fim da funcao minerar
	
/*
 *	FUNCAO AVALIAR
 *	
 *	A partir das notas dos bimestres e da avaliação final irá informar a situação do aluno
 *	na disciplina selecionada. A resposta será passada para o Asterisk pela AGI utilizando
 * 	os métodos da classe Handler
 *
 *	PARAMETROS
 *	$n1 - String contendo a nota do primeiro bimestre ou o caractere '-'
 *	$n2 - String contendo a nota do segundo bimestre ou o caractere '-'
 *	$md - String contendo a média do aluno ou o caractere '-'
 *	$prova_final - String contendo a nota da avaliacao final ou o caractere '-'
 *	
 *	RETORNOS
 *	Se bem sucedida, passará para a AGI a mensagem que será tocada para o usuário
 *	Se mal sucedida, retornara false
*/
 
	function avaliar ($n1, $n2, $md, $prova_final){
		$this->handler->log_agi("SITUACAO.PHP Avaliando as notas");
		if(($n1 !== "-") && ($n2 !== "-") && ($prova_final !== "-")){
			$n1 = intval($n1); 
			$n2 = intval($n2);
			$md = intval($md);			
			$prova_final = intval($prova_final);			
			
			$mdf[0] = ($md+$prova_final)/2;
			$mdf[1] = ((2*$prova_final)+(3*$n2))/5;
			$mdf[2] = ((2*$n1)+(3*$prova_final))/5;
			
			arsort($mdf);
			$mdf[0] = round($mdf[0]);
			if ($mdf[0]<60){
				// Voce foi reprovado com media final $mdf[0]
				//$this->handler->execute_agi('ANSWER');
				$this->handler->execute_agi('STREAM FILE grupo5/4+2 ""');
				$this->handler->execute_agi("SAY NUMBER $mdf[0] \"\"");
			}else{
				// Voce foi aprovado com media final $mdf[0]
				//$this->handler->execute_agi('ANSWER');
				$this->handler->execute_agi('STREAM FILE grupo5/4+1 ""');
				$this->handler->execute_agi("SAY NUMBER $mdf[0] \"\"");
			}			
		}elseif($n1 !== "-" && $n2 !== "-"){
			$n1 = intval($n1); 
			$n2 = intval($n2);
			$md = intval($md);
			
			if($md>=60){
				// Voce foi aprovado com media $md
				//$this->handler->execute_agi('ANSWER');
				$this->handler->execute_agi('STREAM FILE grupo5/3+1 ""');
				$this->handler->execute_agi("SAY NUMBER $md \"\"");
			}elseif (($md > 20) && ($md < 60)){
				$naf[0] = 120 - $md;
				$naf[1] = (300-(3*$n2))/2;
				$naf[2] = (300-(2*$n1))/3;				
				asort($naf);	
				$naf[0] = round($naf[0]);
				// Sua media foi $md e voce fara a prova final necessitando de $naf[0]
				//$this->handler->execute_agi('ANSWER');
				$this->handler->execute_agi('STREAM FILE grupo5/3+2-1 ""');
				$this->handler->execute_agi("SAY NUMBER $md \"\"");
				$this->handler->execute_agi('STREAM FILE grupo5/3+2-2 ""');
				$this->handler->execute_agi("SAY NUMBER $naf[0] \"\"");
			}else{
				// Voce foi reprovado
				//$this->handler->execute_agi('ANSWER');
				$this->handler->execute_agi('STREAM FILE grupo5/3+3 ""');
			}
		}elseif($n1 !== "-" xor $n2 !== "-"){
			if($n1 !== "-"){
				$n1 = intval($n1);
				$n2 = (300-(2*$n1))/3;
				$n2 = round($n2);				
				// Você precisa de $n2 para passar por média na disciplina
				//$this->handler->execute_agi('ANSWER');
				$this->handler->execute_agi('STREAM FILE grupo5/2-1 ""');
				$this->handler->execute_agi("SAY NUMBER $n2 \"\"");
				$this->handler->execute_agi('STREAM FILE grupo5/2-2 ""');
			}else{
				$n2 = intval($n2);
				$n1 = (300-(3*$n2))/2;
				$n1 = round($n1);				
				// Você precisa de $n1 para passar por média na disciplina
				//$this->handler->execute_agi('ANSWER');
				$this->handler->execute_agi('STREAM FILE grupo5/2-1 ""');
				$this->handler->execute_agi("SAY NUMBER $n1 \"\"");
				$this->handler->execute_agi('STREAM FILE grupo5/2-2 ""');	
			}
		}else{
			// Voce nao possui nota lançada nesta disciplina
			//$this->handler->execute_agi('ANSWER');
			$this->handler->execute_agi('STREAM FILE grupo5/1 ""');
		}
	} //Fim da funcao avaliar
	
/*
 *	FUNCAO GET_SITUACAO
 *
 *	As outras funções dessa classe serão chamadas a partir dessa. Ao chamá-la, todo o trabalho de buscar o boletim, trata-lo 
 *	e retornar as informações procuradas será realizado. 
 *
 *	PARAMETROS
 *	$matricula - string contendo a matricula do aluno
 *	$diario - string contendo o número do diário da disciplina
 *
 *	RETORNOS
 *	Se bem sucedida, passará para a AGI a mensagem que será tocada para o usuário
 *  Se mal sucedida, retornará false
*/
	function get_situacao($matricula, $diario){
		//$this->handler->execute_agi('ANSWER');
		//$this->handler->log_agi("dentro de get_situacao");
		$boletim_html = $this->suap->csuap("https://suap.ifrn.edu.br/edu/aluno/" . $matricula . "/?tab=boletim");
		
		$this->handler->log_agi("SITUACAO.PHP get_situacao");
		if ($boletim = $this->minerar($boletim_html, $diario)){
			$n1 = $boletim[7];
			$n2 = $boletim[9];
			$md = $boletim[11];
			$prova_final = $boletim[12];
			
			$this->avaliar($n1, $n2, $md, $prova_final);
		}else{
			//$this->handler->execute_agi('ANSWER');
			$this->handler->execute_agi('STREAM FILE grupo5/erro1 ""');
			$this->handler->execute_agi('STREAM FILE grupo5/erro2 ""');
			return false;			
		}
	} // Fim da funcao get_situacao
	
} // Fim da classe Situacao
?>
