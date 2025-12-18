<?php
/**
 * Configuration Example File
 * 
 * IMPORTANT: For production use, you should:
 * 1. Copy this file to db/Conexao.php
 * 2. Update the values below with your actual credentials
 * 3. Never commit db/Conexao.php with real credentials to version control
 * 
 * For better security:
 * - Use environment variables (e.g., $_ENV, getenv())
 * - Store credentials in a secure configuration management system
 * - Keep this file outside the web root when possible
 */

// Database configuration
session_start();

// Database credentials - UPDATE THESE
$host = 'localhost';
$dbname = 'your_database_name';
$username = 'your_username';
$password = 'your_password';

// Evolution API Configuration - UPDATE THESE
// These values are also defined in master/classes/functions.php
$urlapi = "http://your-evolution-api-url:8080";
$apikey = "your-api-key-here";

// Create PDO connection
try {
    $connect = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $connect->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
} catch(PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}

// reCAPTCHA key - UPDATE THIS
$_captcha = "your-recaptcha-site-key";

// Create required tables if they don't exist
function createTablesAndAddColumnIfNotExist($connect) {
    try {
        // Create conexoes table if not exists
        $connect->exec("
            CREATE TABLE IF NOT EXISTS `conexoes` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_usuario` int(11) NOT NULL,
                `instance_name` varchar(255) DEFAULT NULL,
                `conn` tinyint(1) DEFAULT 0,
                `qr_code` text DEFAULT NULL,
                `data_conexao` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `id_usuario` (`id_usuario`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
        
        // Create whatsapp_contacts table if not exists
        $connect->exec("
            CREATE TABLE IF NOT EXISTS `whatsapp_contacts` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `id_usuario` int(11) NOT NULL,
                `instance_name` varchar(255) DEFAULT NULL,
                `contact_number` varchar(50) NOT NULL,
                `contact_name` varchar(255) DEFAULT NULL,
                `is_group` tinyint(1) DEFAULT 0,
                `source` enum('contacts','chats') DEFAULT 'contacts',
                `data_captura` datetime DEFAULT NULL,
                PRIMARY KEY (`id`),
                KEY `id_usuario` (`id_usuario`),
                KEY `instance_name` (`instance_name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");

        // Check if assinatura column exists in carteira table
        $result = $connect->query("SHOW COLUMNS FROM `carteira` LIKE 'assinatura'");
        if ($result->rowCount() == 0) {
            $connect->exec("ALTER TABLE `carteira` ADD COLUMN `assinatura` varchar(20) DEFAULT NULL");
        }

        // Check if background column exists in carteira table
        $result = $connect->query("SHOW COLUMNS FROM `carteira` LIKE 'background'");
        if ($result->rowCount() == 0) {
            $connect->exec("ALTER TABLE `carteira` ADD COLUMN `background` varchar(500) DEFAULT NULL");
        }

        // Check if tokenapi column exists in carteira table
        $result = $connect->query("SHOW COLUMNS FROM `carteira` LIKE 'tokenapi'");
        if ($result->rowCount() == 0) {
            $connect->exec("ALTER TABLE `carteira` ADD COLUMN `tokenapi` varchar(255) DEFAULT NULL");
        }

    } catch(PDOException $e) {
        // Silent fail - tables might already exist
    }
}

// Call the function to create tables
createTablesAndAddColumnIfNotExist($connect);
?>
