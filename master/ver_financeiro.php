<?php
require_once "topo.php";

// Recupera ID da Cobrança
if (isset($_GET["vercli"])) { $cliente = $_GET['vercli']; }
if (isset($_POST["vercli"])) { $cliente = $_POST['vercli']; }

// Dados da Cobrança Principal
$buscafin = $connect->query("SELECT * FROM financeiro1 WHERE Id='$cliente' AND idm ='" . $cod_id . "'");
$buscafinx = $buscafin->fetch(PDO::FETCH_OBJ);

// Dados do Cliente
$buscacli = $connect->query("SELECT * FROM clientes WHERE Id='" . $buscafinx->idc . "' AND idm ='" . $cod_id . "'");
$buscaclix = $buscacli->fetch(PDO::FETCH_OBJ);

// Dados da Carteira (Pix)
$buscaCarteira = $connect->query("SELECT pagamentos, pix_manual FROM carteira WHERE Id='" . $cod_id . "'");
$dadosCarteira = $buscaCarteira->fetch(PDO::FETCH_OBJ);

// Limpa celular para link do zap
$celular_cliente = isset($buscaclix->celular) ? preg_replace("/[^0-9]/", "", $buscaclix->celular) : "";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" type="text/css" href="https://agilizaweb.com/atoast.css" />
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .form-control[disabled] { background-color: #fff; border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; border-left: 0; height: 45px; color: #495057; font-weight: 500; }
        .input-group-text { border-radius: 8px 0 0 8px; border: 1px solid #e0e0e0; border-right: 0; width: 45px; justify-content: center; background: #fff; }
        
        /* Ícones Coloridos */
        .icon-green { color: #28a745; background-color: #e8f5e9; }
        .icon-blue { color: #007bff; background-color: #e6f2ff; }
        .icon-purple { color: #6f42c1; background-color: #f3e9fe; }

        .table th { border-top: none; font-weight: 600; color: #343a40; text-transform: uppercase; font-size: 0.85rem; }
        .table td { vertical-align: middle; }
        
        /* Botões Redondos na Tabela */
        .btn-action { 
            width: 38px; height: 38px; border-radius: 50%; 
            display: inline-flex; align-items: center; justify-content: center; 
            border: none; color: white; margin: 0 3px; transition: all 0.2s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .btn-action:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); color: white; }
        
        .btn-manual { background: linear-gradient(135deg, #6f42c1, #8e44ad); } /* Roxo */
        .btn-wa { background: linear-gradient(135deg, #20c997, #1abc9c); }     /* Verde Água */
        .btn-receipt { background: linear-gradient(135deg, #343a40, #50555a); } /* Cinza Escuro */
        .btn-del { background: linear-gradient(135deg, #dc3545, #e74c3c); }     /* Vermelho */
        .btn-ok { background: linear-gradient(135deg, #28a745, #27ae60); cursor: default; } /* Verde Sucesso */

        /* Modal Moderno */
        .modal-content-modern { border-radius: 20px; border: none; overflow: hidden; }
        .modal-header-modern { background: #fff; border-bottom: 1px solid #f0f0f0; padding: 20px 25px; }
        .modal-body-modern { padding: 30px; background: #fcfcfc; }
        
        .msg-box { 
            background: #f8f9fa; border: 2px dashed #d1d3e2; border-radius: 12px; 
            padding: 15px; width: 100%; min-height: 110px; font-size: 0.9rem; color: #555; resize: none; 
            font-family: 'Courier New', monospace;
        }

        .btn-pill { border-radius: 50px; padding: 12px 20px; font-weight: 600; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
        .btn-whatsapp-full { background-color: #25D366; color: white; border: none; }
        .btn-copy-full { background-color: #4e73df; color: white; border: none; }
        .btn-confirm-full { background: linear-gradient(135deg, #6610f2, #6f42c1); color: white; border: none; width: 100%; font-size: 1rem; }
        
        .btn-confirm-full:hover { background: linear-gradient(135deg, #520dc2, #5a32a3); color: white; box-shadow: 0 5px 15px rgba(102, 16, 242, 0.3); }
    </style>
</head>
<body>

<div class="slim-mainpanel">
	<div class="container-fluid">
        
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <div class="mr-3" style="background: linear-gradient(135deg, #17a2b8, #138496); padding: 12px; border-radius: 10px;">
                            <i class="fas fa-file-invoice-dollar fa-lg text-white"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-dark font-weight-bold">FINANCEIRO DO CLIENTE</h4>
                            <p class="text-muted mb-0"><?= $buscaclix->nome; ?></p>
                        </div>
                    </div>
                    <a href="./contas_receber" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                        <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                    </a>
                </div>
            </div>
        </div>

		<div class="row">
			<div class="col-md-12">
                <!-- Card de Resumo -->
				<div class="card mb-4">
					<div class="card-body p-4">
						<div class="row mb-4">
							<div class="col-md-12 text-center">
								<h5 class="font-weight-bold text-dark mb-3">Status da Cobrança #<?php echo $buscafinx->Id; ?></h5>
                                <?php if ($buscafinx->status == 1) { ?>
                                    <span class="badge badge-danger p-2 px-4 shadow-sm" style="font-size: 1rem; border-radius: 20px;">
                                        <i class="fas fa-exclamation-circle mr-2"></i> PENDENTE
                                    </span>
                                <?php } else { ?>
                                    <span class="badge badge-success p-2 px-4 shadow-sm" style="font-size: 1rem; border-radius: 20px;">
                                        <i class="fas fa-check-circle mr-2"></i> QUITADO
                                    </span>
                                <?php } ?>
							</div>
						</div>
                        <hr class="mb-4">
						<div class="row">
                            <!-- Valor -->
							<div class="col-md-4 mb-3">
								<label class="font-weight-bold text-dark small">Valor Final Total</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text icon-green"><i class="fas fa-dollar-sign"></i></span></div>
                                    <input type="text" class="dinheiro form-control" value="R$ <?php print number_format($buscafinx->vparcela * $buscafinx->parcelas, 2, ',', '.'); ?>" disabled>
                                </div>
							</div>
                            <!-- Forma Pagamento -->
							<div class="col-md-4 mb-3">
								<label class="font-weight-bold text-dark small">Forma de Pagamento</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text icon-blue"><i class="fas fa-credit-card"></i></span></div>
                                    <input type="text" class="form-control" value="<?php echo ($buscafinx->formapagamento == 30 ? 'Mensal' : 'Outros'); ?>" disabled>
                                </div>
							</div>
                            <!-- Parcelas -->
							<div class="col-md-4 mb-3">
								<label class="font-weight-bold text-dark small">Qtd. Parcelas</label>
                                <div class="input-group">
                                    <div class="input-group-prepend"><span class="input-group-text icon-purple"><i class="fas fa-list-ol"></i></span></div>
                                    <input type="text" class="form-control" value="<?php print $buscafinx->parcelas; ?>" disabled>
                                </div>
							</div>
						</div>
                    </div>
                </div>

                <!-- Tabela de Parcelas -->
                <div class="card">
                    <div class="card-body p-4">
                        <h6 class="font-weight-bold text-dark mb-4"><i class="fas fa-list mr-2"></i> Lista de Parcelas</h6>
						<div class="table-responsive">
                            <table id="datatable1" class="table table-hover w-100">
                                <thead class="bg-light">
                                    <tr>
                                        <th>ID</th>
                                        <th>VENCIMENTO</th>
                                        <th>PAGAMENTO</th>
                                        <th>VALOR</th>
                                        <th class="text-center">STATUS</th> 
                                        <th class="text-center">AÇÕES</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Lógica de Juros
                                    function obterTaxaJurosDiaria($connect, $cod_id) {
                                        try {
                                            $busca = $connect->query("SELECT juros_diarios FROM carteira WHERE Id = '" . $cod_id . "'");
                                            return $busca->fetch(PDO::FETCH_OBJ)->juros_diarios;
                                        } catch (PDOException $e) { return null; }
                                    }
                                    $taxaJurosDiaria = floatval(obterTaxaJurosDiaria($connect, $cod_id));
                                    
                                    $buscafin2 = $connect->query("SELECT f2.*, f1.valorfinal as valor_original, f1.vparcela FROM financeiro2 f2 INNER JOIN financeiro1 f1 ON f2.chave = f1.chave WHERE f2.chave='" . $buscafinx->chave . "' AND f2.idm = '" . $cod_id . "' ORDER BY f2.Id ASC");
                                    
                                    while ($buscafinx2 = $buscafin2->fetch(PDO::FETCH_OBJ)) {
                                        $data1 = date("d/m/Y");
                                        $data2 = $buscafinx2->datapagamento;
                                        $d1 = strtotime(implode('-', array_reverse(explode('/', $data1))));
                                        $d2 = strtotime(implode('-', array_reverse(explode('/', $data2))));
                                        $prazo = ($d2 - $d1) / 86400;
                                        $prazox = explode(".", $prazo);
                                        $dias_atraso = str_replace("-", "", $prazo);

                                        // Atualiza Juros (simplificado para exibição)
                                        if ($prazo < 0 && ($buscafinx2->juros_calculados == 0 || $buscafinx2->taxa_juros_diaria != $taxaJurosDiaria || $buscafinx2->dias_vencidos != abs($prazo))) {
                                            $diasAtraso = abs($prazo);
                                            $juro = bcmul($taxaJurosDiaria, $diasAtraso, 2);
                                            $valorParcela = bcadd($buscafinx2->vparcela, $juro, 2);
                                            $connect->query("UPDATE financeiro2 SET parcela = '$valorParcela', juros_calculados = 1, dias_vencidos = '$diasAtraso' WHERE Id = '{$buscafinx2->Id}'");
                                            $buscafinx2->parcela = $valorParcela; // Atualiza visualmente
                                        }
                                    ?>
                                    <tr>
                                        <td><strong>#<?php print $buscafinx2->Id; ?></strong></td>
                                        <td><?php print $buscafinx2->datapagamento; ?></td>
                                        <td><?php echo ($buscafinx2->pagoem == "n") ? "--" : $buscafinx2->pagoem; ?></td>
                                        <td class="font-weight-bold text-dark">R$ <?php print number_format($buscafinx2->parcela, 2, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <?php if ($buscafinx2->pagoem != "n") { ?>
                                                <span class="badge badge-success px-3">PAGA</span>
                                            <?php } else { 
                                                if ($prazox[0] < 0) { ?> <span class="badge badge-danger px-3">VENCIDA</span> <?php } 
                                                else { ?> <span class="badge badge-info px-3">ABERTO</span> <?php } 
                                            } ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center">
                                                <?php if ($buscafinx2->status == 2) { ?>
                                                    <!-- Botão Pago -->
                                                    <button class="btn-action btn-ok" title="Já Pago"><i class="fas fa-check"></i></button>
                                                    
                                                    <!-- Comprovante -->
                                                    <form onsubmit="enviarcomprovate(event)" class="mb-0">
                                                        <input type="hidden" name="dcob" value="<?php print $cliente; ?>" />
                                                        <input type="hidden" name="cob" value="<?php print $buscafinx2->Id; ?>" />
                                                        <input type="hidden" name="codclix" value="<?php print $buscaclix->Id; ?>" />
                                                        <input type="hidden" name="tipom" value="5" />
                                                        <button type="submit" class="btn-action btn-receipt" title="Comprovante"><i class="bi bi-box-arrow-up-right"></i></button>
                                                    </form>
                                                <?php } else { ?>
                                                    <!-- BOTÃO MANUAL (ROXO) -->
                                                    <button type="button" class="btn-action btn-manual" 
                                                            onclick="abrirModalManual('<?php echo $buscafinx2->Id; ?>', '<?php echo number_format($buscafinx2->parcela, 2, ',', '.'); ?>', '<?php echo $buscafinx2->datapagamento; ?>')"
                                                            title="Cobrança Manual">
                                                        <i class="fas fa-hand-holding-usd"></i>
                                                    </button>
                                                    
                                                    <!-- BOTÃO GATEWAY (VERDE ÁGUA) -->
                                                    <form onsubmit="gerarPagamentos_omie(event)" class="mb-0">
                                                        <input type="hidden" name="dcob" value="<?php print $cliente; ?>" />
                                                        <input type="hidden" name="cob" value="<?php print $buscafinx2->Id; ?>" />
                                                        <input type="hidden" name="codclix" value="<?php print $buscaclix->Id; ?>" />
                                                        <input type="hidden" name="tipom" value="<?php echo ($prazox[0] < 0) ? 6 : 4; ?>" />
                                                        <button type="submit" class="btn-action btn-wa" title="Link Gateway"><i class="fab fa-whatsapp"></i></button>
                                                    </form>
                                                <?php } ?>
                                                
                                                <!-- EXCLUIR (VERMELHO) -->
                                                <form action="classes/clientes_exe.php" method="post" class="mb-0">
                                                    <input type="hidden" name="delparcela" value="<?php print $buscafinx2->chave; ?>" />
                                                    <input type="hidden" name="idparcela" value="<?php print $buscafinx2->Id; ?>" />
                                                    <button type="submit" class="btn-action btn-del" onclick='return confirm("Excluir?");'><i class="fas fa-trash-alt"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- ==============================================
     MODAL DE COBRANÇA (COM AJAX E VISUAL NOVO)
=================================================== -->
<div id="modalManual" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content modal-content-modern">
      
      <div class="modal-header modal-header-modern">
        <h5 class="modal-title font-weight-bold ml-2 text-dark">
            <i class="fas fa-wallet text-primary mr-2"></i> Recebimento Manual
        </h5>
        <button type="button" class="btn-close-custom" data-dismiss="modal" aria-label="Close">✕</button>
      </div>

      <div class="modal-body modal-body-modern text-center">
        
        <div class="mb-4">
            <h1 class="modal-valor-big" id="modalValorTxt">R$ 0,00</h1>
            <span class="badge-data" id="modalVencimentoTxt">Vencimento: --/--/----</span>
        </div>

        <div class="text-left mb-2">
            <small class="font-weight-bold text-muted ml-1">MENSAGEM DE COBRANÇA</small>
        </div>
        <textarea id="textoCobranca" class="msg-box mb-4" readonly></textarea>

        <div class="row mb-4">
            <div class="col-6 pr-2">
                <button class="btn btn-copy-full btn-pill btn-block shadow-sm" onclick="copiarTexto()">
                    <i class="far fa-copy mr-1"></i> COPIAR
                </button>
            </div>
            <div class="col-6 pl-2">
                <a href="#" id="btnZapDireto" target="_blank" class="btn btn-whatsapp-full btn-pill btn-block shadow-sm">
                    <i class="fab fa-whatsapp mr-1"></i> ENVIAR
                </a>
            </div>
        </div>

        <hr style="border-top: 1px solid #e3e6f0;">
        
        <div class="alert alert-warning text-center small p-2 mb-3" style="border-radius: 10px;">
            <i class="fas fa-info-circle"></i> Clique abaixo <strong>apenas</strong> se o cliente já pagou.
        </div>

        <!-- Botão AJAX -->
        <button type="button" onclick="confirmarPagamentoAjax()" class="btn btn-confirm-full btn-pill shadow" id="btnConfirmarAjax">
            <i class="fas fa-check-double mr-2"></i> CONFIRMAR RECEBIMENTO
        </button>

      </div>
    </div>
  </div>
</div>

<input type="hidden" id="hiddenIdParcela" value="">

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../lib/bootstrap/js/bootstrap.js"></script>
<script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
<script src="https://agilizaweb.com/atoast.js"></script>
<script src="../js/slim.js"></script>

<script>
    // Variáveis Globais
    var chavePix = "<?php echo isset($dadosCarteira->pix_manual) ? $dadosCarteira->pix_manual : 'Chave não cadastrada'; ?>";
    var nomeCliente = "<?php echo $buscaclix->nome; ?>";
    var numCliente = "<?php echo $celular_cliente; ?>"; 

    // Abre Modal e preenche dados
    function abrirModalManual(id, valor, vencimento) {
        $('#hiddenIdParcela').val(id);
        
        $('#modalValorTxt').text('R$ ' + valor);
        $('#modalVencimentoTxt').text('Vencimento: ' + vencimento);
        
        var msg = `Olá ${nomeCliente},\nSegue dados para pagamento:\n\n💰 Valor: R$ ${valor}\n📅 Vencimento: ${vencimento}\n\n🔑 Chave Pix: ${chavePix}\n\nPor favor, envie o comprovante.`;

        $('#textoCobranca').val(msg);

        // Link do WhatsApp
        if(numCliente.length > 8) {
             var link = 'https://api.whatsapp.com/send?phone=55' + numCliente + '&text=' + encodeURIComponent(msg);
             $('#btnZapDireto').attr('href', link);
             $('#btnZapDireto').removeClass('disabled');
        } else {
             $('#btnZapDireto').addClass('disabled');
        }

        $('#modalManual').modal('show');
    }

    function copiarTexto() {
        var copyText = document.getElementById("textoCobranca");
        copyText.select();
        document.execCommand("copy");
        let atoast = aToast('Copiado com sucesso!', { position: 'top-center', type: 'success' });
    }

    // --- FUNÇÃO AJAX PARA CONFIRMAR PAGAMENTO ---
    function confirmarPagamentoAjax() {
        var idParcela = $('#hiddenIdParcela').val();
        var btn = $('#btnConfirmarAjax');

        if(!confirm("Deseja confirmar o recebimento desta parcela?")) return;

        // Bloqueia botão
        btn.prop('disabled', true);
        btn.html('<i class="fas fa-spinner fa-spin"></i> Processando...');

        $.ajax({
            url: 'classes/baixa_ajax.php', 
            type: 'POST',
            dataType: 'json',
            data: { id_parcela: idParcela },
            success: function(response) {
                if(response.erro === false) {
                    let atoast = aToast('Pagamento Confirmado!', { position: 'center', type: 'success' });
                    $('#modalManual').modal('hide');
                    setTimeout(function(){ location.reload(); }, 1500);
                } else {
                    alert("Erro: " + response.msg);
                    btn.prop('disabled', false);
                    btn.html('CONFIRMAR RECEBIMENTO');
                }
            },
            error: function() {
                alert("Erro de comunicação.");
                btn.prop('disabled', false);
                btn.html('CONFIRMAR RECEBIMENTO');
            }
        });
    }

    // Funções Legadas
    function gerarPagamentos_omie(event) { event.preventDefault(); /* ... */ }
    function enviarcomprovate(event) { event.preventDefault(); /* ... */ }

	$(function () {
		$('#datatable1').DataTable({
			responsive: true,
			language: { search: "", searchPlaceholder: 'Buscar...', lengthMenu: '_MENU_', paginate: { next: '>', previous: '<' } }
		});
	});
</script>
</body>
</html>