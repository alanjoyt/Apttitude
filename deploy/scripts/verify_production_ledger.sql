-- ==============================================================================
-- Script: verify_production_ledger.sql
-- Description: Read-only audit query to detect wallet vs. ledger discrepancies
-- Target Database: MySQL 5.7+ / 8.0+ (InfinityFree / Production)
-- Usage: Run in phpMyAdmin (SQL tab) before and after deployment
-- ==============================================================================

-- 1. Summary of Overall Economy Health
SELECT 
    COUNT(DISTINCT u.id) AS total_users,
    SUM(u.coins) AS total_wallet_coins,
    (SELECT COALESCE(SUM(amount), 0) FROM coin_transactions) AS total_ledger_coins,
    (SUM(u.coins) - (SELECT COALESCE(SUM(amount), 0) FROM coin_transactions)) AS total_variance
FROM users u;

-- 2. Detailed List of Any Mismatched Accounts
-- (If this returns 0 rows, the economy is in 100% mathematical parity)
SELECT 
    u.id AS user_id,
    u.name,
    u.email,
    u.coins AS wallet_balance,
    COALESCE(SUM(ct.amount), 0) AS ledger_sum,
    (u.coins - COALESCE(SUM(ct.amount), 0)) AS discrepancy
FROM users u
LEFT JOIN coin_transactions ct ON u.id = ct.user_id
GROUP BY u.id, u.name, u.email, u.coins
HAVING discrepancy != 0
ORDER BY ABS(discrepancy) DESC;
