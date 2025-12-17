<?php
require_once "topo.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.dataTables.min.css" />
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 15px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        
        /* Botão Voltar */
        .btn-back { border-radius: 50px; padding: 8px 20px; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        
        /* Ícone do Cabeçalho */
        .header-icon {
            width: 50px; height: 50px; border-radius: 12px;
            background: linear-gradient(135deg, #28a745, #218838);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.5rem; margin-right: 15px;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }

        /* Tabela */
        .table th { border-top: none; font-weight: 700; color: #343a40; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        .table td { vertical-align: middle; padding: 15px; }
        
        /* Ícones da Tabela (Sem repetir cores) */
        .icon-col { margin-right: 8px; width: 25px; text-align: center; }
        .text-purple { color: #6f42c1; }
        .text-blue { color: #007bff; }
        .text-orange { color: #fd7e14; }
        .text-teal { color: #20c997; }

        /* Botão de Ação Redondo */
        .btn-view {
            width: 38px; height: 38px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            border: none; color: white; transition: all 0.2s;
            background: linear-gradient(135deg, #17a2b8, #117a8b);
            box-shadow: 0 3px 6px rgba(23, 162, 184, 0.3);
        }
        .btn-view:hover { transform: translateY(-2px); box-shadow: 0 5px 10px rgba(23, 162, 184, 0.4); color: white; }
    </style>
</head>
<body>

<div class="slim-mainpanel">
    <div class="container">
        
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="header-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">PAGAMENTOS FINALIZADOS</h4>
                        <p class="text-muted mb-0">Histórico de cobranças quitadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <a href="./" class="btn btn-secondary btn-back shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> VOLTAR
                </a>
            </div>
        </div>
		
		<?php if(isset($_GET["sucesso"])){ ?>
		<div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle mr-2"></i> <strong>Sucesso!</strong> Operação realizada.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
		<meta http-equiv="refresh" content="1;URL=./finalizados" />
		<?php } ?>
		
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body p-4">
                        
                        <div class="table-responsive">
                            <table id="datatable1" class="table table-hover w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 5%;">#ID</th>
                                        <th>Cliente</th>
                                        <th>Data Cadastro</th> 
                                        <th>Data da Baixa</th>
                                        <th class="text-center">Detalhes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $atrazado = "0"; // Mantido da lógica original
                                    $emprestimos = $connect->query("SELECT * FROM financeiro1 WHERE status='2' AND idm = '".$cod_id."'");
                                    
                                    while ($dadosemprestimos = $emprestimos->fetch(PDO::FETCH_OBJ)) {
                                        $clientes = $connect->query("SELECT * FROM clientes WHERE Id='".$dadosemprestimos->idc."' AND idm = '".$cod_id."'");
                                        while ($dadosclientes = $clientes->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                    <tr>
                                        <td class="font-weight-bold text-dark">
                                            <?php print $dadosemprestimos->Id;?>
                                        </td>
                                        
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-user-circle fa-lg icon-col text-blue"></i>
                                                <div>
                                                    <span class="font-weight-bold d-block text-dark"><?php print $dadosclientes->nome;?></span>
                                                    <small class="text-muted"><i class="fas fa-id-card mr-1"></i> <?php print $dadosclientes->cpf;?></small>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <td>
                                            <i class="far fa-calendar-alt icon-col text-orange"></i>
                                            <?php print $dadosemprestimos->entrada;?>
                                        </td>
                                        
                                        <td>
                                            <span class="badge badge-light border px-3 py-2">
                                                <i class="fas fa-calendar-check icon-col text-teal mr-1"></i>
                                                <?php print date_format(new DateTime($dadosemprestimos->pagoem),'d/m/Y');?>
                                            </span>
                                        </td>
                                        
                                        <td class="text-center">
                                            <form action="ver_financeiro_quitado" method="post" class="mb-0">
                                                <input type="hidden" name="vercli" value="<?php print $dadosemprestimos->Id;?>"/>
                                                <button type="submit" class="btn-view" title="Ver Detalhes">
                                                    <i class="fas fa-search-plus"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php } } ?>
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
  $(function(){
    'use strict';

    $('#datatable1').DataTable({
      "order": [[ 0, "desc" ]],
      responsive: true,
      language: {
        searchPlaceholder: 'Buscar...',
        sSearch: '',
        lengthMenu: '_MENU_ itens',
        paginate: { next: '>', previous: '<' }
      }
    });

    $('.dataTables_length select').select2({ minimumResultsForSearch: Infinity });
  });
</script>

<?php if($atrazado >= 1){ ?>
<script>
    var audio = new Audio('campainha.mp3');
    audio.addEventListener('canplaythrough', function() {
        audio.play();
    });
</script>
<?php } ?>

</body>
</html>