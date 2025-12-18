<?php
session_start();
require_once __DIR__ . '/../../db/Conexao.php';

header('Content-Type: application/json');

if (!isset($_SESSION['cod_id'])) {
    echo json_encode(['connected' => false, 'message' => 'Não autenticado']);
    exit;
}

$cod_id = $_SESSION['cod_id'];
$user_id = $_GET['user_id'] ?? $cod_id;

try {
    // Get user data
    $query = $connect->prepare("SELECT * FROM carteira WHERE Id = ?");
    $query->execute([$cod_id]);
    $dadosgerais = $query->fetch(PDO::FETCH_OBJ);
    
    // Get connection info
    $statuscon = $connect->prepare("SELECT * FROM conexoes WHERE id_usuario = ?");
    $statuscon->execute([$cod_id]);
    $dadoscon = $statuscon->fetch(PDO::FETCH_OBJ);
    
    if (!$dadoscon || !$dadoscon->instance_name) {
        echo json_encode(['connected' => false]);
        exit;
    }
    
    // Evolution API Configuration
    $urlapi = "http://whatsapp.painelcontrole.xyz:8080";
    $apikey = $dadosgerais->tokenapi ?? "4FAf4CAnP4jKtbhp6guW1HVbDAhgLmQxO";
    
    // Check instance status
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $urlapi . '/instance/connectionState/' . $dadoscon->instance_name,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $apikey
        ]
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($http_code === 200) {
        $status_data = json_decode($response, true);
        $is_connected = isset($status_data['state']) && $status_data['state'] === 'open';
        
        // Update database
        $stmt = $connect->prepare("UPDATE conexoes SET conn = :conn WHERE id_usuario = :id");
        $stmt->execute([
            ':conn' => $is_connected ? 1 : 0,
            ':id' => $cod_id
        ]);
        
        echo json_encode([
            'connected' => $is_connected,
            'state' => $status_data['state'] ?? 'unknown'
        ]);
    } else {
        echo json_encode(['connected' => false, 'error' => 'Erro ao verificar status']);
    }
} catch (Exception $e) {
    echo json_encode(['connected' => false, 'error' => $e->getMessage()]);
}
?>
