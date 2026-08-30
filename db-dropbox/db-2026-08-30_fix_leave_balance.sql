-- db-2026-08-30_fix_leave_balance.sql
-- Fix: adjust remaining leave balance for employee 42.
UPDATE employee_leave_balances
   SET balance_days = 5
 WHERE employee_id = 42;
