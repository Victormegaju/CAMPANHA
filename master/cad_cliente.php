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
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        
        /* Inputs */
        .form-control, .custom-select { border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; height: 45px; padding-left: 15px; border-left: 0; }
        .form-control:focus, .custom-select:focus { border-color: #e0e0e0; box-shadow: none; border-bottom: 2px solid #4e73df; }
        
        /* Ícones Coloridos na lateral esquerda */
        .input-group-text { 
            border-radius: 8px 0 0 8px; 
            border: 1px solid #e0e0e0; 
            background-color: #fff; 
            border-right: 0;
            width: 45px;
            justify-content: center;
        }

        /* Cores Específicas dos Ícones */
        .icon-blue { color: #4e73df; }      /* Azul para Nome */
        .icon-purple { color: #6610f2; }    /* Roxo para Categoria */
        .icon-green { color: #25d366; }     /* Verde WhatsApp para Celular */
        .icon-orange { color: #fd7e14; }    /* Laranja para Email */

        .btn-gradient-save { 
            background: linear-gradient(135deg, #4e73df, #224abe); 
            color: white; 
            border: none;
            transition: all 0.3s;
        }
        .btn-gradient-save:hover { 
            background: linear-gradient(135deg, #224abe, #1a3a9c); 
            color: white; 
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(78, 115, 223, 0.3);
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
                        <div class="mr-3" style="background: linear-gradient(135deg, #17a2b8, #117a8b); padding: 12px; border-radius: 10px;">
                            <i class="fas fa-user-plus fa-lg text-white"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark font-weight-bold">NOVO CLIENTE</h4>
                            <p class="text-muted mb-0">Preencha os dados do cliente</p>
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
                            <input type="hidden" name="cad_cli" value="ok">
                            
                            <!-- LINHA 1 -->
                            <div class="row">
                                <!-- Nome -->
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark small">Nome Completo *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-user icon-blue fa-lg"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="nome" maxlength="160" 
                                               onkeydown="upperCaseF(this)" placeholder="Nome do cliente" required>
                                    </div>
                                </div>
                                
                                <!-- Categoria -->
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark small">Grupo/Categoria *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-tags icon-purple fa-lg"></i></span>
                                        </div>
                                        <select class="form-control" name="cliente" required style="cursor: pointer;">
                                            <option value="">Selecione uma categoria...</option>								
                                            <?php
                                            $buscacli  = $connect->query("SELECT * FROM categoria WHERE idu = '".$cod_id."' ORDER BY nome ASC");
                                            while ($buscaclix = $buscacli->fetch(PDO::FETCH_OBJ)) { 
                                            ?>
                                            <option value="<?=$buscaclix->id;?>"><?php echo $buscaclix->nome;?></option> 
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- LINHA 2 -->
                            <div class="row">
                                <!-- Celular -->
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark small">Celular/WhatsApp *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fab fa-whatsapp icon-green fa-lg"></i></span>
                                        </div>
                                        <input type="text" class="form-control" name="celular" id="cel" 
                                               placeholder="(00) 00000-0000" required>
                                    </div>
                                </div>

                                <!-- Email (Opcional) -->
                                <div class="col-md-6 mb-4">
                                    <label class="font-weight-bold text-dark small">Email <span class="text-muted font-weight-normal">(Opcional)</span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-envelope icon-orange fa-lg"></i></span>
                                        </div>
                                        <input type="email" class="form-control" name="email" maxlength="60" placeholder="email@exemplo.com">
                                    </div>
                                </div>
                            </div>
                            
                            <hr class="my-4" />
                            
                            <div class="row">
                                <div class="col-md-12 text-center">
                                    <button type="submit" class="btn btn-gradient-save btn-lg px-5 shadow-sm" name="cart">
                                        <i class="fas fa-check mr-2"></i>SALVAR CLIENTE
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