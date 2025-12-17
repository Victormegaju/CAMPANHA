<?php
session_start();

if (!isset($_SESSION['cod_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$cod_id = $_SESSION['cod_id'];

require_once __DIR__ . '/../../db/Conexao.php';

header('Content-Type: application/json');

$acao = $_POST['acao'] ?? '';

switch ($acao) {
    case 'criar':
        criarContato($connect, $cod_id);
        break;
    
    case 'atualizar':
        atualizarContato($connect, $cod_id);
        break;
    
    case 'excluir':
        excluirContato($connect, $cod_id);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
}

function criarContato($connect, $cod_id) {
    try {
        $nome = trim($_POST['nome'] ?? '');
        $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
        
        if (empty($telefone)) {
            echo json_encode(['success' => false, 'message' => 'Telefone é obrigatório']);
            return;
        }
        
        // Verificar duplicidade
        $stmt = $connect->prepare("SELECT id FROM contatos_whatsapp WHERE id_usuario = ? AND telefone = ?");
        $stmt->execute([$cod_id, $telefone]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Este telefone já está cadastrado']);
            return;
        }
        
        $stmt = $connect->prepare("INSERT INTO contatos_whatsapp (id_usuario, telefone, nome, origem) VALUES (?, ?, ?, 'manual')");
        $stmt->execute([$cod_id, $telefone, $nome]);
        
        echo json_encode(['success' => true, 'message' => 'Contato criado com sucesso']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}

function atualizarContato($connect, $cod_id) {
    try {
        $id = intval($_POST['id'] ?? 0);
        $nome = trim($_POST['nome'] ?? '');
        $telefone = preg_replace('/[^0-9]/', '', $_POST['telefone'] ?? '');
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }
        
        $stmt = $connect->prepare("UPDATE contatos_whatsapp SET nome = ?, telefone = ?, atualizado_em = NOW() WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$nome, $telefone, $id, $cod_id]);
        
        echo json_encode(['success' => true, 'message' => 'Contato atualizado']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}

function excluirContato($connect, $cod_id) {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }
        
        $stmt = $connect->prepare("DELETE FROM contatos_whatsapp WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$id, $cod_id]);
        
        echo json_encode(['success' => true, 'message' => 'Contato excluído']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}
?>
