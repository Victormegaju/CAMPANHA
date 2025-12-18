<?php
/**
 * Processador de Campanhas em Background
 * Este script deve ser executado via cron job para processar campanhas agendadas
 * Exemplo de cron: * * * * * php /path/to/processar_campanha.php
 */

// Evitar timeout
set_time_limit(0);
ignore_user_abort(true);

require_once __DIR__ . '/../../db/Conexao.php';
require_once __DIR__ . '/functions.php';

// Configurações da API Evolution
$urlapi = "http://whatsapp.painelcontrole.xyz:8080";

// Buscar campanhas que devem ser iniciadas
$agora = date('Y-m-d H:i:s');
$campanhasParaIniciar = $connect->query("
    SELECT c.*, u.tokenapi 
    FROM campanhas c 
    JOIN carteira u ON c.id_usuario = u.Id
    WHERE c.status = 'agendada' 
    AND c.data_agendamento <= '$agora'
");

while ($campanha = $campanhasParaIniciar->fetch(PDO::FETCH_OBJ)) {
    // Marcar como em andamento
    $connect->prepare("UPDATE campanhas SET status = 'em_andamento', iniciado_em = NOW() WHERE id = ?")->execute([$campanha->id]);
    
    // Registrar log
    registrarLog($connect, $campanha->id, 'info', 'Campanha iniciada');
}

// Processar campanhas em andamento
$campanhasEmAndamento = $connect->query("
    SELECT c.*, u.tokenapi 
    FROM campanhas c 
    JOIN carteira u ON c.id_usuario = u.Id
    WHERE c.status = 'em_andamento'
");

while ($campanha = $campanhasEmAndamento->fetch(PDO::FETCH_OBJ)) {
    processarCampanha($connect, $campanha, $urlapi);
}

function processarCampanha($connect, $campanha, $urlapi) {
    // Buscar próximo contato pendente
    $stmt = $connect->prepare("
        SELECT * FROM campanha_contatos 
        WHERE campanha_id = ? AND status_envio = 'pendente' 
        ORDER BY id ASC 
        LIMIT 1
    ");
    $stmt->execute([$campanha->id]);
    $contato = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$contato) {
        // Não há mais contatos pendentes - finalizar campanha
        $connect->prepare("UPDATE campanhas SET status = 'concluida', finalizado_em = NOW() WHERE id = ?")
                ->execute([$campanha->id]);
        registrarLog($connect, $campanha->id, 'sucesso', 'Campanha concluída com sucesso');
        return;
    }
    
    // Verificar delay desde último envio
    $stmtUltimo = $connect->prepare("
        SELECT MAX(enviado_em) as ultimo FROM campanha_contatos 
        WHERE campanha_id = ? AND status_envio IN ('enviado', 'entregue', 'falha')
    ");
    $stmtUltimo->execute([$campanha->id]);
    $ultimoEnvio = $stmtUltimo->fetch(PDO::FETCH_OBJ);
    
    if ($ultimoEnvio && $ultimoEnvio->ultimo) {
        $ultimoTimestamp = strtotime($ultimoEnvio->ultimo);
        $diferenca = time() - $ultimoTimestamp;
        
        if ($diferenca < $campanha->delay_segundos) {
            // Ainda não passou o delay necessário
            return;
        }
    }
    
    // Preparar telefone
    $telefone = preg_replace('/[^0-9]/', '', $contato->telefone);
    if (strlen($telefone) < 10) {
        // Telefone inválido
        marcarFalha($connect, $contato->id, $campanha->id, 'Número inválido');
        return;
    }
    
    // Adicionar código do país se necessário
    if (strlen($telefone) == 10 || strlen($telefone) == 11) {
        $telefone = '55' . $telefone;
    }
    
    // Substituir variáveis na mensagem
    $mensagem = $campanha->mensagem;
    $mensagem = str_replace('#NOME#', $contato->nome ?? '', $mensagem);
    // Adicionar mais substituições conforme necessário
    
    // Nome da instância
    $instanceName = 'AbC123' . $campanha->tokenapi;
    
    // Enviar mensagem
    $resultado = enviarMensagem($urlapi, $campanha->tokenapi, $instanceName, $telefone, $mensagem, $campanha);
    
    if ($resultado['success']) {
        // Marcar como enviado
        $connect->prepare("
            UPDATE campanha_contatos 
            SET status_envio = 'enviado', enviado_em = NOW() 
            WHERE id = ?
        ")->execute([$contato->id]);
        
        // Atualizar contador da campanha
        $connect->prepare("UPDATE campanhas SET enviados = enviados + 1 WHERE id = ?")
                ->execute([$campanha->id]);
        
        registrarLog($connect, $campanha->id, 'sucesso', "Mensagem enviada para {$telefone}");
    } else {
        marcarFalha($connect, $contato->id, $campanha->id, $resultado['error']);
    }
}

function enviarMensagem($urlapi, $apikey, $instanceName, $telefone, $mensagem, $campanha) {
    try {
        $endpoint = '/message/sendText/' . $instanceName;
        
        $data = [
            'number' => $telefone,
            'text' => $mensagem
        ];
        
        // Se há mídia, usar endpoint apropriado
        if ($campanha->arquivo_media && $campanha->tipo_mensagem != 'texto') {
            // Use configured base URL from functions.php or environment
            $baseUrl = defined('APP_URL') ? APP_URL : 'https://whatsapp.painelcontrole.xyz';
            $mediaUrl = $baseUrl . $campanha->arquivo_media;
            
            switch ($campanha->tipo_mensagem) {
                case 'imagem':
                    $endpoint = '/message/sendMedia/' . $instanceName;
                    $data = [
                        'number' => $telefone,
                        'mediatype' => 'image',
                        'media' => $mediaUrl,
                        'caption' => $mensagem
                    ];
                    break;
                case 'video':
                    $endpoint = '/message/sendMedia/' . $instanceName;
                    $data = [
                        'number' => $telefone,
                        'mediatype' => 'video',
                        'media' => $mediaUrl,
                        'caption' => $mensagem
                    ];
                    break;
                case 'audio':
                    $endpoint = '/message/sendWhatsAppAudio/' . $instanceName;
                    $data = [
                        'number' => $telefone,
                        'audio' => $mediaUrl
                    ];
                    break;
                case 'documento':
                    $endpoint = '/message/sendMedia/' . $instanceName;
                    $data = [
                        'number' => $telefone,
                        'mediatype' => 'document',
                        'media' => $mediaUrl,
                        'fileName' => $campanha->arquivo_nome,
                        'caption' => $mensagem
                    ];
                    break;
            }
        }
        
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => $urlapi . $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'apikey: ' . $apikey
            ],
        ]);
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        
        if ($error) {
            return ['success' => false, 'error' => 'Erro de conexão: ' . $error];
        }
        
        $result = json_decode($response, true);
        
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true];
        } else {
            $errorMsg = $result['message'] ?? $result['error'] ?? 'Erro HTTP ' . $httpCode;
            return ['success' => false, 'error' => $errorMsg];
        }
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function marcarFalha($connect, $contatoId, $campanhaId, $erro) {
    $connect->prepare("
        UPDATE campanha_contatos 
        SET status_envio = 'falha', mensagem_erro = ?, enviado_em = NOW() 
        WHERE id = ?
    ")->execute([$erro, $contatoId]);
    
    $connect->prepare("UPDATE campanhas SET falhas = falhas + 1 WHERE id = ?")
            ->execute([$campanhaId]);
    
    registrarLog($connect, $campanhaId, 'erro', "Falha no envio: $erro");
}

function registrarLog($connect, $campanhaId, $tipo, $mensagem) {
    $connect->prepare("
        INSERT INTO campanha_logs (campanha_id, tipo, mensagem) 
        VALUES (?, ?, ?)
    ")->execute([$campanhaId, $tipo, $mensagem]);
}

echo "Processamento concluído: " . date('Y-m-d H:i:s') . "\n";
?>
