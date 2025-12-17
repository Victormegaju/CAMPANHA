<?php require_once "topo.php"; ?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS Necessários -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../lib/select2/css/select2.min.css">
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        
        /* Inputs */
        .form-control { 
            border-radius: 0 8px 8px 0; 
            border: 1px solid #e0e0e0; 
            height: 45px; 
            padding-left: 15px; 
            border-left: 0; 
            background: #fff; /* Garante fundo branco */
        }
        .form-control:focus { border-color: #e0e0e0; box-shadow: none; border-bottom: 2px solid #dc3545; }
        
        /* Ícones */
        .input-group-text { 
            border-radius: 8px 0 0 8px; 
            border: 1px solid #e0e0e0; 
            background-color: #fff; 
            border-right: 0;
            width: 45px;
            justify-content: center;
        }

        .icon-blue-dark { color: #007bff; background-color: #e6f2ff; }
        .icon-green { color: #28a745; background-color: #e8f5e9; }
        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }
        .icon-orange { color: #fd7e14; background-color: #fff5eb; }

        /* --- CORREÇÃO DO SELECT2 (Tamanho e Layout) --- */
        .select2-container { 
            width: 100% !important; 
            flex: 1; 
            display: block; /* Garante comportamento de bloco */
        }
        .select2-container .select2-selection--single { 
            height: 45px; 
            border: 1px solid #e0e0e0; 
            border-left: none;
            border-radius: 0 8px 8px 0; 
            padding-top: 8px; 
            outline: none;
        }
        .select2-container--default .select2-selection--single .select2-selection__arrow { top: 10px; right: 10px; }
        
        .group-select { 
            display: flex; 
            width: 100%; 
            position: relative; /* Ajuda a conter o elemento */
        }

        .btn-gradient-primary { 
            background: linear-gradient(135deg, #007bff, #0056b3); 
            color: white; 
            border: none;
        }
    </style>
</head>
<body>

<div class="slim-mainpanel">
  <div class="container-fluid">
    
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <div class="mr-3" style="background: linear-gradient(135deg, #dc3545, #c82333); padding: 12px; border-radius: 10px;">
                        <i class="fas fa-file-invoice-dollar fa-lg text-white"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 text-dark font-weight-bold">CONTAS A RECEBER</h4>
                        <p class="text-muted mb-0">Cadastre uma nova cobrança</p>
                    </div>
                </div>
                <a href="contas_receber" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                    <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                </a>
            </div>
        </div>
    </div>

    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-body p-4">

            <form action="cad_contas_simulador" method="post" autocomplete="off">
              
              <!-- LINHA 1: CLIENTE -->
              <div class="row">
                <div class="col-md-12 mb-4">
                  <label class="font-weight-bold text-dark small">Selecione o Cliente *</label>
                  <div class="group-select">
                      <div class="input-group-prepend">
                          <span class="input-group-text icon-blue-dark" style="border-right: 0;"><i class="fas fa-user"></i></span>
                      </div>
                      <select class="form-control select2-show-search" name="cliente" required>
                        <option value="">Pesquisar cliente...</option>
                        <?php 
                        $buscacli = $connect->query("SELECT * FROM clientes WHERE idm = '" . $cod_id . "' ORDER BY nome ASC");
                        while ($buscaclix = $buscacli->fetch(PDO::FETCH_OBJ)) { 
                            // Correção: Mostrando Celular ao invés de CPF
                            $celular = !empty($buscaclix->celular) ? $buscaclix->celular : 'Sem número';
                        ?>
                          <option value="<?= $buscaclix->Id; ?>"><?php echo $buscaclix->nome; ?> - <?php echo $celular; ?></option>
                        <?php } ?>
                      </select>
                  </div>
                </div>
              </div>

              <!-- LINHA 2: VALOR, PARCELAS, DATA -->
              <div class="row">
                
                <!-- Valor (Correção da máscara aplicada no script abaixo) -->
                <div class="col-md-4 mb-4">
                  <label class="font-weight-bold text-dark small">Valor da Mensalidade *</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text icon-green"><i class="fas fa-dollar-sign"></i></span>
                    </div>
                    <!-- Removi classes conflitantes, mantive apenas 'dinheiro' -->
                    <input type="text" name="valor" class="form-control dinheiro" placeholder="0,00" required>
                  </div>
                </div>

                <div class="col-md-4 mb-4">
                  <label class="font-weight-bold text-dark small">Qtd. Parcelas *</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text icon-purple"><i class="fas fa-sort-numeric-up"></i></span>
                    </div>
                    <input type="number" name="parcelas" class="form-control" value="1" min="1" required>
                  </div>
                </div>

                <div class="col-md-4 mb-4">
                  <label class="font-weight-bold text-dark small">Vencimento 1ª Parcela *</label>
                  <div class="input-group">
                    <div class="input-group-prepend">
                      <span class="input-group-text icon-orange"><i class="fas fa-calendar-alt"></i></span>
                    </div>
                    <input type="date" id="datap" name="datap" class="form-control" required style="cursor: pointer;">
                  </div>
                </div>
              </div>

              <hr class="my-4" />

              <div class="row">
                <div class="col-md-12 text-center">
                  <button type="submit" class="btn btn-gradient-primary btn-lg px-5 shadow-sm" name="cart" id="btn-avancar">
                    AVANÇAR <i class="fas fa-arrow-right ml-2"></i>
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

<!-- Scripts Essenciais -->
<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="../lib/select2/js/select2.full.min.js"></script>

<!-- Biblioteca de Máscara (CDN Confiável para Dinheiro) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

<script>
  $(document).ready(function(){
      
      // 1. Configuração da Máscara de Dinheiro (Isso resolve o problema de não conseguir digitar)
      $('.dinheiro').mask('#.##0,00', {reverse: true});

      // 2. Configuração do Select2 (Isso resolve o layout quebrando)
      $('.select2-show-search').select2({
          minimumResultsForSearch: '', // Sempre mostra a busca
          width: '100%',               // Força largura de 100% do pai
          dropdownAutoWidth: false     // Impede que o dropdown expanda além da conta
      });

  });
</script>

<script src="../js/slim.js"></script>
</body>
</html>