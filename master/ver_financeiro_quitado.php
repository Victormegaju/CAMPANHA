<?php
require_once "topo.php";

// ID CLIENTE
if(isset($_GET["vercli"]))  {
    $cliente = $_GET['vercli'];
}
if(isset($_POST["vercli"]))  {
    $cliente = $_POST['vercli'];
}

// Busca dados principais
$buscafin  = $connect->query("SELECT * FROM financeiro1 WHERE Id='$cliente' AND idm ='".$cod_id."'");
$buscafinx = $buscafin->fetch(PDO::FETCH_OBJ);

// Busca dados do cliente
$buscacli  = $connect->query("SELECT * FROM clientes WHERE Id='".$buscafinx->idc."' AND idm ='".$cod_id."'");
$buscaclix = $buscacli->fetch(PDO::FETCH_OBJ);                              
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
        .icon-box { width: 45px; height: 45px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; color: white; margin-right: 15px; box-shadow: 0 3px 6px rgba(0,0,0,0.1); }
        .bg-teal { background: linear-gradient(135deg, #20c997, #1aa179); }
        .bg-green { background: linear-gradient(135deg, #28a745, #218838); }
        .bg-blue { background: linear-gradient(135deg, #007bff, #0056b3); }
        .bg-purple { background: linear-gradient(135deg, #6f42c1, #593196); }
        .bg-orange { background: linear-gradient(135deg, #fd7e14, #e67e22); }
        .bg-red { background: linear-gradient(135deg, #dc3545, #c0392b); }

        /* Status Badges */
        .badge-status { padding: 8px 15px; border-radius: 30px; font-weight: 600; font-size: 0.85rem; letter-spacing: 0.5px; }
        .badge-success-soft { background-color: #d4edda; color: #155724; }
        .badge-danger-soft { background-color: #f8d7da; color: #721c24; }

        /* Card de Parcela */
        .parcela-card { background: #fff; border: 1px solid #e9ecef; border-radius: 10px; padding: 15px; margin-bottom: 15px; border-left: 4px solid #adb5bd; transition: transform 0.2s; }
        .parcela-card:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .parcela-paga { border-left-color: #28a745; }
        .parcela-atrasada { border-left-color: #dc3545; }
    </style>
</head>
<body>

<div class="slim-mainpanel">
    <div class="container">
        
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center">
                    <div class="icon-box bg-teal">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">DETALHES DO PAGAMENTO</h4>
                        <p class="text-muted mb-0">Cliente: <strong><?=$buscaclix->nome;?></strong></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-md-right mt-3 mt-md-0">
                <a href="finalizados" class="btn btn-purple btn-sm shadow-sm" style="border-radius: 50px; padding: 8px 20px;">
                    <i class="fas fa-arrow-left mr-2"></i> VOLTAR
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card card-info mb-4">
                    <div class="card-body p-4">
                        
                        <!-- Status Geral -->
                        <div class="text-center mb-4">
                            <?php if($buscafinx->status == 1){ ?>
                                <span class="badge-status badge-danger-soft"><i class="fas fa-clock mr-1"></i> PENDENTE</span>
                            <?php } ?>
                            <?php if($buscafinx->status == 2){ ?>
                                <span class="badge-status badge-success-soft"><i class="fas fa-check-circle mr-1"></i> QUITADO EM <?php print $buscafinx->pagoem; ?></span>
                            <?php } ?>
                        </div>
                        
                        <hr class="my-4">

                        <!-- Resumo Financeiro (4 Colunas) -->
                        <div class="row">
                            <!-- Valor Total -->
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="icon-box bg-green mb-0" style="width: 40px; height: 40px; font-size: 1rem; margin-right: 10px;">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block font-weight-bold text-uppercase">Valor Total</small>
                                        <h5 class="mb-0 text-dark font-weight-bold">R$ <?php print number_format($buscafinx->valorfinal * $buscafinx->parcelas, 2, ',', '.');?></h5>
                                    </div>
                                </div>
                            </div>

                            <!-- Forma Pagamento -->
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="icon-box bg-blue mb-0" style="width: 40px; height: 40px; font-size: 1rem; margin-right: 10px;">
                                        <i class="fas fa-credit-card"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block font-weight-bold text-uppercase">Frequência</small>
                                        <h5 class="mb-0 text-dark font-weight-bold">
                                            <?php 
                                            if($buscafinx->formapagamento == 1) echo "Diário";
                                            elseif($buscafinx->formapagamento == 7) echo "Semanal";
                                            elseif($buscafinx->formapagamento == 15) echo "Quinzenal";
                                            elseif($buscafinx->formapagamento == 30) echo "Mensal";
                                            else echo "Outros";
                                            ?>
                                        </h5>
                                    </div>
                                </div>
                            </div>

                            <!-- Parcelas -->
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="icon-box bg-purple mb-0" style="width: 40px; height: 40px; font-size: 1rem; margin-right: 10px;">
                                        <i class="fas fa-layer-group"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block font-weight-bold text-uppercase">Qtd. Parcelas</small>
                                        <h5 class="mb-0 text-dark font-weight-bold"><?php print $buscafinx->parcelas;?>x</h5>
                                    </div>
                                </div>
                            </div>

                            <!-- Primeira Parcela -->
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center p-3 bg-light rounded">
                                    <div class="icon-box bg-orange mb-0" style="width: 40px; height: 40px; font-size: 1rem; margin-right: 10px;">
                                        <i class="fas fa-calendar-alt"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block font-weight-bold text-uppercase">1ª Parcela</small>
                                        <h5 class="mb-0 text-dark font-weight-bold"><?php print date_format(new DateTime($buscafinx->primeiraparcela),'d/m/Y');?></h5>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4" />
                        
                        <h6 class="font-weight-bold text-dark mb-3"><i class="fas fa-list-ul mr-2 text-muted"></i> Histórico de Parcelas</h6>

                        <!-- Lista de Parcelas -->
                        <div class="row">
                            <?php
                            $buscafin2  = $connect->query("SELECT * FROM financeiro2 WHERE chave='".$buscafinx->chave."' AND idm ='".$cod_id."' ORDER BY Id ASC");
                            while($buscafinx2 = $buscafin2->fetch(PDO::FETCH_OBJ)){
                            
                                $data1 = date("d/m/Y");
                                $data2 = $buscafinx2->datapagamento;
                                $data1 = implode('-', array_reverse(explode('/', $data1)));
                                $data2 = implode('-', array_reverse(explode('/', $data2)));
                                $d1 = strtotime($data1); 
                                $d2 = strtotime($data2);
                                $prazo = ($d2 - $d1) / 86400;
                                $diasprazox = str_replace("-", "", $prazo);
                                
                                // Calculo simulado de juros (apenas para exibição se necessário)
                                $vjuros = isset($dadosgerais->vjurus) ? $dadosgerais->vjurus : 0; 
                                $diasprazo = $diasprazox * $vjuros;
                                $atualizar = $buscafinx2->parcela + $diasprazo;
                                
                                // Define classe visual
                                $classe_card = "parcela-paga"; // Default verde pois é página de quitados
                                if($prazo < 0 && $buscafinx2->status == 1) $classe_card = "parcela-atrasada";
                            ?>
                            
                            <div class="col-md-6">
                                <div class="parcela-card <?php echo $classe_card; ?>">
                                    <div class="row align-items-center">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Valor Original</small>
                                            <span class="h5 font-weight-bold text-dark">R$ <?php print number_format($buscafinx2->parcela, 2, ',', '.');?></span>
                                            
                                            <?php if($prazo < 0 && $buscafinx2->status == 1){ ?>
                                                <div class="mt-2">
                                                    <small class="text-danger font-weight-bold">Valor Atualizado (Juros)</small><br>
                                                    <span class="badge badge-danger">R$ <?php print number_format($atualizar, 2, ',', '.');?></span>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        
                                        <div class="col-6 text-right border-left">
                                            <div class="mb-2">
                                                <small class="text-muted d-block">Vencimento</small>
                                                <strong><?php print $buscafinx2->datapagamento;?></strong>
                                            </div>
                                            <div>
                                                <small class="text-muted d-block">Pago Em</small>
                                                <strong class="text-success"><i class="fas fa-check-double"></i> <?php print $buscafinx2->pagoem;?></strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <?php } ?>
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
<script src="../js/slim.js"></script>    
</body>
</html>