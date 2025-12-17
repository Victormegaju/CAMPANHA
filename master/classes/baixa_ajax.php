<?php
session_start();
header('Content-Type: application/json');

// Verifica login
if(!isset($_SESSION['cod_id'])){
    echo json_encode(['erro' => true, 'msg' => 'Sessão expirada']);
    exit;
}

$cod_id = $_SESSION['cod_id'];
require "../../db/Conexao.php";

// Verifica se recebeu o ID
if(isset($_POST['id_parcela'])){
    
    $id_parcela = $_POST['id_parcela'];
    $data_hoje = date('d/m/Y');

    try {
        // 1. Marca a parcela como PAGA (Status 2)
        $sql = "UPDATE financeiro2 SET status='2', pagoem=:datahoje WHERE Id=:id AND idm=:cod_id";
        $stmt = $connect->prepare($sql);
        $stmt->execute([':datahoje' => $data_hoje, ':id' => $id_parcela, ':cod_id' => $cod_id]);

        // 2. Verifica se precisa quitar a cobrança Pai (Se todas as parcelas foram pagas)
        $buscaChave = $connect->query("SELECT chave FROM financeiro2 WHERE Id='$id_parcela'");
        if($buscaChave->rowCount() > 0){
            $chave = $buscaChave->fetch(PDO::FETCH_OBJ)->chave;
            
            // Conta quantas sobraram em aberto
            $check = $connect->query("SELECT count(*) as total FROM financeiro2 WHERE chave='$chave' AND status='1' AND idm='$cod_id'");
            $pendentes = $check->fetch(PDO::FETCH_OBJ)->total;

            if($pendentes == 0){
                // Quita o Pai
                $connect->query("UPDATE financeiro1 SET status='2' WHERE chave='$chave' AND idm='$cod_id'");
            }
        }

        echo json_encode(['erro' => false, 'msg' => 'Sucesso']);

    } catch (Exception $e) {
        echo json_encode(['erro' => true, 'msg' => 'Erro no banco: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['erro' => true, 'msg' => 'ID não fornecido']);
}
?>