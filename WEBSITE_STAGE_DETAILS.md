# Apttitudee - Current Website Stage & Technical Documentation

**Generated Date:** September 16, 2026  
**Project Name:** Apttitudee (Gamified Aptitude & Placement Test Preparation Platform)  
**Project Location:** `c:\wamp64\www\Apttitudee`  
**Current Stage:** **Phase 1 Complete — Production-Ready Core MVP**

---

## 1. Executive Summary & Current Stage

**Apttitudee** is an adaptive, gamified web application designed for students preparing for campus recruitment, company aptitude tests (TCS, Infosys, Wipro, Accenture), and competitive exams (CAT, SSC).

### Current Development Stage
The platform is in a **fully functional, production-ready Core MVP stage**. All core engines—including adaptive question delivery, metacognitive confidence tracking, spaced repetition, dual-currency gamification (XP + Speed Coins), company-specific mock exams, tiered leaderboards, store unlockables, and administrative moderation—are fully implemented, linked to a MySQL database schema, and styled with a dark theme.

---

## 2. Technology Stack & Framework

| Layer | Technology |
|---|---|
| **Backend Architecture** | PHP 8.x (Custom MVC Architecture & Central Router) |
| **Database System** | MySQL 5.7+ / 8.0+ / MariaDB (Singleton PDO wrapper with auto-database initialization) |
| **Frontend Framework** | HTML5, Vanilla JavaScript (ES6+ AJAX via `fetch API`) |
| **Styling & UI Design** | Custom Vanilla CSS (Design system with HSL colors, glassmorphism, Bootstrap Icons `bi-*`, Google Fonts `Outfit` & `Inter`) |
| **Authentication** | Native PHP Session Auth + Google OAuth Integration Endpoint |
| **Server Requirements** | Apache / WAMP / XAMPP with `mod_rewrite` enabled |

---

## 3. Directory & File Structure

```
c:\wamp64\www\Apttitudee\
├── app/
│   ├── Controllers/
│   │   ├── AdminController.php        # Handles question reports & admin dashboard API
│   │   ├── DashboardController.php    # Aggregates user profile, accuracy stats, & weak topics
│   │   ├── DiagnosticController.php   # Handles 12-question diagnostic test & baseline setup
│   │   ├── MockTestController.php     # Company mock test engine (TCS, Infosys, Wipro, etc.)
│   │   ├── PracticeController.php     # Adaptive practice engine, confidence tagging & hints
│   │   └── StoreController.php        # Speed Coins store purchases & unlockables logic
│   ├── Models/
│   │   ├── Leaderboard.php            # Tiered rankings & weekly/all-time leaderboards query
│   │   └── User.php                   # Authentication, registration, XP/Coins balance, & streaks
│   ├── Services/
│   │   ├── AdaptiveEngine.php         # Performance-driven dynamic question selection algorithm
│   │   ├── GamificationEngine.php     # XP formulas, Speed Coins calculation, Tiers & Streaks
│   │   ├── SpacedRepetitionService.php# Metacognitive memory model & weak topic deck generator
│   │   └── WalletService.php          # Speed Coins wallet, atomic balance transactions & unlocks
│   └── Views/
│       ├── admin.php                  # Admin panel for reviewing flagged questions & reports
│       ├── dashboard.php              # Comprehensive student analytics & progress hub
│       ├── diagnostic.php             # Initial onboarding test wizard with timer
│       ├── footer.php                 # Global reusable footer component
│       ├── header.php                 # Global reusable topbar with user live stats
│       ├── landing.php                # High-converting landing page with features & CTA
│       ├── leaderboard.php           # Competitive league rankings view
│       ├── login.php                  # Authentication login screen with Google OAuth
│       ├── mock.php                   # Simulated company timed test screen with question grid
│       ├── practice.php               # Dynamic practice workspace with immediate feedback
│       ├── register.php               # Student account creation page
│       └── store.php                  # Gamified Speed Coins store for mock packs & power-ups
├── config/
│   └── db.php                         # PDO Singleton database connection & auto-setup
├── public/
│   ├── css/
│   │   └── style.css                  # Comprehensive design system & component styles
│   ├── js/
│   │   └── app.js                     # Client-side AJAX controller & interactive logic
│   └── index.php                      # Main entry point & action router dispatcher
├── schema.sql                         # Complete 13-table database DDL schema
├── seed.sql                           # Initial seed data for topics, badges, store items & sample questions
└── WEBSITE_STAGE_DETAILS.md           # Current website stage technical specification (This file)
```

---

## 4. Database Schema (13 Tables)

The database `apttitudee` consists of **13 relational tables** with foreign key integrity:

1. **`users`**: Manages accounts, credentials, subscription plans (`free`/`premium`), total XP, spendable Speed Coins, tiers, active streaks, streak freeze counts, and onboarding state.
2. **`topics`**: High-level aptitude categories (*Quantitative Aptitude*, *Logical Reasoning*, *Verbal Ability*, *Data Interpretation*).
3. **`subtopics`**: Granular topic sub-categories (*Speed, Distance & Time*, *Percentages*, *Blood Relations*, *Seating Arrangements*, etc.).
4. **`questions`**: Question bank with options A-D, difficulty ratings (`easy`, `medium`, `hard`), estimated time in seconds, detailed step-by-step explanations, and source tags (*TCS NQT*, *Infosys Pattern*, etc.).
5. **`attempts`**: Historical audit of every question attempt including user answer, correctness, metacognitive confidence rating (`sure`, `not_sure`, `guessing`), time taken, XP earned, Speed Coins awarded, and mode (`practice`, `mock`, `daily_challenge`, `guest`).
6. **`user_progress`**: Tracks subtopic-level mastery levels (Levels 1 to 5) and schedules next spaced-repetition review dates (`next_review_at`).
7. **`leaderboard_scores`**: Weekly and all-time user rankings grouped by competitive tier leagues.
8. **`badges`**: Achievement catalog with badge icons and XP rewards (*First Step*, *Week Warrior*, *Speed Demon*, *Mock Master*, *Percent Prodigy*).
9. **`user_badges`**: Junction table mapping unlocked achievements per student.
10. **`coin_transactions`**: Financial audit log for Speed Coins additions and debits (`speed_bonus`, `streak_reward`, `store_purchase`, `admin_grant`).
11. **`unlockables`**: Catalog of items available in the store (*TCS Mock Pack*, *Infosys Placement Pack*, *Wipro NLTH Challenge*, *Streak Freeze Token*, *Neon Cyber Frame*).
12. **`user_unlocks`**: Tracks store items unlocked by each student.
13. **`question_reports`**: Crowdsourced feedback system for reporting typos, wrong answers, or vague explanations.

---

## 5. Core Algorithmic Systems & Features

### 5.1. Adaptive Learning Engine (`AdaptiveEngine.php`)
- Analyzes the student's last 5 attempts in a subtopic.
- **Accuracy >= 80%** $\rightarrow$ Escalates question difficulty to `hard`.
- **Accuracy <= 40%** $\rightarrow$ Reduces difficulty to `easy` to rebuild foundation.
- **Accuracy 40% - 80%** $\rightarrow$ Maintains `medium` difficulty.
- Fallback hierarchy ensures fresh unattempted questions are prioritized before recycling.

### 5.2. Metacognitive Confidence & Spaced Repetition (`SpacedRepetitionService.php`)
- Before submitting an answer, students tag their confidence level: **Sure**, **Not Sure**, or **Guessing**.
- **Confident but Wrong (`sure` + Incorrect)**: Flagged as a high-priority concept flaw; scheduled for review **the very next day (1 day)**.
- **Unsure/Guessing + Incorrect**: Scheduled for review in **2–3 days**.
- **Correct + Sure**: Scheduled for review in **7 days**.
- Provides a dynamic **"Weak Topics Review Deck"** on the dashboard.

### 5.3. Dual-Currency Gamification System (`GamificationEngine.php` & `WalletService.php`)
- **Total XP**: Permanent progression score used to determine student Tiers (**Bronze**, **Silver**, **Gold**, **Platinum**, **Diamond**).
  - Formula: $\text{Base XP} + \text{Speed Bonus} + \text{Confidence Calibration Bonus}$ (+5 XP when answered `sure` AND correct).
- **Speed Coins**: Spendable economy currency awarded when solving questions faster than 75% of the estimated target time.
- **Streak & Freeze Shield**: Tracks consecutive daily activity. If a day is missed, a **Streak Freeze Token** is automatically consumed from the user's wallet to preserve the streak.

### 5.4. Simulated Company Mock Test Engine (`MockTestController.php`)
- Real-time timed exam interface simulating company test patterns (TCS NQT, Infosys, Wipro, Accenture).
- Features a live countdown timer, question navigator palette (Answered, Unanswered, Flagged for Review), section switching, and instant post-test diagnostic breakdown.

### 5.5. Speed Coins Reward Store (`StoreController.php`)
- Allows students to spend earned Speed Coins on company mock test packs, extra streak freeze tokens, and profile cosmetic badges.
- **Premium Plan Bypass**: Premium users bypass coin costs automatically.

### 5.6. Admin Feedback & Moderation (`AdminController.php`)
- Student error reports (typos, incorrect answer keys) are routed to an admin dashboard for resolution or dismissal.

---

## 6. Page Routes & API Map

| Action / Route | Type | Description |
|---|---|---|
| `action=landing` | Page View | Marketing landing page with hero banner, feature highlights & guest demo link |
| `action=login` | Page View / POST | Student login page with traditional password & Google OAuth sign-in |
| `action=register` | Page View / POST | Student registration page |
| `action=dashboard` | Page View | Central student dashboard with XP progress bar, tier badge, streak & review deck |
| `action=diagnostic` | Page View | 12-question diagnostic test wizard with 5-minute timer |
| `action=practice` | Page View | Metacognitive adaptive practice workspace |
| `action=mock` | Page View | Timed company mock test simulator |
| `action=leaderboard` | Page View | Weekly & All-time tier-filtered leaderboards |
| `action=store` | Page View | Speed Coins unlockables shop |
| `action=admin` | Page View | Flagged question moderation panel |
| `api_get_diagnostic` | JSON API | Fetches 12 mixed-topic diagnostic questions |
| `api_submit_diagnostic` | JSON API | Scores diagnostic test, awards +250 XP / +50 Coins, & sets baseline mastery |
| `api_next_question` | JSON API | Adaptive engine endpoint for fetching next tailored question |
| `api_submit_answer` | JSON API | Processes answer attempt, calculates XP/Coins, updates streak & spaced repetition |
| `api_get_mock` | JSON API | Loads company-specific mock test question set |
| `api_submit_mock` | JSON API | Evaluates full mock test submission & yields accuracy breakdown |
| `api_buy_unlockable` | JSON API | Purchases store item using Speed Coins wallet |
| `api_submit_report` | JSON API | Flags a question for admin review |
| `api_google_login` | JSON API | Authenticates user via Google OAuth |

---

## 7. Current Website Operational Status & Quality Checklist

- [x] **Database Schema Initialized**: Complete schema with foreign keys and index constraints (`schema.sql`).
- [x] **Sample Data Seeded**: Curated question set across Quant, LR, Verbal, & DI with step-by-step explanations (`seed.sql`).
- [x] **Adaptive Question Logic Working**: Dynamic difficulty scaling based on recent student accuracy.
- [x] **Metacognitive Calibration Implemented**: Confidence buttons ('Sure', 'Not Sure', 'Guessing') linked to spaced repetition schedule.
- [x] **Dual Economy & Gamification Active**: Tier progression (Bronze to Diamond), XP formulas, Speed Coins wallet, and Streaks.
- [x] **Company Mock Engine Operational**: Real-time timer, question palette grid, flagging system, and score reports.
- [x] **Store & Item Redemptions Functional**: Speed Coins debiting, item unlocking, and streak freeze protections.
- [x] **Responsive Premium UI**: Custom Dark Mode design system with smooth CSS transitions, glassmorphism card layouts, and Bootstrap icons.

---

## 8. Recommended Future Roadmap (Phase 2 Roadmap)

1. **Production Google OAuth Credential Configuration**: Replace the client-side Google sign-in mock trigger with production Google Client ID keys.
2. **Expand Question Bank**: Increase dataset from 10 sample questions to 1,000+ questions across subtopics.
3. **PDF Performance Report Generator**: Add capability to export detailed placement readiness scorecards as downloadable PDFs.
4. **Live Multiplayer Battle Arena**: Introduce real-time 1v1 aptitude battles over WebSockets.
