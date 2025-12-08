# Artist Index

Private, light-mode-only web application for Gianmaria and Giulio to manage artists, photos, and works with gamification and Wikipedia-assisted onboarding.

## Stack
- Frontend: React + Vite, Fuse.js for local fuzzy search, client-side canvas compression, inline editing UI.
- Backend: Node.js + Express + SQLite (Better-SQLite3), JWT auth, multer uploads, Wikipedia summary fetcher.
- Image storage: configurable local folder (default `backend/uploads`).

## Getting Started
1. Install dependencies
```bash
cd backend && npm install
cd ../frontend && npm install
```
2. Configure environment
```bash
cp backend/.env.example backend/.env
```
Adjust ports, JWT secret, and upload directory as needed.
3. Run migrations and seed users/badges
```bash
cd backend
npm run migrate
```
4. Start backend API
```bash
npm run dev
```
5. Start frontend
```bash
cd ../frontend
npm run dev
```

Backend defaults to `http://localhost:4000`, frontend to `http://localhost:5173`.

## Authentication
Two seeded users:
- gianmaria@example.com / password123
- giulio@example.com / password123

## API Overview
- `POST /api/login` – obtain JWT
- `GET /api/artists` – list with embedded photos/works
- `POST /api/artists` – create; auto-fetch Wikipedia bio/birth year
- `PATCH /api/artists/:id`, `DELETE /api/artists/:id`
- `POST /api/artists/:id/photos`, `POST /api/artists/:id/works`
- `GET /api/dashboard` – stats, activity, ranking
- `POST /api/upload` – upload compressed images

## Data Model
Tables: Artist, Photo, Work, User, Badge, PointsLog (see `backend/db/migrations/001_init.sql`). Tags/links are stored as JSON arrays.

## Gamification
Rules defined in `backend/src/config/gamification.json`. Actions log points and unlock badges. Dashboard aggregates totals and recent activity.

## Wikipedia Fetch
`backend/src/services/wikipediaService.js` pulls summary/birth year/thumbnail. Failures return empty fields to keep flow resilient.

## Image Handling
Frontend compresses images before upload. Backend stores under `uploads` and serves at `/uploads/*`.

## Local Search
Fuse.js index on the client provides instant fuzzy search across names and tags.

## PDF Export Stub
Hook in `backend` for future PDF export can be added using data from `GET /api/artists/:id`; no binary renderer included yet.
