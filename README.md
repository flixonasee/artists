# Artist Notebook

A lightweight PHP + MySQL notebook for cataloguing artists and artworks with image uploads and gamification.

## Quick start
1. Create a MySQL database and import `schema.sql`.
2. Set database credentials via environment variables before running PHP (e.g. `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`).
3. Start a local server from the project root:
   ```bash
   php -S 0.0.0.0:8000
   ```
4. Visit `http://localhost:8000/`.

## Admin bootstrap
Use `create_admin.php` once to seed an administrator:
```bash
php create_admin.php
```
Enter the requested details; the account will be created with role `admin`.

**There are no default credentials.** Run the bootstrap once and choose your own admin email/password (for example, `admin@example.com` with a strong password). After that, log in through the main UI. Regular users can self-register from the login screen or be created by the admin via the register form.

## File uploads
Ensure the `uploads/` directory (and its subfolders) are writable by the web server so image uploads succeed.

## Notes on hosting
GitHub Pages cannot run PHP. Deploy to a PHP-capable host (e.g., DreamHost or any LAMP stack) to use the application.
