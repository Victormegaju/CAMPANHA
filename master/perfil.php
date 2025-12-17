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

        .icon-blue { color: #007bff; background-color: #e6f2ff; }
        .icon-green { color: #28a745; background-color: #e8f5e9; }
        .icon-orange { color: #fd7e14; background-color: #fff5eb; }
        .icon-red { color: #dc3545; background-color: #fce8e6; }
        
        /* Inputs */
        .form-control { 
            border-radius: 0 8px 8px 0; 
            border: 1px solid #e0e0e0; 
            height: 45px; 
            padding-left: 15px; 
            border-left: 0; 
        }
        .form-control:focus { 
            border-color: #e0e0e0; 
            box-shadow: none; 
            border-bottom: 2px solid #6f42c1; 
        }

        /* Botão Salvar */
        .btn-gradient-primary { 
            background: linear-gradient(135deg, #6f42c1, #593196); 
            color: white; 
            border: none; 
            border-radius: 50px;
            padding: 10px 30px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s;
        }
        .btn-gradient-primary:hover { 
            transform: translateY(-2px); 
            box-shadow: 0 4px 10px rgba(111, 66, 193, 0.3); 
            color: white; 
        }

        /* Header Icon */
        .header-icon-box {
            background: linear-gradient(135deg, #6f42c1, #8e44ad);
            padding: 12px; border-radius: 10px;
            box-shadow: 0 4px 10px rgba(111, 66, 193, 0.3);
            margin-right: 15px;
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
                        <i class="fas fa-user-cog fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">MEU PERFIL</h4>
                        <p class="text-muted mb-0">Gerencie suas informações de acesso</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 text-right">
                <a href="funcionarios.php" class="btn btn-secondary shadow-sm" style="border-radius: 8px;">
                    <i class="fas fa-arrow-left mr-2"></i> VOLTAR
                </a>
            </div>
        </div>

		<?php if(isset($_GET["sucesso"])){ ?>
		<div class="alert alert-success alert-dismissible fade show mb-4 shadow-sm" role="alert" style="border-radius: 10px;">
            <i class="fas fa-check-circle mr-2"></i> <strong>Sucesso!</strong> Perfil atualizado.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
		<meta http-equiv="refresh" content="1;URL=./perfil" />
		<?php } ?>

		<div class="row justify-content-center">
			<div class="col-md-12">
				<div class="card">
					<div class="card-body p-4">

                        <form action="classes/config_exe.php" method="post" autocomplete="off">
                            <input type="hidden" name="edit_cli" value="<?php print isset($edicli) ? $edicli : ''; ?>">
                            
                            <h6 class="font-weight-bold text-dark mb-4 pl-2" style="border-left: 4px solid #6f42c1;">
                                DADOS PESSOAIS
                            </h6>

                            <div class="row">
                                <!-- Nome -->
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark small">Nome Completo</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-blue"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="nome" value="<?php print $dadoscat->nome; ?>" maxlength="160" onkeydown="upperCaseF(this)" required placeholder="Seu nome completo">
                                    </div>
                                </div>
                                
                                <!-- WhatsApp -->
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark small">Celular / WhatsApp</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-green"><i class="fab fa-whatsapp"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="celular" value="<?php print $dadoscat->celular; ?>" maxlength="11" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required placeholder="Apenas números">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            
                            <h6 class="font-weight-bold text-dark mb-4 pl-2" style="border-left: 4px solid #fd7e14;">
                                DADOS DE ACESSO
                            </h6>

                            <div class="row">
                                <!-- Login -->
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark small">Login (CPF/CNPJ)</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-orange"><i class="fas fa-sign-in-alt"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="email" maxlength="14" value="<?php print $dadoscat->login; ?>" onkeypress="return event.charCode >= 48 && event.charCode <= 57" required placeholder="Seu login de acesso">
                                    </div>
                                </div>
                                
                                <!-- Senha -->
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark small">Alterar Senha</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-red"><i class="fas fa-lock"></i></span>
                                        </div>
                                        <input type="password" class="form-control" name="senha" maxlength="20" placeholder="Deixe em branco para manter a atual">
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-4">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-gradient-primary shadow" name="cart">
                                        <i class="fas fa-save mr-2"></i> SALVAR ALTERAÇÕES
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