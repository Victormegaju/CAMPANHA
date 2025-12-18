<?php
require_once "topo.php";

// Evolution API Configuration
$urlapi = "http://whatsapp.painelcontrole.xyz:8080";
$apikey = $dadosgerais->tokenapi ?? "4FAf4CAnP4jKtbhp6guW1HVbDAhgLmQxO";

// Check connection status
$statuscon = $connect->query("SELECT * FROM conexoes WHERE id_usuario = '$cod_id'");
$dadoscon = $statuscon->fetch(PDO::FETCH_OBJ);

// Get captured contacts
$contacts_query = $connect->query("SELECT * FROM whatsapp_contacts WHERE id_usuario = '$cod_id' ORDER BY data_captura DESC");
$contacts = $contacts_query->fetchAll(PDO::FETCH_OBJ);

// Count contacts by source
$count_contacts = $connect->query("SELECT COUNT(*) as total FROM whatsapp_contacts WHERE id_usuario = '$cod_id' AND source = 'contacts'")->fetch(PDO::FETCH_OBJ)->total;
$count_chats = $connect->query("SELECT COUNT(*) as total FROM whatsapp_contacts WHERE id_usuario = '$cod_id' AND source = 'chats'")->fetch(PDO::FETCH_OBJ)->total;
$count_total = count($contacts);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captura de Contatos WhatsApp</title>
    
    <style>
        .contacts-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .status-card {
            background: #fff;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
        .status-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 25px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f2f5;
        }
        
        .status-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }
        
        .status-badge {
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .status-badge.connected {
            background: #dcfce7;
            color: #16a34a;
        }
        
        .status-badge.disconnected {
            background: #fee2e2;
            color: #dc2626;
        }
        
        .qr-container {
            text-align: center;
            padding: 30px;
            background: #f8f9fa;
            border-radius: 10px;
            margin: 20px 0;
        }
        
        .qr-container img {
            max-width: 300px;
            border: 3px solid #00a884;
            border-radius: 10px;
            padding: 10px;
            background: white;
        }
        
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .btn-action {
            padding: 15px 25px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 1rem;
        }
        
        .btn-connect {
            background: linear-gradient(135deg, #00a884, #008c6a);
            color: white;
            box-shadow: 0 4px 15px rgba(0, 168, 132, 0.3);
        }
        
        .btn-connect:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 168, 132, 0.4);
        }
        
        .btn-capture {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        
        .btn-capture:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }
        
        .btn-disconnect {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.3);
        }
        
        .btn-disconnect:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
        }
        
        .btn-download {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            box-shadow: 0 4px 15px rgba(139, 92, 246, 0.3);
        }
        
        .btn-download:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(139, 92, 246, 0.4);
        }
        
        .btn-action:disabled {
            background: #e5e7eb;
            color: #9ca3af;
            cursor: not-allowed;
            box-shadow: none;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin: 25px 0;
        }
        
        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .stat-card:nth-child(2) {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }
        
        .stat-card:nth-child(3) {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stat-label {
            font-size: 0.9rem;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .contacts-table-container {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            overflow-x: auto;
        }
        
        .contacts-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .contacts-table thead {
            background: #f8f9fa;
        }
        
        .contacts-table th {
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #64748b;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }
        
        .contacts-table td {
            padding: 15px;
            border-bottom: 1px solid #f1f5f9;
            color: #334155;
        }
        
        .contacts-table tr:hover {
            background: #f8fafc;
        }
        
        .source-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
        }
        
        .source-contacts {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .source-chats {
            background: #fce7f3;
            color: #be185d;
        }
        
        .loading-spinner {
            border: 3px solid #f3f4f6;
            border-top: 3px solid #00a884;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .alert-custom {
            padding: 15px 20px;
            border-radius: 10px;
            margin: 20px 0;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
        }
        
        .alert-success {
            background: #dcfce7;
            color: #16a34a;
            border-left: 4px solid #16a34a;
        }
        
        .alert-error {
            background: #fee2e2;
            color: #dc2626;
            border-left: 4px solid #dc2626;
        }
        
        .alert-info {
            background: #dbeafe;
            color: #2563eb;
            border-left: 4px solid #2563eb;
        }
    </style>
</head>
<body>

<div class="slim-mainpanel">
    <div class="contacts-container">
        
        <!-- Page Header -->
        <div style="margin-bottom: 30px;">
            <a href="./" style="color: #64748b; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fa fa-arrow-left"></i> Voltar ao Painel
            </a>
        </div>
        
        <!-- Alert Messages -->
        <div id="alertContainer"></div>
        
        <!-- Status Card -->
        <div class="status-card">
            <div class="status-header">
                <h1 class="status-title">
                    <i class="fa fa-whatsapp" style="color: #00a884;"></i>
                    Captura de Contatos WhatsApp
                </h1>
                <span class="status-badge <?php echo ($dadoscon && $dadoscon->conn == 1) ? 'connected' : 'disconnected'; ?>">
                    <i class="fa fa-<?php echo ($dadoscon && $dadoscon->conn == 1) ? 'check-circle' : 'times-circle'; ?>"></i>
                    <?php echo ($dadoscon && $dadoscon->conn == 1) ? 'Conectado' : 'Desconectado'; ?>
                </span>
            </div>
            
            <?php if (!$dadoscon || $dadoscon->conn == 0): ?>
                <!-- Disconnected State -->
                <div class="qr-container" id="qrContainer" style="display: none;">
                    <h3 style="color: #1e293b; margin-bottom: 15px;">Escaneie o QR Code</h3>
                    <p style="color: #64748b; margin-bottom: 20px;">Abra o WhatsApp no seu celular e escaneie este código</p>
                    <img id="qrImage" src="" alt="QR Code" />
                </div>
                
                <div class="action-buttons">
                    <button class="btn-action btn-connect" onclick="conectarWhatsApp()" id="btnConnect">
                        <i class="fa fa-qrcode"></i>
                        Gerar QR Code
                    </button>
                </div>
            <?php else: ?>
                <!-- Connected State -->
                <div class="alert-custom alert-success">
                    <i class="fa fa-check-circle" style="font-size: 1.5rem;"></i>
                    <span>WhatsApp conectado! Agora você pode capturar os contatos.</span>
                </div>
                
                <div class="action-buttons">
                    <button class="btn-action btn-capture" onclick="capturarContatos()" id="btnCaptureContacts">
                        <i class="fa fa-address-book"></i>
                        Capturar da Lista de Contatos
                    </button>
                    <button class="btn-action btn-capture" onclick="capturarConversas()" id="btnCaptureChats">
                        <i class="fa fa-comments"></i>
                        Capturar das Conversas
                    </button>
                    <button class="btn-action btn-disconnect" onclick="desconectarWhatsApp()">
                        <i class="fa fa-power-off"></i>
                        Desconectar
                    </button>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Statistics -->
        <?php if ($count_total > 0): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-label">Total de Contatos</div>
                <div class="stat-number"><?php echo $count_total; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Da Lista</div>
                <div class="stat-number"><?php echo $count_contacts; ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-label">Das Conversas</div>
                <div class="stat-number"><?php echo $count_chats; ?></div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Contacts Table -->
        <?php if ($count_total > 0): ?>
        <div class="contacts-table-container">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="margin: 0; color: #1e293b; font-size: 1.5rem;">
                    <i class="fa fa-list"></i> Contatos Capturados
                </h2>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-action btn-download" onclick="baixarCSV()" style="padding: 10px 20px; font-size: 0.9rem;">
                        <i class="fa fa-file-csv"></i>
                        Baixar CSV
                    </button>
                    <button class="btn-action btn-download" onclick="limparContatos()" style="padding: 10px 20px; font-size: 0.9rem; background: linear-gradient(135deg, #f59e0b, #d97706);">
                        <i class="fa fa-trash"></i>
                        Limpar Lista
                    </button>
                </div>
            </div>
            
            <table class="contacts-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Número</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Origem</th>
                        <th>Data de Captura</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $index = 1;
                    foreach($contacts as $contact): 
                    ?>
                    <tr>
                        <td><?php echo $index++; ?></td>
                        <td><?php echo htmlspecialchars($contact->contact_number); ?></td>
                        <td><?php echo htmlspecialchars($contact->contact_name ?? 'N/A'); ?></td>
                        <td><?php echo $contact->is_group ? 'Grupo' : 'Contato'; ?></td>
                        <td>
                            <span class="source-badge source-<?php echo $contact->source; ?>">
                                <?php echo $contact->source == 'contacts' ? 'Lista' : 'Conversas'; ?>
                            </span>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($contact->data_captura)); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
        
    </div>
</div>

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>

<script>
const API_URL = '<?php echo $urlapi; ?>';
const API_KEY = '<?php echo $apikey; ?>';
const USER_ID = <?php echo $cod_id; ?>;
const INSTANCE_NAME = '<?php echo $dadoscon->instance_name ?? ""; ?>';

function showAlert(message, type = 'info') {
    const alertContainer = document.getElementById('alertContainer');
    const alertHTML = `
        <div class="alert-custom alert-${type}">
            <i class="fa fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}" style="font-size: 1.2rem;"></i>
            <span>${message}</span>
        </div>
    `;
    alertContainer.innerHTML = alertHTML;
    
    setTimeout(() => {
        alertContainer.innerHTML = '';
    }, 5000);
}

async function conectarWhatsApp() {
    const btn = document.getElementById('btnConnect');
    btn.disabled = true;
    btn.innerHTML = '<div class="loading-spinner" style="width: 20px; height: 20px; margin: 0 auto;"></div> Conectando...';
    
    try {
        const response = await fetch('ajax/whatsapp_connect.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'connect',
                user_id: USER_ID
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Conexão iniciada! Escaneie o QR Code.', 'success');
            
            if (data.qrcode) {
                document.getElementById('qrImage').src = data.qrcode;
                document.getElementById('qrContainer').style.display = 'block';
                
                // Poll for connection status
                checkConnectionStatus();
            }
        } else {
            showAlert('Erro ao conectar: ' + data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-qrcode"></i> Gerar QR Code';
        }
    } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao processar requisição', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-qrcode"></i> Gerar QR Code';
    }
}

function checkConnectionStatus() {
    const interval = setInterval(async () => {
        try {
            const response = await fetch('ajax/whatsapp_status.php?user_id=' + USER_ID);
            const data = await response.json();
            
            if (data.connected) {
                clearInterval(interval);
                showAlert('WhatsApp conectado com sucesso!', 'success');
                setTimeout(() => {
                    location.reload();
                }, 2000);
            }
        } catch (error) {
            console.error('Erro ao verificar status:', error);
        }
    }, 3000);
    
    // Stop checking after 2 minutes
    setTimeout(() => {
        clearInterval(interval);
    }, 120000);
}

async function capturarContatos() {
    const btn = document.getElementById('btnCaptureContacts');
    btn.disabled = true;
    btn.innerHTML = '<div class="loading-spinner" style="width: 20px; height: 20px; margin: 0 auto;"></div> Capturando...';
    
    try {
        const response = await fetch('ajax/whatsapp_capture.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'capture_contacts',
                user_id: USER_ID,
                instance_name: INSTANCE_NAME
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(`${data.count} contatos capturados com sucesso!`, 'success');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('Erro ao capturar contatos: ' + data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-address-book"></i> Capturar da Lista de Contatos';
        }
    } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao processar requisição', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-address-book"></i> Capturar da Lista de Contatos';
    }
}

async function capturarConversas() {
    const btn = document.getElementById('btnCaptureChats');
    btn.disabled = true;
    btn.innerHTML = '<div class="loading-spinner" style="width: 20px; height: 20px; margin: 0 auto;"></div> Capturando...';
    
    try {
        const response = await fetch('ajax/whatsapp_capture.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                action: 'capture_chats',
                user_id: USER_ID,
                instance_name: INSTANCE_NAME
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert(`${data.count} contatos das conversas capturados com sucesso!`, 'success');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('Erro ao capturar conversas: ' + data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-comments"></i> Capturar das Conversas';
        }
    } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao processar requisição', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fa fa-comments"></i> Capturar das Conversas';
    }
}

async function desconectarWhatsApp() {
    if (!confirm('Tem certeza que deseja desconectar o WhatsApp?')) {
        return;
    }
    
    try {
        const response = await fetch('ajax/whatsapp_disconnect.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: USER_ID,
                instance_name: INSTANCE_NAME
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('WhatsApp desconectado com sucesso!', 'success');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('Erro ao desconectar: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao processar requisição', 'error');
    }
}

function baixarCSV() {
    window.location.href = 'ajax/download_contacts.php?user_id=' + USER_ID + '&format=csv';
}

async function limparContatos() {
    if (!confirm('Tem certeza que deseja limpar todos os contatos capturados?')) {
        return;
    }
    
    try {
        const response = await fetch('ajax/clear_contacts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                user_id: USER_ID
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('Contatos limpos com sucesso!', 'success');
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('Erro ao limpar contatos: ' + data.message, 'error');
        }
    } catch (error) {
        console.error('Erro:', error);
        showAlert('Erro ao processar requisição', 'error');
    }
}
</script>

</body>
</html>

<?php
ob_end_flush();
?>
