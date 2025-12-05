<?php
/**
 * Script para criar o banco de dados automaticamente
 * Execute: php criar_banco.php
 */

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '1234');

echo "🔧 Criando banco de dados processos_aereos...\n\n";

try {
    // Conectar sem especificar banco de dados
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
    
    // Criar banco de dados
    $pdo->exec("CREATE DATABASE IF NOT EXISTS processos_aereos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✅ Banco de dados 'processos_aereos' criado com sucesso!\n\n";
    
    // Selecionar o banco
    $pdo->exec("USE processos_aereos");
    
    // Ler e executar o arquivo SQL
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    // Remover a linha CREATE DATABASE e USE se existir
    $sql = preg_replace('/CREATE DATABASE.*?;/i', '', $sql);
    $sql = preg_replace('/USE.*?;/i', '', $sql);
    
    // Dividir em comandos individuais
    $commands = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($commands as $command) {
        if (!empty($command) && !preg_match('/^--/', $command)) {
            try {
                $pdo->exec($command);
            } catch (PDOException $e) {
                // Ignorar erros de "já existe"
                if (strpos($e->getMessage(), 'already exists') === false) {
                    echo "⚠️  Aviso: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    echo "✅ Tabelas criadas com sucesso!\n\n";
    
    // Verificar tabelas
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "📊 Tabelas no banco de dados:\n";
    foreach ($tables as $table) {
        echo "   - $table\n";
    }
    
    echo "\n✅ Banco de dados configurado com sucesso!\n";
    echo "🌐 Agora você pode acessar: http://localhost:8000/index.html\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    echo "\n💡 Verifique:\n";
    echo "   1. MySQL está rodando\n";
    echo "   2. Credenciais estão corretas (usuário: root, senha: 1234)\n";
    echo "   3. Você tem permissões para criar bancos de dados\n";
    exit(1);
}
?>


