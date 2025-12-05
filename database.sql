-- ============================================
-- BANCO DE DADOS: Sistema de Gestão de Processos Aéreos
-- ============================================

-- Criar o banco de dados
CREATE DATABASE IF NOT EXISTS processos_aereos 
CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

-- Selecionar o banco de dados
USE processos_aereos;

-- ============================================
-- TABELA: empresas
-- ============================================
CREATE TABLE IF NOT EXISTS empresas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL COMMENT 'Nome da empresa/organização',
    cnpj VARCHAR(20) NULL COMMENT 'CNPJ da empresa',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora de criação do registro',
    
    -- Índices para melhorar performance nas buscas
    INDEX idx_nome (nome),
    INDEX idx_cnpj (cnpj)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tabela para armazenar empresas/organizações';

-- ============================================
-- TABELA: responsaveis
-- ============================================
CREATE TABLE IF NOT EXISTS responsaveis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL COMMENT 'Nome do responsável',
    cargo VARCHAR(100) NULL COMMENT 'Cargo do responsável',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora de criação do registro',
    
    -- Índices para melhorar performance nas buscas
    INDEX idx_nome (nome)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tabela para armazenar responsáveis pelos processos';

-- ============================================
-- TABELA: processos_aereos
-- ============================================
CREATE TABLE IF NOT EXISTS processos_aereos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_processo VARCHAR(50) NOT NULL UNIQUE COMMENT 'Número único do processo (ex: PRO-2024-001)',
    tipo_processo ENUM('Licenciamento', 'Autorização', 'Certificação', 'Fiscalização', 'Outro') NOT NULL COMMENT 'Tipo do processo',
    empresa_id INT NOT NULL COMMENT 'ID da empresa (chave estrangeira)',
    responsavel_id INT NOT NULL COMMENT 'ID do responsável (chave estrangeira)',
    data_inicio DATE NOT NULL COMMENT 'Data de início do processo',
    data_prevista DATE NULL COMMENT 'Data prevista de conclusão',
    status ENUM('Em Análise', 'Aprovado', 'Rejeitado', 'Pendente', 'Concluído') NOT NULL DEFAULT 'Em Análise' COMMENT 'Status atual do processo',
    observacoes TEXT NULL COMMENT 'Observações e informações adicionais',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora de criação do registro',
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data e hora da última atualização',
    
    -- Chaves estrangeiras
    FOREIGN KEY (empresa_id) REFERENCES empresas(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    FOREIGN KEY (responsavel_id) REFERENCES responsaveis(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Índices para melhorar performance nas buscas
    INDEX idx_numero_processo (numero_processo),
    INDEX idx_tipo_processo (tipo_processo),
    INDEX idx_empresa_id (empresa_id),
    INDEX idx_responsavel_id (responsavel_id),
    INDEX idx_status (status),
    INDEX idx_data_inicio (data_inicio),
    INDEX idx_data_criacao (data_criacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tabela principal para armazenar processos aéreos';

-- ============================================
-- INSERIR DADOS DE EXEMPLO (OPCIONAL)
-- ============================================

-- Inserir empresas de exemplo
INSERT INTO empresas (nome, cnpj) VALUES 
('Aeroporto Internacional São Paulo', '12.345.678/0001-90'),
('Companhia Aérea Brasileira', '23.456.789/0001-01'),
('Heliporto Central', '34.567.890/0001-12'),
('Aeroclube Regional', '45.678.901/0001-23');

-- Inserir responsáveis de exemplo
INSERT INTO responsaveis (nome, cargo) VALUES 
('João Silva', 'Gerente de Operações'),
('Maria Santos', 'Coordenadora de Certificação'),
('Pedro Oliveira', 'Supervisor de Autorizações'),
('Ana Costa', 'Fiscal de Segurança');

-- Inserir processos de exemplo
INSERT INTO processos_aereos 
(numero_processo, tipo_processo, empresa_id, responsavel_id, data_inicio, data_prevista, status, observacoes) 
VALUES 
('PRO-2024-001', 'Licenciamento', 1, 1, '2024-01-15', '2024-03-15', 'Em Análise', 'Processo de licenciamento para nova pista'),
('PRO-2024-002', 'Certificação', 2, 2, '2024-02-01', '2024-04-01', 'Aprovado', 'Certificação de aeronave modelo Boeing 737'),
('PRO-2024-003', 'Autorização', 3, 3, '2024-02-10', NULL, 'Pendente', 'Autorização para operação noturna'),
('PRO-2024-004', 'Fiscalização', 4, 4, '2024-01-20', '2024-02-20', 'Concluído', 'Fiscalização de segurança concluída com sucesso');

