<?php
require_once "topo.php";
$wallet = $connect->query("SELECT * FROM carteira WHERE Id = 1");
$walletRow = $wallet->fetch(PDO::FETCH_OBJ);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body { background-color: #f5f7fb; }
        
        /* Grid de Planos */
        .plans-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 25px;
            padding: 20px 0;
        }

        /* Card do Plano */
        .plan-card {
            background: #fff;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            border: 1px solid #f0f0f0;
            position: relative;
            display: flex;
            flex-direction: column;
        }

        .plan-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        }

        /* Header Colorido (Variações) */
        .plan-header {
            padding: 30px 20px;
            text-align: center;
            color: white;
            position: relative;
        }
        
        /* Cores Variadas para os Cards */
        .plan-card:nth-child(4n+1) .plan-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); } /* Roxo */
        .plan-card:nth-child(4n+2) .plan-header { background: linear-gradient(135deg, #00b09b, #96c93d); } /* Verde */
        .plan-card:nth-child(4n+3) .plan-header { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 99%, #fecfef 100%); color: #444; } /* Rosa */
        .plan-card:nth-child(4n+4) .plan-header { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); } /* Lilás */

        .plan-title { font-size: 1.2rem; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 10px; }
        
        .plan-price { font-size: 2.5rem; font-weight: 800; display: flex; align-items: flex-start; justify-content: center; }
        .plan-currency { font-size: 1rem; margin-top: 10px; margin-right: 5px; opacity: 0.8; }
        .plan-cents { font-size: 1rem; margin-top: 10px; opacity: 0.8; }

        /* Botão Editar Flutuante */
        .btn-edit-float {
            position: absolute; top: 15px; right: 15px;
            background: rgba(255,255,255,0.2); border: none; color: white;
            width: 35px; height: 35px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            backdrop-filter: blur(5px); cursor: pointer; transition: 0.2s;
        }
        .btn-edit-float:hover { background: rgba(255,255,255,0.4); transform: scale(1.1); }

        /* Lista de Recursos */
        .plan-features { padding: 25px; flex-grow: 1; }
        .feature-item { 
            display: flex; align-items: center; margin-bottom: 12px; color: #555; font-size: 0.9rem; 
        }
        .icon-check { 
            color: #2ecc71; margin-right: 10px; background: #e8f8f5; 
            width: 25px; height: 25px; border-radius: 50%; display: flex; 
            align-items: center; justify-content: center; flex-shrink: 0;
        }

        /* Footer e Botão */
        .plan-footer { padding: 20px; text-align: center; background: #f9f9f9; border-top: 1px solid #eee; }
        .btn-subscribe {
            display: block; width: 100%; padding: 12px;
            background: #333; color: white; border-radius: 50px;
            font-weight: bold; text-decoration: none; transition: 0.3s;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .btn-subscribe:hover { background: #000; transform: translateY(-2px); color: white; }

        .payment-info { font-size: 0.75rem; color: #888; margin-top: 10px; }
    </style>
</head>
<body>

<div class="slim-mainpanel">
  <div class="container">
    
    <!-- Cabeçalho da Página -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="text-dark font-weight-bold mb-0">PLANOS DE ASSINATURA</h4>
            <p class="text-muted mb-0">Gerencie seus pacotes de serviços</p>
        </div>
        <div class="col-md-6 text-md-right">
            <?php if ($dadosgerais->tipo == 1) { ?>
                <a href="cad_planos" class="btn btn-primary shadow-sm" style="background: linear-gradient(135deg, #667eea, #764ba2); border:none; border-radius: 50px; padding: 10px 25px;">
                    <i class="fas fa-plus-circle mr-2"></i> NOVO PLANO
                </a>
            <?php } ?>
        </div>
    </div>

    <?php if (isset($_GET["cad"]) && $_GET["cad"] == "ok") { ?>
      <div class="alert alert-success shadow-sm mb-4" role="alert" style="border-radius: 10px;">
        <i class="fas fa-check-circle mr-2"></i> <strong>Sucesso!</strong> Plano cadastrado. Pode levar alguns minutos para aparecer na lista.
      </div>
    <?php } ?>

    <?php if (isset($_GET["edit"]) && $_GET["edit"] == "ok") { ?>
      <div class="alert alert-warning shadow-sm mb-4" role="alert" style="border-radius: 10px;">
        <i class="fas fa-edit mr-2"></i> <strong>Atualizado!</strong> Plano editado com sucesso.
      </div>
    <?php } ?>

    <div class="plans-container">
      <?php
      // --- LÓGICA DE BUSCA DA API ---
      $curl = curl_init();
      
      // Busca TODOS os planos (active e inactive para debug, depois filtra)
      // Removido ?status=active da URL para pegar tudo e filtrar no PHP se necessário
      // Adicionado limit e offset para garantir paginação
      curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://api.mercadopago.com/preapproval_plan/search?status=active&limit=50',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $walletRow->tokenmp
          )
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);
      
      $planos = json_decode($response, true);
      
      if ($err) {
          echo '<div class="alert alert-danger">Erro ao conectar com Mercado Pago: '.$err.'</div>';
      } elseif (!isset($planos['results']) || empty($planos['results'])) {
          echo '<div class="col-12 text-center py-5"><h5 class="text-muted">Nenhum plano encontrado ou aguardando indexação.</h5></div>';
      } else {

        foreach ($planos['results'] as $plano) {
            // Formatação de Preço
            $amount = $plano['auto_recurring']['transaction_amount'];
            $value_parts = explode('.', number_format($amount, 2, '.', ''));
            
            // Link de Pagamento (init_point)
            $link = isset($plano['init_point']) ? $plano['init_point'] : '#';
      ?>
      
      <div class="plan-card">
        <div class="plan-header">
            <?php if ($dadosgerais->tipo == 1) { ?>
            <form action="edit_planos" method="post">
                <input type="hidden" name="id" value="<?php echo $plano['id']; ?>">
                <button type="submit" class="btn-edit-float" title="Editar"><i class="fas fa-pencil-alt"></i></button>
            </form>
            <?php } ?>

            <div class="plan-title"><?php echo $plano['reason']; ?></div>
            <div class="plan-price">
                <span class="plan-currency">R$</span>
                <?php echo $value_parts[0]; ?>
                <span class="plan-cents">,<?php echo $value_parts[1]; ?></span>
            </div>
            <small><?php echo ($plano['auto_recurring']['frequency'] == 1) ? 'Mensal' : 'Recorrente'; ?></small>
        </div>

        <div class="plan-features">
            <div class="feature-item"><span class="icon-check"><i class="fas fa-check"></i></span> Área do Cliente</div>
            <div class="feature-item"><span class="icon-check"><i class="fas fa-check"></i></span> API WhatsApp Própria</div>
            <div class="feature-item"><span class="icon-check"><i class="fas fa-check"></i></span> Envio em Massa</div>
            <div class="feature-item"><span class="icon-check"><i class="fas fa-check"></i></span> Financeiro Completo</div>
            <div class="feature-item"><span class="icon-check"><i class="fas fa-check"></i></span> Fatura Automática</div>
            <div class="feature-item"><span class="icon-check"><i class="fas fa-check"></i></span> Clientes Ilimitados</div>
            <div class="feature-item"><span class="icon-check"><i class="fas fa-check"></i></span> Suporte Remoto</div>
        </div>

        <div class="plan-footer">
            <a href="<?php echo $link; ?>" target="_blank" class="btn-subscribe">CONTRATAR AGORA</a>
            <div class="payment-info"><i class="fab fa-pix"></i> Pix &bull; <i class="far fa-credit-card"></i> Cartão</div>
            <div class="mt-2 small text-danger" style="font-size: 0.7rem;">*Necessário retornar ao site após pagar</div>
        </div>
      </div>

      <?php } // Fim do foreach
      } // Fim do else
      ?>
    </div>

  </div>
</div>

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../js/slim.js"></script>
</body>
</html>