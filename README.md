# Ski Manager

Ski Manager is a browser-based ski resort management simulation game. Players build and operate a virtual ski resort — managing lifts, slopes, snowmaking, staff, finances, guests, and long-term strategy across real simulated seasons.

---

## Features

### Resort Operations
- Lift management (construction, upgrades, lift lines, lift network, scenic lifts)
- Slope and trail management (grooming, snowmaking per trail, slope upgrades)
- Snow cannon fleet and snowmaking operations
- Night skiing with configurable lighting levels
- Mountain plan and resort map

### Guest Experience
- Dynamic guest AI and visitor needs simulation
- Demand curve and crowding management
- Ski school, rental shop, and guided tours
- VIP loyalty program
- Guest skill progression system

### Facilities & Revenue
- Hotel and accommodation management
- Restaurant, retail, and leisure facilities
- Luxury amenities and event venues
- Season passes and daily ski passes (with group discounts and parking fees)
- Building access control with dynamic pricing

### Finance & Business
- Resort bank and financial reporting
- Insurance management
- PayPal payment integration
- Sponsorship and marketing campaigns
- Real estate and town development
- Empire mode (multi-resort management)

### Events & Engagement
- Special events and tournaments
- Celebrity visits and micro-events
- Mini-games and ski quiz
- Daily bonus system
- Achievements

### Analytics & Strategy
- Data dashboard and reporting
- Leaderboard (global, by country, by slope)
- Competitor analysis
- Seasonal objectives
- R&D upgrades

### Environment & Infrastructure
- Climate change simulation
- Energy management
- Microclimate and weather systems
- Transportation and ski bus
- Government relations
- Emergency and crisis management

### Platform
- Maintenance mode and closed/pre-launch mode
- Multi-language support (i18n via Language Switcher)
- CrazyGames SDK integration
- Google OAuth login
- Cron-driven simulation (nightly, morning, 2-minute, and event cycles)
- REST API for external data consumers
- Blog, guide, and help pages

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend framework | CodeIgniter 3 (PHP) |
| Database | MySQL |
| Caching | Redis / Memcached |
| Sessions | Database or file-based (CodeIgniter Session) |
| Auth | Custom + Google OAuth + CrazyGames SDK |
| Payments | PayPal |
| Web server | Apache (`.htaccess` URL rewriting) |
| Reverse proxy | NGINX / OpenResty (optional) |
| CDN / WAF | Cloudflare (optional) |
| CI/CD | GitHub Actions → SSH deploy to VPS |
| Server management | 1Panel |

---

## Project Structure

```
.
├── application/
│   ├── config/           # CI3 config files (database, routes, game constants)
│   ├── controllers/      # One controller per game module + crons/ subfolder
│   ├── models/           # Data models
│   ├── views/            # Rendered HTML views
│   └── helpers/          # Custom helper functions
├── system/               # CodeIgniter 3 core (do not modify)
├── api/                  # Standalone REST endpoints (ops, night skiing)
├── .github/
│   ├── workflows/        # GitHub Actions deploy pipeline
│   └── dependabot.yml
├── .htaccess             # Apache URL rewriting + security rules
├── .user.ini             # PHP runtime overrides
├── Users_model.php       # Core user model
├── 404.html
├── ads.txt
├── README.md
├── ARCHITECTURE.md
└── .gitignore
```

### Cron Jobs

Scheduled tasks drive the game simulation engine:

| Cron | Trigger | Purpose |
|---|---|---|
| `Cron2min_controller` | Every 2 min | Real-time updates |
| `CronMorning_controller` | Daily morning | New-day setup |
| `NightlyMainJobs_controller` | Nightly | Core simulation tick |
| `CronPostNightlyJobs_controller` | Post-nightly | Follow-up calculations |
| `CronSpecialEvents_controller` | Scheduled | Event processing |
| `CronTournaments_controller` | Scheduled | Tournament processing |
| `NightlyDBBackup_controller` | Nightly | Database backup |
| `ReportingData_controller` | Scheduled | Analytics aggregation |

---

## Getting Started

### Requirements

- PHP 8.0+
- MySQL 5.7+ or MariaDB 10+
- Apache with `mod_rewrite` enabled
- Redis or Memcached (optional but recommended)
- A VPS or shared hosting environment supporting `.htaccess`

### Setup

1. Clone the repo and copy to your web root.
2. Copy `application/config/database.php` and fill in your DB credentials.
3. Copy `application/config/config.php` and set `base_url`, encryption keys, and environment flags.
4. Import the database schema (see `docs/` or your migration setup).
5. Configure cron jobs on the server to hit the cron controllers on schedule.
6. Set `maintenance_mode` or `closed_mode` in `config.php` as needed during launch.

### Deployment

Deployment is automated via GitHub Actions on push to `main`. Requires three repository secrets:

| Secret | Value |
|---|---|
| `VPS_SSH_KEY` | Private SSH key for the VPS |
| `VPS_USER` | SSH username |
| `VPS_HOST` | VPS hostname or IP |

See `.github/workflows/deploy.yml` for the full pipeline.

---

## Configuration Notes

- **Game pricing constants** live in `application/config/game_pricing.php` — ski pass limits, group discounts, parking fees, and night skiing quality factors.
- **Maintenance / closed mode** is toggled in `application/config/config.php` and enforced in `application/config/routes.php`. Whitelisted IPs (e.g. dev team) bypass these modes.
- **Cron routes** are defined before the maintenance catch-all in `routes.php` so scheduled jobs always run regardless of site mode.

---

## License

See [LICENSE](LICENSE) for full terms.
