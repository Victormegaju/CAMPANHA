<?php
// --- INÍCIO DA LÓGICA PHP ---

// 1. DATA DE HOJE (FIXA)
// Essa variável precisa ser a data atual real, independente do filtro de mês.
$datam2 = date("d/m/Y"); 
// OBS: Se no seu banco a data for salva como '2025-12-16' (ano-mês-dia), 
// descomente a linha abaixo e comente a de cima:
// $datam2 = date("Y-m-d");

if (isset($_GET["mes"])) {
  $mmm = $_GET["mes"];
  $aaa = date("Y");
  $ddd = date("d");
  $datam = $mmm . "/" . $aaa;
  // $datam2 removido daqui para não bugar a lógica do "Hoje"
  $datam3 = $mmm . "/" . $aaa;
} else {
  $datam = date("m/Y");
  $mmm = date("m");
  $aaa = date("Y");
  // $datam2 removido daqui
  $datam3 = date("Y-m");
}

// SOMA VALORES A RECEBER
$valoresareceber = $connect->query("SELECT SUM(parcela) AS totalparcela FROM financeiro2 WHERE status='1' AND datapagamento LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$valoresareceberx = $valoresareceber->fetch(PDO::FETCH_OBJ);

// SOMA VALORES RECEBIDOS (MÊS)
$valoresrecebidos = $connect->query("SELECT SUM(parcela) AS totalpago FROM financeiro2 WHERE status='2' AND pagoem LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$valoresrecebidoss = $valoresrecebidos->fetch(PDO::FETCH_OBJ);

// SOMA VALORES RECEBIDOS (HOJE - CORRIGIDO)
// Usamos LIKE para garantir que encontre mesmo se houver espaços ou horários
$valoresrecebidosh = $connect->query("SELECT SUM(parcela) AS totalrh FROM financeiro2 WHERE status='2' AND pagoem LIKE '%" . $datam2 . "%' AND idm ='" . $cod_id . "'");
$valoresrecebidossh = $valoresrecebidosh->fetch(PDO::FETCH_OBJ);

// EMPRÉSTIMOS ATIVOS
$empativos = $connect->query("SELECT * FROM financeiro1 WHERE status='1' AND idm ='" . $cod_id . "'");
$empativosx = $empativos->rowCount();

// PARCELAS ABERTAS
$parcelasab = $connect->query("SELECT * FROM financeiro2 WHERE status='1' AND datapagamento LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$parcelasabx = $parcelasab->rowCount();

// PARCELAS PAGAS
$parcelasap = $connect->query("SELECT * FROM financeiro2 WHERE status='2' AND pagoem LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$parcelasapx = $parcelasap->rowCount();

// CLIENTES
$cadcli = $connect->query("SELECT * FROM clientes WHERE idm ='" . $cod_id . "'");
$cadclix = $cadcli->rowCount();

// SOMA VALORES CONTAS A PAGAR
$valoresapagar = $connect->query("SELECT SUM(valor) AS totalapagar FROM financeiro3 WHERE status='1' AND datavencimento LIKE '%" . $datam . "%' AND idm ='" . $cod_id . "'");
$valoresapagarx = $valoresapagar->fetch(PDO::FETCH_OBJ);

// SOMA VALORES CONTAS PAGAS
$valorespagos = $connect->query("SELECT SUM(valor) AS totalpago FROM financeiro3 WHERE status='2' AND datapagamento LIKE '%" . $datam3 . "%' AND idm ='" . $cod_id . "'");
$valorespagosx = $valorespagos->fetch(PDO::FETCH_OBJ);

$meses = array(
  '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
  '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
  '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
);
$mes = $meses[$mmm];
$ano = date('Y');
$url_base = "https://$_SERVER[HTTP_HOST]";
?>

<head>
    <!-- Certifique-se de que não há outros CSS conflitando -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* Customizações adicionais específicas da página home */
        .page-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 0;
        }
        
        .page-subtitle {
            opacity: 0.7;
            font-size: 0.9rem;
        }
        
        /* Botões de Ação */
        .btn-action-dash {
            display: block; 
            width: 100%; 
            padding: 15px;
            border-radius: 12px; 
            text-align: center; 
            color: #fff;
            font-weight: bold; 
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .btn-action-dash:hover { 
            opacity: 0.9; 
            color: #fff; 
            text-decoration: none;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15);
        }

        /* Chart Container */
        .chart-box { 
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 20px; 
            border-radius: 20px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        body.dark-mode .chart-box {
            background: rgba(26, 31, 46, 0.95);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        /* Summary Card */
        .summary-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px; 
            padding: 25px;
            border-left: 5px solid #667eea;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            display: flex; 
            align-items: center; 
            justify-content: space-between;
        }
        
        body.dark-mode .summary-card {
            background: rgba(26, 31, 46, 0.95);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            border-left-color: #f093fb;
        }
    </style>
</head>

<div class="slim-mainpanel">
  <div class="container">

    <!-- CABEÇALHO -->
    <div class="d-flex align-items-center justify-content-between mb-4 mt-2">
      <div>
        <h4 class="page-title">PAINEL FINANCEIRO</h4>
        <p class="page-subtitle mb-0">Referência: <span class="badge badge-light border px-2"><?php print $mes; ?> / <?php print $ano; ?></span></p>
      </div>
      
      <div class="d-flex align-items-center">
        <!-- API STATUS -->
        <?php
        // Verificação simples para evitar erro caso a tabela conexoes esteja vazia ou com erro
        $v_conexao = 0;
        try {
            $stmt = $connect->query("SELECT count(id) FROM conexoes WHERE id_usuario = '" . $cod_id . "' AND conn = '1'");
            if($stmt) $v_conexao = $stmt->fetchColumn();
        } catch (Exception $e) { $v_conexao = 0; }

        if ($v_conexao == "0") { ?>
            <a href="whatsapp" class="btn btn-light text-danger shadow-sm mr-2 font-weight-bold" style="border-radius: 50px;"><i class="fab fa-whatsapp mr-1"></i> Desconectado</a>
        <?php } else {
            $idins = $dadosgerais->tokenapi;
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $urlapi . '/instance/connectionState/AbC123' . $idins,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 2,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array('apikey: ' . $idins . ''),
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $res = json_decode($response, true);
            $conexaoo = isset($res['instance']['state']) ? $res['instance']['state'] : 'close';

            if ($conexaoo == 'open') { ?>
                <a href="whatsapp" class="btn btn-light text-success shadow-sm mr-2 font-weight-bold" style="border-radius: 50px;"><i class="fab fa-whatsapp mr-1"></i> Conectado</a>
            <?php } else {
                $connect->query("UPDATE conexoes SET qrcode='', conn = '0', apikey = '0' WHERE id_usuario = '" . $cod_id . "'"); ?>
                <a href="whatsapp" class="btn btn-light text-danger shadow-sm mr-2 font-weight-bold" style="border-radius: 50px;"><i class="fab fa-whatsapp mr-1"></i> Desconectado</a>
            <?php } 
        } ?>

        <!-- SELETOR DE MÊS -->
        <div class="dropdown">
          <button class="btn btn-primary dropdown-toggle shadow-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" style="border-radius: 50px;">
            <i class="far fa-calendar-alt mr-2"></i> Mês
          </button>
          <div class="dropdown-menu dropdown-menu-right shadow" aria-labelledby="dropdownMenuButton" style="border-radius: 12px; border:none;">
            <a class="dropdown-item" href="./">Mês Atual</a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="&mes=01">Janeiro</a>
            <a class="dropdown-item" href="&mes=02">Fevereiro</a>
            <a class="dropdown-item" href="&mes=03">Março</a>
            <a class="dropdown-item" href="&mes=04">Abril</a>
            <a class="dropdown-item" href="&mes=05">Maio</a>
            <a class="dropdown-item" href="&mes=06">Junho</a>
            <a class="dropdown-item" href="&mes=07">Julho</a>
            <a class="dropdown-item" href="&mes=08">Agosto</a>
            <a class="dropdown-item" href="&mes=09">Setembro</a>
            <a class="dropdown-item" href="&mes=10">Outubro</a>
            <a class="dropdown-item" href="&mes=11">Novembro</a>
            <a class="dropdown-item" href="&mes=12">Dezembro</a>
          </div>
        </div>
      </div>
    </div>

    <!-- LINHA 1: FINANCEIRO PRINCIPAL -->
    <div class="row row-xs mb-4">
      <!-- Recebidos Mês (Color 4 - Verde) -->
      <div class="col-sm-6 col-lg-4 mb-3 mb-lg-0">
        <div class="stat-card">
          <div class="stat-icon color-4">
            <i class="fas fa-hand-holding-usd"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Recebidos (Mês)</div>
            <div class="stat-value">R$ <?php echo number_format($valoresrecebidoss->totalpago ?? 0, 2, ',', '.'); ?></div>
          </div>
        </div>
      </div>

      <!-- Recebidos Hoje (Color 3 - Azul) -->
      <div class="col-sm-6 col-lg-4 mb-3 mb-lg-0">
        <div class="stat-card">
          <div class="stat-icon color-3">
            <i class="fas fa-calendar-day"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Recebidos (Hoje)</div>
            <div class="stat-value">R$ <?php echo number_format($valoresrecebidossh->totalrh ?? 0, 2, ',', '.'); ?></div>
          </div>
        </div>
      </div>

      <!-- A Receber (Color 8 - Amarelo/Laranja) -->
      <div class="col-sm-6 col-lg-4">
        <div class="stat-card">
          <div class="stat-icon color-8">
            <i class="fas fa-clock"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">A Receber (Mês)</div>
            <div class="stat-value">R$ <?php echo number_format($valoresareceberx->totalparcela ?? 0, 2, ',', '.'); ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- LINHA 2: ESTATÍSTICAS (CORES ÚNICAS) -->
    <div class="row row-xs mb-4">
      <!-- Clientes (Color 1 - Roxo) -->
      <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card">
          <div class="stat-icon color-1">
            <i class="fas fa-users"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Clientes</div>
            <div class="stat-value"><?php echo $cadclix; ?></div>
          </div>
        </div>
      </div>

      <!-- Cobranças Ativas (Color 6 - Teal) -->
      <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card">
          <div class="stat-icon color-6">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Cobranças Ativas</div>
            <div class="stat-value"><?php echo $empativosx; ?></div>
          </div>
        </div>
      </div>

      <!-- Mensalidades Aberto (Color 9 - Vermelho) -->
      <div class="col-sm-6 col-lg-3 mb-3 mb-lg-0">
        <div class="stat-card">
          <div class="stat-icon color-9">
            <i class="fas fa-exclamation-circle"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Em Aberto</div>
            <div class="stat-value"><?php echo $parcelasabx; ?></div>
          </div>
        </div>
      </div>

      <!-- Mensalidades Pagas (Color 2 - Rosa) -->
      <div class="col-sm-6 col-lg-3">
        <div class="stat-card">
          <div class="stat-icon color-2">
            <i class="fas fa-check-double"></i>
          </div>
          <div class="stat-content">
            <div class="stat-label">Pagas</div>
            <div class="stat-value"><?php echo $parcelasapx; ?></div>
          </div>
        </div>
      </div>
    </div>

    <!-- LINHA 3: RESUMO CONTAS A PAGAR -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="summary-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon color-18 mr-3">
                        <i class="fas fa-wallet"></i>
                    </div>
                    <div>
                        <h6 class="font-weight-bold mb-1">CONTAS A PAGAR</h6>
                        <small class="opacity-75">Resumo de saídas do mês</small>
                    </div>
                </div>
                <div class="text-right">
                    <div class="d-inline-block mr-4 text-center">
                        <small class="d-block font-weight-bold" style="color: #43e97b;">PAGOS</small>
                        <span class="h5 font-weight-bold">R$ <?php echo number_format($valorespagosx->totalpago ?? 0, 2, ',', '.'); ?></span>
                    </div>
                    <div class="d-inline-block text-center">
                        <small class="d-block font-weight-bold" style="color: #ff6b6b;">A PAGAR</small>
                        <span class="h5 font-weight-bold">R$ <?php echo number_format($valoresapagarx->totalapagar ?? 0, 2, ',', '.'); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- LINHA 4: BOTÕES DE AÇÃO -->
    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <a href="clientes" class="btn-action-dash" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <i class="fas fa-user-plus mr-2"></i> Cadastrar Clientes
            </a>
        </div>
        <div class="col-md-4 mb-2">
            <a href="contas_pagar" class="btn-action-dash" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);">
                <i class="fas fa-minus-circle mr-2"></i> Contas a Pagar
            </a>
        </div>
        <div class="col-md-4 mb-2">
            <a href="contas_receber" class="btn-action-dash" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <i class="fas fa-plus-circle mr-2"></i> Contas a Receber
            </a>
        </div>
    </div>

    <!-- LINHA 5: GRÁFICO -->
    <div class="row mb-5">
      <div class="col-lg-12">
        <div class="chart-box">
            <h6 class="font-weight-bold mb-4 ml-2">Fluxo Financeiro Gráfico</h6>
            <canvas id="myChart" style="max-height: 400px;"></canvas>
        </div>
      </div>
    </div>

  </div><!-- container -->
</div><!-- slim-mainpanel -->

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/popper.js/js/popper.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>

<script>
  // Adicionado verificação '?? 0' para evitar erro de JS se vier nulo do banco
  var valoresRecebidosMes = <?php echo json_encode(isset($valoresrecebidoss->totalpago) ? $valoresrecebidoss->totalpago : 0); ?>;
  var valoresRecebidosHoje = <?php echo json_encode(isset($valoresrecebidossh->totalrh) ? $valoresrecebidossh->totalrh : 0); ?>;
  var valoresAReceberMes = <?php echo json_encode(isset($valoresareceberx->totalparcela) ? $valoresareceberx->totalparcela : 0); ?>;
  var clientesCadastrados = <?php echo json_encode($cadclix ? $cadclix : 0); ?>;
  var cobrancasAtivas = <?php echo json_encode($empativosx ? $empativosx : 0); ?>;
  var mensalidadesAberto = <?php echo json_encode($parcelasabx ? $parcelasabx : 0); ?>;
  var mensalidadesPagas = <?php echo json_encode($parcelasapx ? $parcelasapx : 0); ?>;

  var ctx = document.getElementById('myChart').getContext('2d');
  var myChart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: ['Recebidos (Mês)', 'Recebidos (Hoje)', 'A Receber', 'Clientes', 'Ativas', 'Em Aberto', 'Pagas'],
      datasets: [{
        label: 'Valores e Quantidades',
        data: [valoresRecebidosMes, valoresRecebidosHoje, valoresAReceberMes, clientesCadastrados, cobrancasAtivas, mensalidadesAberto, mensalidadesPagas],
        backgroundColor: [
          '#2ecc71', // Verde
          '#3498db', // Azul
          '#f39c12', // Laranja
          '#9b59b6', // Roxo
          '#1abc9c', // Teal
          '#e74c3c', // Vermelho
          '#e84393'  // Rosa
        ],
        borderWidth: 0,
        borderRadius: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        y: { beginAtZero: true, grid: { color: '#f0f0f0' } },
        x: { grid: { display: false } }
      },
      plugins: {
          legend: { display: false }
      }
    }
  });
</script>
<script src="../js/slim.js"></script>
</body>
</html>