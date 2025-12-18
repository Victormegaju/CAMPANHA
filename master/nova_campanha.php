<?php
require_once "topo.php";
require_once "menu.php";
require_once "classes/functions.php";

// ===== VERIFICAÇÃO AUTOMÁTICA DE CONEXÃO COM EVOLUTION API =====
$idins = $dadosgerais->tokenapi;
$stmtConn = $connect->prepare("SELECT apikey, conn FROM conexoes WHERE id_usuario = ?");
$stmtConn->execute([$cod_id]);
$connData = $stmtConn->fetch(PDO::FETCH_OBJ);

$conexaoAtiva = false;
if ($connData && $connData->apikey) {
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlapi . '/instance/connectionState/AbC123' . $idins,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 5,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array('apikey: ' . $connData->apikey),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    
    $res = json_decode($response, true);
    $conexaoo = "";
    if (isset($res['instance']['state'])) {
        $conexaoo = $res['instance']['state'];
    } elseif (isset($res['state'])) {
        $conexaoo = $res['state'];
    }
    
    if ($conexaoo == 'open') {
        $conexaoAtiva = true;
        if ($connData->conn != 1) {
            $connect->prepare("UPDATE conexoes SET conn = 1 WHERE id_usuario = ?")->execute([$cod_id]);
        }
    } else {
        if ($connData->conn == 1) {
            $connect->prepare("UPDATE conexoes SET conn = 0 WHERE id_usuario = ?")->execute([$cod_id]);
        }
    }
} elseif ($connData && $connData->conn == 1) {
    $conexaoAtiva = true;
}
// ===== FIM DA VERIFICAÇÃO =====

// Verificar limite de campanhas
$stmtCount = $connect->prepare("SELECT COUNT(*) FROM campanhas WHERE id_usuario = ? AND status != 'cancelada'");
$stmtCount->execute([$cod_id]);
$countCampanhas = $stmtCount->fetchColumn();

// Verificar se é edição
$campanha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$campanha = null;
$contatosSelecionados = [];

if ($campanha_id > 0) {
    $stmt = $connect->prepare("SELECT * FROM campanhas WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$campanha_id, $cod_id]);
    $campanha = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($campanha) {
        // Buscar contatos já selecionados
        $stmtContatos = $connect->prepare("SELECT telefone FROM campanha_contatos WHERE campanha_id = ?");
        $stmtContatos->execute([$campanha_id]);
        while ($row = $stmtContatos->fetch(PDO::FETCH_ASSOC)) {
            $contatosSelecionados[] = $row['telefone'];
        }
    }
} elseif ($countCampanhas >= 3) {
    header('Location: campanhas?erro=Limite de 3 campanhas atingido');
    exit;
}

// Buscar contatos do usuário
$stmtContatos = $connect->prepare("SELECT * FROM contatos_whatsapp WHERE id_usuario = ? ORDER BY nome ASC");
$stmtContatos->execute([$cod_id]);
$contatos = $stmtContatos;

// Buscar clientes cadastrados
$stmtClientes = $connect->prepare("SELECT * FROM clientes WHERE idm = ? ORDER BY nome ASC");
$stmtClientes->execute([$cod_id]);
$clientes = $stmtClientes;
?>

<style>
    .wizard-container {
        max-width: 100%;
        padding: 20px;
    }
    
    .breadcrumb-nav {
        color: #8a8a8a;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }
    
    .breadcrumb-nav a {
        color: #8a8a8a;
        text-decoration: none;
    }
    
    .breadcrumb-nav a:hover {
        color: #00a884;
    }
    
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #fff;
        margin-bottom: 30px;
    }
    
    /* Stepper */
    .wizard-stepper {
        display: flex;
        justify-content: center;
        margin-bottom: 40px;
        position: relative;
    }
    
    .wizard-stepper::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 15%;
        right: 15%;
        height: 2px;
        background: #2d2d3a;
        z-index: 0;
    }
    
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 1;
        flex: 1;
        max-width: 200px;
    }
    
    .step-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #2d2d3a;
        color: #8a8a8a;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-bottom: 10px;
        transition: all 0.3s;
        border: 2px solid #2d2d3a;
    }
    
    .step-item.active .step-circle,
    .step-item.completed .step-circle {
        background: #00a884;
        color: #fff;
        border-color: #00a884;
    }
    
    .step-item.completed .step-circle {
        background: #00d4aa;
    }
    
    .step-label {
        color: #8a8a8a;
        font-size: 0.85rem;
        text-align: center;
    }
    
    .step-item.active .step-label {
        color: #00a884;
        font-weight: 600;
    }
    
    /* Wizard Content - Rounded Theme */
    .wizard-content {
        background: #1e1e2d;
        border-radius: 20px;
        padding: 30px;
        border: 1px solid #2d2d3a;
    }
    
    .wizard-step {
        display: none;
    }
    
    .wizard-step.active {
        display: block;
    }
    
    .section-title {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 1.2rem;
        color: #fff;
        margin-bottom: 25px;
    }
    
    .section-title i {
        color: #00a884;
    }
    
    /* Stats Cards */
    .stats-mini {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        margin-bottom: 25px;
    }
    
    @media (max-width: 768px) {
        .stats-mini {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    .stat-mini-card {
        background: #2d2d3a;
        border-radius: 15px;
        padding: 20px;
        text-align: center;
        border: 1px solid #3d3d4a;
    }
    
    .stat-mini-card .value {
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
    }
    
    .stat-mini-card .label {
        color: #8a8a8a;
        font-size: 0.85rem;
    }
    
    .stat-mini-card.selected {
        border-color: #00a884;
    }
    
    .stat-mini-card.selected .value {
        color: #00a884;
    }
    
    /* Filter Section */
    .filter-section {
        display: flex;
        gap: 20px;
        margin-bottom: 20px;
        flex-wrap: wrap;
    }
    
    .filter-group {
        flex: 1;
        min-width: 200px;
    }
    
    .filter-group label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #8a8a8a;
        font-size: 0.85rem;
        margin-bottom: 8px;
    }
    
    .filter-group select,
    .filter-group input {
        width: 100%;
        background: #2d2d3a;
        border: 1px solid #3d3d4a;
        color: #fff;
        padding: 12px 15px;
        border-radius: 12px;
        font-size: 0.9rem;
    }
    
    .filter-group input::placeholder {
        color: #6a6a7a;
    }
    
    /* Action Buttons Row */
    .action-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .btn-filter {
        padding: 10px 20px;
        border-radius: 25px;
        border: none;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-filter.primary {
        background: #007bff;
        color: #fff;
    }
    
    .btn-filter.success {
        background: #00a884;
        color: #fff;
    }
    
    .btn-filter.secondary {
        background: #2d2d3a;
        color: #fff;
        border: 1px solid #3d3d4a;
    }
    
    .btn-filter:hover {
        opacity: 0.9;
        transform: translateY(-1px);
    }
    
    /* Contact List */
    .contacts-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .contact-item {
        background: #2d2d3a;
        border-radius: 15px;
        padding: 15px 20px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border: 1px solid #3d3d4a;
        transition: all 0.2s;
    }
    
    .contact-item:hover {
        border-color: #00a884;
    }
    
    .contact-item.selected {
        border-color: #00a884;
        background: rgba(0, 168, 132, 0.1);
    }
    
    .contact-item.received {
        opacity: 0.6;
        background: rgba(108, 117, 125, 0.1);
    }
    
    .contact-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .contact-checkbox {
        width: 22px;
        height: 22px;
        accent-color: #00a884;
    }
    
    .contact-details h4 {
        color: #fff;
        font-size: 0.95rem;
        margin: 0 0 3px 0;
    }
    
    .contact-details span {
        color: #8a8a8a;
        font-size: 0.8rem;
    }
    
    .contact-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .contact-badge.whatsapp {
        background: rgba(0, 168, 132, 0.15);
        color: #00a884;
    }
    
    .contact-badge.cliente {
        background: rgba(0, 123, 255, 0.15);
        color: #007bff;
    }
    
    /* Message Composer */
    .variables-section {
        margin-bottom: 20px;
    }
    
    .variables-section label {
        color: #8a8a8a;
        font-size: 0.85rem;
        margin-bottom: 10px;
        display: block;
    }
    
    .variable-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .variable-tag {
        background: #007bff;
        color: #fff;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
    }
    
    .variable-tag:hover {
        background: #0056b3;
        transform: translateY(-2px);
    }
    
    .message-area {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 30px;
    }
    
    @media (max-width: 992px) {
        .message-area {
            grid-template-columns: 1fr;
        }
    }
    
    .message-input-section label {
        color: #fff;
        font-weight: 600;
        margin-bottom: 10px;
        display: block;
    }
    
    .message-textarea {
        width: 100%;
        min-height: 200px;
        background: #2d2d3a;
        border: 1px solid #3d3d4a;
        border-radius: 15px;
        padding: 15px;
        color: #fff;
        font-size: 0.95rem;
        resize: vertical;
    }
    
    .message-textarea::placeholder {
        color: #6a6a7a;
    }
    
    .char-counter {
        text-align: right;
        color: #8a8a8a;
        font-size: 0.8rem;
        margin-top: 5px;
    }
    
    /* File Upload */
    .upload-section {
        margin-top: 25px;
    }
    
    .upload-section label {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #8a8a8a;
        font-size: 0.85rem;
        margin-bottom: 10px;
    }
    
    .upload-box {
        background: #2d2d3a;
        border: 2px dashed #3d3d4a;
        border-radius: 15px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .upload-box input[type="file"] {
        display: none;
    }
    
    .upload-box .btn-upload {
        background: #3d3d4a;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        border: none;
    }
    
    .upload-box .file-name {
        color: #8a8a8a;
    }
    
    .upload-info {
        color: #6a6a7a;
        font-size: 0.8rem;
        margin-top: 8px;
    }
    
    /* Message Preview */
    .preview-section h4 {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #8a8a8a;
        font-size: 0.9rem;
        margin-bottom: 15px;
    }
    
    .phone-preview {
        background: #0b141a;
        border-radius: 20px;
        padding: 20px;
        max-width: 300px;
    }
    
    .preview-header {
        text-align: center;
        color: #8a8a8a;
        font-size: 0.75rem;
        margin-bottom: 15px;
    }
    
    .preview-bubble {
        background: #005c4b;
        color: #fff;
        padding: 12px 15px;
        border-radius: 10px 10px 0 10px;
        font-size: 0.9rem;
        word-wrap: break-word;
        min-height: 50px;
    }
    
    .preview-media {
        background: #1e1e2d;
        border-radius: 10px;
        padding: 40px;
        text-align: center;
        color: #8a8a8a;
        margin-bottom: 10px;
    }
    
    /* Instance Selection */
    .instance-mode {
        margin-bottom: 25px;
    }
    
    .mode-option {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 15px;
        background: #2d2d3a;
        border-radius: 15px;
        margin-bottom: 10px;
        cursor: pointer;
        border: 1px solid #3d3d4a;
        transition: all 0.2s;
    }
    
    .mode-option:hover,
    .mode-option.selected {
        border-color: #00a884;
    }
    
    .mode-option input[type="radio"] {
        margin-top: 3px;
        accent-color: #00a884;
    }
    
    .mode-option .mode-info h5 {
        color: #fff;
        margin: 0 0 5px 0;
        font-size: 0.95rem;
    }
    
    .mode-option .mode-info p {
        color: #8a8a8a;
        margin: 0;
        font-size: 0.8rem;
    }
    
    .instances-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .instance-card {
        background: #2d2d3a;
        border-radius: 15px;
        padding: 20px;
        border: 2px solid #3d3d4a;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .instance-card:hover,
    .instance-card.selected {
        border-color: #00a884;
    }
    
    .instance-card.selected {
        background: rgba(0, 168, 132, 0.1);
    }
    
    .instance-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }
    
    .instance-name {
        color: #fff;
        font-weight: 600;
        font-size: 1.1rem;
    }
    
    .instance-status {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #00a884;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .instance-status i {
        color: #fff;
        font-size: 0.7rem;
    }
    
    .instance-badge {
        display: inline-block;
        padding: 4px 10px;
        background: rgba(0, 168, 132, 0.15);
        color: #00a884;
        border-radius: 15px;
        font-size: 0.75rem;
        margin-bottom: 10px;
    }
    
    .instance-info {
        display: flex;
        justify-content: space-between;
        color: #8a8a8a;
        font-size: 0.8rem;
    }
    
    /* Schedule Section */
    .schedule-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 30px;
    }
    
    @media (max-width: 768px) {
        .schedule-grid {
            grid-template-columns: 1fr;
        }
    }
    
    .form-group label {
        color: #fff;
        font-weight: 500;
        margin-bottom: 10px;
        display: block;
    }
    
    .form-group input {
        width: 100%;
        background: #2d2d3a;
        border: 1px solid #3d3d4a;
        color: #fff;
        padding: 15px;
        border-radius: 12px;
        font-size: 0.95rem;
    }
    
    .delay-config {
        background: #2d2d3a;
        border-radius: 15px;
        padding: 20px;
        border: 1px solid #3d3d4a;
    }
    
    .delay-config h5 {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;
        margin: 0 0 15px 0;
    }
    
    .delay-info {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #8a8a8a;
        font-size: 0.85rem;
    }
    
    .delay-info .highlight {
        color: #00a884;
        font-weight: 600;
    }
    
    .btn-delay {
        background: rgba(0, 168, 132, 0.15);
        color: #00a884;
        border: 1px solid #00a884;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 0.8rem;
        cursor: pointer;
    }
    
    /* Summary Section */
    .summary-card {
        background: #2d2d3a;
        border-radius: 15px;
        padding: 25px;
        border: 1px solid #3d3d4a;
    }
    
    .summary-card h3 {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #fff;
        margin: 0 0 20px 0;
        font-size: 1.2rem;
    }
    
    .summary-card h3 i {
        color: #00a884;
    }
    
    .summary-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
    }
    
    .summary-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
    }
    
    .summary-item i {
        color: #00a884;
        margin-top: 3px;
    }
    
    .summary-item .label {
        color: #8a8a8a;
        font-size: 0.85rem;
    }
    
    .summary-item .value {
        color: #00a884;
        font-weight: 600;
    }
    
    /* Navigation Buttons */
    .wizard-nav {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #2d2d3a;
    }
    
    .btn-wizard {
        padding: 12px 30px;
        border-radius: 8px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 10px;
        border: none;
        text-decoration: none;
    }
    
    .btn-wizard.back {
        background: #3d3d4a;
        color: #fff;
    }
    
    .btn-wizard.next {
        background: #00a884;
        color: #fff;
    }
    
    .btn-wizard.submit {
        background: linear-gradient(135deg, #00d4aa 0%, #00a884 100%);
        color: #fff;
    }
    
    .btn-wizard:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
    }
    
    /* Delay Modal - Modern Rounded */
    .delay-modal .modal-content {
        background: #1e1e2d;
        border: 1px solid #2d2d3a;
        border-radius: 20px;
    }
    
    .delay-modal .modal-header {
        border-bottom: 1px solid #2d2d3a;
    }
    
    .delay-modal .modal-title {
        color: #fff;
    }
    
    .delay-modal .close {
        color: #fff;
    }
    
    .delay-slider-container {
        padding: 20px 0;
    }
    
    .delay-slider {
        width: 100%;
        accent-color: #00a884;
    }
    
    .delay-value {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        color: #00a884;
        margin: 15px 0;
    }
    
    .delay-labels {
        display: flex;
        justify-content: space-between;
        color: #8a8a8a;
        font-size: 0.8rem;
    }
    
    /* Theme Support - Light Mode */
    body:not(.dark-mode) .wizard-container,
    body:not(.dark-mode) .wizard-section,
    body:not(.dark-mode) .stat-mini-card,
    body:not(.dark-mode) .contato-checkbox,
    body:not(.dark-mode) .cliente-checkbox,
    body:not(.dark-mode) .instancia-card,
    body:not(.dark-mode) .delay-config {
        background: #fff !important;
        border-color: #e0e0e0 !important;
    }
    
    body:not(.dark-mode) .page-title,
    body:not(.dark-mode) .section-title,
    body:not(.dark-mode) .contato-checkbox h4,
    body:not(.dark-mode) .cliente-checkbox h4,
    body:not(.dark-mode) .step-label,
    body:not(.dark-mode) .instancia-name,
    body:not(.dark-mode) .stat-mini-card .value,
    body:not(.dark-mode) .filter-group label {
        color: #333 !important;
    }
    
    body:not(.dark-mode) .breadcrumb-nav,
    body:not(.dark-mode) .contato-checkbox span,
    body:not(.dark-mode) .cliente-checkbox span,
    body:not(.dark-mode) .delay-labels,
    body:not(.dark-mode) .stat-mini-card .label {
        color: #666 !important;
    }
    
    body:not(.dark-mode) .filter-group input,
    body:not(.dark-mode) .filter-group select,
    body:not(.dark-mode) textarea {
        background: #f8f9fa !important;
        border-color: #dee2e6 !important;
        color: #333 !important;
    }
</style>

<div class="slim-mainpanel">
    <div class="wizard-container">
        
        <!-- Breadcrumb -->
        <div class="breadcrumb-nav">
            <i class="fas fa-plus-circle" style="color: #28a745;"></i>
            <a href="campanhas">Campanhas</a> - <?= $campanha_id > 0 ? 'Editar Campanha' : 'Nova Campanha' ?>
        </div>
        
        <h1 class="page-title"><i class="fas fa-bullhorn" style="color: #28a745; margin-right: 10px;"></i><?= $campanha_id > 0 ? 'Editar Campanha WhatsApp' : 'Nova Campanha WhatsApp' ?></h1>
        
        <!-- Stepper -->
        <div class="wizard-stepper">
            <div class="step-item active" data-step="1">
                <div class="step-circle">1</div>
                <div class="step-label">Destinatários</div>
            </div>
            <div class="step-item" data-step="2">
                <div class="step-circle">2</div>
                <div class="step-label">Mensagem</div>
            </div>
            <div class="step-item" data-step="3">
                <div class="step-circle">3</div>
                <div class="step-label">Instâncias</div>
            </div>
            <div class="step-item" data-step="4">
                <div class="step-circle">4</div>
                <div class="step-label">Agendar</div>
            </div>
        </div>
        
        <form id="formCampanha" enctype="multipart/form-data">
            <input type="hidden" name="campanha_id" value="<?= $campanha_id ?>">
            <input type="hidden" name="acao" value="<?= $campanha_id ? 'atualizar' : 'criar' ?>">
            
            <div class="wizard-content">
                
                <!-- Step 1: Selecionar Destinatários -->
                <div class="wizard-step active" data-step="1">
                    <h3 class="section-title">
                        <i class="fas fa-users" style="color: #007bff;"></i>
                        Selecionar Destinatários
                    </h3>
                    
                    <!-- Stats -->
                    <div class="stats-mini">
                        <div class="stat-mini-card">
                            <div class="value" id="totalContatos">0</div>
                            <div class="label">Todos</div>
                        </div>
                        <div class="stat-mini-card">
                            <div class="value" id="contatosVencidos">0</div>
                            <div class="label">Vencidos</div>
                        </div>
                        <div class="stat-mini-card">
                            <div class="value" id="contatosEmDia">0</div>
                            <div class="label">Em Dia</div>
                        </div>
                        <div class="stat-mini-card selected">
                            <div class="value" id="contatosSelecionados">0</div>
                            <div class="label">Selecionados</div>
                        </div>
                    </div>
                    
                    <!-- Filters -->
                    <div class="filter-section">
                        <div class="filter-group">
                            <label><i class="fas fa-filter"></i> Filtrar por Categoria</label>
                            <select id="filterCategoria">
                                <option value="">Todas as Categorias</option>
                                <?php
                                $stmtCat = $connect->prepare("SELECT * FROM categoria WHERE idu = ? ORDER BY nome");
                                $stmtCat->execute([$cod_id]);
                                while ($cat = $stmtCat->fetch(PDO::FETCH_OBJ)) {
                                    echo "<option value='{$cat->id}'>{$cat->nome}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="filter-group">
                            <label><i class="fas fa-search"></i> Buscar Cliente</label>
                            <input type="text" id="searchContato" placeholder="Digite nome ou telefone...">
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="button" class="btn-filter primary" onclick="showTodos()">
                            <i class="fas fa-users"></i> Todos
                        </button>
                        <button type="button" class="btn-filter secondary" onclick="showVencidos()">
                            <i class="fas fa-exclamation-circle"></i> Vencidos
                        </button>
                        <button type="button" class="btn-filter secondary" onclick="showEmDia()">
                            <i class="fas fa-check-circle"></i> Em Dia
                        </button>
                        <button type="button" class="btn-filter secondary" onclick="showSemCobranca()">
                            <i class="fas fa-ban"></i> Sem Cobranças
                        </button>
                    </div>
                    
                    <div class="action-buttons">
                        <button type="button" class="btn-filter success" onclick="marcarVisiveis()">
                            <i class="fas fa-check-square"></i> Marcar Visíveis
                        </button>
                        <button type="button" class="btn-filter success" onclick="marcarTodos()">
                            <i class="fas fa-database"></i> Marcar Todos (Banco)
                        </button>
                        <button type="button" class="btn-filter secondary" onclick="desmarcarTodos()">
                            <i class="fas fa-times-circle"></i> Desmarcar
                        </button>
                    </div>
                    
                    <p class="text-muted mb-3"><small>* Máximo de 50 contatos por campanha. Contatos que já receberam esta campanha não podem ser selecionados novamente.</small></p>
                    
                    <!-- Contact List -->
                    <div class="contacts-list" id="contactsList">
                        <?php 
                        // Combinar contatos WhatsApp e clientes
                        $allContacts = [];
                        
                        // Contatos WhatsApp
                        $stmtContatosWA = $connect->prepare("SELECT *, 'whatsapp' as tipo FROM contatos_whatsapp WHERE id_usuario = ?");
                        $stmtContatosWA->execute([$cod_id]);
                        while ($c = $stmtContatosWA->fetch(PDO::FETCH_OBJ)) {
                            $allContacts[] = $c;
                        }
                        
                        // Clientes
                        $stmtClientesDB = $connect->prepare("SELECT Id as id, nome, celular as telefone, 'cliente' as tipo, idc FROM clientes WHERE idm = ?");
                        $stmtClientesDB->execute([$cod_id]);
                        while ($c = $stmtClientesDB->fetch(PDO::FETCH_OBJ)) {
                            $allContacts[] = $c;
                        }
                        
                        // Verificar quais já receberam (para edição)
                        $jaReceberam = [];
                        if ($campanha_id > 0) {
                            $stmtRecebidos = $connect->prepare("SELECT telefone FROM campanha_contatos WHERE campanha_id = ? AND status_envio IN ('enviado', 'entregue', 'lido')");
                            $stmtRecebidos->execute([$campanha_id]);
                            while ($r = $stmtRecebidos->fetch(PDO::FETCH_ASSOC)) {
                                $jaReceberam[] = $r['telefone'];
                            }
                        }
                        
                        $counter = 0;
                        foreach ($allContacts as $contato):
                            $telefone = preg_replace('/[^0-9]/', '', $contato->telefone ?? '');
                            if (empty($telefone)) continue;
                            
                            $jaRecebeu = in_array($telefone, $jaReceberam);
                            $selecionado = in_array($telefone, $contatosSelecionados);
                            $counter++;
                        ?>
                        <div class="contact-item <?= $jaRecebeu ? 'received' : '' ?> <?= $selecionado ? 'selected' : '' ?>" 
                             data-telefone="<?= $telefone ?>" 
                             data-nome="<?= htmlspecialchars($contato->nome ?? '') ?>"
                             data-tipo="<?= $contato->tipo ?>">
                            <div class="contact-info">
                                <input type="checkbox" 
                                       class="contact-checkbox" 
                                       name="contatos[]" 
                                       value="<?= $telefone ?>"
                                       data-nome="<?= htmlspecialchars($contato->nome ?? '') ?>"
                                       <?= $jaRecebeu ? 'disabled' : '' ?>
                                       <?= $selecionado && !$jaRecebeu ? 'checked' : '' ?>>
                                <div class="contact-details">
                                    <h4><?= $telefone ?> - <?= htmlspecialchars($contato->nome ?? 'Sem Nome') ?></h4>
                                    <span><i class="fas fa-phone"></i> <?= $telefone ?></span>
                                </div>
                            </div>
                            <div>
                                <span class="contact-badge <?= $contato->tipo ?>">
                                    <?= $contato->tipo == 'whatsapp' ? '<i class="fab fa-whatsapp"></i> WhatsApp' : '<i class="fas fa-user"></i> Cliente' ?>
                                </span>
                                <?php if ($jaRecebeu): ?>
                                <span class="contact-badge" style="background: rgba(108, 117, 125, 0.15); color: #6c757d; margin-left: 5px;">
                                    <i class="fas fa-check"></i> Recebido
                                </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <?php if ($counter == 0): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-address-book fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhum contato encontrado. <a href="contatos_campanha" class="text-success">Adicione contatos</a> para criar campanhas.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Step 2: Compor Mensagem -->
                <div class="wizard-step" data-step="2">
                    <h3 class="section-title">
                        <i class="far fa-comment-dots" style="color: #f6ad55;"></i>
                        Compor Mensagem
                    </h3>
                    
                    <!-- Variables -->
                    <div class="variables-section">
                        <label>Variáveis Disponíveis (clique para inserir)</label>
                        <div class="variable-tags">
                            <button type="button" class="variable-tag" data-var="#NOME#">#NOME#</button>
                            <button type="button" class="variable-tag" data-var="#EMPRESA#">#EMPRESA#</button>
                            <button type="button" class="variable-tag" data-var="#CNPJ#">#CNPJ#</button>
                            <button type="button" class="variable-tag" data-var="#ENDERECO#">#ENDERECO#</button>
                            <button type="button" class="variable-tag" data-var="#CONTATO#">#CONTATO#</button>
                            <button type="button" class="variable-tag" data-var="#VENCIMENTO#">#VENCIMENTO#</button>
                            <button type="button" class="variable-tag" data-var="#DATAPAGAMENTO#">#DATAPAGAMENTO#</button>
                            <button type="button" class="variable-tag" data-var="#VALOR#">#VALOR#</button>
                        </div>
                    </div>
                    
                    <div class="message-area">
                        <div class="message-input-section">
                            <label>Mensagem</label>
                            <textarea class="message-textarea" 
                                      name="mensagem" 
                                      id="mensagemTexto" 
                                      maxlength="700" 
                                      placeholder="Digite sua mensagem aqui..."><?= $campanha ? htmlspecialchars($campanha->mensagem) : '' ?></textarea>
                            <div class="char-counter"><span id="charCount">0</span>/700</div>
                            
                            <!-- File Upload -->
                            <div class="upload-section">
                                <label><i class="fas fa-paperclip"></i> Anexar Arquivo (Opcional - Máx: 10MB)</label>
                                <div class="upload-box">
                                    <input type="file" name="arquivo_media" id="arquivoMedia" accept="image/*,video/*,audio/*,.pdf,.doc,.docx,.xls,.xlsx,.csv">
                                    <button type="button" class="btn-upload" onclick="document.getElementById('arquivoMedia').click()">
                                        Escolher arquivo
                                    </button>
                                    <span class="file-name" id="fileName">
                                        <?= $campanha && $campanha->arquivo_nome ? $campanha->arquivo_nome : 'Nenhum arquivo escolhido' ?>
                                    </span>
                                </div>
                                <div class="upload-info">Formatos aceitos: Imagem, Vídeo, Áudio, PDF, Excel, CSV</div>
                            </div>
                        </div>
                        
                        <div class="preview-section">
                            <h4><i class="fas fa-eye"></i> Preview da Mensagem</h4>
                            <div class="phone-preview">
                                <div class="preview-header">Hoje às 00:00</div>
                                <div id="previewMedia" class="preview-media" style="display: none;">
                                    <i class="fas fa-image fa-2x"></i>
                                    <p>Nenhum arquivo</p>
                                </div>
                                <div class="preview-bubble" id="previewText">
                                    Sua mensagem aparecerá aqui...
                                </div>
                            </div>
                            
                            <div class="mt-3">
                                <h4><i class="fas fa-paperclip"></i> Preview do Anexo</h4>
                                <div class="phone-preview" style="background: #2d2d3a;">
                                    <div id="anexoPreview" class="text-center py-3">
                                        <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                        <p class="text-muted mb-0">Nenhum arquivo</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 3: Selecionar Instância -->
                <div class="wizard-step" data-step="3">
                    <h3 class="section-title">
                        <i class="fab fa-whatsapp" style="color: #25D366;"></i>
                        Selecionar Instância(s) WhatsApp
                    </h3>
                    
                    <!-- Modo de Envio -->
                    <div class="instance-mode">
                        <label class="mode-option selected" onclick="selectMode('unica')">
                            <input type="radio" name="modo_envio" value="unica" checked>
                            <div class="mode-info">
                                <h5>Usar apenas 1 instância</h5>
                                <p>Todos os clientes serão enviados pela mesma conexão</p>
                            </div>
                        </label>
                        <label class="mode-option" onclick="selectMode('balanceado')">
                            <input type="radio" name="modo_envio" value="balanceado">
                            <div class="mode-info">
                                <h5>Balancear automaticamente (Round-Robin)</h5>
                                <p>Distribui os envios igualmente entre todas as instâncias ativas</p>
                            </div>
                        </label>
                    </div>
                    
                    <!-- Instances Grid -->
                    <div class="instances-grid" id="instancesGrid">
                        <!-- Instância padrão baseada na conexão do usuário -->
                        <?php
                        $stmtConexao = $connect->prepare("SELECT * FROM conexoes WHERE id_usuario = ? AND conn = 1");
                        $stmtConexao->execute([$cod_id]);
                        $conexao = $stmtConexao->fetch(PDO::FETCH_OBJ);
                        if ($conexao):
                        ?>
                        <div class="instance-card selected" data-id="<?= $dadosgerais->tokenapi ?>" onclick="selectInstance(this)">
                            <div class="instance-header">
                                <span class="instance-name">Conexão Principal</span>
                                <div class="instance-status">
                                    <i class="fas fa-check"></i>
                                </div>
                            </div>
                            <span class="instance-badge">Padrão</span>
                            <div class="instance-info">
                                <span><i class="fas fa-server"></i> Servidor 1</span>
                                <span>0/500</span>
                            </div>
                            <input type="hidden" name="instancia_id" value="<?= $dadosgerais->tokenapi ?>">
                            <input type="hidden" name="instancia_nome" value="Conexão Principal">
                        </div>
                        <?php else: ?>
                        <div class="text-center py-5 w-100">
                            <i class="fab fa-whatsapp fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhuma instância conectada. <a href="whatsapp" class="text-success">Conecte seu WhatsApp</a> primeiro.</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Step 4: Agendar -->
                <div class="wizard-step" data-step="4">
                    <h3 class="section-title">
                        <i class="far fa-calendar-alt" style="color: #9c27b0;"></i>
                        Definir Data e Hora
                    </h3>
                    
                    <div class="schedule-grid">
                        <div class="form-group">
                            <label>Data do Disparo</label>
                            <input type="date" name="data_disparo" id="dataDisparo" 
                                   value="<?= $campanha && $campanha->data_agendamento ? date('Y-m-d', strtotime($campanha->data_agendamento)) : '' ?>">
                        </div>
                        <div class="form-group">
                            <label>Horário do Disparo</label>
                            <input type="time" name="hora_disparo" id="horaDisparo"
                                   value="<?= $campanha && $campanha->data_agendamento ? date('H:i', strtotime($campanha->data_agendamento)) : '' ?>">
                        </div>
                    </div>
                    
                    <!-- Delay Config -->
                    <div class="delay-config">
                        <h5><i class="fas fa-cog"></i> Configurações de Envio</h5>
                        <div class="delay-info">
                            <i class="fas fa-clock"></i>
                            Delay entre mensagens: <span class="highlight" id="delayDisplay"><?= $campanha ? $campanha->delay_segundos : 60 ?> segundos</span>
                            <span class="text-muted">(recomendado para evitar bloqueios)</span>
                            <button type="button" class="btn-delay" onclick="$('#modalDelay').modal('show')">
                                <i class="fas fa-cog"></i> Configurar Delay
                            </button>
                            <input type="hidden" name="delay_segundos" id="delayValue" value="<?= $campanha ? $campanha->delay_segundos : 60 ?>">
                        </div>
                    </div>
                    
                    <!-- Summary -->
                    <div class="summary-card mt-4">
                        <h3><i class="fas fa-clipboard-list"></i> Resumo da Campanha</h3>
                        <div class="summary-grid">
                            <div class="summary-item">
                                <i class="fas fa-users"></i>
                                <div>
                                    <div class="label">Destinatários:</div>
                                    <div class="value" id="summaryDestinatarios">0 clientes</div>
                                </div>
                            </div>
                            <div class="summary-item">
                                <i class="fas fa-paper-plane"></i>
                                <div>
                                    <div class="label">Modo de Envio:</div>
                                    <div class="value" id="summaryModo">Instância Única</div>
                                </div>
                            </div>
                            <div class="summary-item">
                                <i class="fas fa-file-alt"></i>
                                <div>
                                    <div class="label">Caracteres:</div>
                                    <div class="value" id="summaryChars">0/700</div>
                                </div>
                            </div>
                            <div class="summary-item">
                                <i class="fas fa-paperclip"></i>
                                <div>
                                    <div class="label">Anexo:</div>
                                    <div class="value" id="summaryAnexo">Não</div>
                                </div>
                            </div>
                            <div class="summary-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <div class="label">Tempo Estimado:</div>
                                    <div class="value" id="summaryTempo">~0min 0seg</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Navigation -->
                <div class="wizard-nav">
                    <button type="button" class="btn-wizard back" id="btnVoltar" style="visibility: hidden;">
                        <i class="fas fa-chevron-left"></i> Voltar
                    </button>
                    <button type="button" class="btn-wizard next" id="btnProximo">
                        Próximo <i class="fas fa-chevron-right"></i>
                    </button>
                    <button type="submit" class="btn-wizard submit" id="btnAgendar" style="display: none;">
                        <i class="fas fa-check-circle"></i> Agendar Campanha
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal Delay -->
<div class="modal fade delay-modal" id="modalDelay" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-clock mr-2"></i>Configurar Delay</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p class="text-muted text-center">Defina o intervalo entre cada mensagem enviada (mínimo 60 segundos para evitar bloqueios)</p>
                <div class="delay-slider-container">
                    <input type="range" class="delay-slider" id="delaySlider" min="60" max="300" value="60" step="10">
                    <div class="delay-value"><span id="delaySliderValue">60</span> segundos</div>
                    <div class="delay-labels">
                        <span>1 min</span>
                        <span>5 min</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" onclick="aplicarDelay()">Aplicar</button>
            </div>
        </div>
    </div>
</div>

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../js/slim.js"></script>

<script>
let currentStep = 1;
const totalSteps = 4;
const maxContatos = 50;

$(document).ready(function() {
    updateStats();
    updatePreview();
    
    // Character counter
    $('#mensagemTexto').on('input', function() {
        const len = $(this).val().length;
        $('#charCount').text(len);
        updatePreview();
    });
    
    // File upload
    $('#arquivoMedia').on('change', function() {
        const file = this.files[0];
        if (file) {
            $('#fileName').text(file.name);
            
            // Preview
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#anexoPreview').html('<img src="' + e.target.result + '" style="max-width: 100%; max-height: 150px; border-radius: 10px;">');
                    $('#previewMedia').show().html('<img src="' + e.target.result + '" style="max-width: 100%; border-radius: 10px;">');
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                $('#anexoPreview').html('<i class="fas fa-video fa-2x text-primary"></i><p class="mb-0">' + file.name + '</p>');
                $('#previewMedia').show().html('<i class="fas fa-video fa-3x text-primary"></i>');
            } else {
                $('#anexoPreview').html('<i class="fas fa-file fa-2x text-warning"></i><p class="mb-0">' + file.name + '</p>');
                $('#previewMedia').hide();
            }
            
            $('#summaryAnexo').text('Sim');
        }
    });
    
    // Variable tags
    $('.variable-tag').on('click', function() {
        const variable = $(this).data('var');
        const textarea = $('#mensagemTexto');
        const curPos = textarea[0].selectionStart;
        const text = textarea.val();
        textarea.val(text.slice(0, curPos) + variable + text.slice(curPos));
        textarea.focus();
        updatePreview();
    });
    
    // Contact selection
    $(document).on('change', '.contact-checkbox', function() {
        const selected = $('.contact-checkbox:checked').length;
        
        if (selected > maxContatos) {
            $(this).prop('checked', false);
            alert('Máximo de ' + maxContatos + ' contatos por campanha!');
            return;
        }
        
        $(this).closest('.contact-item').toggleClass('selected', $(this).is(':checked'));
        updateStats();
    });
    
    // Search
    $('#searchContato').on('keyup', function() {
        const search = $(this).val().toLowerCase();
        $('.contact-item').each(function() {
            const nome = $(this).data('nome').toLowerCase();
            const telefone = $(this).data('telefone');
            $(this).toggle(nome.includes(search) || telefone.includes(search));
        });
    });
    
    // Delay slider
    $('#delaySlider').on('input', function() {
        $('#delaySliderValue').text($(this).val());
    });
    
    // Form submission
    $('#formCampanha').on('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        $.ajax({
            url: 'classes/campanhas_exe.php',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    window.location.href = 'campanhas?sucesso=' + encodeURIComponent(resp.message);
                } else {
                    alert(resp.message || 'Erro ao salvar campanha');
                }
            },
            error: function(xhr, status, error) {
                alert('Erro ao processar requisição: ' + error);
            }
        });
    });
});

// Navigation
$('#btnProximo').on('click', function() {
    if (!validateStep(currentStep)) return;
    
    if (currentStep < totalSteps) {
        goToStep(currentStep + 1);
    }
});

$('#btnVoltar').on('click', function() {
    if (currentStep > 1) {
        goToStep(currentStep - 1);
    }
});

function goToStep(step) {
    currentStep = step;
    
    // Update steps
    $('.wizard-step').removeClass('active');
    $(`.wizard-step[data-step="${step}"]`).addClass('active');
    
    // Update stepper
    $('.step-item').removeClass('active completed');
    $('.step-item').each(function() {
        const s = $(this).data('step');
        if (s < step) $(this).addClass('completed');
        if (s === step) $(this).addClass('active');
    });
    
    // Update buttons
    $('#btnVoltar').css('visibility', step === 1 ? 'hidden' : 'visible');
    
    if (step === totalSteps) {
        $('#btnProximo').hide();
        $('#btnAgendar').show();
        updateSummary();
    } else {
        $('#btnProximo').show();
        $('#btnAgendar').hide();
    }
}

function validateStep(step) {
    if (step === 1) {
        const selected = $('.contact-checkbox:checked').length;
        if (selected === 0) {
            alert('Selecione pelo menos um contato!');
            return false;
        }
        if (selected > maxContatos) {
            alert('Máximo de ' + maxContatos + ' contatos permitido!');
            return false;
        }
    }
    
    if (step === 2) {
        const msg = $('#mensagemTexto').val().trim();
        if (msg.length === 0) {
            alert('Digite uma mensagem!');
            return false;
        }
    }
    
    return true;
}

function updateStats() {
    const total = $('.contact-item:not(.received)').length;
    const selected = $('.contact-checkbox:checked').length;
    
    $('#totalContatos').text(total);
    $('#contatosSelecionados').text(selected);
}

function updatePreview() {
    const msg = $('#mensagemTexto').val() || 'Sua mensagem aparecerá aqui...';
    $('#previewText').text(msg);
}

function updateSummary() {
    const selected = $('.contact-checkbox:checked').length;
    const chars = $('#mensagemTexto').val().length;
    const delay = parseInt($('#delayValue').val());
    const tempo = (selected * delay) / 60;
    const modo = $('input[name="modo_envio"]:checked').val();
    
    $('#summaryDestinatarios').text(selected + ' clientes');
    $('#summaryChars').text(chars + '/700');
    $('#summaryModo').text(modo === 'unica' ? 'Instância Única' : 'Balanceado');
    $('#summaryTempo').text('~' + Math.floor(tempo) + 'min ' + Math.round((tempo % 1) * 60) + 'seg');
}

function marcarVisiveis() {
    let count = 0;
    $('.contact-item:visible:not(.received) .contact-checkbox').each(function() {
        if (count < maxContatos && !$(this).is(':checked')) {
            $(this).prop('checked', true);
            $(this).closest('.contact-item').addClass('selected');
            count++;
        }
    });
    updateStats();
}

function marcarTodos() {
    let count = 0;
    $('.contact-item:not(.received) .contact-checkbox').each(function() {
        if (count < maxContatos) {
            $(this).prop('checked', true);
            $(this).closest('.contact-item').addClass('selected');
            count++;
        }
    });
    updateStats();
}

function desmarcarTodos() {
    $('.contact-checkbox').prop('checked', false);
    $('.contact-item').removeClass('selected');
    updateStats();
}

function showTodos() {
    $('.contact-item').show();
}

function showVencidos() {
    // Implementar lógica de filtro por vencidos se necessário
    $('.contact-item').show();
}

function showEmDia() {
    // Implementar lógica de filtro
    $('.contact-item').show();
}

function showSemCobranca() {
    // Implementar lógica de filtro
    $('.contact-item').show();
}

function selectMode(mode) {
    $('.mode-option').removeClass('selected');
    $(`input[value="${mode}"]`).closest('.mode-option').addClass('selected');
}

function selectInstance(el) {
    $('.instance-card').removeClass('selected');
    $(el).addClass('selected');
}

function aplicarDelay() {
    const delay = $('#delaySlider').val();
    $('#delayValue').val(delay);
    $('#delayDisplay').text(delay + ' segundos');
    $('#modalDelay').modal('hide');
}
</script>

</body>
</html>
<?php ob_end_flush(); ?>
