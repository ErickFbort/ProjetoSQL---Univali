# ⚙️ Guia Rápido de Configuração - Sistema H&E

## 📋 Passo a Passo para Integração Frontend + MySQL

### 1️⃣ Criar o Banco de Dados

Execute o arquivo SQL no MySQL:

```bash
mysql -u root -p < database.sql
```

Ou via phpMyAdmin/MySQL Workbench, copie e execute o conteúdo de `database.sql`.

### 2️⃣ Configurar Credenciais da API

Edite o arquivo `api.php` (linhas 11-14) com suas credenciais:

```php
define('DB_HOST', 'localhost');      // Seu servidor MySQL
define('DB_USER', 'root');            // Seu usuário MySQL
define('DB_PASS', '');                // Sua senha MySQL
define('DB_NAME', 'processos_aereos'); // Nome do banco
```

### 3️⃣ Configurar Servidor Web

#### Opção A: XAMPP/WAMP/MAMP
1. Copie a pasta do projeto para `htdocs` (XAMPP) ou `www` (WAMP)
2. Acesse: `http://localhost/ProjetoSQL/`

#### Opção B: Servidor PHP Built-in
```bash
cd /Users/erickfranzmann/ProjetoSQL
php -S localhost:8000
```
Acesse: `http://localhost:8000`

### 4️⃣ Testar a API

Teste se a API está funcionando:

```bash
# No terminal
curl http://localhost:8000/api.php

# Ou no navegador
http://localhost:8000/api.php
```

Deve retornar um array JSON (pode estar vazio `[]` se não houver dados).

### 5️⃣ Testar o Frontend

Abra `index.html` no navegador ou acesse via servidor:
- `http://localhost:8000/index.html`

---

## 🔧 Solução de Problemas

### Erro: "Erro ao carregar processos"

**Causa**: API não está acessível ou banco não configurado.

**Solução**:
1. Verifique se o servidor PHP está rodando
2. Verifique as credenciais do banco em `api.php`
3. Verifique se o banco de dados foi criado
4. Abra o Console do navegador (F12) para ver erros detalhados

### Erro: "Access to fetch blocked by CORS"

**Causa**: Problema de CORS (Cross-Origin Resource Sharing).

**Solução**: A API já tem headers CORS configurados. Se ainda houver problema:
- Certifique-se de acessar via servidor (não `file://`)
- Use o mesmo domínio/porta para HTML e API

### Erro: "Campo obrigatório faltando"

**Causa**: Formulário não está preenchendo todos os campos obrigatórios.

**Solução**: Verifique se todos os campos marcados com `*` estão preenchidos.

### Erro: "Número de processo já existe"

**Causa**: Tentando criar processo com número que já existe.

**Solução**: Use um número de processo único.

---

## ✅ Checklist de Verificação

- [ ] Banco de dados `processos_aereos` criado
- [ ] Tabela `processos_aereos` existe
- [ ] Credenciais do banco configuradas em `api.php`
- [ ] Servidor PHP rodando
- [ ] API acessível (teste com curl ou navegador)
- [ ] Frontend acessando a API corretamente
- [ ] Console do navegador sem erros

---

## 🧪 Testar CRUD Completo

### CREATE (Criar)
1. Preencha o formulário
2. Clique em "Cadastrar Processo"
3. Verifique se aparece na lista

### READ (Ler)
1. A lista deve carregar automaticamente
2. Use a busca para filtrar processos

### UPDATE (Atualizar)
1. Clique em "Editar" em um processo
2. Modifique os dados
3. Clique em "Atualizar Processo"

### DELETE (Excluir)
1. Clique em "Excluir" em um processo
2. Confirme a exclusão
3. Processo deve desaparecer da lista

---

## 📝 Notas Importantes

1. **URL da API**: O JavaScript está configurado para usar `api.php` na mesma pasta. Se estiver em outra pasta, ajuste a constante `API_URL` no `script.js`.

2. **Formato de Dados**: A API usa `snake_case` (numero_processo) enquanto o formulário HTML usa `kebab-case` (numero-processo). O JavaScript faz a conversão automaticamente.

3. **Modo Desenvolvimento**: Para debug, abra o Console do navegador (F12) para ver requisições e erros.

4. **Backup**: Antes de fazer alterações, faça backup do banco de dados:
```bash
mysqldump -u root -p processos_aereos > backup.sql
```

---

**Pronto! Seu sistema está integrado e funcionando! 🚀**

