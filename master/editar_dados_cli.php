<?php
require_once "topo.php";

$id_cliente = isset($_POST['vercli']) ? $_POST['vercli'] : '';
if(empty($id_cliente) && isset($_GET['vercli'])) $id_cliente = $_GET['vercli'];

// Busca o nome do cliente
$cliente_info = $connect->query("SELECT nome FROM clientes WHERE Id='$id_cliente'")->fetch(PDO::FETCH_OBJ);
$nome_cliente = $cliente_info ? $cliente_info->nome : '';

// Busca os dados da conta do cliente apenas se o status for 1
$conta_info = $connect->query("SELECT * FROM financeiro1 WHERE idc='$id_cliente' AND status = 1")->fetch(PDO::FETCH_OBJ);

// Define os dados da conta
$dados_conta = new stdClass();
$dados_conta->cliente = $nome_cliente;
$dados_conta->valorfinal = $conta_info ? $conta_info->valorfinal : '';
$dados_conta->parcelas = $conta_info ? $conta_info->parcelas : 1;

// Busca data da primeira parcela
$buscaPrimeiraParcela = $connect->query("SELECT f2.datapagamento FROM financeiro2 f2 INNER JOIN financeiro1 f1 ON f2.chave = f1.chave WHERE f2.idc = '" . $id_cliente . "' ORDER BY f2.datapagamento ASC LIMIT 1");
$primeiraParcela = $buscaPrimeiraParcela->fetch(PDO::FETCH_OBJ);

$primeiraParcelaFormatada = '';
if ($primeiraParcela && isset($primeiraParcela->datapagamento)) {
  $primeiraParcelaDateTime = DateTime::createFromFormat('d/m/Y', $primeiraParcela->datapagamento);
  if ($primeiraParcelaDateTime) {
    $primeiraParcelaFormatada = $primeiraParcelaDateTime->format('Y-m-d');
  }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS Necessários -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../lib/select2/css/select2.min.css">
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        
        /* Inputs */
        .form-control, .custom-select { border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; height: 45px; padding-left: 15px; border-left: 0; }
        .form-control:focus, .custom-select:focus { border-color: #e0e0e0; box-shadow: none; border-bottom: 2px solid #fd7e14; }
        
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
        .icon-blue { color: #007bff; background-color: #e6f2ff; }       /* Cliente */
        .icon-green { color: #28a745; background-color: #e8f5e9; }      /* Valor */
        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }     /* Parcelas */
        .icon-red { color: #dc3545; background-color: #fce8e6; }        /* Data */

        /* Ajuste do Select2 */
        .select2-container { width: 100% !important; flex: 1; }
        .select2-container .select2-selection--single { 
            height: 45px; 
            border: 1px solid #e0e0e0; 
            border-left: none;
            border-radius: 0 8px 8px 0; 
            padding-top: 8px; 
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { top: 10px; right: 10px; }
        .group-select { display: flex; width: 100%; }

        /* Botão Avançar */
        .btn-gradient-primary { 
            background: linear-gradient(135deg, #007bff, #0056b3); 
            color: white; 
            border: none;
            transition: all 0.3s;
        }
        .btn-gradient-primary:hover { 
            background: linear-gradient(135deg, #0056b3, #004085); 
            color: white; 
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
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
                    <!-- Ícone Laranja para Edição -->
                    <div class="mr-3" style="background: linear-gradient(135deg, #fd7e14, #ffc107); padding: 12px; border-radius: 10px;">
                        <i class="fas fa-edit fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">EDITAR DADOS DA COBRANÇA</h4>
                        <p class="text-muted mb-0">Altere os parâmetros do financiamento</p>
                    </div>
                </div>
                <a href="contas_receber" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                    <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body p-4">

            <form action="cad_contas_simulador_edit" method="post" autocomplete="off">
              <input type="hidden" name="vercli" value="<?php echo $id_cliente; ?>">

              <div class="row">
                <!-- Cliente (Azul) -->
                <div class="col-md-12 mb-4">
                  <label class="font-weight-bold text-dark small">Cliente Selecionado</label>
                  <div class="group-select">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-blue"><i class="fas fa-user"></i></span>
                      </div>
                      <select class="form-control select2-show-search" name="cliente" required>
                        <option value="">Pesquisar...</option>
                        <?php
                        $buscacli = $connect->query("SELECT * FROM clientes WHERE idm = '" . $cod_id . "' ORDER BY nome ASC");
                        while ($buscaclix = $buscacli->fetch(PDO::FETCH_OBJ)) {
                          $selected = $buscaclix->Id == $id_cliente ? 'selected' : '';
                          echo "<option value='" . $buscaclix->Id . "' $selected>" . $buscaclix->cpf . " - " . $buscaclix->nome . "</option>";
                        }
                        ?>
                      </select>
                  </div>
                </div>
              </div>

              <div class="row">
                <!-- Valor (Verde) -->
                <div class="col-md-4 mb-4">
                  <label class="font-weight-bold text-dark small">Valor da Mensalidade</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-green"><i class="fas fa-dollar-sign"></i></span>
                      </div>
                      <input type="text" name="valor" class="dinheiro form-control"
                        value="<?php echo number_format($dados_conta->valorfinal, 2, ',', '.'); ?>" required>
                  </div>
                </div>

                <!-- Parcelas (Roxo) -->
                <div class="col-md-4 mb-4">
                  <label class="font-weight-bold text-dark small">Qtd. Mensalidades</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-purple"><i class="fas fa-layer-group"></i></span>
                      </div>
                      <input type="number" name="parcelas" class="form-control"
                        value="<?php echo $dados_conta->parcelas; ?>" required>
                  </div>
                </div>

                <!-- Data (Vermelho) -->
                <div class="col-md-4 mb-4">
                  <label class="font-weight-bold text-dark small">Vencimento 1ª Parcela</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-red"><i class="fas fa-calendar-alt"></i></span>
                      </div>
                      <input type="date" name="datap" class="form-control"
                        value="<?php echo $primeiraParcelaFormatada; ?>" required style="cursor: pointer;">
                  </div>
                </div>
              </div>

              <hr class="my-4" />

              <div class="row">
                <div class="col-md-12 text-center">
                  <button type="submit" class="btn btn-gradient-primary btn-lg px-5 shadow-sm" name="cart">
                      AVANÇAR <i class="fas fa-arrow-right ml-2"></i>
                  </button>
                </div>
              </div>
            </form>

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
<script src="../js/moeda.js"></script>

<script>
    $(function(){
        'use strict';
        
        // Máscara de Dinheiro
        $('.dinheiro').mask('#.##0,00', { reverse: true });

        // Select2
        $('.select2-show-search').select2({
            minimumResultsForSearch: '',
            width: '100%',
            dropdownAutoWidth: true
        });
    });
</script>

<script src="../js/slim.js"></script>
</body>
</html>