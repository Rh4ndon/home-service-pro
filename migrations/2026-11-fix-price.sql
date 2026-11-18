-- 2026-11: Repairman fixed service price.
-- Repairmen can set a base "fix price" on their profile. It is shown to
-- customers and pre-filled as an item when an estimate is prepared, so it is
-- included in the estimated total.

ALTER TABLE `user_repairmanprofile`
  ADD COLUMN `Fix_Price` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `Ratings`;

ALTER TABLE `repair_estimates`
  ADD COLUMN `Fix_Price` decimal(10,2) NOT NULL DEFAULT 0.00 AFTER `Appliance_Name`;
