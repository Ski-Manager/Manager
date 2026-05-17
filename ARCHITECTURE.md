# ARCHITECTURE.md

# System Architecture

## Overview

Ski Manager is designed as a modular, scalable web platform for ski resort operations.

The platform separates infrastructure, backend services, frontend rendering, and storage layers for easier maintenance and horizontal scaling.

---

# High-Level Architecture

                        ┌─────────────────┐
                        │     Client      │
                        │ Browser / App   │
                        └────────┬────────┘
                                 │
                                 ▼
                    ┌──────────────────────┐
                    │ Cloudflare / CDN/WAF │
                    └────────┬─────────────┘
                             │
                             ▼
                    ┌──────────────────────┐
                    │  NGINX / OpenResty   │
                    │ Reverse Proxy Layer  │
                    └────────┬─────────────┘
                             │
        ┌────────────────────┴────────────────────┐
        ▼                                         ▼
┌─────────────────┐                     ┌─────────────────┐
│ Frontend Server │                     │ API / Backend   │
│ React / Vue     │                     │ Node / PHP      │
└────────┬────────┘                     └────────┬────────┘
         │                                       │
         ▼                                       ▼
┌─────────────────┐                     ┌─────────────────┐
│ Static Assets   │                     │ Authentication  │
│ CDN Cache       │                     │ Business Logic  │
└─────────────────┘                     └────────┬────────┘
                                                 │
                         ┌───────────────────────┴───────────────────────┐
                         ▼                                               ▼
               ┌─────────────────┐                           ┌─────────────────┐
               │ Redis Cache     │                           │ Database Layer  │
               │ Sessions/Cache  │                           │ MySQL/Postgres  │
               └─────────────────┘                           └─────────────────┘

---

# Components

## Frontend Layer

Responsible for:

- User interface
- Dashboard rendering
- Interactive maps
- Admin controls
- Real-time updates

### Recommended Stack

- React
- Vue
- Tailwind CSS
- Vite

---

## Backend Layer

Responsible for:

- Authentication
- API endpoints
- Resort logic
- User permissions
- Analytics
- Data validation

### Recommended Stack

- Node.js + Express
- Laravel
- FastAPI microservices

---

## Database Layer

Stores:

- User accounts
- Resort configurations
- Lift systems
- Trail metadata
- Snowmaking data
- Logs
- Analytics

### Recommended Databases

- PostgreSQL
- MySQL

---

## Cache Layer

Redis is used for:

- Session storage
- Queue processing
- API caching
- Rate limiting
- Temporary state

---

## Infrastructure Layer

### Reverse Proxy

NGINX/OpenResty handles:

- SSL termination
- Load balancing
- Reverse proxy routing
- Compression
- Static asset delivery

### CDN/WAF

Cloudflare provides:

- DDoS protection
- Global caching
- DNS management
- TLS certificates
- Bot filtering

---

# Deployment Model

## Single VPS

Best for:
- Small deployments
- Testing
- Development

Services run on one server.

---

## Distributed Deployment

Best for:
- Production
- Large traffic loads
- High availability

Recommended separation:

| Service | Host |
|---|---|
| Reverse Proxy | Edge VPS |
| Backend API | App VPS |
| Database | DB VPS |
| Redis | Cache VPS |
| Storage | Object Storage |

---

# Docker Architecture

docker-compose
│
├── nginx
├── frontend
├── backend
├── redis
├── database
└── worker

---

# Logging

Recommended logging paths:

/www/sites/skimanager/log/

Important logs:

- access.log
- error.log
- application.log
- worker.log

---

# Security Architecture

## Recommended Security Features

- HTTPS enforcement
- HSTS headers
- Rate limiting
- CSRF protection
- Input validation
- SQL injection protection
- Container isolation
- Firewall restrictions

---

# Backup Strategy

## Daily
- Database dump
- Config backups

## Weekly
- Full storage snapshot

## Monthly
- Offsite archive backup

---

# Scaling Strategy

Horizontal scaling supported through:

- Stateless backend services
- Redis shared sessions
- Load-balanced API servers
- CDN asset caching
- Database replication

---

# Monitoring

Recommended tools:

- Prometheus
- Grafana
- Uptime Kuma
- Netdata

---

# Future Expansion

Potential modules:

- Mobile applications
- Lift telemetry
- RFID passes
- Weather integrations
- Snow depth AI analytics
- Resort simulation systems
- Real-time map overlays
