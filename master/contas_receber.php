<?php
require_once "topo.php";

// Lógica de Datas
if (isset($_GET["mes"])) {
  $mmm = $_GET["mes"];
  $aaa = date("Y");
  $datam = $mmm . "/" . $aaa;
} else {
  $datam = date("m/Y");
  $mmm = date("m");
}

// SOMAS E CONTAGENS (MANTIDAS)
$valoresareceber = $connect->query("SELECT SUM(parcela) AS totalparcela FROM financeiro2 WHERE status='1' AND datapagamento LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$valoresareceberx = $valoresareceber->fetch(PDO::FETCH_OBJ);

$valoresrecebidos = $connect->query("SELECT SUM(parcela) AS totalpago FROM financeiro2 WHERE status='2' AND pagoem LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$valoresrecebidoss = $valoresrecebidos->fetch(PDO::FETCH_OBJ);

$empativos = $connect->query("SELECT * FROM financeiro1 WHERE status='1' AND idm ='" . $cod_id . "'");
$empativosx = $empativos->rowCount();

$parcelasab = $connect->query("SELECT * FROM financeiro2 WHERE status='1' AND datapagamento LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$parcelasabx = $parcelasab->rowCount();

$parcelasap = $connect->query("SELECT * FROM financeiro2 WHERE status='2' AND pagoem LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$parcelasapx = $parcelasap->rowCount();

$cadcli = $connect->query("SELECT * FROM clientes WHERE idm ='" . $cod_id . "'");
$cadclix = $cadcli->rowCount();

$meses = array('01'=>'Janeiro','02'=>'Fevereiro','03'=>'Março','04'=>'Abril','05'=>'Maio','06'=>'Junho','07'=>'Julho','08'=>'Agosto','09'=>'Setembro','10'=>'Outubro','11'=>'Novembro','12'=>'Dezembro');
$mes_extenso = $meses[$mmm];
$ano = date('Y');
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        
        /* Dashboard Icons Gradients */
        .bg-icon-blue { background: linear-gradient(135deg, #4e73df, #224abe); }
        .bg-icon-purple { background: linear-gradient(135deg, #6f42c1, #593196); }
        .bg-icon-orange { background: linear-gradient(135deg, #fd7e14, #d65f02); }
        .bg-icon-green { background: linear-gradient(135deg, #28a745, #1e7e34); }
        .bg-icon-red { background: linear-gradient(135deg, #dc3545, #a71d2a); }

        .icon-box {
            width: 50px; height: 50px; 
            border-radius: 12px; 
            display: flex; align-items: center; justify-content: center;
            color: white; margin-right: 15px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        /* Ícones Pequenos na Tabela */
        .mini-icon {
            width: 30px; height: 30px; border-radius: 50%; display: inline-flex;
            align-items: center; justify-content: center; margin-right: 8px; font-size: 0.8rem;
        }
        .mi-blue { background-color: #e6f0ff; color: #4e73df; }
        .mi-orange { background-color: #fff5e6; color: #fd7e14; }
        .mi-purple { background-color: #f3e6ff; color: #6f42c1; }
        .mi-teal { background-color: #e0f2f1; color: #00897b; }

        /* Tabela Desktop */
        .d-none.d-lg-block .table td, .d-none.d-lg-block .table th { padding: 12px 10px; vertical-align: middle; }
        .d-none.d-lg-block .table-hover tbody tr:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); transition: all 0.3s ease; }

        /* Mobile Cards */
        .mobile-card { transition: all 0.3s ease; }
        .mobile-card:hover { transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.1) !important; }
    </style>
</head>
<body>

<div class="slim-mainpanel">
  <div class="container-fluid">

      <!-- Header -->
      <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="d-flex align-items-center mb-2">
                    <div class="mr-3 bg-icon-red icon-box">
                        <i class="fas fa-hand-holding-usd fa-lg"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">CONTAS A RECEBER</h4>
                        <p class="text-muted mb-0">Referente a <?php print $mes_extenso; ?> de <?php print $ano; ?></p>
                    </div>
                </div>
                
                <div class="d-flex">
                    <a href="cad_contas" class="btn btn-danger mg-r-5" style="border-radius: 8px;">
                        <i class="fa fa-plus mg-r-5"></i> Nova Cobrança
                    </a>
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown" style="border-radius: 8px;">
                            <i class="icon ion-ios-calendar-outline"></i> Mês
                        </button>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a class="dropdown-item" href="./">Mês Atual</a>
                            <?php foreach($meses as $k => $v): ?>
                                <a class="dropdown-item" href="&mes=<?=$k?>"><?=$v?></a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
      </div>

      <!-- Resumo Financeiro -->
      <div class="card border-0 mb-4 bg-white">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="tx-inverse mg-b-5">Resumo Financeiro</h5>
                    <p class="mg-b-0 text-muted">Balanço do mês.</p>
                </div>
                <div class="d-flex text-right">
                    <div class="ml-4">
                        <h2 class="text-success mb-0" style="font-weight: 700;">R$ <?php echo number_format($valoresrecebidoss->totalpago, 2, ',', '.'); ?></h2>
                        <span class="text-muted small text-uppercase">Recebidos</span>
                    </div>
                    <div class="ml-4">
                        <h2 class="text-primary mb-0" style="font-weight: 700;">R$ <?php echo number_format($valoresareceberx->totalparcela, 2, ',', '.'); ?></h2>
                        <span class="text-muted small text-uppercase">A Receber</span>
                    </div>
                </div>
            </div>
          </div>
      </div>

      <!-- TABELA DESKTOP -->
      <div class="card border-0 d-none d-lg-block">
          <div class="card-body p-4">
            <div class="table-responsive">
                <table id="datatable1" class="table table-hover w-100">
                    <thead>
                        <tr style="background: linear-gradient(90deg, #f8f9fa, #e9ecef);">
                            <th style="border-radius: 8px 0 0 8px;">ID COB.</th>
                            <th>CLIENTE</th>
                            <th>PRÓX. VENCIMENTO</th>
                            <th>PRAZO</th>
                            <th>PARCELAS</th>
                            <th>STATUS</th>
                            <th class="text-center" style="border-radius: 0 8px 8px 0;">AÇÕES</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Busca todas as cobranças ativas (financeiro1)
                        $emprestimos = $connect->query("SELECT * FROM financeiro1 WHERE status='1' AND idm = '" . $cod_id . "' ORDER BY Id DESC");
                        
                        while ($dadosemprestimos = $emprestimos->fetch(PDO::FETCH_OBJ)) {
                            // Busca os dados do cliente associado
                            $clientes = $connect->query("SELECT * FROM clientes WHERE Id='" . $dadosemprestimos->idc . "' AND idm = '" . $cod_id . "'");
                            while ($dadosclientes = $clientes->fetch(PDO::FETCH_OBJ)) {
                                
                                // Lógica de Data e Dias Restantes (Busca a próxima fatura em aberto DESTA cobrança específica)
                                $buscaprox = $connect->query("SELECT datapagamento FROM financeiro2 WHERE chave='" . $dadosemprestimos->chave . "' AND status='1' ORDER BY id ASC LIMIT 1");
                                $proxima = $buscaprox->fetch(PDO::FETCH_OBJ);
                                
                                $status_texto = "Concluído";
                                $classe_dias = "success";
                                $texto_dias = "Quitado";
                                $data_venc = "---";

                                if($proxima) {
                                    $data_venc = $proxima->datapagamento;
                                    $dt_venc = DateTime::createFromFormat('d/m/Y', $data_venc);
                                    $dt_hoje = new DateTime();
                                    
                                    if($dt_venc){
                                        $diff = $dt_hoje->diff($dt_venc);
                                        $dias = $diff->days;
                                        
                                        if($diff->invert) { // Atrasado
                                            $status_texto = "ATRASADO";
                                            $classe_dias = "danger";
                                            $texto_dias = "Atrasado (" . $dias . "d)";
                                        } else {
                                            $status_texto = "EM ABERTO";
                                            if($dias == 0) {
                                                $classe_dias = "warning";
                                                $texto_dias = "Vence Hoje";
                                            } else {
                                                $classe_dias = "info";
                                                $texto_dias = "Faltam " . $dias . " dias";
                                            }
                                        }
                                    }
                                }
                        ?>
                        <tr>
                            <td>
                                <span class="badge badge-light border">#<?php print $dadosemprestimos->Id; ?></span>
                            </td>
                            
                            <!-- Cliente -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mini-icon mi-blue"><i class="fas fa-user"></i></div>
                                    <div>
                                        <div class="font-weight-bold text-dark"><?php print $dadosclientes->nome; ?></div>
                                        <small class="text-muted">Entrada: <?php print $dadosemprestimos->entrada; ?></small>
                                    </div>
                                </div>
                            </td>

                            <!-- Vencimento -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mini-icon mi-orange"><i class="fas fa-calendar-alt"></i></div>
                                    <span class="font-weight-bold text-dark"><?php print $data_venc; ?></span>
                                </div>
                            </td>

                            <!-- Dias Restantes -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mini-icon mi-purple"><i class="fas fa-hourglass-half"></i></div>
                                    <span class="badge badge-<?php print $classe_dias; ?> p-2"><?php print $texto_dias; ?></span>
                                </div>
                            </td>

                            <!-- Parcelas -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="mini-icon mi-teal"><i class="fas fa-layer-group"></i></div>
                                    <span><?php print $dadosemprestimos->parcelas; ?>x</span>
                                </div>
                            </td>
                            
                            <!-- Status -->
                            <td>
                                <?php if ($status_texto == "ATRASADO") { ?>
                                    <span class="badge badge-danger p-2">EM ATRASO</span>
                                <?php } else { ?>
                                    <span class="badge badge-success p-2">EM DIA</span>
                                <?php } ?>
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center" style="gap: 5px;">
                                    <!-- Botão Ver Finanças (Passa o ID da Cobrança, não do cliente, se possível, ou usa lógica no backend) -->
                                    <form action="ver_financeiro" method="post" class="mb-0">
                                        <!-- Aqui passamos o ID da cobrança (financeiro1) -->
                                        <input type="hidden" name="vercli" value="<?php print $dadosemprestimos->Id; ?>" />
                                        <button type="submit" class="btn btn-sm btn-info rounded" title="Ver Detalhes"><i class="fas fa-search"></i></button>
                                    </form>
                                    
                                    <!-- Botão Excluir -->
                                    <form action="classes/clientes_exe.php" method="post" class="mb-0">
                                        <input type="hidden" name="delfin" value="<?php print $dadosemprestimos->chave; ?>" />
                                        <input type="hidden" name="idfin" value="<?php print $dadosemprestimos->Id; ?>" />
                                        <button type="submit" class="btn btn-sm btn-dark rounded" onclick='return confirm("Excluir esta cobrança?");' title="Excluir"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php } } ?>
                    </tbody>
                </table>
            </div>
          </div>
      </div>

      <!-- CARDS MOBILE -->
      <div class="d-block d-lg-none">
          <?php
            // Re-executa query para mobile
            $emprestimos_mobile = $connect->query("SELECT * FROM financeiro1 WHERE status='1' AND idm = '" . $cod_id . "' ORDER BY Id DESC");
            while ($dadosemprestimos = $emprestimos_mobile->fetch(PDO::FETCH_OBJ)) {
                $clientes_mobile = $connect->query("SELECT * FROM clientes WHERE Id='" . $dadosemprestimos->idc . "' AND idm = '" . $cod_id . "'");
                while ($dadosclientes = $clientes_mobile->fetch(PDO::FETCH_OBJ)) {
                    
                    // Lógica de Data Mobile
                    $buscaprox = $connect->query("SELECT datapagamento FROM financeiro2 WHERE chave='" . $dadosemprestimos->chave . "' AND status='1' ORDER BY id ASC LIMIT 1");
                    $proxima = $buscaprox->fetch(PDO::FETCH_OBJ);
                    
                    $status_texto = "Concluído";
                    $classe_dias = "success";
                    $texto_dias = "Pago";
                    $data_venc = "--/--/----";

                    if($proxima) {
                        $data_venc = $proxima->datapagamento;
                        $dt_venc = DateTime::createFromFormat('d/m/Y', $data_venc);
                        $dt_hoje = new DateTime();
                        
                        if($dt_venc){
                            $diff = $dt_hoje->diff($dt_venc);
                            $dias = $diff->days;
                            
                            if($diff->invert) {
                                $status_texto = "ATRASADO";
                                $classe_dias = "danger";
                                $texto_dias = "Atrasado (" . $dias . "d)";
                            } else {
                                $status_texto = "EM ABERTO";
                                if($dias == 0) { $classe_dias = "warning"; $texto_dias = "Vence Hoje"; }
                                else { $classe_dias = "info"; $texto_dias = "Faltam " . $dias . " dias"; }
                            }
                        }
                    }
          ?>
            <div class="card border-0 mb-3 mobile-card">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-icon-purple icon-box" style="width: 35px; height: 35px; font-size: 0.8rem; margin-right: 10px;">
                                #<?php print $dadosemprestimos->Id; ?>
                            </div>
                            <div>
                                <h6 class="mb-0 font-weight-bold text-dark"><?php print $dadosclientes->nome; ?></h6>
                                <small class="text-muted">Cadastro: <?php print $dadosemprestimos->entrada; ?></small>
                            </div>
                        </div>
                        <?php if ($status_texto == "ATRASADO") { ?>
                            <span class="badge badge-danger">ATRASADO</span>
                        <?php } else { ?>
                            <span class="badge badge-success">EM DIA</span>
                        <?php } ?>
                    </div>

                    <div class="row mb-2">
                        <div class="col-6">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">PRÓX. VENCIMENTO</small>
                            <span class="text-dark font-weight-bold small"><?php print $data_venc; ?></span>
                        </div>
                        <div class="col-6 text-right">
                            <small class="text-muted d-block" style="font-size: 0.7rem;">PRAZO</small>
                            <span class="badge badge-<?php print $classe_dias; ?>"><?php print $texto_dias; ?></span>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3 d-flex justify-content-between">
                        <form action="ver_financeiro" method="post" style="flex:1; margin: 0 2px;">
                            <input type="hidden" name="vercli" value="<?php print $dadosemprestimos->Id; ?>" />
                            <button type="submit" class="btn btn-sm btn-info w-100 rounded"> Ver Detalhes</button>
                        </form>
                        
                        <form action="classes/clientes_exe.php" method="post" style="flex:1; margin: 0 2px;">
                            <input type="hidden" name="delfin" value="<?php print $dadosemprestimos->chave; ?>" />
                            <input type="hidden" name="idfin" value="<?php print $dadosemprestimos->Id; ?>" />
                            <button type="submit" class="btn btn-sm btn-dark w-100 rounded" onclick='return confirm("Excluir?");'> Excluir</button>
                        </form>
                    </div>
                </div>
            </div>
          <?php } } ?>
      </div>

  </div>
</div>

<!-- Scripts -->
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/datatables/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>

<script>
  $(function () {
    'use strict';
    $('#datatable1').DataTable({
      "order": [[0, "desc"]], // Ordena pelo ID mais recente
      responsive: true,
      language: {
        search: "",
        searchPlaceholder: 'Buscar...',
        lengthMenu: '_MENU_',
        paginate: { next: '>', previous: '<' }
      }
    });
  });
</script>

<script src="../js/slim.js"></script>
</body>
</html>