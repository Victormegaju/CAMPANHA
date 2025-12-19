<?php
require_once __DIR__ . '/db/Conexao.php';

// Inicia sessão
if(!isset($_SESSION)){ session_start(); }

// --- LÓGICA DE LOGIN ---
if (isset($_POST["login"])) {

    $response = ['status' => 'error', 'message' => 'Erro desconhecido'];
    
    // 1. LÓGICA DO RECAPTCHA PERSISTENTE (COOKIE)
    // Verifica se já existe o Cookie de "Humano"
    $is_human = isset($_COOKIE['auth_human']);

    // Se NÃO tem o cookie, obriga a verificar o Recaptcha do POST
    if (!$is_human) {
        if(empty($_POST['g-recaptcha-response'])) {
            echo json_encode(['status' => 'error', 'message' => 'Por favor, realize a verificação "Não sou um robô".']);
            exit;
        }
        // Se passou, cria o Cookie para durar 1 ano (365 dias)
        setcookie("auth_human", "true", time() + (86400 * 365), "/"); 
        $is_human = true;
    }

    // 2. TRATAMENTO DOS DADOS
    // trim() remove espaços antes e depois, resolvendo o problema de usuários com espaço no banco
    $login = trim(filter_input(INPUT_POST, 'login', FILTER_DEFAULT));
    $senha = sha1(filter_input(INPUT_POST, 'password', FILTER_DEFAULT));

    // 3. BUSCA USUÁRIO
    // Usamos TRIM no banco também para garantir que ache mesmo se tiver espaço salvo errado lá
    $query = $connect->prepare("SELECT * FROM carteira WHERE trim(login) = :login LIMIT 1");
    $query->execute([':login' => $login]);
    $user = $query->fetch(PDO::FETCH_OBJ);

    if ($user) {
        // 4. VERIFICA SENHA PRIMEIRO
        if ($user->senha === $senha) {
            
            // SENHA CORRETA! AGORA VERIFICAMOS SE PODE ENTRAR.
            $contaSuspensa = false;

            // REGRA: Admin (ID 1) NUNCA é suspenso
            if ($user->Id != 1) {
                
                // A) Verifica Status (0 = Suspenso, 1 = Ativo)
                if ($user->status != 1) {
                    $contaSuspensa = true;
                }

                // B) Verifica Data de Assinatura
                if (!$contaSuspensa && !empty($user->assinatura)) {
                    $dataAssinatura = DateTime::createFromFormat('d/m/Y', $user->assinatura);
                    $dataHoje = new DateTime();
                    $dataHoje->setTime(0, 0, 0);

                    if ($dataAssinatura) {
                        $dataAssinatura->setTime(0, 0, 0);
                        if ($dataAssinatura < $dataHoje) {
                            $contaSuspensa = true;
                        }
                    }
                }
            }

            // DECISÃO FINAL
            if ($contaSuspensa) {
                $response = ['status' => 'suspended']; // Manda abrir modal de suspenso
            } else {
                // SUCESSO TOTAL
                $_SESSION['cod_id'] = $user->Id;
                
                // Cookie de "Lembrar login" (Preenche o campo usuário)
                if(isset($_POST['lembrar']) && $_POST['lembrar'] == 1){
                    setcookie("login_user", $login, time() + (86400 * 30), "/");
                }
                
                $response = ['status' => 'success', 'redirect' => 'master/'];
            }

        } else {
            // Senha não bate
            $response = ['status' => 'error', 'message' => 'Senha incorreta'];
        }
    } else {
        // Login não achado
        $response = ['status' => 'error', 'message' => 'Usuário não encontrado'];
    }

    echo json_encode($response);
    exit;
}

// --- VISUAL ---
$adm_id = "1";
$query = $connect->query("SELECT * FROM carteira WHERE Id = '$adm_id'");
$dados = $query->fetch(PDO::FETCH_OBJ);
$bgImage = ($dados && $dados->background) ? $dados->background : 'https://images.unsplash.com/photo-1497215728101-856f4ea42174?ixlib=rb-1.2.1&auto=format&fit=crop&w=1950&q=80'; 
$logoPath = 'master/img/logo.png'; 
$logoCache = file_exists($logoPath) ? '?v='.time() : '';
$saved_login = isset($_COOKIE['login_user']) ? $_COOKIE['login_user'] : '';

// Verifica se o cookie de "Humano" já existe para esconder o captcha
$ja_verificado = isset($_COOKIE['auth_human']);

createTablesAndAddColumnIfNotExist($connect);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Acesso Restrito</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.css">
  <link rel="stylesheet" href="styles/modern-theme.css">

  <style>
    :root {
      --login-surface: rgba(255, 255, 255, 0.9);
      --login-border: rgba(255, 255, 255, 0.35);
      --login-shadow: rgba(0, 0, 0, 0.35);
      --login-text: #0f172a;
      --login-bg-1: rgba(10, 10, 20, 0.82);
      --login-bg-2: rgba(10, 10, 20, 0.94);
      --login-bg-dark-1: rgba(10, 12, 18, 0.9);
      --login-bg-dark-2: rgba(10, 12, 18, 0.98);
    }
    body.dark-mode {
      --login-surface: rgba(28, 31, 42, 0.9);
      --login-border: rgba(255, 255, 255, 0.08);
      --login-shadow: rgba(0, 0, 0, 0.65);
      --login-text: #e5e7eb;
    }

    body {
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
      background: linear-gradient(120deg, var(--login-bg-1), var(--login-bg-2)), url('<?php echo htmlspecialchars($bgImage, ENT_QUOTES, 'UTF-8'); ?>');
      background-size: cover;
      background-position: center;
    }
    body.dark-mode {
      background: linear-gradient(120deg, var(--login-bg-dark-1), var(--login-bg-dark-2)), url('<?php echo htmlspecialchars($bgImage, ENT_QUOTES, 'UTF-8'); ?>');
      background-size: cover;
      background-position: center;
      color: var(--login-text);
    }

    .login-card {
      background: var(--login-surface);
      width: 100%;
      max-width: 400px;
      padding: 40px;
      border-radius: 15px;
      border: 1px solid var(--login-border);
      box-shadow: 0 15px 35px var(--login-shadow);
      position: relative;
      backdrop-filter: blur(10px);
      overflow: hidden;
      color: var(--login-text);
    }
    .login-card::before {
      content: "";
      position: absolute;
      inset: -40% auto auto -40%;
      width: 120%;
      height: 60%;
      background: radial-gradient(circle at top left, rgba(255,255,255,0.3), transparent 60%);
      transform: rotate(12deg);
      opacity: 0.7;
    }
    .login-card::after {
      content: "";
      position: absolute;
      inset: auto -30% -55% auto;
      width: 130%;
      height: 70%;
      background: linear-gradient(135deg, rgba(255,255,255,0.18), transparent 65%);
      transform: rotate(-14deg);
    }
    .login-card > * { position: relative; z-index: 1; }

    .brand-logo { text-align: center; margin-bottom: 30px; }
    .brand-logo img { height: 100px; object-fit: contain; }

    .input-group-custom { position: relative; margin-bottom: 20px; }
    .input-group-custom .form-control {
        height: 50px; padding-left: 50px; border-radius: 8px;
        border: 1px solid #e3e6f0; background-color: #f8f9fc; font-size: 14px; color: var(--login-text);
    }
    .input-group-custom .form-control:focus {
        background-color: #fff; border-color: #4e73df; box-shadow: 0 0 0 3px rgba(78, 115, 223, 0.1);
    }
    body.dark-mode .input-group-custom .form-control {
        background-color: #1f2330;
        border-color: #2f3545;
        color: #e6e6e6;
    }
    body.dark-mode .input-group-custom .form-control:focus {
        background-color: #232839; border-color: #6f42c1; box-shadow: 0 0 0 3px rgba(111, 66, 193, 0.2);
    }
    .icon-holder {
        position: absolute; left: 0; top: 0; bottom: 0; width: 50px;
        display: flex; align-items: center; justify-content: center; z-index: 10; font-size: 18px;
    }
    .icon-user { color: #007bff; }
    .icon-pass { color: #6f42c1; }

    .btn-login {
      background: linear-gradient(to right, #2c3e50, #4ca1af); 
      border: none; height: 50px; border-radius: 8px; color: white; font-weight: 600;
      width: 100%; margin-top: 10px; transition: transform 0.2s;
    }
    .btn-login:hover:not(:disabled) { transform: translateY(-2px); color: white; }
    .btn-login:disabled { background: #ccc; cursor: not-allowed; }
    body.dark-mode .btn-login { background: linear-gradient(120deg, #6f42c1, #3b82f6); }

    .modal-content { border-radius: 15px; border: none; text-align: center; }
    .icon-modal { font-size: 40px; margin-bottom: 15px; }
    .text-danger-custom { color: #e74a3b; }
    .text-warning-custom { color: #f6c23e; }
  </style>
</head>

<body>

  <div class="theme-toggle" id="themeToggle" title="Alternar tema">
    <i class="fas fa-moon"></i>
  </div>

  <div class="login-card">
    <div class="brand-logo">
      <img src="<?php echo $logoPath . $logoCache; ?>" alt="Logo">
    </div>

    <h5 class="text-center font-weight-bold mb-4 text-dark">Bem-vindo</h5>

    <form id="loginForm">
      
      <div class="input-group-custom">
        <div class="icon-holder icon-user"><i class="fas fa-user"></i></div>
        <input type="text" class="form-control" id="login" name="login" placeholder="Usuário" value="<?php echo $saved_login; ?>" required>
      </div>

      <div class="input-group-custom">
        <div class="icon-holder icon-pass"><i class="fas fa-lock"></i></div>
        <input type="password" class="form-control" id="password" name="password" placeholder="Senha" required>
      </div>

      <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="custom-control custom-checkbox">
          <input type="checkbox" class="custom-control-input" id="lembrar" name="lembrar" <?php echo $saved_login ? 'checked' : ''; ?>>
          <label class="custom-control-label" for="lembrar">Lembrar-me</label>
        </div>
      </div>

      <!-- CAPTCHA: Se ja_verificado for true, esconde a div inteira -->
      <div class="text-center mb-3" id="captcha-container" style="<?php echo $ja_verificado ? 'display:none;' : ''; ?>">
         <div class="g-recaptcha" data-callback="recaptchaCallback" data-sitekey="<?php print $_captcha; ?>" style="display: inline-block;"></div>
      </div>

      <!-- Se ja_verificado, botão começa ativado. Senão, desativado -->
      <button type="submit" id="submit" class="btn btn-login" <?php echo $ja_verificado ? '' : 'disabled'; ?>>ENTRAR</button>
    </form>
  </div>

  <!-- MODAL: CONTA SUSPENSA/VENCIDA -->
  <div class="modal fade" id="modalVencido" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-4">
        <div class="icon-modal text-danger-custom"><i class="fas fa-ban"></i></div>
        <h4 class="font-weight-bold">Acesso Suspenso</h4>
        <p class="text-muted">Sua conta está vencida ou suspensa.<br>Entre em contato com o suporte.</p>
        <button type="button" class="btn btn-secondary rounded-pill mt-2" data-dismiss="modal">Fechar</button>
      </div>
    </div>
  </div>

  <!-- MODAL: DADOS INVÁLIDOS -->
  <div class="modal fade" id="modalErro" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content p-4">
        <div class="icon-modal text-warning-custom"><i class="fas fa-exclamation-triangle"></i></div>
        <h4 class="font-weight-bold">Acesso Negado</h4>
        <p class="text-muted">Usuário ou senha incorretos.<br>Verifique seus dados.</p>
        <button type="button" class="btn btn-primary rounded-pill mt-2" data-dismiss="modal">Tentar Novamente</button>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-toast-plugin/1.3.2/jquery.toast.min.js"></script>
  <script src="https://www.google.com/recaptcha/api.js" async defer></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var body = document.body;
      var toggle = document.getElementById('themeToggle');
      var icon = toggle ? toggle.querySelector('i') : null;

      function setTheme(mode) {
        if (mode === 'dark') {
          body.classList.add('dark-mode');
          if (icon) icon.className = 'fas fa-sun';
        } else {
          body.classList.remove('dark-mode');
          if (icon) icon.className = 'fas fa-moon';
        }
        localStorage.setItem('mode', mode);
      }

      function restoreTheme() {
        var saved = localStorage.getItem('mode');
        if (saved === 'dark') {
          setTheme('dark');
        } else {
          setTheme('light');
        }
      }

      restoreTheme();

      if (!toggle) {
        console.warn('Theme toggle element not found on page.');
        return;
      }

      toggle.addEventListener('click', function() {
        var next = body.classList.contains('dark-mode') ? 'light' : 'dark';
        setTheme(next);
      });
    });

    function recaptchaCallback() { $("#submit").prop("disabled", false); }

    $(document).ready(function () {
      $("#loginForm").submit(function (e) {
        e.preventDefault();

        var btn = $("#submit");
        var originalText = btn.html();
        btn.html('<i class="fas fa-spinner fa-spin"></i>').prop("disabled", true);
        
        var gResponse = grecaptcha && grecaptcha.getResponse ? grecaptcha.getResponse() : "";

        $.ajax({
          url: '',
          type: 'post',
          data: {
            login: $("#login").val(),
            password: $("#password").val(),
            lembrar: $("#lembrar").is(':checked') ? 1 : 0,
            'g-recaptcha-response': gResponse
          },
          success: function (response) {
            btn.html(originalText).prop("disabled", false);
            try {
              var data = JSON.parse(response);

              if (data.status == 'success') {
                 window.location.href = data.redirect;
              } 
              else if (data.status == 'suspended') {
                 $('#modalVencido').modal('show');
              } 
              else {
                 $('#modalErro').modal('show');
              }
            } catch (e) { 
                console.log(response);
                alert("Erro inesperado.");
            }
          },
          error: function () {
            btn.html(originalText).prop("disabled", false);
            alert("Erro de conexão");
          }
        });
      });
    });
  </script>
</body>
</html>
