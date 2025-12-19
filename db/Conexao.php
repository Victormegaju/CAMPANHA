<?php
$servidor = 'localhost';
$usuario  = 'sql_financeiro_painelcontrole_xyz';
$senha 	  = '853508336e02a8';
$banco    = 'sql_financeiro_painelcontrole_xyz';

$connect = new PDO("mysql:host=$servidor;dbname=$banco", $usuario , $senha);  
$connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$_urlmaster = "https://".@$_SERVER['HTTP_HOST'];
$_urlapi = "https://financeiro.skcsoftware.com.br/";
$_ativacom = "1";



// Para criar sua chave acesse:
//https://www.google.com/recaptcha/admin/create

$_captcha = "6LeBBSwsAAAAAN09ZFIf4_Eddcon8EzIdhrK3OgL";

// Dados do Painel

$_nomesistema = "Painel SKC";

// ALTERAR AS LINHAS 16 E 19 SOMENTE





function createTablesAndAddColumnIfNotExist($connect) {
    try {
        $sql = "ALTER TABLE carteira ADD background VARCHAR(255)";
        $connect->exec($sql);
    } catch (PDOException $e) {
    }
    try {
        $sql = "ALTER TABLE carteira ADD juros_diarios DECIMAL(10,2) NOT NULL DEFAULT 0.00";
        $connect->exec($sql);
    } catch (PDOException $e) {
    }
    try {
        $sql = "ALTER TABLE financeiro2 ADD juros_calculados INT DEFAULT 0";
        $connect->exec($sql);
    } catch (PDOException $e) {
    }
    try {
        $sql = "ALTER TABLE financeiro2 ADD taxa_juros_diaria DECIMAL(10,2) DEFAULT 0.00";
        $connect->exec($sql);
    } catch (PDOException $e) {
    }
    try {
        $sql = "ALTER TABLE financeiro2 ADD COLUMN dias_vencidos INT DEFAULT 0";
        $connect->exec($sql);
    } catch (PDOException $e) {
    }
    try {
        $sql = "ALTER TABLE financeiro1 MODIFY valorfinal BIGINT;";
        $connect->exec($sql);
    } catch (PDOException $e) {
    }
    try {
        $sql = "ALTER TABLE message_queue ADD timestamp DATETIME;";
        $connect->exec($sql);
    } catch (PDOException $e) {
    }
    try {
        $sql = "ALTER TABLE financeiro1 MODIFY pagoem VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'n';";
        $connect->exec($sql);
    } catch (PDOException $e) {
    }
    try {
        $sql = "CREATE TABLE IF NOT EXISTS message_queue (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(255) NOT NULL,
            phone VARCHAR(255) NOT NULL,
            message TEXT,
            media TEXT,
            status VARCHAR(255) NOT NULL
        )";
        $connect->exec($sql);
    } catch (PDOException $e) {
    }
    $sql = "CREATE TABLE IF NOT EXISTS videos (
        id INT AUTO_INCREMENT PRIMARY KEY,
        link VARCHAR(255) NOT NULL,
        title VARCHAR(255)
    )";
    if ($connect->exec($sql) === 0) {
    } else {
    }
}

?>
