<?php
require_once "topo.php";

// Garante que variáveis globais de functions.php estejam acessíveis se necessário
// Se o seu topo.php já chama o functions.php, essa linha não é necessária, mas não faz mal.
require_once "classes/functions.php"; 

$idins = $dadosgerais->tokenapi;

// LÓGICA DE VERIFICAÇÃO DE CONEXÃO
$stmt = $connect->query("SELECT id FROM conexoes WHERE id_usuario = '" . $cod_id . "' AND conn = '1'");
$rowCount = $stmt->rowCount();

if($rowCount > 0) {
    echo "<meta http-equiv=\"refresh\" content=\"0;URL=./\">";
    exit;
} else {
    $connections = $connect->query("SELECT apikey FROM conexoes WHERE id_usuario = '" . $cod_id . "'");
    $connectionsRow = $connections->fetch(PDO::FETCH_OBJ);

    // Se tiver apikey salva, tenta checar o status
    if ($connectionsRow && $connectionsRow->apikey) {
        $curl = curl_init();
        
        // Usando a chave da instância ou a global (conforme sua lógica original)
        $key_to_use = $connectionsRow->apikey; 

        curl_setopt_array($curl, array(
            CURLOPT_URL => $urlapi.'/instance/connectionState/AbC123'. $idins,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 5, // Timeout curto para não travar a página
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'apikey: '. $key_to_use
            ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $res = json_decode($response, true);

        // Tratamento para variações da API (v1/v2)
        $conexaoo = "";
        if(isset($res['instance']['state'])) {
            $conexaoo = $res['instance']['state'];
        } elseif (isset($res['state'])) {
            $conexaoo = $res['state'];
        }

        if($conexaoo == 'open') {
            $connect->query("UPDATE conexoes SET conn = '1' WHERE id_usuario = '" . $cod_id . "'");
            echo "<meta http-equiv=\"refresh\" content=\"0;URL=./whatsapp\">";
            exit;
        }
    }
}
?>

<!-- ESTILOS CSS PERSONALIZADOS -->
<style>
    .wa-card {
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        overflow: hidden;
        border: none;
    }
    .qr-section {
        background: #f0f2f5;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        min-height: 400px;
        position: relative;
    }
    .qr-frame {
        background: white;
        padding: 15px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
    }
    .instructions-section {
        padding: 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }
    .step-list {
        list-style: none;
        padding: 0;
        margin-top: 20px;
    }
    .step-list li {
        margin-bottom: 20px;
        font-size: 16px;
        color: #3b4a54;
        display: flex;
        align-items: flex-start;
    }
    .step-num {
        font-weight: bold;
        color: #00a884;
        margin-right: 15px;
        font-size: 18px;
    }
    .reload-bar-container {
        height: 4px;
        width: 100%;
        background: #e9edef;
        position: absolute;
        top: 0;
        left: 0;
    }
    .reload-bar {
        height: 100%;
        background: #00a884;
        width: 100%;
        transition: width 1s linear;
    }
    .btn-reset {
        background-color: #3b4a54;
        color: white;
        border-radius: 20px;
        padding: 10px 25px;
        transition: all 0.3s;
    }
    .btn-reset:hover {
        background-color: #2a353c;
        transform: translateY(-2px);
    }
</style>

<div class="slim-mainpanel">
    <div class="container">
        
        <?php if(isset($_GET["sucesso"])) { ?>
            <div class="alert alert-solid alert-success mg-b-20" role="alert">
                <strong><i class="fa fa-check"></i> Sucesso!</strong> Operação realizada.
            </div>
            <meta http-equiv="refresh" content="1;URL=./usuarios" />
        <?php } ?>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card wa-card">
                    
                    <!-- Barra de progresso do refresh -->
                    <div class="reload-bar-container">
                        <div class="reload-bar" id="progressBar"></div>
                    </div>

                    <div class="row no-gutters">
                        <!-- COLUNA DO QR CODE (ESQUERDA) -->
                        <div class="col-md-5 qr-section">
                            <div class="qr-frame">
                                <?php
                                $stm = $connect->query("SELECT qrcode, conn FROM conexoes WHERE id_usuario = '". $cod_id ."'");
                                $rowCount = $stm->rowCount();
                                
                                if($rowCount > 0) {
                                    $row = $stm->fetch();
                                    
                                    if($row["qrcode"] == "") {
                                        // Loading state
                                        echo '<div class="text-center pd-y-50">';
                                        echo '<div class="spinner-border text-success" role="status"></div>';
                                        echo '<p class="mg-t-15 tx-gray-600">Gerando QR Code...</p>';
                                        echo '</div>';
                                    } else {
                                        // QR Code Image
                                        echo '<img src="'.$row["qrcode"].'" style="width: 240px; height: 240px; object-fit: contain;">';
                                    }
                                } else {
                                    echo '<div class="text-center text-success"><i class="fa fa-check-circle fa-3x"></i><br>Conectado!</div>';
                                    echo '<meta http-equiv="refresh" content="1;URL=./">';
                                }
                                ?>
                            </div>
                        </div>

                        <!-- COLUNA DE INSTRUÇÕES (DIREITA) -->
                        <div class="col-md-7 instructions-section">
                            <h3 class="tx-dark tx-normal mg-b-5">Conectar WhatsApp</h3>
                            <p class="tx-gray-500">Escaneie o QR Code para conectar seu sistema.</p>
                            
                            <ul class="step-list">
                                <li>
                                    <span class="step-num">1.</span>
                                    <span>Abra o WhatsApp no seu celular.</span>
                                </li>
                                <li>
                                    <span class="step-num">2.</span>
                                    <span>Toque em <strong>Mais opções</strong> <i class="fa fa-ellipsis-v"></i> ou <strong>Configurações</strong> <i class="fa fa-gear"></i> e selecione <strong>Aparelhos Conectados</strong>.</span>
                                </li>
                                <li>
                                    <span class="step-num">3.</span>
                                    <span>Toque em <strong>Conectar Aparelho</strong>.</span>
                                </li>
                                <li>
                                    <span class="step-num">4.</span>
                                    <span>Aponte seu celular para esta tela para capturar o código.</span>
                                </li>
                            </ul>

                            <hr class="mg-y-20">

                            <div class="d-flex justify-content-between align-items-center">
                                <small class="tx-gray-500">
                                    <i class="fa fa-refresh"></i> Atualizando em <span id="contador" class="tx-bold tx-dark">20</span>s
                                </small>

                                <form action="classes/gera_qr.php" method="post" style="margin: 0;">
                                    <input type="hidden" name="token_api" value="<?php print $dadosgerais->tokenapi;?>">
                                    <input type="hidden" name="celular" value="<?php print $dadosgerais->celular; ?>">
                                    <button type="submit" class="btn btn-reset btn-sm" name="cart">
                                        <i class="fa fa-qrcode"></i> Gerar Novo QR Code
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="../lib/jquery/js/jquery.js"></script>
<script>
    // Configuração do Tempo (20 segundos)
    var tempoTotal = 20; 
    var tempoRestante = tempoTotal;
    
    var divContador = document.getElementById("contador");
    var barraProgresso = document.getElementById("progressBar");

    var intervalo = setInterval(function() {
        tempoRestante--;
        
        // Atualiza número
        divContador.innerHTML = tempoRestante;
        
        // Atualiza Barra de Progresso
        var porcentagem = (tempoRestante / tempoTotal) * 100;
        barraProgresso.style.width = porcentagem + "%";

        // Cores da barra baseada no tempo
        if(tempoRestante < 5) {
            barraProgresso.style.backgroundColor = "#dc3545"; // Vermelho no final
        }

        if (tempoRestante <= 0) {
            clearInterval(intervalo);
            location.reload();
        }
    }, 1000);
</script>

<script src="../js/slim.js"></script>
</body>
</html>