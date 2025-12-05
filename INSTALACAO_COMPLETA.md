# 📦 Guia de Instalação Completa - Sistema H&E

## ⚠️ Situação Atual

✅ **MySQL Workbench** - Instalado (interface gráfica)  
❌ **Servidor MySQL** - Não encontrado no PATH  
❌ **PHP** - Não encontrado no PATH

**Importante:** MySQL Workbench é apenas uma ferramenta visual. Você precisa instalar o **servidor MySQL** separadamente.

---

## 🚀 Instalação Rápida (Recomendado)

### Opção 1: Via Homebrew (Mais Fácil)

```bash
# 1. Instalar Homebrew (se não tiver)
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# 2. Instalar PHP e MySQL
brew install php mysql

# 3. Iniciar MySQL
brew services start mysql

# 4. Configurar senha do MySQL (primeira vez)
mysql_secure_installation
```

### Opção 2: Instalação Manual

#### PHP:
- Baixar de: https://www.php.net/downloads.php
- Ou usar: `brew install php`

#### MySQL Server:
- Baixar de: https://dev.mysql.com/downloads/mysql/
- Escolher: **MySQL Community Server** (não apenas Workbench)
- Instalar o pacote `.dmg`

---

## ✅ Após Instalação

### 1. Verificar Instalação

```bash
./verificar_instalacao.sh
```

### 2. Configurar Banco de Dados

```bash
./configurar_banco.sh
```

### 3. Configurar Credenciais

Edite `api.php` (linhas 11-14):
```php
define('DB_USER', 'root');
define('DB_PASS', 'sua_senha_aqui');
```

### 4. Iniciar Servidor

```bash
./iniciar.sh
```

### 5. Acessar Sistema

Abra no navegador: `http://localhost:8000/index.html`

---

## 🔧 Adicionar ao PATH (Se Necessário)

Se PHP/MySQL estiverem instalados mas não no PATH:

```bash
# Adicionar ao ~/.zshrc ou ~/.bash_profile

# Para Homebrew no Apple Silicon:
export PATH="/opt/homebrew/bin:$PATH"

# Para Homebrew no Intel:
export PATH="/usr/local/bin:$PATH"

# Para MySQL instalado manualmente:
export PATH="/usr/local/mysql/bin:$PATH"

# Recarregar:
source ~/.zshrc
```

---

## 🧪 Testar Instalação

### Testar PHP:
```bash
php -v
```

### Testar MySQL:
```bash
mysql --version
mysql -u root -p
```

### Testar Servidor PHP:
```bash
php -S localhost:8000
# Acesse: http://localhost:8000/index.html
```

---

## 💡 Alternativa Temporária

Enquanto instala MySQL e PHP, você pode:

1. **Abrir `index.html` diretamente no navegador**
   - Funcionará com localStorage (sem MySQL)
   - Dados salvos apenas no navegador
   - Perfeito para testar a interface

2. **Depois migrar para MySQL:**
   - Execute `./configurar_banco.sh`
   - Configure `api.php`
   - Execute `./iniciar.sh`

---

## 📞 Precisa de Ajuda?

Execute o diagnóstico:
```bash
./verificar_instalacao.sh
```

Isso mostrará exatamente o que está faltando!

