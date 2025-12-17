<?php
session_start();
require_once "../../db/Conexao.php";
require_once "functions.php"; // Pega URL da API

$cod_id = $_SESSION['cod_id'];
$inst = $connect->query("SELECT tokenapi FROM conexoes WHERE id_usuario='$cod_id' AND conn='1' LIMIT 1")->fetch(PDO::FETCH_OBJ);

if(!$inst) { echo "Nenhuma conexão ativa."; exit; }

$instanceName = "AbC123" . $inst->tokenapi;
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => $urlapi . '/chat/findChats/' . $instanceName,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER => array('apikey: ' . $apikey),
));
$resp = curl_exec($curl);
curl_close($curl);
$chats = json_decode($resp, true);

$count = 0;
if(is_array($chats)){
    foreach($chats as $c){
        if(strpos($c['id'], '@s.whatsapp.net') !== false){
            $num = explode('@', $c['id'])[0];
            $nom = $c['pushName'] ?? $num;
            $sql = "INSERT IGNORE INTO contatos_chats (id_usuario, numero, nome, ultima_interacao) VALUES ('$cod_id', '$num', '$nom', NOW())";
            $connect->query($sql);
            $count++;
        }
    }
}
echo "<div class='alert alert-success'>Importados: $count chats.</div>";
?>