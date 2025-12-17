<?php require_once "topo.php"; ?>
<?php
  $cliente = $_POST['cliente'];
  $fpagamento = '30';
  $parcelas = $_POST['parcelas'];

  $valor = $_POST['valor'];
  $valor = str_replace(".", "", $valor);
  $valor = str_replace(",", ".", $valor);

  $valorparcela = $valor;

  $datap = date('d/m/Y', strtotime($_POST["datap"]));

  $vencimento_primeira_parcela = explode('/', $datap);

  $dia = $vencimento_primeira_parcela[0];
  $mes = $vencimento_primeira_parcela[1];
  $ano = $vencimento_primeira_parcela[2];
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
        
        /* Inputs Desabilitados com visual clean */
        .form-control[disabled], .form-control[readonly] {
            background-color: #fff;
            opacity: 1;
            border-radius: 0 8px 8px 0; 
            border: 1px solid #e0e0e0;
            border-left: 0;
            height: 45px;
            color: #495057;
        }
        
        /* Ícones Coloridos na lateral esquerda */
        .input-group-text { 
            border-radius: 8px 0 0 8px; 
            border: 1px solid #e0e0e0; 
            border-right: 0;
            width: 45px;
            justify-content: center;
        }

        /* Cores Específicas dos Ícones (Sem repetir no cabeçalho) */
        .icon-green { color: #28a745; background-color: #e8f5e9; }     /* Valor Total */
        .icon-blue { color: #007bff; background-color: #e6f2ff; }      /* Forma Pagamento */
        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }    /* Qtd Parcelas */
        .icon-orange { color: #fd7e14; background-color: #fff5eb; }    /* Primeira Data */
        
        /* Cores para a Lista de Parcelas */
        .icon-teal { color: #20c997; background-color: #e6fffa; }      /* Valor Parcela */
        .icon-red { color: #dc3545; background-color: #ffeef0; }       /* Data Parcela */

        /* Botões */
        .btn-gradient-success { 
            background: linear-gradient(135deg, #28a745, #20c997); 
            color: white; 
            border: none;
            transition: all 0.3s;
        }
        .btn-gradient-success:hover { 
            background: linear-gradient(135deg, #218838, #1e7e34); 
            color: white; 
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>

  <div class="slim-mainpanel">
    <div class="container-fluid">
      
      <!-- Header -->
      <div class="row mb-4">
          <div class="col-md-12">
              <div class="d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center">
                      <div class="mr-3" style="background: linear-gradient(135deg, #6610f2, #4e73df); padding: 12px; border-radius: 10px;">
                          <i class="fas fa-file-invoice fa-lg text-white"></i>
                      </div>
                      <div>
                          <h4 class="mb-0 text-dark font-weight-bold">DEMONSTRATIVO</h4>
                          <p class="text-muted mb-0">Confira os dados antes de finalizar</p>
                      </div>
                  </div>
                  <a href="cad_contas" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                      <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                  </a>
              </div>
          </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body p-4">
              
              <!-- Nome do Cliente -->
              <div class="row mb-4">
                <div class="col-md-12 text-center">
                    <?php $buscacli = $connect->query("SELECT * FROM clientes WHERE Id='$cliente'"); $buscaclix = $buscacli->fetch(PDO::FETCH_OBJ); ?>
                    <h5 class="text-muted small text-uppercase mb-1">Cliente Selecionado</h5>
                    <h3 class="font-weight-bold text-dark">
                        <i class="fas fa-user-circle text-primary mr-2"></i><?= $buscaclix->nome; ?>
                    </h3>
                </div>
              </div>

              <hr class="mb-4">

              <!-- Resumo Geral (4 Colunas) -->
              <div class="row mb-4">
                <!-- Valor Total -->
                <div class="col-md-3 mb-3">
                  <label class="font-weight-bold text-dark small">Valor Total</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-green"><i class="fas fa-dollar-sign"></i></span>
                      </div>
                      <input type="text" class="form-control" value="R$ <?php print number_format($valor * $parcelas, 2, ',', '.'); ?>" disabled>
                  </div>
                </div>

                <!-- Forma Pagamento -->
                <div class="col-md-3 mb-3">
                  <label class="font-weight-bold text-dark small">Forma de Pagamento</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-blue"><i class="fas fa-credit-card"></i></span>
                      </div>
                      <?php if ($fpagamento == 30) { ?>
                        <input type="text" class="form-control" value="Mensal" disabled>
                      <?php } ?>
                  </div>
                </div>

                <!-- Qtd Parcelas -->
                <div class="col-md-3 mb-3">
                  <label class="font-weight-bold text-dark small">Qtd. Parcelas</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-purple"><i class="fas fa-layer-group"></i></span>
                      </div>
                      <input type="text" class="form-control" value="<?php print $parcelas; ?>" disabled>
                  </div>
                </div>

                <!-- Primeira Data -->
                <div class="col-md-3 mb-3">
                  <label class="font-weight-bold text-dark small">Primeira Parcela</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-orange"><i class="far fa-calendar-alt"></i></span>
                      </div>
                      <input type="text" class="form-control" value="<?php print $datap; ?>" disabled>
                  </div>
                </div>
              </div>

              <hr class="mb-4" />
              <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-list-ul mr-2"></i> Detalhamento das Parcelas</h6>

              <!-- Loop das Parcelas -->
              <div class="row">
                <?php
                for ($parcela = 0; $parcela < $parcelas; $parcela++) {
                  $data = new DateTime();
                  $data->setDate($ano, $mes, $dia);
                  $data->modify('+' . $parcela . ' month');
                  if ($data->format('d') != $dia) {
                    $data->modify('last day of previous month');
                  }
                  $qwerr = $data->format('d/m/Y');              
                ?>
                
                <div class="col-md-6 mb-3">
                    <div class="card bg-light border-0 p-3" style="border-radius: 8px;">
                        <div class="row">
                            <div class="col-6">
                                <label class="small text-muted mb-1">Valor da Parcela</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-teal" style="border:none;"><i class="fas fa-money-bill-wave"></i></span>
                                    </div>
                                    <input type="text" class="form-control bg-white border-0" value="R$ <?php print number_format($valorparcela, 2, ',', '.'); ?>" disabled>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted mb-1">Vencimento</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-red" style="border:none;"><i class="fas fa-calendar-day"></i></span>
                                    </div>
                                    <input type="text" class="form-control bg-white border-0" value="<?php print $qwerr; ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php } ?>
              </div>

              <hr class="mt-4 mb-4">

              <!-- Botões de Ação -->
              <div class="row">
                <div class="col-md-6 mb-2">
                    <a href="cad_contas" class="btn btn-secondary btn-lg btn-block shadow-sm" style="border-radius: 8px;">
                      <i class="fa fa-arrow-left mr-2"></i> Corrigir Dados
                    </a>
                </div>

                <div class="col-md-6 mb-2">
                  <form action="classes/simulador_exe.php" method="post">
                    <input type="hidden" name="idcliente" value="<?php print $cliente; ?>">
                    <input type="hidden" name="formapagamento" value="<?php print $fpagamento; ?>">
                    <input type="hidden" name="parcelas" value="<?php print $parcelas; ?>">
                    <input type="hidden" name="dataparcela" value="<?php print $datap; ?>">
                    <input type="hidden" name="dataparcelax" value="<?php print $_POST["datap"]; ?>">
                    <input type="hidden" name="idpedido" value="<?php print $better_token = md5(uniqid(rand(), true)); ?>">
                    <input type="hidden" name="vparcela" value="<?php print $valorparcela; ?>">

                    <button type="submit" class="btn btn-gradient-success btn-lg btn-block shadow-sm" name="cart" style="border-radius: 8px;">
                        CONFIRMAR CADASTRO <i class="fa fa-check ml-2"></i>
                    </button>
                  </form>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../lib/jquery/js/jquery.js"></script>
  <script src="../lib/bootstrap/js/bootstrap.js"></script>
  <script src="../js/slim.js"></script>
</body>
</html>