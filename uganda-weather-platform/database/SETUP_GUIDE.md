# Database Setup Guide — XAMPP / MySQL / MariaDB

`schema.sql` and `seed_demo.sql` in this folder are the only database files
you need. They work unmodified on **all three** of MySQL, MariaDB, and
XAMPP's bundled database server, because:

- Modern XAMPP ships **MariaDB** by default (not MySQL) — but MariaDB is a
  drop-in replacement that understands the same SQL used here (`CREATE
  TABLE`, `ENUM`, `FOREIGN KEY`, `AUTO_INCREMENT`, etc.).
- If you're running standalone MySQL Server or MySQL Workbench instead of
  XAMPP, the same two files import exactly the same way.

So there is only **one** schema file — you don't need separate versions per
database. Below are the three common ways to import and connect it.

---

## Option A: XAMPP + phpMyAdmin (easiest, recommended for beginners)

1. Open the **XAMPP Control Panel** and click **Start** next to both
   `Apache` and `MySQL`. Both rows should turn green.
2. Click **Admin** next to MySQL (or go to `http://localhost/phpmyadmin`).
3. Click the **SQL** tab at the top.
4. Open `database/schema.sql` in a text editor, copy all of it, paste it
   into the SQL box, and click **Go**. This creates the `uganda_agri_weather`
   database and all 16 tables, and seeds districts/crops/roles.
5. Repeat step 3–4 with `database/seed_demo.sql` to load demo accounts and
   sample data.
6. In the left sidebar, click `uganda_agri_weather` and confirm you see 16
   tables (users, farms, crops, districts, plantings, yield_reports, etc.).

## Option B: Command line (`mysql` client — works for MySQL and MariaDB)

Open a terminal (on Windows, use the XAMPP shell or Command Prompt with
`C:\xampp\mysql\bin` on your PATH):

```bash
# Import the schema (creates the database)
mysql -u root -p < database/schema.sql

# Import demo data
mysql -u root -p < database/seed_demo.sql
```

If your root user has no password (XAMPP default), just press Enter when
prompted, or add `-p""` / omit `-p` entirely:

```bash
mysql -u root < database/schema.sql
mysql -u root < database/seed_demo.sql
```

## Option C: A GUI tool (HeidiSQL, MySQL Workbench, DBeaver, TablePlus)

1. Create a new connection: host `127.0.0.1`, port `3306`, user `root`,
   password blank (XAMPP default) or whatever you set.
2. Open `database/schema.sql` as a new SQL tab/query and **execute the whole
   file**.
3. Open `database/seed_demo.sql` the same way and execute it.

---

## Connecting the PHP app to your database

The app reads its connection details from `app/config/db.php`, which uses
`getenv()` with sensible XAMPP defaults built in, so **it usually works with
zero changes**:

```php
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'uganda_agri_weather');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
```

If your MySQL/MariaDB setup differs from the XAMPP default (e.g. you set a
root password, use a different port, or run MySQL outside XAMPP), do **one**
of the following:

### 1. Edit `app/config/db.php` directly (simplest)
Change the fallback values on the right of `?:`:
```php
define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
define('DB_NAME', getenv('DB_NAME') ?: 'uganda_agri_weather');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'your_password_here');
```
If MySQL runs on a non-default port, change the PDO DSN a few lines below to
add `;port=3307` (or your port) after the host.

### 2. Use environment variables instead (cleaner, keeps secrets out of code)
Edit the `.env` file at the project root:
```
DB_HOST=localhost
DB_NAME=uganda_agri_weather
DB_USER=root
DB_PASS=your_password_here
```
Note: plain PHP does **not** read `.env` automatically — XAMPP's PHP has no
built-in dotenv loader. `.env` here is documentation of the values in one
place; to actually have PHP read it, either set the values as real OS/Apache
environment variables, or simplest, just edit `app/config/db.php` directly as
in option 1 above (recommended for local XAMPP use).

---

## Verifying the connection

Visit `http://localhost/uganda-weather-platform/public/` in your browser.

- **If you see the homepage with district/crop counts** — the connection
  works.
- **If you see "Database connection failed"** — check:
  1. Is MySQL actually running (green in XAMPP Control Panel)?
  2. Does the database `uganda_agri_weather` exist? (check in phpMyAdmin)
  3. Do the credentials in `db.php` match what MySQL actually expects?
  4. Is something else already using port 3306? (Skype, another MySQL
     instance, etc. — stop it or change XAMPP's MySQL port in
     `my.ini` and update `db.php` to match)

## Re-importing from scratch

`schema.sql` starts with `DROP DATABASE IF EXISTS uganda_agri_weather;`, so
it is always safe to re-run — it wipes and rebuilds cleanly rather than
colliding with a half-imported previous attempt. Just re-run schema.sql then
seed_demo.sql any time you want a clean slate.
