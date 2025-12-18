<?php
session_start();
require_once __DIR__ . '/../../db/Conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cod_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado']);
    exit;
}

$cod_id = $_SESSION['cod_id'];
$input = json_decode(file_get_contents('php://input'), true);

try {
    // Delete all contacts for this user
    $stmt = $connect->prepare("DELETE FROM whatsapp_contacts WHERE id_usuario = :id");
    $stmt->execute([':id' => $cod_id]);
    
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
