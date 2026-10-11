-- Migration: Company Placement PYQ Topic & Innovature Categorization
-- Date: 2026-10-10

START TRANSACTION;

-- 1. Insert Topic 6 for Company Placement PYQs (distinct from Core Technical Assessment)
INSERT INTO topics (id, name, code, icon, created_at) 
VALUES (6, 'Company Placement PYQs', 'COMPANY', 'bi-building-fill-check', NOW())
ON DUPLICATE KEY UPDATE name = 'Company Placement PYQs', code = 'COMPANY', icon = 'bi-building-fill-check';

-- 2. Move Subtopic 22 (Innovature Labs Technical & PYQ) under Topic 6
UPDATE subtopics 
SET topic_id = 6 
WHERE id = 22;

-- 3. Flag all questions under Subtopic 22 as company PYQs belonging to Innovature
UPDATE questions 
SET is_company_pyq = 1, 
    company_name = 'Innovature' 
WHERE subtopic_id = 22;

COMMIT;
