<?php
ob_start();
session_start();
if ((!isset ($_SESSION['cod_id']) == true)) {
  unset($_SESSION['cod_id']);
  header('location: ../');
}

$cod_id = $_SESSION['cod_id'];

require "../../db/Conexao.php";

// EDITAR FUNCIONARIO
if (isset ($_POST["edit_cli"])) {

  $senha = $_POST['senha'];
  $valor = $_POST['caixa'];

  if ($senha) {
    $senha = sha1($_POST['senha']);
    $editarcad = $connect->query("UPDATE carteira SET nome='" . $_POST["nome"] . "', celular='" . $_POST["celular"] . "', login='" . $_POST["email"] . "', senha='$senha' WHERE Id='" . $cod_id . "'");

  } else {
    $editarcad = $connect->query("UPDATE carteira SET nome='" . $_POST["nome"] . "', celular='" . $_POST["celular"] . "', login='" . $_POST["email"] . "' WHERE Id='" . $cod_id . "'");

  }

  if ($editarcad) {
    header("location: ../perfil&sucesso=");
    exit;
  }

}

// EDIT WHATS
if (isset ($_POST["edit_w"])) {

  $editarcad = $connect->query("UPDATE carteira SET valor='" . $_POST["caixa"] . "', vjurus='" . $_POST["jurus"] . "' WHERE Id='" . $cod_id . "'");

  if ($editarcad) {
    header("location: ../whatsapp&sucesso=");
    exit;
  }

}

// EDIT MP
if (isset ($_POST["edit_m"])) {

  $editarcad = $connect->query("UPDATE carteira SET tokenmp='" . $_POST["tokenmp"] . "', msgqr='" . $_POST["msgqr"] . "', msgpix='" . $_POST["msgpix"] . "' WHERE Id='" . $cod_id . "'");

  if ($editarcad) {
    header("location: ../mercadopago&sucesso=");
    exit;
  }

}

// EDITAR CONFIGURAÇÕES DA EMPRESA (Onde estava o erro)
if (isset ($_POST["edit_emp"])) {

  // Lógica de Upload do Logo
  if (isset ($_FILES['logoEmpresa']) && $_FILES['logoEmpresa']['error'] == 0) {
    $extensao = pathinfo($_FILES['logoEmpresa']['name'], PATHINFO_EXTENSION);
    if ($extensao === 'png') {
      // Definindo o caminho do arquivo
      $path = '../../master/img/logo.png'; // Caminho onde o logo será armazenado

      // Movendo o arquivo carregado para o diretório do logo, substituindo o existente
      if (move_uploaded_file($_FILES['logoEmpresa']['tmp_name'], $path)) {
        // Upload e substituição bem-sucedidos
      } else {
        // Erro ao mover o arquivo
        header("location: ../configuracoes&erro=upload");
        exit;
      }
    } else {
      // Formato de arquivo incorreto
      header("location: ../configuracoes&erro=formato");
      exit;
    }
  }

  // Tratamento de variáveis para evitar erros se o campo não existir no HTML
  $background = isset($_POST['background']) ? $_POST['background'] : '';
  
  // CORREÇÃO AQUI: Se juros não for enviado ou for vazio, define como 0
  $juros_diarios = (isset($_POST['juros_diarios']) && $_POST['juros_diarios'] != '') ? $_POST['juros_diarios'] : 0;
  
  // Previne erro se enderecom não existir
  $enderecom = isset($_POST['enderecom']) ? $_POST['enderecom'] : ''; 

  $editarcad = $connect->query("UPDATE carteira SET pagamentos='" . $_POST["tipopgmto"] . "', nomecom='" . $_POST["nomecom"] . "', cnpj='" . $_POST["cnpj"] . "', enderecom='$enderecom' , contato='" . $_POST["contato"] . "', background='$background', juros_diarios='$juros_diarios' WHERE Id='" . $cod_id . "'");

  if ($editarcad) {
    header("location: ../configuracoes&sucesso=ok");
    exit;
  }
}
?>