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

if (!isset($input['action']) || !isset($input['instance_name'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit;
}

// Get user data
$query = $connect->prepare("SELECT * FROM carteira WHERE Id = ?");
$query->execute([$cod_id]);
$dadosgerais = $query->fetch(PDO::FETCH_OBJ);

// Evolution API Configuration
require_once __DIR__ . '/../config_evolution.php';
$evolutionConfig = getEvolutionConfig($dadosgerais);
$urlapi = $evolutionConfig['url'];
$apikey = $evolutionConfig['key'];

try {
    $instance_name = $input['instance_name'];
    $action = $input['action'];
    
    if ($action === 'capture_contacts') {
        // Fetch contacts from WhatsApp
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $urlapi . '/chat/findContacts/' . $instance_name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . $apikey
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            $contacts_data = json_decode($response, true);
            $count = 0;
            
            if (is_array($contacts_data)) {
                foreach ($contacts_data as $contact) {
                    if (isset($contact['id'])) {
                        $number = $contact['id'];
                        $name = $contact['pushName'] ?? $contact['name'] ?? 'N/A';
                        $is_group = isset($contact['isGroup']) ? ($contact['isGroup'] ? 1 : 0) : 0;
                        
                        // Check if contact already exists
                        $check = $connect->prepare("SELECT id FROM whatsapp_contacts WHERE id_usuario = :id AND contact_number = :number AND source = 'contacts'");
                        $check->execute([':id' => $cod_id, ':number' => $number]);
                        
                        if ($check->rowCount() == 0) {
                            $stmt = $connect->prepare("
                                INSERT INTO whatsapp_contacts 
                                (id_usuario, instance_name, contact_number, contact_name, is_group, source, data_captura)
                                VALUES (:id, :instance, :number, :name, :is_group, 'contacts', NOW())
                            ");
                            
                            $stmt->execute([
                                ':id' => $cod_id,
                                ':instance' => $instance_name,
                                ':number' => $number,
                                ':name' => $name,
                                ':is_group' => $is_group
                            ]);
                            
                            $count++;
                        }
                    }
                }
            }
            
            echo json_encode(['success' => true, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar contatos: ' . $http_code]);
        }
    } 
    elseif ($action === 'capture_chats') {
        // Fetch chats from WhatsApp
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $urlapi . '/chat/findChats/' . $instance_name,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'apikey: ' . $apikey
            ]
        ]);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code === 200) {
            $chats_data = json_decode($response, true);
            $count = 0;
            
            if (is_array($chats_data)) {
                foreach ($chats_data as $chat) {
                    if (isset($chat['id'])) {
                        $number = $chat['id'];
                        $name = $chat['name'] ?? $chat['pushName'] ?? 'N/A';
                        $is_group = isset($chat['isGroup']) ? ($chat['isGroup'] ? 1 : 0) : 0;
                        
                        // Check if contact already exists
                        $check = $connect->prepare("SELECT id FROM whatsapp_contacts WHERE id_usuario = :id AND contact_number = :number AND source = 'chats'");
                        $check->execute([':id' => $cod_id, ':number' => $number]);
                        
                        if ($check->rowCount() == 0) {
                            $stmt = $connect->prepare("
                                INSERT INTO whatsapp_contacts 
                                (id_usuario, instance_name, contact_number, contact_name, is_group, source, data_captura)
                                VALUES (:id, :instance, :number, :name, :is_group, 'chats', NOW())
                            ");
                            
                            $stmt->execute([
                                ':id' => $cod_id,
                                ':instance' => $instance_name,
                                ':number' => $number,
                                ':name' => $name,
                                ':is_group' => $is_group
                            ]);
                            
                            $count++;
                        }
                    }
                }
            }
            
            echo json_encode(['success' => true, 'count' => $count]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao buscar conversas: ' . $http_code]);
        }
    }
    else {
        echo json_encode(['success' => false, 'message' => 'Ação inválida']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
