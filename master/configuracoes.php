<?php
require_once "topo.php";

$editarcat = $connect->query("SELECT * FROM carteira WHERE Id='$cod_id'");
$dadoscat = $editarcat->fetch(PDO::FETCH_OBJ);

$scheme = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
$host = $_SERVER['HTTP_HOST'];
$url = $scheme . "://" . $host;
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
        .form-control:focus, .custom-select:focus { border-color: #e0e0e0; box-shadow: none; border-bottom: 2px solid #6610f2; }
        
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
        .icon-gray { color: #6c757d; background-color: #f8f9fa; }      /* Logo */
        .icon-blue { color: #007bff; background-color: #e6f2ff; }       /* Nome */
        .icon-orange { color: #fd7e14; background-color: #fff5eb; }     /* CNPJ */
        .icon-green { color: #28a745; background-color: #e8f5e9; }      /* WhatsApp */
        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }     /* Modelo */
        .icon-teal { color: #20c997; background-color: #e6fffa; }       /* Pix */
        .icon-pink { color: #e83e8c; background-color: #fce8f3; }       /* Background */

        /* Botão Salvar */
        .btn-gradient-primary { 
            background: linear-gradient(135deg, #6610f2, #4e73df); 
            color: white; 
            border: none;
            transition: all 0.3s;
        }
        .btn-gradient-primary:hover { 
            background: linear-gradient(135deg, #520dc2, #2e59d9); 
            color: white; 
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(102, 16, 242, 0.3);
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
                    <div class="mr-3" style="background: linear-gradient(135deg, #6c757d, #343a40); padding: 12px; border-radius: 10px;">
                        <i class="fas fa-cogs fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">CONFIGURAÇÕES</h4>
                        <p class="text-muted mb-0">Gerencie os dados da sua empresa</p>
                    </div>
                </div>
                <a href="./" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                    <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                </a>
            </div>
        </div>
    </div>

    <?php if (isset($_GET["sucesso"])) { ?>
      <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" style="border-radius: 10px;">
        <strong>Sucesso!</strong> As configurações foram atualizadas.
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <meta http-equiv="refresh" content="1;URL=./configuracoes" />
    <?php } ?>

    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body p-4">

            <form action="classes/config_exe.php" method="post" enctype="multipart/form-data" autocomplete="off">
              <input type="hidden" name="edit_emp" value="<?php print $cod_id; ?>">

              <!-- LINHA 1: LOGO (APENAS ADMIN) + DADOS BÁSICOS -->
              <div class="row">
                
                <?php if ($cod_id == 1) { ?>
                  <div class="col-md-12 mb-4">
                    <label class="font-weight-bold text-dark small">Logo da Empresa</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text icon-gray"><i class="fas fa-image"></i></span>
                        </div>
                        <input type="file" class="form-control" name="logoEmpresa" accept="image/png" style="padding: 10px;">
                    </div>
                    <small class="text-muted">Recomendado: Formato PNG transparente.</small>
                  </div>
                <?php } ?>

                <div class="col-md-6 mb-4">
                  <label class="font-weight-bold text-dark small">Nome Comercial</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-blue"><i class="fas fa-building"></i></span>
                      </div>
                      <input type="text" class="form-control" name="nomecom" value="<?php print $dadoscat->nomecom; ?>" required>
                  </div>
                </div>

                <div class="col-md-6 mb-4">
                  <label class="font-weight-bold text-dark small">CNPJ</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-orange"><i class="fas fa-id-card"></i></span>
                      </div>
                      <input type="text" class="form-control" name="cnpj" value="<?php print $dadoscat->cnpj; ?>" required>
                  </div>
                </div>
              </div>

              <div class="row">
                <!-- Contato -->
                <div class="col-md-4 mb-4">
                  <label class="font-weight-bold text-dark small">WhatsApp de Suporte</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-green"><i class="fab fa-whatsapp"></i></span>
                      </div>
                      <input type="text" class="form-control" name="contato" id="cel" value="<?php print $dadoscat->contato; ?>" required>
                  </div>
                </div>

                <!-- Modelo de Cobrança -->
                <div class="col-md-4 mb-4">
                  <label class="font-weight-bold text-dark small">Modelo de Cobrança</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-purple"><i class="fas fa-credit-card"></i></span>
                      </div>
                      <select class="form-control" name="tipopgmto" required>
                        <option value="1" <?php if($dadoscat->pagamentos == "1") echo 'selected="selected"'; ?>>Mercado Pago (Automático)</option>
                        <option value="2" <?php if($dadoscat->pagamentos == "2") echo 'selected="selected"'; ?>>Sem Gateway (Manual)</option>
                      </select>
                  </div>
                </div>

                <!-- Chave Pix -->
                <div class="col-md-4 mb-4">
                  <label class="font-weight-bold text-success small">Chave Pix (Manual)</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-teal"><i class="fas fa-key"></i></span>
                      </div>
                      <input type="text" class="form-control" name="pix_manual" value="<?php echo isset($dadoscat->pix_manual) ? htmlspecialchars($dadoscat->pix_manual) : ''; ?>" placeholder="Email, CPF ou Aleatória">
                  </div>
                </div>
              </div>

              <!-- BACKGROUND (APENAS ADMIN) -->
              <?php if ($cod_id == 1) { ?>
              <div class="row">
                <div class="col-md-12 mb-4">
                  <label class="font-weight-bold text-dark small">Imagem de Fundo (Login)</label>
                  <div class="input-group">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-pink"><i class="fas fa-desktop"></i></span>
                      </div>
                      <input type="text" class="form-control" name="background" value="<?php echo htmlspecialchars($dadoscat->background); ?>" placeholder="URL da imagem">
                  </div>
                </div>
              </div>
              <?php } ?>
              
              <hr class="my-4" />

              <div class="row">
                <div class="col-md-12 text-center">
                  <button type="submit" class="btn btn-gradient-primary btn-lg px-5 shadow-sm" name="cart">
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

<!-- Scripts -->
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script>
  $(function(){
    $('#cel').mask('(99) 99999-9999');
  });
</script>
<script src="../js/slim.js"></script>
</body>
</html>