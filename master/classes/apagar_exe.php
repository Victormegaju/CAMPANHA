<?php
ob_start();
session_start();

// Verifica login
if((!isset ($_SESSION['cod_id']) == true)) { 
    unset($_SESSION['cod_id']); 
    header('location: ../'); 
    exit;
}

$cod_id = $_SESSION['cod_id'];
require "../../db/Conexao.php";

// ==========================================================
// RENOVAR MENSALIDADE (Salvar histórico e jogar data pra frente)
// ==========================================================
if(isset($_GET["idfin2"]) && isset($_GET["emdias"]))  {

    $id_parcela = $_GET['idfin2'];
    $id_pai     = $_GET['codcli'];
    
    // 1. Busca a data atual da parcela para calcular a próxima
    $busca = $connect->prepare("SELECT datapagamento, parcela, chave FROM financeiro2 WHERE Id=:id AND idm=:idm");
    $busca->bindValue(':id', $id_parcela);
    $busca->bindValue(':idm', $cod_id);
    $busca->execute();
    
    if($busca->rowCount() > 0) {
        $dados = $busca->fetch(PDO::FETCH_OBJ);
        $data_atual_br = $dados->datapagamento; // Ex: 15/10/2023
        $valor = $dados->parcela;
        $chave = $dados->chave;

        // Converte para formato Americano para somar (Y-m-d)
        $data_obj = DateTime::createFromFormat('d/m/Y', $data_atual_br);
        
        // --- REGISTRAR NO EXTRATO (Opcional, mas bom para saber que pagou) ---
        // Aqui você poderia inserir numa tabela de "extrato" se tiver.
        // Como pediu apenas para renovar, vamos focar nisso.

        // 2. Soma 1 Mês na data
        $data_obj->modify('+1 month');
        $nova_data = $data_obj->format('d/m/Y');

        // 3. Atualiza a parcela com a NOVA DATA e mantém status EM ABERTO (1)
        // Resetamos também juros e dias vencidos para começar o mês limpo
        $update = $connect->prepare("UPDATE financeiro2 SET datapagamento = :novadata, status='1', dias_vencidos='0', juros_calculados='0', taxa_juros_diaria='0' WHERE Id=:id");
        $update->bindValue(':novadata', $nova_data);
        $update->bindValue(':id', $id_parcela);
        $exec = $update->execute();

        if($exec) {
            // Sucesso: Volta para a tela do cliente
            header("location: ../ver_financeiro?vercli=$id_pai&sucesso=renovado");
            exit;
        } else {
            echo "Erro ao atualizar data.";
        }
    } else {
        echo "Parcela não encontrada.";
    }
}

// Se cair aqui sem parâmetros, volta
header("location: ../contas_receber");
?>