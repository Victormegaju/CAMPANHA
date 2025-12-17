<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

if((!isset ($_SESSION['cod_id']) == true)) { unset($_SESSION['cod_id']); header('location: ../'); exit; }

$cod_id = $_SESSION['cod_id'];

require_once __DIR__ . '/../../db/Conexao.php';
require_once __DIR__ . '/functions.php';

if (isset($_POST["token_api"])) {
    $tokenid = $_POST["token_api"];
    $celular = "55" . preg_replace("/[^0-9]/", "", $_POST["celular"]);
    
    // 1. DELETE OLD INSTANCE
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlapi . '/instance/delete/AbC123'. $tokenid,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
        CURLOPT_HTTPHEADER => array(
            'apikey: ' . $apikey
        ),
    ));
    $response_delete = curl_exec($curl);
    curl_close($curl);
    
    sleep(2);
    
    // Generate new token
    $bytes = random_bytes(16);
    $new_tokenid = bin2hex($bytes);
    
    $editarcad = $connect->query("UPDATE carteira SET tokenapi='" . $new_tokenid . "' WHERE Id = '" . $cod_id . "'");
    
    // 2. CREATE NEW INSTANCE
    $curl = curl_init();
    $postData = json_encode([
        "instanceName" => "AbC123" . $new_tokenid,
        "token" => $new_tokenid,
        "qrcode" => true,
        "number" => $celular,
        "integration" => "WHATSAPP-BAILEYS" // Explicitly setting integration
    ]);

    curl_setopt_array($curl, array(
        CURLOPT_URL => $urlapi .'/instance/create',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'apikey: ' . $apikey
        )
    ));
    
    $response = curl_exec($curl);
    curl_close($curl);
    
    $res = json_decode($response, true);
    
    // Verifica se temos um QR Code ou se o status é positivo
    $status = isset($res["instance"]["status"]) ? $res["instance"]["status"] : "";
    $qrcode = isset($res["qrcode"]["base64"]) ? $res["qrcode"]["base64"] : "";
    
    if (($status == "created" || $status == "connecting") && !empty($qrcode)) {
        
        $newApiKey = isset($res["hash"]["apikey"]) ? $res["hash"]["apikey"] : "";

        $stmt = $connect->prepare("UPDATE conexoes SET qrcode = :qrcode, apikey = :apikey WHERE id_usuario = :id_usuario");
        $stmt->bindParam(':qrcode', $qrcode);
        $stmt->bindParam(':apikey', $newApiKey);
        $stmt->bindParam(':id_usuario', $cod_id);
        $stmt->execute();
        
        header("location: ../qrcode");
        exit;
    } else {
        header("location: ../whatsapp?erro=ok");
        exit;
    }
}
?>