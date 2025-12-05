#!/bin/bash

# Script para iniciar o servidor PHP e configurar o banco de dados
# Sistema H&E - Gestão de Processos Aéreos

echo "🚀 Iniciando Sistema H&E - Gestão de Processos Aéreos"
echo "=================================================="
echo ""

# Verificar se PHP está instalado
if ! command -v php &> /dev/null; then
    echo "❌ PHP não encontrado. Por favor, instale o PHP primeiro."
    exit 1
fi

# Verificar se MySQL está instalado
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL não encontrado. Por favor, instale o MySQL primeiro."
    exit 1
fi

echo "✅ PHP encontrado: $(php -v | head -1)"
echo "✅ MySQL encontrado: $(mysql --version | head -1)"
echo ""

# Perguntar se deseja criar/configurar o banco de dados
read -p "Deseja criar/configurar o banco de dados agora? (s/n): " criar_db

if [ "$criar_db" = "s" ] || [ "$criar_db" = "S" ]; then
    echo ""
    echo "📊 Configurando banco de dados..."
    read -p "Usuário MySQL (padrão: root): " mysql_user
    mysql_user=${mysql_user:-root}
    
    read -sp "Senha MySQL: " mysql_pass
    echo ""
    
    # Criar banco de dados
    mysql -u "$mysql_user" -p"$mysql_pass" < database.sql 2>/dev/null
    
    if [ $? -eq 0 ]; then
        echo "✅ Banco de dados criado com sucesso!"
    else
        echo "⚠️  Erro ao criar banco de dados. Verifique as credenciais."
        echo "   Você pode criar manualmente executando: mysql -u root -p < database.sql"
    fi
    echo ""
fi

# Verificar se a porta 8000 está disponível
if lsof -Pi :8000 -sTCP:LISTEN -t >/dev/null 2>&1 ; then
    echo "⚠️  Porta 8000 já está em uso."
    read -p "Deseja usar outra porta? (s/n): " outra_porta
    if [ "$outra_porta" = "s" ] || [ "$outra_porta" = "S" ]; then
        read -p "Digite o número da porta (ex: 8080): " porta
        porta=${porta:-8080}
    else
        porta=8000
    fi
else
    porta=8000
fi

echo ""
echo "🌐 Iniciando servidor PHP na porta $porta..."
echo "📍 Acesse: http://localhost:$porta/index.html"
echo ""
echo "⚠️  Para parar o servidor, pressione Ctrl+C"
echo ""

# Iniciar servidor PHP
php -S localhost:$porta

