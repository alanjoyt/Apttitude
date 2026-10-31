# Production Preservation Specification (PRESERVE.md)

This document defines all files, directories, and environmental state on the remote production server (InfinityFree / LAMP) that **MUST NEVER BE OVERWRITTEN, DELETED, OR REPLACED WHOLESALE** during deployment.

---

## 1. Remote-Only Files to Preserve

| Path / File | Purpose | Preservation Rule |
| :--- | :--- | :--- |
| `config/db.php` | Production database credentials & host definition (`sql210.infinityfree.com`) | **DO NOT OVERWRITE.** If updated, merge only functional helper methods. Never overwrite DB host, user, or pass. |
| `public/uploads/` | User avatar uploads, community question submission attachments | **DO NOT OVERWRITE OR DELETE.** Preserve all existing uploaded student assets. |
| `public/sitemap.xml` | Dynamically generated SEO sitemap cache | **PRESERVE.** Maintained by server script. |
| `.htaccess` (Server Root) | Shared hosting Apache rewrite rules and security filters | **MERGE CAREFULLY.** Ensure directory protection rules are present without breaking host proxy headers. |

---

## 2. Files That Must NOT Be Uploaded to Production

The following directories and artifacts are local development / staging tools and **MUST REMAIN OFF THE PRODUCTION SERVER**:

1. **`deploy/` Directory:**  
   The entire `deploy/` folder (including migration SQL files, audit scripts, and manifests) is strictly an operator tool for phpMyAdmin and local verification. **Do not upload `deploy/` to the remote `htdocs/`.**
2. **`scratch/` Directory:**  
   Temporary testing and verification scripts.
3. **`tests/` Directory:**  
   Playwright test suites, browser automation fixtures, and test users.
4. **`.git/` & Git metadata:**  
   Repository internals and history.
5. **`.env` / `*.sql` / `*.bak` files:**  
   Raw SQL backups and environment secret files must never be placed in the web document tree.

---

## 3. Safe Selective Upload Procedure

When deploying via FTPS (FileZilla / WinSCP):
1. **Never drag-and-drop the root directory wholesale.**
2. Upload modified files selectively by comparing file timestamps or referring to `deploy/manifest.json`.
3. In `config/`: upload `economy.php` and `app.php`. **Skip `config/db.php`** unless specifically migrating connection logic.
4. In `public/`: upload `index.php`, `css/`, `js/`, `assets/`, `manifest.json`, and `sw.js`. **Skip `public/uploads/`**.
5. In `app/`: replace updated `Controllers/`, `Models/`, `Services/`, and `Views/`.
