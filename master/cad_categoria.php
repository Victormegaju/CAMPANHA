<?php require_once "topo.php"; ?>
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
        .form-control { border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; height: 45px; padding-left: 15px; border-left: 0; }
        .form-control:focus { border-color: #e0e0e0; box-shadow: none; border-bottom: 2px solid #6610f2; }
        
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
        .icon-purple { color: #6610f2; } /* Roxo para Categoria */

        .btn-gradient-save { 
            background: linear-gradient(135deg, #6610f2, #520dc2); 
            color: white; 
            border: none;
            transition: all 0.3s;
        }
        .btn-gradient-save:hover { 
            background: linear-gradient(135deg, #520dc2, #3e0a91); 
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
                      <div class="mr-3" style="background: linear-gradient(135deg, #6610f2, #4e73df); padding: 12px; border-radius: 10px;">
                          <i class="fas fa-tag fa-lg text-white"></i>
                      </div>
                      <div>
                          <h4 class="mb-0 text-dark font-weight-bold">NOVA CATEGORIA</h4>
                          <p class="text-muted mb-0">Cadastre um novo grupo</p>
                      </div>
                  </div>
                  <a href="categorias" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                      <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                  </a>
              </div>
          </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body p-4">

              <form id="formulario" autocomplete="off">
                <input type="hidden" name="cad_cat" id="cad_cat" value="ok">

                <div class="row">
                  <div class="col-md-12 mb-4">
                    <label class="font-weight-bold text-dark small">Nome da Categoria *</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-tags icon-purple fa-lg"></i></span>
                        </div>
                        <input type="text" class="form-control" name="nome" id="nome" maxlength="160" 
                               onkeydown="upperCaseF(this)" placeholder="Ex: Clientes VIP, Fornecedores..." required>
                    </div>
                  </div>
                </div>

                <hr class="my-4" />

                <div class="row">
                  <div class="col-md-12 text-center">
                    <button type="button" onclick="inserirRegistro()" class="btn btn-gradient-save btn-lg px-5 shadow-sm">
                        <i class="fas fa-check mr-2"></i>SALVAR CATEGORIA
                    </button>
                  </div>
                </div>
              </form>

              <div id="mensagem"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../lib/jquery/js/jquery.js"></script>
  <script src="../lib/bootstrap/js/bootstrap.js"></script>
  
  <script>
  function inserirRegistro() {
    var nome = $("#nome").val();
    var cad_cat = $("#cad_cat").val();

    if(nome == "") {
        alert("Por favor, digite o nome da categoria.");
        return;
    }

    $.ajax({
      type: "POST",
      url: "classes/categoria_exe.php",
      data: {
        nome: nome,
        cad_cat: cad_cat
      },
      success: function (response) {
        window.location.href = './categorias?sucesso=ok';
      },
      error: function() {
        alert("Erro ao salvar.");
      }
    });
  }

  function upperCaseF(a) {
    setTimeout(function () {
      a.value = a.value.toUpperCase();
    }, 1);
  }
  </script>
  <script src="../js/slim.js"></script>
</body>
</html>