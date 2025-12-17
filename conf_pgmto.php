<?php
date_default_timezone_set('America/Sao_Paulo');

require_once "../db/Conexao.php";

$dataAtualMenos24Horas = date('Y-m-d H:i:s', strtotime('-24 hours'));

$consultapgmto  = $connect->query("SELECT * FROM mercadopago WHERE status = 'pending' AND data < '".$dataAtualMenos24Horas."'");
$buscapgmto = $consultapgmto->fetch(PDO::FETCH_OBJ);
					
echo $idpgmto = $buscapgmto->id;
$idpedid = $buscapgmto->idp;



?>