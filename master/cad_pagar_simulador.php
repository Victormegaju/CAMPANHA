<?php
require_once "topo.php";

// Recebe os dados do formulário anterior
$descricao      = $_POST['descricao'];
$formapagamento = $_POST['formapagamento'];
$parcelas       = $_POST['parcelas'];
$datap          = $_POST['datap'];
$valor          = $_POST['valor'];

// Converte valor para formato banco (1.000,00 -> 1000.00)
$valor_formatado = str_replace(".", "", $valor);
$valor_formatado = str_replace(",", ".", $valor_formatado);

// Calcula totais
$total_final = $valor_formatado * $parcelas;
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
        
        /* Ícones Coloridos */
        .icon-box { width: 50px; height: 50px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; margin: 0 auto 10px; }
        .bg-green { background-color: #e8f5e9; color: #28a745; }
        .bg-blue { background-color: #e6f2ff; color: #007bff; }
        .bg-purple { background-color: #f3e9fe; color: #6f42c1; }

        /* Tabela */
        .table th { border-top: none; font-weight: 600; color: #343a40; text-transform: uppercase; font-size: 0.85rem; }
        .table td { vertical-align: middle; font-weight: 500; }
        
        /* Botão Confirmar */
        .btn-confirm { 
            background: linear-gradient(135deg, #28a745, #218838); 
            color: white; border: none; padding: 12px 30px; border-radius: 50px; 
            font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; 
            transition: all 0.3s; width: 100%;
        }
        .btn-confirm:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3); color: white; }

        .btn-back { border-radius: 50px; padding: 10px 20px; }
    </style>
</head>
<body>

<div class="slim-mainpanel">
    <div class="container-fluid">
        
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="mr-3" style="background: linear-gradient(135deg, #6f42c1, #593196); padding: 12px; border-radius: 10px;">
                        <i class="fas fa-calculator fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">CONFIRMAÇÃO DE LANÇAMENTO</h4>
                        <p class="text-muted mb-0">Confira as parcelas antes de finalizar</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-md-right mt-3 mt-md-0">
                <a href="cad_pagar" class="btn btn-secondary btn-back shadow-sm">
                    <i class="fas fa-arrow-left mr-2"></i> VOLTAR E EDITAR
                </a>
            </div>
        </div>

        <div class="row">
            
            <!-- Coluna da Esquerda: Resumo -->
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <div class="card-body p-4 text-center">
                        <h6 class="text-uppercase text-muted font-weight-bold mb-4">Resumo da Despesa</h6>
                        
                        <div class="mb-4">
                            <div class="icon-box bg-blue"><i class="fas fa-tag"></i></div>
                            <h5 class="text-dark font-weight-bold mb-0"><?php echo $descricao; ?></h5>
                            <small class="text-muted">Descrição</small>
                        </div>

                        <div class="row">
                            <div class="col-6 mb-3">
                                <div class="icon-box bg-green" style="width: 40px; height: 40px; font-size: 1.2rem;"><i class="fas fa-dollar-sign"></i></div>
                                <h6 class="font-weight-bold text-success mb-0">R$ <?php echo $valor; ?></h6>
                                <small class="text-muted">Por Parcela</small>
                            </div>
                            <div class="col-6 mb-3">
                                <div class="icon-box bg-purple" style="width: 40px; height: 40px; font-size: 1.2rem;"><i class="fas fa-layer-group"></i></div>
                                <h6 class="font-weight-bold text-dark mb-0"><?php echo $parcelas; ?>x</h6>
                                <small class="text-muted">Parcelas</small>
                            </div>
                        </div>

                        <hr>
                        
                        <h3 class="font-weight-bold text-primary">R$ <?php echo number_format($total_final, 2, ',', '.'); ?></h3>
                        <p class="text-muted small">VALOR TOTAL PREVISTO</p>

                        <!-- FORMULÁRIO DE ENVIO FINAL -->
                        <form action="classes/contas_pagar_exe.php" method="post" class="mt-4">
                            <input type="hidden" name="cadpagar" value="ok">
                            <input type="hidden" name="descricao" value="<?php echo $descricao; ?>">
                            <input type="hidden" name="formapagamento" value="<?php echo $formapagamento; ?>">
                            <input type="hidden" name="parcelas" value="<?php echo $parcelas; ?>">
                            <input type="hidden" name="dataparcela" value="<?php echo date('d/m/Y', strtotime($datap)); ?>">
                            <input type="hidden" name="vparcela" value="<?php echo $valor_formatado; ?>"> <!-- Valor Formatado 1000.00 -->

                            <button type="submit" class="btn btn-confirm shadow">
                                <i class="fas fa-check-circle mr-2"></i> CONFIRMAR LANÇAMENTO
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Coluna da Direita: Lista de Parcelas -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-dark mb-4"><i class="fas fa-list-ol mr-2 text-primary"></i> Previsão de Vencimentos</h6>
                        
                        <div class="table-responsive">
                            <table class="table table-hover w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="text-center">Parcela</th>
                                        <th>Vencimento Previsto</th>
                                        <th>Valor</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Lógica de simulação de datas
                                    $data_atual = $datap; // Data vinda do form (Y-m-d)
                                    $dia = date('d', strtotime($data_atual));
                                    $mes = date('m', strtotime($data_atual));
                                    $ano = date('Y', strtotime($data_atual));

                                    for($i = 0; $i < $parcelas; $i++) {
                                        // Calcula a data somando dias baseado na frequência (ex: 30 dias)
                                        $dias_a_somar = $i * $formapagamento;
                                        $vencimento = date('d/m/Y', strtotime("+$dias_a_somar days", mktime(0, 0, 0, $mes, $dia, $ano)));
                                    ?>
                                    <tr>
                                        <td class="text-center"><strong><?php echo $i + 1; ?>/<?php echo $parcelas; ?></strong></td>
                                        <td><i class="far fa-calendar-alt text-muted mr-2"></i> <?php echo $vencimento; ?></td>
                                        <td class="text-dark font-weight-bold">R$ <?php echo $valor; ?></td>
                                        <td class="text-center"><span class="badge badge-warning px-3 py-1" style="border-radius: 20px;">A LANÇAR</span></td>
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

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../js/slim.js"></script>
</body>
</html>