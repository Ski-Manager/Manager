# Architecture

## Overview

Ski Manager is a server-rendered PHP web application built on **CodeIgniter 3**, deployed on a single VPS behind Apache. A cron-driven simulation engine handles the game tick cycle. A lightweight REST API layer serves external data consumers (e.g. mobile clients, third-party embeds).

---

## High-Level Diagram

```
                        ┌─────────────────────┐
                        │     Player Browser  │
                        └──────────┬──────────┘
                                   │ HTTPS
                                   ▼
                    ┌──────────────────────────┐
                    │   Cloudflare (optional)  │
                    │   CDN / WAF / DDoS       │
                    └──────────┬───────────────┘
                               │
                               ▼
                    ┌──────────────────────────┐
                    │  NGINX / OpenResty        │
                    │  Reverse Proxy (optional) │
                    └──────────┬───────────────┘
                               │
                               ▼
                    ┌──────────────────────────┐
                    │  Apache + mod_rewrite     │
                    │  .htaccess URL routing    │
                    └──────────┬───────────────┘
                               │
                               ▼
                    ┌──────────────────────────┐
                    │  CodeIgniter 3 (PHP)      │
                    │  MVC Application          │
                    └────┬─────────────────┬───┘
                         │                 │
              ┌──────────┘                 └──────────┐
              ▼                                       ▼
  ┌───────────────────────┐             ┌─────────────────────────┐
  │  Controllers          │             │  REST API (/api/)        │
  │  ~80 game modules     │             │  ops.php, night_skiing   │
  └──────────┬────────────┘             └─────────────┬───────────┘
             │                                        │
             ▼                                        ▼
  ┌───────────────────────┐             ┌─────────────────────────┐
  │  Models               │             │  MySQL Database          │
  │  Business logic       │◄───────────►│  Game state / users      │
  └──────────┬────────────┘             └─────────────────────────┘
             │
             ▼
  ┌───────────────────────┐
  │  Redis / Memcached    │
  │  Sessions / Cache     │
  └───────────────────────┘

  Cron Scheduler (server-side)
  └──► Cron Controllers (8 scheduled jobs)
       └──► Simulation engine tick (nightly, morning, 2-min, events)
```

---

## Application Layer (CodeIgniter 3 MVC)

### Entry Point

All requests hit `index.php` at the web root. Apache's `.htaccess` strips `index.php` from URLs and enforces security headers. CodeIgniter's front controller dispatches to the matching controller.

### Routing

Defined in `application/config/routes.php`. Key behaviors:

- **Default controller**: `Home_controller`
- **Maintenance / closed mode**: Activated via flags in `config.php`. Non-whitelisted IPs are routed to `Maintenance_controller` for all paths, except legal pages and contact. Whitelisted dev IPs bypass the catch-all.
- **Cron routes**: Defined *before* the maintenance catch-all so scheduled jobs are never blocked by site mode.
- **SEO-friendly aliases**: `/trails`, `/leaderboard`, `/blog`, `/guide`, `/privacy`, `/contact`, etc. map to their controllers cleanly.
- **Admin prefix handling**: Compatibility aliases route `/admin/leaderboard_controller/*` to the correct controllers.
- **CrazyGames**: `/crazygames_controller/verify_token` routes to `Crazygames_controller` for SDK auth.

### Controllers (Game Modules)

Controllers are organized by domain. Each controller handles one game system:

**Core / Auth**
- `Login_controller` — email/password auth, Google OAuth callback
- `Register_controller`, `Reset_controller`, `Reset_password_controller`
- `Account_controller`, `User_authentication`
- `Maintenance_controller`, `Beta_controller`

**Resort Operations**
- `Lift_controller`, `Lift_line_controller`, `Lift_network_controller`, `Lift_tech_controller`, `Scenic_lift_controller`
- `Slope_controller`, `Slope_upgrade_controller`, `Trail_snowmaking_controller`
- `Snow_cannon_controller`, `Snowmaking_upgrade_controller`
- `Groomer_controller`
- `Night_skiing_controller`
- `Mountain_plan_controller`, `Resort_map_controller`
- `Resort_controller`

**Guest Experience**
- `Guest_ai_controller`, `Guest_skill_controller`, `Visitor_needs_controller`
- `Demand_curve_controller`, `Crowding_controller`
- `Ski_school_controller`, `Rental_controller`
- `Vip_loyalty_controller`

**Facilities & Revenue**
- `Hotel_controller`, `Accommodation_controller`
- `Restaurant_controller`, `Retail_controller`, `Leisure_controller`
- `Luxury_controller`, `Event_venues_controller`
- `Building_access_controller` — ski pass pricing, group discounts, parking fees
- `Season_pass_controller`

**Finance & Business**
- `Bank_controller`, `Finances_controller`, `Insurance_controller`
- `Marketing_controller`, `Marketing_upgrade_controller`
- `Sponsorship_controller`
- `Real_estate_controller`, `Town_controller`
- `Empire_controller` — multi-resort management

**Analytics & Strategy**
- `Data_dashboard_controller`, `Statistics_controller`, `Reporting_controller`
- `Leaderboard_controller` — global, by country, by slope
- `Competitors_controller`
- `Seasonal_objectives_controller`
- `Rd_controller` — research and development

**Events & Engagement**
- `Special_events_controller`, `Micro_events_controller`
- `Celebrity_visit_controller`, `Tournaments_controller`
- `Minigames_controller`, `Ski_quiz_controller`
- `Achievements_controller`, `Daily_bonus_controller`

**Environment & Infrastructure**
- `Climate_change_controller`, `Energy_controller`
- `Environment_controller`, `Microclimate_controller`, `Weather_controller`
- `Transportation_controller`, `Skibus_controller`
- `Government_controller`
- `Emergency_controller`, `Crisis_controller`, `Medical_controller`

**Staff**
- `Hire_staff_controller`, `Overview_staff_controller`, `Staff_upgrade_controller`
- `Upgrades_controller`, `Facilities_controller`, `Maintenance_controller`

**Cron Jobs** (`application/controllers/crons/`)
- `Cron2min_controller` — real-time game tick
- `CronMorning_controller` — daily morning setup
- `NightlyMainJobs_controller` — core nightly simulation
- `CronPostNightlyJobs_controller` — post-nightly follow-ups
- `CronSpecialEvents_controller` — event processing
- `CronTournaments_controller` — tournament processing
- `NightlyDBBackup_controller` — automated DB backup
- `ReportingData_controller` — analytics aggregation

**Public / CMS**
- `Home_controller`, `About_controller`, `Contact_controller`
- `Blogs_controller`, `Guide_controller`, `Help_controller`
- `Privacy_controller`, `Terms_controller`, `About_cookies_controller`
- `Leaderboard_controller` (public-facing)
- `Language_switcher`

---

## REST API (`/api/`)

Standalone PHP scripts outside the CodeIgniter MVC stack, serving JSON to external consumers:

| Endpoint | File | Description |
|---|---|---|
| `GET /api/` | `index.php` | API discovery / health |
| `GET /api/ops` | `ops.php` | Resort operations data |
| `GET /api/night_skiing` | `night_skiing.php` | Night skiing status and quality |

---

## Database Layer

- **Engine**: MySQL (configured in `application/config/database.php`)
- **ORM**: CodeIgniter's Active Record / Query Builder
- **Session storage**: Database-backed CI3 sessions (fallback: file-based)
- **Backup**: Automated nightly via `NightlyDBBackup_controller`

---

## Caching Layer

- **Redis** or **Memcached** (configured in `application/config/memcached.php`)
- Used for session storage, output caching, and expensive game-state queries
- Cache drivers: `Cache_redis`, `Cache_memcached`, `Cache_file`, `Cache_apc`, `Cache_dummy`

---

## Authentication

| Method | Implementation |
|---|---|
| Email / password | Custom CI3 auth (`Login_controller`, `User_authentication`) |
| Google OAuth | `Login_controller/googleCallback` |
| CrazyGames SDK | `Crazygames_controller/verify_token` |

Session tokens stored in the database session table. Whitelisted IPs for maintenance bypass stored in `config.php` under `maintenance_ips`.

---

## Configuration Files

| File | Purpose |
|---|---|
| `application/config/config.php` | Base URL, encryption key, site mode flags |
| `application/config/database.php` | DB credentials and connection settings |
| `application/config/routes.php` | URI routing rules and maintenance mode logic |
| `application/config/game_pricing.php` | Game constants: ski pass limits, group discounts, parking, night skiing quality |
| `application/config/autoload.php` | Auto-loaded libraries, helpers, models |
| `application/config/hooks.php` | CI3 lifecycle hooks |
| `application/config/email.php` | SMTP / email settings |
| `application/config/google.php` | Google OAuth credentials |
| `application/config/facebook.php` | Facebook credentials |
| `application/config/memcached.php` | Cache server settings |

---

## CI/CD Pipeline

```
Developer pushes to main
        │
        ▼
GitHub Actions (ubuntu-latest)
        │
        ├── Load SSH key from VPS_SSH_KEY secret
        │
        └── SSH into VPS_USER@VPS_HOST
                │
                └── cd /opt/1panel/www/sites/skimanager/index
                        └── git pull origin main
```

Managed via **1Panel** on the VPS. PHP-FPM restarts (if needed) are added as a step in `deploy.yml`.

Dependabot monitors GitHub Actions action versions (`dependabot.yml`).

---

## Security Considerations

- `.htaccess` blocks direct access to `application/` and `system/` directories
- `.user.ini` overrides PHP runtime settings (upload limits, execution time)
- All environment secrets (DB credentials, OAuth keys, API keys) must be set in `config.php` and never committed
- SSL/TLS certificates managed at the NGINX or Cloudflare layer
- Maintenance IP whitelist prevents lockout during deploys
