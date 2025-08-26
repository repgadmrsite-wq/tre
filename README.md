# KISHUP Rental Platform

This repository contains the PHP source for the KISHUP motorcycle rental service.
It provides separate panels for riders and administrators and exposes APIs for
reviews, tickets and more.

## Requirements
- PHP 8.0+
- MySQL 5.7+/MariaDB
- Node.js (optional, for running front-end checks)

## Setup
1. **Install dependencies**
   ```bash
   cp .env.example .env
   ```
   Populate the variables in `.env`:
   - `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
   - `SMTP_HOST`, `SMTP_PORT`, `SMTP_USER`, `SMTP_PASS`, `SMTP_FROM`
   - `TWILIO_SID`, `TWILIO_TOKEN`, `TWILIO_FROM`

2. **Import the database**
   Create the database defined in `DB_NAME` and import `database.sql`:
   ```bash
   mysql -u your_user -p your_db < database.sql
   ```

3. **Serve the application**
   ```bash
   php -S localhost:8000
   ```
   Then visit `http://localhost:8000` in your browser.

4. **Optional password migration**
   If upgrading from MD5 hashes, run `migrate_passwords.php` once after
   configuring the environment.

## Uploads
Ensure the `uploads/` directory is writable by the web server for storing
motorcycle images.

## Testing
Basic syntax checks:
```bash
php -l login.php
```
