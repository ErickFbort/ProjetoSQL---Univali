# 📊 Documentação do Banco de Dados - Sistema H&E

## 📋 Estrutura do Banco de Dados

### Arquivos SQL Criados

1. **`database.sql`** - Criação do banco de dados e tabela principal
2. **`crud_queries.sql`** - Exemplos de queries para operações CRUD
3. **`api.php`** - API REST em PHP para integração com o frontend
4. **`config.php`** - Arquivo de configuração do banco de dados

---

## 🚀 Instalação e Configuração

### 1. Criar o Banco de Dados

Execute o arquivo `database.sql` no MySQL:

```bash
mysql -u root -p < database.sql
```

Ou via MySQL Workbench / phpMyAdmin, copie e execute o conteúdo do arquivo.

### 2. Configurar Credenciais

Edite o arquivo `config.php` e `api.php` com suas credenciais do MySQL:

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'seu_usuario');
define('DB_PASS', 'sua_senha');
define('DB_NAME', 'processos_aereos');
```

### 3. Testar a API

Se estiver usando PHP, coloque os arquivos PHP em um servidor web (XAMPP, WAMP, etc.) e teste:

```bash
# Listar todos os processos
GET http://localhost/api.php

# Buscar processo por ID
GET http://localhost/api.php?id=1

# Buscar processos
GET http://localhost/api.php?search=aeroporto
```

---

## 📊 Estrutura da Tabela

### Tabela: `processos_aereos`

| Campo | Tipo | Descrição |
|-------|------|-----------|
| `id` | INT (AUTO_INCREMENT) | ID único do processo (chave primária) |
| `numero_processo` | VARCHAR(50) | Número único do processo (ex: PRO-2024-001) |
| `tipo_processo` | ENUM | Tipo: Licenciamento, Autorização, Certificação, Fiscalização, Outro |
| `empresa` | VARCHAR(255) | Nome da empresa/organização |
| `responsavel` | VARCHAR(255) | Nome do responsável |
| `data_inicio` | DATE | Data de início do processo |
| `data_prevista` | DATE | Data prevista de conclusão (opcional) |
| `status` | ENUM | Status: Em Análise, Aprovado, Rejeitado, Pendente, Concluído |
| `observacoes` | TEXT | Observações adicionais (opcional) |
| `data_criacao` | TIMESTAMP | Data/hora de criação (automático) |
| `data_atualizacao` | TIMESTAMP | Data/hora da última atualização (automático) |

---

## 🔧 Operações CRUD

### CREATE (Inserir)

```sql
INSERT INTO processos_aereos 
(numero_processo, tipo_processo, empresa, responsavel, data_inicio, data_prevista, status, observacoes) 
VALUES 
('PRO-2024-001', 'Licenciamento', 'Empresa XYZ', 'João Silva', '2024-01-15', '2024-03-15', 'Em Análise', 'Observações aqui');
```

### READ (Consultar)

```sql
-- Listar todos
SELECT * FROM processos_aereos ORDER BY data_criacao DESC;

-- Buscar por ID
SELECT * FROM processos_aereos WHERE id = 1;

-- Buscar por número
SELECT * FROM processos_aereos WHERE numero_processo = 'PRO-2024-001';

-- Buscar por status
SELECT * FROM processos_aereos WHERE status = 'Em Análise';
```

### UPDATE (Atualizar)

```sql
UPDATE processos_aereos 
SET status = 'Aprovado', observacoes = 'Processo aprovado' 
WHERE id = 1;
```

### DELETE (Excluir)

```sql
DELETE FROM processos_aereos WHERE id = 1;
```

---

## 🌐 API REST (PHP)

### Endpoints Disponíveis

#### GET - Listar/Buscar Processos

```
GET /api.php                    # Lista todos os processos
GET /api.php?id=1               # Busca processo por ID
GET /api.php?search=aeroporto    # Busca geral
```

#### POST - Criar Processo

```json
POST /api.php
Content-Type: application/json

{
    "numero_processo": "PRO-2024-001",
    "tipo_processo": "Licenciamento",
    "empresa": "Empresa XYZ",
    "responsavel": "João Silva",
    "data_inicio": "2024-01-15",
    "data_prevista": "2024-03-15",
    "status": "Em Análise",
    "observacoes": "Observações aqui"
}
```

#### PUT - Atualizar Processo

```json
PUT /api.php
Content-Type: application/json

{
    "id": 1,
    "numero_processo": "PRO-2024-001",
    "tipo_processo": "Licenciamento",
    "empresa": "Empresa XYZ",
    "responsavel": "João Silva",
    "data_inicio": "2024-01-15",
    "data_prevista": "2024-03-15",
    "status": "Aprovado",
    "observacoes": "Processo aprovado"
}
```

#### DELETE - Excluir Processo

```
DELETE /api.php?id=1
```

---

## 📝 Queries Úteis

### Relatórios

```sql
-- Total de processos por status
SELECT status, COUNT(*) AS total 
FROM processos_aereos 
GROUP BY status;

-- Processos atrasados
SELECT * FROM processos_aereos 
WHERE data_prevista < CURDATE() 
AND status NOT IN ('Concluído', 'Rejeitado');

-- Top 5 empresas
SELECT empresa, COUNT(*) AS total 
FROM processos_aereos 
GROUP BY empresa 
ORDER BY total DESC 
LIMIT 5;
```

Veja mais exemplos no arquivo `crud_queries.sql`.

---

## 🔗 Integração com Frontend

Para integrar o frontend JavaScript com a API PHP, você precisará atualizar o arquivo `script.js` para fazer requisições AJAX/Fetch em vez de usar localStorage.

### Exemplo de Integração:

```javascript
// Substituir localStorage por chamadas à API
async function criarProcesso(dados) {
    const response = await fetch('api.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(dados)
    });
    return await response.json();
}
```

---

## ⚠️ Segurança

**IMPORTANTE**: Este é um exemplo básico. Para produção, considere:

1. ✅ Validação de entrada mais rigorosa
2. ✅ Autenticação e autorização
3. ✅ Proteção contra SQL Injection (já implementado com prepared statements)
4. ✅ Rate limiting
5. ✅ HTTPS obrigatório
6. ✅ Sanitização de dados
7. ✅ Logs de auditoria

---

## 📚 Recursos Adicionais

- [Documentação MySQL](https://dev.mysql.com/doc/)
- [PDO PHP](https://www.php.net/manual/pt_BR/book.pdo.php)
- [REST API Best Practices](https://restfulapi.net/)

---

**Desenvolvido para o Sistema H&E - Gestão de Processos Aéreos**

