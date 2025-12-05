<?php
/**
 * Página de Setup - Criar banco de dados
 * Acesse: http://localhost:8000/setup.php
 */

header('Content-Type: text/html; charset=utf-8');

// Configurações
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '1234');
define('DB_NAME', 'processos_aereos');

$mensagem = '';
$erro = '';
$sucesso = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['criar_banco'])) {
    try {
        // Conectar sem especificar banco
        $pdo = new PDO(
            "mysql:host=" . DB_HOST . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        
        // Criar banco
        $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE " . DB_NAME);
        
        // Criar tabela
        $sql = "
        CREATE TABLE IF NOT EXISTS processos_aereos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            numero_processo VARCHAR(50) NOT NULL UNIQUE,
            tipo_processo ENUM('Licenciamento', 'Autorização', 'Certificação', 'Fiscalização', 'Outro') NOT NULL,
            empresa VARCHAR(255) NOT NULL,
            responsavel VARCHAR(255) NOT NULL,
            data_inicio DATE NOT NULL,
            data_prevista DATE NULL,
            status ENUM('Em Análise', 'Aprovado', 'Rejeitado', 'Pendente', 'Concluído') NOT NULL DEFAULT 'Em Análise',
            observacoes TEXT NULL,
            data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_numero_processo (numero_processo),
            INDEX idx_tipo_processo (tipo_processo),
            INDEX idx_empresa (empresa),
            INDEX idx_status (status)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        
        $pdo->exec($sql);
        
        $sucesso = true;
        $mensagem = "✅ Banco de dados criado com sucesso!";
        
    } catch (PDOException $e) {
        $erro = "❌ Erro: " . $e->getMessage();
    }
}

// Verificar se banco existe
$bancoExiste = false;
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS
    );
    $bancoExiste = true;
} catch (PDOException $e) {
    $bancoExiste = false;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - Sistema H&E</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1f3a 0%, #2c3e7f 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            max-width: 600px;
            width: 100%;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }
        h1 {
            color: #1a1f3a;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #666;
            margin-bottom: 30px;
        }
        .status {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .status.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status.info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .btn {
            background: #ff6b35;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-size: 1em;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
        .btn:hover {
            background: #e55a2b;
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #ff6b35;
            text-decoration: none;
        }
        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Setup do Banco de Dados</h1>
        <p class="subtitle">Sistema H&E - Gestão de Processos Aéreos</p>
        
        <?php if ($sucesso): ?>
            <div class="status success">
                <?php echo $mensagem; ?>
            </div>
            <a href="index.html" class="link">➡️ Ir para o Sistema</a>
        <?php elseif ($erro): ?>
            <div class="status error">
                <?php echo $erro; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($bancoExiste): ?>
            <div class="status info">
                ✅ Banco de dados 'processos_aereos' já existe e está configurado!
            </div>
            <a href="index.html" class="link">➡️ Ir para o Sistema</a>
        <?php else: ?>
            <div class="status info">
                ⚠️ Banco de dados não encontrado. Clique no botão abaixo para criar.
            </div>
            <form method="POST">
                <button type="submit" name="criar_banco" class="btn">
                    Criar Banco de Dados
                </button>
            </form>
            <p style="margin-top: 20px; color: #666; font-size: 0.9em;">
                <strong>Configuração:</strong><br>
                Host: <?php echo DB_HOST; ?><br>
                Usuário: <?php echo DB_USER; ?><br>
                Banco: <?php echo DB_NAME; ?>
            </p>
        <?php endif; ?>
    </div>
</body>
</html>

