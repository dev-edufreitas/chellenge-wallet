#!/bin/bash
# Script para aguardar a inicialização do MySQL

set -e

host="$1"
shift
cmd="$@"

until php -r "try {
    \$pdo = new PDO('mysql:host=$host;dbname=wallet', 'walletuser', 'walletpassword');
    echo 'MySQL conectado com sucesso!';
    exit(0);
} catch (PDOException \$e) {
    echo 'MySQL indisponível - ' . \$e->getMessage() . PHP_EOL;
    exit(1);
}"; do
  echo "MySQL ainda não está disponível, aguardando..."
  sleep 2
done

echo "MySQL está pronto!"
exec $cmd
