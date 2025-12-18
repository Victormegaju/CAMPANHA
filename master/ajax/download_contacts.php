<?php
session_start();
require_once __DIR__ . '/../../db/Conexao.php';

if (!isset($_SESSION['cod_id'])) {
    die('Não autenticado');
}

$cod_id = $_SESSION['cod_id'];
$format = $_GET['format'] ?? 'csv';

try {
    // Get contacts
    $query = $connect->prepare("SELECT * FROM whatsapp_contacts WHERE id_usuario = ? ORDER BY data_captura DESC");
    $query->execute([$cod_id]);
    $contacts = $query->fetchAll(PDO::FETCH_OBJ);
    
    if ($format === 'csv') {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=contatos_whatsapp_' . date('Y-m-d_H-i-s') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        // Add BOM for UTF-8
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Header
        fputcsv($output, ['Número', 'Nome', 'Tipo', 'Origem', 'Data de Captura'], ';');
        
        // Data
        foreach ($contacts as $contact) {
            fputcsv($output, [
                $contact->contact_number,
                $contact->contact_name ?? 'N/A',
                $contact->is_group ? 'Grupo' : 'Contato',
                $contact->source == 'contacts' ? 'Lista' : 'Conversas',
                date('d/m/Y H:i', strtotime($contact->data_captura))
            ], ';');
        }
        
        fclose($output);
    }
} catch (Exception $e) {
    die('Erro ao gerar arquivo: ' . $e->getMessage());
}
?>
