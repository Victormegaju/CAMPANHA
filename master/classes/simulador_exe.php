<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
session_start();

if((!isset ($_SESSION['cod_id']) == true)) {
  unset($_SESSION['cod_id']);
  header('location: ../');
}

$cod_id = $_SESSION['cod_id'];

require_once __DIR__ . '/../../db/Conexao.php';
require_once __DIR__ . '/../../master/classes/functions.php';

function sendCurlRequest($url, $token, $data) {
  $curl = curl_init();

  $idempotencyKey = uniqid();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'Authorization: Bearer ' . $token,
      'X-Idempotency-Key: ' . $idempotencyKey,
    ),
  ));

  $response = curl_exec($curl);

  curl_close($curl);

  return $response;
}

$idcliente = $_POST['idcliente'];
$formapagamento = $_POST['formapagamento'];
$parcelas = $_POST['parcelas'];
$dataparcela = $_POST['dataparcela'];
$dataparcelax = $_POST['dataparcelax'];
$idpedido = $_POST['idpedido'];
$vparcela = $_POST['vparcela'];

$financeiro1 = $connect->query("INSERT INTO financeiro1 (idc, idm, valorfinal, formapagamento, parcelas, primeiraparcela, chave, vparcela, entrada) VALUES ('$idcliente','$cod_id','$vparcela','$formapagamento','$parcelas','$dataparcelax','$idpedido','$vparcela','".date("d/m/Y")."')");

$vencimento_primeira_parcela = DateTime::createFromFormat('d/m/Y', $dataparcela);

for ($parcela = 1; $parcela <= $parcelas; $parcela++) {
  $dataParcela = clone $vencimento_primeira_parcela;

  $dataParcela->add(new DateInterval('P' . ($parcela - 1) . 'M'));

  if ($dataParcela->format('d') != $vencimento_primeira_parcela->format('d')) {
    $dataParcela->modify('last day of last month');
  }

  $qwerr = $dataParcela->format('d/m/Y');

  $financeiro1 = $connect->prepare("INSERT INTO financeiro2 (idc, chave, idm, parcela, datapagamento) VALUES (?, ?, ?, ?, ?)");

  $financeiro1->execute([$idcliente, $idpedido, $cod_id, $vparcela, $qwerr]);

  $idCadastrado = $connect->lastInsertId();

  $getMaster = $connect->query("SELECT * FROM carteira WHERE Id = '" . $cod_id . "'");
  $masterInfo = $getMaster->fetch(PDO::FETCH_OBJ);

  $tokenapi = $masterInfo->tokenapi;
  $tokenmp = $masterInfo->tokenmp;
  $company = $masterInfo->nomecom;
  $cnpj = $masterInfo->cnpj;
  $address = $masterInfo->enderecom;

  $getClient = $connect->query("SELECT Id, nome, celular, email FROM clientes WHERE id = '" . $idcliente . "'");
  $clientInfo = $getClient->fetch(PDO::FETCH_OBJ);

  $nameParts = explode(" ", $clientInfo->nome);
  $firstName = $nameParts[0];
  $lastName = end($nameParts);
  $phone = $clientInfo->celular;
  $email = $clientInfo->email;
  $clientId = $clientInfo->Id;

  $amount = $vparcela;
  $cobId = $idCadastrado;
  $paymentDate = $qwerr;

  $data = '{
    "transaction_amount": ' . $amount . ',
    "description": "PAGAMENTO DE MENSALIDADE ' . $firstName . '",
    "payment_method_id": "pix",
    "payer": {
      "email": "'. $email .'",
      "first_name": "' . $firstName . '",
      "last_name": "' . $lastName . '"
    }
  }';

  $response = sendCurlRequest('https://api.mercadopago.com/v1/payments', $tokenmp, $data);
  $response = json_decode($response, true);

  $transactionId = $response["id"];
  $createdDate = date("Y-m-d H:i:s");
  $status = $response["status"];
  $totalPaid = $response["transaction_details"]["total_paid_amount"];
  $codePix = $response["point_of_interaction"]["transaction_data"]["qr_code"];
  $qrcodeBase64 = $response["point_of_interaction"]["transaction_data"]["qr_code_base64"];

  if ($status == "pending") {
    $add = $connect->prepare("INSERT INTO mercadopago (idc, status, instancia, data, valor, idp, qrcode, linhad) VALUES (?, ?, ?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE status = VALUES(status), instancia = VALUES(instancia), data = VALUES(data), valor = VALUES(valor), qrcode = VALUES(qrcode), linhad = VALUES(linhad)");
    $add->execute([$clientId, $status, $cobId, $createdDate, $totalPaid, $transactionId, $qrcodeBase64, $codePix]);
  }
}

header("location: ../contas_receber&sucesso=ok");
exit;
