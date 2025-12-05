<?php
/**
 * API REST para CRUD de Processos Aéreos
 * Sistema H&E - Gestão de Processos Aéreos
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Tratar requisições OPTIONS (CORS preflight)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '1234');
define('DB_NAME', 'processos_aereos');

// Conectar ao banco de dados
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    $mensagem = 'Erro ao conectar ao banco de dados: ' . $e->getMessage();
    
    // Mensagem mais amigável se o banco não existir
    if (strpos($e->getMessage(), 'Unknown database') !== false) {
        $mensagem = 'Banco de dados não encontrado. Acesse http://localhost:8000/setup.php para criar o banco.';
    }
    
    echo json_encode(['error' => $mensagem]);
    exit();
}

// Obter método HTTP e dados
$method = $_SERVER['REQUEST_METHOD'];
$input = json_decode(file_get_contents('php://input'), true);

// Roteamento
switch ($method) {
    case 'GET':
        handleGet($pdo);
        break;
    case 'POST':
        handlePost($pdo, $input);
        break;
    case 'PUT':
        handlePut($pdo, $input);
        break;
    case 'DELETE':
        handleDelete($pdo);
        break;
    default:
        http_response_code(405);
        echo json_encode(['error' => 'Método não permitido']);
}

// ============================================
// GET - Listar ou buscar processo
// ============================================
function handleGet($pdo) {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $search = isset($_GET['search']) ? $_GET['search'] : null;
    $entity = isset($_GET['entity']) ? $_GET['entity'] : null;
    
    try {
        // Listar empresas
        if ($entity === 'empresas') {
            $stmt = $pdo->query("SELECT * FROM empresas ORDER BY nome");
            $empresas = $stmt->fetchAll();
            echo json_encode($empresas);
            return;
        }
        
        // Listar responsáveis
        if ($entity === 'responsaveis') {
            $stmt = $pdo->query("SELECT * FROM responsaveis ORDER BY nome");
            $responsaveis = $stmt->fetchAll();
            echo json_encode($responsaveis);
            return;
        }
        
        // Buscar empresa por ID
        if ($entity === 'empresa' && $id) {
            $stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            $empresa = $stmt->fetch();
            if ($empresa) {
                echo json_encode($empresa);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Empresa não encontrada']);
            }
            return;
        }
        
        // Buscar responsável por ID
        if ($entity === 'responsavel' && $id) {
            $stmt = $pdo->prepare("SELECT * FROM responsaveis WHERE id = ?");
            $stmt->execute([$id]);
            $responsavel = $stmt->fetch();
            if ($responsavel) {
                echo json_encode($responsavel);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Responsável não encontrado']);
            }
            return;
        }
        
        // Processos: Buscar por ID com JOIN
        if ($id) {
            $stmt = $pdo->prepare("
                SELECT 
                    p.*,
                    e.nome AS empresa_nome,
                    e.cnpj AS empresa_cnpj,
                    r.nome AS responsavel_nome,
                    r.cargo AS responsavel_cargo
                FROM processos_aereos p
                INNER JOIN empresas e ON p.empresa_id = e.id
                INNER JOIN responsaveis r ON p.responsavel_id = r.id
                WHERE p.id = ?
            ");
            $stmt->execute([$id]);
            $processo = $stmt->fetch();
            
            if ($processo) {
                echo json_encode($processo);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Processo não encontrado']);
            }
        } elseif ($search) {
            // Busca geral com JOIN
            $searchTerm = "%{$search}%";
            $stmt = $pdo->prepare("
                SELECT 
                    p.*,
                    e.nome AS empresa_nome,
                    e.cnpj AS empresa_cnpj,
                    r.nome AS responsavel_nome,
                    r.cargo AS responsavel_cargo
                FROM processos_aereos p
                INNER JOIN empresas e ON p.empresa_id = e.id
                INNER JOIN responsaveis r ON p.responsavel_id = r.id
                WHERE 
                    p.numero_processo LIKE ? OR
                    p.tipo_processo LIKE ? OR
                    e.nome LIKE ? OR
                    r.nome LIKE ? OR
                    p.status LIKE ? OR
                    p.observacoes LIKE ?
                ORDER BY p.data_criacao DESC
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $processos = $stmt->fetchAll();
            echo json_encode($processos);
        } else {
            // Listar todos com JOIN
            $stmt = $pdo->query("
                SELECT 
                    p.*,
                    e.nome AS empresa_nome,
                    e.cnpj AS empresa_cnpj,
                    r.nome AS responsavel_nome,
                    r.cargo AS responsavel_cargo
                FROM processos_aereos p
                INNER JOIN empresas e ON p.empresa_id = e.id
                INNER JOIN responsaveis r ON p.responsavel_id = r.id
                ORDER BY p.data_criacao DESC
            ");
            $processos = $stmt->fetchAll();
            echo json_encode($processos);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao buscar dados: ' . $e->getMessage()]);
    }
}

// ============================================
// POST - Criar novo processo, empresa ou responsável
// ============================================
function handlePost($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Dados inválidos']);
        return;
    }
    
    $entity = isset($_GET['entity']) ? $_GET['entity'] : null;
    
    // Criar empresa
    if ($entity === 'empresas') {
        if (empty($input['nome'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Campo obrigatório faltando: nome']);
            return;
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO empresas (nome, cnpj) VALUES (?, ?)");
            $stmt->execute([
                $input['nome'],
                $input['cnpj'] ?? null
            ]);
            
            $id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            $empresa = $stmt->fetch();
            
            http_response_code(201);
            echo json_encode($empresa);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao criar empresa: ' . $e->getMessage()]);
        }
        return;
    }
    
    // Criar responsável
    if ($entity === 'responsaveis') {
        if (empty($input['nome'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Campo obrigatório faltando: nome']);
            return;
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO responsaveis (nome, cargo) VALUES (?, ?)");
            $stmt->execute([
                $input['nome'],
                $input['cargo'] ?? null
            ]);
            
            $id = $pdo->lastInsertId();
            $stmt = $pdo->prepare("SELECT * FROM responsaveis WHERE id = ?");
            $stmt->execute([$id]);
            $responsavel = $stmt->fetch();
            
            http_response_code(201);
            echo json_encode($responsavel);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao criar responsável: ' . $e->getMessage()]);
        }
        return;
    }
    
    // Criar processo (padrão)
    // Validar campos obrigatórios
    $required = ['numero_processo', 'tipo_processo', 'empresa_id', 'responsavel_id', 'data_inicio', 'status'];
    foreach ($required as $field) {
        if (empty($input[$field])) {
            http_response_code(400);
            echo json_encode(['error' => "Campo obrigatório faltando: {$field}"]);
            return;
        }
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO processos_aereos 
            (numero_processo, tipo_processo, empresa_id, responsavel_id, data_inicio, data_prevista, status, observacoes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $input['numero_processo'],
            $input['tipo_processo'],
            $input['empresa_id'],
            $input['responsavel_id'],
            $input['data_inicio'],
            $input['data_prevista'] ?? null,
            $input['status'],
            $input['observacoes'] ?? null
        ]);
        
        $id = $pdo->lastInsertId();
        // Retornar com JOIN para incluir nomes
        $stmt = $pdo->prepare("
            SELECT 
                p.*,
                e.nome AS empresa_nome,
                e.cnpj AS empresa_cnpj,
                r.nome AS responsavel_nome,
                r.cargo AS responsavel_cargo
            FROM processos_aereos p
            INNER JOIN empresas e ON p.empresa_id = e.id
            INNER JOIN responsaveis r ON p.responsavel_id = r.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $processo = $stmt->fetch();
        
        http_response_code(201);
        echo json_encode($processo);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            http_response_code(409);
            echo json_encode(['error' => 'Número de processo já existe']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao criar processo: ' . $e->getMessage()]);
        }
    }
}

// ============================================
// PUT - Atualizar processo, empresa ou responsável
// ============================================
function handlePut($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID é obrigatório']);
        return;
    }
    
    $id = (int)$input['id'];
    $entity = isset($_GET['entity']) ? $_GET['entity'] : null;
    
    // Atualizar empresa
    if ($entity === 'empresas') {
        try {
            $stmt = $pdo->prepare("SELECT id FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['error' => 'Empresa não encontrada']);
                return;
            }
            
            $stmt = $pdo->prepare("UPDATE empresas SET nome = ?, cnpj = ? WHERE id = ?");
            $stmt->execute([
                $input['nome'] ?? '',
                $input['cnpj'] ?? null,
                $id
            ]);
            
            $stmt = $pdo->prepare("SELECT * FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            $empresa = $stmt->fetch();
            
            echo json_encode($empresa);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar empresa: ' . $e->getMessage()]);
        }
        return;
    }
    
    // Atualizar responsável
    if ($entity === 'responsaveis') {
        try {
            $stmt = $pdo->prepare("SELECT id FROM responsaveis WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['error' => 'Responsável não encontrado']);
                return;
            }
            
            $stmt = $pdo->prepare("UPDATE responsaveis SET nome = ?, cargo = ? WHERE id = ?");
            $stmt->execute([
                $input['nome'] ?? '',
                $input['cargo'] ?? null,
                $id
            ]);
            
            $stmt = $pdo->prepare("SELECT * FROM responsaveis WHERE id = ?");
            $stmt->execute([$id]);
            $responsavel = $stmt->fetch();
            
            echo json_encode($responsavel);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao atualizar responsável: ' . $e->getMessage()]);
        }
        return;
    }
    
    // Atualizar processo (padrão)
    try {
        // Verificar se processo existe
        $stmt = $pdo->prepare("SELECT id FROM processos_aereos WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Processo não encontrado']);
            return;
        }
        
        // Atualizar
        $stmt = $pdo->prepare("
            UPDATE processos_aereos 
            SET 
                numero_processo = ?,
                tipo_processo = ?,
                empresa_id = ?,
                responsavel_id = ?,
                data_inicio = ?,
                data_prevista = ?,
                status = ?,
                observacoes = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $input['numero_processo'],
            $input['tipo_processo'],
            $input['empresa_id'],
            $input['responsavel_id'],
            $input['data_inicio'],
            $input['data_prevista'] ?? null,
            $input['status'],
            $input['observacoes'] ?? null,
            $id
        ]);
        
        // Retornar processo atualizado com JOIN
        $stmt = $pdo->prepare("
            SELECT 
                p.*,
                e.nome AS empresa_nome,
                e.cnpj AS empresa_cnpj,
                r.nome AS responsavel_nome,
                r.cargo AS responsavel_cargo
            FROM processos_aereos p
            INNER JOIN empresas e ON p.empresa_id = e.id
            INNER JOIN responsaveis r ON p.responsavel_id = r.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        $processo = $stmt->fetch();
        
        echo json_encode($processo);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar processo: ' . $e->getMessage()]);
    }
}

// ============================================
// DELETE - Excluir processo, empresa ou responsável
// ============================================
function handleDelete($pdo) {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    $entity = isset($_GET['entity']) ? $_GET['entity'] : null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID é obrigatório']);
        return;
    }
    
    // Excluir empresa
    if ($entity === 'empresas') {
        try {
            $stmt = $pdo->prepare("SELECT id FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['error' => 'Empresa não encontrada']);
                return;
            }
            
            // Verificar se há processos usando esta empresa
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM processos_aereos WHERE empresa_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            if ($result['count'] > 0) {
                http_response_code(409);
                echo json_encode(['error' => 'Não é possível excluir empresa que possui processos associados']);
                return;
            }
            
            $stmt = $pdo->prepare("DELETE FROM empresas WHERE id = ?");
            $stmt->execute([$id]);
            
            http_response_code(200);
            echo json_encode(['message' => 'Empresa excluída com sucesso']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao excluir empresa: ' . $e->getMessage()]);
        }
        return;
    }
    
    // Excluir responsável
    if ($entity === 'responsaveis') {
        try {
            $stmt = $pdo->prepare("SELECT id FROM responsaveis WHERE id = ?");
            $stmt->execute([$id]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['error' => 'Responsável não encontrado']);
                return;
            }
            
            // Verificar se há processos usando este responsável
            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM processos_aereos WHERE responsavel_id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch();
            if ($result['count'] > 0) {
                http_response_code(409);
                echo json_encode(['error' => 'Não é possível excluir responsável que possui processos associados']);
                return;
            }
            
            $stmt = $pdo->prepare("DELETE FROM responsaveis WHERE id = ?");
            $stmt->execute([$id]);
            
            http_response_code(200);
            echo json_encode(['message' => 'Responsável excluído com sucesso']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Erro ao excluir responsável: ' . $e->getMessage()]);
        }
        return;
    }
    
    // Excluir processo (padrão)
    try {
        // Verificar se processo existe
        $stmt = $pdo->prepare("SELECT id FROM processos_aereos WHERE id = ?");
        $stmt->execute([$id]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Processo não encontrado']);
            return;
        }
        
        // Excluir
        $stmt = $pdo->prepare("DELETE FROM processos_aereos WHERE id = ?");
        $stmt->execute([$id]);
        
        http_response_code(200);
        echo json_encode(['message' => 'Processo excluído com sucesso']);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao excluir processo: ' . $e->getMessage()]);
    }
}
?>
