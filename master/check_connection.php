<?php
// classes/check_connection.php
session_start();
require_once "../config.php";

header('Content-Type: application/json');

if(isset($_SESSION['instance_name']) && isset($_SESSION['4FAf4CAnP4jKtbhp6guW1HVbDAhgLmQxO'])) {
    $api_url = 'http://whatsapp.painelcontrole.xyz:8080/manager';
    $instance_name = $_SESSION['Financeiro Siniclei'];
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $api_url . '/instance/connectionState/' . $instance_name,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'apikey: ' . $_SESSION['4FAf4CAnP4jKtbhp6guW1HVbDAhgLmQxO']
        ]
    ]);
    
    $response = curl_exec($ch);
    $connection_data = json_decode($response, true);
    
    if(isset($connection_data['state']) && $connection_data['state'] === 'open') {
        // WhatsApp conectado
        $stmt = $connect->prepare("UPDATE conexoes SET conn = 1 WHERE id_usuario = :id");
        $stmt->execute([':id' => $cod_id]);
        
        unset($_SESSION['qr_code']);
        
        echo json_encode(['connected' => true]);
    } else {
        echo json_encode(['connected' => false]);
    }
    
    curl_close($ch);
} else {
    echo json_encode(['connected' => false]);
}
?>