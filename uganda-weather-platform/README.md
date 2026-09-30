# Uganda Agricultural Weather Platform (Prototype v1.0)

A PHP + MySQL + JavaScript web app that gives Ugandan farmers live district weather,
personalized crop recommendations, and yield tracking — with an analytics and
national dashboard for ministry-level oversight.

## Tech Stack
- PHP 8 (plain PHP, PDO for MySQL — no framework)
- MySQL / MariaDB
- Bootstrap 5 (UI), Leaflet.js (map), Chart.js (analytics)
- Open-Meteo API (free, no API key needed) for live weather

## Setup (XAMPP)

1. Install [XAMPP](https://www.apachefriends.org/) and start **Apache** and **MySQL**.
2. Copy this whole `uganda-weather-platform` folder into `C:\xampp\htdocs\`.
3. Import `database/schema.sql` then `database/seed_demo.sql` (via
   phpMyAdmin, the `mysql` CLI, or a GUI client — see
   **`database/SETUP_GUIDE.md`** for full step-by-step instructions covering
   XAMPP, MySQL, and MariaDB, plus how to configure credentials in
   `app/config/db.php` if yours differ from the XAMPP default).
4. Visit **http://localhost/uganda-weather-platform/public/**

## Demo accounts (after importing seed_demo.sql)

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@agriweather.ug | admin123 |
| Ministry | ministry@demo.ug | password123 |
| Farmer | farmer@demo.ug | password123 |
| Association admin | sarah.nabirye@demo.ug | password123 |
| Extension officer | james.t@demo.ug | password123 |
| Researcher | aisha.namono@demo.ug | password123 |

## Folder structure

```
uganda-weather-platform/
├── public/              # Web root — point your server here (…/public/)
│   ├── assets/          # css, js, img
│   ├── api/             # JSON endpoints (weather, analytics, recommend, check_email)
│   ├── farms/           # Farm CRUD
│   ├── crops/           # Crop management (admin/ministry only)
│   ├── reports/         # Planting + yield report flow
│   ├── analytics/       # Charts
│   ├── ministry/        # National dashboard + CSV export
│   ├── index.php, login.php, register.php, dashboard.php, map.php, recommend.php
├── app/
│   ├── config/db.php    # PDO connection + site constants
│   ├── helpers/         # header/footer, auth guards, recommendation rules
│   └── models/          # User, Farm, Crop, Planting, YieldReport, Recommendation
├── database/
│   ├── schema.sql        # Tables + seed reference data (roles, districts, crops)
│   └── seed_demo.sql     # Demo users, farms, plantings, yield reports
├── .env                  # DB credentials read via getenv() fallback in db.php
└── .gitignore
```

## How each part works

- **Auth**: bcrypt password hashing (`password_hash`), sessions store `user_id`,
  `full_name`, `role`, `district`. `app/helpers/auth.php` has `require_login()`
  and `require_role()` guards used at the top of protected pages.
- **Weather map**: `public/map.php` renders a Leaflet map; `assets/js/map.js`
  calls `public/api/weather.php`, which proxies Open-Meteo per district and
  caches results in the `weather_cache` table for 30 minutes.
- **Crop recommendation engine**: rule-based scoring in
  `app/helpers/crop_rules.php` — rainfall (40%), temperature (30%), soil match
  (20%), season (10%) — called from `app/models/Recommendation.php`.
- **Yield reporting**: `reports/yield.php` shows good/bad reason checkboxes
  pulled from the `yield_reasons` table, stores selections in
  `yield_report_reasons`.
- **Analytics**: `public/api/analytics.php` returns JSON aggregates (outcome
  split, top failure reasons, crop performance); rendered with Chart.js in
  `assets/js/charts.js`, reused by both the farmer analytics page and the
  ministry dashboard (which passes `scope=all`).
- **Role-based access**: `roles` table has farmer, association_admin,
  extension_officer, researcher, ministry, admin. Crop management and the
  ministry dashboard are gated with `require_role(['admin','ministry'])`.
- **Weather card**: `app/helpers/weather_card.php` (included on
  `dashboard.php`) is an interactive card — switch district, toggle °C/°F,
  and refresh — backed by `public/api/weather_district.php`, which caches
  results in `weather_cache` for 30 minutes per district.
- **Chatbot**: a floating widget (`assets/js/chatbot.js`, styled in
  `assets/css/style.css`) appears on every page via `footer.php`. It posts to
  `public/api/chatbot.php`, a **rule-based** assistant that matches intents
  by keyword and answers using live data from the `crops`/`districts` tables
  (crop planting windows, pests, diseases, watering; live weather by
  district; how to log plantings/yield reports). It's intentionally not an
  LLM — that keeps the app dependency- and API-key-free. If you want to swap
  in a real LLM later, replace the body of `chatbot.php` with a call to your
  provider of choice, keeping the same `{"reply": "..."}` JSON response
  shape so the front-end widget needs no changes.
- **Display settings**: a "⚙️ Display" dropdown in the navbar (every page)
  lets the user switch Light/Dark theme and toggle High Contrast mode.
  Choices are saved in `localStorage` and re-applied instantly on every page
  load (`assets/js/theme.js`, with an early inline script in `header.php` so
  there's no flash of the wrong theme). Theming uses Bootstrap 5.3's built-in
  color modes (`data-bs-theme`) so standard components (cards, tables, forms,
  navbar) restyle automatically; high contrast adds stronger borders and
  removes low-contrast gray text via a `.contrast-high` class.

## Known gaps / notes for v2.0

- Session hijacking protection is not implemented (documented as a known
  limitation, matching the original security test plan).
- The crop rule engine uses a live weather reading as a proxy for seasonal
  rainfall — a production version should use historical/forecast rainfall
  aggregates rather than the instantaneous reading.
- `public/api/recommend.php` is available as an optional AJAX endpoint if you
  want to turn `recommend.php` into a no-reload experience later.

## Troubleshooting

| Issue | Fix |
|-------|-----|
| "Database connection failed" | Make sure MySQL is running in XAMPP and the database/tables exist |
| 404 Not Found | URL must include `/public/` |
| Blank white page | `display_errors` is already on in `db.php`; check the Apache error log |
| Map has no markers | Check your internet connection — Leaflet tiles and Open-Meteo both need it |
| Charts are empty | Import `seed_demo.sql`, or submit a yield report yourself |
