<?php
// /www/wwwroot/financeiro.painelcontrole.xyz/master/gera_qr.php

// Incluir configuração
require_once __DIR__ . '/../config.php';

// Obter conexão
$connect = getConnection();

// Verificar se usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../index.php");
    exit;
}

$cod_id = $_SESSION['usuario_id'];

// Buscar token da API
$stmt = $connect->prepare("SELECT tokenapi FROM configuracoes WHERE id_usuario = :id");
$stmt->execute([':id' => $cod_id]);
$config = $stmt->fetch();

$token_api = $config->tokenapi ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $celular = $_POST['celular'] ?? '';
    
    // Nome único para a instância
    $instance_name = 'inst_' . $cod_id . '_' . md5(time());
    
    // 1. Criar instância na API Evolution
    $curl = curl_init();
    
    curl_setopt_array($curl, [
        CURLOPT_URL => API_URL . '/instance/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'apikey: ' . $token_api
        ],
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'instanceName' => $instance_name,
            'qrcode' => true,
            'webhook' => 'https://financeiro.painelcontrole.xyz/master/webhook.php',
            'webhook_by_events' => false
        ])
    ]);
    
    $response = curl_exec($curl);
    $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
    if ($http_code === 201) {
        // 2. Obter QR Code
        curl_setopt_array($curl, [
            CURLOPT_URL => API_URL . '/instance/connect/' . $instance_name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . $token_api
            ]
        ]);
        
        $qr_response = curl_exec($curl);
        $qr_data = json_decode($qr_response, true);
        
        if (isset($qr_data['code']) && $qr_data['code'] === 'qrCode') {
            // Salvar QR Code na sessão
            $_SESSION['qr_code'] = $qr_data['base64'];
            $_SESSION['instance_name'] = $instance_name;
            $_SESSION['token_api'] = $token_api;
            
            // Salvar no banco
            $stmt = $connect->prepare("
                INSERT INTO conexoes (id_usuario, instance_name, conn, qr_code, data_conexao)
                VALUES (:id, :instance, 1, :qr, NOW())
                ON DUPLICATE KEY UPDATE 
                instance_name = VALUES(instance_name),
                conn = VALUES(conn),
                qr_code = VALUES(qr_code),
                data_conexao = VALUES(data_conexao)
            ");
            
            $stmt->execute([
                ':id' => $cod_id,
                ':instance' => $instance_name,
                ':qr' => $qr_data['base64']
            ]);
            
            header("Location: whatsapp.php?qr=1");
            exit;
        } else {
            header("Location: whatsapp.php?erro=qr");
            exit;
        }
    } else {
        header("Location: whatsapp.php?erro=api&code=" . $http_code);
        exit;
    }
    
    curl_close($curl);
} else {
    header("Location: whatsapp.php");
    exit;
}
?>