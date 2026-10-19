-- Allows clerk-submitted boundary payments to remain unapproved until an accountant reviews them.
-- Run once in phpMyAdmin if you are applying this database change manually.
-- If the Laravel migration has already run, do not run this script.

ALTER TABLE `boundary_payments`
    MODIFY COLUMN `approved_by` BIGINT UNSIGNED NULL;
