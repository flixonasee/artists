# Artist Index – Full Technical PRD

## 1. Executive Summary

**Product Name:** Artist Index

**Product Type:** Private, web-based, collaborative artist cataloguing system.

**Primary Users:** Initially two users (Gianmaria and Giulio), with optional future support for additional collaborators.

**Purpose:** Artist Index is a minimal, fast, high-clarity system for collecting, editing, organising, and exploring structured artist profiles. It combines manual data entry with automated information retrieval (e.g., from Wikipedia) and integrates a lightweight gamification layer to promote ongoing contributions.

**Core Value Proposition:** Provide a reliable, elegant, Apple‑like tool for art‑related research, reference, and personal archiving, enhanced by optional competitive elements such as points, badges, and activity dashboards.

**Form Factor:** Responsive web application (desktop + mobile). Light mode only.

**Design Principles:**

* Clarity above everything (Apple‑inspired UI; SF Pro/Inter typography).
* Consistency in data structure across all artists.
* Speed: instant search, inline editing, real‑time updates.
* Reliability: always available, predictable behaviour.
* Low friction: intuitive actions, collapsible sections, minimal navigation.
* High‑quality but compressed image handling.
* Playful professionalism: fun badges + competitive dashboards without compromising the research‑oriented nature of the tool.

**Primary Features:**

* Add/edit artist profiles with collapsible structured sections.
* Featured image upload (device, URL, drag‑and‑drop).
* Automatic data retrieval (birth year, short bio, Wikipedia link, images when possible).
* Notes, tags, links, photos with captions, works with metadata.
* Lightbox photo viewer with zoom and swipe.
* Instant global search across all attributes.
* Gamification: user points, configurable scoring system, badges.
* Dashboard: contributions, statistics, unlocked badges.
* Export artist profile as PDF (future).

**Target Experience:** A tool that feels as light as Apple Notes but behaves as structured as a museum‑grade archive, with the charm and humour of the contemporary NYC art world.

## 2. Product Vision & Scope

### 2.1 Vision Statement

Artist Index aims to become the most elegant, minimal, and high-performance tool for privately cataloguing and researching artists. The product blends Apple-grade UX clarity with rigorous art-world data structures, enabling two users to collaboratively maintain a long-term, extensible archive of artists, images, notes, and works — with a subtle gamified layer that encourages continuous contribution.

The vision is to support both everyday artistic research and playful competition, without compromising professional usability or visual refinement. The system should feel effortless, reliable, and inspiring.

### 2.2 Problem Statement

Professionals and enthusiasts working with art (e.g., curators, advisors, researchers, collectors) often lack a simple, beautiful, private tool for collecting structured artist information. Existing tools are either:

* too corporate (CRM platforms),
* too public (social platforms),
* too unstructured (note-taking apps), or
* too complex (museum cataloguing systems).

Artist Index fills this gap by offering a light, fast, aesthetically curated environment for managing artist profiles, notes, works, and images.

### 2.3 Product Objectives

1. **Enable fast, low-friction data entry** for artists, works, and notes.
2. **Provide a consistent data model** that supports clean organisation.
3. **Offer automation** (Wikipedia lookup) to reduce manual workload.
4. **Support long-term use** with stability, clarity, and strong UX patterns.
5. **Include playful motivation mechanisms** (points, badges, dashboards).
6. **Ensure privacy** through a 2-user private-access model.
7. **Optimise performance** for fast search and smooth navigation.
8. **Maintain high-quality compressed images** suitable for artwork reference.

### 2.4 Scope Overview

The scope of Artist Index includes:

* Full creation and editing of structured artist profiles.
* Global instant search across all artist-related fields.
* Image management with high-quality compression and lightbox display.
* Works management with minimal but extensible metadata fields.
* Configurable points system for contributions.
* Badge system with unlockable milestones.
* Activity and competition dashboards.
* Automatic ingestion of basic artist data from Wikipedia.
* Light mode–only responsive interface.

The following features are intentionally **out of scope for v1**, but may be revisited:

* Public sharing of profiles.
* Multi-user beyond the initial two.
* Social features (comments, likes, visibility).
* Full mobile-native app (iOS/Android).
* Complex artwork metadata (materials, dimensions, provenance).
* Multi-language UI.

### 2.5 Success Criteria (High-Level)

The product will be considered successful if it achieves:

* Daily/weekly engagement from both primary users.
* A continuously growing index of artists.
* High search performance (<100ms local filtering).
* Reliable image upload and lightbox experience.
* Active participation in the gamification layer (badges unlocked).
* Minimal friction in adding/editing artists.
* Positive subjective assessment: “easy”, “pleasant”, “beautiful”, “useful”.

### 2.6 Constraints

* **UI constraint:** Light mode only; no dark mode.
* **User constraint:** Two-account access model.
* **Storage constraint:** Must balance high visual quality with efficient compression.
* **Technical constraint:** Web-only for v1.
* **Operational constraint:** System must handle up to ~100,000 artists (future-proofing).

### 2.7 Assumptions

* Users are comfortable with desktop and mobile web interfaces.
* Users require a visually refined product aligned with the art world.
* Users value a mix of precision (data structure) and playfulness (gamification).
* Image-heavy content is expected.

## 3. User Personas & Use Cases

### 3.1 Primary Personas

Artist Index is initially designed for two core users. These personas represent behavioural patterns, not personal identity, to support generalisation and future expansion.

#### Persona A — The Curatorial Researcher

* **Profile:** Works with art professionally or semi-professionally; needs a clear and structured tool to keep track of artists, references, works, and images.
* **Goals:**

  * Maintain a reliable, cross-referenced personal database of artists.
  * Store high-quality images and notes in a structured way.
  * Quickly retrieve artist information, works, and research notes.
* **Behaviours:**

  * Adds new artists frequently based on exhibitions, reading, or projects.
  * Uses search often.
  * Updates profiles to refine details over time.
  * Appreciates clean and controlled layouts.
* **Needs:** Consistency, clarity, speed, high-quality visuals.
* **Frustrations:** Messy tools, chaotic note apps, underpowered archives, slow interfaces.

#### Persona B — The Enthusiastic Collaborator

* **Profile:** Interested in art, research, or collecting; enjoys contributing to a shared project.
* **Goals:**

  * Add artists casually and enjoy the playful competition.
  * Explore the archive comfortably.
  * Upload images, tag artists, and contribute personal notes.
* **Behaviours:**

  * Adds/update artists based on personal discovery.
  * Engages with gamification (points and badges).
  * Browses the archive for leisure or inspiration.
* **Needs:** Simplicity, discoverability, intuitive UX.
* **Frustrations:** Overly technical tools, lack of fun, visually unappealing layouts.

### 3.2 Secondary Personas (Future Expansion)

#### Persona C — The Guest Collaborator

* Occasional contributor.
* Access to limited features.
* Can browse and comment (future versions).

#### Persona D — The Research Viewer

* Read-only mode for researchers or assistants.
* No editing or gamification.

These personas are out of scope for v1 but inform future extensibility.

### 3.3 Key Use Cases (Primary)

#### UC01 — Add a New Artist

**Trigger:** User clicks the "+" button.
**Flow:**

1. User inputs the artist name (required).
2. System fetches available data from Wikipedia:

   * Birth year (day/month if available)
   * Short bio extract
   * Wikipedia URL
   * Possible images
3. User reviews and adjusts imported fields.
4. User adds tags, notes, links, photos, works (optional).
5. Artist entry is saved.
6. User receives points for the contribution.

#### UC02 — Edit an Artist

**Trigger:** User navigates to an existing profile.
**Flow:**

1. User enters edit mode.
2. User modifies any field inline.
3. User saves changes (autosave or explicit save depending on design).
4. Points are assigned for edits.

#### UC03 — Upload Photos

**Trigger:** User presses “Add Photo”.
**Flow:**

1. User uploads one or multiple photos from device, URL, or drag-and-drop.
2. System compresses the images.
3. User adds captions.
4. Photos appear in the photo gallery.
5. Lightbox allows full-screen view.

#### UC04 — Add a Work

**Trigger:** User is in edit mode, “Add Work”.
**Flow:**

1. User enters title (required).
2. User optionally adds year, image, long-text notes.
3. Work appears under the "Works" section.

#### UC05 — Instant Search

**Trigger:** User types in the global search bar.
**Flow:**

1. System filters across all relevant fields.
2. Results appear instantly (<100ms).
3. User navigates to selected results.

#### UC06 — View Dashboard

**Trigger:** User opens the "Recap / Dashboard" area.
**Flow:**

1. System loads user statistics.
2. Shows points, badges, activity charts, contributions.
3. User sees comparative ranking.

#### UC07 — Automatic Data Fetch

**Trigger:** User adds a new artist name.
**Flow:**

1. System hits Wikipedia API.
2. Parses and extracts relevant fields.
3. Fills available fields.
4. User reviews or overrides.

### 3.4 Secondary Use Cases (Future)

* Export artist profile as PDF.
* Filter artist list by tags, birth year, nationality, presence of images.
* Multi-user access with permission levels.
* Public or semi-public sharing of artist entries.

## 4. System Overview

### 4.1 System Description

Artist Index is a private, two-user, web-based cataloguing platform consisting of:

* A **client-side application** (front‑end) responsible for user interaction, data presentation, inline editing, image viewing, and local search.
* A **backend service layer** responsible for data storage, authentication, Wikipedia data ingestion, point and badge logic, and user activity tracking.
* A **media storage subsystem** for high‑quality compressed images.
* A **lightweight analytics subsystem** supporting dashboards and user activity metrics.

The system is optimised for speed, stability, and clarity, prioritising instant interactions and predictable behaviour.

### 4.2 System Components (High-Level)

1. **Client Application (Front-End)**

   * Built as a responsive web app.
   * Handles UI, interaction patterns, collapsible sections, inline edits.
   * Implements instant search via local indexing.
   * Performs client-side image compression before upload.
   * Manages session state and caches user activity.

2. **Backend API Layer**

   * Provides endpoints for CRUD operations on artists, photos, works, notes, tags, links, user points, badges.
   * Validates input and enforces data rules.
   * Handles Wikipedia fetch and sanitisation of external data.
   * Implements the gamification engine (points + badge unlocks).
   * Logs all user activity.

3. **Database**

   * Stores structured artist data.
   * Stores references to image files.
   * Stores user accounts, sessions, points, badges, and activity logs.

4. **Image Storage**

   * Stores compressed yet high-quality images.
   * Provides URLs for front-end rendering.
   * Supports multiple image formats.

5. **Wikipedia Integration Module**

   * Fetches public artist data from Wikipedia.
   * Extracts structured birth year, short bio, and image references.
   * Normalises, sanitises, and deduplicates data.

6. **Dashboard Metrics Service**

   * Aggregates activity counts.
   * Computes weekly summaries.
   * Renders competition and ranking data.

7. **Authentication & User Session System**

   * Two-user model (User A, User B) with potential extensibility.
   * Secure login and session persistence.

### 4.3 System Constraints

* **Light Mode only** (no dark mode support).
* **Two-user-only model** (keeps architecture simple and secure).
* **High image volume:** system must handle thousands of images efficiently.
* **Scalability target:** up to 100,000 artists.
* **Mobile performance:** must remain fast even on mid-range devices.

### 4.4 Core System Responsibilities

* Guarantee fast data retrieval and instant search.
* Provide reliable long-term storage for notes, tags, photos, works.
* Offer real-time feedback during editing and data entry.
* Maintain stable collaborative use between two concurrent users.
* Handle high-resolution images without compromising performance.
* Compute gamification events without latency.
* Ensure a visually refined, Apple-inspired UX.

### 4.5 Non-Goals (Explicit Limits)

* No public sharing of profiles in v1.
* No social interaction features (comments, likes, sharing).
* No AI-generated biographies or summaries.
* No complex artwork metadata (materials, dimensions, provenance).
* No versioning system for edits.
* No native mobile app in v1.

### 4.6 System Diagram (Textual / High-Level)

```
[Front-End Web App]
       |
       | REST API Calls
       v
[Backend API Layer] -----> [Wikipedia Fetch Module]
       |
       | Database Queries
       v
[Database] <--------> [Gamification Engine]
       |
       v
[Image Storage]
       |
       v
[Dashboard Metrics Service]
```

### 4.7 Expected System Behaviour

* All edits should propagate instantly.
* Search must be real-time, local, and performant.
* Wikipedia data imports should not block UI interactions.
* Image uploads should be non-blocking and background-processed.
* Badges should unlock in real time.
* Dashboard metrics should be refreshed on page load.

## 5. Functional Requirements

### 5.1 Functional Overview

This section details every functional capability the system must support. Requirements are written in a precise, engineering-oriented manner.

---

### 5.2 Artist Management

#### FR-ART-01 — Create Artist

* User MUST be able to create a new artist entry via the “+” button.
* “Name” is the only required field at creation.
* All other fields DEFAULT to null/empty.

#### FR-ART-02 — Wikipedia Auto-Fetch

* On entering an artist name:

  * System SHALL call Wikipedia API.
  * System SHALL extract birth year (day/month if available).
  * System SHALL extract a short bio summary.
  * System SHALL extract the canonical Wikipedia page URL.
  * System SHOULD extract up to 3 candidate images.
  * User SHALL be able to accept or override imported data.

#### FR-ART-03 — Edit Artist (Inline)

* All fields MUST be editable in place.
* Edits MUST auto-save unless explicitly disabled.
* Collapsible sections MUST retain their open/closed state.

#### FR-ART-04 — Delete Artist

* Artist deletion is OPTIONAL for v1; if implemented:

  * Must require confirmation.
  * Must cascade-delete all related photos and works.

---

### 5.3 Artist Fields

#### FR-ART-FIELDS-01 — Featured Image

* Supported inputs: device upload, URL paste, drag-and-drop.
* System MUST compress images before upload.
* Accepted formats: .jpg, .png, .webp.
* Max size pre-compression: 20MB.

#### FR-ART-FIELDS-02 — Tags

* Free-form string tags.
* Multi-value.
* Searchable.
* User MUST be able to add/remove quickly.

#### FR-ART-FIELDS-03 — Notes

* Long text field.
* Supports paragraphs.
* Auto-save.

#### FR-ART-FIELDS-04 — Links

* URL list.
* System MUST validate URL format.

---

### 5.4 Photos Module

#### FR-PHOTO-01 — Upload

* Multi-upload supported.
* Inputs: local files, URL, drag-and-drop.
* System MUST compress images before storage.
* System MUST generate unique IDs for each photo.

#### FR-PHOTO-02 — Captions

* Captions MUST be editable inline.

#### FR-PHOTO-03 — Lightbox

* Lightbox MUST support:

  * zoom
  * swipe (mobile)
  * keyboard navigation (desktop)
  * pinch-to-zoom
  * high-quality display

---

### 5.5 Works Module

#### FR-WORK-01 — Create Work

* Required field: Title
* Optional fields: Year, Image, Notes
* MUST belong to exactly one artist.

#### FR-WORK-02 — View Works

* Works MUST display as a vertical list.
* Each item MUST show: title, year (if present), thumbnail.

#### FR-WORK-03 — Edit Work

* All fields MUST be editable inline.

---

### 5.6 Global Search

#### FR-SEARCH-01 — Instant Search

* MUST return results <100ms for 100k entries.
* Search MUST include:

  * artist name
  * notes
  * links
  * photo captions
  * works titles & notes
  * tags

#### FR-SEARCH-02 — Incremental Filtering

* Results MUST update on each keystroke.

---

### 5.7 Dashboard & Gamification

#### FR-DASH-01 — Points

* MUST track points per user.
* Rules MUST be configurable (future-proofing).
* Points MUST update instantly.

#### FR-DASH-02 — Badges

* MUST unlock automatically when conditions are met.
* MUST trigger a visual popup.
* MUST display in profile & dashboard.

#### FR-DASH-03 — Dashboard

* MUST show:

  * total artists added
  * total edits
  * total photos
  * total works
  * weekly activity chart
  * unlocked badges
  * ranking per user

---

### 5.8 PDF Export

#### FR-PDF-01 — Export Artist Profile

* System MUST allow export of a single artist’s profile as a PDF.
* PDF MUST include:

  * name, bio, birth year
  * featured image
  * notes
  * links
  * photos with captions
  * works list

---

### 5.9 Authentication

#### FR-AUTH-01 — Two-User Model

* System MUST support exactly two authenticated accounts.

#### FR-AUTH-02 — Login

* MUST use email + password or equivalent secure method.

---

## 6. Non-Functional Requirements

### 6.1 Performance

* Search latency MUST be <100ms for 100k artists.
* Image upload SHOULD complete <3s on average networks.

### 6.2 Scalability

* Must support:

  * 100k artists
  * 1M images
  * 250k works

### 6.3 Availability

* System SHOULD achieve 99% uptime.

### 6.4 Reliability

* Autosave MUST not lose data.

### 6.5 Usability

* Interface MUST remain responsive on mobile.
* Fully keyboard-navigable.

---

## 7. UX Requirements

### 7.1 UI Style

* Light mode only.
* Typography: Inter / SF Pro.
* Minimal, high-clarity layout.
* Collapsible sections.

### 7.2 Interactions

* Inline editing everywhere.
* Smooth animations (<150ms).
* Lightbox photo viewer.

### 7.3 Navigation

* Persistent top bar.
* "Add Artist" via floating plus button.

---

## 8. Interaction Model

### 8.1 User Flow Summary

* Add → Edit → Browse → Search → Dashboard.

### 8.2 Detailed Flows

* Artist creation flow.
* Wikipedia ingestion flow.
* Edit mode lifecycle.
* Image handling flow.
* Badge unlocking sequence.

---

## 9. System Architecture

### 9.1 Front-End

* React / Next.js recommended.
* Local search indexing.
* Client-side image compression.

### 9.2 Backend

* REST API.
* Node.js recommended.

### 9.3 Storage

* SQL/NoSQL hybrid.
* Image storage in S3-like bucket.

### 9.4 Caching

* Client cache for instant responses.
* Server cache for Wikipedia queries.

---

## 10. Data Model

### 10.1 Entities

* Artist
* Photo
* Work
* User
* Badge
* PointsLog

### 10.2 Schema (Simplified)

```
Artist(id, name, birth_year, bio, featured_image_url, tags[], links[])
Photo(id, artist_id, image_url, caption)
Work(id, artist_id, title, year, image_url, notes)
User(id, name, email, password)
Badge(id, name, description, icon_url)
PointsLog(id, user_id, artist_id, action, points, timestamp)
```

---

## 11. API Requirements

### 11.1 Endpoints

* GET /artists
* POST /artists
* PATCH /artists/{id}
* DELETE /artists/{id}
* POST /artists/{id}/photos
* POST /artists/{id}/works
* GET /dashboard
* POST /login

---

## 12. Search System Requirements

* Local fuzzy search.
* Weighted scoring (name > tags > notes > works).

---

## 13. Gamification Specification

* Points awarded based on actions.
* Badge unlock conditions.
* Ranking logic.

---

## 14. Error Handling & Edge Cases

* Wikipedia fails → fallback manual.
* Image too large → compress.
* Duplicate artist names → prompt confirmation.

---

## 15. Security Requirements

* HTTPS mandatory.
* Password hashing.
* Input sanitisation.
