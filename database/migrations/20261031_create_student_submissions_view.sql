-- Migration: Create view student_submissions as alias to student_question_submissions
CREATE OR REPLACE VIEW `student_submissions` AS 
SELECT * FROM `student_question_submissions`;
