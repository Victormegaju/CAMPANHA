<?php
require_once "topo.php";

// Lógica de Mês/Data
if(isset($_GET["mes"])) {
    $mmm = $_GET["mes"];
    $aaa = date("Y");
    $datam = $mmm."/".$aaa;
} else {
    $datam = date("m/Y");
    $mmm = date("m");
    $datam2 = date("Y-m");
}

// SOMA VALORES CONTAS A PAGAR
$valoresapagar  = $connect->query("SELECT SUM(valor) AS totalapagar FROM financeiro3 WHERE status='1' AND datavencimento LIKE '%".$datam."%' AND idm ='".$cod_id."'");
$valoresapagarx = $valoresapagar->fetch(PDO::FETCH_OBJ);

// SOMA VALORES CONTAS PAGAS
$valorespagos   = $connect->query("SELECT SUM(valor) AS totalpago FROM financeiro3 WHERE status='2' AND datapagamento LIKE '%".$datam2."%' AND idm ='".$cod_id."'");
$valorespagosx  = $valorespagos->fetch(PDO::FETCH_OBJ);

// PEGA NOME DO MÊS
$meses = array(
    '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
    '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
    '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
);
$mes = isset($meses[$mmm]) ? $meses[$mmm] : 'Atual';
$ano = date('Y');
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
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        /* Cores de Fundo Gradient */
        .bg-gradient-orange { background: linear-gradient(135deg, #fd7e14, #d35400); }
        .bg-gradient-green { background: linear-gradient(135deg, #28a745, #1e7e34); }
        .bg-gradient-red { background: linear-gradient(135deg, #dc3545, #c0392b); }
        .bg-gradient-blue { background: linear-gradient(135deg, #007bff, #0056b3); }
        .bg-gradient-purple { background: linear-gradient(135deg, #6f42c1, #5e35b1); }
        
        /* Botões de Ação */
        .btn-confirm-pay {
            background: linear-gradient(135deg, #20c997, #16a085);
            color: white; border: none; border-radius: 30px;
            padding: 6px 15px; font-weight: 600; font-size: 0.8rem;
            box-shadow: 0 3px 6px rgba(32, 201, 151, 0.3);
            transition: all 0.2s;
        }
        .btn-confirm-pay:hover { transform: translateY(-2px); box-shadow: 0 5px 10px rgba(32, 201, 151, 0.4); color: white; }

        .btn-icon-circle {
            width: 35px; height: 35px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            border: none; color: white; margin-left: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1); transition: all 0.2s;
        }
        .btn-icon-circle:hover { transform: translateY(-2px); }
        
        .btn-edit { background: linear-gradient(135deg, #3498db, #2980b9); }
        .btn-del { background: linear-gradient(135deg, #e74c3c, #c0392b); }
        
        /* Badges */
        .badge-pill-custom { padding: 8px 12px; border-radius: 30px; font-weight: 600; font-size: 0.75rem; letter-spacing: 0.5px; }
    </style>
</head>
<body>

<div class="slim-mainpanel">
    <div class="container-fluid">
        
        <!-- HEADER -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="mr-3 bg-gradient-orange" style="padding: 15px; border-radius: 12px;">
                        <i class="fas fa-wallet fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">CONTAS A PAGAR</h4>
                        <p class="text-muted mb-0">Referência: <strong class="text-primary"><?php print $mes;?> / <?php print $ano;?></strong></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-right mt-3 mt-md-0">
                <a href="cad_pagar" class="btn bg-gradient-blue text-white shadow-sm mr-2" style="border-radius: 8px; border:none;">
                    <i class="fas fa-plus-circle mr-2"></i> Nova Despesa
                </a>
                
                <div class="dropdown d-inline-block">
                    <button class="btn bg-gradient-purple text-white dropdown-toggle shadow-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="border-radius: 8px; border:none;">
                        <i class="far fa-calendar-alt mr-2"></i> Mudar Mês
                    </button>
                    <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownMenuButton" style="border-radius: 12px; border:none;">
                        <a class="dropdown-item" href="./contas_pagar">Mês Atual</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="contas_pagar&mes=01">Janeiro</a>
                        <a class="dropdown-item" href="contas_pagar&mes=02">Fevereiro</a>
                        <a class="dropdown-item" href="contas_pagar&mes=03">Março</a>
                        <a class="dropdown-item" href="contas_pagar&mes=04">Abril</a>
                        <a class="dropdown-item" href="contas_pagar&mes=05">Maio</a>
                        <a class="dropdown-item" href="contas_pagar&mes=06">Junho</a>
                        <a class="dropdown-item" href="contas_pagar&mes=07">Julho</a>
                        <a class="dropdown-item" href="contas_pagar&mes=08">Agosto</a>
                        <a class="dropdown-item" href="contas_pagar&mes=09">Setembro</a>
                        <a class="dropdown-item" href="contas_pagar&mes=10">Outubro</a>
                        <a class="dropdown-item" href="contas_pagar&mes=11">Novembro</a>
                        <a class="dropdown-item" href="contas_pagar&mes=12">Dezembro</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARDS DE RESUMO -->
        <div class="row mb-4">
            <div class="col-md-6 col-lg-6 mb-3">
                <div class="card p-4" style="border-left: 5px solid #28a745;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.8rem;">Total Pago</span>
                            <h2 class="mb-0 text-success font-weight-bold">R$ <?php echo number_format($valorespagosx->totalpago, 2, ',', '.'); ?></h2>
                        </div>
                        <div class="bg-gradient-green text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="fas fa-check"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6 mb-3">
                <div class="card p-4" style="border-left: 5px solid #dc3545;">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <span class="text-uppercase text-muted font-weight-bold" style="font-size: 0.8rem;">A Pagar</span>
                            <h2 class="mb-0 text-danger font-weight-bold">R$ <?php echo number_format($valoresapagarx->totalapagar, 2, ',', '.'); ?></h2>
                        </div>
                        <div class="bg-gradient-red text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="fas fa-arrow-down"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if(isset($_GET["sucesso"])){ ?>
        <div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px; border-left: 5px solid #28a745;">
            <i class="fas fa-check-circle mr-2"></i> <strong>Pronto!</strong> A operação foi realizada com sucesso.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <meta http-equiv="refresh" content="1;URL=./contas_pagar" />
        <?php } ?>

        <!-- TABELA DE CONTAS -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-dark mb-4 pl-2" style="border-left: 4px solid #6f42c1;">
                            LISTA DE DESPESAS
                        </h6>
                        
                        <div class="table-responsive">
                            <table id="datatable1" class="table table-hover w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="border-radius: 8px 0 0 8px;">ID</th>
                                        <th>DESCRIÇÃO</th>
                                        <th>VALOR</th>
                                        <th>VENCIMENTO</th>
                                        <th class="text-center">STATUS</th>
                                        <th class="text-center" style="border-radius: 0 8px 8px 0;">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $emprestimos = $connect->query("SELECT * FROM financeiro3 WHERE datavencimento LIKE '%".$datam."%' AND idm = '".$cod_id."'");
                                    while ($dadosemprestimos = $emprestimos->fetch(PDO::FETCH_OBJ)) {
                                    ?>
                                    <tr>
                                        <td class="align-middle"><strong>#<?php print $dadosemprestimos->id;?></strong></td>
                                        
                                        <td class="align-middle text-dark font-weight-bold">
                                            <i class="fas fa-tag text-muted mr-2" style="font-size: 0.8rem;"></i>
                                            <?php print $dadosemprestimos->descricao;?>
                                        </td>
                                        
                                        <td class="align-middle text-danger font-weight-bold">
                                            R$ <?php echo number_format($dadosemprestimos->valor, 2, ',', '.'); ?>
                                        </td>
                                        
                                        <td class="align-middle text-muted">
                                            <i class="fas fa-calendar-day text-info mr-1"></i> <?php print $dadosemprestimos->datavencimento;?>
                                        </td>
                                        
                                        <td class="align-middle text-center">
                                            <?php if($dadosemprestimos->status == 1){ 
                                                $atrz = 0;
                                                $data1 = date("d/m/Y");
                                                $data2 = $dadosemprestimos->datavencimento;
                                                
                                                $d1 = strtotime(implode('-', array_reverse(explode('/', $data1))));
                                                $d2 = strtotime(implode('-', array_reverse(explode('/', $data2))));

                                                if($d2 < $d1) {
                                                    echo '<span class="badge badge-danger badge-pill-custom">ATRASADO</span>'; 
                                                    $atrz = 1;
                                                }
                                                if($atrz <= 0){ 
                                                    echo '<span class="badge badge-warning badge-pill-custom text-white">EM ABERTO</span>'; 
                                                } 
                                            } else {
                                                echo '<span class="badge badge-success badge-pill-custom">PAGO</span>';
                                            } ?>
                                        </td>
                                        
                                        <td class="align-middle text-center">
                                            <div class="d-flex justify-content-center align-items-center">
                                                
                                                <?php if($dadosemprestimos->status == 2){ ?>
                                                    <!-- Já Pago -->
                                                    <button class="btn btn-sm btn-light text-success mr-2" style="border-radius: 20px;" disabled>
                                                        <i class="fas fa-check-double mr-1"></i> PAGO
                                                    </button>
                                                <?php } else { ?>
                                                    <!-- Botão CONFIRMAR PAGAMENTO (Destaque) -->
                                                    <form action="classes/baixa_exe.php" method="get" class="mb-0 mr-2">
                                                        <input type="hidden" name="idfin2" value="<?php print $dadosemprestimos->id;?>"/>
                                                        <input type="hidden" name="baixapagar" value="ok"/>
                                                        <button type="submit" class="btn-confirm-pay" title="Confirmar Pagamento">
                                                            <i class="fas fa-check mr-1"></i> Confirmar Pagamento
                                                        </button>
                                                    </form>    
                                                <?php } ?>
                                                
                                                <!-- Editar -->
                                                <form action="editar_pagamento" method="post" class="mb-0">
                                                    <input type="hidden" name="vercli" value="<?php print $dadosemprestimos->id;?>"/>
                                                    <button type="submit" class="btn-icon-circle btn-edit" title="Editar">
                                                        <i class="fas fa-pencil-alt"></i>
                                                    </button>
                                                </form>

                                                <!-- Excluir -->
                                                <form action="classes/apagar_exe.php" method="post" class="mb-0">
                                                    <input type="hidden" name="delfin" value="<?php print $dadosemprestimos->id;?>"/>
                                                    <button type="submit" class="btn-icon-circle btn-del" onclick='return pergunta();' title="Excluir">
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
            </div>
        </div>

    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
<script src="../js/slim.js"></script>

<script>
  $(function(){
    'use strict';
    $('#datatable1').DataTable({
      responsive: true,
      language: { searchPlaceholder: 'Buscar...', sSearch: '', lengthMenu: '_MENU_ itens', paginate: { next: '>', previous: '<' } }
    });
    $('[data-toggle="tooltip"]').tooltip();
  });

  function pergunta(){ 
     return confirm('Tem certeza que deseja excluir esta conta a pagar?');
  }
</script>
</body>
</html>