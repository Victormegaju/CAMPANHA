<?php
require_once "topo.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS Necessários -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        
        /* Cabeçalho Gradiente Roxo/Azul para Categorias */
        .header-icon-bg { background: linear-gradient(135deg, #6f42c1, #4e73df); }
        
        /* Inputs */
        .form-control { border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; height: 45px; padding-left: 15px; border-left: 0; }
        .form-control:focus { border-color: #e0e0e0; box-shadow: none; border-bottom: 2px solid #6f42c1; }
        
        .input-group-text { 
            border-radius: 8px 0 0 8px; 
            border: 1px solid #e0e0e0; 
            background-color: #fff; 
            border-right: 0;
            width: 45px;
            justify-content: center;
        }

        /* Estilo Tabela Desktop */
        .d-none.d-lg-block .table td, .d-none.d-lg-block .table th { padding: 12px 15px; vertical-align: middle; }
        .d-none.d-lg-block .table-hover tbody tr:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.3s ease; }

        /* Mobile Cards */
        .categoria-mobile-card { transition: all 0.3s ease; }
        .categoria-mobile-card:hover { transform: translateY(-2px); }
        
        /* Botões de Ação */
        .btn-action-edit { background: linear-gradient(135deg, #ffc107, #fd7e14); color: white; border: none; }
        .btn-action-del { background: linear-gradient(135deg, #dc3545, #c82333); color: white; border: none; }
    </style>
</head>
<body>

<div class="slim-mainpanel">
    <div class="container-fluid">
        
        <!-- Header com botão novo -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="mr-3 header-icon-bg" style="padding: 12px; border-radius: 10px;">
                            <i class="fas fa-tags fa-lg text-white"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark font-weight-bold">
                                GERENCIAR CATEGORIAS
                            </h4>
                            <p class="text-muted mb-0">Organize seus grupos e categorias</p>
                        </div>
                    </div>
                    <a href="cad_categoria" class="btn" style="background: linear-gradient(135deg, #28a745, #20c997); color: white; padding: 10px 20px; border-radius: 8px; border: none;">
                        <i class="fas fa-plus mr-2"></i>NOVA CATEGORIA
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
            <meta http-equiv="refresh" content="1;URL=./categorias" />
        <?php } ?>

        <!-- Tabela para Desktop -->
        <div class="card border-0 d-none d-lg-block">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="mb-0 text-dark font-weight-bold">
                            <i class="fas fa-list mr-2" style="color: #6f42c1;"></i>Lista de Categorias
                        </h5>
                    </div>
                    <div class="form-group mb-0">
                        <div class="input-group" style="max-width: 300px;">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                            </div>
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar categorias...">
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="datatable1" class="table table-hover" style="border-collapse: separate; border-spacing: 0 8px;">
                        <thead>
                            <tr style="background: linear-gradient(90deg, #f8f9fa, #e9ecef);">
                                <th style="border-top-left-radius: 8px; border-bottom-left-radius: 8px; border: none; width: 60px;">ID</th>
                                <th style="border: none;">NOME DA CATEGORIA</th>
                                <th style="border-top-right-radius: 8px; border-bottom-right-radius: 8px; border: none; width: 120px;" class="text-center">AÇÕES</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $clientes = $connect->query("SELECT * FROM categoria WHERE idu = '".$cod_id."' ORDER BY nome ASC");
                            while ($dadosclientes = $clientes->fetch(PDO::FETCH_OBJ)) {
                            ?>
                                <tr style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.05);">
                                    <td style="border-top-left-radius: 8px; border-bottom-left-radius: 8px; border: none; vertical-align: middle;">
                                        <div class="d-flex align-items-center justify-content-center">
                                            <div style="background: linear-gradient(135deg, #6c757d, #495057); color: white; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.75rem;">
                                                <?php print $dadosclientes->id; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="border: none; vertical-align: middle;">
                                        <div class="font-weight-bold text-dark" style="font-size: 0.95rem;">
                                            <?php print $dadosclientes->nome; ?>
                                        </div>
                                    </td>
                                    <td style="border: none; border-top-right-radius: 8px; border-bottom-right-radius: 8px; vertical-align: middle;" class="text-center">
                                        <div class="d-flex justify-content-center" style="gap: 8px;">
                                            <!-- Editar -->
                                            <form action="edit_categoria" method="post" class="mb-0">
                                                <input type="hidden" name="edicli" value="<?php print $dadosclientes->id;?>"/>
                                                <button type="submit" class="btn btn-sm btn-action-edit" style="border-radius: 6px; padding: 6px 10px;" title="Editar">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Excluir -->
                                            <button type="button" class="btn btn-sm btn-action-del" style="border-radius: 6px; padding: 6px 10px;" 
                                                    title="Excluir" onclick="excluirRegistro(<?php print $dadosclientes->id;?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
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
        <div class="d-block d-lg-none">
            
            <!-- Barra de Busca Mobile -->
            <div class="card border-0 mb-3">
                <div class="card-body p-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-search text-muted"></i></span>
                        </div>
                        <input type="text" class="form-control" id="searchMobile" placeholder="Buscar categorias...">
                    </div>
                </div>
            </div>

            <div id="listaCategoriasMobile">
                <?php
                // Re-executa query para mobile
                $clientes_mobile = $connect->query("SELECT * FROM categoria WHERE idu = '".$cod_id."' ORDER BY nome ASC");
                while ($dadosclientes = $clientes_mobile->fetch(PDO::FETCH_OBJ)) {
                ?>
                    <div class="card border-0 mb-3 categoria-mobile-card">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div style="background: linear-gradient(135deg, #6c757d, #495057); color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.7rem; margin-right: 10px;">
                                        <?php print $dadosclientes->id; ?>
                                    </div>
                                    <h6 class="mb-0 font-weight-bold text-dark"><?php print $dadosclientes->nome; ?></h6>
                                </div>
                            </div>

                            <div class="border-top pt-3">
                                <div class="d-flex">
                                    <form action="edit_categoria" method="post" style="flex: 1; margin-right: 5px;">
                                        <input type="hidden" name="edicli" value="<?php print $dadosclientes->id;?>"/>
                                        <button type="submit" class="btn btn-sm w-100 btn-action-edit" style="border-radius: 6px;">
                                            <i class="fas fa-pencil-alt mr-1"></i> Editar
                                        </button>
                                    </form>
                                    
                                    <div style="flex: 1; margin-left: 5px;">
                                        <button type="button" class="btn btn-sm w-100 btn-action-del" style="border-radius: 6px;" 
                                                onclick="excluirRegistro(<?php print $dadosclientes->id;?>)">
                                            <i class="fas fa-trash mr-1"></i> Excluir
                                        </button>
                                    </div>
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

        var table = $('#datatable1').DataTable({
            responsive: true,
            language: {
                search: "",
                searchPlaceholder: 'Buscar...',
                lengthMenu: '_MENU_ itens',
                info: 'Mostrando _START_ a _END_ de _TOTAL_',
                infoEmpty: 'Nenhum registro encontrado',
                infoFiltered: '(filtrado de _MAX_ registros)',
                paginate: { first: '<<', last: '>>', next: '>', previous: '<' }
            },
            dom: '<"row"<"col-md-6"l><"col-md-6"f>>rt<"row"<"col-md-6"i><"col-md-6"p>>',
            pageLength: 10,
            order: [[1, 'asc']] // Ordena pela coluna Nome
        });

        // Conecta o input customizado ao DataTables (Desktop)
        $('#searchInput').on('keyup', function() {
            table.search($(this).val()).draw();
        });

        // Busca Mobile (Filtra os Cards)
        $('#searchMobile').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $("#listaCategoriasMobile .categoria-mobile-card").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });

    function excluirRegistro(id) {
        if(confirm('Tem certeza que deseja excluir esta categoria?')) {
            $.ajax({
                type: "POST",
                url: "classes/categoria_exe.php",
                data: { delcli: id },
                success: function (response) {
                    window.location.href='./categorias?sucesso=ok';
                },
                error: function() {
                    alert("Erro ao excluir. Tente novamente.");
                }
            });
        }
    }
</script>

<script src="../js/slim.js"></script>  
</body>
</html>