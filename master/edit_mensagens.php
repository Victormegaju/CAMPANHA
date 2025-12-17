<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once "topo.php";

$edicli = $_POST['edicli'];

$editarcat = $connect->query("SELECT * FROM mensagens WHERE id = '$edicli' AND idu = '" . $cod_id . "'");
$dadoscat = $editarcat->fetch(PDO::FETCH_OBJ);

// Define o título baseado no tipo
switch ($dadoscat->tipo) {
    case '1': $tipomsg = "Cobrança 5 dias"; break;
    case '2': $tipomsg = "Cobrança 3 dias"; break;
    case '3': $tipomsg = "Cobrança no dia"; break;
    case '4': $tipomsg = "Mensalidade Vencida"; break;
    case '5': $tipomsg = "Agradecimento"; break;
    case '6': $tipomsg = "Cobrança Manual"; break;
    case '7': $tipomsg = "Cobrança 7 dias"; break;
    default:  $tipomsg = "Notificação"; break;
}
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
        
        /* Inputs */
        .form-control { border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; height: 45px; padding-left: 15px; border-left: 0; }
        .form-control:focus { border-color: #e0e0e0; box-shadow: none; border-bottom: 2px solid #6f42c1; }
        textarea.form-control { height: auto; border-radius: 0 0 8px 8px; border-top: 0; }
        
        /* Ícones Coloridos nos Inputs */
        .input-group-text { border-radius: 8px 0 0 8px; border: 1px solid #e0e0e0; border-right: 0; width: 45px; justify-content: center; background: #fff; }
        .input-group-header { border-radius: 8px 8px 0 0; border: 1px solid #e0e0e0; border-bottom: 0; background: #f8f9fa; padding: 10px 15px; font-weight: 600; color: #555; display: flex; align-items: center; }
        
        /* Cores */
        .icon-orange { color: #fd7e14; background-color: #fff5eb; }
        .icon-blue { color: #007bff; background-color: #e6f2ff; }
        .icon-teal { color: #20c997; background-color: #e6fffa; }
        
        /* Tags de Variáveis */
        .var-tag { 
            background-color: #e9ecef; color: #333; 
            padding: 2px 8px; border-radius: 4px; 
            font-family: monospace; font-weight: bold; font-size: 0.9rem;
            border: 1px solid #ced4da;
        }
        
        /* Botão */
        .btn-gradient-primary { 
            background: linear-gradient(135deg, #6f42c1, #593196); color: white; border: none; transition: all 0.3s;
        }
        .btn-gradient-primary:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(111, 66, 193, 0.3); color: white; }

        .help-list li { margin-bottom: 8px; font-size: 0.9rem; color: #666; }
    </style>
</head>
<body>

<div class="slim-mainpanel">
	<div class="container">
        
        <!-- Header -->
        <div class="row mb-4 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center">
                    <div class="mr-3" style="background: linear-gradient(135deg, #6f42c1, #4e73df); padding: 12px; border-radius: 10px;">
                        <i class="fas fa-edit fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">EDITAR MENSAGEM</h4>
                        <p class="text-muted mb-0"><?php print $tipomsg; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <a href="mensagens" class="btn btn-secondary shadow-sm" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left mr-2"></i> VOLTAR
                </a>
            </div>
        </div>

		<div class="row">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body p-4">

						<form action="classes/mensagens_exe.php" method="post">
							<input type="hidden" name="edit_cli" value="<?php print $edicli; ?>">

							<div class="row">
                                <!-- Coluna Esquerda: Formulário -->
								<div class="col-md-8">
                                    
                                    <!-- Hora -->
									<div class="form-group mb-4">
										<label class="font-weight-bold text-dark small">Hora de Execução (Envio)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text icon-orange"><i class="far fa-clock"></i></span>
                                            </div>
										    <input id="hora" type="time" name="hora" class="form-control" value="<?php print date("H:i", strtotime($dadoscat->hora)); ?>">
                                        </div>
									</div>

                                    <!-- Mensagem -->
									<div class="form-group mb-4">
										<label class="font-weight-bold text-dark small">Conteúdo da Mensagem</label>
                                        <div class="input-group-header">
                                            <i class="fas fa-comment-dots text-primary mr-2"></i> Texto do WhatsApp
                                        </div>
										<textarea name="msg" cols="30" rows="10" class="form-control" style="resize: vertical;"><?php print $dadoscat->msg; ?></textarea>
									</div>
								</div>

                                <!-- Coluna Direita: Variáveis -->
								<div class="col-md-4">
                                    <div class="card bg-light border-0">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold text-dark mb-3">
                                                <i class="fas fa-code text-teal mr-2"></i> Variáveis Disponíveis
                                            </h6>
                                            <p class="small text-muted mb-3">Copie os códigos abaixo para personalizar a mensagem:</p>
                                            
                                            <ul class="list-unstyled help-list pl-0">
                                                <li><span class="var-tag">#NOME#</span> <span class="float-right">Nome do Cliente</span></li>
                                                <li><span class="var-tag">#EMPRESA#</span> <span class="float-right">Nome da Empresa</span></li>
                                                <li><span class="var-tag">#VALOR#</span> <span class="float-right">Valor da Parcela</span></li>
                                                <li><span class="var-tag">#VENCIMENTO#</span> <span class="float-right">Data Vencimento</span></li>
                                                <li><span class="var-tag">#DATAPAGAMENTO#</span> <span class="float-right">Data Pagamento</span></li>
                                                <li><span class="var-tag">#LINK#</span> <span class="float-right">Link Pagamento</span></li>
                                                <li><span class="var-tag">#CNPJ#</span> <span class="float-right">CNPJ Empresa</span></li>
                                                <li><span class="var-tag">#CONTATO#</span> <span class="float-right">WhatsApp Suporte</span></li>
                                            </ul>
                                            
                                            <div class="alert alert-info small mt-3 mb-0 p-2">
                                                <i class="fas fa-info-circle mr-1"></i> O link deve conter <strong>http://</strong>
                                            </div>
                                        </div>
                                    </div>
								</div>
							</div>

							<hr class="my-4">

							<div class="row">
								<div class="col-md-12 text-center">
									<button type="submit" class="btn btn-gradient-primary btn-lg px-5 shadow-sm" name="cart">
                                        <i class="fas fa-save mr-2"></i> ATUALIZAR MENSAGEM
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
	$(function() {
		'use strict';
		$("#hora").mask("99:99");
	});
</script>
<script src="../js/slim.js"></script>
</body>
</html>