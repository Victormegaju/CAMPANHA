<?php
require_once "db/Conexao.php";
require_once __DIR__ . '/master/classes/functions.php';
$responsee = [];
header('Content-Type: application/json');
$idfinan2 = $_POST["cob"];
$idcliente = $_POST["codclix"];
$tipocob = $_POST["tipom"];
$dcob = $_POST["dcob"];


// Consulta SQL para buscar registros
$sql = "SELECT * FROM financeiro2 WHERE Id = '" . $idfinan2 . "' AND idc = '" . $idcliente . "'";
$buscafin2 = $connect->query($sql);
$empativosx = $buscafin2->rowCount();

$val = $buscafin2->fetch(PDO::FETCH_OBJ);

// PEGA DADOS DO MASTER
$pegamaster = $connect->query("SELECT * FROM carteira WHERE Id='" . $val->idm . "'");
$pegamastern = $pegamaster->fetch(PDO::FETCH_OBJ);


//$urlapi = $pegamastern->valor;
$tokenapi = $pegamastern->tokenapi;
$token = $pegamastern->vjurus;
$tokenmp = $pegamastern->tokenmp;
$empnome = $pegamastern->nomecom;
$empcnpj = $pegamastern->cnpj;
$empende = $pegamastern->enderecom;
$empcomt = $pegamastern->contato;
$msg1 = $pegamastern->msg;
$msg2 = $pegamastern->msgqr;
$msg3 = $pegamastern->msgpix;

// FIM PEGA DADOS DO MASTER

// FIM PEGA DADOS DO CLIENTE

$buscacli = $connect->query("SELECT Id, nome, celular FROM clientes WHERE id='" . $val->idc . "'");
$buscacli = $buscacli->fetch(PDO::FETCH_OBJ);

$partesNome = explode(" ", $buscacli->nome);
$primeiroNome = $partesNome[0];
$ultimoNome = end($partesNome);
$celular = $buscacli->celular;
$idcli = $buscacli->Id;



// FIM PEGA DADOS DO CLIENTE

// DADOS DA COBRANCA

$parcela = $val->parcela;
$idcob = $val->Id;
$data2x = $val->datapagamento;
$data3x = $val->pagoem;

// FIM DADOS DA COBRANCA


// MENSAGEM DE COBRANCA

$linkcob = $_urlmaster . "/pagamento/?cob=" . $idcob;

$stmt = $connect->prepare("SELECT msg FROM mensagens WHERE tipo = '5' AND idu = :idm");
$stmt->execute(['idm' => $val->idm]);

$buscamsg = $stmt->fetch(PDO::FETCH_OBJ);

$search = array('#NOME#', '#VENCIMENTO#', '#DATAPAGAMENTO#', '#VALOR#', '#LINK#', '#EMPRESA#', '#CNPJ#', '#ENDERECO#', '#CONTATO#');
$replace = array($primeiroNome . " " . $ultimoNome, $data2x, $data3x, $parcela, $linkcob, $empnome, $empcnpj, $empende, $empcomt);
$textomsg = str_replace($search, $replace, $buscamsg->msg);

$textomsg = str_replace("\n", "\\n", $textomsg);


sendTextMessageWhats($urlapi, $tokenapi, $celular, $textomsg, $apikey);






// FIM MENSAGEM DE COBRANCA

// MSG1
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
function sendTextMessageWhats($urlapi, $tokenapi, $celular, $textomsg, $apikey) {



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
        "number": "55' . $celular . '",
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

// FIM MSG1

header('Content-Type: application/json');
    $responsee = [
        'message' => 'ok',
        'success' => true
    ];
    echo json_encode($responsee);
    exit;

?>