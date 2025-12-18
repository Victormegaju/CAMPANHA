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

if (!isset($input['instance_name'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Get user data
$query = $connect->query("SELECT * FROM carteira WHERE Id = '$cod_id'");
$dadosgerais = $query->fetch(PDO::FETCH_OBJ);

// Evolution API Configuration
$urlapi = "http://whatsapp.painelcontrole.xyz:8080";
$apikey = $dadosgerais->tokenapi ?? "4FAf4CAnP4jKtbhp6guW1HVbDAhgLmQxO";

try {
    $instance_name = $input['instance_name'];
    
    // Logout instance
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $urlapi . '/instance/logout/' . $instance_name,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $apikey
        ]
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200 || $http_code === 204) {
        // Update database
        $stmt = $connect->prepare("UPDATE conexoes SET conn = 0, qr_code = NULL WHERE id_usuario = :id");
        $stmt->execute([':id' => $cod_id]);
        
        echo json_encode(['success' => true]);
    } else {
        // Even if API fails, update local status
        $stmt = $connect->prepare("UPDATE conexoes SET conn = 0, qr_code = NULL WHERE id_usuario = :id");
        $stmt->execute([':id' => $cod_id]);
        
        echo json_encode(['success' => true, 'warning' => 'Desconectado localmente']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
