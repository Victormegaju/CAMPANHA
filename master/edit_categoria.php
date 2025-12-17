<?php
require_once "topo.php";

$edicli = $_POST['edicli'];
// Previne erro se acessar direto sem post
if(!isset($edicli)){ header("location: categorias"); exit; }

$editarcat = $connect->query("SELECT * FROM categoria WHERE id='$edicli'");
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
        .form-control { border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; height: 45px; padding-left: 15px; border-left: 0; }
        .form-control:focus { border-color: #fd7e14; box-shadow: none; } /* Foco Laranja */
        
        /* Ícones Coloridos */
        .input-group-text { 
            border-radius: 8px 0 0 8px; 
            border: 1px solid #e0e0e0; 
            border-right: 0;
            width: 45px;
            justify-content: center;
        }

        /* Cor Laranja para o Ícone de Edição (Para não repetir o Roxo do Header) */
        .icon-orange { color: #fd7e14; background-color: #fff5eb; }

        /* Botão Salvar */
        .btn-gradient-success { background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; }
        .btn-gradient-success:hover { background: linear-gradient(135deg, #218838, #1e7e34); color: white; transform: translateY(-1px); }
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
                      <!-- Header com gradiente Roxo/Azul -->
                      <div class="mr-3" style="background: linear-gradient(135deg, #6610f2, #4e73df); padding: 12px; border-radius: 10px;">
                          <i class="fas fa-edit fa-lg text-white"></i>
                      </div>
                      <div>
                          <h4 class="mb-0 text-dark font-weight-bold">EDITAR CATEGORIA</h4>
                          <p class="text-muted mb-0">Alterar dados da categoria</p>
                      </div>
                  </div>
                  <a href="categorias" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                      <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                  </a>
              </div>
          </div>
      </div>

      <!-- Formulário -->
      <div class="row justify-content-center">
        <div class="col-md-12">
          <div class="card">
            <div class="card-body p-4">
              
              <form id="formulario">
                <input type="hidden" name="edit_cat" id="edit_cat" value="<?php print $edicli; ?>">

                <div class="row">
                  <div class="col-md-12 mb-3">
                    <label class="font-weight-bold text-dark small">Nome da Categoria <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <!-- Ícone Laranja -->
                            <span class="input-group-text icon-orange"><i class="fas fa-tag"></i></span>
                        </div>
                        <input type="text" class="form-control" name="nome" id="nome" 
                               value="<?php print $dadoscat->nome; ?>" maxlength="160" 
                               onkeydown="upperCaseF(this)" required placeholder="Digite o nome da categoria">
                    </div>
                  </div>
                </div>

                <hr class="my-4" />

                <div class="row">
                  <div class="col-md-12 text-center">
                    <!-- Botão Verde -->
                    <button type="button" onclick="editarRegistro()" class="btn btn-gradient-success btn-lg px-5 shadow-sm">
                        <i class="fas fa-check mr-2"></i>SALVAR ALTERAÇÕES
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
  <script src="../lib/bootstrap/js/bootstrap.js"></script>
  
  <script>
  function editarRegistro() {
    var nome = $("#nome").val();
    var edit_cat = $("#edit_cat").val();

    if(nome.trim() == "") {
        alert("Por favor, preencha o nome.");
        return;
    }

    $.ajax({
      type: "POST",
      url: "classes/categoria_exe.php",
      data: { nome: nome, edit_cat: edit_cat },
      success: function (response) {
        // Redireciona após salvar
        window.location.href='./categorias?sucesso=ok';
      },
      error: function() {
        alert("Erro ao salvar.");
      }
    });
  }

  function upperCaseF(a) {
    setTimeout(function() {
      a.value = a.value.toUpperCase();
    }, 1);
  }
  </script>
  
  <script src="../js/slim.js"></script>
</body>
</html>