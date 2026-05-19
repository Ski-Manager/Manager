# Ski Manager

Ski Manager is an open-source browser-based ski resort management simulation game where players build, operate, and expand a fully simulated ski resort across dynamic seasons.

The project combines business management, guest simulation, mountain operations, weather systems, infrastructure planning, and long-term strategy into a large-scale resort management experience inspired by classic tycoon and simulation games.

The goal of the project is to build the most advanced open-source ski resort simulator on the web.

---

## Website

- Production game: https://ski-manager.net
- Bug tracker / automated analysis: https://docs.google.com/spreadsheets/d/1CcNFAsFVS9rXGUQqlRAj2NNu5vp7rwL-hMji2oB2LLs/edit?usp=sharing

---

# Open Source Goals

Ski Manager is actively developed and community contributions are encouraged.

Contributors can help with:

- New gameplay systems
- UI/UX improvements
- Performance optimization
- PHP 8 compatibility fixes
- AI simulation improvements
- Mobile responsiveness
- Database optimization
- Balancing and economy tuning
- Security improvements
- API development
- DevOps and CI/CD
- Documentation
- Bug fixes and refactoring

Both beginner-friendly and advanced contributions are welcome.

---

# Core Gameplay Systems

## Resort Operations

- Lift construction and upgrades
- Lift queues and transport simulation
- Scenic gondolas and sightseeing systems
- Slope creation and terrain management
- Grooming operations
- Snowmaking systems
- Snow cannon fleet management
- Night skiing
- Resort map and mountain planning
- Terrain expansion

## Guest Simulation

- Dynamic guest AI
- Crowd flow and congestion systems
- Skill progression
- Guest happiness and satisfaction
- VIP guests and loyalty systems
- Rental shop systems
- Ski school and guided tours
- Dynamic demand simulation

## Business & Economy

- Resort finances and accounting
- Marketing campaigns
- Sponsorship systems
- Insurance systems
- Dynamic pricing
- Parking systems
- Season passes and ticketing
- Hotel and accommodation management
- Food, retail, and entertainment venues
- Real estate and town expansion
- Multi-resort empire management

## Environment & Infrastructure

- Weather simulation
- Climate change systems
- Energy management
- Ski buses and transportation
- Emergency response systems
- Government relations
- Microclimate simulation

## Events & Multiplayer Features

- Tournaments
- Seasonal events
- Leaderboards
- Daily rewards
- Achievements
- Competitor analysis
- Analytics dashboards

---

# Technology Stack

| Layer | Technology |
|---|---|
| Backend Framework | CodeIgniter 3 (PHP) |
| Language | PHP 8+ |
| Database | MySQL / MariaDB |
| Caching | Redis / Memcached |
| Sessions | CodeIgniter Sessions |
| Authentication | Custom Auth + Google OAuth + CrazyGames SDK |
| Payments | PayPal |
| Web Server | Apache |
| Reverse Proxy | NGINX / OpenResty |
| CDN / WAF | Cloudflare |
| Deployment | GitHub Actions |
| Infrastructure | VPS + 1Panel |

---

# Project Structure

```text
.
├── application/
│   ├── config/           # CI3 config files and game constants
│   ├── controllers/      # Main game controllers
│   ├── controllers/crons # Simulation cron controllers
│   ├── models/           # Database models
│   ├── views/            # HTML templates/views
│   ├── helpers/          # Custom helper functions
│   └── libraries/        # Custom libraries and integrations
│
├── api/                  # Standalone REST/API endpoints
├── system/               # CodeIgniter core
├── assets/               # CSS, JS, images, UI assets
├── uploads/              # User-generated content
│
├── .github/
│   ├── workflows/        # GitHub Actions CI/CD
│   └── dependabot.yml
│
├── .htaccess
├── .user.ini
├── README.md
├── ARCHITECTURE.md
├── LICENSE
└── .gitignore
```

---

# Simulation Engine

The game runs on a cron-driven simulation system.

## Cron Jobs

| Cron Controller | Frequency | Purpose |
|---|---|---|
| `Cron2min_controller` | Every 2 minutes | Real-time resort updates |
| `CronMorning_controller` | Daily | New-day initialization |
| `NightlyMainJobs_controller` | Nightly | Main simulation processing |
| `CronPostNightlyJobs_controller` | Nightly | Post-processing calculations |
| `CronSpecialEvents_controller` | Scheduled | Event handling |
| `CronTournaments_controller` | Scheduled | Tournament processing |
| `NightlyDBBackup_controller` | Nightly | Automated backups |
| `ReportingData_controller` | Scheduled | Analytics aggregation |

The cron system powers:

- Guest movement
- Resort economy
- Weather progression
- Staff systems
- Snow conditions
- Event generation
- AI decisions
- Leaderboards
- Financial calculations

---

# Getting Started

## Requirements

- PHP 8.0+
- MySQL 5.7+ or MariaDB 10+
- Apache with `mod_rewrite`
- Redis or Memcached (recommended)
- Linux VPS or shared hosting
- Cron access

---

## Installation

### 1. Clone the Repository

```bash
git clone https://github.com/YOUR_ORG/ski-manager.git
```

### 2. Configure the Database

Edit:

```text
application/config/database.php
```

Add your MySQL credentials.

---

### 3. Configure the Application

Edit:

```text
application/config/config.php
```

Set:

- `base_url`
- encryption keys
- environment settings
- maintenance flags

---

### 4. Import the Database

Import your SQL schema into MySQL/MariaDB.

---

### 5. Configure Cron Jobs

Set up cron jobs to trigger the simulation controllers.

Example:

```bash
*/2 * * * * php /path/to/index.php cron/Cron2min_controller
```

---

### 6. Configure Web Server

Enable Apache rewrite rules:

```bash
a2enmod rewrite
```

Ensure `.htaccess` overrides are enabled.

---

# Deployment

Deployments are automated using GitHub Actions.

## Required GitHub Secrets

| Secret | Description |
|---|---|
| `VPS_SSH_KEY` | SSH private key |
| `VPS_USER` | SSH username |
| `VPS_HOST` | VPS IP or hostname |

CI/CD automatically deploys changes to production after pushes to `main`.

---

# Contributing

Community contributions are highly encouraged.

## Recommended Contribution Areas

### Gameplay Systems
- New lifts
- New weather systems
- AI improvements
- Resort balancing
- Staff management
- Terrain systems

### Technical Improvements
- PHP 8 modernization
- Database optimization
- Redis caching
- API expansion
- Security hardening
- Refactoring legacy CI3 code

### Frontend
- Mobile UI improvements
- Accessibility
- Responsive layouts
- Improved dashboards
- Interactive maps

### Infrastructure
- Docker support
- Better CI/CD
- Monitoring
- Automated testing

---

## Contribution Workflow

1. Fork the repository
2. Create a feature branch
3. Commit changes
4. Open a pull request
5. Link related issues or bug reports

---

## Coding Guidelines

- Follow existing CodeIgniter 3 patterns where practical
- Avoid modifying `/system`
- Keep controllers thin and move logic into models/helpers
- Prefer reusable components/helpers
- Test cron-related changes carefully
- Maintain backward compatibility where possible

---

# Development Notes

## Maintenance / Closed Mode

Maintenance and pre-launch restrictions are controlled in:

```text
application/config/config.php
```

Routing behavior is enforced in:

```text
application/config/routes.php
```

Whitelisted developer IPs can bypass restrictions.

---

## Pricing & Economy Configuration

Core pricing constants live in:

```text
application/config/game_pricing.php
```

Includes:

- Ski pass pricing
- Parking fees
- Group discounts
- Night skiing modifiers
- Economy balancing constants

---

# QA & Automated Bug Tracking

Static analysis results, automated scans, and generated bug reports are tracked here:

https://docs.google.com/spreadsheets/d/1CcNFAsFVS9rXGUQqlRAj2NNu5vp7rwL-hMji2oB2LLs/edit?usp=sharing

Contributors are encouraged to review unresolved issues and submit fixes.

---

# Roadmap Ideas

Planned or possible future systems include:

- Multiplayer economy
- Live weather APIs
- 3D resort map
- Steam integration
- Modding support
- Avalanche systems
- Dynamic terrain editor
- VR compatibility
- Dedicated mobile app
- Staff unions and labor systems
- Advanced AI competitors
- Procedural mountain generation

---

# License

See [LICENSE](LICENSE) for full licensing details.
