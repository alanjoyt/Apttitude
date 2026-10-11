-- Rollback Migration: Revert Company Placement PYQs topic and restore Innovature subtopic to topic_id=5 (Technical Assessment)
-- Date: 2026-10-10

START TRANSACTION;

-- 1. Restore Subtopic 22 to Topic 5 (Technical Assessment)
UPDATE subtopics 
SET topic_id = 5 
WHERE id = 22;

-- 2. Restore questions is_company_pyq and company_name flags to default
UPDATE questions 
SET is_company_pyq = 0, 
    company_name = NULL 
WHERE subtopic_id = 22;

-- 3. Remove Topic 6 if it has no remaining subtopics
DELETE FROM topics 
WHERE id = 6 
  AND code = 'COMPANY'
  AND NOT EXISTS (SELECT 1 FROM subtopics WHERE topic_id = 6);

COMMIT;
