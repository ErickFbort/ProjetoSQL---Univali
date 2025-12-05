#!/bin/bash

echo "🔍 Verificando Instalação - Sistema H&E"
echo "======================================="
echo ""

# Verificar PHP
echo "📌 Verificando PHP..."
php_paths=(
    "/usr/local/bin/php"
    "/opt/homebrew/bin/php"
    "/usr/bin/php"
    "$(which php 2>/dev/null)"
)

php_found=false
for path in "${php_paths[@]}"; do
    if [ -f "$path" ] && [ -x "$path" ]; then
        echo "✅ PHP encontrado em: $path"
        $path -v | head -1
        php_found=true
        break
    fi
done

if [ "$php_found" = false ]; then
    echo "❌ PHP não encontrado"
    echo "   Instale via: brew install php"
    echo "   Ou baixe de: https://www.php.net/downloads.php"
fi

echo ""

# Verificar MySQL
echo "📌 Verificando MySQL..."
mysql_paths=(
    "/usr/local/bin/mysql"
    "/opt/homebrew/bin/mysql"
    "/usr/local/mysql/bin/mysql"
    "$(which mysql 2>/dev/null)"
)

mysql_found=false
for path in "${mysql_paths[@]}"; do
    if [ -f "$path" ] && [ -x "$path" ]; then
        echo "✅ MySQL encontrado em: $path"
        $path --version | head -1
        mysql_found=true
        break
    fi
done

if [ "$mysql_found" = false ]; then
    echo "❌ MySQL não encontrado"
    echo "   MySQL Workbench está instalado, mas o servidor MySQL precisa ser instalado separadamente"
    echo "   Instale via: brew install mysql"
    echo "   Ou baixe de: https://dev.mysql.com/downloads/mysql/"
fi

echo ""

# Verificar MySQL Workbench
echo "📌 Verificando MySQL Workbench..."
if [ -d "/Applications/MySQLWorkbench.app" ]; then
    echo "✅ MySQL Workbench instalado"
else
    echo "⚠️  MySQL Workbench não encontrado"
fi

echo ""

# Verificar se MySQL está rodando
echo "📌 Verificando se MySQL está rodando..."
if ps aux | grep -i "[m]ysqld" > /dev/null; then
    echo "✅ Servidor MySQL está rodando"
else
    echo "⚠️  Servidor MySQL não está rodando"
    if [ "$mysql_found" = true ]; then
        echo "   Para iniciar: brew services start mysql"
        echo "   Ou: mysql.server start"
    fi
fi

echo ""

# Resumo
echo "📊 RESUMO:"
echo "=========="
if [ "$php_found" = true ] && [ "$mysql_found" = true ]; then
    echo "✅ Tudo pronto! Você pode executar: ./iniciar.sh"
elif [ "$php_found" = true ]; then
    echo "⚠️  PHP OK, mas MySQL precisa ser instalado/configurado"
elif [ "$mysql_found" = true ]; then
    echo "⚠️  MySQL OK, mas PHP precisa ser instalado/configurado"
else
    echo "❌ PHP e MySQL precisam ser instalados"
    echo ""
    echo "💡 INSTALAÇÃO RÁPIDA (macOS com Homebrew):"
    echo "   /bin/bash -c \"\$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)\""
    echo "   brew install php mysql"
    echo "   brew services start mysql"
fi

echo ""

