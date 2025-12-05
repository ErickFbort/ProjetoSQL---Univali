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
-- TABELA: processos_aereos
-- ============================================
CREATE TABLE IF NOT EXISTS processos_aereos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    numero_processo VARCHAR(50) NOT NULL UNIQUE COMMENT 'Número único do processo (ex: PRO-2024-001)',
    tipo_processo ENUM('Licenciamento', 'Autorização', 'Certificação', 'Fiscalização', 'Outro') NOT NULL COMMENT 'Tipo do processo',
    empresa VARCHAR(255) NOT NULL COMMENT 'Nome da empresa/organização',
    responsavel VARCHAR(255) NOT NULL COMMENT 'Nome do responsável pelo processo',
    data_inicio DATE NOT NULL COMMENT 'Data de início do processo',
    data_prevista DATE NULL COMMENT 'Data prevista de conclusão',
    status ENUM('Em Análise', 'Aprovado', 'Rejeitado', 'Pendente', 'Concluído') NOT NULL DEFAULT 'Em Análise' COMMENT 'Status atual do processo',
    observacoes TEXT NULL COMMENT 'Observações e informações adicionais',
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data e hora de criação do registro',
    data_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Data e hora da última atualização',
    
    -- Índices para melhorar performance nas buscas
    INDEX idx_numero_processo (numero_processo),
    INDEX idx_tipo_processo (tipo_processo),
    INDEX idx_empresa (empresa),
    INDEX idx_status (status),
    INDEX idx_data_inicio (data_inicio),
    INDEX idx_data_criacao (data_criacao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tabela principal para armazenar processos aéreos';

-- ============================================
-- INSERIR DADOS DE EXEMPLO (OPCIONAL)
-- ============================================
INSERT INTO processos_aereos 
(numero_processo, tipo_processo, empresa, responsavel, data_inicio, data_prevista, status, observacoes) 
VALUES 
('PRO-2024-001', 'Licenciamento', 'Aeroporto Internacional São Paulo', 'João Silva', '2024-01-15', '2024-03-15', 'Em Análise', 'Processo de licenciamento para nova pista'),
('PRO-2024-002', 'Certificação', 'Companhia Aérea Brasileira', 'Maria Santos', '2024-02-01', '2024-04-01', 'Aprovado', 'Certificação de aeronave modelo Boeing 737'),
('PRO-2024-003', 'Autorização', 'Heliporto Central', 'Pedro Oliveira', '2024-02-10', NULL, 'Pendente', 'Autorização para operação noturna'),
('PRO-2024-004', 'Fiscalização', 'Aeroclube Regional', 'Ana Costa', '2024-01-20', '2024-02-20', 'Concluído', 'Fiscalização de segurança concluída com sucesso');

