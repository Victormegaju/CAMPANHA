<?php  
require_once "topo.php";

$edicli = $_POST['vercli'];

// Proteção caso acesse direto sem ID
if(!isset($edicli)){ header("location: clientes"); exit; }

$editarcat = $connect->query("SELECT * FROM clientes WHERE Id='$edicli' AND idm = '".$cod_id."'");
$dadoscat = $editarcat->fetch(PDO::FETCH_OBJ); 

// Limpa o celular para o link do WhatsApp
$whatsapp_num = preg_replace("/[^0-9]/", "", $dadoscat->celular);
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
        .icon-blue { color: #007bff; background-color: #e6f2ff; }      /* Nome */
        .icon-green { color: #28a745; background-color: #e8f5e9; }     /* Celular */
        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }    /* Email */

        /* Botão Ação */
        .btn-whatsapp { 
            background-color: #25d366; color: white; border: none; 
            transition: all 0.3s;
        }
        .btn-whatsapp:hover { background-color: #1ebc57; color: white; transform: translateY(-2px); }
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
                        <div class="mr-3" style="background: linear-gradient(135deg, #17a2b8, #117a8b); padding: 12px; border-radius: 10px;">
                            <i class="fas fa-id-card fa-lg text-white"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark font-weight-bold">DADOS DO CLIENTE</h4>
                            <p class="text-muted mb-0">Visualizando informações de cadastro</p>
                        </div>
                    </div>
                    <a href="clientes" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                        <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                    </a>
                </div>
            </div>
        </div>

		<div class="row justify-content-center">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body p-4">
					
                        <!-- Perfil / Nome -->
                        <div class="row mb-4">
                            <div class="col-md-12 text-center">
                                <div style="width: 80px; height: 80px; background-color: #e6f2ff; color: #007bff; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 15px;">
                                    <i class="fas fa-user"></i>
                                </div>
                                <h3 class="text-dark font-weight-bold mb-1"><?php print $dadoscat->nome; ?></h3>
                                <span class="badge badge-light border">Cliente Cadastrado</span>
                            </div>
                        </div>

                        <hr class="mb-4">

                        <div class="row justify-content-center">
                            
                            <!-- Celular -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark small">Celular/WhatsApp</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-green"><i class="fab fa-whatsapp"></i></span>
                                    </div>
                                    <input type="text" class="form-control" disabled value="<?php print $dadoscat->celular; ?>">
                                    <div class="input-group-append">
                                        <a href="https://api.whatsapp.com/send?phone=55<?php echo $whatsapp_num; ?>" target="_blank" class="btn btn-whatsapp d-flex align-items-center">
                                            <i class="fas fa-comment-dots"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Email -->
                            <div class="col-md-6 mb-4">
                                <label class="font-weight-bold text-dark small">Email de Contato</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text icon-purple"><i class="fas fa-envelope"></i></span>
                                    </div>
                                    <input type="text" class="form-control" disabled value="<?php print $dadoscat->email; ?>">
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="row mt-3">
                            <div class="col-md-12 text-center">
                                <form action="edit_cliente" method="post" class="d-inline">
                                    <input type="hidden" name="edicli" value="<?php print $dadoscat->Id;?>"/>
                                    <button type="submit" class="btn btn-warning btn-lg px-5 shadow-sm text-white" style="border-radius: 8px;">
                                        <i class="fas fa-pencil-alt mr-2"></i> EDITAR DADOS
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
<script src="../js/slim.js"></script>	
</body>
</html>