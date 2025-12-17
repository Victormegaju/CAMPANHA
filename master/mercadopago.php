<?php  
require_once "topo.php";

$editarcat = $connect->query("SELECT * FROM carteira WHERE Id='$cod_id'");
$dadoscat = $editarcat->fetch(PDO::FETCH_OBJ); 
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
        
        /* Ícones Coloridos nos Inputs */
        .input-group-text { 
            border-radius: 8px 0 0 8px; 
            border: 1px solid #e0e0e0; 
            background-color: #fff; 
            border-right: 0;
            width: 45px;
            justify-content: center;
        }

        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }
        .icon-orange { color: #fd7e14; background-color: #fff5eb; }
        .icon-green { color: #28a745; background-color: #e8f5e9; }
        .icon-cyan { color: #17a2b8; background-color: #e0f7fa; }
        
        /* Inputs e Selects */
        .form-control, .custom-select { 
            border-radius: 0 8px 8px 0; 
            border: 1px solid #e0e0e0; 
            height: 45px; 
            padding-left: 15px; 
            border-left: 0; 
        }
        .form-control:focus, .custom-select:focus { 
            border-color: #e0e0e0; 
            box-shadow: none; 
            border-bottom: 2px solid #009ee3; /* Azul MP */
        }

        /* Botão Salvar */
        .btn-gradient-mp { 
            background: linear-gradient(135deg, #009ee3, #0072bb); 
            color: white; 
            border: none; 
            border-radius: 50px;
            padding: 12px 40px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        .btn-gradient-mp:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 5px 15px rgba(0, 158, 227, 0.4); 
            color: white; 
        }

        /* Header Icon */
        .header-icon-box {
            background: linear-gradient(135deg, #009ee3, #00aae4);
            padding: 12px; border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 158, 227, 0.3);
            margin-right: 15px;
        }

        /* Lista de Passos */
        .step-list { list-style: none; padding: 0; }
        .step-list li { margin-bottom: 8px; font-size: 0.9rem; color: #555; display: flex; align-items: flex-start; }
        .step-num { 
            background: #e9ecef; color: #333; 
            width: 20px; height: 20px; border-radius: 50%; 
            display: flex; align-items: center; justify-content: center; 
            font-size: 0.75rem; font-weight: bold; margin-right: 10px; margin-top: 2px; flex-shrink: 0;
        }
    </style>
</head>
<body>

<div class="slim-mainpanel">
	<div class="container">
        
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="header-icon-box">
                        <i class="fas fa-hand-holding-usd fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">MERCADO PAGO</h4>
                        <p class="text-muted mb-0">Integração de pagamentos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <a href="./" class="btn btn-secondary shadow-sm" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left mr-2"></i> VOLTAR
                </a>
            </div>
        </div>
		
		<?php if(isset($_GET["sucesso"])){ ?>
		<div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle mr-2"></i> <strong>Sucesso!</strong> Configurações salvas.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
		<meta http-equiv="refresh" content="1;URL=./mercadopago" />
		<?php } ?>
		
		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body p-4">
                        
                        <form action="classes/config_exe.php" method="post">
					    <input type="hidden" name="edit_m" value="<?php print isset($edicli) ? $edicli : ''; ?>">
                        
                        <div class="row">
                            <!-- Coluna Esquerda: Instruções -->
                            <div class="col-md-5 mb-4 mb-md-0">
                                <div class="card bg-light border-0 h-100">
                                    <div class="card-body">
                                        <h6 class="font-weight-bold text-dark mb-3">
                                            <i class="fas fa-info-circle text-info mr-2"></i> Como obter o Token?
                                        </h6>
                                        
                                        <ul class="step-list">
                                            <li><span class="step-num">1</span> <div>Acesse o painel de desenvolvedor: <a href="https://www.mercadopago.com.br/developers/panel/app" target="_blank" class="font-weight-bold text-primary">Clicando Aqui</a></div></li>
                                            <li><span class="step-num">2</span> Clique em <b>Criar Aplicação</b></li>
                                            <li><span class="step-num">3</span> Preencha o <b>Nome da Aplicação</b></li>
                                            <li><span class="step-num">4</span> Selecione <b>Pagamento Online</b></li>
                                            <li><span class="step-num">5</span> Integração e-commerce? <b>Não</b></li>
                                            <li><span class="step-num">6</span> Produto? <b>CheckoutTransparente</b></li>
                                            <li><span class="step-num">7</span> Aceite os termos e crie a aplicação</li>
                                            <li><span class="step-num">8</span> No menu lateral, vá em <b>Credenciais de Produção</b></li>
                                            <li><span class="step-num">9</span> Setor: <b>Serviços de TI</b> (Site em branco)</li>
                                            <li><span class="step-num">10</span> Copie o <b>Access Token</b> e cole ao lado.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- Coluna Direita: Formulário -->
                            <div class="col-md-7">
                                <h6 class="font-weight-bold text-dark mb-4">Credenciais de Acesso</h6>

                                <!-- Token MP -->
                                <div class="form-group mb-4">
                                    <label class="font-weight-bold text-dark small">Access Token (Produção)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-purple"><i class="fas fa-key"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="tokenmp" value="<?php print $dadoscat->tokenmp; ?>" required placeholder="APP_USR-...">
                                    </div>
                                </div>

                                <div class="row">
                                    <!-- Chave Copia e Cola -->
                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark small">Enviar Chave Pix (Copia e Cola)?</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon-orange"><i class="far fa-copy"></i></span>
                                            </div>
                                            <select class="custom-select" name="msgqr" required>
                                                <option value="1" <?php if($dadoscat->msgqr == "1") echo 'selected'; ?>>Sim</option>
                                                <option value="2" <?php if($dadoscat->msgqr == "2") echo 'selected'; ?>>Não</option>
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <!-- QRCode -->
                                    <div class="col-md-6 mb-4">
                                        <label class="font-weight-bold text-dark small">Enviar Imagem QRCode?</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon-green"><i class="fas fa-qrcode"></i></span>
                                            </div>
                                            <select class="custom-select" name="msgpix" required>
                                                <option value="1" <?php if($dadoscat->msgpix == "1") echo 'selected'; ?>>Sim</option>
                                                <option value="2" <?php if($dadoscat->msgpix == "2") echo 'selected'; ?>>Não</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="alert alert-warning small mt-2">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> Atenção: Utilize sempre credenciais de <strong>Produção</strong>, não de Teste.
                                </div>

                            </div>
                        </div>
                      
                        <hr class="my-4" />
                        
                        <div class="row">
                            <div class="col-md-12 text-center">
                                <button type="submit" class="btn btn-gradient-mp shadow" name="cart">
                                    <i class="fas fa-save mr-2"></i> SALVAR CONFIGURAÇÕES
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

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script>
	$('.dinheiro').mask('#.##0,00', {reverse: true});
	function upperCaseF(a){
        setTimeout(function(){
            a.value = a.value.toUpperCase();
        }, 1);
    }
</script>
<script src="../js/slim.js"></script>
</body>
</html>
<?php
ob_end_flush();
?>