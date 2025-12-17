<?php
require_once "topo.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<div class="slim-mainpanel">
    <div class="container-fluid">
        <!-- Header com botão novo -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="background: linear-gradient(135deg, #4e73df, #6610f2); padding: 12px; border-radius: 10px;">
                            <i class="fas fa-users fa-lg text-white"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark font-weight-bold">
                                GERENCIAR USUÁRIOS
                            </h4>
                            <p class="text-muted mb-0">Gerencie todos os usuários do sistema</p>
                        </div>
                    </div>
                    <a href="cad_usuario" class="btn" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 10px 20px; border-radius: 8px; border: none;">
                        <i class="fas fa-plus mr-2"></i>NOVO USUÁRIO
                    </a>
                </div>
            </div>
        </div>

        <?php if (isset($_GET["sucesso"])) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: 10px; border-left: 4px solid #28a745;">
                <i class="fas fa-check-circle mr-2"></i>
                <strong>Sucesso!</strong> Operação realizada com sucesso.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>
        
        <?php if (isset($_GET["erro"])) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: 10px; border-left: 4px solid #dc3545;">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <strong>Erro!</strong> <?php echo htmlspecialchars($_GET["erro"]); ?>
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        <?php } ?>

        <!-- Tabela para Desktop / Cards para Mobile -->
        <div class="card border-0 d-none d-lg-block" style="background: linear-gradient(to bottom, #ffffff, #f8f9fa); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-0 text-dark font-weight-bold">
                            <i class="fas fa-list mr-2" style="color: #4e73df;"></i>Lista de Usuários
                        </h5>
                    </div>
                    <div class="form-group mb-0">
                        <!-- UNICA BARRA DE PESQUISA (MANTIDA) -->
                        <div class="input-group" style="max-width: 300px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control border-left-0" id="searchInput" placeholder="Buscar usuários...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable1" class="table table-hover" style="border-collapse: separate; border-spacing: 0 8px;">
                        <thead>
                            <tr style="background: linear-gradient(90deg, #f8f9fa, #e9ecef);">
                                <th style="border-top-left-radius: 8px; border-bottom-left-radius: 8px; border: none; width: 50px;">ID</th>
                                <th style="border: none;">NOME</th>
                                <th style="border: none;">LOGIN</th>
                                <th style="border: none;">SENHA</th>
                                <th style="border: none;">VALIDADE</th>
                                <th style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; border: none;" class="text-center">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $clientes = $connect->query("SELECT * FROM carteira WHERE tipo != '1' AND idm ='" . $cod_id . "' ORDER BY nome ASC");
                            while ($dadosclientes = $clientes->fetch(PDO::FETCH_OBJ)) {
                                // Verificar status da assinatura
                                $assinatura = $dadosclientes->assinatura;
                                $status = "Ativo";
                                $statusClass = "success";
                                $suspenso = isset($dadosclientes->status) && $dadosclientes->status == '0';
                                
                                if($suspenso) {
                                    $status = "Suspenso";
                                    $statusClass = "secondary";
                                } elseif($assinatura) {
                                    $dataAssinatura = DateTime::createFromFormat('d/m/Y', $assinatura);
                                    $hoje = new DateTime();
                                    if($dataAssinatura < $hoje) {
                                        $status = "Expirado";
                                        $statusClass = "danger";
                                    } elseif($dataAssinatura->diff($hoje)->days <= 7) {
                                        $status = "Expira em " . $dataAssinatura->diff($hoje)->days . " dias";
                                        $statusClass = "warning";
                                    }
                                }
                                
                                // Exibição da senha
                                $senha_texto = $dadosclientes->senha;
                                // Mascara a senha visualmente na tabela, mas o botão mostrará o valor real
                                $senha_exibicao = '●●●●●●●●';
                                $senha_tipo = 'texto'; // Assumimos texto para permitir visualização direta
                            ?>
                                <tr style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <td style="border-top-left-radius: 8px; border-bottom-left-radius: 8px; border: none; vertical-align: middle; width: 50px;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div style="background: linear-gradient(135deg, #6c757d, #495057); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.75rem;">
                                                <?php print $dadosclientes->Id; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="border: none; vertical-align: middle;">
                                        <div class="d-flex align-items-center">
                                            <div style="background: linear-gradient(135deg, #4e73df, #6610f2); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                                <i class="fas fa-user" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size: 0.9rem;"><?php print $dadosclientes->nome; ?></div>
                                                <small class="text-muted d-block" style="font-size: 0.8rem;"><?php print $dadosclientes->celular; ?></small>
                                                <span class="badge badge-pill badge-<?php print $statusClass; ?>" style="padding: 2px 6px; font-size: 0.7rem; margin-top: 2px;">
                                                    <?php print $status; ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="border: none; vertical-align: middle;">
                                        <div class="d-flex align-items-center">
                                            <div style="background: linear-gradient(135deg, #fd7e14, #ffc107); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                                <i class="fas fa-sign-in-alt" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark" style="font-size: 0.9rem;"><?php print $dadosclientes->login; ?></div>
                                        </div>
                                    </td>
                                    <td style="border: none; vertical-align: middle;">
                                        <div class="d-flex align-items-center">
                                            <div style="background: linear-gradient(135deg, #20c997, #28a745); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                                <i class="fas fa-key" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div>
                                                <div class="font-weight-bold text-dark" style="font-size: 0.9rem;" id="senha-text-<?php print $dadosclientes->Id; ?>">
                                                    <?php print $senha_exibicao; ?>
                                                </div>
                                                <button type="button" class="btn btn-link btn-sm p-0" onclick="mostrarSenhaDesktop(<?php print $dadosclientes->Id; ?>, '<?php print $senha_tipo; ?>')" style="font-size: 0.75rem;">
                                                    <small><i class="fas fa-eye mr-1"></i>Mostrar</small>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="border: none; vertical-align: middle;">
                                        <div class="d-flex align-items-center">
                                            <div style="background: linear-gradient(135deg, #17a2b8, #20c9c9); color: white; width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                                <i class="fas fa-calendar-alt" style="font-size: 0.85rem;"></i>
                                            </div>
                                            <div class="font-weight-bold text-dark" style="font-size: 0.9rem;"><?php print $assinatura ?: 'Não definida'; ?></div>
                                        </div>
                                    </td>
                                    <td style="border: none; border-top-right-radius: 8px; border-bottom-right-radius: 8px; vertical-align: middle;" class="text-center">
                                        <div class="d-flex flex-wrap justify-content-center" style="gap: 4px;">
                                            <!-- Painel Usuário -->
                                            <form action="painel_usuario" method="POST" class="mb-1">
                                                <input type="hidden" name="ver" value="ok" />
                                                <input type="hidden" name="idcli" value="<?php print $dadosclientes->Id; ?>" />
                                                <input type="hidden" name="idmas" value="<?php print $cod_id; ?>" />
                                                <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #17a2b8, #20c9c9); color: white; border-radius: 6px; padding: 4px 8px; min-width: 32px;" title="Painel do Usuário">
                                                    <i class="fas fa-search" style="font-size: 0.85rem;"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Editar -->
                                            <form action="edit_usuario" method="post" class="mb-1">
                                                <input type="hidden" name="edicli" value="<?php print $dadosclientes->Id; ?>" />
                                                <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #ffc107, #fd7e14); color: white; border-radius: 6px; padding: 4px 8px; min-width: 32px;" title="Editar">
                                                    <i class="fas fa-pencil-alt" style="font-size: 0.85rem;"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Suspender/Ativar -->
                                            <button type="button" 
                                                    class="btn btn-sm <?php print $suspenso ? 'btn-success' : 'btn-secondary'; ?> mb-1" 
                                                    style="border-radius: 6px; padding: 4px 8px; min-width: 32px;" 
                                                    title="<?php print $suspenso ? 'Ativar Usuário' : 'Suspender Usuário'; ?>"
                                                    onclick="alterarStatusUsuario(<?php print $dadosclientes->Id; ?>, '<?php print $suspenso ? 'ativar' : 'suspender'; ?>')">
                                                <i class="fas <?php print $suspenso ? 'fa-play' : 'fa-pause'; ?>" style="font-size: 0.85rem;"></i>
                                            </button>
                                            
                                            <!-- Renovar Assinatura -->
                                            <button type="button" class="btn btn-sm mb-1" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border-radius: 6px; padding: 4px 8px; min-width: 32px;" 
                                                    title="Renovar Assinatura"
                                                    onclick="abrirModalRenovacao(<?php print $dadosclientes->Id; ?>, '<?php print $dadosclientes->nome; ?>')">
                                                <i class="fas fa-sync-alt" style="font-size: 0.85rem;"></i>
                                            </button>
                                            
                                            <!-- Excluir -->
                                            <form action="classes/funcionario_exe.php" method="post" class="mb-1">
                                                <input type="hidden" name="delcob" value="<?php print $dadosclientes->Id; ?>" />
                                                <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 6px; padding: 4px 8px; min-width: 32px;" 
                                                        title="Excluir" onclick='return confirm("Tem certeza que deseja excluir este usuário?")'>
                                                    <i class="fas fa-trash" style="font-size: 0.85rem;"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Cards para Mobile -->
        <div class="d-block d-lg-none" id="mobileCards">
            <!-- SEGUNDA BUSCA REMOVIDA AQUI -->

            <!-- Lista de cards mobile -->
            <?php
            // Re-executar a query para mobile
            $clientes_mobile = $connect->query("SELECT * FROM carteira WHERE tipo != '1' AND idm ='" . $cod_id . "' ORDER BY nome ASC");
            while ($dadosclientes = $clientes_mobile->fetch(PDO::FETCH_OBJ)) {
                // Verificar status da assinatura
                $assinatura = $dadosclientes->assinatura;
                $status = "Ativo";
                $statusClass = "success";
                $suspenso = isset($dadosclientes->status) && $dadosclientes->status == '0';
                
                if($suspenso) {
                    $status = "Suspenso";
                    $statusClass = "secondary";
                } elseif($assinatura) {
                    $dataAssinatura = DateTime::createFromFormat('d/m/Y', $assinatura);
                    $hoje = new DateTime();
                    if($dataAssinatura < $hoje) {
                        $status = "Expirado";
                        $statusClass = "danger";
                    } elseif($dataAssinatura->diff($hoje)->days <= 7) {
                        $status = "Expira em " . $dataAssinatura->diff($hoje)->days . " dias";
                        $statusClass = "warning";
                    }
                }
                
                $senha_exibicao = '●●●●●●●●';
                $senha_tipo = 'texto';
            ?>
                <div class="card border-0 mb-3 usuario-mobile-card" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div class="card-body p-3">
                        <!-- Header do card mobile -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <div style="background: linear-gradient(135deg, #6c757d, #495057); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.7rem; margin-right: 8px;">
                                    <?php print $dadosclientes->Id; ?>
                                </div>
                                <div>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.9rem;"><?php print $dadosclientes->nome; ?></div>
                                    <small class="text-muted" style="font-size: 0.75rem;"><?php print $dadosclientes->celular; ?></small>
                                </div>
                            </div>
                            <span class="badge badge-pill badge-<?php print $statusClass; ?>" style="padding: 2px 6px; font-size: 0.7rem;">
                                <?php print $status; ?>
                            </span>
                        </div>
                        
                        <!-- Informações do usuário em linhas compactas -->
                        <div class="mb-3">
                            <!-- Login -->
                            <div class="d-flex align-items-center mb-2">
                                <div style="background: linear-gradient(135deg, #fd7e14, #ffc107); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 8px; flex-shrink: 0;">
                                    <i class="fas fa-sign-in-alt" style="font-size: 0.8rem;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">LOGIN</small>
                                    <div class="font-weight-bold text-dark text-truncate" style="font-size: 0.85rem;"><?php print $dadosclientes->login; ?></div>
                                </div>
                            </div>
                            
                            <!-- Senha -->
                            <div class="d-flex align-items-center mb-2">
                                <div style="background: linear-gradient(135deg, #20c997, #28a745); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 8px; flex-shrink: 0;">
                                    <i class="fas fa-key" style="font-size: 0.8rem;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">SENHA</small>
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="font-weight-bold text-dark" style="font-size: 0.85rem;" id="senha-mobile-<?php print $dadosclientes->Id; ?>">
                                            <?php print $senha_exibicao; ?>
                                        </div>
                                        <button type="button" class="btn btn-link btn-sm p-0" onclick="mostrarSenhaMobile(<?php print $dadosclientes->Id; ?>, '<?php print $senha_tipo; ?>')" style="font-size: 0.75rem;">
                                            <small><i class="fas fa-eye mr-1"></i>Mostrar</small>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Validade -->
                            <div class="d-flex align-items-center">
                                <div style="background: linear-gradient(135deg, #17a2b8, #20c9c9); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 10px; flex-shrink: 0;">
                                    <i class="fas fa-calendar-alt" style="font-size: 0.8rem;"></i>
                                </div>
                                <div style="flex: 1;">
                                    <small class="text-muted d-block mb-1" style="font-size: 0.75rem;">VALIDADE</small>
                                    <div class="font-weight-bold text-dark" style="font-size: 0.85rem;"><?php print $assinatura ?: 'Não definida'; ?></div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Ações em linha -->
                        <div class="border-top pt-3">
                            <div class="d-flex justify-content-between">
                                <!-- Painel Usuário -->
                                <form action="painel_usuario" method="POST" style="flex: 1; margin: 0 2px;">
                                    <input type="hidden" name="ver" value="ok" />
                                    <input type="hidden" name="idcli" value="<?php print $dadosclientes->Id; ?>" />
                                    <input type="hidden" name="idmas" value="<?php print $cod_id; ?>" />
                                    <button type="submit" class="btn btn-sm w-100" style="background: linear-gradient(135deg, #17a2b8, #20c9c9); color: white; border-radius: 6px; padding: 6px 0; font-size: 0.75rem;" title="Painel">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                                
                                <!-- Editar -->
                                <form action="edit_usuario" method="post" style="flex: 1; margin: 0 2px;">
                                    <input type="hidden" name="edicli" value="<?php print $dadosclientes->Id; ?>" />
                                    <button type="submit" class="btn btn-sm w-100" style="background: linear-gradient(135deg, #ffc107, #fd7e14); color: white; border-radius: 6px; padding: 6px 0; font-size: 0.75rem;" title="Editar">
                                        <i class="fas fa-pencil-alt"></i>
                                    </button>
                                </form>
                                
                                <!-- Suspender/Ativar -->
                                <div style="flex: 1; margin: 0 2px;">
                                    <button type="button" 
                                            class="btn btn-sm w-100 <?php print $suspenso ? 'btn-success' : 'btn-secondary'; ?>" 
                                            style="border-radius: 6px; padding: 6px 0; font-size: 0.75rem;" 
                                            title="<?php print $suspenso ? 'Ativar' : 'Suspender'; ?>"
                                            onclick="alterarStatusUsuario(<?php print $dadosclientes->Id; ?>, '<?php print $suspenso ? 'ativar' : 'suspender'; ?>')">
                                        <i class="fas <?php print $suspenso ? 'fa-play' : 'fa-pause'; ?>"></i>
                                    </button>
                                </div>
                                
                                <!-- Renovar -->
                                <button type="button" class="btn btn-sm w-100" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; border-radius: 6px; padding: 6px 0; font-size: 0.75rem; margin: 0 2px;" 
                                        title="Renovar"
                                        onclick="abrirModalRenovacao(<?php print $dadosclientes->Id; ?>, '<?php print $dadosclientes->nome; ?>')">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                                
                                <!-- Excluir -->
                                <form action="classes/funcionario_exe.php" method="post" style="flex: 1; margin: 0 2px;">
                                    <input type="hidden" name="delcob" value="<?php print $dadosclientes->Id; ?>" />
                                    <button type="submit" class="btn btn-sm w-100" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 6px; padding: 6px 0; font-size: 0.75rem;" 
                                            title="Excluir" onclick='return confirm("Tem certeza que deseja excluir este usuário?")'>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div><!-- container -->
</div><!-- slim-mainpanel -->

<!-- Modal para Renovação (AJAX) -->
<div class="modal fade" id="modalRenovacao" tabindex="-1" role="dialog" aria-labelledby="modalRenovacaoLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                <h5 class="modal-title">
                    <i class="fas fa-sync-alt mr-2"></i>Renovar Assinatura
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <form id="formRenovacao" action="javascript:void(0);">
                    <input type="hidden" name="renovar_usuario" id="renovarUsuarioId">
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark d-flex align-items-center mb-2">
                            <i class="fas fa-user mr-2" style="color: #4e73df;"></i>Usuário:
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0">
                                    <i class="fas fa-user text-primary"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control border-left-0" id="nomeUsuarioRenovar" readonly
                                   style="background-color: #f8f9fa; font-weight: 600;">
                        </div>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-alt mr-2" style="color: #17a2b8;"></i>Período de Renovação:
                        </label>
                        <select name="periodo_renovacao" id="periodoRenovacao" class="form-control" 
                                style="border: 2px solid #e0e0e0; border-radius: 8px; padding: 10px;"
                                onchange="calcularNovaData(this.value)">
                            <option value="30">30 dias</option>
                            <option value="60">60 dias</option>
                            <option value="90">90 dias</option>
                            <option value="180">180 dias</option>
                            <option value="365">1 ano</option>
                            <option value="custom">Data Personalizada</option>
                        </select>
                    </div>
                    
                    <div class="form-group mb-4">
                        <label class="font-weight-bold text-dark d-flex align-items-center mb-2">
                            <i class="fas fa-calendar-check mr-2" style="color: #20c997;"></i>Nova Data de Vencimento:
                        </label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0">
                                    <i class="fas fa-calendar text-success"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control border-left-0 datepicker-modal" 
                                   name="nova_data_assinatura" id="novaDataRenovacao" required readonly
                                   placeholder="Selecione a data"
                                   style="border: 2px solid #e0e0e0; border-radius: 0 8px 8px 0;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary" type="button" id="abrirCalendario"
                                        style="border: 2px solid #e0e0e0; border-left: 0; border-radius: 0 8px 8px 0;">
                                    <i class="fas fa-calendar-alt"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top: 1px solid #e9ecef;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal" style="border-radius: 8px; padding: 8px 20px;">
                    <i class="fas fa-times mr-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" onclick="confirmarRenovacao()" 
                        style="border-radius: 8px; padding: 8px 25px; background: linear-gradient(135deg, #28a745, #20c997); border: none;">
                    <i class="fas fa-sync-alt mr-2"></i>Renovar Assinatura
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/jquery.cookie/js/jquery.cookie.js"></script>
<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script src="../lib/select2/js/select2.full.min.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js"></script>

<script>
$(function () {

    $('.datepicker-modal').datepicker({ format: 'dd/mm/yyyy', language: 'pt-BR', autoclose: true, todayHighlight: true, startDate: 'today' });
    $('#abrirCalendario').click(function(){ $('#novaDataRenovacao').datepicker('show'); });
    $('#searchInput').on('keyup', function() { $('#datatable1').DataTable().search($(this).val()).draw(); });
});

function mostrarSenhaDesktop(id, tipo) {
    var senhaSpan = $('#senha-text-' + id);
    var button = $(event.target).closest('button');
    if(senhaSpan.text() === '●●●●●●●●') {
        $.ajax({
            url: 'classes/funcionario_exe.php',
            type: 'POST',
            data: { obter_senha: 1, id_usuario: id },
            success: function(response) {
                try { if(typeof response === 'string') response = JSON.parse(response); } catch(e) {}
                if(response.success || response.senha) {
                    senhaSpan.text(response.senha || response);
                    button.find('small').html('<i class="fas fa-eye-slash mr-1"></i>Ocultar');
                } else { mostrarNotificacao('Erro ao obter senha', 'error'); }
            },
            error: function() { mostrarNotificacao('Erro ao conectar com o servidor', 'error'); }
        });
    } else {
        senhaSpan.text('●●●●●●●●');
        button.find('small').html('<i class="fas fa-eye mr-1"></i>Mostrar');
    }
}

function mostrarSenhaMobile(id, tipo) {
    var senhaSpan = $('#senha-mobile-' + id);
    var button = $(event.target).closest('button');
    if(senhaSpan.text() === '●●●●●●●●') {
        $.ajax({
            url: 'classes/funcionario_exe.php',
            type: 'POST',
            data: { obter_senha: 1, id_usuario: id },
            success: function(response) {
                try { if(typeof response === 'string') response = JSON.parse(response); } catch(e) {}
                if(response.success || response.senha) {
                    senhaSpan.text(response.senha || response);
                    button.find('small').html('<i class="fas fa-eye-slash mr-1"></i>Ocultar');
                } else { mostrarNotificacao('Erro ao obter senha', 'error'); }
            },
            error: function() { mostrarNotificacao('Erro ao conectar com o servidor', 'error'); }
        });
    } else {
        senhaSpan.text('●●●●●●●●');
        button.find('small').html('<i class="fas fa-eye mr-1"></i>Mostrar');
    }
}

function abrirModalRenovacao(id, nome) {
    $('#renovarUsuarioId').val(id);
    $('#nomeUsuarioRenovar').val(nome);
    calcularNovaData(30);
    $('#periodoRenovacao').val('30');
    $('#modalRenovacao').modal('show');
}

function calcularNovaData(dias) {
    if(dias === 'custom') { $('#novaDataRenovacao').datepicker('show'); return; }
    var hoje = new Date();
    var dataFutura = new Date(hoje);
    dataFutura.setDate(hoje.getDate() + parseInt(dias));
    var dia = dataFutura.getDate().toString().padStart(2, '0');
    var mes = (dataFutura.getMonth() + 1).toString().padStart(2, '0');
    var ano = dataFutura.getFullYear();
    $('#novaDataRenovacao').val(dia + '/' + mes + '/' + ano);
}

function confirmarRenovacao() {
    if(!$('#novaDataRenovacao').val()) { mostrarNotificacao('Selecione uma data.', 'warning'); return; }
    if(confirm('Renovar assinatura?')) {
        $.ajax({
            url: 'classes/funcionario_exe.php',
            type: 'POST',
            data: $('#formRenovacao').serialize(),
            success: function() {
                $('#modalRenovacao').modal('hide');
                mostrarNotificacao('Assinatura renovada!', 'success');
                setTimeout(function(){ location.reload(); }, 1000);
            },
            error: function() { mostrarNotificacao('Erro na conexão.', 'error'); }
        });
    }
}

function alterarStatusUsuario(id, acao) {
    if(confirm(acao === 'suspender' ? 'Suspender usuário?' : 'Ativar usuário?')) {
        $.ajax({
            url: 'classes/funcionario_exe.php',
            type: 'POST',
            data: { suspender_usuario: id },
            success: function() {
                mostrarNotificacao('Status alterado!', 'success');
                setTimeout(function(){ location.reload(); }, 1000);
            },
            error: function() { mostrarNotificacao('Erro na operação.', 'error'); }
        });
    }
}

function mostrarNotificacao(mensagem, tipo = 'success') {
    var bgColor = tipo === 'success' ? '#28a745' : tipo === 'warning' ? '#ffc107' : '#dc3545';
    var notificacao = $('<div class="notificacao"></div>')
        .css({ 'position': 'fixed', 'top': '20px', 'right': '20px', 'background': bgColor, 'color': 'white', 'padding': '12px 20px', 'border-radius': '8px', 'box-shadow': '0 4px 12px rgba(0,0,0,0.15)', 'z-index': '9999', 'font-weight': '500' })
        .html('<i class="fas fa-info-circle mr-2"></i>' + mensagem);
    $('body').append(notificacao);
    setTimeout(function() { notificacao.fadeOut(300, function() { $(this).remove(); }); }, 3000);
}
</script>

<style>
body { background-color: #f5f7fb; }
.d-none.d-lg-block .table-hover tbody tr:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.3s ease; }
.usuario-mobile-card { transition: all 0.3s ease; }
.usuario-mobile-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.12) !important; }
.text-truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.notificacao { animation: slideIn 0.3s ease; }
@keyframes slideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
</style>
<script src="../js/slim.js"></script>
</body>
</html>