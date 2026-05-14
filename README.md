# Hermes HUD

A lightweight web dashboard to monitor the [Hermes Agent](https://github.com/jefersonpn) running locally on macOS. Built with Laravel 12, Inertia, Vue 3, and Tailwind CSS 4.

## What it shows

- **System metrics** — CPU load and RAM usage, read directly from `vm_stat` / `sysctl` on the host machine.
- **Hermes gateway status** — online/offline state, PID liveness check, active agents, per-platform state — sourced from `~/.hermes/gateway_state.json`.
- **Agent logs** — the last N lines of `~/.hermes/logs/agent.log`, parsed into structured timestamp / level / message entries.

The HUD reads Hermes's local state files directly, so no extra daemon or socket is required between them.

## Stack

| Layer    | Tech                                  |
| -------- | ------------------------------------- |
| Backend  | Laravel 12, PHP 8.2+                  |
| Frontend | Inertia.js + Vue 3                    |
| Styling  | Tailwind CSS 4 (Vite plugin)          |
| Build    | Vite 7                                |
| Database | SQLite (default, for sessions/auth)   |

## Requirements

- PHP **8.2+**
- Composer
- Node.js **20+** and npm
- macOS host (the system endpoints use `vm_stat` and `sysctl -n hw.ncpu`)
- A running Hermes agent that writes its state under `~/.hermes` (configurable)

## Setup

```bash
git clone git@github.com:jefersonpn/hermes-hud.git
cd hermes-hud

composer setup
```

`composer setup` runs `composer install`, copies `.env.example` → `.env`, generates the app key, runs migrations, then `npm install && npm run build`.

## Running

For local development with hot reload, logs, and queue worker all together:

```bash
composer dev
```

This launches `php artisan serve`, `queue:listen`, `pail` (live log viewer), and `vite` concurrently.

Then open [http://localhost:8000](http://localhost:8000) and log in.

## Configuration

The Hermes integration is configured via environment variables (see [`config/hermes.php`](config/hermes.php)):

| Variable                        | Default              | Purpose                                                                 |
| ------------------------------- | -------------------- | ----------------------------------------------------------------------- |
| `HERMES_DATA_ROOT`              | `$HOME/.hermes`      | Root directory where Hermes writes `gateway_state.json` and `logs/`.    |
| `HERMES_LOG_TAIL`               | `30`                 | Default number of log lines returned by `/hud/api/hermes/logs`.         |
| `HERMES_GATEWAY_STALE_SECONDS`  | `30`                 | How old `gateway_state.json` can be before being flagged as stale.      |

## API endpoints

All endpoints sit under `/hud/api` and require an authenticated session.

| Method | Path                       | Returns                                                            |
| ------ | -------------------------- | ------------------------------------------------------------------ |
| GET    | `/hud/api/system`          | `{ cpu, ram, load, cores, ts }` — host metrics                     |
| GET    | `/hud/api/hermes/status`   | Gateway status + per-platform state, with PID liveness check       |
| GET    | `/hud/api/hermes/logs`     | Last N parsed log entries from `~/.hermes/logs/agent.log`          |
| GET    | `/hud/api/hermes/gateway`  | Raw contents of `gateway_state.json`                               |

## Project layout

```
app/
├── Http/Controllers/
│   ├── Auth/LoginController.php   # session-based login
│   └── HudController.php          # dashboard + system + Hermes endpoints
└── Services/
    └── HermesClient.php           # reads ~/.hermes state files
resources/js/
├── Pages/Hud/Dashboard.vue        # main HUD screen
└── Pages/Auth/                    # login page
config/hermes.php                  # Hermes integration config
routes/web.php                     # routes
```

## Testing

```bash
composer test
```

Runs `php artisan test` after clearing the config cache.

## License

MIT.
