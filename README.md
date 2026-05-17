# Ski Manager

Ski Manager is a modern ski resort management platform for hosting, managing, and operating ski resort systems online. It is designed to be modular, scalable, and production-ready for real‑world resort operations.

---

## Features

- **Resort management dashboard** – Centralized overview of resort status and operations.
- **Lift and trail management** – Configure, monitor, and manage lifts, trails, and their statuses.
- **Snowmaking systems** – Model or integrate snowmaking systems and conditions.
- **Staff administration** – Manage staff roles, access, and assignments.
- **Guest services** – Track guest services, passes, and resort offerings.
- **Analytics and reporting** – Resort metrics, performance insights, and historical data.
- **VPS-ready deployment** – Optimized for common VPS environments.
- **Docker support** – Containerized services with `docker-compose`.
- **Reverse proxy support** – First-class support for NGINX / OpenResty.
- **SSL / TLS support** – Production-friendly HTTPS setup.
- **API-ready architecture** – Backend designed for SPA and mobile clients.

---

## Tech Stack

Ski Manager is built as a polyglot, service‑oriented web application. You can adopt all or only some of the recommended components.

### Frontend

- HTML5
- CSS3 / SCSS
- JavaScript / TypeScript
- React or Vue (recommended)
- Optional tooling: Vite, Tailwind CSS

### Backend

- Node.js / Express
- PHP / Laravel
- Optional Python services (e.g., FastAPI) for specialized tasks

### Infrastructure

- NGINX / OpenResty (reverse proxy)
- Docker / Docker Compose
- Ubuntu Server (24.04 recommended)
- Redis
- MySQL or PostgreSQL

---

## Project Structure

```text
.
├── app/
├── config/
├── public/
├── storage/
├── logs/
├── docker/
├── scripts/
├── nginx/
├── docs/
├── tests/
├── .env
├── README.md
├── ARCHITECTURE.md
└── .gitignore
