<?php
require_once "topo.php";

// Verifica se o ID do cliente foi recebido corretamente
$id_cliente = isset($_POST['vercli']) ? $_POST['vercli'] : '';
if(empty($id_cliente) && isset($_GET['vercli'])) $id_cliente = $_GET['vercli'];

// Dados do Formulário Anterior
$cliente = $_POST['cliente'];
$fpagamento = '30';
$parcelas = $_POST['parcelas'];
$valor = $_POST['valor'];
$valor = str_replace(".", "", $valor);
$valor = str_replace(",", ".", $valor);
$valorparcela = $valor;

// Tratamento de Data
$datap = date('d/m/Y', strtotime($_POST["datap"]));
$vencimento_primeira_parcela = explode('/', $datap);
$dia = $vencimento_primeira_parcela[0];
$mes = $vencimento_primeira_parcela[1];
$ano = $vencimento_primeira_parcela[2];

// Busca Cliente
$buscacli = $connect->query("SELECT * FROM clientes WHERE Id='$cliente'"); 
$buscaclix = $buscacli->fetch(PDO::FETCH_OBJ); 
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
        
        /* Inputs Desabilitados Estilizados */
        .form-control[disabled], .form-control[readonly] {
            background-color: #fff;
            opacity: 1;
            border-radius: 0 8px 8px 0; 
            border: 1px solid #e0e0e0;
            border-left: 0;
            height: 45px;
            color: #495057;
            font-weight: 500;
        }
        
        /* Ícones Coloridos na lateral esquerda */
        .input-group-text { 
            border-radius: 8px 0 0 8px; 
            border: 1px solid #e0e0e0; 
            background-color: #fff; 
            border-right: 0;
            width: 45px;
            justify-content: center;
        }

        /* Cores Específicas dos Ícones (Sem repetir) */
        .icon-green { color: #28a745; background-color: #e8f5e9; }      /* Valor Total */
        .icon-blue { color: #007bff; background-color: #e6f2ff; }       /* Forma Pagamento */
        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }     /* Parcelas */
        .icon-orange { color: #fd7e14; background-color: #fff5eb; }     /* Data Início */
        
        /* Ícones da Lista de Parcelas */
        .icon-teal { color: #20c997; background-color: #e6fffa; }       /* Valor Parcela */
        .icon-red { color: #dc3545; background-color: #fce8e6; }        /* Data Parcela */

        /* Botões */
        .btn-gradient-success { background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; }
        .btn-gradient-success:hover { background: linear-gradient(135deg, #218838, #1e7e34); color: white; transform: translateY(-1px); }
        
        .section-divider {
            border-top: 1px dashed #e0e0e0;
            margin: 20px 0;
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
                    <div class="mr-3" style="background: linear-gradient(135deg, #17a2b8, #138496); padding: 12px; border-radius: 10px;">
                        <i class="fas fa-file-signature fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">RESUMO DA ATUALIZAÇÃO</h4>
                        <p class="text-muted mb-0">Confira os dados antes de salvar</p>
                    </div>
                </div>
                <!-- Botão Voltar (Formulário para manter dados se possível, ou link simples) -->
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
                    <h5 class="text-muted mb-1">Cliente Selecionado</h5>
                    <h3 class="text-dark font-weight-bold"><?= $buscaclix->nome; ?></h3>
                </div>
            </div>

            <hr class="mb-4">

            <!-- Resumo Geral (4 Colunas) -->
            <div class="row">
                <!-- Valor Total -->
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold text-dark small">Valor Total Previsto</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text icon-green"><i class="fas fa-coins"></i></span>
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
                        <input type="text" class="form-control" value="<?php echo ($fpagamento == 30) ? 'Mensal' : 'Outro'; ?>" disabled>
                    </div>
                </div>

                <!-- Qtd Parcelas -->
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold text-dark small">Qtd. Mensalidades</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text icon-purple"><i class="fas fa-layer-group"></i></span>
                        </div>
                        <input type="number" class="form-control" value="<?php print $parcelas; ?>" disabled>
                    </div>
                </div>

                <!-- Primeira Parcela -->
                <div class="col-md-3 mb-3">
                    <label class="font-weight-bold text-dark small">Início (1ª Mensalidade)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text icon-orange"><i class="fas fa-calendar-check"></i></span>
                        </div>
                        <input type="text" class="form-control" value="<?php print $datap; ?>" disabled>
                    </div>
                </div>
            </div>

            <div class="section-divider"></div>

            <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-list-ol mr-2"></i> Detalhamento das Parcelas Geradas</h6>

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
                <div class="card bg-light border-0">
                    <div class="card-body p-2">
                        <div class="row">
                            <div class="col-6">
                                <label class="small text-muted mb-0">Valor Parcela <?php echo $parcela + 1; ?></label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-teal border-0"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="text" class="form-control border-0 bg-white" value="R$ <?php print number_format($valorparcela, 2, ',', '.'); ?>" disabled>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="small text-muted mb-0">Vencimento</label>
                                <div class="input-group input-group-sm">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-red border-0"><i class="far fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="text" class="form-control border-0 bg-white" value="<?php print $qwerr; ?>" disabled>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
              </div>

              <?php } ?>
            </div>

            <hr class="my-4">

            <!-- Botões de Ação -->
            <div class="row">
              <div class="col-md-6 mb-2">
                  <a href="cad_contas" class="btn btn-secondary btn-lg btn-block shadow-sm">
                    <i class="fa fa-arrow-left mr-2"></i> Corrigir Dados
                  </a>
              </div>

              <div class="col-md-6">
                <form action="classes/simulador_exe_edit.php" method="post">
                 <input type="hidden" name="vercli" value="<?php echo $id_cliente; ?>">
                  <input type="hidden" name="formapagamento" value="<?php print $fpagamento; ?>">
                  <input type="hidden" name="parcelas" value="<?php print $parcelas; ?>">
                  <input type="hidden" name="dataparcela" value="<?php print $datap; ?>">
                  <input type="hidden" name="dataparcelax" value="<?php print $_POST["datap"]; ?>">
                  <input type="hidden" name="idpedido" value="<?php print $better_token = md5(uniqid(rand(), true)); ?>">
                  <input type="hidden" name="vparcela" value="<?php print $valorparcela; ?>">

                  <button type="submit" class="btn btn-gradient-success btn-lg btn-block shadow-sm" name="cart">
                      CONFIRMAR ATUALIZAÇÃO <i class="fa fa-check-circle ml-2"></i>
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

<!-- Scripts -->
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script src="../lib/select2/js/select2.full.min.js"></script>
<script src="../js/slim.js"></script>

</body>
</html>