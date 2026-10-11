-- Preview script for questions with leading bracket prefix (e.g. [Innovature Labs ...])
-- DO NOT RUN UPDATE statements without explicit user approval.

-- 1. Count of questions with leading bracket prefix:
SELECT COUNT(*) AS total_prefixed_questions
FROM questions
WHERE question_text LIKE '[%';

-- 2. Detailed listing of question IDs, tags, and text:
SELECT 
    id,
    language,
    subtopic_id,
    SUBSTRING(question_text, 1, LOCATE(']', question_text)) AS extracted_prefix,
    TRIM(SUBSTRING(question_text, LOCATE(']', question_text) + 1)) AS cleaned_text_preview
FROM questions
WHERE question_text LIKE '[%'
ORDER BY id ASC;
