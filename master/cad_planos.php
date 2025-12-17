<?php
ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

require_once "topo.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        
        /* Inputs Customizados */
        .input-group-text {
            border-radius: 8px 0 0 8px;
            border: 1px solid #e0e0e0;
            border-right: none;
            background: white;
            width: 45px;
            justify-content: center;
        }
        .form-control, .custom-select {
            border-radius: 0 8px 8px 0;
            border: 1px solid #e0e0e0;
            border-left: none;
            height: 45px;
            padding-left: 10px;
        }
        .form-control:focus, .custom-select:focus {
            box-shadow: none;
            border-color: #e0e0e0;
            border-bottom: 2px solid #6f42c1;
        }

        /* Cores Ícones */
        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }
        .icon-green { color: #28a745; background-color: #e8f5e9; }
        .icon-header { 
            background: linear-gradient(135deg, #6f42c1, #8e44ad); 
            padding: 12px; border-radius: 10px; box-shadow: 0 4px 10px rgba(111, 66, 193, 0.3);
        }

        /* Botão */
        .btn-gradient {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white; border: none; padding: 12px 40px; border-radius: 50px;
            font-weight: bold; letter-spacing: 0.5px; transition: all 0.3s;
        }
        .btn-gradient:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0,123,255,0.3); color: white; }
        
        .btn-back { border-radius: 50px; padding: 8px 20px; font-weight: 600; }
    </style>
</head>
<body>

  <div class="slim-mainpanel">
    <div class="container">
      
      <!-- Cabeçalho -->
      <div class="row mb-4 align-items-center">
          <div class="col-md-6">
              <div class="d-flex align-items-center">
                  <div class="mr-3 icon-header">
                      <i class="fas fa-layer-group fa-lg text-white"></i>
                  </div>
                  <div>
                      <h4 class="mb-0 text-dark font-weight-bold">NOVO PLANO</h4>
                      <p class="text-muted mb-0">Crie um novo pacote de assinatura</p>
                  </div>
              </div>
          </div>
          <div class="col-md-6 text-md-right mt-3 mt-md-0">
              <a href="planos" class="btn btn-secondary btn-back shadow-sm">
                  <i class="fas fa-arrow-left mr-2"></i> VOLTAR
              </a>
          </div>
      </div>

      <div class="row justify-content-center">
        <div class="col-md-10">
          <div class="card">
            <div class="card-body p-5">
              
              <form action="classes/planos_exe.php" method="post">
                
                <div class="row">
                  <!-- Tipo de Plano -->
                  <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark small">Periodicidade / Nome</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text icon-purple"><i class="fas fa-calendar-alt"></i></span>
                        </div>
                        <select class="custom-select" name="type" required>
                            <option value="">Selecione a periodicidade...</option>
                            <option value="TWVuc2Fs">Mensal</option>
                            <option value="VHJpbWVzdHJhbA==">Trimestral</option>
                            <option value="U2VtZXN0cmFs">Semestral</option>
                            <option value="QW51YWw=">Anual</option>
                        </select>
                    </div>
                    <small class="text-muted">Isso definirá o nome e a cobrança recorrente.</small>
                  </div>

                  <!-- Valor -->
                  <div class="col-md-6 mb-4">
                    <label class="font-weight-bold text-dark small">Valor da Assinatura (R$)</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text icon-green"><i class="fas fa-dollar-sign"></i></span>
                        </div>
                        <input type="text" name="value" class="money form-control" placeholder="0,00" required>
                    </div>
                  </div>
                </div>

                <hr class="my-4" />

                <div class="row">
                  <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-gradient shadow">
                        <i class="fas fa-check-circle mr-2"></i> CADASTRAR PLANO
                    </button>
                  </div>
                </div>

                <input type="hidden" name="cad_planos" value="ok">
                <input type="hidden" name="base_url" value="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . $_SERVER['HTTP_HOST']; ?>">
              </form>

            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="../lib/jquery/js/jquery.js"></script>
  <script src="../js/moeda.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <script>
    $(document).ready(function(){
        $('.money').mask('#.##0,00', { reverse: true });
    });
  </script>
  <script src="../js/slim.js"></script>
</body>
</html>