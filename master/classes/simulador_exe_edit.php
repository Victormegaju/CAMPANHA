<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

ob_start();
session_start();

if (!isset($_SESSION['cod_id'])) {
    unset($_SESSION['cod_id']);
    header('location: ../');
}

$cod_id = $_SESSION['cod_id'];

require_once __DIR__ . '/../../db/Conexao.php';
require_once __DIR__ . '/../../master/classes/functions.php';

function sendCurlRequest($url, $token, $data)
{
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
    )
    );

    $response = curl_exec($curl);

    curl_close($curl);

    return $response;
}

$idcliente = isset($_POST['vercli']) ? $_POST['vercli'] : '';

$formapagamento = $_POST['formapagamento'];
$parcelas = $_POST['parcelas'];
$dataparcela = DateTime::createFromFormat('d/m/Y', $_POST['dataparcela']);
$dataparcelax = $_POST['dataparcelax'];
$idpedido = $_POST['idpedido'];
$vparcela = $_POST['vparcela'];

$deleteOldMercadoPago = $connect->prepare("DELETE FROM mercadopago WHERE idc = ? AND instancia < ?");
$deleteOldMercadoPago->execute([$idcliente, $idpedido]);

$deleteOldEntries = $connect->prepare("DELETE FROM financeiro2 WHERE idc = ?");
$deleteOldEntries->execute([$idcliente]);

$dataparcela_string_financeiro1 = $dataparcela->format('Y-m-d');

$insertedParcelIds = array();

for ($i = 1; $i <= $parcelas; $i++) {
    $dataparcela_string = $dataparcela->format('d/m/Y');
    $insertFinanceiro2 = $connect->prepare("INSERT INTO financeiro2 (idc, chave, idm, datapagamento, parcela) VALUES (?, ?, ?, ?, ?)");
    $insertFinanceiro2->execute([$idcliente, $idpedido, $cod_id, $dataparcela_string, $vparcela]);

    /*Obter o último ID inserido na tabela financeiro2 e armazená-lo no array*/
    $insertedParcelIds[] = $connect->lastInsertId();

    $dataparcela->modify('+1 month');
}

$updateFinanceiro1 = $connect->prepare("UPDATE financeiro1 SET chave = ?, parcelas = ?, valorfinal = ?, primeiraparcela = ? WHERE idc = ? AND status = ?");
$updateFinanceiro1->execute([$idpedido, $parcelas, $formapagamento, $dataparcela_string_financeiro1, $idcliente, 1]);

$vencimento_primeira_parcela = DateTime::createFromFormat('d/m/Y', $_POST['dataparcela']);

for ($parcelaIndex = 0; $parcelaIndex < $parcelas; $parcelaIndex++) {
    $dataParcela = clone $vencimento_primeira_parcela;
    $dataParcela->add(new DateInterval('P' . $parcelaIndex . 'M'));
    if ($dataParcela->format('d') != $vencimento_primeira_parcela->format('d')) {
        $dataParcela->modify('last day of last month');
    }
    $qwerr = $dataParcela->format('d/m/Y');

    $getMaster = $connect->query("SELECT * FROM carteira WHERE Id = '" . $cod_id . "'");
    $masterInfo = $getMaster->fetch(PDO::FETCH_OBJ);

    $tokenmp = $masterInfo->tokenmp;

    $getClient = $connect->query("SELECT Id, nome, celular, email FROM clientes WHERE id = '" . $idcliente . "'");
    $clientInfo = $getClient->fetch(PDO::FETCH_OBJ);

    $nameParts = explode(" ", $clientInfo->nome);
    $firstName = $nameParts[0];
    $lastName = end($nameParts);
    $phone = $clientInfo->celular;
    $email = $clientInfo->email;
    $clientId = $clientInfo->Id;

    $amount = $vparcela;
    $cobId = $insertedParcelIds[$parcelaIndex]; /*Usando o ID da parcela correspondente*/
    $paymentDate = $qwerr;

    $data = '{
        "transaction_amount": ' . $amount . ',
        "description": "PAGAMENTO DE MENSALIDADE ' . $firstName . '",
        "payment_method_id": "pix",
        "payer": {
          "email": "' . $email . '",
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
?>