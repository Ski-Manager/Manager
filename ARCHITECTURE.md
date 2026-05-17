# System Architecture

## Overview

Ski Manager is a modular, scalable web platform for ski resort operations.

The platform separates infrastructure, backend services, frontend rendering, and storage layers to simplify maintenance and enable horizontal scaling.

---

## High-Level Architecture

```text
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
