<?php
ob_start();
session_start();

if((!isset ($_SESSION['cod_id']) == true)) { unset($_SESSION['cod_id']); header('location: ../'); exit;}
$cod_id = $_SESSION['cod_id'];
require "../../db/Conexao.php";

// CADASTRA CONTAS A PAGAR
if(isset($_POST["cadpagar"]))  {
    
    $descricao      = $_POST['descricao'];
    $formapagamento = $_POST['formapagamento'];
    $parcelas       = $_POST['parcelas'];
    $dataparcela    = $_POST['dataparcela']; // Formato d/m/Y
    $vparcela       = $_POST['vparcela'];    // Formato 1000.00

    // Separa a data
    $vencimento_partes = explode('/', $dataparcela);
    $dia = $vencimento_partes[0];
    $mes = $vencimento_partes[1];
    $ano = $vencimento_partes[2];

    // Loop para inserir cada parcela
    for($i = 0; $i < $parcelas; $i++) {
        
        $dias_a_somar = $i * $formapagamento;
        // Calcula data correta para salvar no banco (d/m/Y)
        $data_vencimento = date('d/m/Y', strtotime("+$dias_a_somar days", mktime(0, 0, 0, $mes, $dia, $ano)));
        
        // Insere no banco (financeiro3 é contas a pagar)
        $sql = "INSERT INTO financeiro3 (idm, valor, datavencimento, descricao, status) VALUES (:idm, :valor, :data, :desc, '1')";
        $stmt = $connect->prepare($sql);
        $stmt->execute([
            ':idm'   => $cod_id,
            ':valor' => $vparcela,
            ':data'  => $data_vencimento,
            ':desc'  => $descricao . " (" . ($i+1) . "/" . $parcelas . ")"
        ]);
    }

    header("location: ../contas_pagar?sucesso=ok"); 
    exit;
}
?>