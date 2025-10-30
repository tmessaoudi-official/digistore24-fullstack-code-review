# Docker Setup Guide

## Quick Start (Easiest Way to Run)

### Prerequisites
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) installed
- That's it! No PHP, Node.js, or PostgreSQL needed on your machine

### Start the Application (1 command)

```bash
docker-compose up -d
```

Wait ~30 seconds for all services to start, then access:
- **Application**: http://localhost:4200
- **API Documentation**: http://localhost:8000/api/doc/ui

### What Gets Set Up Automatically

✅ **PostgreSQL Database**
- Runs on port 5432
- Database: `chatmessenger`
- User: `chatmessenger`
- Password: `chatmessenger`

✅ **Symfony Backend**
- Runs on port 8000
- Composer dependencies installed
- JWT keys generated
- Database created and migrated
- Ready to serve API requests

✅ **Angular Frontend**
- Runs on port 4200
- npm dependencies installed
- Development server running
- Connected to backend API

---

## Docker Commands

### Start Services
```bash
# Start in background
docker-compose up -d

# Start with logs visible
docker-compose up
```

### Stop Services
```bash
# Stop containers (keep data)
docker-compose down

# Stop and remove all data
docker-compose down -v
```

### View Logs
```bash
# All services
docker-compose logs -f

# Specific service
docker-compose logs -f backend
docker-compose logs -f frontend
docker-compose logs -f db
```

### Restart Services
```bash
# Restart all
docker-compose restart

# Restart specific service
docker-compose restart backend
```

### Rebuild After Code Changes
```bash
docker-compose up -d --build
```

### Run Commands Inside Containers

**Backend commands:**
```bash
# Clear cache
docker-compose exec backend php bin/console cache:clear

# Run migrations
docker-compose exec backend php bin/console doctrine:migrations:migrate

# Run tests
docker-compose exec backend php bin/phpunit

# Access Symfony console
docker-compose exec backend php bin/console
```

**Frontend commands:**
```bash
# Run tests
docker-compose exec frontend npm test

# Run linter
docker-compose exec frontend npm run lint

# Install new package
docker-compose exec frontend npm install package-name
```

**Database access:**
```bash
# Connect to PostgreSQL
docker-compose exec db psql -U chatmessenger -d chatmessenger

# Backup database
docker-compose exec db pg_dump -U chatmessenger chatmessenger > backup.sql

# Restore database
docker-compose exec -T db psql -U chatmessenger -d chatmessenger < backup.sql
```

---

## Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    Docker Compose                        │
├─────────────────────────────────────────────────────────┤
│                                                          │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐ │
│  │   Frontend   │  │   Backend    │  │  PostgreSQL  │ │
│  │              │  │              │  │              │ │
│  │  Angular 20  │──│  Symfony 7   │──│  Database    │ │
│  │  Node 20     │  │  PHP 8.2     │  │  Port 5432   │ │
│  │  Port 4200   │  │  Port 8000   │  │              │ │
│  └──────────────┘  └──────────────┘  └──────────────┘ │
│                                                          │
└─────────────────────────────────────────────────────────┘
```

---

## Volumes

Docker Compose creates volumes to persist data:

- `db_data` - PostgreSQL database files
- `backend_vendor` - PHP Composer dependencies
- `backend_var` - Symfony cache and logs
- `frontend_node_modules` - npm packages

**To remove all data:**
```bash
docker-compose down -v
```

---

## Ports

| Service  | Port | URL                              |
|----------|------|----------------------------------|
| Frontend | 4200 | http://localhost:4200            |
| Backend  | 8000 | http://localhost:8000            |
| Database | 5432 | postgresql://localhost:5432      |

**If ports are already in use**, edit `docker-compose.yml`:
```yaml
ports:
  - "4201:4200"  # Change 4201 to any available port
```

---

## Troubleshooting

### Services Won't Start

**Check logs:**
```bash
docker-compose logs
```

**Rebuild containers:**
```bash
docker-compose down
docker-compose up -d --build
```

### Database Connection Failed

**Wait for database to be ready** (takes ~10 seconds on first start):
```bash
docker-compose logs db
```

**Restart backend after database is ready:**
```bash
docker-compose restart backend
```

### Port Already in Use

**Find what's using the port:**
```bash
# macOS/Linux
lsof -i :8000
lsof -i :4200
lsof -i :5432

# Windows
netstat -ano | findstr :8000
```

**Change port in docker-compose.yml** or stop the conflicting service.

### Frontend Can't Connect to Backend

**Check backend is running:**
```bash
docker-compose logs backend
curl http://localhost:8000/api/doc.json
```

**Check CORS configuration** in backend container:
```bash
docker-compose exec backend cat .env | grep CORS
```

### Changes Not Reflected

**Rebuild containers:**
```bash
docker-compose up -d --build
```

**Clear backend cache:**
```bash
docker-compose exec backend php bin/console cache:clear
```

---

## Development Workflow

### Making Code Changes

1. **Edit files** on your host machine (changes sync automatically)
2. **Backend changes**: Cache clears automatically in dev mode
3. **Frontend changes**: Hot reload happens automatically
4. **Dependency changes**: Rebuild containers

### Adding Dependencies

**Backend (Composer):**
```bash
docker-compose exec backend composer require package/name
```

**Frontend (npm):**
```bash
docker-compose exec frontend npm install package-name
```

### Running Tests

**Backend:**
```bash
docker-compose exec backend php bin/phpunit
```

**Frontend:**
```bash
docker-compose exec frontend npm test
```

---

## Production Deployment

For production, create a separate `docker-compose.prod.yml`:

```yaml
version: '3.8'

services:
  backend:
    environment:
      APP_ENV: prod
      APP_DEBUG: 0
    command: >
      sh -c "
        composer install --no-dev --optimize-autoloader &&
        php bin/console cache:clear --env=prod &&
        php bin/console cache:warmup --env=prod &&
        php -S 0.0.0.0:8000 -t public/
      "
  
  frontend:
    command: >
      sh -c "
        npm ci &&
        npm run build --configuration=production &&
        npx http-server dist/frontend -p 4200
      "
```

Deploy with:
```bash
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

---

## Clean Up

### Remove Everything
```bash
# Stop containers and remove volumes
docker-compose down -v

# Remove images
docker-compose down --rmi all

# Remove everything (containers, volumes, images, networks)
docker-compose down -v --rmi all --remove-orphans
```

### Start Fresh
```bash
docker-compose down -v
docker-compose up -d --build
```

---

## Benefits of Docker Setup

✅ **No Local Dependencies** - No need to install PHP, Node.js, PostgreSQL  
✅ **Consistent Environment** - Same setup for all developers  
✅ **Easy Onboarding** - New developers start in minutes  
✅ **Isolated** - Doesn't interfere with other projects  
✅ **Production-Like** - Development environment matches production  
✅ **Easy Cleanup** - Remove everything with one command  

---

## Next Steps

1. **Start the application**: `docker-compose up -d`
2. **Access the app**: http://localhost:4200
3. **Register a user** and start chatting
4. **Explore the API**: http://localhost:8000/api/doc/ui

For detailed instructions, see `INSTRUCTIONS.md`.
