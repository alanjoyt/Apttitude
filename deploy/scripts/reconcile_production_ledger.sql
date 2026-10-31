-- ==============================================================================
-- Script: reconcile_production_ledger.sql
-- Description: Safe, idempotent opening-balance reconciliation for mismatched users.
-- Action: Inserts an 'admin_grant' opening balance record for any user whose
--         recorded ledger sum does not match their wallet balance.
-- Target Database: MySQL 5.7+ / 8.0+ (InfinityFree / Production)
-- CAUTION: Review the output of verify_production_ledger.sql BEFORE running this script!
-- ==============================================================================

START TRANSACTION;

-- 1. Insert opening balance ledger adjustment for any accounts with discrepancies
INSERT INTO `coin_transactions` (`user_id`, `amount`, `transaction_type`, `reference_description`, `created_at`)
SELECT 
    u.id AS user_id,
    (u.coins - COALESCE(SUM(ct.amount), 0)) AS adjustment_amount,
    'admin_grant' AS transaction_type,
    'Pre-Deploy Opening Balance Reconciliation (Audit Parity)' AS reference_description,
    NOW() AS created_at
FROM `users` u
LEFT JOIN `coin_transactions` ct ON u.id = ct.user_id
GROUP BY u.id, u.coins
HAVING (u.coins - COALESCE(SUM(ct.amount), 0)) != 0;

-- 2. Post-Reconciliation Verification Check
-- MUST RETURN ZERO ROWS before executing COMMIT.
SELECT 
    u.id AS user_id,
    u.coins AS wallet_balance,
    COALESCE(SUM(ct.amount), 0) AS new_ledger_sum,
    (u.coins - COALESCE(SUM(ct.amount), 0)) AS remaining_discrepancy
FROM `users` u
LEFT JOIN `coin_transactions` ct ON u.id = ct.user_id
GROUP BY u.id, u.coins
HAVING remaining_discrepancy != 0;

-- Reviewer: If the verification query above returns 0 rows, uncomment COMMIT and run:
COMMIT;

-- If any discrepancies remain or unexpected rows appear, run:
-- ROLLBACK;
