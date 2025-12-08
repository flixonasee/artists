# Artist Index PHP Backend

PHP 8 + SQLite backend for Artist Index, designed for DreamHost shared hosting without Composer.

## Structure
- `api/index.php`: Single entrypoint/router for `/api/*` requests.
- `api/auth.php`: Login + current user.
- `api/artists.php`: CRUD and Wikipedia enrichment.
- `api/photos.php`: Photo uploads (stored under `uploads/`).
- `api/works.php`: Work creation.
- `api/dashboard.php`: Totals, points, badges, recent activity.
- `lib/`: Config, DB init, JWT, helpers, gamification, Wikipedia client.
- `uploads/`: Stored uploads (kept out of git).
- `schema.sql`: SQLite schema.
- `.htaccess`: Rewrites `/api/*` to the router.

## Deployment (DreamHost shared hosting)
1. Upload the `backend-php` folder to your site root (same level as your frontend build).
2. Ensure PHP 8.1+ is enabled for the domain in DreamHost panel.
3. Confirm the web user has write access to `backend-php/data.sqlite` and `backend-php/uploads/`.
4. The first API request triggers database initialization if `data.sqlite` is missing (runs `schema.sql`, seeds two users and badges).
5. Frontend should call the API at `https://your-domain/api` with `Authorization: Bearer <token>` after login.

## Configuration
Edit `lib/config.php` to set:
- `JWT_SECRET`: change to a strong secret.
- `FRONTEND_ORIGIN`: your frontend origin for CORS (e.g., `https://index.housita.art`).
- `UPLOAD_DIR`: filesystem path for uploads.
- `DB_PATH`: SQLite file path.

## Authentication
Use POST `/api/login` with JSON `{ "email": "gianmaria@example.com", "password": "password123" }` (or `giulio@example.com`). On success you receive `{ token, user }`. Send the token via `Authorization: Bearer <token>`.

## API Overview
- `POST /api/login`
- `GET /api/artists` (auth)
- `POST /api/artists` (auth; accepts `autoFetchWikipedia`)
- `PATCH /api/artists/{id}` (auth)
- `DELETE /api/artists/{id}` (auth)
- `POST /api/artists/{id}/photos` (auth; multipart `file`, optional `caption`)
- `POST /api/artists/{id}/works` (auth; JSON `title` required)
- `GET /api/dashboard` (auth)
- `GET /api/me` (auth)

Uploads are available at `/api/uploads/<filename>` (served by router/htaccess).
