<?php
/**
 * Evolution API Configuration
 * Centralized configuration to avoid duplication across files
 */

// Default Evolution API settings
// These can be overridden by user-specific settings in the database
define('EVOLUTION_API_URL', 'http://whatsapp.painelcontrole.xyz:8080');
define('EVOLUTION_API_KEY', '4FAf4CAnP4jKtbhp6guW1HVbDAhgLmQxO');

/**
 * Get Evolution API configuration for a user
 * @param object $dadosgerais User data object from database
 * @return array ['url' => string, 'key' => string]
 */
function getEvolutionConfig($dadosgerais = null) {
    return [
        'url' => EVOLUTION_API_URL,
        'key' => $dadosgerais->tokenapi ?? EVOLUTION_API_KEY
    ];
}
?>
