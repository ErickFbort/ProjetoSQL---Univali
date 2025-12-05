#!/bin/bash

echo "🚀 Fazendo Push para GitHub - ProjetoSQL - Univali"
echo "=================================================="
echo ""

# Verificar se está no diretório correto
if [ ! -d ".git" ]; then
    echo "❌ Erro: Não é um repositório Git"
    exit 1
fi

# Verificar remote
if ! git remote -v | grep -q "origin"; then
    echo "❌ Remote 'origin' não configurado"
    exit 1
fi

echo "📋 Remote configurado:"
git remote -v
echo ""

# Verificar se há commits para push
LOCAL=$(git rev-parse @)
REMOTE=$(git rev-parse @{u} 2>/dev/null || echo "")

if [ -z "$REMOTE" ]; then
    echo "📤 Primeiro push para o repositório remoto"
    echo ""
    echo "⚠️  Você precisará inserir suas credenciais:"
    echo "   Username: Seu usuário do GitHub"
    echo "   Password: Personal Access Token (não sua senha!)"
    echo ""
    read -p "Pressione Enter para continuar..."
    git push -u origin main
else
    if [ "$LOCAL" = "$REMOTE" ]; then
        echo "✅ Repositório já está sincronizado!"
        exit 0
    else
        echo "📤 Fazendo push de commits locais..."
        echo ""
        echo "⚠️  Você precisará inserir suas credenciais:"
        echo "   Username: Seu usuário do GitHub"
        echo "   Password: Personal Access Token (não sua senha!)"
        echo ""
        read -p "Pressione Enter para continuar..."
        git push origin main
    fi
fi

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Push realizado com sucesso!"
    echo ""
    echo "🌐 Acesse: https://github.com/ErickFbort/ProjetoSQL---Univali"
else
    echo ""
    echo "❌ Erro ao fazer push"
    echo ""
    echo "💡 Soluções:"
    echo "   1. Verifique suas credenciais"
    echo "   2. Crie um Personal Access Token:"
    echo "      https://github.com/settings/tokens"
    echo "   3. Use o token como senha (não sua senha do GitHub)"
    echo ""
    echo "   Ou execute manualmente:"
    echo "   git push origin main"
fi

