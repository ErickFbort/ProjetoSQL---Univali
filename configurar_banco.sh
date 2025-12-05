#!/bin/bash

# Script para configurar apenas o banco de dados
# Sistema H&E - Gestão de Processos Aéreos

echo "📊 Configuração do Banco de Dados - Sistema H&E"
echo "=============================================="
echo ""

# Verificar se MySQL está instalado
if ! command -v mysql &> /dev/null; then
    echo "❌ MySQL não encontrado. Por favor, instale o MySQL primeiro."
    exit 1
fi

echo "✅ MySQL encontrado: $(mysql --version | head -1)"
echo ""

# Solicitar credenciais
read -p "Usuário MySQL (padrão: root): " mysql_user
mysql_user=${mysql_user:-root}

read -sp "Senha MySQL: " mysql_pass
echo ""

# Testar conexão
echo ""
echo "🔌 Testando conexão com MySQL..."
mysql -u "$mysql_user" -p"$mysql_pass" -e "SELECT 1;" 2>/dev/null

if [ $? -ne 0 ]; then
    echo "❌ Erro ao conectar ao MySQL. Verifique as credenciais."
    exit 1
fi

echo "✅ Conexão estabelecida!"
echo ""

# Criar banco de dados
echo "📦 Criando banco de dados e tabelas..."
mysql -u "$mysql_user" -p"$mysql_pass" < database.sql

if [ $? -eq 0 ]; then
    echo "✅ Banco de dados criado com sucesso!"
    echo ""
    echo "📝 Próximos passos:"
    echo "   1. Edite o arquivo api.php e configure as credenciais:"
    echo "      DB_USER = '$mysql_user'"
    echo "      DB_PASS = 'sua_senha'"
    echo ""
    echo "   2. Execute: ./iniciar.sh para iniciar o servidor"
else
    echo "❌ Erro ao criar banco de dados."
    exit 1
fi

