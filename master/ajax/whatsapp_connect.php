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

if (!isset($input['action']) || !isset($input['user_id'])) {
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
    if ($input['action'] === 'connect') {
        // Generate unique instance name
        $instance_name = 'inst_' . $cod_id . '_' . md5(time() . rand());
        
        // Create instance
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $urlapi . '/instance/create',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . $apikey
            ],
            CURLOPT_POSTFIELDS => json_encode([
                'instanceName' => $instance_name,
                'qrcode' => true
            ])
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 201 || $http_code === 200) {
            $response_data = json_decode($response, true);
            
            // Get QR Code
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $urlapi . '/instance/connect/' . $instance_name,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'apikey: ' . $apikey
                ]
            ]);
            
            $qr_response = curl_exec($ch);
            $qr_data = json_decode($qr_response, true);
            curl_close($ch);
            
            if (isset($qr_data['base64']) || isset($qr_data['code'])) {
                $qr_code = $qr_data['base64'] ?? $qr_data['code'] ?? '';
                
                // Save to database
                $stmt = $connect->prepare("
                    INSERT INTO conexoes (id_usuario, instance_name, conn, qr_code, data_conexao)
                    VALUES (:id, :instance, 0, :qr, NOW())
                    ON DUPLICATE KEY UPDATE 
                    instance_name = VALUES(instance_name),
                    qr_code = VALUES(qr_code),
                    data_conexao = VALUES(data_conexao)
                ");
                
                $stmt->execute([
                    ':id' => $cod_id,
                    ':instance' => $instance_name,
                    ':qr' => $qr_code
                ]);
                
                echo json_encode([
                    'success' => true,
                    'instance_name' => $instance_name,
                    'qrcode' => $qr_code
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'QR Code não gerado']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao criar instância: ' . $http_code]);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
