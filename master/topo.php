<?php
ob_start();
session_start();

if ((!isset($_SESSION['cod_id']) == true)) {
    unset($_SESSION['cod_id']);
    header('location: ../');
    exit;
}

$cod_id = $_SESSION['cod_id'];
$bytes = random_bytes(16);
$idempotency = bin2hex($bytes);

require_once __DIR__ . '/../db/Conexao.php';

// DADOS GERAIS
$pegadadosgerais = $connect->query("SELECT * FROM carteira WHERE Id = '$cod_id'");
$dadosgerais = $pegadadosgerais->fetch(PDO::FETCH_OBJ);

// Verificação de Assinatura
$actualDate = date("Y-m-d");
$date = $dadosgerais->assinatura;
$dateParts = explode("/", $date);
$subscriptionDate = $dateParts[2] . "-" . $dateParts[1] . "-" . $dateParts[0];

// Cálculo de dias restantes (Para a barra de progresso)
$dataInicio = new DateTime($actualDate);
$dataFim = new DateTime($subscriptionDate);
$intervalo = $dataInicio->diff($dataFim);
$diasRestantes = $intervalo->days;
$isExpired = $actualDate > $subscriptionDate;

if ($isExpired && $dadosgerais->tipo > 1 && $dadosgerais->tipo != 2) {
    if (isset($_COOKIE['pdvx'])) {
        unset($_COOKIE['pdvx']);
        setcookie('pdvx', null, -1, '/');
    }
    session_destroy();
    session_write_close();
    header('location: ../index.php');
    exit;
}

// Configuração de Hora
date_default_timezone_set('America/Sao_Paulo');
$hora = date('H');
if ($hora >= 5 && $hora < 12) { $saudacao = "Bom dia"; $iconeSaudacao = "fa-sun"; $corSaudacao = "text-warning"; }
elseif ($hora >= 12 && $hora < 18) { $saudacao = "Boa tarde"; $iconeSaudacao = "fa-cloud-sun"; $corSaudacao = "text-orange"; }
else { $saudacao = "Boa noite"; $iconeSaudacao = "fa-moon"; $corSaudacao = "text-primary"; }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?php echo isset($_nomesistema) ? $_nomesistema : 'Painel Financeiro'; ?></title>
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="../lib/Ionicons/css/ionicons.css" rel="stylesheet">
    <link href="../lib/datatables/css/jquery.dataTables.css" rel="stylesheet">
    <link href="../lib/select2/css/select2.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/slim.css">
    
    <!-- Estilos Personalizados -->
    <style>
        :root {
            --header-bg-light: #ffffff;
            --header-bg-dark: #1e1e2d;
            --primary-color: #6f42c1;
            --secondary-color: #007bff;
        }

        body.dark-mode {
            background-color: #151521;
            color: #e6e6e6;
        }

        /* Header Moderno */
        .custom-header {
            background: var(--header-bg-light);
            border-bottom: 1px solid #eef2f7;
            height: 70px;
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            box-shadow: 0 0 35px 0 rgba(154,161,171,.15); /* Sombra mais moderna */
            transition: all 0.3s ease;
        }

        body.dark-mode .custom-header {
            background: var(--header-bg-dark);
            border-bottom: 1px solid rgba(255,255,255,0.05);
            box-shadow: none;
        }

        .header-container {
            width: 100%;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* Área da Esquerda */
        .left-area {
            display: flex;
            align-items: center;
        }

        /* Botão Menu Moderno */
        #slimSidebarMenu {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 8px; /* Bordas arredondadas */
            background-color: #f3f6f9; /* Fundo suave */
            color: #5e6278;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.2s;
            margin-right: 20px;
        }
        
        #slimSidebarMenu:hover {
            background-color: var(--primary-color);
            color: #fff;
        }

        body.dark-mode #slimSidebarMenu {
            background-color: #2b2b40;
            color: #a1a5b7;
        }
        body.dark-mode #slimSidebarMenu:hover {
            color: #fff;
            background-color: var(--primary-color);
        }

        /* Logo PainelPro (Texto em duas cores) */
        .brand-text {
            font-size: 1.5rem;
            font-weight: 800;
            color: #3f4254; /* Cor escura */
            text-decoration: none !important;
            font-family: 'Segoe UI', sans-serif;
            letter-spacing: -0.5px;
        }
        .brand-text span {
            color: var(--primary-color); /* Cor Roxa */
        }
        
        body.dark-mode .brand-text { color: #fff; }

        /* Pills de Assinatura */
        .subscription-pill {
            background: #f8f5ff;
            color: var(--primary-color);
            padding: 5px 15px;
            border-radius: 50px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        body.dark-mode .subscription-pill {
            background: #2b2b40;
            color: #b5b5c3;
        }
        
        .renew-btn {
            background: var(--primary-color);
            color: white !important;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            font-weight: 700;
            text-decoration: none;
        }

        /* Área do Usuário */
        .user-area {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .theme-switch {
            width: 35px; height: 35px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            color: #b5b5c3;
            transition: 0.2s;
        }
        .theme-switch:hover { color: #f6ad55; background: rgba(0,0,0,0.03); }

        .user-profile {
            display: flex; align-items: center; gap: 10px; cursor: pointer;
        }

        .user-avatar {
            width: 38px; height: 38px;
            border-radius: 10px; /* Quadrado arredondado moderno */
            background: linear-gradient(135deg, #6f42c1, #8950fc);
            color: white;
            display: flex; align-items: center; justify-content: center;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 3px 6px rgba(111,66,193,0.2);
        }

        .user-info { display: flex; flex-direction: column; line-height: 1.2; text-align: right; }
        .user-greeting { font-size: 0.75rem; color: #b5b5c3; }
        .user-name { font-weight: 700; color: #3f4254; font-size: 0.9rem; }
        
        body.dark-mode .user-name { color: #fff; }

        /* Loader */
        #loader {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: #fff; z-index: 9999;
            display: flex; justify-content: center; align-items: center;
        }
        
        /* Ajuste Mainpanel para não esconder conteúdo */
        .slim-mainpanel { padding-top: 90px; }

        @media (max-width: 768px) {
            .subscription-pill { display: none; }
            .user-info { display: none; }
        }
    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
</head>

<body>
    
    <!-- Loader -->
    <div id="loader">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Carregando...</span>
        </div>
    </div>

    <!-- Novo Header -->
    <header class="custom-header">
        <div class="header-container">
            
            <!-- Lado Esquerdo -->
            <div class="left-area">
                <!-- Botão Menu Original (Estilizado Moderno) -->
                <a id="slimSidebarMenu">
                    <i class="fas fa-bars"></i>
                </a>

                <!-- Logo PainelPro -->
                <a href="/." class="brand-text">
                    Painel<span>Pro</span>
                </a>
            </div>

            <!-- Centro: Assinatura -->
            <?php if ($dadosgerais->tipo > 1) { ?>
            <div class="subscription-pill d-none d-md-flex">
                <i class="far fa-clock"></i>
                <span>Expira em: <span class="text-dark font-weight-bold"><?php echo $diasRestantes; ?> dias</span></span>
                <a href="/master/planos" class="renew-btn">RENOVAR</a>
            </div>
            
            <!-- Modal Expiração (Logica Mantida) -->
            <?php if ($isExpired && strpos($_SERVER['REQUEST_URI'], '/master/planos') !== false) { ?>
                <script>
                    $(document).ready(function(){
                        $('#expiry-modal').modal('show');
                    });
                </script>
                <div class="modal fade" id="expiry-modal" data-backdrop="static" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-body text-center p-5">
                                <i class="fas fa-exclamation-circle fa-4x text-danger mb-3"></i>
                                <h4>Plano Expirado</h4>
                                <p>Sua assinatura venceu. Renove agora para continuar acessando.</p>
                                <a href="/master/planos" class="btn btn-primary btn-block rounded-pill mt-3">Renovar Agora</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php } ?>

            <!-- Lado Direito: Tema e Usuário -->
            <div class="user-area">
                
                <!-- Toggle Dark Mode -->
                <div class="theme-switch" id="darkModeSwitch" onclick="toggleDarkMode()">
                    <i class="fas fa-moon" id="darkModeIcon"></i>
                </div>

                <!-- Perfil -->
                <div class="dropdown">
                    <div class="user-profile" data-toggle="dropdown">
                        <div class="user-info">
                            <span class="user-greeting"><?php echo $saudacao; ?></span>
                            <span class="user-name"><?php echo explode(' ', $dadosgerais->nome)[0]; ?></span>
                        </div>
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($dadosgerais->nome, 0, 1)); ?>
                        </div>
                    </div>
                    <!-- Dropdown Menu -->
                    <div class="dropdown-menu dropdown-menu-right shadow border-0 mt-3" style="border-radius: 10px;">
                        <a href="perfil" class="dropdown-item py-2"><i class="fas fa-user-circle mr-2 text-primary"></i> Meu Perfil</a>
                        <a href="configuracoes" class="dropdown-item py-2"><i class="fas fa-cog mr-2 text-secondary"></i> Configurações</a>
                        <div class="dropdown-divider"></div>
                        <a href="sair" class="dropdown-item py-2 text-danger"><i class="fas fa-sign-out-alt mr-2"></i> Sair</a>
                    </div>
                </div>

            </div>
        </div>
    </header>

    <script>
        function toggleDarkMode() {
            var body = document.body;
            var icon = document.getElementById('darkModeIcon');
            
            body.classList.toggle('dark-mode');

            if (body.classList.contains('dark-mode')) {
                icon.className = 'fas fa-sun'; // Muda para sol
                localStorage.setItem('mode', 'dark');
            } else {
                icon.className = 'fas fa-moon'; // Muda para lua
                localStorage.setItem('mode', 'light');
            }
        }

        window.onload = function () {
            var body = document.body;
            var icon = document.getElementById('darkModeIcon');
            var mode = localStorage.getItem('mode');

            if (mode === 'dark') {
                body.classList.add('dark-mode');
                icon.className = 'fas fa-sun';
            } else {
                body.classList.remove('dark-mode');
                icon.className = 'fas fa-moon';
            }

            // Remove loader
            setTimeout(function () {
                $('#loader').fadeOut();
            }, 600);
        }
    </script>
</body>
</html>