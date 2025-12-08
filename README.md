# Artist Index

## Product Overview
- **Product Type:** Private, web-based, collaborative artist cataloguing system.
- **Primary Users:** Initially two users (Gianmaria and Giulio) with optional future collaborators.
- **Purpose:** Minimal, fast, high-clarity system for collecting, editing, organising, and exploring structured artist profiles, combining manual entry with automated Wikipedia retrieval and lightweight gamification.
- **Core Value Proposition:** Reliable, elegant, Apple-inspired tool for art research and archiving with points, badges, and activity dashboards.
- **Form Factor:** Responsive web application (desktop + mobile), light mode only.

## Design Principles
- Clarity above everything (Apple-inspired UI; SF Pro/Inter typography).
- Consistency in data structure across all artists.
- Speed: instant search, inline editing, real-time updates.
- Reliability: always available, predictable behaviour.
- Low friction: intuitive actions, collapsible sections, minimal navigation.
- High-quality but compressed image handling.
- Playful professionalism: fun badges and competitive dashboards without compromising research focus.

## Primary Features
- Add/edit artist profiles with collapsible structured sections.
- Featured image upload (device, URL, drag-and-drop).
- Automatic data retrieval (birth year, short bio, Wikipedia link, images when possible).
- Notes, tags, links, photos with captions, works with metadata.
- Lightbox photo viewer with zoom and swipe.
- Instant global search across all attributes.
- Gamification with user points, configurable scoring, and badges.
- Dashboard for contributions, statistics, and unlocked badges.
- Future: export artist profile as PDF.

## Vision & Scope
### Vision Statement
Artist Index aims to be the most elegant, minimal, high-performance tool for privately cataloguing and researching artists, blending Apple-grade UX clarity with rigorous data structures and a subtle gamified layer for continuous contribution.

### Problem Statement
Art professionals and enthusiasts lack a simple, beautiful, private tool for structured artist information. Existing tools are often too corporate, public, unstructured, or complex. Artist Index offers a light, fast, curated environment for managing artist profiles, notes, works, and images.

### Product Objectives
1. Enable fast, low-friction data entry for artists, works, and notes.
2. Provide a consistent data model for clean organisation.
3. Offer automation (Wikipedia lookup) to reduce manual workload.
4. Support long-term use with stability, clarity, and strong UX patterns.
5. Include playful motivation mechanisms (points, badges, dashboards).
6. Ensure privacy through a two-user private-access model.
7. Optimise performance for fast search and smooth navigation.
8. Maintain high-quality compressed images suitable for artwork reference.

### Scope Overview
In scope: structured artist creation/editing, instant search, image management, works management, points system, badges, dashboards, Wikipedia ingestion, light-mode responsive UI. Out of scope for v1: public sharing, multi-user beyond two, social features, complex artwork metadata, mobile-native apps, multi-language UI.

## Success Criteria
- Daily/weekly engagement from both primary users.
- Growing index of artists.
- High search performance (<100ms local filtering).
- Reliable image upload and lightbox experience.
- Active participation in gamification.
- Minimal friction in adding/editing artists.
- Positive subjective assessment: “easy”, “pleasant”, “beautiful”, “useful”.

## Personas
### Primary Personas
- **Curatorial Researcher:** Structured professional use; frequent additions, heavy search, high-quality visuals.
- **Enthusiastic Collaborator:** Casual contributions; enjoys gamification; values simplicity and discoverability.

### Future Personas
- **Guest Collaborator:** Occasional contributor with limited features.
- **Research Viewer:** Read-only user. (Both out of scope for v1.)

## Key Use Cases
- **UC01 — Add a New Artist:** Enter name, auto-fetch Wikipedia data (birth year, bio, URL, images), review/adjust, add tags/notes/links/photos/works, save, earn points.
- **UC02 — Edit an Artist:** Inline edits with auto-save; collapsible state preserved.
- **UC03 — Upload Photos:** Multi-source uploads with compression; add captions; lightbox viewing.
- **UC04 — Add a Work:** Title required; optional year, image, notes; displayed in list.
- **UC05 — Instant Search:** <100ms incremental search across all fields.
- **UC06 — View Dashboard:** Points, badges, activity charts, contributions, rankings.
- **UC07 — Automatic Data Fetch:** Wikipedia lookup on new artist name with editable results.

## System Overview
- **Front-End:** Responsive web app (React/Next.js recommended), inline editing, collapsible sections, local search indexing, client-side image compression.
- **Backend API Layer:** CRUD for artists/photos/works/notes/tags/links, gamification logic, Wikipedia ingestion, activity logging.
- **Database:** Stores structured artist data, images references, users, points, badges, activity logs.
- **Image Storage:** Compressed high-quality images with accessible URLs.
- **Dashboard Metrics Service:** Aggregates activity, weekly summaries, rankings.
- **Authentication:** Two-user model with secure login.

## Core Functional Requirements (Selected)
### Artist Management
- Create with required name; optional fields default empty.
- Wikipedia auto-fetch for birth year, bio, URL, and up to three images; user can override.
- Inline edit with auto-save and preserved section state.

### Fields & Media
- Featured image via upload/URL/drag-and-drop; compress before upload; accept jpg/png/webp; 20MB pre-compression limit.
- Tags (multi-value, searchable), notes (auto-save), links (URL validation).
- Photos: multi-upload, compression, unique IDs, inline captions, lightbox (zoom/swipe/keyboard/pinch).
- Works: title required; optional year/image/notes; belongs to one artist; inline edits.

### Search
- Instant search (<100ms for 100k entries) across name, notes, links, photo captions, works, and tags with incremental filtering.

### Dashboard & Gamification
- Points per user with configurable rules and instant updates.
- Badges unlock automatically with visual popup; displayed in profile/dashboard.
- Dashboard shows totals (artists, edits, photos, works), weekly activity chart, unlocked badges, and rankings.

### PDF Export (Future)
- Export single artist profile with key fields, images, and works list.

### Authentication
- Exactly two authenticated accounts using secure login.

## Non-Functional Requirements
- Performance: search <100ms; image uploads ~<3s on typical networks.
- Scalability targets: 100k artists, 1M images, 250k works.
- Availability: 99% uptime goal.
- Reliability: autosave must not lose data.
- Usability: responsive on mobile; fully keyboard-navigable.

## UX Requirements
- Light mode only; Inter/SF Pro typography; minimal, high-clarity layout.
- Inline editing, smooth animations (<150ms), lightbox viewer.
- Persistent top bar; “Add Artist” via floating plus button.

## Data Model (Simplified)
```
Artist(id, name, birth_year, bio, featured_image_url, tags[], links[])
Photo(id, artist_id, image_url, caption)
Work(id, artist_id, title, year, image_url, notes)
User(id, name, email, password)
Badge(id, name, description, icon_url)
PointsLog(id, user_id, artist_id, action, points, timestamp)
```

## API Requirements (High-Level)
- GET /artists, POST /artists, PATCH /artists/{id}, DELETE /artists/{id}
- POST /artists/{id}/photos, POST /artists/{id}/works
- GET /dashboard
- POST /login

## Security & Edge Cases
- HTTPS, password hashing, input sanitisation.
- Wikipedia failure falls back to manual entry.
- Image too large triggers compression; duplicate artist names prompt confirmation.

## System Behaviour & Constraints
- Edits propagate instantly; search is real-time/local; imports and uploads are non-blocking.
- Light mode only; two-user model; handle thousands of images and future target of 100k artists.
- Badges unlock in real time; dashboard refreshes on load.

## Interaction Model
Add → Edit → Browse → Search → Dashboard; includes flows for artist creation, Wikipedia ingestion, editing lifecycle, image handling, and badge unlocking.
