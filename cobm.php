<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('America/Sao_Paulo');

require_once __DIR__ . '/db/Conexao.php';
require_once __DIR__ . '/master/classes/functions.php';
$responsee = [];
header('Content-Type: application/json');
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

function sendTextMessageWhats($urlapi, $tokenapi, $phone, $textomsg, $apikey) {
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $urlapi . "/message/sendText/AbC123" . $tokenapi,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
      "number": "55' . $phone . '",
      "options": {
        "delay": 1200,
        "presence": "composing",
        "linkPreview": false
      },
      "textMessage": {
        "text": "' . $textomsg . '"
      }
    }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'apikey: ' . $apikey . ''
    )
  ));

  curl_exec($curl);

  curl_close($curl);
}

function sendMediaMessageWhats($urlapi, $tokenapi, $phone, $caption, $base64, $apikey) {
  $curl = curl_init();

  curl_setopt_array($curl, array(
    CURLOPT_URL => $urlapi . "/message/sendMedia/AbC123" . $tokenapi,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
      "number": "55' . $phone . '",
      "options": {
        "delay": 1200,
        "presence": "composing"
      },
      "mediaMessage": {
        "mediatype": "image",
        "caption": "' . $caption . '",
        "media": "' . $base64 . '"
      }
    }',
    CURLOPT_HTTPHEADER => array(
      'Content-Type: application/json',
      'apikey: ' . $apikey . ''
    )
  ));

  curl_exec($curl);

  curl_close($curl);
}

if (isset($_POST["cob"], $_POST["codclix"], $_POST["tipom"], $_POST["dcob"])) {
  $idfinan2 = $_POST["cob"];
  $idcliente = $_POST["codclix"];
  $tipocob = $_POST["tipom"];
  $dcob = $_POST["dcob"];

  $dataAtual = date("d/m/Y");

  $stmt = $connect->prepare("SELECT * FROM financeiro2 WHERE Id = :idfinan2 AND idc = :idcliente");
  $stmt->execute(array(':idfinan2' => $idfinan2, ':idcliente' => $idcliente));
  $row = $stmt->fetch(PDO::FETCH_OBJ);

  $getMaster = $connect->query("SELECT * FROM carteira WHERE Id = '" . $row->idm . "'");
  $masterInfo = $getMaster->fetch(PDO::FETCH_OBJ);

  $tokenapi = $masterInfo->tokenapi;
  $tokenmp = $masterInfo->tokenmp;
  $company = $masterInfo->nomecom;
  $cnpj = $masterInfo->cnpj;
  $address = $masterInfo->enderecom;

  $getClient = $connect->query("SELECT Id, nome, celular, email FROM clientes WHERE id = '" . $row->idc . "'");
  $clientInfo = $getClient->fetch(PDO::FETCH_OBJ);

  $nameParts = explode(" ", $clientInfo->nome);
  $firstName = $nameParts[0];
  $lastName = end($nameParts);
  $phone = $clientInfo->celular;
  $email = $clientInfo->email;
  $clientId = $clientInfo->Id;

  $amount = $row->parcela;
  $cobId = $row->Id;
  $paymentDate = $row->datapagamento;

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
    $checkMercadoPago = $connect->prepare("SELECT * FROM mercadopago WHERE idc = :idcliente AND instancia = :instancia");
    $checkMercadoPago->execute([':idcliente' => $idcliente, ':instancia' => $row->Id]);
    $checkMercadoPagoRow = $checkMercadoPago->fetch(PDO::FETCH_OBJ);

    if ($checkMercadoPagoRow) {
      $connect->query("UPDATE mercadopago SET status = '" . $status . "', data = '" . $createdDate . "', valor = '" . $totalPaid . "', idp = '" . $transactionId . "', qrcode = '" . $qrcodeBase64 . "', linhad = '" . $codePix . "' WHERE idc = '" . $idcliente . "' AND instancia = '" . $row->Id . "'");

      $idCobranca = $checkMercadoPagoRow->id;
    } else {
      $add = $connect->prepare("INSERT INTO mercadopago (idc, status, instancia, data, valor, idp, qrcode, linhad) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $add->execute([$clientId, $status, $cobId, $createdDate, $totalPaid, $transactionId, $qrcodeBase64, $codePix]);
      $idCobranca = $connect->lastInsertId();
    }
  }

  $linkcob = "/pagamento/?idCob=" . $idCobranca . "&idInst=". $cobId . "&idFin=" . $cobId;

// Inserir mensagens na tabela message_queue
$messages = $connect->query("SELECT msg FROM mensagens WHERE tipo = '" . $tipocob . "' AND idu = '" . $row->idm . "'");
$messagesRow = $messages->fetch(PDO::FETCH_OBJ);


$search = array('#NOME#', '#VENCIMENTO#', '#VALOR#', '#LINK#', '#EMPRESA#', '#CNPJ#', '#ENDERECO#', '#CONTATO#', '#PIX#');
$replace = array($firstName, $paymentDate, $amount, $linkcob, $company, $cnpj, $address, $phone, $codePix);
$message = str_replace($search, $replace, $messagesRow->msg);


$messageToSend = str_replace("\n", "\\n", $message);

$insertMessage = $connect->prepare("INSERT INTO message_queue (type, phone, message, media, status) VALUES (?, ?, ?, ?, ?)");
$insertMessage->execute(['text', $phone, $messageToSend, '', 'pending']);

// Decodificar a imagem do QRCode
$qrcodeImage = base64_decode($qrcodeBase64);

// Converter a imagem do QRCode em base64 novamente
$qrcodeImageBase64 = base64_encode($qrcodeImage);

// Inserir a imagem do QRCode na fila de envio
$insertMediaMessage = $connect->prepare("INSERT INTO message_queue (type, phone, message, media, status) VALUES (?, ?, ?, ?, ?)");
$insertMediaMessage->execute(['media', $phone, "Pague agora via pix. Leia o QRCode.", $qrcodeImageBase64, 'pending']);

// Inserir mensagem de cópia da chave PIX na fila de envio
$copyPixMessage = $codePix;
$insertPixMessage = $connect->prepare("INSERT INTO message_queue (type, phone, message, media, status) VALUES (?, ?, ?, ?, ?)");
$insertPixMessage->execute(['text', $phone, $copyPixMessage, '', 'pending']);

// Inserir mensagem de aviso na fila de envio
$messageText = "*ATENÇÃO* Esta é uma mensagem automática e não precisa ser respondida.\\n*Caso já tenha efetuado o pagamento por favor desconsidere esta cobrança.*";
$insertWarningMessage = $connect->prepare("INSERT INTO message_queue (type, phone, message, media, status) VALUES (?, ?, ?, ?, ?)");
$insertWarningMessage->execute(['text', $phone, $messageText, '', 'pending']);

// Recuperar mensagens da fila de envio e enviar
$stmt = $connect->prepare("SELECT * FROM message_queue WHERE status = 'pending'");
$stmt->execute();

while ($row = $stmt->fetch(PDO::FETCH_OBJ)) {
    if ($row->type == 'text') {
        $result = sendTextMessageWhats($urlapi, $tokenapi, $row->phone, $row->message, $apikey);
    } elseif ($row->type == 'media') {
        $result = sendMediaMessageWhats($urlapi, $tokenapi, $row->phone, $row->message, $row->media, $apikey);
    }

    if ($result && $result->getStatusCode() == 200) {
        // Se o envio for bem-sucedido, atualize o status para 'sent'
        $update = $connect->prepare("UPDATE message_queue SET status = 'sent' WHERE id = ?");
        $update->execute([$row->id]);
    } else {
        // Se o envio falhar ou se o status da resposta não for 200, atualize o status para 'falha'
        $update = $connect->prepare("UPDATE message_queue SET status = 'falha' WHERE id = ?");
        $update->execute([$row->id]);
        $delete = $connect->prepare("DELETE FROM message_queue WHERE id = ?");
        $delete->execute([$row->id]);
    }

    // Aguarda 10 segundos antes de prosseguir para a próxima mensagem
    sleep(2);
}
header('Content-Type: application/json');
    $responsee = [
        'message' => 'ok',
        'success' => true
    ];
    echo json_encode($responsee);
    exit;


}
?>
