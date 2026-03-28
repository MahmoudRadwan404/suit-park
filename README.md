## 🚀 Getting Started

### Prerequisites
- PHP >= 8.2
- Composer
- MySQL
- Node.js & NPM

---

### Installation

#### 1. Clone the repository
```bash
git clone https://github.com/your-username/your-repo.git
cd your-repo
```

#### 2. Install PHP dependencies
```bash
composer install
```

#### 3. Environment setup
```bash
cp .env.example .env
php artisan key:generate
php artisan jwt:secret
```
> Then open `.env` and fill in your `DB_*` credentials.

#### 4. Run migrations & seeders
```bash
php artisan migrate
php artisan db:seed
```

#### 5. Link storage
```bash
php artisan storage:link
```

#### 6. Install frontend dependencies & build assets
```bash
npm install
npm run build
```

#### 7. Start the server
```bash
php artisan serve
```

## 🔐 Default Login Credentials

> These are seeded automatically after running `php artisan db:seed`

| Role  | Email                | Password      |
|-------|----------------------|---------------|
| Admin | admin@suitpark.sa    | adminpass123  |

> ⚠️ **Change these credentials immediately after first login in production.**

> App will be running at **http://localhost:8000**