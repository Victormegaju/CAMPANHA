<?php
require_once "topo.php";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- CSS Necessários -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
    
    <style>
        body { background-color: #f5f7fb; }
        .card { border: none; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
        .form-control { border-radius: 8px; border: 1px solid #e0e0e0; height: 45px; padding-left: 15px; }
        .form-control:focus { border-color: #4e73df; box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25); }
        .input-group-text { border-radius: 8px 0 0 8px; border: 1px solid #e0e0e0; background-color: #f8f9fa; }
        .btn-gradient-success { background: linear-gradient(135deg, #28a745, #20c997); color: white; border: none; }
        .btn-gradient-success:hover { background: linear-gradient(135deg, #218838, #1e7e34); color: white; transform: translateY(-1px); }
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
                            <div class="mr-3" style="background: linear-gradient(135deg, #28a745, #20c997); padding: 12px; border-radius: 10px;">
                                <i class="fas fa-user-plus fa-lg text-white"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 text-dark font-weight-bold">NOVO USUÁRIO</h4>
                                <p class="text-muted mb-0">Preencha os dados para cadastrar</p>
                            </div>
                        </div>
                        <a href="usuarios" class="btn btn-secondary" style="border-radius: 8px; padding: 10px 20px;">
                            <i class="fas fa-arrow-left mr-2"></i>VOLTAR
                        </a>
                    </div>
                </div>
            </div>

            <!-- Formulário -->
            <div class="row justify-content-center">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body p-4">
                            <form action="classes/funcionario_exe.php" method="post" autocomplete="off">
                                <input type="hidden" name="cad_cli" value="ok">
                                <input type="hidden" name="tipo" value="2">

                                <div class="row">
                                    <!-- Nome -->
                                    <div class="col-md-4 mb-3">
                                        <label class="font-weight-bold text-dark small">Nome Completo</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-user text-muted"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0" name="nome" maxlength="160" 
                                                   onkeydown="upperCaseF(this)" placeholder="Digite o nome completo" required>
                                        </div>
                                    </div>

                                    <!-- Celular -->
                                    <div class="col-md-3 mb-3">
                                        <label class="font-weight-bold text-dark small">Celular/WhatsApp</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fab fa-whatsapp text-muted"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0" name="celular" id="cel" 
                                                   placeholder="(00) 00000-0000" required>
                                        </div>
                                    </div>

                                    <!-- Login -->
                                    <div class="col-md-3 mb-3">
                                        <label class="font-weight-bold text-dark small">Login de Acesso</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-sign-in-alt text-muted"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0" name="email" 
                                                   placeholder="Usuário para login" required>
                                        </div>
                                    </div>
                                    
                                    <!-- Vencimento (Antiga Assinatura) -->
                                    <div class="col-md-2 mb-3">
                                        <label class="font-weight-bold text-dark small">Data de Vencimento</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-calendar-alt text-muted"></i></span>
                                            </div>
                                            <input type="text" class="form-control border-left-0 datepicker" name="assinatura" 
                                                   placeholder="DD/MM/AAAA" required readonly style="background-color: white;">
                                        </div>
                                    </div>

                                    <!-- Senha -->
                                    <div class="col-md-3 mb-3">
                                        <label class="font-weight-bold text-dark small">Senha</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-key text-muted"></i></span>
                                            </div>
                                            <input type="password" class="form-control border-left-0" name="senha" id="inputSenha" 
                                                   maxlength="20" placeholder="Digite a senha" required>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-light border text-muted" type="button" onclick="toggleSenha()" style="border-radius: 0 8px 8px 0; border-color: #e0e0e0 !important;">
                                                    <i class="fas fa-eye" id="iconSenha"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-4">

                                <div class="row">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-gradient-success btn-lg px-5 shadow-sm" name="cart">
                                            <i class="fas fa-check mr-2"></i>SALVAR CADASTRO
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../lib/jquery/js/jquery.js"></script>
    <script src="../lib/bootstrap/js/bootstrap.js"></script>
    <script src="../lib/jquery.maskedinput/js/jquery.maskedinput.js"></script>
    
    <!-- Datepicker JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/locales/bootstrap-datepicker.pt-BR.min.js"></script>

    <script>
    $(function () {
        'use strict';

        // Máscara para celular
        $('#cel').mask('(99) 99999-9999');

        // Inicializar Datepicker (Calendário)
        $('.datepicker').datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            autoclose: true,
            todayHighlight: true,
            orientation: "bottom auto"
        });
    });

    // Converter para maiúsculo
    function upperCaseF(a) {
        setTimeout(function () {
            a.value = a.value.toUpperCase();
        }, 1);
    }

    // Mostrar/Ocultar Senha
    function toggleSenha() {
        var input = document.getElementById('inputSenha');
        var icon = document.getElementById('iconSenha');
        
        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>

    <script src="../js/slim.js"></script>
</body>
</html>