<?php
require_once __DIR__ . '/../db/Conexao.php';
// require_once "topo.php"; // Descomente se precisar do topo
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Changelog</title>
    
    <!-- Font Awesome e Bootstrap -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    
    <style>
        body { background-color: #f5f7fb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        
        /* Card Principal */
        .version-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-top: 40px;
            margin-bottom: 40px;
            border: 1px solid #edf2f9;
            overflow: hidden;
            position: relative;
        }
        
        /* Cabeçalho da Versão */
        .version-header {
            background: #fff;
            padding: 25px 30px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .version-badge {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 800;
            font-size: 1.5rem;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
            letter-spacing: 0.5px;
        }
        
        .version-date {
            color: #8898aa;
            font-weight: 600;
            font-size: 1.1rem;
            display: flex;
            align-items: center;
        }

        /* Corpo do Changelog */
        .version-body { padding: 40px; }

        /* Categorias */
        .category-block { margin-bottom: 30px; }
        .category-title {
            font-size: 1.1rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        /* Ícones e Cores das Categorias */
        .cat-icon {
            width: 36px; height: 36px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            margin-right: 15px;
            font-size: 1rem;
        }

        /* Novidades */
        .cat-feat { color: #2dce89; background: #e6fffa; }
        .title-feat { color: #2dce89; }

        /* Correções */
        .cat-fix { color: #f5365c; background: #fff5f5; }
        .title-fix { color: #f5365c; }

        /* Estilo (No Visual) */
        .cat-style { color: #5e72e4; background: #ebf0ff; }
        .title-style { color: #5e72e4; }

        /* Melhorias */
        .cat-refactor { color: #fb6340; background: #fff4e6; }
        .title-refactor { color: #fb6340; }

        /* Outros */
        .cat-chore { color: #11cdef; background: #e0faff; }
        .title-chore { color: #11cdef; }

        /* Lista de Itens */
        .change-list { list-style: none; padding: 0; margin: 0; }
        .change-list li {
            position: relative;
            padding-left: 25px;
            margin-bottom: 12px;
            color: #525f7f;
            font-size: 1rem;
            line-height: 1.6;
        }
        .change-list li::before {
            content: '';
            position: absolute;
            left: 0; top: 10px;
            width: 8px; height: 8px;
            border-radius: 50%;
            background-color: #dee2e6;
        }
        
        /* Cores dos bullets */
        .list-feat li::before { background-color: #2dce89; }
        .list-fix li::before { background-color: #f5365c; }
        .list-style li::before { background-color: #5e72e4; }
        .list-refactor li::before { background-color: #fb6340; }
        .list-chore li::before { background-color: #11cdef; }

        /* Botão Voltar Flutuante */
        .btn-back-float {
            position: fixed; bottom: 30px; right: 30px;
            background: #fff; color: #333;
            width: 50px; height: 50px; border-radius: 50%;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            display: flex; align-items: center; justify-content: center;
            font-size: 1.2rem; transition: all 0.3s;
            z-index: 999;
            text-decoration: none;
        }
        .btn-back-float:hover { transform: translateY(-5px); color: #007bff; }

    </style>
</head>

<body>

    <a href="./" class="btn-back-float" title="Voltar"><i class="fas fa-arrow-left"></i></a>

    <div class="slim-mainpanel">
        <div class="container">
            
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    
                    <?php
                    // Lógica para ler o JSON ou usar dados padrão se não existir
                    if(file_exists('changelog.json')) {
                        $json_content = file_get_contents('changelog.json');
                        $changelog = json_decode($json_content, true);
                    } else {
                        // Se não existir JSON, inicializa vazio para não dar erro
                        // Mas vamos forçar a exibição dos dados fixos abaixo
                        $changelog = []; 
                    }

                    // --- DADOS FIXOS DA VERSÃO 5.1 (PRIORIDADE) ---
                    // Se quiser usar o JSON, basta remover esta parte e garantir que o JSON tenha a chave "5.1"
                    $versao_atual = "5.1";
                    $data_atual = "16/12/2025";
                    
                    // Se tiver dados no JSON para essa versão, usa eles. Se não, usa os fixos aqui no PHP.
                    // Para garantir o que você pediu, vou colocar fixo aqui as categorias pedidas.
                    
                    $dados_versao = [
                        'feat' => isset($changelog[$versao_atual]['feat']) ? $changelog[$versao_atual]['feat'] : ['Novo sistema de pagamentos via PIX automático', 'Integração com API de WhatsApp'],
                        
                        'style' => isset($changelog[$versao_atual]['style']) ? $changelog[$versao_atual]['style'] : ['Novo layout responsivo para mobile', 'Atualização dos ícones do menu lateral'],
                        
                        'fix' => isset($changelog[$versao_atual]['fix']) ? $changelog[$versao_atual]['fix'] : ['Correção no login de usuários', 'Ajuste na impressão de relatórios'],
                        
                        'refactor' => isset($changelog[$versao_atual]['refactor']) ? $changelog[$versao_atual]['refactor'] : ['Otimização do carregamento da página inicial', 'Melhoria na segurança de senhas'],
                        
                        'chore' => isset($changelog[$versao_atual]['chore']) ? $changelog[$versao_atual]['chore'] : []
                    ];
                    ?>
                    
                    <!-- CARD DA VERSÃO -->
                    <div class="version-card">
                        <div class="version-header">
                            <div class="version-badge">
                                Versão <?php echo $versao_atual; ?>
                            </div>
                            <div class="version-date">
                                <i class="far fa-calendar-alt mr-2"></i> <?php echo $data_atual; ?>
                            </div>
                        </div>
                        
                        <div class="version-body">
                            <div class="row">
                                
                                <!-- Novidades & Recursos -->
                                <?php if (count($dados_versao['feat']) > 0) { ?>
                                <div class="col-md-12 category-block">
                                    <div class="category-title title-feat">
                                        <div class="cat-icon cat-feat"><i class="fas fa-star"></i></div>
                                        Novidades & Recursos
                                    </div>
                                    <ul class="change-list list-feat">
                                        <?php foreach ($dados_versao['feat'] as $item) { echo "<li>$item</li>"; } ?>
                                    </ul>
                                </div>
                                <?php } ?>

                                <!-- No Visual (Style) -->
                                <?php if (count($dados_versao['style']) > 0) { ?>
                                <div class="col-md-6 category-block">
                                    <div class="category-title title-style">
                                        <div class="cat-icon cat-style"><i class="fas fa-paint-brush"></i></div>
                                        No Visual
                                    </div>
                                    <ul class="change-list list-style">
                                        <?php foreach ($dados_versao['style'] as $item) { echo "<li>$item</li>"; } ?>
                                    </ul>
                                </div>
                                <?php } ?>

                                <!-- Correção de Bugs / Erros Corrigidos (Fix) -->
                                <?php if (count($dados_versao['fix']) > 0) { ?>
                                <div class="col-md-6 category-block">
                                    <div class="category-title title-fix">
                                        <div class="cat-icon cat-fix"><i class="fas fa-bug"></i></div>
                                        Erros Corrigidos
                                    </div>
                                    <ul class="change-list list-fix">
                                        <?php foreach ($dados_versao['fix'] as $item) { echo "<li>$item</li>"; } ?>
                                    </ul>
                                </div>
                                <?php } ?>

                                <!-- Otimizações & Melhorias / Mais velocidade e Segurança (Refactor) -->
                                <?php if (count($dados_versao['refactor']) > 0) { ?>
                                <div class="col-md-6 category-block">
                                    <div class="category-title title-refactor">
                                        <div class="cat-icon cat-refactor"><i class="fas fa-rocket"></i></div>
                                        Mais velocidade e Segurança
                                    </div>
                                    <ul class="change-list list-refactor">
                                        <?php foreach ($dados_versao['refactor'] as $item) { echo "<li>$item</li>"; } ?>
                                    </ul>
                                </div>
                                <?php } ?>

                                <!-- Outros (Chore) -->
                                <?php if (count($dados_versao['chore']) > 0) { ?>
                                <div class="col-md-6 category-block">
                                    <div class="category-title title-chore">
                                        <div class="cat-icon cat-chore"><i class="fas fa-info"></i></div>
                                        Outros
                                    </div>
                                    <ul class="change-list list-chore">
                                        <?php foreach ($dados_versao['chore'] as $item) { echo "<li>$item</li>"; } ?>
                                    </ul>
                                </div>
                                <?php } ?>

                            </div> <!-- /row -->
                        </div> <!-- /version-body -->
                    </div> <!-- /version-card -->

                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="../js/slim.js"></script>

</body>
</html>