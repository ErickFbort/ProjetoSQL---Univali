-- ============================================
-- QUERIES CRUD - Sistema de Processos Aéreos
-- ============================================

USE processos_aereos;

-- ============================================
-- CREATE (INSERT) - Criar novo processo
-- ============================================

-- Inserir um novo processo aéreo
INSERT INTO processos_aereos 
(numero_processo, tipo_processo, empresa, responsavel, data_inicio, data_prevista, status, observacoes) 
VALUES 
('PRO-2024-005', 'Licenciamento', 'Aeroporto Regional Norte', 'Carlos Mendes', '2024-03-01', '2024-05-01', 'Em Análise', 'Processo de licenciamento para expansão do terminal');

-- Inserir processo sem data prevista (campo opcional)
INSERT INTO processos_aereos 
(numero_processo, tipo_processo, empresa, responsavel, data_inicio, status, observacoes) 
VALUES 
('PRO-2024-006', 'Certificação', 'Empresa de Aviação Executiva', 'Fernanda Lima', '2024-03-10', 'Pendente', 'Certificação de aeronave executiva');

-- ============================================
-- READ (SELECT) - Ler/Consultar processos
-- ============================================

-- Listar todos os processos
SELECT * FROM processos_aereos ORDER BY data_criacao DESC;

-- Listar processos com informações formatadas
SELECT 
    id,
    numero_processo,
    tipo_processo,
    empresa,
    responsavel,
    DATE_FORMAT(data_inicio, '%d/%m/%Y') AS data_inicio_formatada,
    DATE_FORMAT(data_prevista, '%d/%m/%Y') AS data_prevista_formatada,
    status,
    observacoes,
    DATE_FORMAT(data_criacao, '%d/%m/%Y %H:%i:%s') AS data_criacao_formatada,
    DATE_FORMAT(data_atualizacao, '%d/%m/%Y %H:%i:%s') AS data_atualizacao_formatada
FROM processos_aereos 
ORDER BY data_criacao DESC;

-- Buscar processo por ID
SELECT * FROM processos_aereos WHERE id = 1;

-- Buscar processo por número
SELECT * FROM processos_aereos WHERE numero_processo = 'PRO-2024-001';

-- Buscar processos por status
SELECT * FROM processos_aereos WHERE status = 'Em Análise';

-- Buscar processos por tipo
SELECT * FROM processos_aereos WHERE tipo_processo = 'Licenciamento';

-- Buscar processos por empresa
SELECT * FROM processos_aereos WHERE empresa LIKE '%São Paulo%';

-- Buscar processos por responsável
SELECT * FROM processos_aereos WHERE responsavel LIKE '%Silva%';

-- Busca geral (busca em múltiplos campos)
SELECT * FROM processos_aereos 
WHERE 
    numero_processo LIKE '%2024%' OR
    empresa LIKE '%Aeroporto%' OR
    responsavel LIKE '%João%' OR
    observacoes LIKE '%licenciamento%'
ORDER BY data_criacao DESC;

-- Contar processos por status
SELECT status, COUNT(*) AS total 
FROM processos_aereos 
GROUP BY status;

-- Contar processos por tipo
SELECT tipo_processo, COUNT(*) AS total 
FROM processos_aereos 
GROUP BY tipo_processo;

-- Processos com data prevista vencendo em 30 dias
SELECT * FROM processos_aereos 
WHERE data_prevista BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY)
AND status NOT IN ('Concluído', 'Rejeitado')
ORDER BY data_prevista ASC;

-- Processos atrasados (data prevista passou e não está concluído)
SELECT * FROM processos_aereos 
WHERE data_prevista < CURDATE() 
AND status NOT IN ('Concluído', 'Rejeitado')
ORDER BY data_prevista ASC;

-- ============================================
-- UPDATE - Atualizar processo existente
-- ============================================

-- Atualizar status de um processo
UPDATE processos_aereos 
SET status = 'Aprovado' 
WHERE id = 1;

-- Atualizar múltiplos campos
UPDATE processos_aereos 
SET 
    status = 'Concluído',
    data_prevista = '2024-03-20',
    observacoes = CONCAT(IFNULL(observacoes, ''), '\n\nAtualização: Processo concluído com sucesso em ', CURDATE())
WHERE id = 1;

-- Atualizar data prevista
UPDATE processos_aereos 
SET data_prevista = '2024-06-01' 
WHERE id = 2;

-- Atualizar responsável
UPDATE processos_aereos 
SET responsavel = 'Novo Responsável' 
WHERE id = 3;

-- Atualizar observações (adicionar ao final)
UPDATE processos_aereos 
SET observacoes = CONCAT(IFNULL(observacoes, ''), '\n\nNova observação adicionada em ', NOW())
WHERE id = 1;

-- ============================================
-- DELETE - Excluir processo
-- ============================================

-- Excluir processo por ID
DELETE FROM processos_aereos WHERE id = 5;

-- Excluir processo por número
DELETE FROM processos_aereos WHERE numero_processo = 'PRO-2024-006';

-- Excluir processos concluídos há mais de 1 ano (exemplo de limpeza)
-- ATENÇÃO: Use com cuidado! Descomente apenas se necessário
-- DELETE FROM processos_aereos 
-- WHERE status = 'Concluído' 
-- AND data_atualizacao < DATE_SUB(NOW(), INTERVAL 1 YEAR);

-- ============================================
-- QUERIES ÚTEIS E RELATÓRIOS
-- ============================================

-- Total de processos cadastrados
SELECT COUNT(*) AS total_processos FROM processos_aereos;

-- Processos criados no mês atual
SELECT COUNT(*) AS processos_mes_atual 
FROM processos_aereos 
WHERE MONTH(data_criacao) = MONTH(CURDATE()) 
AND YEAR(data_criacao) = YEAR(CURDATE());

-- Processos por mês (últimos 6 meses)
SELECT 
    DATE_FORMAT(data_criacao, '%Y-%m') AS mes,
    COUNT(*) AS total
FROM processos_aereos
WHERE data_criacao >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
GROUP BY DATE_FORMAT(data_criacao, '%Y-%m')
ORDER BY mes DESC;

-- Top 5 empresas com mais processos
SELECT empresa, COUNT(*) AS total_processos 
FROM processos_aereos 
GROUP BY empresa 
ORDER BY total_processos DESC 
LIMIT 5;

-- Top 5 responsáveis com mais processos
SELECT responsavel, COUNT(*) AS total_processos 
FROM processos_aereos 
GROUP BY responsavel 
ORDER BY total_processos DESC 
LIMIT 5;

-- Tempo médio de conclusão de processos (em dias)
SELECT 
    AVG(DATEDIFF(data_atualizacao, data_inicio)) AS tempo_medio_dias
FROM processos_aereos 
WHERE status = 'Concluído';

-- Processos por faixa de tempo até conclusão
SELECT 
    CASE 
        WHEN DATEDIFF(data_prevista, CURDATE()) < 0 THEN 'Atrasado'
        WHEN DATEDIFF(data_prevista, CURDATE()) <= 7 THEN 'Próximos 7 dias'
        WHEN DATEDIFF(data_prevista, CURDATE()) <= 30 THEN 'Próximos 30 dias'
        ELSE 'Mais de 30 dias'
    END AS faixa_tempo,
    COUNT(*) AS total
FROM processos_aereos
WHERE data_prevista IS NOT NULL
AND status NOT IN ('Concluído', 'Rejeitado')
GROUP BY faixa_tempo;

