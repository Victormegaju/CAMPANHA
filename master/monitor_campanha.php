<?php
require_once "topo.php";
require_once "menu.php";

$campanha_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$campanha = null;
$contatos = [];

if ($campanha_id > 0) {
    $stmt = $connect->prepare("SELECT * FROM campanhas WHERE id = ? AND id_usuario = ?");
    $stmt->execute([$campanha_id, $cod_id]);
    $campanha = $stmt->fetch(PDO::FETCH_OBJ);
    
    if ($campanha) {
        $stmtContatos = $connect->prepare("SELECT * FROM campanha_contatos WHERE campanha_id = ? ORDER BY enviado_em DESC, id ASC");
        $stmtContatos->execute([$campanha_id]);
        $contatos = $stmtContatos->fetchAll(PDO::FETCH_OBJ);
    }
}

// Buscar campanhas em andamento se nenhum ID específico
$campanhasAtivas = [];
if (!$campanha_id) {
    $campanhasAtivas = $connect->query("SELECT * FROM campanhas WHERE id_usuario = ? AND status IN ('em_andamento', 'agendada') ORDER BY data_agendamento ASC")->fetchAll(PDO::FETCH_OBJ);
}
?>

<style>
    .monitor-container { padding: 20px 0; }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .page-title h1 { font-size: 1.8rem; font-weight: 700; color: #fff; margin: 0; }
    .page-title p { color: #8a8a8a; margin: 5px 0 0 0; }
    
    .btn-back {
        background: #3d3d4a;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-back:hover { background: #4d4d5a; color: #fff; text-decoration: none; }
    
    /* Progress Card */
    .progress-card {
        background: #1e1e2d;
        border-radius: 15px;
        padding: 30px;
        border: 1px solid #2d2d3a;
        margin-bottom: 25px;
    }
    
    .progress-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .progress-header h3 { color: #fff; margin: 0; }
    
    .status-badge {
        padding: 8px 20px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .status-badge.em_andamento { background: rgba(0, 123, 255, 0.15); color: #007bff; }
    .status-badge.agendada { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
    .status-badge.concluida { background: rgba(0, 168, 132, 0.15); color: #00d4aa; }
    .status-badge.pausada { background: rgba(255, 152, 0, 0.15); color: #ff9800; }
    
    .progress-bar-container {
        background: #2d2d3a;
        border-radius: 10px;
        height: 20px;
        overflow: hidden;
        margin-bottom: 15px;
    }
    
    .progress-bar-fill {
        height: 100%;
        background: linear-gradient(90deg, #00a884 0%, #00d4aa 100%);
        border-radius: 10px;
        transition: width 0.5s ease;
    }
    
    .progress-stats {
        display: flex;
        justify-content: space-between;
        color: #8a8a8a;
        font-size: 0.9rem;
    }
    
    .progress-stats .highlight { color: #00d4aa; font-weight: 600; }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 25px;
    }
    
    @media (max-width: 992px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 576px) { .stats-grid { grid-template-columns: 1fr; } }
    
    .stat-mini {
        background: #1e1e2d;
        border-radius: 12px;
        padding: 20px;
        border: 1px solid #2d2d3a;
        text-align: center;
    }
    
    .stat-mini .value { font-size: 2rem; font-weight: 700; margin-bottom: 5px; }
    .stat-mini .value.green { color: #00d4aa; }
    .stat-mini .value.blue { color: #007bff; }
    .stat-mini .value.red { color: #dc3545; }
    .stat-mini .value.gray { color: #8a8a8a; }
    .stat-mini .label { color: #8a8a8a; font-size: 0.85rem; }
    
    /* Controls */
    .controls-section {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
    }
    
    .btn-control {
        padding: 12px 25px;
        border-radius: 8px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    
    .btn-control.pause { background: #ff9800; color: #fff; }
    .btn-control.resume { background: #00a884; color: #fff; }
    .btn-control.stop { background: #dc3545; color: #fff; }
    .btn-control:hover { transform: translateY(-2px); }
    
    /* Log Section */
    .log-section {
        background: #1e1e2d;
        border-radius: 15px;
        padding: 25px;
        border: 1px solid #2d2d3a;
    }
    
    .log-section h4 { color: #fff; margin: 0 0 20px 0; }
    
    .log-list {
        max-height: 400px;
        overflow-y: auto;
    }
    
    .log-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 12px 15px;
        background: #2d2d3a;
        border-radius: 8px;
        margin-bottom: 8px;
    }
    
    .log-item .icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }
    
    .log-item .icon.success { background: rgba(0, 168, 132, 0.15); color: #00d4aa; }
    .log-item .icon.error { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
    .log-item .icon.info { background: rgba(0, 123, 255, 0.15); color: #007bff; }
    .log-item .icon.pending { background: rgba(108, 117, 125, 0.15); color: #6c757d; }
    
    .log-item .info { flex: 1; }
    .log-item .info .phone { color: #fff; font-weight: 500; }
    .log-item .info .status { color: #8a8a8a; font-size: 0.8rem; }
    .log-item .time { color: #6a6a7a; font-size: 0.8rem; }
    
    /* Campaign List (when no ID) */
    .campaign-list-item {
        background: #2d2d3a;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 15px;
        border: 1px solid #3d3d4a;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .campaign-list-item:hover { border-color: #00a884; }
    
    .campaign-list-item .info h4 { color: #fff; margin: 0 0 5px 0; }
    .campaign-list-item .info span { color: #8a8a8a; font-size: 0.85rem; }
    
    .btn-monitor {
        background: #00a884;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
    }
    
    .btn-monitor:hover { background: #00d4aa; color: #fff; text-decoration: none; }
</style>

<div class="slim-mainpanel">
    <div class="container-fluid monitor-container">
        
        <!-- Header -->
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fas fa-chart-line mr-2" style="color: #00a884;"></i> Monitor de Campanhas</h1>
                <p>Acompanhe em tempo real o status dos envios</p>
            </div>
            <a href="campanhas" class="btn-back">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
        
        <?php if ($campanha): ?>
        
        <!-- Progress Card -->
        <div class="progress-card">
            <div class="progress-header">
                <h3><?= htmlspecialchars($campanha->nome) ?></h3>
                <span class="status-badge <?= $campanha->status ?>"><?= ucfirst(str_replace('_', ' ', $campanha->status)) ?></span>
            </div>
            
            <?php 
            $percentual = $campanha->total_contatos > 0 ? ($campanha->enviados / $campanha->total_contatos) * 100 : 0;
            ?>
            <div class="progress-bar-container">
                <div class="progress-bar-fill" style="width: <?= $percentual ?>%" id="progressBar"></div>
            </div>
            
            <div class="progress-stats">
                <span><span class="highlight" id="enviados"><?= $campanha->enviados ?></span> de <?= $campanha->total_contatos ?> enviados</span>
                <span>Delay: <?= $campanha->delay_segundos ?>s entre mensagens</span>
            </div>
        </div>
        
        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-mini">
                <div class="value green" id="statEnviados"><?= $campanha->enviados ?></div>
                <div class="label">Enviados</div>
            </div>
            <div class="stat-mini">
                <div class="value blue" id="statEntregues"><?= $campanha->entregues ?></div>
                <div class="label">Entregues</div>
            </div>
            <div class="stat-mini">
                <div class="value red" id="statFalhas"><?= $campanha->falhas ?></div>
                <div class="label">Falhas</div>
            </div>
            <div class="stat-mini">
                <div class="value gray" id="statPendentes"><?= $campanha->total_contatos - $campanha->enviados - $campanha->falhas ?></div>
                <div class="label">Pendentes</div>
            </div>
        </div>
        
        <!-- Controls -->
        <?php if ($campanha->status == 'em_andamento'): ?>
        <div class="controls-section">
            <button class="btn-control pause" onclick="pausarCampanha(<?= $campanha->id ?>)">
                <i class="fas fa-pause"></i> Pausar
            </button>
            <button class="btn-control stop" onclick="pararCampanha(<?= $campanha->id ?>)">
                <i class="fas fa-stop"></i> Parar Campanha
            </button>
        </div>
        <?php elseif ($campanha->status == 'pausada'): ?>
        <div class="controls-section">
            <button class="btn-control resume" onclick="retomarCampanha(<?= $campanha->id ?>)">
                <i class="fas fa-play"></i> Retomar
            </button>
        </div>
        <?php endif; ?>
        
        <!-- Log Section -->
        <div class="log-section">
            <h4><i class="fas fa-list mr-2"></i>Log de Envios em Tempo Real</h4>
            <div class="log-list" id="logList">
                <?php foreach ($contatos as $contato): ?>
                <div class="log-item">
                    <div class="icon <?= $contato->status_envio == 'enviado' || $contato->status_envio == 'entregue' ? 'success' : ($contato->status_envio == 'falha' ? 'error' : 'pending') ?>">
                        <i class="fas fa-<?= $contato->status_envio == 'enviado' || $contato->status_envio == 'entregue' ? 'check' : ($contato->status_envio == 'falha' ? 'times' : 'clock') ?>"></i>
                    </div>
                    <div class="info">
                        <div class="phone"><?= $contato->telefone ?> <?= $contato->nome ? '- ' . htmlspecialchars($contato->nome) : '' ?></div>
                        <div class="status">
                            <?php
                            if ($contato->status_envio == 'enviado') echo 'Mensagem enviada';
                            elseif ($contato->status_envio == 'entregue') echo 'Entregue';
                            elseif ($contato->status_envio == 'lido') echo 'Lido';
                            elseif ($contato->status_envio == 'falha') echo 'Falha: ' . ($contato->mensagem_erro ?? 'Erro desconhecido');
                            else echo 'Aguardando envio';
                            ?>
                        </div>
                    </div>
                    <div class="time">
                        <?= $contato->enviado_em ? date('H:i:s', strtotime($contato->enviado_em)) : '--:--:--' ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php elseif (count($campanhasAtivas) > 0): ?>
        
        <!-- Lista de Campanhas Ativas -->
        <div class="log-section">
            <h4>Campanhas em Execução ou Agendadas</h4>
            <?php foreach ($campanhasAtivas as $camp): ?>
            <div class="campaign-list-item">
                <div class="info">
                    <h4><?= htmlspecialchars($camp->nome) ?></h4>
                    <span>
                        <i class="fas fa-users"></i> <?= $camp->total_contatos ?> contatos | 
                        <i class="fas fa-paper-plane"></i> <?= $camp->enviados ?> enviados |
                        Status: <strong><?= ucfirst(str_replace('_', ' ', $camp->status)) ?></strong>
                    </span>
                </div>
                <a href="monitor_campanha?id=<?= $camp->id ?>" class="btn-monitor">
                    <i class="fas fa-eye"></i> Monitorar
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        
        <?php else: ?>
        
        <div class="log-section text-center py-5">
            <i class="fas fa-broadcast-tower fa-4x text-muted mb-3"></i>
            <h4 class="text-white">Nenhuma campanha em execução</h4>
            <p class="text-muted">Crie e agende uma campanha para monitorar aqui.</p>
            <a href="nova_campanha" class="btn btn-success mt-3">
                <i class="fas fa-plus"></i> Criar Campanha
            </a>
        </div>
        
        <?php endif; ?>
        
    </div>
</div>

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../js/slim.js"></script>

<script>
<?php if ($campanha && $campanha->status == 'em_andamento'): ?>
// Atualizar status a cada 5 segundos
setInterval(function() {
    $.ajax({
        url: 'classes/campanhas_exe.php?acao=status&id=<?= $campanha_id ?>',
        type: 'GET',
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                const camp = resp.campanha;
                const stats = resp.stats;
                
                // Atualizar stats
                $('#statEnviados').text(stats.enviados || 0);
                $('#statFalhas').text(stats.falhas || 0);
                $('#statPendentes').text(stats.pendentes || 0);
                $('#enviados').text(stats.enviados || 0);
                
                // Atualizar barra de progresso
                const percentual = camp.total_contatos > 0 ? ((stats.enviados || 0) / camp.total_contatos) * 100 : 0;
                $('#progressBar').css('width', percentual + '%');
                
                // Se concluída, recarregar
                if (camp.status == 'concluida') {
                    location.reload();
                }
            }
        }
    });
}, 5000);
<?php endif; ?>

function pausarCampanha(id) {
    if (!confirm('Pausar esta campanha?')) return;
    
    $.post('classes/campanhas_exe.php', { acao: 'pausar', id: id }, function(resp) {
        if (resp.success) location.reload();
        else alert(resp.message);
    }, 'json');
}

function retomarCampanha(id) {
    $.post('classes/campanhas_exe.php', { acao: 'retomar', id: id }, function(resp) {
        if (resp.success) location.reload();
        else alert(resp.message);
    }, 'json');
}

function pararCampanha(id) {
    if (!confirm('Tem certeza que deseja PARAR esta campanha? Os envios pendentes serão cancelados.')) return;
    
    $.post('classes/campanhas_exe.php', { acao: 'cancelar', id: id }, function(resp) {
        if (resp.success) location.href = 'campanhas';
        else alert(resp.message);
    }, 'json');
}
</script>

</body>
</html>
<?php ob_end_flush(); ?>
