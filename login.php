<?php
session_start();

require_once __DIR__ . '/db/Conexao.php';

if (isset($_POST["login"])) {
    $login = filter_input(INPUT_POST, 'login', FILTER_DEFAULT);
    $password = sha1($_POST['password']);

    // 1. Buscamos o usuário APENAS por login e senha. 
    // Removemos o 'AND status = 1' daqui para podermos identificar quem está suspenso.
    $stmt = $connect->prepare("SELECT * FROM carteira WHERE login = :login AND senha = :password LIMIT 1");
    $stmt->execute(['login' => $login, 'password' => $password]);
    $user = $stmt->fetch(PDO::FETCH_OBJ);

    if ($user) {
        // O Login e Senha estão corretos! Agora vamos verificar a situação da conta.

        // VERIFICAÇÃO 1: Status Manual (Coluna 'status')
        // Se status for diferente de 1, consideramos suspenso
        if ($user->status != 1) {
            echo json_encode(['status' => 'suspended', 'message' => 'Sua conta está suspensa.']);
            exit;
        }

        // VERIFICAÇÃO 2: Data de Vencimento (Coluna 'assinatura')
        // Converte a data do formato brasileiro (d/m/Y) para comparação
        if (!empty($user->assinatura)) {
            $dataValidade = DateTime::createFromFormat('d/m/Y', $user->assinatura);
            $hoje = new DateTime();
            
            // Zera as horas para comparar apenas os dias (evita erro por hora)
            $hoje->setTime(0, 0, 0);
            if ($dataValidade) $dataValidade->setTime(0, 0, 0);

            if ($dataValidade && $dataValidade < $hoje) {
                echo json_encode(['status' => 'suspended', 'message' => 'Sua assinatura venceu.']);
                exit;
            }
        }

        // SE PASSOU POR TUDO: LOGIN SUCESSO
        $_SESSION["cod_id"] = $user->Id;
        
        // Redirecionamento baseado no tipo de usuário (opcional, mantive sua lógica)
        $redirect = 'master/'; // Padrão
        
        echo json_encode([
            'status' => 'success', 
            'message' => 'Login efetuado com sucesso', 
            'redirect' => $redirect
        ]);

    } else {
        // Se não achou usuário com essa senha
        echo json_encode(['status' => 'error', 'message' => 'Usuário ou Senha incorretos.']);
    }
}
?>