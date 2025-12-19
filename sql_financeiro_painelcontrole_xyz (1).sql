-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Tempo de geração: 19/12/2025 às 12:34
-- Versão do servidor: 10.7.3-MariaDB-log
-- Versão do PHP: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `sql_financeiro_painelcontrole_xyz`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `agendamentos`
--

CREATE TABLE `agendamentos` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_cliente` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_disparo` date NOT NULL,
  `hora_disparo` time NOT NULL,
  `mensagem_id` int(11) NOT NULL,
  `arquivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pendente','enviado') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente',
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `modo_envio` enum('unica','balanceada') COLLATE utf8mb4_unicode_ci DEFAULT 'unica' COMMENT 'Modo de distribuição: unica (1 instância) ou balanceada (round-robin)',
  `instancia_id` int(11) DEFAULT NULL COMMENT 'ID da instância WhatsApp (se modo_envio = unica)',
  `instancias_json` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'JSON com distribuição de clientes por instância {"1":[101,102],"2":[103,104]}',
  `delay_min` int(11) DEFAULT 3 COMMENT 'Delay mínimo entre envios (segundos)',
  `delay_max` int(11) DEFAULT 7 COMMENT 'Delay máximo entre envios (segundos)',
  `inicio_execucao` datetime DEFAULT NULL COMMENT 'Momento em que o envio começou',
  `fim_execucao` datetime DEFAULT NULL COMMENT 'Momento em que o envio foi concluído',
  `ultimo_envio` datetime DEFAULT NULL COMMENT 'Último envio efetivado nesta campanha'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `agendamentos`
--

INSERT INTO `agendamentos` (`id`, `id_usuario`, `id_cliente`, `data_disparo`, `hora_disparo`, `mensagem_id`, `arquivo`, `status`, `data_criacao`, `modo_envio`, `instancia_id`, `instancias_json`, `delay_min`, `delay_max`, `inicio_execucao`, `fim_execucao`, `ultimo_envio`) VALUES
(1, 1, '', '2025-12-18', '13:46:00', 1, '', 'enviado', '2025-12-18 16:45:12', 'unica', 1, NULL, 3, 7, '2025-12-18 13:46:03', '2025-12-18 13:46:09', '2025-12-18 13:46:04'),
(2, 1, '', '2025-12-18', '15:27:00', 2, '', 'enviado', '2025-12-18 18:27:46', 'unica', 1, NULL, 21, 30, '2025-12-18 15:28:03', '2025-12-18 15:28:27', '2025-12-18 15:28:04');

-- --------------------------------------------------------

--
-- Estrutura para tabela `carteira`
--

CREATE TABLE `carteira` (
  `Id` int(11) NOT NULL,
  `idm` varchar(9) DEFAULT NULL,
  `valor` varchar(250) DEFAULT NULL,
  `entrada` timestamp NULL DEFAULT current_timestamp(),
  `vjurus` varchar(255) DEFAULT NULL,
  `login` varchar(255) DEFAULT NULL,
  `senha` varchar(60) DEFAULT NULL,
  `tipo` varchar(2) DEFAULT '1',
  `roles` enum('superadmin','master') DEFAULT 'master',
  `status` varchar(2) DEFAULT '1',
  `nome` varchar(120) DEFAULT NULL,
  `celular` varchar(255) DEFAULT NULL,
  `nascimento` varchar(15) DEFAULT NULL,
  `cpf` varchar(25) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `foto_perfil` varchar(500) DEFAULT NULL COMMENT 'Caminho da foto de perfil do usuário',
  `cep` varchar(25) DEFAULT NULL,
  `endereco` varchar(120) DEFAULT NULL,
  `numero` varchar(9) DEFAULT NULL,
  `bairro` varchar(60) DEFAULT NULL,
  `complemento` varchar(60) DEFAULT NULL,
  `cidade` varchar(30) DEFAULT NULL,
  `uf` varchar(5) DEFAULT NULL,
  `tokenmp` varchar(255) DEFAULT NULL,
  `tokenasaas` varchar(255) DEFAULT NULL,
  `nomecom` varchar(160) NOT NULL,
  `cnpj` varchar(30) NOT NULL,
  `enderecom` varchar(160) NOT NULL,
  `contato` varchar(15) NOT NULL,
  `msg` varchar(2) DEFAULT '1',
  `msgqr` varchar(2) DEFAULT '1',
  `msgpix` varchar(2) DEFAULT '1',
  `tokenapi` varchar(60) DEFAULT NULL,
  `pagamentos` varchar(2) DEFAULT '1',
  `assinatura` varchar(10) DEFAULT NULL,
  `background` varchar(255) DEFAULT NULL,
  `juros_diarios` decimal(10,2) NOT NULL DEFAULT 0.00,
  `gerecianet_client` varchar(255) NOT NULL,
  `gerecianet_secret` varchar(255) NOT NULL,
  `certificado_pem` mediumblob DEFAULT NULL,
  `chave_pix` varchar(255) DEFAULT NULL,
  `key_paghiper` varchar(255) DEFAULT NULL,
  `token_paghiper` varchar(255) DEFAULT NULL,
  `client_id_asaas` varchar(255) DEFAULT NULL,
  `publicmp` varchar(255) DEFAULT NULL,
  `favicon` varchar(255) DEFAULT NULL,
  `whatsapp_api` varchar(255) DEFAULT NULL,
  `whatsapp_token` varchar(255) DEFAULT NULL,
  `pix_manual_key` varchar(255) DEFAULT NULL,
  `premium_zap_name` varchar(255) DEFAULT NULL,
  `premium_zap_token` varchar(255) DEFAULT NULL,
  `premium_zap_checkbox` tinyint(1) DEFAULT 0,
  `cob_brasil` text DEFAULT NULL,
  `client_id_brasil` text DEFAULT NULL,
  `client_secret_brasil` text DEFAULT NULL,
  `convenio_brasil` int(11) DEFAULT 0,
  `carteira_brasil` int(11) DEFAULT 0,
  `variacao_brasil` int(11) DEFAULT 0,
  `cadastros_lim` int(11) DEFAULT NULL COMMENT 'Limite de cadastros herdado do plano',
  `plano_id` int(11) DEFAULT NULL COMMENT 'Plano associado ao usuário',
  `nao_cobrar_fim_semana` int(11) DEFAULT NULL COMMENT '0 = cobra FDS, 1 = não cobrar sábados e domingos',
  `whatsapp_api_2` varchar(255) DEFAULT NULL,
  `whatsapp_token_2` varchar(255) DEFAULT NULL,
  `servidor_1_ativo` tinyint(1) DEFAULT 0,
  `servidor_2_ativo` tinyint(1) DEFAULT 0,
  `mensagem_ajuda` text DEFAULT NULL,
  `servidor_1_limite` int(11) DEFAULT 100,
  `servidor_2_limite` int(11) DEFAULT 100,
  `email_verified` tinyint(1) DEFAULT 0 COMMENT 'Status de verificação do email',
  `email_verification_token` varchar(255) DEFAULT NULL COMMENT 'Token para verificação de email',
  `email_verification_expires` timestamp NULL DEFAULT NULL COMMENT 'Data de expiração do token de verificação',
  `reset_password_token` varchar(255) DEFAULT NULL COMMENT 'Token para redefinição de senha',
  `reset_password_expires` timestamp NULL DEFAULT NULL COMMENT 'Data de expiração do token de redefinição de senha',
  `id_indicador` int(11) DEFAULT NULL COMMENT 'ID do usuário que indicou este usuário',
  `instancias_limite` int(11) DEFAULT NULL COMMENT 'Limite de instâncias WhatsApp herdado do plano',
  `auto_cob_usar_link` tinyint(1) DEFAULT 0 COMMENT 'Tipo de disparo automático: 0=PIX/QRCode direto, 1=Link de pagamento'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `carteira`
--

INSERT INTO `carteira` (`Id`, `idm`, `valor`, `entrada`, `vjurus`, `login`, `senha`, `tipo`, `roles`, `status`, `nome`, `celular`, `nascimento`, `cpf`, `email`, `foto_perfil`, `cep`, `endereco`, `numero`, `bairro`, `complemento`, `cidade`, `uf`, `tokenmp`, `tokenasaas`, `nomecom`, `cnpj`, `enderecom`, `contato`, `msg`, `msgqr`, `msgpix`, `tokenapi`, `pagamentos`, `assinatura`, `background`, `juros_diarios`, `gerecianet_client`, `gerecianet_secret`, `certificado_pem`, `chave_pix`, `key_paghiper`, `token_paghiper`, `client_id_asaas`, `publicmp`, `favicon`, `whatsapp_api`, `whatsapp_token`, `pix_manual_key`, `premium_zap_name`, `premium_zap_token`, `premium_zap_checkbox`, `cob_brasil`, `client_id_brasil`, `client_secret_brasil`, `convenio_brasil`, `carteira_brasil`, `variacao_brasil`, `cadastros_lim`, `plano_id`, `nao_cobrar_fim_semana`, `whatsapp_api_2`, `whatsapp_token_2`, `servidor_1_ativo`, `servidor_2_ativo`, `mensagem_ajuda`, `servidor_1_limite`, `servidor_2_limite`, `email_verified`, `email_verification_token`, `email_verification_expires`, `reset_password_token`, `reset_password_expires`, `id_indicador`, `instancias_limite`, `auto_cob_usar_link`) VALUES
(1, '1', '', '2024-02-14 21:06:28', NULL, '230223', '8bd82ed056fa12b44e99702900e395d8c0afcf93', '1', 'superadmin', '1', 'Siniclei', '98985973392', NULL, NULL, NULL, 'uploads/perfil/perfil_1_1765802959.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'APP_USR-5091991093707024-121800-91e10024428d93d10c740b17505ac5ce-2652688037', '$aact_YTU5YTE0M2M2N2I4MTliNzk0YTI5N2U5MzdjNWZmNDQ6OjAwMDAwMDAwMDAwMDAyODcxMjY6OiRhYWNoXzMyZjYxNzdmLWY3MTMtNDI3MC05MjBkLTlkZDQ1ODNlNzE5Ng==', 'Gestor de Cobranças', '45988080667', 'RUA XXX', '41988150812', '1', '1', '2', 'c5dddaa86bf15c3aeddf278913cd3911', '2', '15/02/2024', 'https://i.ibb.co/yvDCkFR/20251211-180044-1-Photoroom-1.png', 0.00, '', '', NULL, '', 'API_KEY', 'TOKEN_PGHIP', NULL, 'APP_USR-773ff362-17ff-4d0f-b335-43b2bea8ab5f', '/img/favicon_1718851463.png', 'http://whatsapp.painelcontrole.xyz:8080', '3PgGMF0lQySRMpZfN8BfOl0cQIlfHjUX', 'sinicleiss@gmail.com', NULL, NULL, 0, '', '', '', 0, 0, 0, NULL, NULL, 0, '', '', 1, 0, 'Sejam Vem Vindos', 1000, 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, 1),
(81, '1', NULL, '2025-12-19 10:21:24', NULL, 'siniclei@gmail.com', 'ec7117851c0e5dbaad4effdb7cd17c050cea88cb', '2', 'master', '1', 'SuperNet', '21975301749', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Nome Padrão', '00000000000000', 'Endereço Padrão', '0000000000', '1', '1', '1', 'd2833005c43c5cf181dceea17035edd1', '1', '20/12/2025', NULL, 0.00, 'chav254egerenciaid', 'id_secret_aqui', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 200, 19, NULL, NULL, NULL, 0, 0, NULL, 100, 100, 1, NULL, NULL, NULL, NULL, NULL, 3, 0),
(82, '1', NULL, '2025-12-19 09:07:28', NULL, 'Dg8738@gmail.com', 'ec7117851c0e5dbaad4effdb7cd17c050cea88cb', '2', 'master', '1', 'Dg8738', '21975301749', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Nome Padrão', '00000000000000', 'Endereço Padrão', '0000000000', '1', '1', '1', NULL, '1', '22/12/2025', NULL, 0.00, 'chav254egerenciaid', 'id_secret_aqui', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 200, 19, NULL, NULL, NULL, 0, 0, NULL, 100, 100, 1, NULL, NULL, NULL, NULL, NULL, 3, 0),
(83, '1', NULL, '2025-12-19 09:10:07', NULL, 'Akassio@gmail.com', 'ec7117851c0e5dbaad4effdb7cd17c050cea88cb', '2', 'master', '1', 'Akassio', '6696739552', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Nome Padrão', '00000000000000', 'Endereço Padrão', '0000000000', '1', '1', '1', NULL, '1', '22/12/2025', NULL, 0.00, 'chav254egerenciaid', 'id_secret_aqui', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 200, 19, NULL, NULL, NULL, 0, 0, NULL, 100, 100, 1, NULL, NULL, NULL, NULL, NULL, 3, 0),
(84, '1', NULL, '2025-12-19 09:28:18', NULL, 'tiago87@gmail.com', 'ec7117851c0e5dbaad4effdb7cd17c050cea88cb', '2', 'master', '1', 'Tiago87', '9188952599', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Nome Padrão', '00000000000000', 'Endereço Padrão', '0000000000', '1', '1', '1', NULL, '1', '22/12/2025', NULL, 0.00, 'chav254egerenciaid', 'id_secret_aqui', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 200, 19, NULL, NULL, NULL, 0, 0, NULL, 100, 100, 1, NULL, NULL, NULL, NULL, NULL, 3, 0),
(85, '1', NULL, '2025-12-19 14:00:08', NULL, 'rastreadorcassilink@hotmail.com', 'ec7117851c0e5dbaad4effdb7cd17c050cea88cb', '2', 'master', '1', 'Rastreador', '6781529863', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Nome Padrão', '00000000000000', 'Endereço Padrão', '0000000000', '1', '1', '1', NULL, '1', '22/12/2025', NULL, 0.00, 'chav254egerenciaid', 'id_secret_aqui', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 0, 0, 0, 200, 19, NULL, NULL, NULL, 0, 0, NULL, 100, 100, 1, NULL, NULL, NULL, NULL, NULL, 3, 0);

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

CREATE TABLE `categoria` (
  `id` int(11) NOT NULL,
  `idu` varchar(9) DEFAULT NULL,
  `nome` varchar(60) DEFAULT NULL,
  `tipo` varchar(20) DEFAULT 'cliente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `categoria`
--

INSERT INTO `categoria` (`id`, `idu`, `nome`, `tipo`) VALUES
(41, '1', 'INTERNET', 'cliente'),
(42, '1', 'IPTV', 'cliente'),
(43, '1', 'ANUNCIOS', 'cliente'),
(44, '81', 'ANUNCIOS', 'cliente'),
(45, '81', 'MARKMARKET', 'cliente');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categorias_financeiras`
--

CREATE TABLE `categorias_financeiras` (
  `id` int(11) UNSIGNED NOT NULL,
  `idm` int(11) NOT NULL COMMENT 'ID do usuário/empresa',
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome da categoria',
  `tipo` enum('despesa','receita') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo da categoria',
  `icone` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'fa-tag' COMMENT 'Ícone Font Awesome',
  `cor` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'secondary' COMMENT 'Cor do badge (success, danger, info, warning, primary, secondary, lilac)',
  `eh_recorrente` tinyint(1) DEFAULT 0 COMMENT '1=Categoria típica de despesa recorrente (aluguel, luz, etc)',
  `ativo` tinyint(1) DEFAULT 1 COMMENT '1=ativa, 0=inativa',
  `ordem` int(11) DEFAULT 0 COMMENT 'Ordem de exibição',
  `criado_em` timestamp NULL DEFAULT current_timestamp(),
  `atualizado_em` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Categorias para gestão financeira (despesas e receitas)';

--
-- Despejando dados para a tabela `categorias_financeiras`
--

INSERT INTO `categorias_financeiras` (`id`, `idm`, `nome`, `tipo`, `icone`, `cor`, `eh_recorrente`, `ativo`, `ordem`, `criado_em`, `atualizado_em`) VALUES
(1, 1, 'Aluguel', 'despesa', 'fa-home', 'danger', 1, 1, 1, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(2, 1, 'Energia Elétrica', 'despesa', 'fa-bolt', 'warning', 1, 1, 2, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(3, 1, 'Água', 'despesa', 'fa-tint', 'info', 1, 1, 3, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(4, 1, 'Internet', 'despesa', 'fa-wifi', 'primary', 1, 1, 4, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(5, 1, 'Telefone', 'despesa', 'fa-phone', 'secondary', 1, 1, 5, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(6, 1, 'Alimentação', 'despesa', 'fa-utensils', 'success', 0, 1, 10, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(7, 1, 'Transporte', 'despesa', 'fa-car', 'info', 0, 1, 11, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(8, 1, 'Combustível', 'despesa', 'fa-gas-pump', 'danger', 0, 1, 12, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(9, 1, 'Salários', 'despesa', 'fa-users', 'danger', 1, 1, 20, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(10, 1, 'Impostos', 'despesa', 'fa-receipt', 'danger', 0, 1, 21, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(11, 1, 'Contador', 'despesa', 'fa-calculator', 'warning', 1, 1, 22, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(12, 1, 'Escritório', 'despesa', 'fa-building', 'secondary', 1, 1, 23, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(13, 1, 'Fornecedores', 'despesa', 'fa-truck', 'primary', 0, 1, 30, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(14, 1, 'Estoque', 'despesa', 'fa-boxes', 'info', 0, 1, 31, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(15, 1, 'Marketing', 'despesa', 'fa-bullhorn', 'lilac', 0, 1, 32, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(16, 1, 'Manutenção', 'despesa', 'fa-wrench', 'warning', 0, 1, 33, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(17, 1, 'Vendas', 'receita', 'fa-shopping-cart', 'success', 0, 1, 40, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(18, 1, 'Serviços', 'receita', 'fa-handshake', 'success', 0, 1, 41, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(19, 1, 'Investimentos', 'receita', 'fa-chart-line', 'primary', 0, 1, 42, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(20, 1, 'Comissões', 'receita', 'fa-percentage', 'success', 0, 1, 43, '2025-12-15 12:40:53', '2025-12-15 12:40:53'),
(21, 1, 'Outros', 'despesa', 'fa-ellipsis-h', 'secondary', 0, 1, 99, '2025-12-15 12:40:53', '2025-12-15 12:40:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `chat_conversas`
--

CREATE TABLE `chat_conversas` (
  `id` int(11) UNSIGNED NOT NULL,
  `idm` int(11) NOT NULL COMMENT 'ID da empresa/usuário',
  `contato_telefone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Telefone do contato',
  `contato_nome` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Nome do contato',
  `contato_avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL do avatar',
  `ultima_mensagem` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Última mensagem da conversa',
  `ultima_mensagem_data` datetime DEFAULT NULL COMMENT 'Data da última mensagem',
  `nao_lidas` int(11) DEFAULT 0 COMMENT 'Número de mensagens não lidas',
  `status` enum('ativa','arquivada') COLLATE utf8mb4_unicode_ci DEFAULT 'ativa' COMMENT 'Status da conversa',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Conversas do chat WhatsApp';

-- --------------------------------------------------------

--
-- Estrutura para tabela `chat_mensagens`
--

CREATE TABLE `chat_mensagens` (
  `id` int(11) UNSIGNED NOT NULL,
  `conversa_id` int(11) UNSIGNED NOT NULL COMMENT 'ID da conversa',
  `idm` int(11) NOT NULL COMMENT 'ID da empresa/usuário',
  `tipo` enum('enviada','recebida') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo da mensagem',
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Conteúdo da mensagem',
  `tipo_midia` enum('text','image','audio','video','document') COLLATE utf8mb4_unicode_ci DEFAULT 'text' COMMENT 'Tipo de mídia',
  `midia_url` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'URL da mídia',
  `status` enum('enviando','enviada','entregue','lida','erro') COLLATE utf8mb4_unicode_ci DEFAULT 'enviando' COMMENT 'Status da mensagem',
  `lida` tinyint(1) DEFAULT 0 COMMENT 'Mensagem lida',
  `erro` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mensagem de erro',
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Mensagens do chat WhatsApp';

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

CREATE TABLE `clientes` (
  `Id` int(11) NOT NULL,
  `idm` varchar(9) DEFAULT NULL,
  `idc` varchar(9) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT 0.00,
  `entrada` timestamp NULL DEFAULT current_timestamp(),
  `vjurus` varchar(5) DEFAULT '100',
  `login` varchar(15) DEFAULT NULL,
  `senha` varchar(60) DEFAULT NULL,
  `tipo` varchar(2) DEFAULT '1',
  `status` varchar(2) DEFAULT '1',
  `nome` varchar(120) DEFAULT NULL,
  `celular` varchar(255) DEFAULT NULL,
  `nascimento` varchar(15) DEFAULT NULL,
  `cpf` varchar(25) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `cep` varchar(25) DEFAULT NULL,
  `endereco` varchar(120) DEFAULT NULL,
  `numero` varchar(9) DEFAULT NULL,
  `bairro` varchar(60) DEFAULT NULL,
  `complemento` varchar(60) DEFAULT NULL,
  `cidade` varchar(30) DEFAULT NULL,
  `uf` varchar(5) DEFAULT NULL,
  `rg` varchar(20) DEFAULT NULL,
  `emissao` varchar(30) DEFAULT NULL,
  `uf2` varchar(6) DEFAULT NULL,
  `mae` varchar(60) DEFAULT NULL,
  `id_asaas` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL COMMENT 'Caminho completo da foto do cliente',
  `fiador_nome` varchar(255) DEFAULT NULL COMMENT 'Nome do fiador',
  `fiador_whatsapp` varchar(20) DEFAULT NULL COMMENT 'WhatsApp do fiador',
  `imei` varchar(20) DEFAULT NULL COMMENT 'IMEI do dispositivo',
  `referencia_nome` varchar(255) DEFAULT NULL COMMENT 'Nome da referência pessoal',
  `referencia_whatsapp` varchar(20) DEFAULT NULL COMMENT 'WhatsApp da referência pessoal'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `clientes`
--

INSERT INTO `clientes` (`Id`, `idm`, `idc`, `valor`, `entrada`, `vjurus`, `login`, `senha`, `tipo`, `status`, `nome`, `celular`, `nascimento`, `cpf`, `email`, `cep`, `endereco`, `numero`, `bairro`, `complemento`, `cidade`, `uf`, `rg`, `emissao`, `uf2`, `mae`, `id_asaas`, `foto`, `fiador_nome`, `fiador_whatsapp`, `imei`, `referencia_nome`, `referencia_whatsapp`) VALUES
(91, '1', '41', 0.00, '2025-12-18 19:08:48', '100', NULL, NULL, '1', '1', 'TATIANA', '21988987998', '21/08/1984', '42534564366', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, NULL, NULL),
(94, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 3', '244935771082', NULL, NULL, 'cliente3@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(95, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 4', '258851724490', NULL, NULL, 'cliente4@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(96, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 5', '351913078056', NULL, NULL, 'cliente5@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(97, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 6', '5213312951448', NULL, NULL, 'cliente6@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(98, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 7', '551140041144', NULL, NULL, 'cliente7@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(99, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 8', '5511910384197', NULL, NULL, 'cliente8@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(100, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 9', '5511910634772', NULL, NULL, 'cliente9@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(101, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 10', '5511912309370', NULL, NULL, 'cliente10@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(102, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 11', '5511913024047', NULL, NULL, 'cliente11@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(103, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 12', '5511914862273', NULL, NULL, 'cliente12@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(104, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 13', '5511915831413', NULL, NULL, 'cliente13@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(105, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 14', '5511918496901', NULL, NULL, 'cliente14@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(106, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 15', '5511918729627', NULL, NULL, 'cliente15@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(107, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 16', '5511921249121', NULL, NULL, 'cliente16@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(108, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 17', '5511932695739', NULL, NULL, 'cliente17@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(109, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 18', '5511932944848', NULL, NULL, 'cliente18@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(110, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 19', '5511944738051', NULL, NULL, 'cliente19@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(111, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 20', '5511948463187', NULL, NULL, 'cliente20@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(112, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 21', '5511949557884', NULL, NULL, 'cliente21@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(113, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 22', '5511949925848', NULL, NULL, 'cliente22@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(114, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 23', '5511953591641', NULL, NULL, 'cliente23@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(115, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 24', '5511953718363', NULL, NULL, 'cliente24@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(116, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 25', '5511953917117', NULL, NULL, 'cliente25@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(117, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 26', '5511956196510', NULL, NULL, 'cliente26@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(118, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 27', '5511958373837', NULL, NULL, 'cliente27@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(119, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 28', '5511958921011', NULL, NULL, 'cliente28@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(120, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 29', '5511960655039', NULL, NULL, 'cliente29@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(121, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 30', '5511961824095', NULL, NULL, 'cliente30@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(122, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 31', '5511962982678', NULL, NULL, 'cliente31@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(123, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 32', '5511963328335', NULL, NULL, 'cliente32@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(124, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 33', '5511964154044', NULL, NULL, 'cliente33@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(125, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 34', '5511964215395', NULL, NULL, 'cliente34@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(126, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 35', '5511964801246', NULL, NULL, 'cliente35@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(127, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 36', '5511968663146', NULL, NULL, 'cliente36@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(128, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 37', '5511969182740', NULL, NULL, 'cliente37@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(129, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 38', '5511970441750', NULL, NULL, 'cliente38@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(130, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 39', '5511970996724', NULL, NULL, 'cliente39@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(131, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 40', '5511971263857', NULL, NULL, 'cliente40@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(132, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 41', '5511977871702', NULL, NULL, 'cliente41@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(133, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 42', '5511979639244', NULL, NULL, 'cliente42@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(134, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 43', '5511980356180', NULL, NULL, 'cliente43@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(135, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 44', '5511983539266', NULL, NULL, 'cliente44@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(136, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 45', '5511985338257', NULL, NULL, 'cliente45@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(137, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 46', '5511988627998', NULL, NULL, 'cliente46@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(138, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 47', '5511989956204', NULL, NULL, 'cliente47@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(139, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 48', '5511995923624', NULL, NULL, 'cliente48@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(140, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 49', '5511998584274', NULL, NULL, 'cliente49@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(141, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 50', '5511999554805', NULL, NULL, 'cliente50@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(142, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 51', '5512991047855', NULL, NULL, 'cliente51@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(143, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 52', '5512992040465', NULL, NULL, 'cliente52@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(144, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 53', '5512992232370', NULL, NULL, 'cliente53@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(145, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 54', '5512992555310', NULL, NULL, 'cliente54@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(146, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 55', '5512996345426', NULL, NULL, 'cliente55@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(147, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 56', '5512996354318', NULL, NULL, 'cliente56@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(148, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 57', '5512996476459', NULL, NULL, 'cliente57@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(149, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 58', '5512996573765', NULL, NULL, 'cliente58@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(150, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 59', '5512997297304', NULL, NULL, 'cliente59@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(151, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 60', '5512981218120', NULL, NULL, 'cliente60@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(152, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 61', '5512981527194', NULL, NULL, 'cliente61@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(153, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 62', '5512983176576', NULL, NULL, 'cliente62@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(154, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 63', '5512987078327', NULL, NULL, 'cliente63@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(155, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 64', '5512988792443', NULL, NULL, 'cliente64@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(156, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 65', '5512978113849', NULL, NULL, 'cliente65@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(157, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 66', '5513981015082', NULL, NULL, 'cliente66@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(158, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 67', '5513996257829', NULL, NULL, 'cliente67@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(159, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 68', '5513996731803', NULL, NULL, 'cliente68@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(160, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 69', '5513996763504', NULL, NULL, 'cliente69@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(161, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 70', '5513996951735', NULL, NULL, 'cliente70@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(162, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 71', '5513955462710', NULL, NULL, 'cliente71@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(163, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 72', '5514997191466', NULL, NULL, 'cliente72@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(164, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 73', '5514998202367', NULL, NULL, 'cliente73@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(165, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 74', '5515981413328', NULL, NULL, 'cliente74@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(166, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 75', '5515996017798', NULL, NULL, 'cliente75@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(167, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 76', '5516996148072', NULL, NULL, 'cliente76@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(168, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 77', '5516997083324', NULL, NULL, 'cliente77@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(169, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 78', '5516997273867', NULL, NULL, 'cliente78@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(170, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 79', '5516997514688', NULL, NULL, 'cliente79@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(171, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 80', '5516999616160', NULL, NULL, 'cliente80@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(172, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 81', '5516981459556', NULL, NULL, 'cliente81@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(173, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 82', '5517991542300', NULL, NULL, 'cliente82@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(174, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 83', '5518981573793', NULL, NULL, 'cliente83@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(175, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 84', '5518996947640', NULL, NULL, 'cliente84@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(176, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 85', '5518997128711', NULL, NULL, 'cliente85@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(177, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 86', '5518997293587', NULL, NULL, 'cliente86@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(178, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 87', '551971615758', NULL, NULL, 'cliente87@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(179, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 88', '5519982374278', NULL, NULL, 'cliente88@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(180, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 89', '5519987273421', NULL, NULL, 'cliente89@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(181, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 90', '5519988222062', NULL, NULL, 'cliente90@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(182, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 91', '5519992770245', NULL, NULL, 'cliente91@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(183, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 92', '5519995114437', NULL, NULL, 'cliente92@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(184, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 93', '5519999174233', NULL, NULL, 'cliente93@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(185, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 94', '5519999684824', NULL, NULL, 'cliente94@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(186, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 95', '552120180367', NULL, NULL, 'cliente95@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(187, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 96', '552120181195', NULL, NULL, 'cliente96@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(188, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 97', '5521960206671', NULL, NULL, 'cliente97@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(189, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 98', '5521964553902', NULL, NULL, 'cliente98@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(190, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 99', '5521966000003', NULL, NULL, 'cliente99@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(191, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 100', '5521968798947', NULL, NULL, 'cliente100@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(192, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 101', '5521969900195', NULL, NULL, 'cliente101@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(193, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 102', '5521970728970', NULL, NULL, 'cliente102@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(194, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 103', '5521971167001', NULL, NULL, 'cliente103@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(195, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 104', '5521973395927', NULL, NULL, 'cliente104@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(196, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 105', '5521974491729', NULL, NULL, 'cliente105@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(197, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 106', '5521974614450', NULL, NULL, 'cliente106@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(198, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 107', '5521976057357', NULL, NULL, 'cliente107@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(199, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 108', '5521976661131', NULL, NULL, 'cliente108@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(200, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 109', '5521977517736', NULL, NULL, 'cliente109@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(201, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 110', '5521978967095', NULL, NULL, 'cliente110@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(202, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 111', '5521979050037', NULL, NULL, 'cliente111@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(203, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 112', '5521979619524', NULL, NULL, 'cliente112@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(204, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 113', '5521980813182', NULL, NULL, 'cliente113@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(205, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 114', '5521981835918', NULL, NULL, 'cliente114@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(206, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 115', '5521982066865', NULL, NULL, 'cliente115@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(207, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 116', '5521982323040', NULL, NULL, 'cliente116@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(208, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 117', '5521982411170', NULL, NULL, 'cliente117@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(209, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 118', '5521983246511', NULL, NULL, 'cliente118@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(210, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 119', '5521983529500', NULL, NULL, 'cliente119@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(211, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 120', '5521984305446', NULL, NULL, 'cliente120@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(212, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 121', '5521984530214', NULL, NULL, 'cliente121@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(213, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 122', '5521985815959', NULL, NULL, 'cliente122@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(214, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 123', '5521986736025', NULL, NULL, 'cliente123@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(215, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 124', '5521990256390', NULL, NULL, 'cliente124@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(216, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 125', '5521990248838', NULL, NULL, 'cliente125@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(217, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 126', '5521992582616', NULL, NULL, 'cliente126@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(218, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 127', '5521996251382', NULL, NULL, 'cliente127@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(219, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 128', '5521996828043', NULL, NULL, 'cliente128@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(220, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 129', '5521998645105', NULL, NULL, 'cliente129@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(221, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 130', '5521998734330', NULL, NULL, 'cliente130@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(222, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 131', '5522988209287', NULL, NULL, 'cliente131@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(223, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 132', '5522998411171', NULL, NULL, 'cliente132@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(224, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 133', '5522998899375', NULL, NULL, 'cliente133@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(225, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 134', '5522999236532', NULL, NULL, 'cliente134@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(226, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 135', '5524992937870', NULL, NULL, 'cliente135@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(227, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 136', '5524998215327', NULL, NULL, 'cliente136@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(228, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 137', '5524998483034', NULL, NULL, 'cliente137@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(229, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 138', '5524999363431', NULL, NULL, 'cliente138@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(230, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 139', '5524981247481', NULL, NULL, 'cliente139@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(231, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 140', '5524981298351', NULL, NULL, 'cliente140@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(232, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 141', '5524981302854', NULL, NULL, 'cliente141@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(233, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 142', '5527992661493', NULL, NULL, 'cliente142@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(234, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 143', '5527995215190', NULL, NULL, 'cliente143@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(235, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 144', '5527997054017', NULL, NULL, 'cliente144@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(236, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 145', '5527999889054', NULL, NULL, 'cliente145@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(237, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 146', '5528981157864', NULL, NULL, 'cliente146@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(238, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 147', '553172379354', NULL, NULL, 'cliente147@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(239, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 148', '553175126215', NULL, NULL, 'cliente148@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(240, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 149', '553182034554', NULL, NULL, 'cliente149@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(241, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 150', '553185631711', NULL, NULL, 'cliente150@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(242, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 151', '553187037096', NULL, NULL, 'cliente151@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(243, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 152', '553188818374', NULL, NULL, 'cliente152@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(244, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 153', '553189507830', NULL, NULL, 'cliente153@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(245, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 154', '553191255008', NULL, NULL, 'cliente154@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(246, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 155', '553191518837', NULL, NULL, 'cliente155@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(247, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 156', '553192438227', NULL, NULL, 'cliente156@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(248, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 157', '553194466638', NULL, NULL, 'cliente157@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(249, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 158', '553196894482', NULL, NULL, 'cliente158@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(250, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 159', '553291993314', NULL, NULL, 'cliente159@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(251, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 160', '553298411439', NULL, NULL, 'cliente160@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(252, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 161', '553299349801', NULL, NULL, 'cliente161@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(253, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 162', '553388897007', NULL, NULL, 'cliente162@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(254, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 163', '553591044921', NULL, NULL, 'cliente163@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(255, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 164', '553591203982', NULL, NULL, 'cliente164@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(256, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 165', '553592111523', NULL, NULL, 'cliente165@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(257, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 166', '553597322098', NULL, NULL, 'cliente166@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(258, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 167', '553796659129', NULL, NULL, 'cliente167@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(259, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 168', '553888700739', NULL, NULL, 'cliente168@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(260, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 169', '553898235163', NULL, NULL, 'cliente169@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(261, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 170', '553898906404', NULL, NULL, 'cliente170@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(262, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 171', '553898931337', NULL, NULL, 'cliente171@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(263, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 172', '553899203331', NULL, NULL, 'cliente172@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(264, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 173', '554187350181', NULL, NULL, 'cliente173@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(265, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 174', '554192547766', NULL, NULL, 'cliente174@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(266, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 175', '554195228485', NULL, NULL, 'cliente175@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(267, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 176', '554195969596', NULL, NULL, 'cliente176@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(268, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 177', '554198552069', NULL, NULL, 'cliente177@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(269, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 178', '554291428917', NULL, NULL, 'cliente178@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(270, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 179', '554299600387', NULL, NULL, 'cliente179@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(271, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 180', '554384530296', NULL, NULL, 'cliente180@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(272, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 181', '554388489334', NULL, NULL, 'cliente181@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(273, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 182', '554391795799', NULL, NULL, 'cliente182@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL);
INSERT INTO `clientes` (`Id`, `idm`, `idc`, `valor`, `entrada`, `vjurus`, `login`, `senha`, `tipo`, `status`, `nome`, `celular`, `nascimento`, `cpf`, `email`, `cep`, `endereco`, `numero`, `bairro`, `complemento`, `cidade`, `uf`, `rg`, `emissao`, `uf2`, `mae`, `id_asaas`, `foto`, `fiador_nome`, `fiador_whatsapp`, `imei`, `referencia_nome`, `referencia_whatsapp`) VALUES
(274, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 183', '554396267715', NULL, NULL, 'cliente183@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(275, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 184', '554791561251', NULL, NULL, 'cliente184@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(276, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 185', '554797072435', NULL, NULL, 'cliente185@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(277, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 186', '554797351512', NULL, NULL, 'cliente186@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(278, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 187', '554797385949', NULL, NULL, 'cliente187@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(279, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 188', '554884249863', NULL, NULL, 'cliente188@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(280, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 189', '555191061271', NULL, NULL, 'cliente189@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(281, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 190', '555195480148', NULL, NULL, 'cliente190@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(282, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 191', '555196131785', NULL, NULL, 'cliente191@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(283, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 192', '555196254263', NULL, NULL, 'cliente192@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(284, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 193', '555197222532', NULL, NULL, 'cliente193@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(285, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 194', '555197465069', NULL, NULL, 'cliente194@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(286, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 195', '555197522250', NULL, NULL, 'cliente195@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(287, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 196', '555198979426', NULL, NULL, 'cliente196@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(288, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 197', '555591071980', NULL, NULL, 'cliente197@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(289, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 198', '556140040001', NULL, NULL, 'cliente198@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(290, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 199', '556174028208', NULL, NULL, 'cliente199@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(291, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 200', '556181261098', NULL, NULL, 'cliente200@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(292, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 201', '556181262598', NULL, NULL, 'cliente201@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(293, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 202', '556181304923', NULL, NULL, 'cliente202@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(294, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 203', '556181754001', NULL, NULL, 'cliente203@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(295, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 204', '556183801956', NULL, NULL, 'cliente204@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(296, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 205', '556184470964', NULL, NULL, 'cliente205@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(297, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 206', '556191020712', NULL, NULL, 'cliente206@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(298, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 207', '556192272798', NULL, NULL, 'cliente207@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(299, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 208', '556192510181', NULL, NULL, 'cliente208@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(300, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 209', '556194920363', NULL, NULL, 'cliente209@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(301, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 210', '556195612433', NULL, NULL, 'cliente210@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(302, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 211', '556196420363', NULL, NULL, 'cliente211@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(303, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 212', '556196856392', NULL, NULL, 'cliente212@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(304, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 213', '556198231894', NULL, NULL, 'cliente213@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(305, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 214', '556198328054', NULL, NULL, 'cliente214@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(306, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 215', '556198365111', NULL, NULL, 'cliente215@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(307, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 216', '556198645068', NULL, NULL, 'cliente216@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(308, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 217', '556199651431', NULL, NULL, 'cliente217@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(309, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 219', '556232699115', NULL, NULL, 'cliente219@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(310, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 220', '556284637973', NULL, NULL, 'cliente220@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(311, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 221', '556291903777', NULL, NULL, 'cliente221@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(312, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 222', '556393110639', NULL, NULL, 'cliente222@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(313, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 223', '556592063308', NULL, NULL, 'cliente223@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(314, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 224', '556596395131', NULL, NULL, 'cliente224@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(315, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 225', '556596513170', NULL, NULL, 'cliente225@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(316, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 226', '556599902781', NULL, NULL, 'cliente226@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(317, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 227', '556696739552', NULL, NULL, 'cliente227@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(318, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 228', '556699311928', NULL, NULL, 'cliente228@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(319, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 229', '556781038490', NULL, NULL, 'cliente229@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(320, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'BRUNNO', '556781375036', '21/08/1984', '24546546454546', 'cliente230@email.com', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(321, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 231', '556781467768', NULL, NULL, 'cliente231@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(322, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 232', '556781529863', NULL, NULL, 'cliente232@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(323, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 233', '556792116385', NULL, NULL, 'cliente233@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(324, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 234', '556796289565', NULL, NULL, 'cliente234@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(325, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 235', '556796831162', NULL, NULL, 'cliente235@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(326, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 236', '556798104887', NULL, NULL, 'cliente236@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(327, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 237', '556798593081', NULL, NULL, 'cliente237@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(328, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 238', '556791001669', NULL, NULL, 'cliente238@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(329, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 239', '556799169673', NULL, NULL, 'cliente239@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(330, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 240', '556799713902', NULL, NULL, 'cliente240@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(331, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 241', '557193331944', NULL, NULL, 'cliente241@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(332, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 242', '557197235867', NULL, NULL, 'cliente242@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(333, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 243', '557199699572', NULL, NULL, 'cliente243@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(334, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 244', '557499013388', NULL, NULL, 'cliente244@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(335, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 245', '557499522948', NULL, NULL, 'cliente245@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(336, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 246', '557583663620', NULL, NULL, 'cliente246@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(337, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 247', '557788416493', NULL, NULL, 'cliente247@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(338, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 248', '557791099472', NULL, NULL, 'cliente248@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(339, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 249', '557791430033', NULL, NULL, 'cliente249@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(340, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 250', '557791896662', NULL, NULL, 'cliente250@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(341, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 251', '557792116385', NULL, NULL, 'cliente251@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(342, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 252', '557798104887', NULL, NULL, 'cliente252@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(343, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 253', '557799142495', NULL, NULL, 'cliente253@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(344, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 254', '557799350183', NULL, NULL, 'cliente254@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(345, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 255', '557799546186', NULL, NULL, 'cliente255@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(346, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 256', '557998407197', NULL, NULL, 'cliente256@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(347, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 257', '557998572590', NULL, NULL, 'cliente257@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(348, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 258', '557999893583', NULL, NULL, 'cliente258@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(349, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 259', '558181751679', NULL, NULL, 'cliente259@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(350, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 260', '558181916466', NULL, NULL, 'cliente260@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(351, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 261', '558185676678', NULL, NULL, 'cliente261@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(352, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 262', '558193530800', NULL, NULL, 'cliente262@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(353, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 263', '558194666859', NULL, NULL, 'cliente263@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(354, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 264', '558281447746', NULL, NULL, 'cliente264@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(355, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 265', '558282186549', NULL, NULL, 'cliente265@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(356, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 266', '558293452817', NULL, NULL, 'cliente266@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(357, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 267', '558296235125', NULL, NULL, 'cliente267@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(358, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 268', '558382205230', NULL, NULL, 'cliente268@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(359, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 269', '558396048575', NULL, NULL, 'cliente269@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(360, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 270', '558398928307', NULL, NULL, 'cliente270@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(361, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 271', '558399382774', NULL, NULL, 'cliente271@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(362, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 272', '558496322765', NULL, NULL, 'cliente272@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(363, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 273', '558496555317', NULL, NULL, 'cliente273@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(364, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 274', '558581701003', NULL, NULL, 'cliente274@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(365, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 275', '558582101666', NULL, NULL, 'cliente275@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(366, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 276', '558584353373', NULL, NULL, 'cliente276@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(367, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 277', '558586206065', NULL, NULL, 'cliente277@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(368, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 278', '558587962884', NULL, NULL, 'cliente278@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(369, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 279', '558588603301', NULL, NULL, 'cliente279@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(370, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 280', '558596505156', NULL, NULL, 'cliente280@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(371, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 281', '558698221504', NULL, NULL, 'cliente281@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(372, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 282', '558699646212', NULL, NULL, 'cliente282@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(373, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 283', '558981016040', NULL, NULL, 'cliente283@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(374, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 284', '559180237923', NULL, NULL, 'cliente284@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(375, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 285', '559180431097', NULL, NULL, 'cliente285@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(376, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 286', '559180798798', NULL, NULL, 'cliente286@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(377, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 287', '559182507114', NULL, NULL, 'cliente287@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(378, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 288', '559183907627', NULL, NULL, 'cliente288@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(379, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 289', '559188971173', NULL, NULL, 'cliente289@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(380, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 290', '559191687515', NULL, NULL, 'cliente290@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(381, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 291', '559191784237', NULL, NULL, 'cliente291@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(382, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 292', '559192479818', NULL, NULL, 'cliente292@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(383, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 293', '559192541996', NULL, NULL, 'cliente293@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(384, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 294', '559193671291', NULL, NULL, 'cliente294@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(385, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 295', '559193699254', NULL, NULL, 'cliente295@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(386, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 296', '559198399739', NULL, NULL, 'cliente296@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(387, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 297', '559281294125', NULL, NULL, 'cliente297@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(388, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 298', '559281556798', NULL, NULL, 'cliente298@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(389, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 299', '559285998235', NULL, NULL, 'cliente299@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(390, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 300', '559288522985', NULL, NULL, 'cliente300@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(391, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 301', '559291736042', NULL, NULL, 'cliente301@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(392, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 302', '559381233124', NULL, NULL, 'cliente302@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(393, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 303', '559391380954', NULL, NULL, 'cliente303@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(394, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 304', '559481155901', NULL, NULL, 'cliente304@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(395, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 305', '559491192533', NULL, NULL, 'cliente305@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(396, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 306', '559492510081', NULL, NULL, 'cliente306@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(397, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 307', '559884266276', NULL, NULL, 'cliente307@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(398, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 308', '559981399922', NULL, NULL, 'cliente308@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(399, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 309', '559988121037', NULL, NULL, 'cliente309@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(400, '1', '41', 0.00, '2025-12-18 20:57:42', '100', NULL, NULL, '1', '1', 'CLIENTE 310', '9779820755849', NULL, NULL, 'cliente310@email.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(422, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'MEGA ILIMITADA', '558193987080', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(423, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Danyelle', '61995712815', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(424, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Aliancas Palladium', '36292986', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(425, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Antônio Pedreiro', '556196334269', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(426, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Dentista Dany', '996317133', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(427, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Jesus Marcelo', '5519987694285', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(428, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Anderson', '557398242475', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(429, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Internet Il', '559999330217', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(430, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Gusmao', '5511968992087', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(431, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Cláudio', '558281656540', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(432, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Misael', '99269951', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(433, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Nos38', '555193608503', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(434, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Nos242', '5519981747561', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(435, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Nos32', '55613534904382', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(436, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Nos250', '5511910384197', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(437, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Nos247', '5511964801246', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(438, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Nos249', '5511933628050', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(439, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Nos243', '5512988982513', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(440, '81', '45', 0.00, '2025-12-18 23:53:43', '100', NULL, NULL, '1', '1', 'Nos33', '554999409307', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'uploads/clientes/padrao.png', NULL, NULL, NULL, NULL, NULL),
(441, '1', '42', 0.00, '2025-12-19 02:16:15', '100', NULL, NULL, '1', '1', 'MARCELO IPTV', '6596915592', '2025-12-19', '', '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, '', '', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `conexoes`
--

CREATE TABLE `conexoes` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `qrcode` text DEFAULT NULL,
  `conn` int(11) DEFAULT 0,
  `data_cadastro` timestamp NOT NULL DEFAULT current_timestamp(),
  `data_alteracao` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
  `tokenid` varchar(60) DEFAULT NULL,
  `apikey` varchar(60) DEFAULT '0',
  `servidor_id` tinyint(1) DEFAULT 1,
  `instancia_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `conexoes`
--

INSERT INTO `conexoes` (`id`, `id_usuario`, `qrcode`, `conn`, `data_cadastro`, `data_alteracao`, `tokenid`, `apikey`, `servidor_id`, `instancia_id`) VALUES
(2, 80, NULL, 0, '2025-12-18 16:13:16', NULL, 'e276178ffcc7ce2a94110f5c9e3cedd6', '0', 1, NULL),
(3, 81, NULL, 0, '2025-12-18 16:56:35', NULL, '2b465b21d40e847a345c010dd7214fcf', '0', 1, NULL),
(4, 1, 'data:image/png;base64,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', 1, '2025-12-18 20:59:38', '2025-12-18 22:32:02', NULL, 'c5dddaa86bf15c3aeddf278913cd3911', 1, 2),
(5, 82, NULL, 0, '2025-12-18 22:06:28', NULL, 'b081bc2732f81196f74b54cdc1ecae39', '0', 1, NULL),
(6, 83, NULL, 0, '2025-12-18 22:10:07', NULL, 'b3895ab5513d9dd7b0c479af8cd267f1', '0', 1, NULL),
(7, 84, NULL, 0, '2025-12-18 22:28:18', NULL, 'c1e1516699b74c1e749904c2e8176bc2', '0', 1, NULL),
(10, 81, 'data:image/png;base64,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', 0, '2025-12-19 00:00:41', '2025-12-19 00:00:53', NULL, 'd2833005c43c5cf181dceea17035edd1', 1, 5),
(11, 85, NULL, 0, '2025-12-19 03:00:08', NULL, 'b13c4738818bd3d5abbbdab722359d20', '0', 1, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `contas_pagar`
--

CREATE TABLE `contas_pagar` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fornecedor` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `categoria` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `forma_pagamento` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parcelas` int(11) DEFAULT 1,
  `parcela_atual` int(11) DEFAULT 1,
  `juros` decimal(10,2) DEFAULT 0.00,
  `multa` decimal(10,2) DEFAULT 0.00,
  `datavencimento` date NOT NULL,
  `datapagamento` date DEFAULT NULL,
  `status` enum('Pendente','Pago','Atrasado') COLLATE utf8mb4_unicode_ci DEFAULT 'Pendente',
  `observacao` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disparo_enviado`
--

CREATE TABLE `disparo_enviado` (
  `id` int(11) NOT NULL,
  `id_agendamento` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `enviado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `disparo_msg`
--

CREATE TABLE `disparo_msg` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `titulo` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `conteudo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `arquivo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `disparo_msg`
--

INSERT INTO `disparo_msg` (`id`, `id_usuario`, `titulo`, `conteudo`, `arquivo`, `data_criacao`) VALUES
(1, 1, 'Sem Título', 'dsfsdfsdf', '', '2025-12-18 16:45:12'),
(2, 1, 'Sem Título', 'Njdkdmss', '', '2025-12-18 18:27:46');

-- --------------------------------------------------------

--
-- Estrutura para tabela `email_config`
--

CREATE TABLE `email_config` (
  `id` int(11) NOT NULL,
  `smtp_host` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_port` int(11) DEFAULT 587,
  `smtp_username` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_encryption` enum('tls','ssl','none') COLLATE utf8mb4_unicode_ci DEFAULT 'tls',
  `from_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verification_enabled` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `email_config`
--

INSERT INTO `email_config` (`id`, `smtp_host`, `smtp_port`, `smtp_username`, `smtp_password`, `smtp_encryption`, `from_email`, `from_name`, `email_verification_enabled`, `created_at`, `updated_at`) VALUES
(1, '', 587, '', '', 'tls', '', '', 0, '2025-12-15 12:40:52', '2025-12-18 21:45:25');

-- --------------------------------------------------------

--
-- Estrutura para tabela `email_logs`
--

CREATE TABLE `email_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('verification','welcome','reset_password','password_reset') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('sent','failed','bounced') COLLATE utf8mb4_unicode_ci DEFAULT 'sent',
  `sent_at` timestamp NULL DEFAULT current_timestamp(),
  `error_message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro1`
--

CREATE TABLE `financeiro1` (
  `Id` int(11) NOT NULL,
  `idc` varchar(5) DEFAULT 'n',
  `idm` varchar(9) DEFAULT NULL,
  `idcob` varchar(5) DEFAULT 'n',
  `valorsolicitado` decimal(10,2) DEFAULT 0.00,
  `taxaj` varchar(5) DEFAULT 'n',
  `valorjurus` decimal(10,2) DEFAULT 0.00,
  `valorfinal` decimal(10,2) DEFAULT NULL,
  `formapagamento` varchar(3) DEFAULT 'n',
  `parcelas` varchar(3) DEFAULT 'n',
  `primeiraparcela` varchar(20) DEFAULT 'n',
  `chave` varchar(60) DEFAULT 'n',
  `status` varchar(2) DEFAULT '1',
  `vparcela` decimal(10,2) DEFAULT 0.00,
  `pagoem` varchar(255) DEFAULT 'n',
  `entrada` varchar(15) DEFAULT 'n',
  `gatewayPayment` int(6) DEFAULT 1,
  `produtoId` varchar(255) DEFAULT NULL,
  `ciclo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `financeiro1`
--

INSERT INTO `financeiro1` (`Id`, `idc`, `idm`, `idcob`, `valorsolicitado`, `taxaj`, `valorjurus`, `valorfinal`, `formapagamento`, `parcelas`, `primeiraparcela`, `chave`, `status`, `vparcela`, `pagoem`, `entrada`, `gatewayPayment`, `produtoId`, `ciclo_id`) VALUES
(338, '91', '1', 'n', 0.00, 'n', 0.00, 50.00, '30', '12', '19/01/2026', 'c574bb6880383fec0bb8431350c54433', '1', 50.00, 'n', '18/12/2025', 1, NULL, 3),
(340, '423', '81', 'n', 0.00, 'n', 0.00, 40.00, '30', '12', '31/12/2025', '0bc3d5a5ec3f1c5b7a7b684db6d25257', '1', 40.00, 'n', '18/12/2025', 1, NULL, 3),
(341, '441', '1', 'n', 0.00, 'n', 0.00, 20.00, '30', '12', '20/01/2026', 'dbe90371058b1fe8be183db1e0abb3fd', '1', 20.00, 'n', '18/12/2025', 1, NULL, 3),
(342, '320', '1', 'n', 0.00, 'n', 0.00, 25.00, '7', '12', '19/01/2026', '97ad2d3a17ed4e2c2e354c1f6e51884e', '1', 25.00, 'n', '19/12/2025', 1, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro2`
--

CREATE TABLE `financeiro2` (
  `Id` int(11) NOT NULL,
  `idc` varchar(9) DEFAULT NULL,
  `idm` varchar(9) DEFAULT NULL,
  `chave` varchar(60) DEFAULT 'n',
  `parcela` decimal(10,2) NOT NULL,
  `datapagamento` varchar(20) DEFAULT 'n',
  `pagoem` varchar(20) DEFAULT 'n',
  `status` varchar(2) DEFAULT '1',
  `tempo` varchar(2) DEFAULT '2',
  `temp5` varchar(2) DEFAULT '2',
  `temp3` varchar(2) DEFAULT '2',
  `temp0` varchar(2) DEFAULT '2',
  `obsv` text DEFAULT NULL,
  `juros_calculados` int(11) DEFAULT 0,
  `taxa_juros_diaria` decimal(10,2) DEFAULT 0.00,
  `dias_vencidos` int(11) DEFAULT 0,
  `gatewayPayment` int(6) DEFAULT 1,
  `produtoId` varchar(255) DEFAULT NULL,
  `ciclo_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `financeiro2`
--

INSERT INTO `financeiro2` (`Id`, `idc`, `idm`, `chave`, `parcela`, `datapagamento`, `pagoem`, `status`, `tempo`, `temp5`, `temp3`, `temp0`, `obsv`, `juros_calculados`, `taxa_juros_diaria`, `dias_vencidos`, `gatewayPayment`, `produtoId`, `ciclo_id`) VALUES
(952, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/01/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(953, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/02/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(954, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/03/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(955, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/04/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(956, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/05/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(957, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/06/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(958, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/07/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(959, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/08/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(960, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/09/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(961, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/10/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(962, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/11/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(963, '91', '1', 'c574bb6880383fec0bb8431350c54433', 50.00, '19/12/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(976, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/12/2025', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(977, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/01/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(978, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/03/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(979, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/03/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(980, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/05/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(981, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/05/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(982, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/07/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(983, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/07/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(984, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/08/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(985, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/10/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(986, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/10/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(987, '423', '81', '0bc3d5a5ec3f1c5b7a7b684db6d25257', 40.00, '31/12/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(988, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/01/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(989, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/02/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(990, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/03/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(991, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/04/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(992, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/05/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(993, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/06/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(994, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/07/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(995, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/08/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(996, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/09/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(997, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/10/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(998, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/11/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(999, '441', '1', 'dbe90371058b1fe8be183db1e0abb3fd', 20.00, '20/12/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 3),
(1000, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '19/01/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1001, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '26/01/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1002, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '02/02/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1003, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '09/02/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1004, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '16/02/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1005, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '23/02/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1006, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '02/03/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1007, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '09/03/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1008, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '16/03/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1009, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '23/03/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1010, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '30/03/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1),
(1011, '320', '1', '97ad2d3a17ed4e2c2e354c1f6e51884e', 25.00, '06/04/2026', 'n', '1', '2', '2', '2', '2', NULL, 0, 0.00, 0, 1, NULL, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro3`
--

CREATE TABLE `financeiro3` (
  `id` int(11) NOT NULL,
  `idm` varchar(9) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `dataentrada` datetime DEFAULT current_timestamp(),
  `valor` decimal(10,2) DEFAULT NULL,
  `datavencimento` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `datapagamento` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` varchar(2) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `descricao` varchar(120) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `observacao` text COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `tipo` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT 'saida',
  `categoria` varchar(100) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `recorrente` tinyint(1) DEFAULT 0,
  `frequencia` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `data_final` date DEFAULT NULL,
  `origem_recorrente_id` int(11) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL COMMENT 'ID da categoria financeira vinculada'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Despejando dados para a tabela `financeiro3`
--

INSERT INTO `financeiro3` (`id`, `idm`, `dataentrada`, `valor`, `datavencimento`, `datapagamento`, `status`, `descricao`, `observacao`, `tipo`, `categoria`, `recorrente`, `frequencia`, `data_final`, `origem_recorrente_id`, `categoria_id`) VALUES
(23, '1', '2025-12-15 09:53:25', 5.00, '2025-12-15', NULL, '0', 'teste', NULL, 'saida', 'Aluguel', 1, 'mensal', NULL, NULL, 1),
(24, '1', '2025-12-15 09:53:25', 5.00, '2026-01-15', NULL, '0', 'teste', NULL, 'saida', 'Aluguel', 1, 'mensal', NULL, 23, 1),
(25, '1', '2025-12-15 09:53:25', 5.00, '2026-02-15', NULL, '0', 'teste', NULL, 'saida', 'Aluguel', 1, 'mensal', NULL, 23, 1),
(26, '1', '2025-12-15 09:53:25', 5.00, '2026-03-15', NULL, '0', 'teste', NULL, 'saida', 'Aluguel', 1, 'mensal', NULL, 23, 1),
(27, '1', '2025-12-15 09:53:25', 5.00, '2026-04-15', NULL, '0', 'teste', NULL, 'saida', 'Aluguel', 1, 'mensal', NULL, 23, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_global`
--

CREATE TABLE `financeiro_global` (
  `id` int(6) UNSIGNED NOT NULL,
  `idc` int(6) NOT NULL,
  `idm` int(6) NOT NULL,
  `instancia` varchar(60) NOT NULL,
  `valorparcela` decimal(10,2) NOT NULL,
  `parcelas` int(6) NOT NULL,
  `gatewayPayment` int(6) NOT NULL,
  `type` varchar(20) NOT NULL,
  `copiacola` text DEFAULT NULL,
  `codigobarra` text DEFAULT NULL,
  `imagemQrcode` text DEFAULT NULL,
  `id_payment` varchar(255) NOT NULL,
  `status_payment` varchar(255) NOT NULL,
  `entrada` date NOT NULL,
  `vencimento_cron` varchar(255) DEFAULT NULL,
  `link_pagamento` varchar(255) DEFAULT NULL COMMENT 'Hash único do link de pagamento',
  `link_criado_em` datetime DEFAULT NULL COMMENT 'Data de criação do link',
  `link_acessos` int(11) DEFAULT 0 COMMENT 'Total de acessos ao link',
  `link_ultimo_acesso` datetime DEFAULT NULL COMMENT 'Último acesso ao link'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `financeiro_global`
--

INSERT INTO `financeiro_global` (`id`, `idc`, `idm`, `instancia`, `valorparcela`, `parcelas`, `gatewayPayment`, `type`, `copiacola`, `codigobarra`, `imagemQrcode`, `id_payment`, `status_payment`, `entrada`, `vencimento_cron`, `link_pagamento`, `link_criado_em`, `link_acessos`, `link_ultimo_acesso`) VALUES
(84, 91, 1, '952', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/01/2026', NULL, NULL, 0, NULL),
(85, 91, 1, '953', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/02/2026', NULL, NULL, 0, NULL),
(86, 91, 1, '954', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/03/2026', NULL, NULL, 0, NULL),
(87, 91, 1, '955', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/04/2026', NULL, NULL, 0, NULL),
(88, 91, 1, '956', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/05/2026', NULL, NULL, 0, NULL),
(89, 91, 1, '957', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/06/2026', NULL, NULL, 0, NULL),
(90, 91, 1, '958', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/07/2026', NULL, NULL, 0, NULL),
(91, 91, 1, '959', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/08/2026', NULL, NULL, 0, NULL),
(92, 91, 1, '960', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/09/2026', NULL, NULL, 0, NULL),
(93, 91, 1, '961', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/10/2026', NULL, NULL, 0, NULL),
(94, 91, 1, '962', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/11/2026', NULL, NULL, 0, NULL),
(95, 91, 1, '963', 50.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '19/12/2026', NULL, NULL, 0, NULL),
(108, 423, 81, '976', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/12/2025', NULL, NULL, 0, NULL),
(109, 423, 81, '977', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/01/2026', NULL, NULL, 0, NULL),
(110, 423, 81, '978', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/03/2026', NULL, NULL, 0, NULL),
(111, 423, 81, '979', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/03/2026', NULL, NULL, 0, NULL),
(112, 423, 81, '980', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/05/2026', NULL, NULL, 0, NULL),
(113, 423, 81, '981', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/05/2026', NULL, NULL, 0, NULL),
(114, 423, 81, '982', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/07/2026', NULL, NULL, 0, NULL),
(115, 423, 81, '983', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/07/2026', NULL, NULL, 0, NULL),
(116, 423, 81, '984', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/08/2026', NULL, NULL, 0, NULL),
(117, 423, 81, '985', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/10/2026', NULL, NULL, 0, NULL),
(118, 423, 81, '986', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/10/2026', NULL, NULL, 0, NULL),
(119, 423, 81, '987', 40.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '31/12/2026', NULL, NULL, 0, NULL),
(120, 441, 1, '988', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/01/2026', 'ed003f7b59982d8437182cb7da05a6fa257a888a51c6e2dd9e0d7b9989c3a7ae', '2025-12-18 23:18:37', 1, '2025-12-18 23:18:37'),
(121, 441, 1, '989', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/02/2026', NULL, NULL, 0, NULL),
(122, 441, 1, '990', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/03/2026', NULL, NULL, 0, NULL),
(123, 441, 1, '991', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/04/2026', NULL, NULL, 0, NULL),
(124, 441, 1, '992', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/05/2026', NULL, NULL, 0, NULL),
(125, 441, 1, '993', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/06/2026', NULL, NULL, 0, NULL),
(126, 441, 1, '994', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/07/2026', NULL, NULL, 0, NULL),
(127, 441, 1, '995', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/08/2026', NULL, NULL, 0, NULL),
(128, 441, 1, '996', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/09/2026', NULL, NULL, 0, NULL),
(129, 441, 1, '997', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/10/2026', NULL, NULL, 0, NULL),
(130, 441, 1, '998', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/11/2026', NULL, NULL, 0, NULL),
(131, 441, 1, '999', 20.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-18', '20/12/2026', NULL, NULL, 0, NULL),
(132, 320, 1, '1000', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '19/01/2026', NULL, NULL, 0, NULL),
(133, 320, 1, '1001', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '26/01/2026', NULL, NULL, 0, NULL),
(134, 320, 1, '1002', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '02/02/2026', NULL, NULL, 0, NULL),
(135, 320, 1, '1003', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '09/02/2026', NULL, NULL, 0, NULL),
(136, 320, 1, '1004', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '16/02/2026', NULL, NULL, 0, NULL),
(137, 320, 1, '1005', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '23/02/2026', NULL, NULL, 0, NULL),
(138, 320, 1, '1006', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '02/03/2026', NULL, NULL, 0, NULL),
(139, 320, 1, '1007', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '09/03/2026', NULL, NULL, 0, NULL),
(140, 320, 1, '1008', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '16/03/2026', NULL, NULL, 0, NULL),
(141, 320, 1, '1009', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '23/03/2026', NULL, NULL, 0, NULL),
(142, 320, 1, '1010', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '30/03/2026', NULL, NULL, 0, NULL),
(143, 320, 1, '1011', 25.00, 12, 1, 'pix', '', '', '', '', 'pending', '2025-12-19', '06/04/2026', NULL, NULL, 0, NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `financeiro_parciais`
--

CREATE TABLE `financeiro_parciais` (
  `id` int(11) UNSIGNED NOT NULL,
  `idc` int(11) NOT NULL COMMENT 'ID do cliente',
  `idm` int(11) NOT NULL COMMENT 'ID da empresa/instância',
  `parcela_id` int(11) NOT NULL COMMENT 'ID da parcela em financeiro2',
  `chave` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chave da cobrança',
  `valor_original` decimal(10,2) NOT NULL COMMENT 'Valor original da parcela',
  `valor_pago` decimal(10,2) NOT NULL COMMENT 'Valor que foi pago nesta transação',
  `valor_restante` decimal(10,2) NOT NULL COMMENT 'Valor que ainda falta pagar',
  `data_pagamento` datetime DEFAULT current_timestamp() COMMENT 'Data e hora do pagamento',
  `tipo_pagamento` enum('parcial','total') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'parcial' COMMENT 'Tipo do pagamento',
  `status_pagamento` enum('pending','approved','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending' COMMENT 'Status do pagamento',
  `gateway_payment` int(6) DEFAULT 1 COMMENT 'Gateway de pagamento usado',
  `observacao` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observações sobre o pagamento',
  `criado_em` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Tabela para rastrear pagamentos parciais de parcelas';

-- --------------------------------------------------------

--
-- Estrutura para tabela `formas_pagamento`
--

CREATE TABLE `formas_pagamento` (
  `id` int(11) UNSIGNED NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `criado_em` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedores`
--

CREATE TABLE `fornecedores` (
  `id` int(11) NOT NULL,
  `idm` int(11) NOT NULL,
  `nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `cnpj` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criado_em` datetime DEFAULT current_timestamp(),
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `fornecedores_gast`
--

CREATE TABLE `fornecedores_gast` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nome_empresa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nome_representante` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cnpj` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `endereco` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cep` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rua` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bairro` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cidade` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contato` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `whatsapp` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `data_criacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Fornecedores para controle de gastos';

-- --------------------------------------------------------

--
-- Estrutura para tabela `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id` int(11) NOT NULL,
  `id_saas` int(11) NOT NULL,
  `nome` varchar(160) COLLATE utf8mb4_unicode_ci NOT NULL,
  `celular` varchar(11) COLLATE utf8mb4_unicode_ci NOT NULL,
  `login` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `senha` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `funcoes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roles` enum('superadmin','master','funcionario') COLLATE utf8mb4_unicode_ci DEFAULT 'funcionario',
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `porcentagem_vendas` decimal(5,2) DEFAULT 0.00 COMMENT 'Porcentagem de comissão sobre vendas'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `funcionarios`
--

INSERT INTO `funcionarios` (`id`, `id_saas`, `nome`, `celular`, `login`, `senha`, `funcoes`, `roles`, `data_criacao`, `porcentagem_vendas`) VALUES
(1, 1, 'ALEX', '41988150812', 'admin@plwdesign.online', 'b73b2d75e49f7c7ec5d7748bd0e06550fed238dd', 'contas_pagar,finalizados', 'funcionario', '2025-12-15 12:52:27', 1.00);

-- --------------------------------------------------------

--
-- Estrutura para tabela `indicacoes`
--

CREATE TABLE `indicacoes` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_usuario_indicador` int(11) NOT NULL COMMENT 'ID do usuário que fez a indicação',
  `id_usuario_indicado` int(11) NOT NULL COMMENT 'ID do usuário indicado (novo usuário SaaS)',
  `codigo_indicacao` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código único da indicação',
  `link_indicacao` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Link único de indicação',
  `status` enum('pendente','ativa','concluida','cancelada') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente' COMMENT 'Status da indicação',
  `pontos_ganhos` decimal(10,2) DEFAULT 0.00 COMMENT 'Pontos ganhos (em formato dinheiro)',
  `ip_origem` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP de origem da indicação',
  `data_indicacao` timestamp NULL DEFAULT current_timestamp() COMMENT 'Data da indicação',
  `data_ativacao` timestamp NULL DEFAULT NULL COMMENT 'Data de ativação do usuário indicado',
  `observacoes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observações sobre a indicação'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de indicações de usuários SaaS';

-- --------------------------------------------------------

--
-- Estrutura para tabela `indicacoes_config`
--

CREATE TABLE `indicacoes_config` (
  `id` int(11) UNSIGNED NOT NULL,
  `chave` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Chave da configuração',
  `valor` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Valor da configuração',
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT 'string' COMMENT 'Tipo do valor (string, number, boolean, json)',
  `descricao` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrição da configuração',
  `atualizado_em` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Data da última atualização'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Configurações do sistema de indicação (admin)';

--
-- Despejando dados para a tabela `indicacoes_config`
--

INSERT INTO `indicacoes_config` (`id`, `chave`, `valor`, `tipo`, `descricao`, `atualizado_em`) VALUES
(1, 'pontos_por_indicacao', '5.00', 'number', 'Pontos ganhos por cada indicação ativa (em R$)', '2025-12-18 15:30:41'),
(2, 'sistema_ativo', '1', 'boolean', 'Sistema de indicação ativo (1=sim, 0=não)', '2025-12-15 12:40:53'),
(3, 'expiracao_pontos_meses', '12', 'number', 'Meses para expiração dos pontos', '2025-12-15 12:40:53'),
(4, 'minimo_resgate', '50.00', 'number', 'Valor mínimo para resgate de pontos', '2025-12-15 12:40:53');

-- --------------------------------------------------------

--
-- Estrutura para tabela `indicacoes_movimentacao`
--

CREATE TABLE `indicacoes_movimentacao` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) NOT NULL COMMENT 'ID do usuário',
  `id_indicacao` int(11) UNSIGNED DEFAULT NULL COMMENT 'ID da indicação relacionada',
  `tipo` enum('credito','debito','resgate','expiracao') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de movimentação',
  `valor` decimal(10,2) NOT NULL COMMENT 'Valor da movimentação',
  `saldo_anterior` decimal(10,2) NOT NULL COMMENT 'Saldo antes da movimentação',
  `saldo_atual` decimal(10,2) NOT NULL COMMENT 'Saldo após a movimentação',
  `descricao` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Descrição da movimentação',
  `data_movimentacao` timestamp NULL DEFAULT current_timestamp() COMMENT 'Data da movimentação'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de movimentações de pontos/dinheiro';

-- --------------------------------------------------------

--
-- Estrutura para tabela `indicacoes_saldo`
--

CREATE TABLE `indicacoes_saldo` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_usuario` int(11) NOT NULL COMMENT 'ID do usuário',
  `codigo_unico` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código único de indicação do usuário',
  `saldo_total` decimal(10,2) DEFAULT 0.00 COMMENT 'Saldo total de pontos (em formato dinheiro)',
  `saldo_disponivel` decimal(10,2) DEFAULT 0.00 COMMENT 'Saldo disponível para uso',
  `saldo_usado` decimal(10,2) DEFAULT 0.00 COMMENT 'Saldo já utilizado',
  `total_indicacoes` int(11) DEFAULT 0 COMMENT 'Total de indicações realizadas',
  `indicacoes_ativas` int(11) DEFAULT 0 COMMENT 'Total de indicações ativas',
  `indicacoes_concluidas` int(11) DEFAULT 0 COMMENT 'Total de indicações concluídas',
  `data_criacao` timestamp NULL DEFAULT current_timestamp() COMMENT 'Data de criação do registro',
  `data_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Data da última atualização'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Saldo de pontos/dinheiro dos usuários do sistema de indicação';

--
-- Despejando dados para a tabela `indicacoes_saldo`
--

INSERT INTO `indicacoes_saldo` (`id`, `id_usuario`, `codigo_unico`, `saldo_total`, `saldo_disponivel`, `saldo_usado`, `total_indicacoes`, `indicacoes_ativas`, `indicacoes_concluidas`, `data_criacao`, `data_atualizacao`) VALUES
(1, 1, '7CD7A8EEDC', 0.00, 0.00, 0.00, 0, 0, 0, '2025-12-15 12:49:25', '2025-12-15 12:49:25'),
(2, 81, '45919D2F35', 0.00, 0.00, 0.00, 0, 0, 0, '2025-12-18 17:39:07', '2025-12-18 17:39:07'),
(3, 82, '91CAD8B6B4', 0.00, 0.00, 0.00, 0, 0, 0, '2025-12-18 22:11:32', '2025-12-18 22:11:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `instancias_uso`
--

CREATE TABLE `instancias_uso` (
  `id` int(11) NOT NULL,
  `instancia_id` int(11) NOT NULL,
  `data` date NOT NULL,
  `enviados_total` int(11) DEFAULT 0,
  `enviados_por_minuto` int(11) DEFAULT 0,
  `ultima_atualizacao` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `instancias_usuario`
--

CREATE TABLE `instancias_usuario` (
  `id` int(11) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `nome` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `servidor_id` tinyint(1) NOT NULL,
  `apikey` varchar(60) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `tokenid` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ativo` tinyint(1) DEFAULT 1,
  `padrao` tinyint(1) DEFAULT 0,
  `prioridade` int(11) DEFAULT 1,
  `limite_diario` int(11) DEFAULT 1000,
  `limite_minuto` int(11) DEFAULT 50,
  `data_criacao` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `instancias_usuario`
--

INSERT INTO `instancias_usuario` (`id`, `id_usuario`, `nome`, `servidor_id`, `apikey`, `tokenid`, `ativo`, `padrao`, `prioridade`, `limite_diario`, `limite_minuto`, `data_criacao`) VALUES
(2, 1, 'SuperNet2', 1, 'c5dddaa86bf15c3aeddf278913cd3911', NULL, 1, 1, 3, 1000, 50, '2025-12-18 20:59:38'),
(5, 81, 'Mega', 1, 'd2833005c43c5cf181dceea17035edd1', NULL, 1, 1, 3, 1000, 50, '2025-12-19 00:00:41');

-- --------------------------------------------------------

--
-- Estrutura para tabela `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `data` varchar(20) COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `status` varchar(2) COLLATE utf8mb3_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `lojas_woocommerce`
--

CREATE TABLE `lojas_woocommerce` (
  `id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `source_id` varchar(50) NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `tokenapi` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `lojas_woocommerce`
--

INSERT INTO `lojas_woocommerce` (`id`, `nome`, `source_id`, `id_usuario`, `tokenapi`, `created_at`) VALUES
(1, 'internet', 'f6b778b20ba24168', 1, 'c5dddaa86bf15c3aeddf278913cd3911', '2025-12-18 19:41:45');

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens`
--

CREATE TABLE `mensagens` (
  `id` int(11) NOT NULL,
  `idu` varchar(5) DEFAULT NULL,
  `msg` text DEFAULT NULL,
  `tipo` varchar(2) DEFAULT NULL,
  `status` varchar(2) DEFAULT '1',
  `hora` varchar(5) DEFAULT '16:00',
  `dias` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `mensagens`
--

INSERT INTO `mensagens` (`id`, `idu`, `msg`, `tipo`, `status`, `hora`, `dias`) VALUES
(1, '1', 'Olá *#NOME#*, Passando para informar, Sua fatura *Fibra*.\n*R$: #VALOR#* já está disponível para pagamento.\nQue vence em #VENCIMENTO#', '1', '1', '09:22', NULL),
(2, '1', 'Olá, #NOME#, Sua fatura *Fibra*\nNo total de #VALOR#\nQue vence em #VENCIMENTO#\n*Pix Copia e Cola:*', '2', '1', '22:35', NULL),
(3, '1', 'Olá *#NOME#*,\n\nPassando para informar, Sua fatura *Fibra*.\n*R$: #VALOR#* já está disponível para pagamento.\nQue vence em #VENCIMENTO#\n\nChave Pix *Copia e Cola* logo abaixo.\n\nBasta copiar a chave e abrir seu aplicativo para realizar o pagamento.\n\n⚠Enviar comprovante para anexo no sistema.⚠', '3', '1', '19:07', 1),
(4, '1', 'Olá *#NOME#*,\n\nPassando para informar que sua mensalidade no valor de:\n\nSua fatura *Fibra*.\n*R$: #VALOR#* já está disponível para pagamento.\nQue venceu em #VENCIMENTO#\n\nChave Pix *Copia e Cola* logo abaixo.\n\nBasta copiar a chave e abrir seu aplicativo para realizar o pagamento.\n\n⚠Enviar comprovante para anexo no sistema.⚠', '4', '1', '18:52', 1),
(5, '1', '✅ RECIBO DE PAGAMENTO\n=======================\n➡Cliente: #NOME#\n=======================\n➡Data de Vencimento: #VENCIMENTO#\n=======================\n➡Data de Pagamento: #DATAPAGAMENTO#\n=======================\n➡Valor: R$: #VALOR#\n=======================\n➡Esta é uma mensagem automática e não precisa ser respondida.', '5', '1', '09:01', NULL),
(6, '1', 'Olá *#NOME#*,\n\nPassando para informar que sua mensalidade no valor de:\n\nSua fatura *Fibra*.\n*R$: #VALOR#* já está disponível para pagamento.\nQue vence em #VENCIMENTO#\n\nChave Pix *Copia e Cola* logo abaixo.\n\nBasta copiar a chave e abrir seu aplicativo para realizar o pagamento.\n\n⚠Enviar comprovante para anexo no sistema.⚠', '6', '1', '12:00', NULL),
(7, '2', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(8, '2', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(9, '2', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:59', NULL),
(10, '2', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:59', NULL),
(11, '2', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(12, '2', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(13, '3', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(14, '3', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(15, '3', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(16, '3', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(17, '3', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(18, '3', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(19, '4', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(20, '4', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(21, '4', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(22, '4', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(23, '4', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(24, '4', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(25, '5', '*#NOME#* mensagem de com 5 dias antes do vencimento\n\n#EMPRESA# - Nome do Cliente\n#CNPJ# - Nome do Cliente\n#ENDERECO# - Nome do Cliente\n#CONTATO# - Nome do Cliente\n#NOME# - Nome do Cliente\n#VENCIMENTO# - Data de vencimento da parcela\n#DATAPAGAMENTO# - Data de pagamento da parcela\n#VALOR# - Valor da Parcela\n#LINK# - Link de Pagamento final, adicione seu domínio antes.', '1', '1', '16:00', NULL),
(26, '5', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(27, '5', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(28, '5', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(29, '5', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(30, '5', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(31, '6', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(32, '6', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(33, '6', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(34, '6', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(35, '6', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(36, '6', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(37, '7', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(38, '7', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(39, '7', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(40, '7', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(41, '7', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(42, '7', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(43, '8', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(44, '8', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(45, '8', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(46, '8', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(47, '8', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(48, '8', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(49, '9', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(50, '9', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(51, '9', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(52, '9', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(53, '9', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(54, '9', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(55, '10', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(56, '10', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(57, '10', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(58, '10', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(59, '10', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(60, '10', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(61, '11', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(62, '11', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(63, '11', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(64, '11', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(65, '11', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(66, '11', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(67, '12', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(68, '12', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(69, '12', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(70, '12', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(71, '12', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(72, '12', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(73, '13', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(74, '13', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(75, '13', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(76, '13', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(77, '13', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(79, '14', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(80, '14', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(81, '14', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(82, '14', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(83, '14', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(84, '14', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(85, '15', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(86, '15', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(87, '15', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(88, '15', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(89, '15', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(90, '15', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(91, '16', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(92, '16', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(93, '16', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(94, '16', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(95, '16', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(96, '16', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(97, '17', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(98, '17', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(99, '17', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(100, '17', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(101, '17', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(102, '17', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(103, '18', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(104, '18', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(105, '18', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(109, '19', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(110, '19', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(111, '19', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(112, '19', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(113, '19', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(114, '19', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(115, '20', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(116, '20', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(117, '20', '*Aviso de Pagamento - Vencimento Hoje, #VENCIMENTO#* ⏰💳\n\nOlá, Espero que esteja tudo bem com você! 👋\n\nGostaríamos de lembrar que hoje é a data de vencimento da fatura em aberto. 🗓️💸 Não deixe passar esta oportunidade de manter suas contas em dia e evitar quaisquer inconvenientes futuros. 🚫🔒\n\nPara facilitar sua vida, oferecemos diversas formas de pagamento, incluindo [Inserir opções de pagamento, como *PIX, boleto bancário, cartão de crédito, etc*.].\n\n⚠️ *Caso já tenha efetuado o pagamento, agradecemos desde já e pedimos que desconsidere esta mensagem* ⚠️\n\nCaso tenha alguma dúvida ou necessite de assistência adicional, por favor, não hesite em nos contatar. Estamos aqui para ajudar! 🤝\n\nAgradecemos pela sua atenção e compreensão.\n\nAtenciosamente,\n\nCenter Sat Rastreamento.', '3', '1', '18:16', NULL),
(118, '20', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(119, '20', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(120, '20', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(121, '21', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(122, '21', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(123, '21', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(124, '21', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(125, '21', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(126, '21', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(127, '22', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(128, '22', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(129, '22', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(130, '22', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(131, '22', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(132, '22', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(133, '23', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(134, '23', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(135, '23', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(136, '23', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(137, '23', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(138, '23', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(139, '24', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(140, '24', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(141, '24', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(142, '24', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(143, '24', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(144, '24', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(145, '25', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(146, '25', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(147, '25', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(148, '25', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(149, '25', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(150, '25', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(151, '26', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(152, '26', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(153, '26', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(154, '26', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(155, '26', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(156, '26', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(157, '27', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(158, '27', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(159, '27', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(160, '27', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(161, '27', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(162, '27', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(163, '28', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(164, '28', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(165, '28', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(166, '28', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(167, '28', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(168, '28', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(169, '29', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(170, '29', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(171, '29', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(172, '29', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(173, '29', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(174, '29', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(175, '30', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(176, '30', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(177, '30', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(178, '30', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(179, '30', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(180, '30', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(181, '31', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(182, '31', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(183, '31', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(184, '31', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(185, '31', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(186, '31', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(187, '32', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(188, '32', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(189, '32', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(190, '32', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(191, '32', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(192, '32', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(193, '33', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(194, '33', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(195, '33', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(196, '33', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(197, '33', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(198, '33', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(199, '34', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(200, '34', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(201, '34', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(202, '34', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(203, '34', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(204, '34', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(205, '35', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(206, '35', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(207, '35', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(208, '35', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(209, '35', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(210, '35', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(211, '36', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(212, '36', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(213, '36', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(214, '36', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(215, '36', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(216, '36', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(217, '37', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(218, '37', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(219, '37', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(220, '37', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(221, '37', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(222, '37', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(223, '38', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(224, '38', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(225, '38', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(226, '38', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(227, '38', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(228, '38', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(229, '39', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(230, '39', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(231, '39', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(232, '39', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(233, '39', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(234, '39', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(235, '40', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(236, '40', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(237, '40', '*#NOME#* mensagem no dia do vencimento', '3', '1', '21:00', NULL),
(238, '40', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(239, '40', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(240, '40', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(241, '41', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(242, '41', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(243, '41', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(244, '41', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(245, '41', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(246, '41', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(247, '42', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(248, '42', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(249, '42', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(250, '42', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(251, '42', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(252, '42', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(253, '43', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(254, '43', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(255, '43', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(256, '43', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(257, '43', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(258, '43', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(259, '44', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(260, '44', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(261, '44', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(262, '44', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(263, '44', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(264, '44', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(265, '45', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(266, '45', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(267, '45', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(268, '45', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(269, '45', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(270, '45', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(271, '46', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(272, '46', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(273, '46', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(274, '46', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(275, '46', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(276, '46', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(277, '47', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(278, '47', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(279, '47', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(280, '47', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(281, '47', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(282, '47', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(283, '48', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(284, '48', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(285, '48', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(286, '48', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(287, '48', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(288, '48', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(289, '49', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(290, '49', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(291, '49', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(292, '49', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(293, '49', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(294, '49', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(295, '50', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(296, '50', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(297, '50', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(298, '50', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(299, '50', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(300, '50', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(301, '51', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(302, '51', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(303, '51', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(304, '51', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(305, '51', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(306, '51', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(307, '52', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(308, '52', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(309, '52', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(310, '52', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(311, '52', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(312, '52', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(313, '53', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(314, '53', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(315, '53', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(316, '53', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(317, '53', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(318, '53', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(319, '54', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(320, '54', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(321, '54', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(322, '54', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(323, '54', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(324, '54', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(325, '55', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(326, '55', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(327, '55', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(328, '55', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(329, '55', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(330, '55', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(331, '56', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(332, '56', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(333, '56', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(334, '56', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(335, '56', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(336, '56', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(337, '57', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(338, '57', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(339, '57', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(340, '57', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(341, '57', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(342, '57', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(343, '58', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(344, '58', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(345, '58', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(346, '58', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(347, '58', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(348, '58', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(349, '59', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(350, '59', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(351, '59', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(352, '59', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(353, '59', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(354, '59', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(355, '60', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(356, '60', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(357, '60', '*#NOME#* Tudo bem? sua parcela vence hoje  o valor é de : #VALOR#', '3', '1', '12:52', NULL),
(358, '60', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(359, '60', '*#NOME#* Pagamento Recebido com sucesso! já demos baixa aqui no sistema, obrigada.', '5', '1', '11:44', NULL),
(360, '60', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(361, '61', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(362, '61', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(363, '61', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(364, '61', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(365, '61', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(366, '61', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(367, '62', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(368, '62', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(369, '62', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(370, '62', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(371, '62', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(372, '62', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(373, '63', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(374, '63', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(375, '63', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(376, '63', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(377, '63', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(378, '63', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(379, '64', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(380, '64', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(381, '64', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(382, '64', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(383, '64', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(384, '64', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(385, '65', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(386, '65', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(387, '65', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(388, '65', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(389, '65', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(390, '65', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(391, '66', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(392, '66', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(393, '66', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(394, '66', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(395, '66', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(396, '66', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(397, '67', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(398, '67', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(399, '67', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(400, '67', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(401, '67', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(402, '67', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(403, '68', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(404, '68', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(405, '68', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(406, '68', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(407, '68', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(408, '68', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(409, '69', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(410, '69', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(411, '69', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(412, '69', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(413, '69', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(414, '69', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(415, '70', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(416, '70', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(417, '70', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(418, '70', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(419, '70', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(420, '70', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(421, '71', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(422, '71', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(423, '71', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(424, '71', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(425, '71', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(426, '71', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(427, '72', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(428, '72', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(429, '72', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(430, '72', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(431, '72', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(432, '72', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(433, '73', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(434, '73', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(435, '73', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(436, '73', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(437, '73', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(438, '73', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(442, '74', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(443, '74', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(444, '74', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(445, '74', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(446, '74', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(447, '74', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(448, '75', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(449, '75', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(450, '75', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(451, '75', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(452, '75', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(453, '75', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(454, '76', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(455, '76', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(456, '76', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(457, '76', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(458, '76', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(459, '76', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(460, '77', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(461, '77', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(462, '77', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(463, '77', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(464, '77', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(465, '77', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(466, '78', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(467, '78', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(468, '78', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(469, '78', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(470, '78', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(471, '78', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(472, '79', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(473, '79', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(474, '79', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(475, '79', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(476, '79', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(477, '79', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(478, '80', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(479, '80', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(480, '80', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(481, '80', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(482, '80', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(483, '80', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(484, '81', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(485, '81', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(486, '81', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(487, '81', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(488, '81', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(489, '81', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(490, '82', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(491, '82', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(492, '82', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(493, '82', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(494, '82', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(495, '82', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(496, '83', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(497, '83', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(498, '83', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(499, '83', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(500, '83', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(501, '83', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(502, '84', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(503, '84', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(504, '84', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(505, '84', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(506, '84', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(507, '84', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL),
(508, '85', '*#NOME#* mensagem de com 5 dias antes do vencimento', '1', '1', '16:00', NULL),
(509, '85', '*#NOME#* mensagem de com 3 dias antes do vencimento', '2', '1', '16:00', NULL),
(510, '85', '*#NOME#* mensagem no dia do vencimento', '3', '1', '16:00', NULL),
(511, '85', '*#NOME#* mensagem de mensalidade vencida', '4', '1', '16:00', NULL),
(512, '85', '*#NOME#* mensagem de agradecimento', '5', '1', '16:00', NULL),
(513, '85', '*#NOME#* mensagem de cobranca manual', '6', '1', '16:00', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `mensagens_status`
--

CREATE TABLE `mensagens_status` (
  `id` int(11) NOT NULL,
  `loja_id` int(11) NOT NULL,
  `status_pedido` enum('pending','on-hold','processing','completed','cancelled','refunded','failed') NOT NULL,
  `mensagem` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `mercadopago`
--

CREATE TABLE `mercadopago` (
  `id` int(11) NOT NULL,
  `idc` varchar(20) DEFAULT NULL,
  `status` varchar(60) DEFAULT NULL,
  `instancia` varchar(60) DEFAULT NULL,
  `data` datetime NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `idp` varchar(60) NOT NULL,
  `qrcode` text NOT NULL,
  `linhad` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `message_queue`
--

CREATE TABLE `message_queue` (
  `id` int(11) NOT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `media` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `timestamp` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `omie`
--

CREATE TABLE `omie` (
  `id` int(11) NOT NULL,
  `id_cliente` int(11) NOT NULL,
  `codigo_cliente` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `vencimento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `vencimento_cron` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_pagamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_pagamento_pix` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `copia_colar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_barra` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_pagamento` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `chave` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `pagamentofun`
--

CREATE TABLE `pagamentofun` (
  `id` int(11) NOT NULL,
  `idc` varchar(9) DEFAULT NULL,
  `idm` varchar(9) DEFAULT NULL,
  `data` varchar(20) DEFAULT NULL,
  `valor` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `payments`
--

CREATE TABLE `payments` (
  `id` int(6) UNSIGNED NOT NULL,
  `payment_id` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `planName` varchar(255) NOT NULL,
  `planId` int(6) UNSIGNED DEFAULT NULL COMMENT 'ID do plano para evitar conflitos de nome',
  `cod_id` int(6) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COMMENT='Tabela de pagamentos de assinaturas';

--
-- Despejando dados para a tabela `payments`
--

INSERT INTO `payments` (`id`, `payment_id`, `status`, `planName`, `planId`, `cod_id`) VALUES
(1, '137821497059', 'pending', 'Mensal', 14, 81);

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

CREATE TABLE `planos` (
  `id` int(6) UNSIGNED NOT NULL,
  `nome` varchar(255) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `cadastros_lim` int(11) DEFAULT NULL COMMENT 'Limite de cadastros permitido neste plano',
  `instancias_limite` int(11) DEFAULT NULL COMMENT 'Limite de instâncias WhatsApp incluídas neste plano'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `valor`, `cadastros_lim`, `instancias_limite`) VALUES
(14, 'Mensal', 15.00, 10, 3),
(15, 'Mensal', 25.00, 20, 3),
(16, '', 35.00, 35, 3),
(17, 'Mensal', 40.00, 50, 3),
(19, 'Mensal', 50.00, 200, 3);

-- --------------------------------------------------------

--
-- Estrutura para tabela `produtos`
--

CREATE TABLE `produtos` (
  `id` int(11) NOT NULL,
  `cod_id` int(11) NOT NULL,
  `nome` varchar(255) NOT NULL,
  `preco` decimal(10,2) NOT NULL,
  `descricao` text DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `solicitacoes_renovacao`
--

CREATE TABLE `solicitacoes_renovacao` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `plano_id` int(11) NOT NULL,
  `data_solicitacao` timestamp NULL DEFAULT current_timestamp(),
  `status` enum('pendente','aprovada','rejeitada') DEFAULT 'pendente',
  `data_aprovacao` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Despejando dados para a tabela `solicitacoes_renovacao`
--

INSERT INTO `solicitacoes_renovacao` (`id`, `user_id`, `plano_id`, `data_solicitacao`, `status`, `data_aprovacao`) VALUES
(1, 80, 14, '2025-12-18 16:49:55', 'pendente', NULL);

-- --------------------------------------------------------

--
-- Estrutura para tabela `subscription_history`
--

CREATE TABLE `subscription_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `action` enum('created','renewed','cancelled','upgraded','downgraded') NOT NULL,
  `motivo` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='Histórico de ações em assinaturas';

-- --------------------------------------------------------

--
-- Estrutura para tabela `videos`
--

CREATE TABLE `videos` (
  `id` int(11) NOT NULL,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ordem` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `whatsapp_envios`
--

CREATE TABLE `whatsapp_envios` (
  `id` int(11) UNSIGNED NOT NULL,
  `idm` int(11) NOT NULL COMMENT 'ID da empresa/usuário',
  `tipo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de envio (cobranca, pix_codigo, boleto_codigo, pix_manual)',
  `origem` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Arquivo de origem (cobm.php, auto_cob_global.php)',
  `cliente_id` int(11) NOT NULL COMMENT 'ID do cliente',
  `cliente_nome` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome do cliente',
  `telefone` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Telefone do cliente',
  `mensagem` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mensagem enviada',
  `parcela_id` int(11) DEFAULT NULL COMMENT 'ID da parcela',
  `chave` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Chave da cobrança',
  `gateway` int(6) DEFAULT NULL COMMENT 'Gateway de pagamento (1=MP, 2=PIX Manual, 4=PagHiper, 5=Asaas)',
  `tipo_pagamento` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Tipo de pagamento (PIX, BOLETO)',
  `valor` decimal(10,2) DEFAULT NULL COMMENT 'Valor da cobrança',
  `status` enum('sucesso','falha','pendente') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendente' COMMENT 'Status do envio',
  `erro` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Mensagem de erro (se houver)',
  `data_envio` timestamp NULL DEFAULT current_timestamp() COMMENT 'Data e hora do envio'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Registro de envios de WhatsApp para relatório';

--
-- Despejando dados para a tabela `whatsapp_envios`
--

INSERT INTO `whatsapp_envios` (`id`, `idm`, `tipo`, `origem`, `cliente_id`, `cliente_nome`, `telefone`, `mensagem`, `parcela_id`, `chave`, `gateway`, `tipo_pagamento`, `valor`, `status`, `erro`, `data_envio`) VALUES
(12, 1, 'cobranca', 'cobm.php', 441, 'MARCELO IPTV', '6596915592', 'Olá *MARCELO*,\\n\\nPassando para informar que sua mensalidade no valor de:\\n\\nSua fatura *Fibra*.\\n*R$: 20.00* já está disponível para pagamento.\\nQue vence em 20/01/2026\\n\\nChave Pix *Copia e Cola* logo abaixo.\\n\\nBasta copiar a chave e abrir seu aplicativo para realizar o pagamento.\\n\\n⚠Enviar comprovante para anexo no sistema.⚠', 988, 'dbe90371058b1fe8be183db1e0abb3fd', 1, 'PIX', 20.00, 'falha', 'SQLSTATE[23000]: Integrity constraint violation: 1048 Column \'id_payment\' cannot be null', '2025-12-19 02:17:57'),
(13, 1, 'cobranca', 'cobm.php', 441, 'MARCELO IPTV', '6596915592', 'Olá *MARCELO*,\\n\\nPassando para informar que sua mensalidade no valor de:\\n\\nSua fatura *Fibra*.\\n*R$: 20.00* já está disponível para pagamento.\\nQue vence em 20/01/2026\\n\\nChave Pix *Copia e Cola* logo abaixo.\\n\\nBasta copiar a chave e abrir seu aplicativo para realizar o pagamento.\\n\\n⚠Enviar comprovante para anexo no sistema.⚠', 988, 'dbe90371058b1fe8be183db1e0abb3fd', 1, 'PIX', 20.00, 'sucesso', NULL, '2025-12-19 02:18:32');

-- --------------------------------------------------------

--
-- Estrutura para tabela `woo_pedidos`
--

CREATE TABLE `woo_pedidos` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `loja_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `order_total` decimal(10,2) NOT NULL,
  `order_status` varchar(50) NOT NULL,
  `order_currency` varchar(10) DEFAULT NULL,
  `payment_method` varchar(255) DEFAULT NULL,
  `order_product` text DEFAULT NULL,
  `order_product_with_qty` text DEFAULT NULL,
  `billing_first_name` varchar(255) DEFAULT NULL,
  `billing_last_name` varchar(255) DEFAULT NULL,
  `billing_phone` varchar(20) DEFAULT NULL,
  `billing_email` varchar(255) DEFAULT NULL,
  `billing_company` varchar(255) DEFAULT NULL,
  `billing_address` varchar(255) DEFAULT NULL,
  `billing_city` varchar(255) DEFAULT NULL,
  `billing_state` varchar(255) DEFAULT NULL,
  `billing_country` varchar(50) DEFAULT NULL,
  `billing_postcode` varchar(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_app_codigos`
--

CREATE TABLE `_pdv_app_codigos` (
  `id` int(11) NOT NULL,
  `codigo` varchar(6) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código único de 6 dígitos',
  `idm` int(11) NOT NULL COMMENT 'ID da empresa/usuário vinculado',
  `nome_empresa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Nome da empresa para identificação',
  `ativo` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1=ativo, 0=inativo',
  `modo_atual` enum('pdv','cadastro') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pdv' COMMENT 'Modo de operação: pdv=carrinho, cadastro=formulário',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data de criação do código',
  `data_ultimo_uso` timestamp NULL DEFAULT NULL COMMENT 'Data do último uso do código',
  `ip_ultimo_acesso` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP do ltimo acesso',
  `total_usos` int(11) DEFAULT 0 COMMENT 'Total de vezes que o código foi usado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Códigos de acesso para app móvel PDV';

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_caixa`
--

CREATE TABLE `_pdv_caixa` (
  `id` int(11) NOT NULL,
  `idm` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `atendente_nome` varchar(100) DEFAULT NULL,
  `abertura` datetime NOT NULL,
  `fechamento` datetime DEFAULT NULL,
  `status` enum('aberto','fechado') DEFAULT 'aberto',
  `saldo_inicial` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_carrinho`
--

CREATE TABLE `_pdv_carrinho` (
  `id` int(11) NOT NULL,
  `idm` int(11) NOT NULL COMMENT 'ID da empresa/usuário',
  `codigo_app` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código do app que criou o carrinho',
  `cliente_id` int(11) DEFAULT NULL COMMENT 'ID do cliente (opcional)',
  `total` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Total do carrinho',
  `desconto` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Desconto aplicado',
  `observacoes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observações do carrinho',
  `data_criacao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data de criação do carrinho',
  `data_atualizacao` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'Data da última atualização',
  `status` enum('ativo','finalizado','cancelado') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ativo' COMMENT 'Status do carrinho'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Carrinho de compras do app móvel';

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_carrinho_itens`
--

CREATE TABLE `_pdv_carrinho_itens` (
  `id` int(11) NOT NULL,
  `id_carrinho` int(11) NOT NULL COMMENT 'ID do carrinho',
  `id_produto` int(11) NOT NULL COMMENT 'ID do produto',
  `quantidade` decimal(10,3) NOT NULL DEFAULT 1.000 COMMENT 'Quantidade do produto',
  `preco_unitario` decimal(10,2) NOT NULL COMMENT 'Preo unitário na época da adiço',
  `subtotal` decimal(10,2) NOT NULL COMMENT 'Subtotal do item (quantidade * preço)',
  `data_adicao` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data de adição do item',
  `observacoes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observações do item'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens do carrinho de compras';

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_ciclos_pagamento`
--

CREATE TABLE `_pdv_ciclos_pagamento` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dias` int(11) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Despejando dados para a tabela `_pdv_ciclos_pagamento`
--

INSERT INTO `_pdv_ciclos_pagamento` (`id`, `nome`, `dias`, `ativo`) VALUES
(1, 'Semanal', 7, 1),
(2, 'Quinzenal', 15, 1),
(3, 'Mensal', 30, 1),
(4, 'Trimestral', 90, 1),
(5, 'Semestral', 180, 1),
(6, 'Anual', 365, 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_estoque_movimentacao`
--

CREATE TABLE `_pdv_estoque_movimentacao` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_produto` int(11) NOT NULL COMMENT 'ID do produto',
  `tipo_movimentacao` enum('entrada','saida','ajuste','troca','cancelamento') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de movimentação',
  `quantidade` int(11) NOT NULL COMMENT 'Quantidade movimentada',
  `estoque_anterior` int(11) NOT NULL COMMENT 'Estoque antes da movimentação',
  `estoque_atual` int(11) NOT NULL COMMENT 'Estoque após a movimentaão',
  `id_venda` int(11) DEFAULT NULL COMMENT 'ID da venda relacionada (se aplicável)',
  `id_troca` int(11) DEFAULT NULL COMMENT 'ID da troca relacionada (se aplicável)',
  `usuario_id` int(11) NOT NULL COMMENT 'Usuário que fez a movimentação',
  `data_movimentacao` datetime DEFAULT current_timestamp() COMMENT 'Data da movimentação',
  `observacoes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observações sobre a movimentação'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de movimentações de estoque';

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_formas_pagamento`
--

CREATE TABLE `_pdv_formas_pagamento` (
  `id` int(11) NOT NULL,
  `nome` varchar(50) NOT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

--
-- Despejando dados para a tabela `_pdv_formas_pagamento`
--

INSERT INTO `_pdv_formas_pagamento` (`id`, `nome`, `ativo`) VALUES
(1, 'Dinheiro', 1),
(2, 'Pix', 1),
(3, 'Cartão Débito', 1),
(4, 'Cartão Crédito', 1),
(5, 'Fiado', 1),
(6, 'Cheque', 1),
(7, 'Boleto', 1);

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_itens`
--

CREATE TABLE `_pdv_itens` (
  `id` int(11) NOT NULL,
  `id_venda` int(11) NOT NULL,
  `id_produto` int(11) NOT NULL,
  `quantidade` int(11) NOT NULL,
  `preco_unitario` decimal(10,2) NOT NULL,
  `total_item` decimal(10,2) GENERATED ALWAYS AS (`quantidade` * `preco_unitario`) STORED
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_itens_trocados`
--

CREATE TABLE `_pdv_itens_trocados` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_troca` int(11) NOT NULL COMMENT 'ID da troca',
  `id_produto` int(11) NOT NULL COMMENT 'ID do produto',
  `quantidade` int(11) NOT NULL COMMENT 'Quantidade trocada',
  `preco_unitario` decimal(10,2) NOT NULL COMMENT 'Preço unitrio na época da troca',
  `tipo` enum('saida','entrada') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Se é produto que sai ou entra na troca'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Itens envolvidos nas trocas';

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_log_barcode`
--

CREATE TABLE `_pdv_log_barcode` (
  `id` int(11) NOT NULL,
  `idm` int(11) NOT NULL COMMENT 'ID da empresa/usurio',
  `codigo_barras` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Código de barras lido',
  `id_produto` int(11) DEFAULT NULL COMMENT 'ID do produto encontrado (se houver)',
  `status` enum('success','not_found','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'success' COMMENT 'Status da leitura',
  `data_leitura` timestamp NOT NULL DEFAULT current_timestamp() COMMENT 'Data e hora da leitura',
  `ip_origem` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'IP do dispositivo que fez a leitura',
  `observacoes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observações adicionais'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Log de leituras de cdigo de barras do app móvel';

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_produtos`
--

CREATE TABLE `_pdv_produtos` (
  `id` int(11) NOT NULL,
  `idm` int(11) NOT NULL,
  `nome` varchar(120) NOT NULL,
  `descricao` text DEFAULT NULL,
  `imagem` varchar(512) DEFAULT NULL,
  `preco_venda` decimal(10,2) NOT NULL,
  `desconto` decimal(10,2) NOT NULL DEFAULT 0.00,
  `estoque` int(11) NOT NULL DEFAULT 0,
  `fornecedor_id` int(11) DEFAULT NULL,
  `codigo_barras` varchar(50) DEFAULT NULL,
  `ativo` tinyint(1) NOT NULL DEFAULT 1,
  `data_criacao` timestamp NULL DEFAULT current_timestamp(),
  `categoria_id` int(11) DEFAULT NULL,
  `unidade` varchar(10) DEFAULT 'un',
  `preco_custo` decimal(10,2) DEFAULT 0.00,
  `margem_lucro` decimal(7,2) DEFAULT 0.00,
  `estoque_minimo` int(11) DEFAULT 0,
  `alerta_estoque` tinyint(1) DEFAULT 1,
  `fabricante` varchar(100) DEFAULT NULL,
  `ncm` varchar(10) DEFAULT NULL,
  `cfop` varchar(10) DEFAULT NULL,
  `peso` decimal(8,3) DEFAULT NULL,
  `atualizado_em` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_trocas`
--

CREATE TABLE `_pdv_trocas` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_venda_original` int(11) NOT NULL COMMENT 'ID da venda original',
  `id_venda_nova` int(11) DEFAULT NULL COMMENT 'ID da nova venda (se aplicável)',
  `tipo_troca` enum('produto','dinheiro','credito') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo da troca',
  `valor_troca` decimal(10,2) NOT NULL DEFAULT 0.00 COMMENT 'Valor envolvido na troca',
  `motivo` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Motivo da troca',
  `usuario_id` int(11) NOT NULL COMMENT 'Usuário que processou a troca',
  `data_troca` datetime DEFAULT current_timestamp() COMMENT 'Data da troca',
  `status` enum('pendente','processada','cancelada') COLLATE utf8mb4_unicode_ci DEFAULT 'pendente' COMMENT 'Status da troca'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Controle de trocas de produtos';

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_vendas`
--

CREATE TABLE `_pdv_vendas` (
  `id` int(11) NOT NULL,
  `idm` int(11) NOT NULL,
  `caixa_id` int(11) DEFAULT NULL,
  `cliente_id` int(11) DEFAULT NULL,
  `total` decimal(10,2) NOT NULL,
  `id_forma_pagamento` int(11) DEFAULT NULL,
  `data_venda` datetime NOT NULL DEFAULT current_timestamp(),
  `desconto` decimal(10,2) DEFAULT 0.00,
  `status` enum('ativa','cancelada','trocada') DEFAULT 'ativa' COMMENT 'Status da venda',
  `observacoes` text DEFAULT NULL COMMENT 'Observações sobre a venda',
  `usuario_alteracao` int(11) DEFAULT NULL COMMENT 'Usuário que fez a última alteração',
  `data_alteracao` datetime DEFAULT NULL COMMENT 'Data da última alteração',
  `parcelas` int(11) DEFAULT NULL COMMENT 'Número de parcelas',
  `ciclo_pagamento` varchar(20) DEFAULT NULL COMMENT 'Ciclo de pagamento (semanal, quinzenal, mensal)',
  `data_vencimento` date DEFAULT NULL COMMENT 'Data de vencimento da primeira parcela',
  `valor_parcela` decimal(10,2) DEFAULT NULL COMMENT 'Valor de cada parcela',
  `lancar_conta` tinyint(1) DEFAULT 0 COMMENT 'Se deve lançar em contas a receber',
  `funcionario_id` int(11) DEFAULT NULL COMMENT 'ID do funcionário que fez a venda',
  `comissao` decimal(10,2) DEFAULT 0.00 COMMENT 'Valor da comissão calculada',
  `data_comissao` datetime DEFAULT NULL COMMENT 'Data de cálculo da comissão'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Estrutura para tabela `_pdv_vendas_historico`
--

CREATE TABLE `_pdv_vendas_historico` (
  `id` int(11) UNSIGNED NOT NULL,
  `id_venda` int(11) NOT NULL COMMENT 'ID da venda original',
  `acao` enum('criacao','alteracao','cancelamento','troca') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Tipo de ação realizada',
  `status_anterior` enum('ativa','cancelada','trocada') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Status anterior da venda',
  `status_novo` enum('ativa','cancelada','trocada') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Novo status da venda',
  `usuario_id` int(11) NOT NULL COMMENT 'ID do usuário que executou a ao',
  `data_acao` datetime DEFAULT current_timestamp() COMMENT 'Data e hora da ação',
  `observacoes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Observações sobre a aço',
  `dados_anteriores` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dados da venda antes da alteração' CHECK (json_valid(`dados_anteriores`)),
  `dados_novos` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Dados da venda após a alteraço' CHECK (json_valid(`dados_novos`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Histórico de alterações nas vendas PDV';

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_agendamentos_instancia` (`instancia_id`),
  ADD KEY `idx_agendamentos_modo` (`modo_envio`);

--
-- Índices de tabela `carteira`
--
ALTER TABLE `carteira`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `idx_carteira_indicador` (`id_indicador`);

--
-- Índices de tabela `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `categorias_financeiras`
--
ALTER TABLE `categorias_financeiras`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_idm` (`idm`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_ativo` (`ativo`),
  ADD KEY `idx_ordem` (`ordem`);

--
-- Índices de tabela `chat_conversas`
--
ALTER TABLE `chat_conversas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_conversa` (`idm`,`contato_telefone`),
  ADD KEY `idx_idm` (`idm`),
  ADD KEY `idx_telefone` (`contato_telefone`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_updated` (`updated_at`);

--
-- Índices de tabela `chat_mensagens`
--
ALTER TABLE `chat_mensagens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_conversa` (`conversa_id`),
  ADD KEY `idx_idm` (`idm`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_created` (`created_at`);

--
-- Índices de tabela `clientes`
--
ALTER TABLE `clientes`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `idx_clientes_busca` (`idm`,`nome`),
  ADD KEY `idx_clientes_cpf` (`idm`,`cpf`),
  ADD KEY `idx_clientes_email` (`idm`,`email`);

--
-- Índices de tabela `conexoes`
--
ALTER TABLE `conexoes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_conexoes_instancia` (`instancia_id`);

--
-- Índices de tabela `contas_pagar`
--
ALTER TABLE `contas_pagar`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `disparo_enviado`
--
ALTER TABLE `disparo_enviado`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `disparo_msg`
--
ALTER TABLE `disparo_msg`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `email_config`
--
ALTER TABLE `email_config`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `email_logs`
--
ALTER TABLE `email_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_sent_at` (`sent_at`);

--
-- Índices de tabela `financeiro1`
--
ALTER TABLE `financeiro1`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `idx_financeiro1_chave` (`chave`),
  ADD KEY `idx_financeiro1_idm` (`idm`),
  ADD KEY `idx_financeiro1_produtoId` (`produtoId`);

--
-- Índices de tabela `financeiro2`
--
ALTER TABLE `financeiro2`
  ADD PRIMARY KEY (`Id`),
  ADD KEY `idx_financeiro2_chave` (`chave`),
  ADD KEY `idx_financeiro2_status` (`status`),
  ADD KEY `idx_financeiro2_idm` (`idm`),
  ADD KEY `idx_financeiro2_datapagamento` (`datapagamento`),
  ADD KEY `idx_financeiro2_chave_status` (`chave`,`status`),
  ADD KEY `idx_financeiro2_idm_status` (`idm`,`status`);

--
-- Índices de tabela `financeiro3`
--
ALTER TABLE `financeiro3`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_categoria_financeira` (`categoria_id`);

--
-- Índices de tabela `financeiro_global`
--
ALTER TABLE `financeiro_global`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_link_pagamento` (`link_pagamento`);

--
-- Índices de tabela `financeiro_parciais`
--
ALTER TABLE `financeiro_parciais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_parcela` (`parcela_id`),
  ADD KEY `idx_cliente` (`idc`,`idm`),
  ADD KEY `idx_chave` (`chave`),
  ADD KEY `idx_data_pagamento` (`data_pagamento`),
  ADD KEY `idx_status` (`status_pagamento`);

--
-- Índices de tabela `formas_pagamento`
--
ALTER TABLE `formas_pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `fornecedores_gast`
--
ALTER TABLE `fornecedores_gast`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_cnpj` (`cnpj`),
  ADD KEY `idx_cidade` (`cidade`),
  ADD KEY `idx_estado` (`estado`);

--
-- Índices de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `indicacoes`
--
ALTER TABLE `indicacoes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo_indicacao` (`codigo_indicacao`),
  ADD UNIQUE KEY `unique_indicacao` (`id_usuario_indicador`,`id_usuario_indicado`),
  ADD KEY `idx_indicador` (`id_usuario_indicador`),
  ADD KEY `idx_indicado` (`id_usuario_indicado`),
  ADD KEY `idx_codigo` (`codigo_indicacao`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_data` (`data_indicacao`);

--
-- Índices de tabela `indicacoes_config`
--
ALTER TABLE `indicacoes_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `chave` (`chave`),
  ADD KEY `idx_chave` (`chave`);

--
-- Índices de tabela `indicacoes_movimentacao`
--
ALTER TABLE `indicacoes_movimentacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_indicacao` (`id_indicacao`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_data` (`data_movimentacao`);

--
-- Índices de tabela `indicacoes_saldo`
--
ALTER TABLE `indicacoes_saldo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_usuario` (`id_usuario`),
  ADD UNIQUE KEY `codigo_unico` (`codigo_unico`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_codigo` (`codigo_unico`),
  ADD KEY `idx_saldo` (`saldo_disponivel`);

--
-- Índices de tabela `instancias_uso`
--
ALTER TABLE `instancias_uso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_instancia_data` (`instancia_id`,`data`);

--
-- Índices de tabela `instancias_usuario`
--
ALTER TABLE `instancias_usuario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_usuario_nome` (`id_usuario`,`nome`),
  ADD KEY `idx_usuario` (`id_usuario`),
  ADD KEY `idx_ativo` (`ativo`),
  ADD KEY `idx_padrao` (`padrao`);

--
-- Índices de tabela `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `lojas_woocommerce`
--
ALTER TABLE `lojas_woocommerce`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `mensagens`
--
ALTER TABLE `mensagens`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `mensagens_status`
--
ALTER TABLE `mensagens_status`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `mercadopago`
--
ALTER TABLE `mercadopago`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `message_queue`
--
ALTER TABLE `message_queue`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `omie`
--
ALTER TABLE `omie`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `pagamentofun`
--
ALTER TABLE `pagamentofun`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `planos`
--
ALTER TABLE `planos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `produtos`
--
ALTER TABLE `produtos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `solicitacoes_renovacao`
--
ALTER TABLE `solicitacoes_renovacao`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `subscription_history`
--
ALTER TABLE `subscription_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `action` (`action`),
  ADD KEY `created_at` (`created_at`);

--
-- Índices de tabela `videos`
--
ALTER TABLE `videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_videos_ordem` (`ordem`);

--
-- Índices de tabela `whatsapp_envios`
--
ALTER TABLE `whatsapp_envios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_idm` (`idm`),
  ADD KEY `idx_tipo` (`tipo`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_data_envio` (`data_envio`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_gateway` (`gateway`);

--
-- Índices de tabela `woo_pedidos`
--
ALTER TABLE `woo_pedidos`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `_pdv_app_codigos`
--
ALTER TABLE `_pdv_app_codigos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD UNIQUE KEY `idx_codigo` (`codigo`),
  ADD KEY `idx_idm` (`idm`),
  ADD KEY `idx_ativo` (`ativo`),
  ADD KEY `idx_modo_atual` (`modo_atual`),
  ADD KEY `idx_data_criacao` (`data_criacao`);

--
-- Índices de tabela `_pdv_caixa`
--
ALTER TABLE `_pdv_caixa`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `_pdv_carrinho`
--
ALTER TABLE `_pdv_carrinho`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_codigo_app_ativo` (`codigo_app`,`status`),
  ADD KEY `idx_idm` (`idm`),
  ADD KEY `idx_cliente` (`cliente_id`),
  ADD KEY `idx_data_criacao` (`data_criacao`),
  ADD KEY `idx_status` (`status`);

--
-- Índices de tabela `_pdv_carrinho_itens`
--
ALTER TABLE `_pdv_carrinho_itens`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `_pdv_ciclos_pagamento`
--
ALTER TABLE `_pdv_ciclos_pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `_pdv_estoque_movimentacao`
--
ALTER TABLE `_pdv_estoque_movimentacao`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_produto` (`id_produto`),
  ADD KEY `idx_venda` (`id_venda`),
  ADD KEY `idx_troca` (`id_troca`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_data` (`data_movimentacao`),
  ADD KEY `idx_tipo` (`tipo_movimentacao`);

--
-- Índices de tabela `_pdv_formas_pagamento`
--
ALTER TABLE `_pdv_formas_pagamento`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `_pdv_itens`
--
ALTER TABLE `_pdv_itens`
  ADD PRIMARY KEY (`id`);

--
-- Índices de tabela `_pdv_itens_trocados`
--
ALTER TABLE `_pdv_itens_trocados`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_troca` (`id_troca`),
  ADD KEY `idx_produto` (`id_produto`);

--
-- Índices de tabela `_pdv_log_barcode`
--
ALTER TABLE `_pdv_log_barcode`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_idm` (`idm`),
  ADD KEY `idx_codigo_barras` (`codigo_barras`),
  ADD KEY `idx_data_leitura` (`data_leitura`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_idm_data` (`idm`,`data_leitura`),
  ADD KEY `idx_produto_data` (`id_produto`,`data_leitura`);

--
-- Índices de tabela `_pdv_produtos`
--
ALTER TABLE `_pdv_produtos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_pdv_produtos_busca` (`idm`,`ativo`,`nome`),
  ADD KEY `idx_pdv_produtos_codigo` (`idm`,`ativo`,`codigo_barras`),
  ADD KEY `idx_pdv_produtos_nome_codigo` (`idm`,`ativo`,`nome`,`codigo_barras`),
  ADD KEY `idx_pdv_produtos_estoque` (`idm`,`ativo`,`estoque`),
  ADD KEY `idx_pdv_produtos_categoria` (`idm`,`ativo`,`categoria_id`),
  ADD KEY `idx_pdv_produtos_fabricante` (`idm`,`ativo`,`fabricante`),
  ADD KEY `idx_pdv_produtos_preco` (`idm`,`ativo`,`preco_venda`);

--
-- Índices de tabela `_pdv_trocas`
--
ALTER TABLE `_pdv_trocas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venda_original` (`id_venda_original`),
  ADD KEY `idx_venda_nova` (`id_venda_nova`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_data` (`data_troca`),
  ADD KEY `idx_status` (`status`);

--
-- Índices de tabela `_pdv_vendas`
--
ALTER TABLE `_pdv_vendas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_funcionario_comissao` (`funcionario_id`,`data_venda`),
  ADD KEY `idx_comissao` (`comissao`),
  ADD KEY `idx_data_comissao` (`data_comissao`),
  ADD KEY `idx_pdv_vendas_status` (`status`),
  ADD KEY `idx_pdv_vendas_data_alteracao` (`data_alteracao`),
  ADD KEY `idx_pdv_vendas_usuario_alteracao` (`usuario_alteracao`),
  ADD KEY `idx_pdv_vendas_data` (`idm`,`data_venda`),
  ADD KEY `idx_pdv_vendas_cliente` (`idm`,`cliente_id`),
  ADD KEY `idx_pdv_vendas_idm_status` (`idm`,`status`);

--
-- Índices de tabela `_pdv_vendas_historico`
--
ALTER TABLE `_pdv_vendas_historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_venda` (`id_venda`),
  ADD KEY `idx_usuario` (`usuario_id`),
  ADD KEY `idx_data` (`data_acao`),
  ADD KEY `idx_acao` (`acao`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `agendamentos`
--
ALTER TABLE `agendamentos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `carteira`
--
ALTER TABLE `carteira`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT de tabela `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT de tabela `categorias_financeiras`
--
ALTER TABLE `categorias_financeiras`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de tabela `chat_conversas`
--
ALTER TABLE `chat_conversas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `chat_mensagens`
--
ALTER TABLE `chat_mensagens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `clientes`
--
ALTER TABLE `clientes`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=442;

--
-- AUTO_INCREMENT de tabela `conexoes`
--
ALTER TABLE `conexoes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de tabela `contas_pagar`
--
ALTER TABLE `contas_pagar`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `disparo_enviado`
--
ALTER TABLE `disparo_enviado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `disparo_msg`
--
ALTER TABLE `disparo_msg`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `email_config`
--
ALTER TABLE `email_config`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `email_logs`
--
ALTER TABLE `email_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `financeiro1`
--
ALTER TABLE `financeiro1`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=343;

--
-- AUTO_INCREMENT de tabela `financeiro2`
--
ALTER TABLE `financeiro2`
  MODIFY `Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1012;

--
-- AUTO_INCREMENT de tabela `financeiro3`
--
ALTER TABLE `financeiro3`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT de tabela `financeiro_global`
--
ALTER TABLE `financeiro_global`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=144;

--
-- AUTO_INCREMENT de tabela `financeiro_parciais`
--
ALTER TABLE `financeiro_parciais`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `formas_pagamento`
--
ALTER TABLE `formas_pagamento`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fornecedores`
--
ALTER TABLE `fornecedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `fornecedores_gast`
--
ALTER TABLE `fornecedores_gast`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `indicacoes`
--
ALTER TABLE `indicacoes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `indicacoes_config`
--
ALTER TABLE `indicacoes_config`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `indicacoes_movimentacao`
--
ALTER TABLE `indicacoes_movimentacao`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `indicacoes_saldo`
--
ALTER TABLE `indicacoes_saldo`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `instancias_uso`
--
ALTER TABLE `instancias_uso`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `instancias_usuario`
--
ALTER TABLE `instancias_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de tabela `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `lojas_woocommerce`
--
ALTER TABLE `lojas_woocommerce`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `mensagens`
--
ALTER TABLE `mensagens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=514;

--
-- AUTO_INCREMENT de tabela `mensagens_status`
--
ALTER TABLE `mensagens_status`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `mercadopago`
--
ALTER TABLE `mercadopago`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=421;

--
-- AUTO_INCREMENT de tabela `message_queue`
--
ALTER TABLE `message_queue`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2151;

--
-- AUTO_INCREMENT de tabela `omie`
--
ALTER TABLE `omie`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `pagamentofun`
--
ALTER TABLE `pagamentofun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `planos`
--
ALTER TABLE `planos`
  MODIFY `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de tabela `produtos`
--
ALTER TABLE `produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `solicitacoes_renovacao`
--
ALTER TABLE `solicitacoes_renovacao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de tabela `subscription_history`
--
ALTER TABLE `subscription_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `videos`
--
ALTER TABLE `videos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `whatsapp_envios`
--
ALTER TABLE `whatsapp_envios`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de tabela `woo_pedidos`
--
ALTER TABLE `woo_pedidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_app_codigos`
--
ALTER TABLE `_pdv_app_codigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_caixa`
--
ALTER TABLE `_pdv_caixa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_carrinho`
--
ALTER TABLE `_pdv_carrinho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_carrinho_itens`
--
ALTER TABLE `_pdv_carrinho_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_ciclos_pagamento`
--
ALTER TABLE `_pdv_ciclos_pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de tabela `_pdv_estoque_movimentacao`
--
ALTER TABLE `_pdv_estoque_movimentacao`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_formas_pagamento`
--
ALTER TABLE `_pdv_formas_pagamento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de tabela `_pdv_itens`
--
ALTER TABLE `_pdv_itens`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_itens_trocados`
--
ALTER TABLE `_pdv_itens_trocados`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_log_barcode`
--
ALTER TABLE `_pdv_log_barcode`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_produtos`
--
ALTER TABLE `_pdv_produtos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_trocas`
--
ALTER TABLE `_pdv_trocas`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_vendas`
--
ALTER TABLE `_pdv_vendas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de tabela `_pdv_vendas_historico`
--
ALTER TABLE `_pdv_vendas_historico`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restrições para tabelas despejadas
--

--
-- Restrições para tabelas `chat_mensagens`
--
ALTER TABLE `chat_mensagens`
  ADD CONSTRAINT `chat_mensagens_ibfk_1` FOREIGN KEY (`conversa_id`) REFERENCES `chat_conversas` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `indicacoes_movimentacao`
--
ALTER TABLE `indicacoes_movimentacao`
  ADD CONSTRAINT `indicacoes_movimentacao_ibfk_1` FOREIGN KEY (`id_indicacao`) REFERENCES `indicacoes` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `instancias_uso`
--
ALTER TABLE `instancias_uso`
  ADD CONSTRAINT `instancias_uso_ibfk_1` FOREIGN KEY (`instancia_id`) REFERENCES `instancias_usuario` (`id`) ON DELETE CASCADE;

--
-- Restrições para tabelas `_pdv_trocas`
--
ALTER TABLE `_pdv_trocas`
  ADD CONSTRAINT `_pdv_trocas_ibfk_1` FOREIGN KEY (`id_venda_original`) REFERENCES `_pdv_vendas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `_pdv_trocas_ibfk_2` FOREIGN KEY (`id_venda_nova`) REFERENCES `_pdv_vendas` (`id`) ON DELETE SET NULL;

--
-- Restrições para tabelas `_pdv_vendas_historico`
--
ALTER TABLE `_pdv_vendas_historico`
  ADD CONSTRAINT `_pdv_vendas_historico_ibfk_1` FOREIGN KEY (`id_venda`) REFERENCES `_pdv_vendas` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
