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
                        <div class="mr-3" style="background: linear-gradient(135deg, #6610f2, #4e73df); padding: 12px; border-radius: 10px;">
                            <i class="fas fa-users fa-lg text-white"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark font-weight-bold">
                                GERENCIAR CLIENTES
                            </h4>
                            <p class="text-muted mb-0">Gerencie sua carteira de clientes</p>
                        </div>
                    </div>
                    <a href="cad_cliente" class="btn" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 10px 20px; border-radius: 8px; border: none;">
                        <i class="fas fa-plus mr-2"></i>NOVO CLIENTE
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
            <meta http-equiv="refresh" content="1;URL=./clientes" />
        <?php } ?>

        <!-- Tabela para Desktop -->
        <div class="card border-0 d-none d-lg-block" style="background: linear-gradient(to bottom, #ffffff, #f8f9fa); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08);">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-0 text-dark font-weight-bold">
                            <i class="fas fa-list mr-2" style="color: #4e73df;"></i>Lista de Clientes
                        </h5>
                    </div>
                    <div class="form-group mb-0">
                        <div class="input-group" style="max-width: 300px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text bg-light border-right-0">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                            </div>
                            <input type="text" class="form-control border-left-0" id="searchInput" placeholder="Buscar clientes...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable1" class="table table-hover" style="border-collapse: separate; border-spacing: 0 8px;">
                        <thead>
                            <tr style="background: linear-gradient(90deg, #f8f9fa, #e9ecef);">
                                <th style="border-top-left-radius: 8px; border-bottom-left-radius: 8px; border: none; width: 50px;">ID</th>
                                <th style="border: none;">GRUPO</th>
                                <th style="border: none;">NOME</th>
                                <th style="border: none;">EMAIL</th> <!-- ALTERADO DE CPF PARA EMAIL -->
                                <th style="border: none;">CELULAR</th>
                                <th style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; border: none;" class="text-center">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $clientes = $connect->query("SELECT * FROM clientes WHERE idm = '".$cod_id."' ORDER BY nome ASC");
                            while ($dadosclientes = $clientes->fetch(PDO::FETCH_OBJ)) {
                                $editarcat = $connect->query("SELECT nome FROM categoria WHERE id='".$dadosclientes->idc."'");
                                $dadoscat = $editarcat->fetch(PDO::FETCH_OBJ); 
                                $nome_categoria = isset($dadoscat->nome) ? $dadoscat->nome : 'Sem Categoria';
                            ?>
                                <tr style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <!-- ID (Cinza) -->
                                    <td style="border-top-left-radius: 8px; border-bottom-left-radius: 8px; border: none; vertical-align: middle;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div style="background: linear-gradient(135deg, #6c757d, #495057); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.75rem;">
                                                <?php print $dadosclientes->Id; ?>
                                            </div>
                                        </div>
                                    </td>
                                    
                                    <!-- Grupo -->
                                    <td style="border: none; vertical-align: middle;">
                                        <span class="badge badge-light border" style="font-weight: 500;"><?php print $nome_categoria; ?></span>
                                    </td>
                                    
                                    <!-- Nome -->
                                    <td style="border: none; vertical-align: middle;">
                                        <div class="font-weight-bold text-dark"><?php print $dadosclientes->nome; ?></div>
                                    </td>
                                    
                                    <!-- Email (Laranja/Amarelo) - ALTERADO -->
                                    <td style="border: none; vertical-align: middle;">
                                        <div class="d-flex align-items-center">
                                            <div style="width: 24px; height: 24px; background-color: #fff3cd; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 8px;">
                                                <i class="fas fa-envelope text-warning" style="font-size: 0.7rem;"></i>
                                            </div>
                                            <span class="text-muted small"><?php print $dadosclientes->email; ?></span>
                                        </div>
                                    </td>
                                    
                                    <!-- Celular (Verde) -->
                                    <td style="border: none; vertical-align: middle;">
                                        <a href="https://api.whatsapp.com/send?phone=55<?php print preg_replace('/[^0-9]/', '', $dadosclientes->celular);?>&text=Olá <?php print $dadosclientes->nome;?>" target="_blank" class="btn btn-sm rounded-circle mr-1" style="background-color: #25D366; color: white; width: 28px; height: 28px; padding: 0; line-height: 28px;">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        <span class="text-muted small"><?php print $dadosclientes->celular; ?></span>
                                    </td>
                                    
                                    <!-- Ações (Azul, Laranja, Vermelho) -->
                                    <td style="border: none; border-top-right-radius: 8px; border-bottom-right-radius: 8px; vertical-align: middle;" class="text-center">
                                        <div class="d-flex justify-content-center" style="gap: 5px;">
                                            <form action="ver_cliente" method="post" class="mb-0">
                                                <input type="hidden" name="vercli" value="<?php print $dadosclientes->Id;?>"/>
                                                <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #17a2b8, #20c9c9); color: white; border-radius: 6px; padding: 5px 10px;" title="Ver">
                                                    <i class="fas fa-search-plus"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="edit_cliente" method="post" class="mb-0">
                                                <input type="hidden" name="edicli" value="<?php print $dadosclientes->Id;?>"/>
                                                <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #ffc107, #fd7e14); color: white; border-radius: 6px; padding: 5px 10px;" title="Editar">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            </form>
                                            
                                            <form action="classes/clientes_exe.php" method="post" class="mb-0">
                                                <input type="hidden" name="delcli" value="<?php print $dadosclientes->Id;?>"/>
                                                <button type="submit" class="btn btn-sm" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 6px; padding: 5px 10px;" 
                                                        title="Excluir" onclick="return confirm('Tem certeza que deseja excluir este cliente?');">
                                                    <i class="fas fa-trash"></i>
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

        <!-- VERSÃO MOBILE (Cartões) -->
        <div class="d-block d-lg-none">
            
            <!-- Barra de Busca Mobile -->
            <div class="card border-0 mb-3" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <div class="card-body p-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text bg-light border-right-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control border-left-0" id="searchMobile" placeholder="Buscar clientes...">
                    </div>
                </div>
            </div>

            <div id="listaClientesMobile">
                <?php
                // Re-executa query para mobile
                $clientes_mobile = $connect->query("SELECT * FROM clientes WHERE idm = '".$cod_id."' ORDER BY nome ASC");
                while ($dadosclientes = $clientes_mobile->fetch(PDO::FETCH_OBJ)) {
                    $editarcat = $connect->query("SELECT nome FROM categoria WHERE id='".$dadosclientes->idc."'");
                    $dadoscat = $editarcat->fetch(PDO::FETCH_OBJ); 
                    $nome_categoria = isset($dadoscat->nome) ? $dadoscat->nome : 'Sem Categoria';
                ?>
                    <div class="card border-0 mb-3 cliente-mobile-card" style="border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                        <div class="card-body p-3">
                            <!-- Header do Card -->
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div style="background: linear-gradient(135deg, #6c757d, #495057); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.7rem; margin-right: 10px;">
                                        <?php print $dadosclientes->Id; ?>
                                    </div>
                                    <div>
                                        <h6 class="mb-0 font-weight-bold text-dark nome-cliente"><?php print $dadosclientes->nome; ?></h6>
                                        <small class="text-muted"><?php print $nome_categoria; ?></small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Dados do Card (Email e Celular) -->
                            <div class="row mb-3">
                                <div class="col-7"> <!-- Coluna Email -->
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">EMAIL</small>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-envelope text-warning mr-1" style="font-size: 0.8rem;"></i>
                                        <span class="text-dark small text-truncate" style="max-width: 130px;"><?php print $dadosclientes->email; ?></span>
                                    </div>
                                </div>
                                <div class="col-5"> <!-- Coluna Celular -->
                                    <small class="text-muted d-block" style="font-size: 0.7rem;">CELULAR</small>
                                    <div class="d-flex align-items-center">
                                        <a href="https://api.whatsapp.com/send?phone=55<?php print preg_replace('/[^0-9]/', '', $dadosclientes->celular);?>&text=Olá <?php print $dadosclientes->nome;?>" target="_blank" class="text-success mr-1">
                                            <i class="fab fa-whatsapp"></i>
                                        </a>
                                        <span class="text-dark small"><?php print $dadosclientes->celular; ?></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Botões de Ação -->
                            <div class="border-top pt-3">
                                <div class="d-flex">
                                    <form action="ver_cliente" method="post" style="flex: 1; margin: 0 2px;">
                                        <input type="hidden" name="vercli" value="<?php print $dadosclientes->Id;?>"/>
                                        <button type="submit" class="btn btn-sm w-100" style="background: linear-gradient(135deg, #17a2b8, #20c9c9); color: white; border-radius: 6px;">
                                            <i class="fas fa-search-plus"></i>
                                        </button>
                                    </form>

                                    <form action="edit_cliente" method="post" style="flex: 1; margin: 0 2px;">
                                        <input type="hidden" name="edicli" value="<?php print $dadosclientes->Id;?>"/>
                                        <button type="submit" class="btn btn-sm w-100" style="background: linear-gradient(135deg, #ffc107, #fd7e14); color: white; border-radius: 6px;">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                    </form>
                                    
                                    <form action="classes/clientes_exe.php" method="post" style="flex: 1; margin: 0 2px;">
                                        <input type="hidden" name="delcli" value="<?php print $dadosclientes->Id;?>"/>
                                        <button type="submit" class="btn btn-sm w-100" style="background: linear-gradient(135deg, #dc3545, #c82333); color: white; border-radius: 6px;" 
                                                onclick="return confirm('Excluir este cliente?');">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

    </div><!-- container -->
</div><!-- slim-mainpanel -->

<!-- Dependências JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<script>
    $(function(){
        'use strict';

        // Tabela Desktop
        $('#datatable1').DataTable({
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: 'Buscar...',
                lengthMenu: '_MENU_ itens',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                paginate: {
                    first: '<<',
                    last: '>>',
                    next: '>',
                    previous: '<'
                }
            },
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            pageLength: 10,
            order: [[2, 'asc']] // Ordena por Nome
        });

        // Conecta o input customizado ao DataTables (Desktop)
        $('#searchInput').on('keyup', function() {
            $('#datatable1').DataTable().search($(this).val()).draw();
        });

        // Script de Busca Mobile (Javascript Puro)
        $('#searchMobile').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#listaClientesMobile .cliente-mobile-card").filter(function() {
                // Busca no texto do cartão (Nome, Email, Celular)
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

<style>
    body {
        background-color: #f5f7fb;
    }

    /* Estilos Tabela Desktop */
    .d-none.d-lg-block .table td, 
    .d-none.d-lg-block .table th {
        padding: 12px 10px;
    }

    .d-none.d-lg-block .table-hover tbody tr:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    /* Mobile Cards */
    .cliente-mobile-card {
        transition: all 0.3s ease;
    }
    .cliente-mobile-card:hover {
        transform: translateY(-2px);
    }
</style>

<script src="../js/slim.js"></script>  
</body>
</html>