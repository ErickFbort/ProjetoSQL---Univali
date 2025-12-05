# 🚀 Publicar no GitHub - ProjetoSQL - Univali

## ✅ Repositório Local Preparado

O repositório local já está configurado e pronto para ser publicado!

## 📋 Passos para Publicar no GitHub

### 1️⃣ Criar Repositório no GitHub

**Opção A - Via Interface Web:**
1. Acesse: https://github.com/new
2. Nome do repositório: `ProjetoSQL - Univali`
3. Descrição: `Sistema H&E - Gestão de Processos Aéreos com MySQL`
4. **NÃO** marque "Initialize with README" (já temos arquivos)
5. Clique em "Create repository"

**Opção B - Via GitHub CLI (se tiver instalado):**
```bash
gh repo create "ProjetoSQL - Univali" --public --description "Sistema H&E - Gestão de Processos Aéreos com MySQL"
```

### 2️⃣ Conectar e Fazer Push

Após criar o repositório no GitHub, execute:

```bash
# Adicionar remote (substitua SEU_USUARIO pelo seu usuário do GitHub)
git remote add origin https://github.com/SEU_USUARIO/ProjetoSQL---Univali.git

# Ou se preferir SSH:
# git remote add origin git@github.com:SEU_USUARIO/ProjetoSQL---Univali.git

# Verificar remote
git remote -v

# Fazer push
git push -u origin main
```

### 3️⃣ Se o GitHub pedir autenticação

**Token de Acesso Pessoal:**
1. GitHub → Settings → Developer settings → Personal access tokens → Tokens (classic)
2. Generate new token
3. Selecione escopo: `repo`
4. Copie o token
5. Use como senha ao fazer push

**Ou use GitHub CLI:**
```bash
gh auth login
```

---

## 🔄 Comandos Rápidos (Copiar e Colar)

```bash
# 1. Adicionar remote (AJUSTE SEU_USUARIO)
git remote add origin https://github.com/SEU_USUARIO/ProjetoSQL---Univali.git

# 2. Verificar
git remote -v

# 3. Fazer push
git push -u origin main
```

---

## 📝 Estrutura do Repositório

```
ProjetoSQL/
├── .gitignore
├── README.md
├── index.html          # Frontend principal
├── styles.css          # Estilos
├── script.js           # Lógica JavaScript
├── api.php             # API REST PHP
├── config.php          # Configuração
├── database.sql        # Estrutura do banco
├── crud_queries.sql    # Queries de exemplo
├── iniciar.sh          # Script de inicialização
├── configurar_banco.sh  # Script de configuração
└── verificar_instalacao.sh
```

---

## ✅ Verificação

Após o push, acesse:
```
https://github.com/SEU_USUARIO/ProjetoSQL---Univali
```

---

## 🔧 Comandos Úteis

```bash
# Ver status
git status

# Ver histórico
git log --oneline

# Adicionar mudanças futuras
git add .
git commit -m "Descrição das mudanças"
git push

# Ver remotes
git remote -v

# Alterar URL do remote (se necessário)
git remote set-url origin NOVA_URL
```

---

**Pronto! Seu projeto está no GitHub! 🎉**

