# Apttitudee - Current Website Stage & Technical Documentation

**Generated Date:** October 31, 2026  
**Project Name:** Apttitudee (Gamified Placement Preparation & Technical Assessment Platform)  
**Project Location:** `c:\wamp64\www\Apttitudee`  
**Current Stage:** **Phase 3 Production Stage — Gamified Technical & Placement Assessment Platform**  
**Complete Technical Architecture:** See [`CURRENT_STAGE_DOCUMENTATION.md`](file:///c:/wamp64/www/Apttitudee/CURRENT_STAGE_DOCUMENTATION.md) for full architectural specifications, schema definitions, and recent updates.

---

## 1. Executive Summary & Current Stage

**Apttitudee** is an adaptive, gamified placement preparation and technical assessment web application designed for engineering students and job aspirants preparing for campus recruitment drives (TCS, Infosys, Wipro, Accenture, Amazon, Innovature Labs) and technical software engineering interviews.

### Current Development Stage
The platform is in **Phase 3 Production Stage**:
- **Question Bank:** 3,306 curated questions (100% verified & published, 0 duplicate options) across 6 major topics and 27 subtopics.
- **Company Placement Packs:** 21 company-specific mock exam packages in the Speed Coins store (Amazon, Zoho, Deloitte, Capgemini, Cognizant, Tech Mahindra, IBM, LTI Mindtree, UST, HCL, Mphasis, Hexaware, Persistent Systems, Virtusa, Oracle, Coforge, TCS, Infosys, Wipro, Accenture, Innovature Labs).
- **Curriculum Breadth:** Comprehensive coverage spanning Quantitative Aptitude, Logical Reasoning, Verbal Ability, Data Interpretation, 8+ Technical Languages (C, C++, Java, Python, TypeScript, SQL, PHP, C#, DSA), and modern AI tracks (**Large Language Models & RAG**, **Artificial Intelligence & Generative AI**).
- **Core Engines:**
  - **Adaptive Engine:** Sub-5ms response time with in-memory candidate filtering and dynamic difficulty adjustment.
  - **Dual-Currency Economy:** Experience Points (XP) and spendable Speed Coins, with 7-day streaks, streak freezes, and tiered competitive leagues (Bronze to Diamond).
  - **1v1 Quiz Duel Arena:** 10-question rapid fire duels with coin wager staking, pot rake settlement, and AI practice bot fallback.
  - **Lifelines:** "Eliminate 1 Wrong Option" for 5 Speed Coins with real-time wallet debiting.
  - **Company PYQ Hub:** Recruiter-specific placement blueprints, test patterns, and authentic company questions.
  - **Exam Hall UI:** Tactile paper-textured design system, monospace code formatting, LaTeX math rendering, and mobile-friendly responsive layouts.
  - **PWA Capabilities:** Offline shell, service worker asset caching, and web app manifest.

---

## 2. Platform Summary Table

| Layer | Implementation |
|---|---|
| **Backend Architecture** | Native PHP 8.x MVC structure (no heavyweight framework overhead; execution < 10ms) |
| **Database System** | MySQL 8.0+ / MariaDB (32 relational tables, InnoDB, foreign key constraints, `FOR UPDATE` transaction locks) |
| **Frontend Architecture** | Vanilla ES6+ JavaScript with unified `QuizShell` component architecture |
| **Design System & Theme** | Custom Exam Hall paper theme (light/dark mode, HSL CSS variables, Bootstrap 5 modals, Bootstrap Icons) |
| **PWA & Offline** | Service Worker (`public/sw.js`), Web App Manifest (`public/manifest.json`), Offline shell (`public/offline.html`) |
| **Deployment Target** | InfinityFree Cloud Hosting (`http://apttitudee.rf.gd`) via `apttitudee_update.zip` |

---

For the comprehensive technical specification, complete database schema, directory maps, and operational guides, please refer to:
👉 **[`CURRENT_STAGE_DOCUMENTATION.md`](file:///c:/wamp64/www/Apttitudee/CURRENT_STAGE_DOCUMENTATION.md)**
