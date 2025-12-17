<?php
session_start();

if (!isset($_SESSION['cod_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit;
}

$cod_id = $_SESSION['cod_id'];

require_once __DIR__ . '/../../db/Conexao.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json');

$acao = $_POST['acao'] ?? $_GET['acao'] ?? '';

switch ($acao) {
    case 'criar':
    case 'atualizar':
        salvarCampanha($connect, $cod_id, $acao);
        break;
    
    case 'excluir':
        excluirCampanha($connect, $cod_id);
        break;
    
    case 'pausar':
        pausarCampanha($connect, $cod_id);
        break;
    
    case 'retomar':
        retomarCampanha($connect, $cod_id);
        break;
    
    case 'iniciar':
        iniciarCampanha($connect, $cod_id);
        break;
        
    case 'status':
        statusCampanha($connect, $cod_id);
        break;
        
    case 'importar_contatos':
        importarContatosWhatsApp($connect, $cod_id);
        break;
    
    default:
        echo json_encode(['success' => false, 'message' => 'Ação não reconhecida']);
}

function salvarCampanha($connect, $cod_id, $acao) {
    try {
        $campanha_id = intval($_POST['campanha_id'] ?? 0);
        $contatos = $_POST['contatos'] ?? [];
        $mensagem = trim($_POST['mensagem'] ?? '');
        $modo_envio = $_POST['modo_envio'] ?? 'unica';
        $instancia_id = $_POST['instancia_id'] ?? null;
        $instancia_nome = $_POST['instancia_nome'] ?? null;
        $delay_segundos = max(60, intval($_POST['delay_segundos'] ?? 60));
        $data_disparo = $_POST['data_disparo'] ?? null;
        $hora_disparo = $_POST['hora_disparo'] ?? null;
        
        // Validações
        if (empty($contatos)) {
            echo json_encode(['success' => false, 'message' => 'Selecione pelo menos um contato']);
            return;
        }
        
        if (count($contatos) > 50) {
            echo json_encode(['success' => false, 'message' => 'Máximo de 50 contatos por campanha']);
            return;
        }
        
        if (empty($mensagem)) {
            echo json_encode(['success' => false, 'message' => 'Digite uma mensagem']);
            return;
        }
        
        // Verificar limite de campanhas (se for nova)
        if ($acao === 'criar') {
            $stmtCount = $connect->prepare("SELECT COUNT(*) FROM campanhas WHERE id_usuario = ? AND status != 'cancelada'");
            $stmtCount->execute([$cod_id]);
            $count = $stmtCount->fetchColumn();
            if ($count >= 3) {
                echo json_encode(['success' => false, 'message' => 'Limite de 3 campanhas atingido']);
                return;
            }
        }
        
        // Processar upload de arquivo
        $arquivo_media = null;
        $arquivo_nome = null;
        $tipo_mensagem = 'texto';
        
        if (isset($_FILES['arquivo_media']) && $_FILES['arquivo_media']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['arquivo_media'];
            $maxSize = 10 * 1024 * 1024; // 10MB
            
            if ($file['size'] > $maxSize) {
                echo json_encode(['success' => false, 'message' => 'Arquivo muito grande (máx 10MB)']);
                return;
            }
            
            $allowedTypes = [
                'image/jpeg', 'image/png', 'image/gif', 'image/webp',
                'video/mp4', 'video/webm', 'video/mpeg',
                'audio/mpeg', 'audio/wav', 'audio/ogg',
                'application/pdf',
                'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'text/csv'
            ];
            
            if (!in_array($file['type'], $allowedTypes)) {
                echo json_encode(['success' => false, 'message' => 'Tipo de arquivo não permitido']);
                return;
            }
            
            // Determinar tipo
            if (strpos($file['type'], 'image/') === 0) $tipo_mensagem = 'imagem';
            elseif (strpos($file['type'], 'video/') === 0) $tipo_mensagem = 'video';
            elseif (strpos($file['type'], 'audio/') === 0) $tipo_mensagem = 'audio';
            else $tipo_mensagem = 'documento';
            
            // Salvar arquivo
            $uploadDir = __DIR__ . '/../../uploads/campanhas/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $novoNome = uniqid('campanha_') . '.' . $ext;
            $destino = $uploadDir . $novoNome;
            
            if (move_uploaded_file($file['tmp_name'], $destino)) {
                $arquivo_media = '/uploads/campanhas/' . $novoNome;
                $arquivo_nome = $file['name'];
            }
        }
        
        // Montar data de agendamento
        $data_agendamento = null;
        if ($data_disparo && $hora_disparo) {
            $data_agendamento = $data_disparo . ' ' . $hora_disparo . ':00';
        }
        
        // Determinar status
        $status = $data_agendamento ? 'agendada' : 'rascunho';
        
        $connect->beginTransaction();
        
        if ($acao === 'criar') {
            // Inserir campanha
            $stmt = $connect->prepare("
                INSERT INTO campanhas (
                    id_usuario, nome, mensagem, tipo_mensagem, arquivo_media, arquivo_nome,
                    instancia_id, instancia_nome, modo_envio, delay_segundos, data_agendamento,
                    status, total_contatos
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $nome = 'Campanha ' . date('d/m/Y H:i');
            $stmt->execute([
                $cod_id, $nome, $mensagem, $tipo_mensagem, $arquivo_media, $arquivo_nome,
                $instancia_id, $instancia_nome, $modo_envio, $delay_segundos, $data_agendamento,
                $status, count($contatos)
            ]);
            
            $campanha_id = $connect->lastInsertId();
        } else {
            // Atualizar campanha existente
            $sql = "UPDATE campanhas SET 
                    mensagem = ?, tipo_mensagem = ?, modo_envio = ?, 
                    instancia_id = ?, instancia_nome = ?, delay_segundos = ?, 
                    data_agendamento = ?, status = ?, total_contatos = ?, atualizado_em = NOW()";
            
            $params = [$mensagem, $tipo_mensagem, $modo_envio, $instancia_id, $instancia_nome, 
                      $delay_segundos, $data_agendamento, $status, count($contatos)];
            
            if ($arquivo_media) {
                $sql .= ", arquivo_media = ?, arquivo_nome = ?";
                $params[] = $arquivo_media;
                $params[] = $arquivo_nome;
            }
            
            $sql .= " WHERE id = ? AND id_usuario = ?";
            $params[] = $campanha_id;
            $params[] = $cod_id;
            
            $stmt = $connect->prepare($sql);
            $stmt->execute($params);
        }
        
        // Inserir contatos
        // Primeiro, remover contatos não enviados anteriores (para edição)
        $connect->prepare("DELETE FROM campanha_contatos WHERE campanha_id = ? AND status_envio = 'pendente'")
                ->execute([$campanha_id]);
        
        // Inserir novos contatos
        $stmtContato = $connect->prepare("
            INSERT IGNORE INTO campanha_contatos (campanha_id, telefone, nome, status_envio) 
            VALUES (?, ?, ?, 'pendente')
        ");
        
        foreach ($contatos as $telefone) {
            $telefone = preg_replace('/[^0-9]/', '', $telefone);
            
            // Buscar nome do contato
            $nome = '';
            $stmtNome = $connect->prepare("SELECT nome FROM contatos_whatsapp WHERE telefone = ? AND id_usuario = ? LIMIT 1");
            $stmtNome->execute([$telefone, $cod_id]);
            $contatoNome = $stmtNome->fetch(PDO::FETCH_OBJ);
            
            if (!$contatoNome) {
                $stmtNome = $connect->prepare("SELECT nome FROM clientes WHERE celular LIKE ? AND idm = ? LIMIT 1");
                $stmtNome->execute(['%' . $telefone . '%', $cod_id]);
                $contatoNome = $stmtNome->fetch(PDO::FETCH_OBJ);
            }
            
            if ($contatoNome) $nome = $contatoNome->nome;
            
            $stmtContato->execute([$campanha_id, $telefone, $nome]);
        }
        
        // Atualizar contador
        $totalContatos = $connect->query("SELECT COUNT(*) FROM campanha_contatos WHERE campanha_id = $campanha_id")->fetchColumn();
        $connect->prepare("UPDATE campanhas SET total_contatos = ? WHERE id = ?")->execute([$totalContatos, $campanha_id]);
        
        $connect->commit();
        
        $msg = $acao === 'criar' ? 'Campanha criada com sucesso!' : 'Campanha atualizada com sucesso!';
        echo json_encode(['success' => true, 'message' => $msg, 'campanha_id' => $campanha_id]);
        
    } catch (Exception $e) {
        if ($connect->inTransaction()) {
            $connect->rollBack();
        }
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}

function excluirCampanha($connect, $cod_id) {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        if ($id <= 0) {
            echo json_encode(['success' => false, 'message' => 'ID inválido']);
            return;
        }
        
        // Verificar se pertence ao usuário
        $stmt = $connect->prepare("SELECT * FROM campanhas WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$id, $cod_id]);
        $campanha = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$campanha) {
            echo json_encode(['success' => false, 'message' => 'Campanha não encontrada']);
            return;
        }
        
        // Deletar arquivo de mídia se existir
        if ($campanha->arquivo_media) {
            $arquivo = __DIR__ . '/../..' . $campanha->arquivo_media;
            if (file_exists($arquivo)) {
                unlink($arquivo);
            }
        }
        
        // Deletar contatos
        $connect->prepare("DELETE FROM campanha_contatos WHERE campanha_id = ?")->execute([$id]);
        
        // Deletar logs
        $connect->prepare("DELETE FROM campanha_logs WHERE campanha_id = ?")->execute([$id]);
        
        // Deletar campanha
        $connect->prepare("DELETE FROM campanhas WHERE id = ?")->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Campanha excluída com sucesso']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}

function pausarCampanha($connect, $cod_id) {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        $stmt = $connect->prepare("UPDATE campanhas SET status = 'pausada', atualizado_em = NOW() WHERE id = ? AND id_usuario = ? AND status = 'em_andamento'");
        $stmt->execute([$id, $cod_id]);
        
        echo json_encode(['success' => true, 'message' => 'Campanha pausada']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}

function retomarCampanha($connect, $cod_id) {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        $stmt = $connect->prepare("UPDATE campanhas SET status = 'em_andamento', atualizado_em = NOW() WHERE id = ? AND id_usuario = ? AND status = 'pausada'");
        $stmt->execute([$id, $cod_id]);
        
        echo json_encode(['success' => true, 'message' => 'Campanha retomada']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}

function iniciarCampanha($connect, $cod_id) {
    try {
        $id = intval($_POST['id'] ?? 0);
        
        $stmt = $connect->prepare("UPDATE campanhas SET status = 'em_andamento', iniciado_em = NOW(), atualizado_em = NOW() WHERE id = ? AND id_usuario = ? AND status IN ('agendada', 'rascunho')");
        $stmt->execute([$id, $cod_id]);
        
        // Aqui você pode disparar o job de processamento em background
        // Por exemplo, usando uma fila ou um cron job
        
        echo json_encode(['success' => true, 'message' => 'Campanha iniciada']);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}

function statusCampanha($connect, $cod_id) {
    try {
        $id = intval($_GET['id'] ?? 0);
        
        $stmt = $connect->prepare("SELECT * FROM campanhas WHERE id = ? AND id_usuario = ?");
        $stmt->execute([$id, $cod_id]);
        $campanha = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$campanha) {
            echo json_encode(['success' => false, 'message' => 'Campanha não encontrada']);
            return;
        }
        
        // Buscar contatos
        $stmtContatos = $connect->prepare("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status_envio = 'enviado' OR status_envio = 'entregue' OR status_envio = 'lido' THEN 1 ELSE 0 END) as enviados,
                SUM(CASE WHEN status_envio = 'falha' THEN 1 ELSE 0 END) as falhas,
                SUM(CASE WHEN status_envio = 'pendente' THEN 1 ELSE 0 END) as pendentes
            FROM campanha_contatos WHERE campanha_id = ?
        ");
        $stmtContatos->execute([$id]);
        $stats = $stmtContatos->fetch(PDO::FETCH_OBJ);
        
        // Buscar últimos logs
        $stmtLogs = $connect->prepare("SELECT * FROM campanha_logs WHERE campanha_id = ? ORDER BY criado_em DESC LIMIT 20");
        $stmtLogs->execute([$id]);
        $logs = $stmtLogs->fetchAll(PDO::FETCH_OBJ);
        
        echo json_encode([
            'success' => true,
            'campanha' => $campanha,
            'stats' => $stats,
            'logs' => $logs
        ]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}

function importarContatosWhatsApp($connect, $cod_id) {
    try {
        // Buscar dados da API Evolution
        $stmt = $connect->prepare("SELECT tokenapi FROM carteira WHERE Id = ?");
        $stmt->execute([$cod_id]);
        $user = $stmt->fetch(PDO::FETCH_OBJ);
        
        if (!$user || !$user->tokenapi) {
            echo json_encode(['success' => false, 'message' => 'Token API não configurado']);
            return;
        }
        
        // URL da API Evolution (buscar do functions.php ou config)
        $urlapi = "http://whatsapp.painelcontrole.xyz:8080";
        $apikey = $user->tokenapi;
        $instanceName = 'AbC123' . $apikey;
        
        // Buscar chats/contatos da API Evolution
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $urlapi . '/chat/findChats/' . $instanceName,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => ['apikey: ' . $apikey],
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        
        if ($httpCode !== 200) {
            echo json_encode(['success' => false, 'message' => 'Erro ao conectar com Evolution API']);
            return;
        }
        
        $chats = json_decode($response, true);
        
        if (!is_array($chats)) {
            echo json_encode(['success' => false, 'message' => 'Resposta inválida da API']);
            return;
        }
        
        $importados = 0;
        $stmtInsert = $connect->prepare("
            INSERT INTO contatos_whatsapp (id_usuario, telefone, nome, nome_push, origem, ultima_mensagem)
            VALUES (?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE nome_push = VALUES(nome_push), ultima_mensagem = NOW()
        ");
        
        foreach ($chats as $chat) {
            // Ignorar grupos
            if (isset($chat['id']) && strpos($chat['id'], '@g.us') !== false) {
                continue;
            }
            
            $telefone = preg_replace('/[^0-9]/', '', $chat['id'] ?? '');
            $nome = $chat['name'] ?? $chat['pushName'] ?? '';
            $nomePush = $chat['pushName'] ?? '';
            $origem = isset($chat['isContact']) && $chat['isContact'] ? 'salvo' : 'conversa';
            
            if (!empty($telefone)) {
                $stmtInsert->execute([$cod_id, $telefone, $nome, $nomePush, $origem]);
                $importados++;
            }
        }
        
        echo json_encode(['success' => true, 'message' => "$importados contatos importados/atualizados"]);
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Erro: ' . $e->getMessage()]);
    }
}
?>
