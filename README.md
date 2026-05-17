# README.md

# Ski Manager

A modern ski resort management platform built for hosting, managing, and operating ski resort systems online.

## Features

- Resort management dashboard
- Lift and trail management
- Snowmaking systems
- Staff administration
- Guest services
- Analytics and reporting
- VPS-ready deployment
- Docker support
- Reverse proxy support with NGINX/OpenResty
- SSL support
- API-ready architecture

---

## Tech Stack

### Frontend
- HTML5
- CSS3 / SCSS
- JavaScript / TypeScript
- React or Vue (optional)

### Backend
- Node.js / Express
- PHP / Laravel
- Python services (optional)

### Infrastructure
- NGINX / OpenResty
- Docker
- Ubuntu Server
- Redis
- MySQL / PostgreSQL

---

## Project Structure

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

---

## Installation

### Clone the Repository

git clone https://github.com/yourusername/ski-manager.git
cd ski-manager

### Install Dependencies

#### Node.js

npm install

#### PHP

composer install

---

## Environment Setup

Create a `.env` file:

APP_ENV=production
APP_DEBUG=false

DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=skimanager
DB_USERNAME=user
DB_PASSWORD=password

---

## Development

Start the development server:

npm run dev

Or:

docker compose up -d

---

## Production Deployment

### Recommended VPS Stack

- Ubuntu Server 24.04
- Docker Engine
- OpenResty or NGINX
- Cloudflare
- Fail2Ban
- UFW Firewall
- Redis cache
- Automated backups

---

## NGINX Notes

Ensure the following directories exist before startup:

mkdir -p /www/sites/skimanager/log
mkdir -p /www/sites/skimanager/tmp

---

## Security

Recommended security setup:

- HTTPS only
- Cloudflare proxy enabled
- Automatic SSL renewal
- Rate limiting
- WAF protection
- Daily backups
- Non-root containers

---

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit changes
4. Open a pull request

---

## License

MIT License
