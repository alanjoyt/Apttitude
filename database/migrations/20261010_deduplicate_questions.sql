-- Migration: 20261010_deduplicate_questions.sql
-- Purpose: Safely remap attempts to canonical question IDs and remove duplicate question records.
-- Note: Do NOT execute automatically. Run manually when updating production database.

-- Step 1: Remap all attempts pointing to duplicate question IDs to the canonical (lowest) question ID
UPDATE attempts a
JOIN (
    SELECT q1.id AS duplicate_id, q2.id AS canonical_id
    FROM questions q1
    JOIN questions q2 ON q1.question_text = q2.question_text AND q1.id > q2.id
    WHERE q2.id = (
        SELECT MIN(q3.id) 
        FROM questions q3 
        WHERE q3.question_text = q1.question_text
    )
) AS mapping ON a.question_id = mapping.duplicate_id
SET a.question_id = mapping.canonical_id;

-- Step 2: Delete duplicate question rows that are now unreferenced
DELETE q1 FROM questions q1
INNER JOIN questions q2 
WHERE q1.id > q2.id AND q1.question_text = q2.question_text;
