# Security Policy

## Supported Versions

Ski Manager is a live, continuously deployed web game. There are no versioned releases — the `main` branch is the only supported version. Security fixes are applied directly to `main` and deployed automatically.

| Branch / Version | Supported |
| ---------------- | --------- |
| `main` (current) | ✅ |
| Any prior commit | ❌ |

---

## Reporting a Vulnerability

**Please do not open a public GitHub issue for security vulnerabilities.**

If you discover a security issue, report it privately so it can be investigated and patched before public disclosure.

### How to report

Send a report to the maintainer by opening a [GitHub Security Advisory](../../security/advisories/new) (preferred), or by emailing **security@skimanager.net** with the subject line `[SECURITY] <brief description>`.

Include as much of the following as possible:

- A clear description of the vulnerability
- The affected file(s) and line numbers if known
- Steps to reproduce or a proof-of-concept (no live exploitation, please)
- Potential impact (what an attacker could do)
- Your suggested fix, if you have one

### What to expect

| Milestone | Timeframe |
| --------- | --------- |
| Acknowledgement of your report | Within **48 hours** |
| Confirmation of whether the issue is accepted or declined | Within **7 days** |
| Patch deployed to `main` (if accepted) | Within **14 days** for critical issues, **30 days** for others |
| Public disclosure (coordinated) | After the patch is live |

If a report is **declined**, you will receive a clear explanation of why (e.g. out of scope, already known, not reproducible, intended behavior).

### Scope

In scope:

- Authentication and session handling (`Login_controller`, `Register_controller`, session cookies)
- Player data access — unauthorized access to another player's resort, finances, or account
- The ops dashboard (`api/ops.php`) and deploy pipeline
- The night skiing API (`api/night_skiing.php`)
- Payment flows (PayPal integration)
- Account reset and deletion flows (`Reset_controller`)

Out of scope:

- Vulnerabilities in third-party libraries that are already publicly known and tracked upstream (e.g. CodeIgniter 3 known issues)
- Denial-of-service attacks against the game simulation (cron jobs, visitor counts)
- UI bugs with no security impact
- Issues that require physical access to the server

### Responsible disclosure

We ask that you:

- Give us a reasonable amount of time to patch before publishing details publicly
- Do not access or modify other players' data beyond what is necessary to demonstrate the issue
- Do not disrupt the live game or other players during testing

We commit to:

- Treating your report with confidentiality
- Crediting you in the patch notes if you wish to be named
- Not taking legal action against good-faith security researchers

---

## Known Security Commitments

The following security practices are maintained in this project:

- All mutating API endpoints require an active authenticated session tied to the resort being modified
- Resort ownership is verified server-side on all write operations — client-supplied resort IDs are not trusted
- The night skiing API (`api/night_skiing.php`) validates session cookies against the database and confirms resort ownership before any write
- Secrets and credentials must not be committed to this repository — they belong in server environment variables or the server's secrets manager
