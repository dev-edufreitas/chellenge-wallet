
<div align="center">
  <img src="https://github.com/user-attachments/assets/163b8436-9d4a-48bb-be57-7571caae7dcd" alt="Wallet App" width="700">
  <h1>Chellenge Wallet - Grupo Adriano Cobuccio</h1>
  <p>Uma aplicação de carteira financeira onde usuários podem realizar transferências e depósitos.</p>
</div>

## 🚀 Tecnologias

- [Laravel 12](https://laravel.com)
- [MySQL 8.3](https://www.mysql.com)
- [Tailwind CSS](https://tailwindcss.com)
- [Alpine.js](https://alpinejs.dev)
- [Docker](https://www.docker.com)

## 🛠️ Instalação com Docker

```bash
# Clone o repositório
git clone https://github.com/dev-edufreitas/chellenge-wallet.git

# Entre na pasta
cd chellenge-wallet

# Configure o banco de dados no .env
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=wallet
DB_USERNAME=walletuser
DB_PASSWORD=walletpassword

# Inicie os containers
docker-compose up -d

# Instale as dependências
docker-compose exec app composer install
docker-compose exec app php artisan key:generate
docker-compose exec app php artisan migrate
```

## 📦 Funcionalidades

- Registro e autenticação de usuários
- Depósitos e transferências entre carteiras
- Histórico de transações
- Interface responsiva

## 📄 Licença

Este projeto foi desenvolvido como parte de um desafio técnico para o Grupo Adriano Cobuccio.

---

