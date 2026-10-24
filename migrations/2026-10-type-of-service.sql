-- ==========================================================
-- Migration: Add Type_Of_Service column to repairticket table
-- Run once on the target database.
-- ==========================================================

-- 1. Add Type_Of_Service column to repairticket table
ALTER TABLE `repairticket`
  ADD COLUMN `Type_Of_Service` ENUM('Repair', 'Maintenance') NOT NULL DEFAULT 'Repair' AFTER `Details`;