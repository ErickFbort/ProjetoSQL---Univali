# 📤 Instruções para Fazer Push no GitHub

## ✅ Remote Configurado

O repositório remoto já está configurado:
```
origin  https://github.com/ErickFbort/ProjetoSQL---Univali.git
```

## 🚀 Fazer Push (Escolha uma opção)

### Opção 1: Push Manual (Mais Simples)

Execute no terminal:

```bash
git push -u origin main
```

**Quando pedir credenciais:**
- **Username**: Seu usuário do GitHub (ex: ErickFbort)
- **Password**: Use um **Personal Access Token** (não sua senha!)

### Opção 2: Criar Personal Access Token

1. Acesse: https://github.com/settings/tokens
2. Clique em **"Generate new token"** → **"Generate new token (classic)"**
3. Dê um nome (ex: "ProjetoSQL")
4. Selecione escopo: ✅ **repo** (todos os sub-itens)
5. Clique em **"Generate token"**
6. **COPIE O TOKEN** (você só verá uma vez!)

### Opção 3: Usar SSH (Alternativa)

Se preferir SSH em vez de HTTPS:

```bash
# Remover remote HTTPS
git remote remove origin

# Adicionar remote SSH
git remote add origin git@github.com:ErickFbort/ProjetoSQL---Univali.git

# Fazer push
git push -u origin main
```

**Nota:** Para SSH funcionar, você precisa ter chaves SSH configuradas no GitHub.

### Opção 4: GitHub CLI (Se tiver instalado)

```bash
gh auth login
git push -u origin main
```

---

## 🔍 Verificar Status

```bash
# Ver remotes configurados
git remote -v

# Ver commits locais
git log --oneline

# Ver status
git status
```

---

## ✅ Após o Push Bem-Sucedido

Você verá algo como:
```
Enumerating objects: 18, done.
Counting objects: 100% (18/18), done.
Writing objects: 100% (18/18), done.
To https://github.com/ErickFbort/ProjetoSQL---Univali.git
 * [new branch]      main -> main
Branch 'main' set up to track 'remote branch 'main'.
```

Depois, acesse:
**https://github.com/ErickFbort/ProjetoSQL---Univali**

---

## 🐛 Problemas Comuns

### "Authentication failed"
- Use Personal Access Token, não sua senha
- Verifique se o token tem escopo `repo`

### "Repository not found"
- Verifique se o nome do repositório está correto
- Verifique se você tem permissão no repositório

### "Permission denied"
- Verifique suas credenciais
- Tente criar um novo token

---

**Execute: `git push -u origin main` e siga as instruções acima! 🚀**

