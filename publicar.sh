#!/bin/bash

echo "🚀 Publicando ProjetoSQL - Univali no GitHub"
echo "==========================================="
echo ""

# Verificar se já tem remote
if git remote -v | grep -q "origin"; then
    echo "⚠️  Remote 'origin' já existe:"
    git remote -v
    read -p "Deseja substituir? (s/n): " substituir
    if [ "$substituir" = "s" ] || [ "$substituir" = "S" ]; then
        git remote remove origin
    else
        echo "Operação cancelada."
        exit 0
    fi
fi

echo ""
echo "📋 Informe os dados do seu repositório GitHub:"
echo ""

read -p "Seu usuário do GitHub: " github_user

if [ -z "$github_user" ]; then
    echo "❌ Usuário não informado. Operação cancelada."
    exit 1
fi

echo ""
echo "Escolha o protocolo:"
echo "1) HTTPS (recomendado)"
echo "2) SSH"
read -p "Opção (1 ou 2): " protocolo

if [ "$protocolo" = "2" ]; then
    remote_url="git@github.com:${github_user}/ProjetoSQL---Univali.git"
else
    remote_url="https://github.com/${github_user}/ProjetoSQL---Univali.git"
fi

echo ""
echo "🔗 Adicionando remote: $remote_url"
git remote add origin "$remote_url"

if [ $? -eq 0 ]; then
    echo "✅ Remote adicionado com sucesso!"
    echo ""
    echo "📤 Fazendo push para o GitHub..."
    echo ""
    
    git push -u origin main
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "✅ Projeto publicado com sucesso!"
        echo ""
        echo "🌐 Acesse: https://github.com/${github_user}/ProjetoSQL---Univali"
    else
        echo ""
        echo "❌ Erro ao fazer push."
        echo ""
        echo "💡 Possíveis soluções:"
        echo "   1. Verifique se o repositório existe no GitHub"
        echo "   2. Verifique suas credenciais (token de acesso)"
        echo "   3. Tente novamente: git push -u origin main"
    fi
else
    echo "❌ Erro ao adicionar remote."
fi

