# 🚀 Guia Rápido - Sistema H&E

## ⚡ Início Rápido (Após Instalar MySQL e PHP)

### 1️⃣ Configurar o Banco de Dados

**Opção A - Script Automático:**
```bash
./configurar_banco.sh
```

**Opção B - Manual:**
```bash
mysql -u root -p < database.sql
```

### 2️⃣ Configurar Credenciais na API

Edite o arquivo `api.php` (linhas 11-14):

```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');        // Seu usuário MySQL
define('DB_PASS', 'sua_senha');   // Sua senha MySQL
define('DB_NAME', 'processos_aereos');
```

### 3️⃣ Iniciar o Servidor

**Opção A - Script Automático:**
```bash
./iniciar.sh
```

**Opção B - Manual:**
```bash
php -S localhost:8000
```

### 4️⃣ Acessar o Sistema

Abra no navegador:
```
http://localhost:8000/index.html
```

---

## 📋 Checklist Pós-Instalação

- [ ] MySQL instalado e rodando
- [ ] PHP instalado
- [ ] Banco de dados `processos_aereos` criado
- [ ] Credenciais configuradas em `api.php`
- [ ] Servidor PHP iniciado
- [ ] Sistema acessível no navegador

---

## 🔧 Comandos Úteis

### Verificar se MySQL está rodando:
```bash
# macOS
brew services list | grep mysql

# Ou verificar processo
ps aux | grep mysql
```

### Iniciar MySQL (se necessário):
```bash
# macOS com Homebrew
brew services start mysql

# Ou
mysql.server start
```

### Testar conexão MySQL:
```bash
mysql -u root -p
```

### Verificar versão PHP:
```bash
php -v
```

### Verificar se porta está em uso:
```bash
lsof -i :8000
```

---

## 🐛 Solução de Problemas

### Erro: "MySQL não encontrado"
- Verifique se MySQL está instalado: `which mysql`
- Adicione ao PATH se necessário
- No macOS: `brew install mysql`

### Erro: "PHP não encontrado"
- Verifique se PHP está instalado: `which php`
- No macOS: `brew install php`

### Erro: "Can't connect to MySQL server"
- Verifique se MySQL está rodando
- Verifique as credenciais em `api.php`
- Teste a conexão: `mysql -u root -p`

### Erro: "Access denied for user"
- Verifique usuário e senha em `api.php`
- Teste: `mysql -u root -p` com as mesmas credenciais

### Porta 8000 já em uso
- Use outra porta: `php -S localhost:8080`
- Ou mate o processo: `lsof -ti:8000 | xargs kill`

---

## 📚 Instalação no macOS

### Via Homebrew:

```bash
# Instalar Homebrew (se não tiver)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Instalar MySQL
brew install mysql
brew services start mysql

# Instalar PHP
brew install php
```

### Configurar MySQL após instalação:

```bash
# Configurar senha do root
mysql_secure_installation

# Ou definir senha manualmente
mysql -u root
ALTER USER 'root'@'localhost' IDENTIFIED BY 'sua_senha';
```

---

## ✅ Teste Completo

1. **Testar MySQL:**
   ```bash
   mysql -u root -p -e "SHOW DATABASES;"
   ```

2. **Testar PHP:**
   ```bash
   php -r "echo 'PHP funcionando!';"
   ```

3. **Testar API:**
   ```bash
   curl http://localhost:8000/api.php
   ```
   Deve retornar: `[]` (array vazio) ou lista de processos

4. **Abrir no navegador:**
   ```
   http://localhost:8000/index.html
   ```

---

**Pronto! Seu sistema está configurado! 🎉**

