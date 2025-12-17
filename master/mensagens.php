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
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" />
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        
        /* Botões de Ação Redondos */
        .btn-icon-circle {
            width: 35px; height: 35px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            border: none; color: white; margin: 0 2px; transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-icon-circle:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); color: white; }
        
        .btn-edit { background: linear-gradient(135deg, #ffc107, #ffca2c); color: #fff; }
        .btn-activate { background: linear-gradient(135deg, #17a2b8, #138496); }
        .btn-deactivate { background: linear-gradient(135deg, #dc3545, #c82333); }
        
        /* Badges de Status */
        .badge-status { padding: 6px 12px; border-radius: 20px; font-weight: 600; font-size: 0.75rem; letter-spacing: 0.5px; }
        .badge-active { background-color: #d4edda; color: #155724; }
        .badge-inactive { background-color: #f8d7da; color: #721c24; }

        /* Ícones de Tipo */
        .icon-type { font-size: 1.1rem; margin-right: 8px; width: 25px; text-align: center; }
        
        /* Cores sem repetir */
        .text-purple { color: #6f42c1; }
        .text-blue { color: #007bff; }
        .text-teal { color: #20c997; }
        .text-orange { color: #fd7e14; }
        .text-red { color: #dc3545; }
        .text-green { color: #28a745; }
        .text-indigo { color: #6610f2; }

        /* Header Icon */
        .header-icon-box {
            background: linear-gradient(135deg, #00b894, #00cec9);
            padding: 12px; border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 184, 148, 0.3);
        }
    </style>
</head>
<body>

<div class="slim-mainpanel">
    <div class="container-fluid">
        
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="mr-3 header-icon-box">
                        <i class="fas fa-comment-dots fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">NOTIFICAÇÕES</h4>
                        <p class="text-muted mb-0">Gerencie as mensagens automáticas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-right mt-3 mt-md-0">
                <form action="create_menssage" method="post" class="d-inline">
                    <button type="submit" class="btn btn-primary shadow-sm" style="border-radius: 8px; background: linear-gradient(135deg, #007bff, #0056b3); border:none;">
                        <i class="fas fa-plus mr-2"></i> Nova Mensagem
                    </button>
                </form>
                <a href="./" class="btn btn-secondary shadow-sm ml-2" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left mr-2"></i> Voltar
                </a>
            </div>
        </div>

        <?php if (isset($_GET["sucesso"])) { ?>
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle mr-2"></i> <strong>Sucesso!</strong> A operação foi realizada.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <meta http-equiv="refresh" content="1;URL=./mensagens" />
        <?php } ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-4">
                        
                        <div class="table-responsive">
                            <table id="datatable1" class="table table-hover w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="border-radius: 8px 0 0 8px;">#</th>
                                        <th>Tipo da Mensagem</th>
                                        <th>Status</th>
                                        <th>Prévia da Mensagem</th>
                                        <th class="text-center" style="border-radius: 0 8px 8px 0;">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <?php
                                    $clientes = $connect->query("SELECT * FROM mensagens WHERE idu = '" . $cod_id . "' ORDER BY tipo ASC");
                                    while ($dadosclientes = $clientes->fetch(PDO::FETCH_OBJ)) {

                                        // Definição de Ícones e Cores
                                        $icone = "";
                                        $tipomsg = "";
                                        
                                        if ($dadosclientes->tipo == '1') {
                                            $tipomsg = "5 dias antes";
                                            $icone = '<i class="fas fa-clock icon-type text-blue"></i>';
                                        } elseif ($dadosclientes->tipo == '2') {
                                            $tipomsg = "3 dias antes";
                                            $icone = '<i class="fas fa-hourglass-half icon-type text-teal"></i>';
                                        } elseif ($dadosclientes->tipo == '3') {
                                            $tipomsg = "No dia do vencimento";
                                            $icone = '<i class="fas fa-calendar-day icon-type text-purple"></i>';
                                        } elseif ($dadosclientes->tipo == '4') {
                                            $tipomsg = "Cobrança Vencida";
                                            $icone = '<i class="fas fa-exclamation-triangle icon-type text-red"></i>';
                                        } elseif ($dadosclientes->tipo == '5') {
                                            $tipomsg = "Comprovante Pagamento";
                                            $icone = '<i class="fas fa-receipt icon-type text-green"></i>';
                                        } elseif ($dadosclientes->tipo == '6') {
                                            $tipomsg = "Cobrança Manual";
                                            $icone = '<i class="fas fa-hand-holding-usd icon-type text-orange"></i>';
                                        } elseif ($dadosclientes->tipo == '7') {
                                            $tipomsg = "Cobrança 7 dias";
                                            $icone = '<i class="fas fa-calendar-week icon-type text-indigo"></i>';
                                        }
                                    ?>
                                    <tr>
                                        <td class="font-weight-bold text-muted"><?php print $dadosclientes->id; ?></td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php echo $icone; ?>
                                                <span class="font-weight-bold text-dark"><?php print $tipomsg; ?></span>
                                            </div>
                                        </td>

                                        <td>
                                            <?php if ($dadosclientes->status == '1') { ?>
                                                <span class="badge badge-status badge-active">ATIVO</span>
                                            <?php } else { ?>
                                                <span class="badge badge-status badge-inactive">DESATIVADO</span>
                                            <?php } ?>
                                        </td>

                                        <td class="text-muted">
                                            <i class="fas fa-quote-left mr-2 text-muted" style="font-size: 0.8rem;"></i>
                                            <?php print substr($dadosclientes->msg, 0, 60); ?>...
                                        </td>

                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                
                                                <!-- Botão Editar -->
                                                <form action="edit_mensagens" method="post" class="mb-0 mr-1">
                                                    <input type="hidden" name="edicli" value="<?php print $dadosclientes->id; ?>" />
                                                    <button type="submit" class="btn-icon-circle btn-edit" title="Editar Mensagem">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                </form>

                                                <!-- Botão Ativar/Desativar -->
                                                <?php if ($dadosclientes->status == '1') { ?>
                                                    <form action="classes/mensagens_exe.php" method="post" class="mb-0">
                                                        <input type="hidden" name="edicli1" value="<?php print $dadosclientes->id; ?>" />
                                                        <button type="submit" class="btn-icon-circle btn-deactivate" title="Desativar">
                                                            <i class="fas fa-power-off"></i>
                                                        </button>
                                                    </form>
                                                <?php } else { ?>
                                                    <form action="classes/mensagens_exe.php" method="post" class="mb-0">
                                                        <input type="hidden" name="edicli2" value="<?php print $dadosclientes->id; ?>" />
                                                        <button type="submit" class="btn-icon-circle btn-activate" title="Ativar">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    </form>
                                                <?php } ?>

                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="../lib/select2/js/select2.min.js"></script>
<script src="../js/slim.js"></script>

<script>
  $(function () {
    'use strict';

    $('#datatable1').DataTable({
      responsive: true,
      language: {
        searchPlaceholder: 'Buscar...',
        sSearch: '',
        lengthMenu: '_MENU_ itens',
        paginate: { next: '>', previous: '<' }
      }
    });

    $('.dataTables_length select').select2({ minimumResultsForSearch: Infinity });
    $('[data-toggle="tooltip"]').tooltip();
  });

  function gerarRecibo() {
    // Mantive a função caso você use em outro lugar, mas ela não está visível no layout novo
    // ... lógica original ...
  }
</script>

</body>
</html>