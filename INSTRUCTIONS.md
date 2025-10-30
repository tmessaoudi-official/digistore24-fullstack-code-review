# Run Instructions

## Option 1: Docker (Recommended - Easiest)

### Prerequisites
- Docker Desktop installed
- Docker Compose installed (included with Docker Desktop)

### Quick Start (3 commands)

```bash
# 1. Build and start all services
docker-compose up -d

# 2. Wait ~30 seconds for services to be ready, then access:
# - Frontend: http://localhost:4200
# - Backend API: http://localhost:8000
# - API Documentation: http://localhost:8000/api/doc/ui
```

That's it! The application is now running with:
- ✅ PHP 8.4 with Symfony
- ✅ PostgreSQL database (auto-created and migrated)
- ✅ Node.js with Angular
- ✅ All dependencies installed
- ✅ JWT keys generated

### Docker Commands

```bash
# Start services
docker-compose up -d

# Stop services
docker-compose down

# View logs
docker-compose logs -f

# View specific service logs
docker-compose logs -f backend
docker-compose logs -f frontend

# Restart services
docker-compose restart

# Rebuild after code changes
docker-compose up -d --build

# Run backend commands
docker-compose exec backend php bin/console cache:clear
docker-compose exec backend php bin/phpunit

# Run frontend commands
docker-compose exec frontend npm run test

# Access database
docker-compose exec db psql -U chatmessenger -d chatmessenger
```

### Stopping the Application

```bash
# Stop containers (keep data)
docker-compose down

# Stop and remove all data
docker-compose down -v
```

---

## Option 2: Manual Setup (Local Development)

### Prerequisites

- **PHP**: 8.4 or higher
- **Composer**: Latest version
- **Node.js**: 24+ and npm
- **PostgreSQL**: 16+ (or use SQLite)
- **Symfony CLI**: Optional but recommended

### Backend Setup

```bash
# 1. Navigate to backend directory
cd backend

# 2. Install dependencies
composer install

# 3. Configure environment
cp .env .env.local

# Edit .env.local and set your database URL:
# For PostgreSQL:
DATABASE_URL="postgresql://user:password@127.0.0.1:5432/chatmessenger?serverVersion=14&charset=utf8"

# Or for SQLite (easier for testing):
DATABASE_URL="sqlite:///%kernel.project_dir%/var/data.db"

# 4. Generate JWT keys
php bin/console lexik:jwt:generate-keypair

# 5. Create database
php bin/console doctrine:database:create

# 6. Run migrations
php bin/console doctrine:migrations:migrate --no-interaction

# 7. Start the server
symfony server:start
# Or without Symfony CLI:
php -S localhost:8000 -t public/
```

**Backend will run on**: `http://localhost:8000`

### Frontend Setup

```bash
# 1. Navigate to frontend directory
cd frontend

# 2. Install dependencies
npm install

# 3. Start development server
npm start
```

**Frontend will run on**: `http://localhost:4200`

---

## Access Points

Once running, access the application at:

- **Application**: http://localhost:4200
- **API Documentation (Swagger UI)**: http://localhost:8000/api/doc/ui
- **API Spec (JSON)**: http://localhost:8000/api/doc.json
- **API Spec (YAML)**: http://localhost:8000/api/doc.yaml

---

## First Time Usage

### 1. Register a User

**Via Web Interface:**
1. Go to http://localhost:4200
2. Click "Register" (or navigate to http://localhost:4200/register)
3. Fill in the form:
   - **Name**: John Doe
   - **Email**: john@example.com
   - **Password**: SecurePass123! (must have uppercase, lowercase, digit, special char)
4. Click "Register"

**Via API (cURL):**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePass123!",
    "name": "John Doe"
  }'
```

### 2. Login

**Via Web Interface:**
1. Go to http://localhost:4200/login
2. Enter your credentials
3. Click "Login"
4. You'll be redirected to the chat

**Via API (cURL):**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "SecurePass123!"
  }'
```

Response:
```json
{
  "token": "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9..."
}
```

### 3. Send a Message

**Via Web Interface:**
1. Type your message in the input field at the bottom
2. Click "Send" or press Enter
3. The chatbot will automatically reply if your message contains keywords

**Via API (cURL):**
```bash
# First, get your token from login, then:
curl -X POST http://localhost:8000/api/messages \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "message": "Hello, how are you?"
  }'
```

### 4. Try the Chatbot

The chatbot responds to these keywords:
- **"hello"** or **"hi"** → Greeting response
- **"help"** → Help information
- **"time"** or **"date"** → Current date/time

Try sending: "Hello!" or "What time is it?"

---

## Testing

### Backend Tests

```bash
cd backend

# Run all tests
php bin/phpunit

# Run specific test suite
php bin/phpunit tests/Unit
php bin/phpunit tests/Integration
php bin/phpunit tests/Functional

# Run with coverage (requires Xdebug)
php bin/phpunit --coverage-html coverage/
```

### Frontend Tests

```bash
cd frontend

# Run all tests
npm run test

# Run tests in watch mode
npm run test -- --watch

# Run specific test file
npm run test -- login.spec.ts

# Run with coverage
npm run test -- --coverage
```

---

## Environment Configuration

### Backend (.env.local)

```env
# Application
APP_ENV=dev
APP_SECRET=your-secret-key-here

# Database
DATABASE_URL="postgresql://chatmessenger:password@db:5432/chatmessenger?serverVersion=14&charset=utf8"

# JWT Configuration
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your-jwt-passphrase

# CORS (for frontend)
CORS_ALLOW_ORIGIN='^http://localhost:4200$'
```

### Frontend (environment.ts)

```typescript
export const environment = {
  production: false,
  apiUrl: 'http://localhost:8000/api'
};

If you want local environment where you can test variables without worying about commiting them by error, you can use environment.local.ts and the use npm run start:local
```
