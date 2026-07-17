Here is the detailed Product Requirements Document for the **Undiksha CTF System**, organized for direct reference.

---

# Product Requirements Document: Undiksha CTF System

**Event:** CTF Bulfest

**Target Release:** End of July 2026

**Language Constraints:** PRD content in English; Frontend/UI components must be in **Bahasa Indonesia**.

## 1. Executive Summary

The Undiksha CTF System is a web-based Capture The Flag (CTF) platform designed for the Bulfest event. Given the compact timeframe, the system focuses on rapid, robust development to support 20–30 participants. The architecture prioritizes performance, security, and maintainability.

## 2. Core Mechanics

- **Team vs. Account System:** Teams of ~3 members. Each user has a unique account for audit logging, but scoring and question status are aggregated at the team level.
- **Dynamic Scoring (First-Blood):** The first team to solve a challenge earns maximum points. Subsequent correct submissions by other teams receive reduced points.
- **Penalty System:** Configurable point deductions apply for incorrect flag submissions to discourage brute-forcing.
- **Format:** Text-based flag submissions. 3–5 challenges per category.

## 3. Functional Specifications

### Admin Module (Laravel Filament)

- **Participant Management:** Create/edit/group user accounts into teams.
- **Classification Management:** CRUD operations for challenge categories (e.g., Reverse Engineering, Cryptography).
- **Challenge Management:** Rich-Text question editor; define base scores and difficulty levels.
- **Penalty Configuration:** Adjust point deduction values for incorrect flags.
- **Real-time Leaderboard:** Monitor standing and scores dynamically.
- **Audit Logging:** Detailed logs of all submissions (user, team, flag, timestamp).
- **Server Monitoring:** Native widget for CPU/RAM utilization.

### Participant Module (Laravel + Inertia.js + Svelte)

- **Question Explorer:** Filter challenges by category and difficulty.
- **Flag Submission:** Input form with real-time feedback and penalty warnings (**in Bahasa Indonesia**).
- **Dashboard:** Team score overview and challenge completion status.

## 4. Technical Architecture

- **Backend:** Laravel (for stability and speed).
- **Admin Side:** **Laravel Filament** (for rapid administrative interface development).
- **Participant Side:** **Laravel Inertia.js with Svelte** (for a reactive, high-performance SPA experience).
- **Database:** MySQL/MariaDB.
- **Deployment:** Self-hosted on client server.

## 5. Code Quality & Maintainability

To ensure the system remains maintainable after the event, the following architectural patterns and quality standards will be enforced:

- **Service Layer Pattern:** Business logic (especially scoring calculations and penalty logic) will be extracted into dedicated Service classes, keeping Controllers thin and focused only on request handling.
- **Data Validation:** Use of Laravel **FormRequest** classes to enforce strict validation rules before data reaches the application layer.
- **Type-Safety:** Full use of **TypeScript** within the Svelte components to minimize runtime errors and ensure props passed from the backend (Inertia) are handled correctly.
- **DTOs (Data Transfer Objects):** Complex data structures for challenge status and scoring will utilize DTOs to ensure consistency across the application.
- **"Execute, Don't Explain" Methodology:** Deployment and recurring tasks will be automated via **Makefiles**. This removes the need for lengthy manual documentation, making the system easy to redeploy or maintain by future developers who can simply run commands like `make setup` or `make deploy`.
- **Testing Strategy:** **Locust** will be used to run stress tests, specifically targeting the scoring logic to ensure no race conditions occur when multiple teams submit correct flags simultaneously.
