# 🚀 Team Project Setup Guide

Welcome to our project! Follow these steps carefully to set up the development environment locally using Docker.

---

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
* **Git**
* **Docker** & **Docker Compose**
* **Node.js** (including `npm`)

---

## 🛠️ Installation Steps

Follow these commands in the exact order:

### 1. Clone and Navigate
Clone the repository and enter the project directory:
```bash
git clone <repository-url>
cd TeamProject
```

### 2. Environment Configuration
Create your environment file by copying the template:
```bash
cp .env.example .env
```

**Open the `.env` file and ensure the following values are set:**
* `APP_URL=http://localhost:8080`
* `DB_CONNECTION=mysql`
* `DB_HOST=mysql`
* `DB_PORT=3306`
* `DB_DATABASE=`
* `DB_USERNAME=`
* `DB_PASSWORD=`
* `DB_ROOT_PASSWORD=`
* `PMA_HOST=`
* `PMA_USER=`
* `PMA_PASSWORD=`

### 3. Start Docker Containers
Launch the infrastructure. 
> **Note:** This may take a few minutes. Please wait for it to finish and do not interrupt the process.
```bash
docker compose up -d
```
or if you are not in the `laravel` directory:
```bash
docker compose --env-file ./laravel/.env up
```
### 3.1 Stop Docker Containers
To shut down all the containers run
```bash
docker compose down
```
or if you are not in the `laravel` directory:
```bash
docker compose --env-file ./laravel/.env up
```


### 4. Backend Setup
Install the necessary backend packages and dependencies:
```bash
docker compose exec laravel install
docker compose exec laravel composer install
```

### 5. Application Key & Migrations
Generate the application security key and run the initial database migrations:
```bash
docker compose exec laravel php artisan key:generate
docker compose exec laravel php artisan migrate
```

### 6. Frontend & Permissions
Install frontend dependencies and set the correct folder permissions for the web server:
```bash
cd laravel
npm install
cd ..
docker compose exec laravel chown -R www-data:www-data storage bootstrap/cache
docker compose exec laravel chmod -R 775 storage bootstrap/cache
```

### 7. Database Seeding
Populate the database with initial tables and seed data:
```bash
docker compose exec laravel php artisan migrate:fresh --seed
```

---

## 🏃 Running the Application

To start the development server for the frontend, run:
```bash
npm run dev
```

The application will be accessible at: 
👉 **[http://localhost:8080/](http://localhost:8080/)**

---

### ⚠️ Troubleshooting
If you encounter permission issues, re-run the `chown` and `chmod` commands from step 6. Make sure Docker is active before running any `docker compose` commands.