-- ============================================================================
-- Migration 09: Anti-Inspect DevTools Tracking
-- Target: MariaDB 10.4+ / MySQL 8.0+
-- Idempotent: Tracks count of developer tool opening detections per mock session
-- ============================================================================

ALTER TABLE `mock_sessions` 
    ADD COLUMN IF NOT EXISTS `devtools_count` int(11) NOT NULL DEFAULT 0 AFTER `tab_switch_count`;
