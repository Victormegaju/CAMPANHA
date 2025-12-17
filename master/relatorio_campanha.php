<?php
require_once "topo.php";
require_once "menu.php";

// Buscar campanhas concluídas
$stmtCampanhas = $connect->prepare("
    SELECT * FROM campanhas 
    WHERE id_usuario = ? 
    ORDER BY finalizado_em DESC, criado_em DESC
");
$stmtCampanhas->execute([$cod_id]);
$campanhas = $stmtCampanhas;

// Estatísticas gerais
$stmtStats = $connect->prepare("
    SELECT 
        COUNT(*) as total_campanhas,
        SUM(total_contatos) as total_destinatarios,
        SUM(enviados) as total_enviados,
        SUM(falhas) as total_falhas
    FROM campanhas 
    WHERE id_usuario = ? AND status != 'cancelada'
");
$stmtStats->execute([$cod_id]);
$statsGeral = $stmtStats->fetch(PDO::FETCH_OBJ);
?>

<style>
    .relatorio-container { padding: 20px 0; }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .page-title h1 { font-size: 1.8rem; font-weight: 700; color: #fff; margin: 0; }
    .page-title p { color: #8a8a8a; margin: 5px 0 0 0; }
    
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    @media (max-width: 992px) { .stats-row { grid-template-columns: repeat(2, 1fr); } }
    
    .stat-card {
        background: #1e1e2d;
        border-radius: 15px;
        padding: 25px;
        border: 1px solid #2d2d3a;
    }
    
    .stat-card .value { font-size: 2.5rem; font-weight: 700; margin-bottom: 5px; }
    .stat-card .value.green { color: #00d4aa; }
    .stat-card .value.blue { color: #007bff; }
    .stat-card .value.red { color: #dc3545; }
    .stat-card .value.purple { color: #9c27b0; }
    .stat-card .label { color: #8a8a8a; font-size: 0.85rem; }
    
    .relatorio-section {
        background: #1e1e2d;
        border-radius: 15px;
        padding: 25px;
        border: 1px solid #2d2d3a;
    }
    
    .relatorio-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .relatorio-table thead th {
        color: #8a8a8a;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        padding: 15px 10px;
        border-bottom: 1px solid #2d2d3a;
        text-align: left;
    }
    
    .relatorio-table tbody tr {
        border-bottom: 1px solid #2d2d3a;
    }
    
    .relatorio-table tbody tr:hover {
        background: rgba(255, 255, 255, 0.02);
    }
    
    .relatorio-table tbody td {
        padding: 15px 10px;
        color: #fff;
        font-size: 0.9rem;
    }
    
    .taxa-sucesso {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .taxa-sucesso.alta { background: rgba(0, 168, 132, 0.15); color: #00d4aa; }
    .taxa-sucesso.media { background: rgba(255, 193, 7, 0.15); color: #ffc107; }
    .taxa-sucesso.baixa { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
    
    .btn-export {
        background: #007bff;
        color: #fff;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .btn-export:hover { background: #0056b3; }
    
    .btn-details {
        background: rgba(0, 168, 132, 0.15);
        color: #00a884;
        padding: 8px 15px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 0.8rem;
    }
    
    .btn-details:hover { background: rgba(0, 168, 132, 0.3); }
</style>

<div class="slim-mainpanel">
    <div class="container-fluid relatorio-container">
        
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fas fa-chart-bar mr-2" style="color: #00a884;"></i> Relatório de Campanhas</h1>
                <p>Análise detalhada de todas as campanhas realizadas</p>
            </div>
        </div>
        
        <!-- Stats Gerais -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="value purple"><?= $statsGeral->total_campanhas ?? 0 ?></div>
                <div class="label">Total de Campanhas</div>
            </div>
            <div class="stat-card">
                <div class="value blue"><?= $statsGeral->total_destinatarios ?? 0 ?></div>
                <div class="label">Total Destinatários</div>
            </div>
            <div class="stat-card">
                <div class="value green"><?= $statsGeral->total_enviados ?? 0 ?></div>
                <div class="label">Mensagens Enviadas</div>
            </div>
            <div class="stat-card">
                <div class="value red"><?= $statsGeral->total_falhas ?? 0 ?></div>
                <div class="label">Falhas</div>
            </div>
        </div>
        
        <!-- Tabela de Relatórios -->
        <div class="relatorio-section">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="text-white mb-0">Histórico de Campanhas</h4>
            </div>
            
            <div class="table-responsive">
                <table class="relatorio-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Data</th>
                            <th>Destinatários</th>
                            <th>Enviados</th>
                            <th>Falhas</th>
                            <th>Taxa de Sucesso</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($campanha = $campanhas->fetch(PDO::FETCH_OBJ)): 
                            $taxa = $campanha->total_contatos > 0 
                                ? round(($campanha->enviados / $campanha->total_contatos) * 100, 1) 
                                : 0;
                            $taxaClass = $taxa >= 80 ? 'alta' : ($taxa >= 50 ? 'media' : 'baixa');
                        ?>
                        <tr>
                            <td><span style="color: #00a884; font-weight: 600;">#<?= str_pad($campanha->id, 4, '0', STR_PAD_LEFT) ?></span></td>
                            <td><?= htmlspecialchars($campanha->nome) ?></td>
                            <td>
                                <?= $campanha->finalizado_em 
                                    ? date('d/m/Y H:i', strtotime($campanha->finalizado_em)) 
                                    : date('d/m/Y H:i', strtotime($campanha->criado_em)) ?>
                            </td>
                            <td><?= $campanha->total_contatos ?></td>
                            <td style="color: #00d4aa;"><?= $campanha->enviados ?></td>
                            <td style="color: #dc3545;"><?= $campanha->falhas ?></td>
                            <td>
                                <span class="taxa-sucesso <?= $taxaClass ?>"><?= $taxa ?>%</span>
                            </td>
                            <td>
                                <span class="badge badge-<?= $campanha->status == 'concluida' ? 'success' : ($campanha->status == 'em_andamento' ? 'primary' : 'secondary') ?>">
                                    <?= ucfirst(str_replace('_', ' ', $campanha->status)) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn-details" onclick="verDetalhes(<?= $campanha->id ?>)">
                                    <i class="fas fa-eye"></i> Detalhes
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Detalhes -->
<div class="modal fade" id="modalDetalhes" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="background: #1e1e2d; border: 1px solid #2d2d3a;">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white">Detalhes da Campanha</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body" id="detalhesContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-success"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../js/slim.js"></script>

<script>
function verDetalhes(id) {
    $('#modalDetalhes').modal('show');
    
    $.ajax({
        url: 'classes/campanhas_exe.php?acao=status&id=' + id,
        type: 'GET',
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                let html = `
                    <div class="row mb-4">
                        <div class="col-md-3 text-center">
                            <div style="font-size: 2rem; font-weight: 700; color: #007bff;">${resp.stats.total || 0}</div>
                            <div style="color: #8a8a8a; font-size: 0.85rem;">Total</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div style="font-size: 2rem; font-weight: 700; color: #00d4aa;">${resp.stats.enviados || 0}</div>
                            <div style="color: #8a8a8a; font-size: 0.85rem;">Enviados</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div style="font-size: 2rem; font-weight: 700; color: #dc3545;">${resp.stats.falhas || 0}</div>
                            <div style="color: #8a8a8a; font-size: 0.85rem;">Falhas</div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div style="font-size: 2rem; font-weight: 700; color: #8a8a8a;">${resp.stats.pendentes || 0}</div>
                            <div style="color: #8a8a8a; font-size: 0.85rem;">Pendentes</div>
                        </div>
                    </div>
                    <h6 class="text-white mb-3">Últimos Logs</h6>
                    <div style="max-height: 300px; overflow-y: auto;">
                `;
                
                resp.logs.forEach(function(log) {
                    let icon = log.tipo == 'sucesso' ? 'check text-success' : (log.tipo == 'erro' ? 'times text-danger' : 'info-circle text-info');
                    html += `
                        <div style="background: #2d2d3a; padding: 10px 15px; border-radius: 8px; margin-bottom: 8px;">
                            <i class="fas fa-${icon} mr-2"></i>
                            <span class="text-white">${log.mensagem}</span>
                            <span class="float-right" style="color: #6a6a7a; font-size: 0.8rem;">${log.criado_em}</span>
                        </div>
                    `;
                });
                
                html += '</div>';
                $('#detalhesContent').html(html);
            } else {
                $('#detalhesContent').html('<p class="text-center text-muted">Erro ao carregar detalhes</p>');
            }
        }
    });
}
</script>

</body>
</html>
<?php ob_end_flush(); ?>
