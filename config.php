<?php
/**
 * Arquivo de configuração do banco de dados
 * Ajuste as credenciais conforme seu ambiente
 */

return [
    'host' => 'localhost',
    'database' => 'processos_aereos',
    'username' => 'root',
    'password' => '1234',
    'charset' => 'utf8mb4',
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]
];
?>

