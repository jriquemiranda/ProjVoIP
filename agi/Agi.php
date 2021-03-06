#!/usr/bin/php
<?php
//ob_implicit_flush(false);
//error_reporting(0);

$s_in = fopen('php://stdin', 'r');
$s_out = fopen('php://stdout', 'w');

require_once(dirname(__FILE__) . '/' . 'Suap.php');
require_once(dirname(__FILE__) . '/' . 'Handler.php');
require_once(dirname(__FILE__) . '/' . 'Main.php');
require_once(dirname(__FILE__) . '/' . 'Situacao.php');
require_once(dirname(__FILE__) . '/' . 'Dbc.php');

//$matricula = '20141014050520';
//$senha = 'metron.550';
//$diario = '7536';

// --- BLOCO CONEXAO  --- \\
$handler = new Handler($s_in, $s_out);
$dbc = new Dbc();
$matricula = $handler->getCallerId();
$aluno = $dbc->getByMat($matricula);
$senha = $aluno['spass'];
$suap = new Suap($matricula,$senha);
//$handler->log_agi($suap->consume('https://suap.ifrn.edu.br/'));
$tools = ['suap'=>$suap,'handler'=>$handler];

// --- BLOCO MENU  --- \\
$menu = new MenuBasic($tools);
$diario = trim($menu->getInfo($matricula));
$opc = $menu->getOpMenu($matricula);

//$handler->log_agi('>>>'.$diario.'<<<');
//$handler->log_agi('<<<'.$opc.'>>>');

// --- BLOCO OPCAO ---\\

switch ($opc) {
   case 'op1':
	$handler->log_agi("opc=1");
	break;
   case 'op2': $handler->log_agi("opc=2"); break;
   case 'op3':
	// --- BLOCO SITUACAO --- \\
	$sit = new Situacao($tools);
	$sit->get_situacao($matricula, $diario);
	break;
}
// --- BLOCO FINAL  --- \\

$handler->endConversation();
?>
