<?php  
require_once "topo.php";

$edicli = $_POST['edicli'];

// Proteção básica se acessar sem ID
if(!isset($edicli)){ header("location: clientes"); exit; }

$editarcat = $connect->query("SELECT * FROM clientes WHERE Id='$edicli' AND idm = '".$cod_id."'");
$dadoscat = $editarcat->fetch(PDO::FETCH_OBJ); 
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
        .icon-blue { color: #4e73df; background-color: #f8f9fc; }      /* Nome */
        .icon-purple { color: #6610f2; background-color: #f3e9fe; }    /* Categoria */
        .icon-green { color: #25d366; background-color: #e8fcef; }     /* Celular */
        .icon-red { color: #e74a3b; background-color: #fce8e6; }       /* Email */

        /* Botão Atualizar (Laranja para indicar edição) */
        .btn-gradient-update { 
            background: linear-gradient(135deg, #fd7e14, #f36100); 
            color: white; 
            border: none;
            transition: all 0.3s;
        }
        .btn-gradient-update:hover { 
            background: linear-gradient(135deg, #e35d00, #c74f00); 
            color: white; 
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(253, 126, 20, 0.3);
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
                            <i class="fas fa-user-edit fa-lg text-white"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark font-weight-bold">EDITAR CLIENTE</h4>
                            <p class="text-muted mb-0">Atualize os dados abaixo</p>
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
					
                        <form action="classes/clientes_exe.php" method="post" autocomplete="off">
                            <input type="hidden" name="edit_cli" value="<?php print $edicli; ?>">
                            
                            <!-- LINHA 1: Nome -->
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="font-weight-bold text-dark small">Nome Completo *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-blue"><i class="fas fa-user"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="nome" maxlength="160" 
                                               value="<?php print $dadoscat->nome; ?>"
                                               onkeydown="upperCaseF(this)" placeholder="Nome do cliente" required>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- LINHA 2: Categoria, Celular, Email -->
                            <div class="row">
                                <!-- Categoria -->
                                <div class="col-md-4 mb-4">
                                    <label class="font-weight-bold text-dark small">Grupo/Categoria *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-purple"><i class="fas fa-tags"></i></span>
                                        </div>
                                        <select class="form-control" name="cliente" required style="cursor: pointer;">
                                            <option value="">Selecione...</option>								
                                            <?php
                                            $buscacli  = $connect->query("SELECT * FROM categoria WHERE idu = '".$cod_id."' ORDER BY nome ASC");
                                            while ($buscaclix = $buscacli->fetch(PDO::FETCH_OBJ)) { 
                                                // Verifica se é a categoria atual do cliente para marcar selected
                                                $selected = ($buscaclix->id == $dadoscat->idc) ? 'selected' : '';
                                            ?>
                                            <option value="<?=$buscaclix->id;?>" <?=$selected;?>><?php echo $buscaclix->nome;?></option> 
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>

                                <!-- Celular -->
                                <div class="col-md-4 mb-4">
                                    <label class="font-weight-bold text-dark small">Celular/WhatsApp *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-green"><i class="fab fa-whatsapp"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="celular" id="cel" 
                                               value="<?php print $dadoscat->celular; ?>"
                                               placeholder="(00) 00000-0000" required>
                                    </div>
                                </div>

                                <!-- Email -->
                                <div class="col-md-4 mb-4">
                                    <label class="font-weight-bold text-dark small">Email <span class="text-muted font-weight-normal">(Opcional)</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text icon-red"><i class="fas fa-envelope"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="email" maxlength="60" 
                                               value="<?php print $dadoscat->email; ?>"
                                               placeholder="email@exemplo.com">
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4" />
                            
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-gradient-update btn-lg px-5 shadow-sm" name="cart">
                                        <i class="fas fa-sync-alt mr-2"></i>ATUALIZAR DADOS
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

<script>
    $(function(){
        'use strict';
        // Máscara Celular
        $('#cel').mask('(99) 99999-9999');
    });

	function upperCaseF(a){
        setTimeout(function(){
            a.value = a.value.toUpperCase();
        }, 1);
    }
</script>

<script src="../js/slim.js"></script>	
</body>
</html>