## Overview of the Recruitment Exercise

This is a dummy project, which is used to demonstrate knowledge of PHP with Symfony and Angular as well as development in general. It serves as an example with some bad practices included.

### Technologies:

- Backend: Symfony
- Frontend: Angular
- API: REST with an openapi.yaml file

**Duration: 5-8 hours**

## Exercise Structure

### Repository Structure:

`/backend` - ⭐ **Primary backend** - Manual Symfony implementation (production-ready)

`/backend-apiplatform` - 🆕 **Alternative backend** - API Platform implementation (comparison)

`/frontend` - Angular 20 application with modern features

`/docs` - OpenAPI specification and documentation

#### Backend (/backend):

**Primary implementation** built from scratch to demonstrate:
- Clean Architecture & SOLID principles
- Custom controllers, services, and DTOs
- Manual OpenAPI documentation
- Chatbot plugin system
- JWT authentication

**See**: `BACKEND_CHANGES.md` for details

#### Backend API Platform (/backend-apiplatform):

**Alternative implementation** using API Platform to demonstrate:
- Modern framework usage
- Rapid development capabilities
- Auto-generated documentation
- Built-in pagination, filtering, sorting
- Same functionality, 47% less code

**See**: `API_PLATFORM_COMPARISON.md` for comparison

#### Frontend (/frontend):

Modern Angular 20 application featuring:
- Standalone components (no NgModules)
- Signal-based reactivity
- Zoneless change detection
- Modern control flow syntax
- Comprehensive testing

**See**: `FRONTEND_CHANGES.md` for details

##### API Definition (/docs):

openapi.yaml

## Tasks:

### Backend:
- [X] Implement the backend architecture from scratch, which will support the Angular application's API calls.
- [X] Implement error handling.
- [X] Implement the plugin system for extensibility (Chatbot).
- [X] Add authentication for message sending.
### Frontend:
- [X] Optimize data bindings and state management.
- [X] Improve the user interface responsiveness.
- [X] Implement a feature to display message status (sent, received).
- [X] Add seamless communication with the backend application.
- [X] Create a login form to allow users to log in and send messages.
### API:
- [X] Review and if necessary correct RESTful API practices.
- [X] Ensure best practices in the API definition.

## General instructions

- Make sure to follow best practices.
- Pay attention to the code quality as well as software architecture. We value **maintainability** and readability.
- We recommend documenting your changes and the reasoning behind them.
- Git history is important. Make sure to commit your changes as you progress.
- Feel free to ask questions if you have any doubts.
- We are looking for a clean, well-structured solution that demonstrates your understanding of the technologies used.
- The task is only about seeing your skills, nothing more. It is therefore not to be expected that you will work full-time on these 7 days.

## Deliverables

- [x] send in files with your comments by (one of)
    - Inline-Code-Comments and send us the files
    - drop the files anywhere and send us the link
    - upload the code to your own Repository (Avoid forking the repository and creating a PR, as this would make your solution visible to others)
- [x] A brief report summarizing the changes you made, why, and **any additional recommendations if they had more time**.
    - See `BACKEND_CHANGES.md` and `FRONTEND_CHANGES.md` and `RECOMNENDATIONS.md`
- [x] Approximate indication of how many hours you worked for this
    - **~7-8 hours** (documented in reports)

## Run Instructions

### 🐳 Option 1: Docker (Recommended - Easiest)

```bash
# Start everything with one command
docker-compose up -d

# Wait ~30 seconds, then access:
# - Application: http://localhost:4200
# - API Documentation: http://localhost:8000/api/doc/ui
```

**See `DOCKER_SETUP.md` for complete Docker guide.**

---

### 💻 Option 2: Manual Setup

**Backend:**
```bash
cd backend
composer install
php bin/console lexik:jwt:generate-keypair
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate --no-interaction
symfony server:start  # or: php -S localhost:8000 -t public/
```

**Frontend:**
```bash
cd frontend
npm ci
npm start:develoment
```

**See `INSTRUCTIONS.md` for detailed setup instructions.**

---

### Access Points
- **Application**: http://localhost:4200
- **API Documentation**: http://localhost:8000/api/doc/ui
- **API Spec (JSON)**: http://localhost:8000/api/doc.json
- **API Spec (YAML)**: http://localhost:8000/api/doc.yaml

### Testing
```bash
# Backend tests
cd backend && composer test

# Frontend tests
cd frontend && npm run test
```
