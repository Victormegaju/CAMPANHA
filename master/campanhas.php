<?php
require_once "topo.php";
require_once "menu.php";
require_once "classes/functions.php";

// ===== VERIFICAÇÃO AUTOMÁTICA DE CONEXÃO COM EVOLUTION API =====
// Isso sincroniza o status da conexão para todos os usuários automaticamente
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
        // Atualizar banco se estava desconectado
        if ($connData->conn != 1) {
            $connect->prepare("UPDATE conexoes SET conn = 1 WHERE id_usuario = ?")->execute([$cod_id]);
        }
    } else {
        // Se API diz desconectado, atualizar banco
        if ($connData->conn == 1) {
            $connect->prepare("UPDATE conexoes SET conn = 0 WHERE id_usuario = ?")->execute([$cod_id]);
        }
    }
} elseif ($connData && $connData->conn == 1) {
    // Tem registro mas sem apikey - verificar se realmente está conectado
    $conexaoAtiva = true;
}

// ===== FIM DA VERIFICAÇÃO =====

// Verificar limite de campanhas (máximo 3) - usando prepared statement
$stmtCount = $connect->prepare("SELECT COUNT(*) FROM campanhas WHERE id_usuario = ? AND status != 'cancelada'");
$stmtCount->execute([$cod_id]);
$countCampanhas = $stmtCount->fetchColumn();

// Buscar estatísticas
$stmtStats = $connect->prepare("
    SELECT 
        SUM(CASE WHEN status = 'em_andamento' THEN 1 ELSE 0 END) as executando,
        SUM(CASE WHEN status = 'agendada' THEN 1 ELSE 0 END) as aguardando,
        SUM(CASE WHEN status = 'concluida' THEN 1 ELSE 0 END) as concluidas,
        SUM(total_contatos) as total_destinatarios
    FROM campanhas 
    WHERE id_usuario = ? AND status != 'cancelada'
");
$stmtStats->execute([$cod_id]);
$stats = $stmtStats->fetch(PDO::FETCH_OBJ);

// Buscar campanhas
$stmtCampanhas = $connect->prepare("
    SELECT * FROM campanhas 
    WHERE id_usuario = ? AND status != 'cancelada'
    ORDER BY criado_em DESC
");
$stmtCampanhas->execute([$cod_id]);
$campanhas = $stmtCampanhas;
?>

<style>
    /* Estilos Dark Mode para Campanhas */
    .campanhas-container {
        padding: 20px 0;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .page-title h1 {
        font-size: 1.8rem;
        font-weight: 700;
        color: #fff;
        margin: 0;
    }
    
    .page-title p {
        color: #8a8a8a;
        margin: 5px 0 0 0;
        font-size: 0.9rem;
    }
    
    .btn-nova-campanha {
        background: linear-gradient(135deg, #00d4aa 0%, #00a884 100%);
        color: #fff;
        border: none;
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        text-decoration: none;
    }
    
    .btn-nova-campanha:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 212, 170, 0.4);
        color: #fff;
        text-decoration: none;
    }
    
    .btn-nova-campanha.disabled {
        background: #444;
        cursor: not-allowed;
        opacity: 0.7;
    }
    
    /* Cards de Estatísticas */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    @media (max-width: 992px) {
        .stats-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 576px) {
        .stats-row {
            grid-template-columns: 1fr;
        }
    }
    
    /* Rounded Corners Modern Theme */
    .stat-card {
        background: #1e1e2d;
        border-radius: 20px;
        padding: 25px;
        position: relative;
        overflow: hidden;
        border: 1px solid #2d2d3a;
    }
    
    .stat-card .badge-label {
        position: absolute;
        top: 15px;
        right: 15px;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .stat-card .badge-realtime { background: rgba(0, 168, 132, 0.2); color: #00a884; }
    .stat-card .badge-fila { background: rgba(255, 193, 7, 0.2); color: #ffc107; }
    .stat-card .badge-historico { background: rgba(108, 117, 125, 0.2); color: #6c757d; }
    .stat-card .badge-volume { background: rgba(0, 123, 255, 0.2); color: #007bff; }
    
    .stat-card .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 15px;
    }
    
    .stat-card .stat-icon.green { background: rgba(0, 168, 132, 0.15); color: #00a884; }
    .stat-card .stat-icon.yellow { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
    .stat-card .stat-icon.gray { background: rgba(108, 117, 125, 0.15); color: #6c757d; }
    .stat-card .stat-icon.blue { background: rgba(0, 123, 255, 0.15); color: #007bff; }
    
    .stat-card .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .stat-card .stat-value.green { color: #00d4aa; }
    .stat-card .stat-value.yellow { color: #ffc107; }
    .stat-card .stat-value.gray { color: #6c757d; }
    .stat-card .stat-value.blue { color: #007bff; }
    
    .stat-card .stat-label {
        color: #8a8a8a;
        font-size: 0.85rem;
    }
    
    /* Seção de Campanhas e Sidebar */
    .main-content-row {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 25px;
    }
    
    @media (max-width: 1200px) {
        .main-content-row {
            grid-template-columns: 1fr;
        }
    }
    
    /* Rounded Modern Theme */
    .campanhas-section {
        background: #1e1e2d;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid #2d2d3a;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .section-title h2 {
        font-size: 1.3rem;
        color: #fff;
        margin: 0;
        font-weight: 600;
    }
    
    .section-title p {
        color: #8a8a8a;
        font-size: 0.85rem;
        margin: 5px 0 0 0;
    }
    
    .section-filters {
        display: flex;
        gap: 10px;
    }
    
    .section-filters select {
        background: #2d2d3a;
        border: 1px solid #3d3d4a;
        color: #fff;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 0.85rem;
    }
    
    /* Tabela de Campanhas */
    .campanhas-table {
        width: 100%;
    }
    
    .campanhas-table thead th {
        color: #8a8a8a;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        padding: 15px 10px;
        border-bottom: 1px solid #2d2d3a;
    }
    
    .campanhas-table tbody tr {
        border-bottom: 1px solid #2d2d3a;
        transition: background 0.2s;
    }
    
    .campanhas-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }
    
    .campanhas-table tbody td {
        padding: 15px 10px;
        color: #fff;
        font-size: 0.9rem;
        vertical-align: middle;
    }
    
    .campanha-id {
        color: #00d4aa;
        font-weight: 600;
    }
    
    .campanha-data {
        color: #8a8a8a;
        font-size: 0.8rem;
    }
    
    .campanha-msg-preview {
        color: #8a8a8a;
        font-size: 0.8rem;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    .badge-modo {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .badge-modo.unica { background: rgba(0, 123, 255, 0.15); color: #007bff; }
    .badge-modo.balanceado { background: rgba(156, 39, 176, 0.15); color: #9c27b0; }
    
    .badge-status {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .badge-status.enviado { background: rgba(0, 168, 132, 0.15); color: #00d4aa; }
    .badge-status.agendada { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
    .badge-status.em_andamento { background: rgba(0, 123, 255, 0.15); color: #007bff; }
    .badge-status.pausada { background: rgba(255, 152, 0, 0.15); color: #ff9800; }
    .badge-status.rascunho { background: rgba(108, 117, 125, 0.15); color: #6c757d; }
    .badge-status.concluida { background: rgba(0, 168, 132, 0.15); color: #00d4aa; }
    
    .btn-action {
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 0.8rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-action.danger {
        background: rgba(220, 53, 69, 0.15);
        color: #dc3545;
    }
    
    .btn-action.danger:hover {
        background: #dc3545;
        color: #fff;
    }
    
    .btn-action.primary {
        background: rgba(0, 168, 132, 0.15);
        color: #00a884;
    }
    
    .btn-action.primary:hover {
        background: #00a884;
        color: #fff;
    }
    
    .btn-action.warning {
        background: rgba(246, 173, 85, 0.15);
        color: #f6ad55;
    }
    
    .btn-action.warning:hover {
        background: #f6ad55;
        color: #fff;
    }
    
    /* Theme Support */
    body:not(.dark-mode) .campanhas-container,
    body:not(.dark-mode) .stat-card,
    body:not(.dark-mode) .campanhas-table,
    body:not(.dark-mode) .sidebar-card {
        background: #fff;
        border-color: #e0e0e0;
    }
    
    body:not(.dark-mode) .page-title h1,
    body:not(.dark-mode) .stat-label,
    body:not(.dark-mode) th,
    body:not(.dark-mode) td,
    body:not(.dark-mode) .sidebar-card h3 {
        color: #333 !important;
    }
    
    body:not(.dark-mode) .page-title p,
    body:not(.dark-mode) .sidebar-card p,
    body:not(.dark-mode) .empty-state p {
        color: #666 !important;
    }
    
    /* Sidebar */
    .sidebar-content {
        display: flex;
        flex-direction: column;
        gap: 25px;
    }
    
    .sidebar-card {
        background: #1e1e2d;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid #2d2d3a;
    }
    
    .sidebar-card h3 {
        font-size: 1.1rem;
        color: #fff;
        margin: 0 0 20px 0;
        font-weight: 600;
    }
    
    .sidebar-card p {
        color: #8a8a8a;
        font-size: 0.85rem;
        margin: 0;
    }
    
    .quick-actions {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 15px;
        background: #2d2d3a;
        border: 1px solid #3d3d4a;
        border-radius: 15px;
        color: #fff;
        text-decoration: none;
        transition: all 0.2s;
    }
    
    .quick-action-btn:hover {
        background: #3d3d4a;
        border-color: #00a884;
        color: #fff;
        text-decoration: none;
    }
    
    .quick-action-btn i {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }
    
    .quick-action-btn i.green { background: rgba(0, 168, 132, 0.15); color: #00a884; }
    .quick-action-btn i.blue { background: rgba(0, 123, 255, 0.15); color: #007bff; }
    .quick-action-btn i.purple { background: rgba(156, 39, 176, 0.15); color: #9c27b0; }
    .quick-action-btn i.orange { background: rgba(255, 152, 0, 0.15); color: #ff9800; }
    .quick-action-btn i.red { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
    
    .quick-action-btn span {
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state i {
        font-size: 4rem;
        color: #3d3d4a;
        margin-bottom: 20px;
    }
    
    .empty-state h4 {
        color: #fff;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #8a8a8a;
    }
    
    /* Paginação e Controles */
    .table-controls {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .table-controls .show-entries {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #8a8a8a;
        font-size: 0.85rem;
    }
    
    .table-controls .show-entries select {
        background: #2d2d3a;
        border: 1px solid #3d3d4a;
        color: #fff;
        padding: 5px 10px;
        border-radius: 5px;
    }
    
    .table-controls .search-box {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .table-controls .search-box input {
        background: #2d2d3a;
        border: 1px solid #3d3d4a;
        color: #fff;
        padding: 8px 15px;
        border-radius: 8px;
        width: 200px;
    }
    
    .table-controls .search-box input::placeholder {
        color: #6a6a7a;
    }
    
    /* Toast Notifications */
    .toast-container {
        position: fixed;
        top: 90px;
        right: 20px;
        z-index: 9999;
    }
    
    .toast-notification {
        background: #1e1e2d;
        border: 1px solid #2d2d3a;
        border-radius: 10px;
        padding: 15px 20px;
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        animation: slideIn 0.3s ease;
    }
    
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    .toast-notification.success i { color: #00d4aa; }
    .toast-notification.error i { color: #dc3545; }
    .toast-notification.warning i { color: #ffc107; }
    
    .toast-notification span { color: #fff; }
</style>

<div class="slim-mainpanel">
    <div class="container-fluid campanhas-container">
        
        <!-- Header da Página -->
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fab fa-whatsapp mr-2" style="color: #00a884;"></i> Campanhas WhatsApp</h1>
                <p>Acompanhe seus agendamentos, monitore execuções e organize próximos disparos.</p>
            </div>
            
            <?php if ($countCampanhas < 3): ?>
            <a href="nova_campanha" class="btn-nova-campanha">
                <i class="fas fa-plus"></i>
                Nova Campanha
            </a>
            <?php else: ?>
            <button class="btn-nova-campanha disabled" onclick="alert('Limite de 3 campanhas atingido. Delete uma campanha para criar outra.');">
                <i class="fas fa-ban"></i>
                Limite Atingido (3/3)
            </button>
            <?php endif; ?>
        </div>
        
        <!-- Cards de Estatísticas -->
        <div class="stats-row">
            <div class="stat-card">
                <span class="badge-label badge-realtime">Tempo real</span>
                <div class="stat-icon green">
                    <i class="fab fa-whatsapp"></i>
                </div>
                <div class="stat-value green"><?= $stats->executando ?? 0 ?></div>
                <div class="stat-label">Campanhas executando agora</div>
            </div>
            
            <div class="stat-card">
                <span class="badge-label badge-fila">Fila</span>
                <div class="stat-icon yellow">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-value yellow"><?= $stats->aguardando ?? 0 ?></div>
                <div class="stat-label">Campanhas aguardando envio</div>
            </div>
            
            <div class="stat-card">
                <span class="badge-label badge-historico">Histórico</span>
                <div class="stat-icon gray">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value gray"><?= $stats->concluidas ?? 0 ?></div>
                <div class="stat-label">Campanhas concluídas</div>
            </div>
            
            <div class="stat-card">
                <span class="badge-label badge-volume">Volume</span>
                <div class="stat-icon blue">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value blue"><?= $stats->total_destinatarios ?? 0 ?></div>
                <div class="stat-label">Destinatários previstos</div>
            </div>
        </div>
        
        <!-- Conteúdo Principal -->
        <div class="main-content-row">
            
            <!-- Seção de Campanhas -->
            <div class="campanhas-section">
                <div class="section-header">
                    <div class="section-title">
                        <h2>Campanhas agendadas</h2>
                        <p>Gerencie seus envios programados e acompanhe o status de cada campanha.</p>
                    </div>
                    <div class="section-filters">
                        <select id="filterStatus">
                            <option value="">Todos status</option>
                            <option value="rascunho">Rascunho</option>
                            <option value="agendada">Agendada</option>
                            <option value="em_andamento">Em Andamento</option>
                            <option value="concluida">Concluída</option>
                            <option value="pausada">Pausada</option>
                        </select>
                        <select id="filterModo">
                            <option value="">Todos modos</option>
                            <option value="unica">Instância única</option>
                            <option value="balanceado">Balanceado</option>
                        </select>
                    </div>
                </div>
                
                <div class="table-controls">
                    <div class="show-entries">
                        Mostrar 
                        <select id="entriesPerPage">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                        </select>
                        registros
                    </div>
                    <div class="search-box">
                        <label>Buscar:</label>
                        <input type="text" id="searchCampanha" placeholder="">
                    </div>
                </div>
                
                <?php if ($campanhas->rowCount() > 0): ?>
                <div class="table-responsive">
                    <table class="campanhas-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Data / Hora</th>
                                <th>Mensagem</th>
                                <th>Clientes</th>
                                <th>Modo</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($campanha = $campanhas->fetch(PDO::FETCH_OBJ)): ?>
                            <tr data-id="<?= $campanha->id ?>" data-status="<?= $campanha->status ?>" data-modo="<?= $campanha->modo_envio ?>">
                                <td>
                                    <span class="campanha-id">#<?= str_pad($campanha->id, 4, '0', STR_PAD_LEFT) ?></span>
                                </td>
                                <td>
                                    <div>
                                        <?= $campanha->data_agendamento ? date('d/m/Y', strtotime($campanha->data_agendamento)) : date('d/m/Y', strtotime($campanha->criado_em)) ?>
                                    </div>
                                    <div class="campanha-data">
                                        <?= $campanha->data_agendamento ? date('H:i', strtotime($campanha->data_agendamento)) : date('H:i', strtotime($campanha->criado_em)) ?>
                                    </div>
                                </td>
                                <td>
                                    <div><?= $campanha->nome ?: 'Sem Título' ?></div>
                                    <div class="campanha-msg-preview"><?= htmlspecialchars(substr($campanha->mensagem ?? '', 0, 50)) ?>...</div>
                                </td>
                                <td>
                                    <strong><?= $campanha->total_contatos ?></strong>
                                    <span class="campanha-data">destinatários</span>
                                </td>
                                <td>
                                    <span class="badge-modo <?= $campanha->modo_envio ?>">
                                        <?= $campanha->modo_envio == 'unica' ? 'Instância única' : 'Balanceado' ?>
                                    </span>
                                    <?php if ($campanha->instancia_nome): ?>
                                    <div class="campanha-data mt-1"><?= htmlspecialchars($campanha->instancia_nome) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusLabels = [
                                        'rascunho' => 'Rascunho',
                                        'agendada' => 'Agendada',
                                        'em_andamento' => 'Em Andamento',
                                        'concluida' => 'Enviado',
                                        'pausada' => 'Pausada',
                                        'cancelada' => 'Cancelada'
                                    ];
                                    ?>
                                    <span class="badge-status <?= $campanha->status ?>">
                                        <?= $statusLabels[$campanha->status] ?? $campanha->status ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <?php if ($campanha->status == 'em_andamento'): ?>
                                        <a href="monitor_campanha?id=<?= $campanha->id ?>" class="btn-action primary" title="Monitorar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <?php elseif ($campanha->status == 'rascunho' || $campanha->status == 'agendada'): ?>
                                        <a href="editar_campanha?id=<?= $campanha->id ?>" class="btn-action warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <?php endif; ?>
                                        <button class="btn-action danger btn-excluir" data-id="<?= $campanha->id ?>" title="Excluir">
                                            <i class="fas fa-trash"></i> Excluir
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="empty-state">
                    <i class="fab fa-whatsapp"></i>
                    <h4>Nenhuma campanha encontrada</h4>
                    <p>Crie sua primeira campanha de WhatsApp Marketing para começar.</p>
                    <?php if ($countCampanhas < 3): ?>
                    <a href="nova_campanha" class="btn-nova-campanha mt-3" style="display: inline-flex;">
                        <i class="fas fa-plus"></i> Criar Campanha
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="sidebar-content">
                <!-- Próximo Disparo -->
                <div class="sidebar-card">
                    <h3>Próximo disparo</h3>
                    <?php
                    $stmtProximo = $connect->prepare("
                        SELECT * FROM campanhas 
                        WHERE id_usuario = ? 
                        AND status = 'agendada' 
                        AND data_agendamento > NOW()
                        ORDER BY data_agendamento ASC 
                        LIMIT 1
                    ");
                    $stmtProximo->execute([$cod_id]);
                    $proximoDisparo = $stmtProximo->fetch(PDO::FETCH_OBJ);
                    ?>
                    <?php if ($proximoDisparo): ?>
                    <div style="background: #2d2d3a; padding: 15px; border-radius: 10px; margin-bottom: 15px;">
                        <div style="color: #00d4aa; font-weight: 600; margin-bottom: 5px;">
                            <?= $proximoDisparo->nome ?>
                        </div>
                        <div style="color: #8a8a8a; font-size: 0.85rem;">
                            <i class="far fa-calendar-alt mr-1"></i>
                            <?= date('d/m/Y H:i', strtotime($proximoDisparo->data_agendamento)) ?>
                        </div>
                        <div style="color: #fff; margin-top: 10px;">
                            <?= $proximoDisparo->total_contatos ?> destinatários
                        </div>
                    </div>
                    <?php else: ?>
                    <p>Nenhum disparo futuro encontrado. Agende uma nova campanha para ver o resumo aqui.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Ações Rápidas -->
                <div class="sidebar-card">
                    <h3>Ações rápidas</h3>
                    <div class="quick-actions">
                        <a href="monitor_campanha" class="quick-action-btn">
                            <i class="fas fa-eye green"></i>
                            <span>Monitorar envios</span>
                        </a>
                        <a href="contatos_campanha" class="quick-action-btn">
                            <i class="fas fa-address-book blue"></i>
                            <span>Contatos</span>
                        </a>
                        <a href="whatsapp" class="quick-action-btn">
                            <i class="fab fa-whatsapp purple"></i>
                            <span>Gerenciar Instâncias</span>
                        </a>
                        <a href="relatorio_campanha" class="quick-action-btn">
                            <i class="fas fa-chart-bar orange"></i>
                            <span>Relatório de envios</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="modalExcluir" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background: #1e1e2d; border: 1px solid #2d2d3a; border-radius: 20px;">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white"><i class="fas fa-exclamation-triangle text-danger mr-2"></i>Confirmar Exclusão</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash fa-4x text-danger mb-3"></i>
                <p class="text-white">Tem certeza que deseja excluir esta campanha?</p>
                <p class="text-muted">Esta ação não pode ser desfeita. Todos os arquivos de mídia serão removidos.</p>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 10px;">Cancelar</button>
                <button type="button" class="btn btn-danger" id="confirmarExclusao" style="border-radius: 10px;">
                    <i class="fas fa-trash mr-1"></i> Excluir Campanha
                </button>
            </div>
        </div>
    </div>
</div>

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../js/slim.js"></script>

<script>
let campanhaIdParaExcluir = null;

// Filtros
$('#filterStatus, #filterModo').on('change', function() {
    filterTable();
});

$('#searchCampanha').on('keyup', function() {
    filterTable();
});

function filterTable() {
    const status = $('#filterStatus').val();
    const modo = $('#filterModo').val();
    const search = $('#searchCampanha').val().toLowerCase();
    
    $('.campanhas-table tbody tr').each(function() {
        const rowStatus = $(this).data('status');
        const rowModo = $(this).data('modo');
        const text = $(this).text().toLowerCase();
        
        let show = true;
        
        if (status && rowStatus !== status) show = false;
        if (modo && rowModo !== modo) show = false;
        if (search && text.indexOf(search) === -1) show = false;
        
        $(this).toggle(show);
    });
}

// Excluir Campanha
$('.btn-excluir').on('click', function() {
    campanhaIdParaExcluir = $(this).data('id');
    $('#modalExcluir').modal('show');
});

$('#confirmarExclusao').on('click', function() {
    if (!campanhaIdParaExcluir) return;
    
    $.ajax({
        url: 'classes/campanhas_exe.php',
        type: 'POST',
        data: {
            acao: 'excluir',
            id: campanhaIdParaExcluir
        },
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                showToast('Campanha excluída com sucesso!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showToast(resp.message || 'Erro ao excluir campanha', 'error');
            }
        },
        error: function() {
            showToast('Erro ao processar requisição', 'error');
        }
    });
    
    $('#modalExcluir').modal('hide');
});

// Toast
function showToast(message, type) {
    const toast = $(`
        <div class="toast-notification ${type}">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'times-circle' : 'exclamation-circle'}"></i>
            <span>${message}</span>
        </div>
    `);
    
    $('#toastContainer').append(toast);
    
    setTimeout(() => {
        toast.fadeOut(300, function() { $(this).remove(); });
    }, 3000);
}

// Verificar mensagens da URL
<?php if (isset($_GET['sucesso'])): ?>
showToast('<?= htmlspecialchars($_GET['sucesso']) ?>', 'success');
<?php endif; ?>

<?php if (isset($_GET['erro'])): ?>
showToast('<?= htmlspecialchars($_GET['erro']) ?>', 'error');
<?php endif; ?>
</script>

</body>
</html>
<?php ob_end_flush(); ?>
