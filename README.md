# 🚀 Team Project Setup Guide

Welcome to our project! Follow these steps carefully to set up the development environment locally using Docker.

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:

- **Git**
- **Docker** & **Docker Compose**
- **Node.js** (including `npm`)

---

## 🛠️ Installation Steps

Follow these commands in the exact order:

### 1. Clone and Navigate

Clone the repository and enter the project directory:

```bash
git clone <repository-url>
cd TeamProject
```

### 2. Environment Setup

Install the necessary backend packages and dependencies:

```bash
composer install
npm install
```

Setup .env in ./laravel:

- `APP_URL=http://localhost:8000`
- `REDIS_CLIENT=phpredis`
- `REDIS_HOST=127.0.0.1`
- `REDIS_PASSWORD=null`
- `REDIS_PORT=6379`
- `DB_CONNECTION=mysql`
- `DB_HOST=127.0.0.1`
- `DB_PORT=3306`
- `DB_DATABASE=laravel`
- `DB_USERNAME=`
- `DB_PASSWORD=`

Setup .env in root (must match with the ./laravel/.env):

- `DB_DATABASE=laravel`
- `DB_USERNAME=`
- `DB_PASSWORD=`

- `PMA_HOST=mysql`
- `PMA_USER=`
- `PMA_PASSWORD=`

### 3. Start Docker Containers

Launch the infrastructure.

> **Note:** This may take a few minutes. Please wait for it to finish and do not interrupt the process.

```bash
docker-compose --env-file ./.env up --build
```

### 3.1 Stop Docker Containers

To shut down all the containers run

```bash
docker compose down
```

### 4. Application Key & Migrations

Generate the application security key and run the initial database migrations:

```bash
php artisan key:generate
php artisan migrate:fresh --seed
```

### 5. Frontend & Permissions

Install frontend dependencies and set the correct folder permissions for the web server:

```bash
cd laravel
npm install
cd ..
docker compose exec laravel chown -R www-data:www-data storage bootstrap/cache
docker compose exec laravel chmod -R 775 storage bootstrap/cache
```

---

## 🏃 Running the Application

To start the development server for the frontend, run:

```bash
php artisan serve
npm run dev
```

The application will be accessible at:
👉 **[http://localhost:8000/](http://localhost:8000/)**

---

### ⚠️ Troubleshooting

If you encounter permission issues, re-run the `chown` and `chmod` commands from step 6. Make sure Docker is active before running any `docker compose` commands.

---


## 📡 MCP

### Architecture

The MCP setup consists of **1 server** and **1 client**.

### Environment Variables

Add the following variables to your `.env` file to enable MCP client-server communication (check example.env for reference):

```env
MCP_SERVER=
MCP_OLLAMA_URL=
MCP_OLLAMA_MODEL=
```
### Running the MCP Server

The MCP server starts automatically after `php artisan serve`. To test it in isolation, use the inspector command:

```bash
php artisan mcp:inspector mcp
```

### Running the MCP Client
<small>Make sure you run the LLM</small>

The chat interface is currently implemented as a CLI command:

```bash
php artisan mcp:client
```


---
<small>

⚠️ **Llama API Limitation:** The Llama chat/API does not support resources or prompts — **only tools** will be used during MCP communication.

⚠️ **Client implemented from scratch:** There is no good existing library for a PHP-based MCP client.

ℹ️ **Orchestration** - Since we only use one server and one client, we are not implementing a full orchestration layer. Only tools will be orchestrated using token based orchestration.

</small>