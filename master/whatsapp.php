<?php  
require_once "topo.php";

// Consultas mantidas da lógica original
$editarcat = $connect->query("SELECT * FROM carteira WHERE Id='$cod_id'");
$dadoscat = $editarcat->fetch(PDO::FETCH_OBJ);

$statuscon = $connect->query("SELECT * FROM conexoes WHERE id_usuario = '$cod_id'");
$dadoscon = $statuscon->fetch(PDO::FETCH_OBJ);
?>

<!-- Estilos Personalizados para esta página -->
<style>
    .whatsapp-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        overflow: hidden;
        background: #fff;
        max-width: 500px;
        margin: 0 auto;
    }
    .wa-header {
        background: #00a884; /* Verde WhatsApp */
        color: white;
        padding: 30px 20px;
        text-align: center;
    }
    .wa-body {
        padding: 40px 30px;
        text-align: center;
    }
    .status-icon-box {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 25px auto;
        font-size: 40px;
    }
    .status-disconnected {
        background-color: #f0f2f5;
        color: #b1b3b5;
        border: 2px dashed #b1b3b5;
    }
    .status-connected {
        background-color: #e6fffa;
        color: #00a884;
        border: 2px solid #00a884;
        position: relative;
    }
    /* Animação de Pulso para status conectado */
    .pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 3px solid #00a884;
        animation: pulse 2s infinite;
        opacity: 0;
    }
    @keyframes pulse {
        0% { transform: scale(1); opacity: 0.8; }
        100% { transform: scale(1.5); opacity: 0; }
    }
    
    .btn-wa-primary {
        background-color: #00a884;
        color: white;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 168, 132, 0.3);
        transition: all 0.3s;
        width: 100%;
    }
    .btn-wa-primary:hover {
        background-color: #008f6f;
        transform: translateY(-2px);
    }
    
    .btn-wa-danger {
        background-color: #ff3b30;
        color: white;
        border-radius: 50px;
        padding: 12px 30px;
        font-weight: 600;
        border: none;
        box-shadow: 0 4px 15px rgba(255, 59, 48, 0.3);
        transition: all 0.3s;
        width: 100%;
    }
    .btn-wa-danger:hover {
        background-color: #d63026;
        transform: translateY(-2px);
    }

    .wa-title { font-size: 1.5rem; font-weight: 700; color: #111b21; margin-bottom: 10px; }
    .wa-subtitle { color: #54656f; font-size: 0.95rem; margin-bottom: 30px; line-height: 1.5; }
    .btn-back-custom { color: #54656f; font-weight: 600; text-decoration: none; display: inline-block; margin-bottom: 20px; }
    .btn-back-custom:hover { color: #111b21; }
</style>

<div class="slim-mainpanel">
    <div class="container">
        
        <div class="row">
            <div class="col-md-12">
                <a href="./" class="btn-back-custom"><i class="fa fa-arrow-left"></i> Voltar ao Painel</a>
            </div>
        </div>

        <!-- Mensagens de Alerta -->
        <?php if(isset($_GET["sucesso"])){ ?>
            <div class="alert alert-success d-flex align-items-center" role="alert" style="max-width: 500px; margin: 0 auto 20px auto; border-radius: 10px;">
                <i class="fa fa-check-circle mg-r-10"></i> <strong>Sucesso!</strong> Operação realizada.
            </div>
            <meta http-equiv="refresh" content="2;URL=./whatsapp" />
        <?php } ?>
        
        <?php if(isset($_GET["erro"])){ ?>
            <div class="alert alert-danger d-flex align-items-center" role="alert" style="max-width: 500px; margin: 0 auto 20px auto; border-radius: 10px;">
                <i class="fa fa-exclamation-circle mg-r-10"></i> <strong>Ops!</strong> Houve uma falha, tente novamente.
            </div>
            <meta http-equiv="refresh" content="2;URL=./whatsapp" />
        <?php } ?>

        <!-- LÓGICA DE EXIBIÇÃO -->
        <?php if($dadoscon->conn == 0){ ?>
            <!-- TELA: DESCONECTADO -->
            <div class="whatsapp-card">
                <div class="wa-header">
                    <i class="fa fa-whatsapp fa-3x"></i>
                </div>
                <div class="wa-body">
                    <div class="status-icon-box status-disconnected">
                        <i class="fa fa-plug"></i>
                    </div>
                    
                    <h2 class="wa-title">Não Conectado</h2>
                    <p class="wa-subtitle">
                        Sua instância do WhatsApp está desconectada. <br>
                        Clique abaixo para gerar um QR Code e parear seu aparelho.
                    </p>

                    <form action="classes/gera_qr.php" method="post">
                        <input type="hidden" name="token_api" value="<?php print $dadosgerais->tokenapi;?>">
                        <input type="hidden" name="celular" value="<?php print $dadosgerais->celular; ?>">
                        
                        <button type="submit" class="btn btn-wa-primary">
                            <i class="fa fa-qrcode mg-r-5"></i> CONECTAR AGORA
                        </button>
                    </form>
                </div>
            </div>

        <?php } else { ?>
            <!-- TELA: CONECTADO -->
            <div class="whatsapp-card">
                <div class="wa-header">
                    <i class="fa fa-whatsapp fa-3x"></i>
                </div>
                <div class="wa-body">
                    <div class="status-icon-box status-connected">
                        <div class="pulse-ring"></div>
                        <i class="fa fa-check"></i>
                    </div>
                    
                    <h2 class="wa-title">WhatsApp Online</h2>
                    <p class="wa-subtitle">
                        Tudo certo! Seu sistema está conectado e pronto para enviar mensagens.
                    </p>

                    <form action="classes/qr_exit.php" method="post">
                        <input type="hidden" name="token_api" value="<?php print $dadosgerais->tokenapi;?>">
                        
                        <button type="submit" class="btn btn-wa-danger">
                            <i class="fa fa-power-off mg-r-5"></i> DESCONECTAR
                        </button>
                    </form>
                </div>
            </div>
        <?php } ?>

    </div>
</div>

<script src="../lib/jquery/js/jquery.js"></script>
<script src="../lib/jquery.cookie/js/jquery.cookie.js"></script>
<script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
<script src="../lib/select2/js/select2.full.min.js"></script>
<script src="../js/moeda.js"></script>
<script src="../js/slim.js"></script>

<script>
    $('.dinheiro').mask('#.##0,00', {reverse: true});
    
    function upperCaseF(a) {
        setTimeout(function(){
            a.value = a.value.toUpperCase();
        }, 1);
    }
</script>
</body>
</html>
<?php
ob_end_flush();
?>