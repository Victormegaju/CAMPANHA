<?php
ob_start();

session_start();

if((!isset ($_SESSION['cod_id']) == true)) { 
  unset($_SESSION['cod_id']); 
  header('location: ../'); 
  exit;
}

$cod_id = $_SESSION['cod_id'];

require_once __DIR__ . '/../../db/Conexao.php';

$stmt = null; // 

if(isset($_POST["edicli1"]))  {
  $stmt = $connect->prepare("UPDATE mensagens SET status = 0 WHERE id = :id AND idu = :cod_id");
  $stmt->execute(['id' => $_POST["edicli1"], 'cod_id' => $cod_id]);
} elseif(isset($_POST["edicli2"])) {
  $stmt = $connect->prepare("UPDATE mensagens SET status = 1 WHERE id = :id AND idu = :cod_id");
  $stmt->execute(['id' => $_POST["edicli2"], 'cod_id' => $cod_id]);

} elseif(isset($_POST["edit_cli"])) {
$msg = str_replace("\r\n", "\n", $_POST["msg"]);
// $msg = str_replace("\n", "\\n", $msg);
  $stmt = $connect->prepare("UPDATE mensagens SET hora = :hora, msg = :msg WHERE id = :id AND idu = :cod_id");
  $stmt->execute([
    'hora' => $_POST["hora"], 
    'msg' => $msg,
    'id' => $_POST["edit_cli"], 
    'cod_id' => $cod_id
  ]);
}

elseif(isset($_POST["cart"])) {
  
$msg = str_replace("\r\n", "\n", $_POST["msg"]);
  $stmt = $connect->prepare("INSERT INTO mensagens (tipo, hora, msg, idu) VALUES (:tipo, :hora, :msg, :cod_id)");
  $stmt->execute([
    'tipo' => $_POST["tipo"], 
    'hora' => $_POST["hora"], 
    'msg' => $msg, 
    'cod_id' => $cod_id
  ]);
}


if($stmt && $stmt->rowCount() > 0) {
  header("location: ../mensagens&sucesso=ok"); 
  exit;
} else {
  header("location: ../mensagens&erro=ok"); 
  exit;
}