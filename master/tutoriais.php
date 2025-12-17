<?php
// Inclua o arquivo de conexão do banco de dados
require_once __DIR__ . '/../db/Conexao.php';
// Se existir o topo.php no seu sistema, descomente a linha abaixo e remova o require do Conexao se duplicar
// require_once "topo.php"; 

$cod_id = $_SESSION['cod_id'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeria de Vídeos</title>
    
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <style>
        body { background-color: #f5f7fb; }
        
        .card { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); transition: transform 0.2s; }
        
        /* Área de Cadastro */
        .icon-header {
            width: 50px; height: 50px; border-radius: 12px;
            background: linear-gradient(135deg, #ff0000, #c0392b);
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 1.8rem; margin-right: 15px;
            box-shadow: 0 4px 10px rgba(255, 0, 0, 0.3);
        }

        /* Inputs */
        .input-group-text { border-radius: 8px 0 0 8px; border: 1px solid #e0e0e0; background: #fff; width: 45px; justify-content: center; border-right: 0; }
        .form-control { border-radius: 0 8px 8px 0; border: 1px solid #e0e0e0; height: 45px; border-left: 0; }
        .form-control:focus { box-shadow: none; border-color: #e0e0e0; border-bottom: 2px solid #ff0000; }

        /* Ícones Inputs */
        .icon-blue { color: #007bff; background-color: #e6f2ff; }
        .icon-teal { color: #20c997; background-color: #e6fffa; }

        /* Card de Vídeo */
        .video-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .video-card iframe { border-radius: 15px 15px 0 0; }
        .card-title { font-weight: 700; color: #333; font-size: 1rem; margin-bottom: 15px; }

        /* Botões de Ação */
        .btn-action-circle {
            width: 35px; height: 35px; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            border: none; color: white; transition: all 0.2s; margin: 0 5px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        }
        .btn-action-circle:hover { transform: scale(1.1); color: white; }
        
        .btn-edit { background: linear-gradient(135deg, #fd7e14, #d35400); }
        .btn-del { background: linear-gradient(135deg, #dc3545, #c0392b); }

        .btn-add {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white; border-radius: 50px; padding: 10px 30px; border: none; font-weight: bold;
            box-shadow: 0 4px 10px rgba(0, 123, 255, 0.3); transition: all 0.3s;
        }
        .btn-add:hover { transform: translateY(-2px); color: white; }
    </style>
</head>

<body>

    <div class="slim-mainpanel">
        <div class="container mt-5">
            
            <!-- ÁREA ADMINISTRATIVA: ADICIONAR VÍDEO -->
            <?php if ($dadosgerais->tipo == 1): ?>
                <div class="row justify-content-center mb-5">
                    <div class="col-md-10">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center mb-4">
                                    <div class="icon-header">
                                        <i class="fab fa-youtube"></i>
                                    </div>
                                    <div>
                                        <h4 class="mb-0 text-dark font-weight-bold">NOVO VÍDEO</h4>
                                        <p class="text-muted mb-0">Adicione tutoriais para seus clientes</p>
                                    </div>
                                </div>
                                <hr>

                                <form id="videoForm" method="POST">
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold text-dark small">Título do Vídeo</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text icon-blue"><i class="fas fa-heading"></i></span>
                                                </div>
                                                <input type="text" name="title" id="title" class="form-control" placeholder="Ex: Como configurar..." required>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="font-weight-bold text-dark small">ID do Vídeo (YouTube)</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text icon-teal"><i class="fas fa-link"></i></span>
                                                </div>
                                                <input type="text" name="link" id="link" class="form-control" placeholder="Ex: i6gEcyFmJDc" required>
                                            </div>
                                            <small class="text-muted">Cole apenas o código após "v=" ou a ID do link curto.</small>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="submit" class="btn btn-add">
                                            <i class="fas fa-plus-circle mr-2"></i> ADICIONAR VÍDEO
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- LISTAGEM DE VÍDEOS -->
            <?php if ($dadosgerais->tipo == 2 || $dadosgerais->tipo == 1): ?>
                <?php $stmt = $connect->query('SELECT * FROM videos ORDER BY id DESC'); ?>
                
                <div class="d-flex align-items-center mb-4">
                    <i class="fas fa-play-circle fa-2x text-danger mr-2"></i>
                    <h4 class="text-dark font-weight-bold mb-0">Galeria de Tutoriais</h4>
                </div>

                <div class="row">
                    <?php while ($row = $stmt->fetch()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card video-card h-100">
                                <!-- Embed YouTube -->
                                <div class="embed-responsive embed-responsive-16by9" style="border-radius: 15px 15px 0 0;">
                                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/<?= $row['link'] ?>" allowfullscreen></iframe>
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title text-center"><?= $row['title'] ?></h5>
                                    
                                    <?php if ($dadosgerais->tipo == 1): ?>
                                        <div class="mt-auto text-center pt-3 border-top">
                                            <a href="edit_video.php?id=<?= $row['id'] ?>" class="btn-action-circle btn-edit" title="Editar">
                                                <i class="fas fa-pencil-alt"></i>
                                            </a>
                                            <button onclick="confirmDelete('<?= $row['id'] ?>')" class="btn-action-circle btn-del" title="Excluir">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>
    
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="../lib/bootstrap/js/bootstrap.js"></script>
    <script src="../js/slim.js"></script>
    
    <script>
        $(document).ready(function(){
            // Config Toastr
            toastr.options = {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-top-right",
                "timeOut": "3000"
            };

            // Checa mensagem na URL
            const urlParams = new URLSearchParams(window.location.search);
            const message = urlParams.get('message');
            if (message) {
                toastr.success(message);
            }

            // AJAX do Formulário
            $('#videoForm').on('submit', function(e){
                e.preventDefault();
                $.ajax({
                    url: '/master/adicionar_video.php',
                    type: 'post',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response){
                        if (response.success) {
                            toastr.success(response.message);
                            setTimeout(function(){ location.reload(); }, 1500); // Recarrega para mostrar o novo vídeo
                        } else {
                            toastr.error(response.message);
                        }
                    },
                    error: function() {
                        toastr.error("Erro ao comunicar com o servidor.");
                    }
                });
            });
        });

        function confirmDelete(id) {
            swal({
                title: "Excluir Vídeo?",
                text: "Essa ação não pode ser desfeita.",
                icon: "warning",
                buttons: ["Cancelar", "Excluir"],
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    window.location.href = 'delete_video.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>