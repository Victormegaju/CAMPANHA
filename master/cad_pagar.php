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
    <link rel="stylesheet" href="../lib/select2/css/select2.min.css">
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        
        /* Inputs e Selects */
        .form-control, .custom-select { border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; height: 45px; padding-left: 15px; border-left: 0; }
        .form-control:focus, .custom-select:focus { border-color: #e0e0e0; box-shadow: none; border-bottom: 2px solid #dc3545; }
        
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
        .icon-blue-dark { color: #0056b3; background-color: #e6f0ff; } /* Descrição */
        .icon-green { color: #28a745; background-color: #e8f5e9; }    /* Valor */
        .icon-cyan { color: #17a2b8; background-color: #e0f7fa; }      /* Frequência */
        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }    /* Parcelas */
        .icon-orange { color: #fd7e14; background-color: #fff5eb; }    /* Data */

        /* Ajuste do Select2 */
        .select2-container { width: 100% !important; flex: 1; display: block; }
        .select2-container .select2-selection--single { 
            height: 45px; 
            border: 1px solid #e0e0e0; 
            border-left: none;
            border-radius: 0 8px 8px 0; 
            padding-top: 8px; 
            outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { top: 10px; right: 10px; }
        
        .group-select { display: flex; width: 100%; position: relative; }

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
                        <div class="mr-3" style="background: linear-gradient(135deg, #dc3545, #c82333); padding: 12px; border-radius: 10px;">
                            <i class="fas fa-file-invoice-dollar fa-lg text-white"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark font-weight-bold">CONTAS A PAGAR</h4>
                            <p class="text-muted mb-0">Cadastre uma nova despesa</p>
                        </div>
                    </div>
                    <a href="contas_pagar" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                        <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                    </a>
                </div>
            </div>
        </div>

		<div class="row justify-content-center">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body p-4">
					
					<form action="cad_pagar_simulador" method="post" autocomplete="off">
                        
                        <!-- LINHA 1: Descrição -->
                        <div class="row">
                            <div class="col-md-12 mb-4">
                                <label class="font-weight-bold text-dark small">Descrição da Despesa *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-blue-dark"><i class="fas fa-tag"></i></span>
                                    </div>
                                    <input type="text" name="descricao" class="form-control" placeholder="Ex: Conta de Luz, Aluguel, Fornecedor X" required>
                                </div>
                            </div>
                        </div>	
                        
                        <!-- LINHA 2: Detalhes -->
                        <div class="row">
                            <!-- Valor -->
                            <div class="col-md-3 mb-4">
                                <label class="font-weight-bold text-dark small">Valor da Parcela *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-green"><i class="fas fa-dollar-sign"></i></span>
                                    </div>
                                    <input type="text" name="valor" class="dinheiro form-control" placeholder="0,00" required>
                                </div>
                            </div>
                            
                            <!-- Frequência -->
                            <div class="col-md-3 mb-4">
                                <label class="font-weight-bold text-dark small">Frequência *</label>
                                <div class="group-select">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-cyan" style="border-right: 0;"><i class="fas fa-clock"></i></span>
                                    </div>
                                    <select class="form-control select2-show-search" name="fpagamento" required>
                                        <option value="">Selecione...</option>
                                        <option value="30">Mensal</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Qtd Parcelas -->
                            <div class="col-md-3 mb-4">
                                <label class="font-weight-bold text-dark small">Qtd. Parcelas *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-purple"><i class="fas fa-sort-numeric-up"></i></span>
                                    </div>
                                    <input type="number" name="parcelas" class="form-control" value="1" min="1" required>
                                </div>
                            </div>
                            
                            <!-- Data -->
                            <div class="col-md-3 mb-4">
                                <label class="font-weight-bold text-dark small">1º Pagamento *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-orange"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" name="datap" class="form-control" required style="cursor: pointer;">
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
<script src="../lib/select2/js/select2.full.min.js"></script>
<!-- CDN Confiável para Máscara de Dinheiro -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
    $(document).ready(function(){
        // Máscara de Dinheiro
        $('.dinheiro').mask('#.##0,00', {reverse: true});

        // Configuração do Select2
        $('.select2-show-search').select2({
            minimumResultsForSearch: Infinity, // Desabilita busca pois só tem poucas opções
            width: '100%',
            dropdownAutoWidth: false
        });
    });
</script>
    
</body>
</html>