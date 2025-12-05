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
define('DB_PASS', '');
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
    echo json_encode(['error' => 'Erro ao conectar ao banco de dados: ' . $e->getMessage()]);
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
    
    try {
        if ($id) {
            // Buscar por ID
            $stmt = $pdo->prepare("SELECT * FROM processos_aereos WHERE id = ?");
            $stmt->execute([$id]);
            $processo = $stmt->fetch();
            
            if ($processo) {
                echo json_encode($processo);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'Processo não encontrado']);
            }
        } elseif ($search) {
            // Busca geral
            $searchTerm = "%{$search}%";
            $stmt = $pdo->prepare("
                SELECT * FROM processos_aereos 
                WHERE 
                    numero_processo LIKE ? OR
                    tipo_processo LIKE ? OR
                    empresa LIKE ? OR
                    responsavel LIKE ? OR
                    status LIKE ? OR
                    observacoes LIKE ?
                ORDER BY data_criacao DESC
            ");
            $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
            $processos = $stmt->fetchAll();
            echo json_encode($processos);
        } else {
            // Listar todos
            $stmt = $pdo->query("SELECT * FROM processos_aereos ORDER BY data_criacao DESC");
            $processos = $stmt->fetchAll();
            echo json_encode($processos);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao buscar processos: ' . $e->getMessage()]);
    }
}

// ============================================
// POST - Criar novo processo
// ============================================
function handlePost($pdo, $input) {
    if (!$input) {
        http_response_code(400);
        echo json_encode(['error' => 'Dados inválidos']);
        return;
    }
    
    // Validar campos obrigatórios
    $required = ['numero_processo', 'tipo_processo', 'empresa', 'responsavel', 'data_inicio', 'status'];
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
            (numero_processo, tipo_processo, empresa, responsavel, data_inicio, data_prevista, status, observacoes) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $input['numero_processo'],
            $input['tipo_processo'],
            $input['empresa'],
            $input['responsavel'],
            $input['data_inicio'],
            $input['data_prevista'] ?? null,
            $input['status'],
            $input['observacoes'] ?? null
        ]);
        
        $id = $pdo->lastInsertId();
        $stmt = $pdo->prepare("SELECT * FROM processos_aereos WHERE id = ?");
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
// PUT - Atualizar processo
// ============================================
function handlePut($pdo, $input) {
    if (!$input || !isset($input['id'])) {
        http_response_code(400);
        echo json_encode(['error' => 'ID do processo é obrigatório']);
        return;
    }
    
    $id = (int)$input['id'];
    
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
                empresa = ?,
                responsavel = ?,
                data_inicio = ?,
                data_prevista = ?,
                status = ?,
                observacoes = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $input['numero_processo'],
            $input['tipo_processo'],
            $input['empresa'],
            $input['responsavel'],
            $input['data_inicio'],
            $input['data_prevista'] ?? null,
            $input['status'],
            $input['observacoes'] ?? null,
            $id
        ]);
        
        // Retornar processo atualizado
        $stmt = $pdo->prepare("SELECT * FROM processos_aereos WHERE id = ?");
        $stmt->execute([$id]);
        $processo = $stmt->fetch();
        
        echo json_encode($processo);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao atualizar processo: ' . $e->getMessage()]);
    }
}

// ============================================
// DELETE - Excluir processo
// ============================================
function handleDelete($pdo) {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : null;
    
    if (!$id) {
        http_response_code(400);
        echo json_encode(['error' => 'ID do processo é obrigatório']);
        return;
    }
    
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

