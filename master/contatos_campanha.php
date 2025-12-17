<?php
require_once "topo.php";
require_once "menu.php";

// Buscar contatos do usuário
$stmtWA = $connect->prepare("SELECT * FROM contatos_whatsapp WHERE id_usuario = ? AND origem IN ('conversa', 'salvo') ORDER BY origem, nome ASC");
$stmtWA->execute([$cod_id]);
$contatosWhatsApp = $stmtWA;

$stmtManuais = $connect->prepare("SELECT * FROM contatos_whatsapp WHERE id_usuario = ? AND origem = 'manual' ORDER BY nome ASC");
$stmtManuais->execute([$cod_id]);
$contatosManuais = $stmtManuais;

// Contar totais
$stmtTotalWA = $connect->prepare("SELECT COUNT(*) FROM contatos_whatsapp WHERE id_usuario = ? AND origem IN ('conversa', 'salvo')");
$stmtTotalWA->execute([$cod_id]);
$totalWA = $stmtTotalWA->fetchColumn();

$stmtTotalManuais = $connect->prepare("SELECT COUNT(*) FROM contatos_whatsapp WHERE id_usuario = ? AND origem = 'manual'");
$stmtTotalManuais->execute([$cod_id]);
$totalManuais = $stmtTotalManuais->fetchColumn();

$stmtTotalSalvos = $connect->prepare("SELECT COUNT(*) FROM contatos_whatsapp WHERE id_usuario = ? AND origem = 'salvo'");
$stmtTotalSalvos->execute([$cod_id]);
$totalSalvos = $stmtTotalSalvos->fetchColumn();

$stmtTotalConversas = $connect->prepare("SELECT COUNT(*) FROM contatos_whatsapp WHERE id_usuario = ? AND origem = 'conversa'");
$stmtTotalConversas->execute([$cod_id]);
$totalConversas = $stmtTotalConversas->fetchColumn();
?>

<style>
    .contatos-container { padding: 20px 0; }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .page-title h1 { font-size: 1.8rem; font-weight: 700; color: #fff; margin: 0; }
    .page-title p { color: #8a8a8a; margin: 5px 0 0 0; font-size: 0.9rem; }
    
    .header-actions { display: flex; gap: 10px; flex-wrap: wrap; }
    
    .btn-action {
        padding: 12px 25px;
        border-radius: 50px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }
    
    .btn-action.primary {
        background: linear-gradient(135deg, #00d4aa 0%, #00a884 100%);
        color: #fff;
    }
    
    .btn-action.secondary {
        background: #007bff;
        color: #fff;
    }
    
    .btn-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
        color: #fff;
        text-decoration: none;
    }
    
    /* Stats */
    .stats-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    
    @media (max-width: 992px) {
        .stats-row { grid-template-columns: repeat(2, 1fr); }
    }
    
    @media (max-width: 576px) {
        .stats-row { grid-template-columns: 1fr; }
    }
    
    /* Modern Rounded Theme */
    .stat-card {
        background: #1e1e2d;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid #2d2d3a;
    }
    
    .stat-card .stat-value {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 5px;
    }
    
    .stat-card .stat-value.green { color: #00d4aa; }
    .stat-card .stat-value.blue { color: #007bff; }
    .stat-card .stat-value.purple { color: #9c27b0; }
    .stat-card .stat-value.orange { color: #ff9800; }
    
    .stat-card .stat-label { color: #8a8a8a; font-size: 0.85rem; }
    
    /* Tabs */
    .contatos-tabs {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
        border-bottom: 1px solid #2d2d3a;
        padding-bottom: 15px;
    }
    
    .tab-btn {
        padding: 10px 25px;
        border-radius: 25px;
        background: #2d2d3a;
        color: #fff;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .tab-btn.active {
        background: #00a884;
    }
    
    .tab-btn:hover {
        background: #3d3d4a;
    }
    
    .tab-btn.active:hover {
        background: #00a884;
    }
    
    /* Content Section - Rounded Theme */
    .contatos-section {
        background: #1e1e2d;
        border-radius: 20px;
        padding: 25px;
        border: 1px solid #2d2d3a;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .search-box input {
        background: #2d2d3a;
        border: 1px solid #3d3d4a;
        color: #fff;
        padding: 10px 20px;
        border-radius: 25px;
        width: 300px;
    }
    
    .search-box input::placeholder { color: #6a6a7a; }
    
    /* Contact List */
    .contatos-list { max-height: 500px; overflow-y: auto; }
    
    .contato-item {
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
    
    .contato-item:hover { border-color: #00a884; }
    
    .contato-info { display: flex; align-items: center; gap: 15px; }
    
    .contato-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #00a884 0%, #00d4aa 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 1.2rem;
    }
    
    .contato-details h4 { color: #fff; font-size: 0.95rem; margin: 0 0 3px 0; }
    .contato-details span { color: #8a8a8a; font-size: 0.8rem; }
    
    .contato-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .contato-badge.salvo { background: rgba(0, 168, 132, 0.15); color: #00a884; }
    .contato-badge.conversa { background: rgba(0, 123, 255, 0.15); color: #007bff; }
    .contato-badge.manual { background: rgba(156, 39, 176, 0.15); color: #9c27b0; }
    
    .contato-actions { display: flex; gap: 8px; }
    
    .btn-icon {
        width: 35px;
        height: 35px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
    }
    
    .btn-icon.edit { background: rgba(0, 123, 255, 0.15); color: #007bff; }
    .btn-icon.delete { background: rgba(220, 53, 69, 0.15); color: #dc3545; }
    
    .btn-icon:hover { transform: scale(1.1); }
    
    /* Tab Content */
    .tab-content { display: none; }
    .tab-content.active { display: block; }
    
    /* Modal - Modern Rounded Centered */
    .modal-dark .modal-content {
        background: #1e1e2d;
        border: 1px solid #2d2d3a;
        border-radius: 20px;
    }
    
    .modal-dark .modal-header { border-bottom: 1px solid #2d2d3a; }
    .modal-dark .modal-title { color: #fff; }
    .modal-dark .close { color: #fff; }
    .modal-dark .modal-footer { border-top: 1px solid #2d2d3a; }
    
    .form-group-dark label { color: #fff; margin-bottom: 8px; display: block; }
    
    .form-group-dark input {
        width: 100%;
        background: #2d2d3a;
        border: 1px solid #3d3d4a;
        color: #fff;
        padding: 12px 15px;
        border-radius: 12px;
    }
    
    .form-group-dark input::placeholder { color: #6a6a7a; }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
    }
    
    .empty-state i { font-size: 4rem; color: #3d3d4a; margin-bottom: 20px; }
    .empty-state h4 { color: #fff; margin-bottom: 10px; }
    .empty-state p { color: #8a8a8a; }
    
    /* Theme Support - Light Mode */
    body:not(.dark-mode) .contatos-container,
    body:not(.dark-mode) .stat-card,
    body:not(.dark-mode) .contatos-section,
    body:not(.dark-mode) .contato-item {
        background: #fff;
        border-color: #e0e0e0;
    }
    
    body:not(.dark-mode) .page-title h1,
    body:not(.dark-mode) .stat-label,
    body:not(.dark-mode) .contato-details h4,
    body:not(.dark-mode) .section-header h3,
    body:not(.dark-mode) .modal-title {
        color: #333 !important;
    }
    
    body:not(.dark-mode) .page-title p,
    body:not(.dark-mode) .contato-details span,
    body:not(.dark-mode) .empty-state p {
        color: #666 !important;
    }
    
    body:not(.dark-mode) .search-box input,
    body:not(.dark-mode) .form-group-dark input {
        background: #f8f9fa;
        border-color: #dee2e6;
        color: #333;
    }
    
    body:not(.dark-mode) .modal-content {
        background: #fff !important;
        border-color: #dee2e6 !important;
    }
    
    body:not(.dark-mode) .contato-avatar {
        background: linear-gradient(135deg, #00d4aa 0%, #00a884 100%);
    }
</style>

<div class="slim-mainpanel">
    <div class="container-fluid contatos-container">
        
        <!-- Header -->
        <div class="page-header">
            <div class="page-title">
                <h1><i class="fas fa-address-book mr-2" style="color: #9c27b0;"></i> Contatos para Campanhas</h1>
                <p>Gerencie seus contatos do WhatsApp e adicione novos manualmente</p>
            </div>
            
            <div class="header-actions">
                <button class="btn-action secondary" onclick="importarContatos()">
                    <i class="fab fa-whatsapp"></i> Importar do WhatsApp
                </button>
                <button class="btn-action primary" data-toggle="modal" data-target="#modalNovoContato">
                    <i class="fas fa-plus"></i> Adicionar Contato
                </button>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="stats-row">
            <div class="stat-card">
                <div class="stat-value green"><?= $totalWA + $totalManuais ?></div>
                <div class="stat-label">Total de Contatos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value blue"><?= $totalSalvos ?></div>
                <div class="stat-label">Salvos no WhatsApp</div>
            </div>
            <div class="stat-card">
                <div class="stat-value purple"><?= $totalConversas ?></div>
                <div class="stat-label">Conversas (não salvos)</div>
            </div>
            <div class="stat-card">
                <div class="stat-value orange"><?= $totalManuais ?></div>
                <div class="stat-label">Adicionados Manualmente</div>
            </div>
        </div>
        
        <!-- Tabs -->
        <div class="contatos-tabs">
            <button class="tab-btn active" onclick="showTab('todos')">Todos</button>
            <button class="tab-btn" onclick="showTab('whatsapp')">WhatsApp</button>
            <button class="tab-btn" onclick="showTab('manuais')">Manuais</button>
        </div>
        
        <!-- Content -->
        <div class="contatos-section">
            <div class="section-header">
                <div class="search-box">
                    <input type="text" id="searchContato" placeholder="Buscar por nome ou telefone...">
                </div>
            </div>
            
            <!-- Tab: Todos -->
            <div class="tab-content active" id="tab-todos">
                <div class="contatos-list">
                    <?php 
                    $stmtAll = $connect->prepare("SELECT * FROM contatos_whatsapp WHERE id_usuario = ? ORDER BY origem, nome ASC");
                    $stmtAll->execute([$cod_id]);
                    $hasContatos = false;
                    while ($contato = $stmtAll->fetch(PDO::FETCH_OBJ)): 
                        $hasContatos = true;
                    ?>
                    <div class="contato-item" data-id="<?= $contato->id ?>" data-nome="<?= htmlspecialchars($contato->nome ?? '') ?>" data-telefone="<?= $contato->telefone ?>">
                        <div class="contato-info">
                            <div class="contato-avatar"><?= strtoupper(substr($contato->nome ?? $contato->telefone, 0, 1)) ?></div>
                            <div class="contato-details">
                                <h4><?= htmlspecialchars($contato->nome ?? 'Sem Nome') ?></h4>
                                <span><i class="fas fa-phone"></i> <?= $contato->telefone ?></span>
                            </div>
                        </div>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="contato-badge <?= $contato->origem ?>">
                                <?php 
                                if ($contato->origem == 'salvo') echo '<i class="fas fa-star"></i> Salvo';
                                elseif ($contato->origem == 'conversa') echo '<i class="fas fa-comment"></i> Conversa';
                                else echo '<i class="fas fa-user-plus"></i> Manual';
                                ?>
                            </span>
                            <div class="contato-actions">
                                <button class="btn-icon edit" onclick="editarContato(<?= $contato->id ?>, '<?= htmlspecialchars($contato->nome ?? '') ?>', '<?= $contato->telefone ?>')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn-icon delete" onclick="excluirContato(<?= $contato->id ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    
                    <?php if (!$hasContatos): ?>
                    <div class="empty-state">
                        <i class="fas fa-address-book"></i>
                        <h4>Nenhum contato encontrado</h4>
                        <p>Importe contatos do WhatsApp ou adicione manualmente.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Tab: WhatsApp -->
            <div class="tab-content" id="tab-whatsapp">
                <div class="contatos-list">
                    <?php 
                    $stmtWATab = $connect->prepare("SELECT * FROM contatos_whatsapp WHERE id_usuario = ? AND origem IN ('salvo', 'conversa') ORDER BY origem, nome ASC");
                    $stmtWATab->execute([$cod_id]);
                    while ($contato = $stmtWATab->fetch(PDO::FETCH_OBJ)): 
                    ?>
                    <div class="contato-item" data-id="<?= $contato->id ?>">
                        <div class="contato-info">
                            <div class="contato-avatar"><?= strtoupper(substr($contato->nome ?? $contato->telefone, 0, 1)) ?></div>
                            <div class="contato-details">
                                <h4><?= htmlspecialchars($contato->nome ?? 'Sem Nome') ?></h4>
                                <span><i class="fas fa-phone"></i> <?= $contato->telefone ?></span>
                            </div>
                        </div>
                        <span class="contato-badge <?= $contato->origem ?>">
                            <?= $contato->origem == 'salvo' ? '<i class="fas fa-star"></i> Salvo' : '<i class="fas fa-comment"></i> Conversa' ?>
                        </span>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
            
            <!-- Tab: Manuais -->
            <div class="tab-content" id="tab-manuais">
                <div class="contatos-list">
                    <?php 
                    $stmtManuaisTab = $connect->prepare("SELECT * FROM contatos_whatsapp WHERE id_usuario = ? AND origem = 'manual' ORDER BY nome ASC");
                    $stmtManuaisTab->execute([$cod_id]);
                    $hasManuais = false;
                    while ($contato = $stmtManuaisTab->fetch(PDO::FETCH_OBJ)): 
                        $hasManuais = true;
                    ?>
                    <div class="contato-item" data-id="<?= $contato->id ?>">
                        <div class="contato-info">
                            <div class="contato-avatar" style="background: linear-gradient(135deg, #9c27b0 0%, #e040fb 100%);">
                                <?= strtoupper(substr($contato->nome ?? $contato->telefone, 0, 1)) ?>
                            </div>
                            <div class="contato-details">
                                <h4><?= htmlspecialchars($contato->nome ?? 'Sem Nome') ?></h4>
                                <span><i class="fas fa-phone"></i> <?= $contato->telefone ?></span>
                            </div>
                        </div>
                        <div class="contato-actions">
                            <button class="btn-icon edit" onclick="editarContato(<?= $contato->id ?>, '<?= htmlspecialchars($contato->nome ?? '') ?>', '<?= $contato->telefone ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-icon delete" onclick="excluirContato(<?= $contato->id ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    
                    <?php if (!$hasManuais): ?>
                    <div class="empty-state">
                        <i class="fas fa-user-plus"></i>
                        <h4>Nenhum contato manual</h4>
                        <p>Adicione contatos manualmente clicando no botão acima.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Contato -->
<div class="modal fade modal-dark" id="modalNovoContato" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus mr-2"></i>Adicionar Contato</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formNovoContato">
                <div class="modal-body">
                    <div class="form-group-dark mb-3">
                        <label>Nome</label>
                        <input type="text" name="nome" id="novoNome" placeholder="Nome do contato" required>
                    </div>
                    <div class="form-group-dark">
                        <label>Telefone (com DDD)</label>
                        <input type="text" name="telefone" id="novoTelefone" placeholder="Ex: 11999999999" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Salvar Contato</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Editar Contato -->
<div class="modal fade modal-dark" id="modalEditarContato" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit mr-2"></i>Editar Contato</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <form id="formEditarContato">
                <input type="hidden" name="id" id="editId">
                <div class="modal-body">
                    <div class="form-group-dark mb-3">
                        <label>Nome</label>
                        <input type="text" name="nome" id="editNome" placeholder="Nome do contato" required>
                    </div>
                    <div class="form-group-dark">
                        <label>Telefone (com DDD)</label>
                        <input type="text" name="telefone" id="editTelefone" placeholder="Ex: 11999999999" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Confirmação Importar -->
<div class="modal fade modal-dark" id="modalImportar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fab fa-whatsapp mr-2" style="color: #25D366;"></i>Importar Contatos</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fab fa-whatsapp" style="font-size: 3rem; color: #25D366; margin-bottom: 15px;"></i>
                <p style="color: #fff;">Isso irá importar/atualizar contatos do seu WhatsApp.<br>Deseja continuar?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-success" id="btnConfirmarImportar">
                    <i class="fas fa-download mr-1"></i> Importar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Confirmação Excluir -->
<div class="modal fade modal-dark" id="modalExcluirContato" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle mr-2" style="color: #dc3545;"></i>Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center py-4">
                <i class="fas fa-trash" style="font-size: 3rem; color: #dc3545; margin-bottom: 15px;"></i>
                <p style="color: #fff;">Tem certeza que deseja excluir este contato?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-danger" id="btnConfirmarExcluir">
                    <i class="fas fa-trash mr-1"></i> Excluir
                </button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="contatoIdExcluir" value="">

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../js/slim.js"></script>

<script>
function showTab(tab) {
    $('.tab-btn').removeClass('active');
    $(`.tab-btn:contains('${tab === 'todos' ? 'Todos' : tab === 'whatsapp' ? 'WhatsApp' : 'Manuais'}')`).addClass('active');
    
    $('.tab-content').removeClass('active');
    $(`#tab-${tab}`).addClass('active');
}

$('#searchContato').on('keyup', function() {
    const search = $(this).val().toLowerCase();
    $('.contato-item').each(function() {
        const nome = $(this).data('nome')?.toLowerCase() || '';
        const telefone = $(this).data('telefone') || '';
        $(this).toggle(nome.includes(search) || telefone.includes(search));
    });
});

function importarContatos() {
    $('#modalImportar').modal('show');
}

$('#btnConfirmarImportar').on('click', function() {
    $('#modalImportar').modal('hide');
    
    $.ajax({
        url: 'classes/campanhas_exe.php',
        type: 'POST',
        data: { acao: 'importar_contatos' },
        dataType: 'json',
        beforeSend: function() {
            $('body').append('<div id="loading" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.7);display:flex;align-items:center;justify-content:center;z-index:9999;"><div class="spinner-border text-success" style="width:3rem;height:3rem;"></div></div>');
        },
        success: function(resp) {
            $('#loading').remove();
            showToast(resp.message, resp.success ? 'success' : 'error');
            if (resp.success) setTimeout(() => location.reload(), 1500);
        },
        error: function() {
            $('#loading').remove();
            showToast('Erro ao importar contatos', 'error');
        }
    });
});

$('#formNovoContato').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'classes/contatos_exe.php',
        type: 'POST',
        data: {
            acao: 'criar',
            nome: $('#novoNome').val(),
            telefone: $('#novoTelefone').val()
        },
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                $('#modalNovoContato').modal('hide');
                showToast('Contato criado com sucesso', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(resp.message, 'error');
            }
        }
    });
});

function editarContato(id, nome, telefone) {
    $('#editId').val(id);
    $('#editNome').val(nome);
    $('#editTelefone').val(telefone);
    $('#modalEditarContato').modal('show');
}

$('#formEditarContato').on('submit', function(e) {
    e.preventDefault();
    
    $.ajax({
        url: 'classes/contatos_exe.php',
        type: 'POST',
        data: {
            acao: 'atualizar',
            id: $('#editId').val(),
            nome: $('#editNome').val(),
            telefone: $('#editTelefone').val()
        },
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                $('#modalEditarContato').modal('hide');
                showToast('Contato atualizado com sucesso', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(resp.message, 'error');
            }
        }
    });
});

function excluirContato(id) {
    $('#contatoIdExcluir').val(id);
    $('#modalExcluirContato').modal('show');
}

$('#btnConfirmarExcluir').on('click', function() {
    var id = $('#contatoIdExcluir').val();
    $('#modalExcluirContato').modal('hide');
    
    $.ajax({
        url: 'classes/contatos_exe.php',
        type: 'POST',
        data: { acao: 'excluir', id: id },
        dataType: 'json',
        success: function(resp) {
            if (resp.success) {
                showToast('Contato excluído com sucesso', 'success');
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(resp.message, 'error');
            }
        }
    });
});

// Toast notification function
function showToast(message, type) {
    var bgColor = type === 'success' ? '#00a884' : '#dc3545';
    var icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
    
    var toast = $(`
        <div class="toast-notification" style="
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${bgColor};
            color: #fff;
            padding: 15px 25px;
            border-radius: 10px;
            z-index: 10000;
            display: flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease;
        ">
            <i class="${icon}"></i>
            <span>${message}</span>
        </div>
    `);
    
    $('body').append(toast);
    setTimeout(() => toast.fadeOut(300, () => toast.remove()), 3000);
}
</script>

</body>
</html>
<?php ob_end_flush(); ?>
