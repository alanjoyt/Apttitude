# Production Database Export Checklist (phpMyAdmin)

This 1-page guide is for the repository owner / administrator to export the production schema and question bank from InfinityFree phpMyAdmin. This ensures local staging tests match the exact production column structure and question pool counts.

---

## Step 1: Export Production Schema (Structure Only)

1. Log in to the **InfinityFree Control Panel (cPanel)**.
2. Under the **Databases** section, click **phpMyAdmin** and select your active production database (e.g., `if0_..._apttitudee`).
3. Click the **Export** tab in the top navigation bar.
4. Select export method: **Custom - display all possible options**.
5. **Tables:** Click **Select All**.
6. **Output:** 
   - File name template: `production_schema`
   - Character set: `utf-8`
   - Compression: `None`
7. **Format-specific options:**
   - Under **Export type**, select **Structure** (uncheck *Data*).
   - Check **Add DROP TABLE / VIEW / PROCEDURE / FUNCTION / EVENT**.
   - Check **Add CREATE TABLE / VIEW / ... statement** and check **IF NOT EXISTS**.
   - Check **AUTO_INCREMENT** values.
   - Enclose table and field names with backquotes.
8. Click **Export** at the bottom to download `production_schema.sql`.

---

## Step 2: Export Question Bank & Unlockables (Data Only)

1. Return to phpMyAdmin with the same production database selected.
2. Click the **Export** tab again.
3. Select **Custom - display all possible options**.
4. **Tables:** Uncheck all, then select ONLY:
   - `questions`
   - `unlockables`
   - `topics`
   - `subtopics`
   *(Do NOT export `users`, `coin_transactions`, or `attempts` to keep student personal data confidential).*
5. **Format-specific options:**
   - Under **Export type**, select **Data only** (uncheck *Structure*).
   - Syntax: **INSERT** (or **REPLACE**).
6. Click **Export** to download `production_questions_data.sql`.

---

## Step 3: Run the Automated Schema Parity Diff

Place `production_schema.sql` into the project root or pass its file path to the diff tool:

```bash
php deploy/scripts/diff_production_schema.php production_schema.sql
```

The script parses table names, columns, data types, nullability, and primary/foreign keys, and compares them against `deploy/schema_expected.sql`. It will output:
- **Missing Tables** in production
- **Missing Columns** in production
- **Type or Default Discrepancies**
- **Foreign Key / Index Discrepancies**

---

## Step 4: Deploying Phase 2 Migrations (In Order)

Once the schema parity diff is confirmed, apply migrations **01 through 07 in exact sequence**:

1. `deploy/migrations/01_schema_extensions_and_tables.sql`
2. `deploy/migrations/02_company_practice_patterns_and_unlockables.sql`
3. `deploy/migrations/03_full_company_questions_import.sql`
4. `deploy/migrations/04_configurable_mock_lengths.sql`
5. `deploy/migrations/05_seven_day_fix_plan.sql`
6. `deploy/migrations/06_college_leaderboards.sql`
7. `deploy/migrations/07_college_stats_and_kerala_colleges.sql`
