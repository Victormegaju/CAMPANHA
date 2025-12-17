<div class="slim-body">
    
    
  <?php
  // ... (SEU CÓDIGO PHP EXISTENTE) ...
  $urlk = $_SERVER["REQUEST_URI"];
  $currentPage = basename($urlk);
  $url_base = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
  
  $dataExpiracaoBr = isset($dataExpiracao) ? date('d/m/Y', strtotime($dataExpiracao)) : date('d/m/Y'); 

  $menuItems = [

    ["name" => "Home", "path" => "./", "icon" => $url_base . "/master/icon/controle.png", "activeOn" => ["master"]],
    ["name" => "Grupo/Categoria", "path" => "categorias", "icon" => $url_base . "/master/icon/categorizar.png", "activeOn" => ["categorias", "cad_categoria", "edit_categoria"]],
    ["name" => "Clientes", "path" => "clientes", "icon" => $url_base . "/master/icon/clientes.png", "activeOn" => ["clientes", "ver_cliente", "cad_cliente", "edit_cliente"]],
    ["name" => "Contas a Receber", "path" => "contas_receber", "icon" => $url_base . "/master/icon/fatura.png", "activeOn" => ["cad_contas", "contas_receber", "editar_mensalidade", "cad_contas_simulador", "ver_financeiro"]],
    ["name" => "Contas a Pagar", "path" => "contas_pagar", "icon" =>  $url_base . "/master/icon/verificar.png", "activeOn" => ["contas_pagar", "cad_pagar", "editar_pagamento", "cad_pagar_simulador"]],
    ["name" => "Extrato de Pagamento", "path" => "finalizados", "icon" =>  $url_base . "/master/icon/pagamento.png", "activeOn" => ["finalizados", "ver_financeiro_quitado"]],
    ["name" => "Notificações", "path" => "mensagens", "icon" =>  $url_base . "/master/icon/notificacoes.png", "activeOn" => ["mensagens", "edit_mensagens"]],
  ];

  if (isset($dadosgerais->tipo) && $dadosgerais->tipo == 1) {
    $menuItems[] = ["name" => "Usuários SAAS", "path" => "usuarios", "icon" =>  $url_base . "/master/icon/usuarios.png", "activeOn" => ["usuarios", "cad_usuario", "edit_usuario", "painel_usuario"]];
  }
// ... código existente ...
$menuItems[] = ["name" => "WhatsApp", "path" => "whatsapp", "icon" => $url_base . "/master/icon/whatsapp.png", "activeOn" => ["whatsapp"]];

// ADICIONE ESTA LINHA:
$menuItems[] = ["name" => "Campanhas", "path" => "campanhas", "icon" => $url_base . "/master/icon/notificacoes.png", "activeOn" => ["campanhas", "nova_campanha"]];

// ... restante do código ...
  $menuItems[] = ["name" => "Planos", "path" => "planos", "icon" =>  $url_base . "/master/icon/planos.png", "activeOn" => ["planos", "cad_planos", "edit_planos"]];
  $menuItems[] = ["name" => "Meu Perfil", "path" => "perfil", "icon" => $url_base . "/master/icon/profile.png", "activeOn" => ["perfil"]];
  $menuItems[] = ["name" => "Configurações", "path" => "configuracoes", "icon" => $url_base . "/master/icon/configurar.png", "activeOn" => ["configuracoes"]];
  
  $menuItems[] = ["name" => "WhatsApp", "path" => "whatsapp", "icon" => $url_base . "/master/icon/whatsapp.png", "activeOn" => ["whatsapp"]];
  $menuItems[] = ["name" => "MercadoPago", "path" => "mercadopago", "icon" => $url_base . "/master/icon/mercado.png", "activeOn" => ["mercadopago"]];
  $menuItems[] = ["name" => "Tutoriais", "path" => "tutoriais", "icon" => $url_base . "/master/icon/tutorial.png", "activeOn" => ["tutoriais"]];
  $menuItems[] = ["name" => "Log de Atualizações", "path" => "update", "icon" => $url_base . "/master/icon/update.png", "activeOn" => ["update"]];
  $menuItems[] = ["name" => "Sair", "path" => "sair", "icon" => $url_base . "/master/icon/sair.png", "activeOn" => ["sair"]];
  ?>

     <!-- ESTILOS CSS (Mobile e Desktop) -->
  <style>
    /* --- DESKTOP (PC > 992px) --- */
    @media (min-width: 992px) {
        .slim-sidebar {
            position: relative !important; 
            border-radius: 20px !important; 
            margin: 15px !important;
            height: calc(100vh - 30px) !important;
            background-color: #fff !important;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            border: 1px solid #eee;
            z-index: 100 !important;
            display: block !important; /* Sempre visível no PC */
        }
        /* Esconde o overlay no PC */
        #mobile-menu-overlay { display: none !important; }
    }

    /* --- MOBILE (Celular < 991px) --- */
    @media (max-width: 991px) {
        .slim-sidebar {
            position: fixed !important;
            top: 70px !important;
            left: 10px !important;
            width: 260px !important;
            height: auto !important; 
            max-height: calc(100vh - 90px);
            overflow-y: auto;
            
            background-color: #fff !important;
            border-radius: 20px !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5) !important;
            z-index: 9999 !important; /* Fica acima do overlay */

            /* Efeito de abrir/fechar */
            transform: translateX(-150%);
            transition: transform 0.3s ease-in-out;
            display: block !important;
        }

        /* Classe que abre o menu */
        body.show-sidebar .slim-sidebar {
            transform: translateX(0) !important;
        }

        /* --- OVERLAY (Fundo que detecta o clique) --- */
        #mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.3); /* Fundo escuro semitransparente */
            z-index: 9990; /* Fica ATRÁS do menu (9999), mas NA FRENTE do site */
            display: none; /* Escondido por padrão */
            opacity: 0;
            transition: opacity 0.3s;
        }

        /* Mostra o overlay quando o menu abre */
        body.show-sidebar #mobile-menu-overlay {
            display: block;
            opacity: 0;
        }
    }
  </style>

  <!-- ELEMENTO OVERLAY (Adicionado para detectar o clique fora) -->
  <div id="mobile-menu-overlay" onclick="fecharMenuMobile()"></div>

  <div class="slim-sidebar">
      <?php if (isset($dadosgerais->tipo) && $dadosgerais->tipo > 1) { ?>
        <div id="msg-assinatura" class="slim-header-center mobile">
          <div class="time" style="padding-top: 5px;">Seu plano expira em: <span class="expiration-date"><?php echo isset($dataExpiracao) ? date('d/m/Y', strtotime($dataExpiracao)) : ''; ?></span></div>
        </div>
      <?php } ?>

    <label class="sidebar-label">MENU</label>

    <ul class="nav nav-sidebar">
      <?php foreach ($menuItems as $item): ?>
        <li class="sidebar-nav-item">
          <a href="<?php echo $item["path"]; ?>"
            class="sidebar-nav-link <?php if (in_array($currentPage, $item["activeOn"])) {
              echo "active";
            } ?>">
            <?php if (filter_var($item["icon"], FILTER_VALIDATE_URL)): ?>
              <img src="<?php echo $item["icon"]; ?>" class="mg-r-10" style="font-size: 16px; width: 24px; height: 24px;"
                alt="<?php echo $item["name"]; ?>">
            <?php else: ?>
              <i class="<?php echo $item["icon"]; ?> mg-r-10" style="font-size: 16px;"></i>
            <?php endif; ?>

            <?php echo $item["name"]; ?>
          </a>
        </li>
      <?php endforeach; ?>
    </ul>
    
    <div style="height: 20px;"></div>
  </div>

  <!-- SCRIPT PARA FECHAR O MENU -->
  <script>
    function fecharMenuMobile() {
        // Remove a classe que deixa o menu aberto
        document.body.classList.remove('show-sidebar');
        
        // Se o seu template usa outra classe além de 'show-sidebar' (ex: 'active'), remova aqui também:
        document.querySelector('.slim-sidebar').classList.remove('active');
    }
  </script>