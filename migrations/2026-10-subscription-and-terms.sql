-- ==========================================================
-- Migration: Terms acceptance, repairman subscriptions
-- Run once on the target database.
-- ==========================================================

-- 1. Terms & Conditions acceptance timestamp (customers + repairmen)
ALTER TABLE `users`
  ADD COLUMN `Terms_Accepted_At` DATETIME NULL DEFAULT NULL AFTER `Created_At`;

-- 2. Repairman subscription state
--    trial        = 3-day free trial that starts when the admin approves the account
--    subscribed   = paid plan active until Subscription_Ends_At
--    unsubscribed = locked (trial expired, plan expired, or revoked by admin)
ALTER TABLE `user_repairmanprofile`
  ADD COLUMN `Subscription_Status` ENUM('trial','subscribed','unsubscribed') NOT NULL DEFAULT 'unsubscribed' AFTER `Status`,
  ADD COLUMN `Trial_Ends_At` DATETIME NULL DEFAULT NULL AFTER `Subscription_Status`,
  ADD COLUMN `Subscription_Ends_At` DATETIME NULL DEFAULT NULL AFTER `Trial_Ends_At`;

-- 3. Subscription plans (prices are set by the admin)
CREATE TABLE IF NOT EXISTS `subscription_plans` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `Code` VARCHAR(20) NOT NULL,
  `Name` VARCHAR(100) NOT NULL,
  `Duration_Months` TINYINT UNSIGNED NOT NULL,
  `Price` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  `Updated_At` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Code_UNIQUE` (`Code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `subscription_plans` (`Code`, `Name`, `Duration_Months`, `Price`) VALUES
  ('monthly',   '1 Month',  1,  299.00),
  ('quarterly', '3 Months', 3,  799.00),
  ('yearly',    '1 Year',   12, 2999.00);

-- 4. Admin-uploaded payment QR codes (one per provider; file lives in uploads/qr)
CREATE TABLE IF NOT EXISTS `payment_qr_codes` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `Provider` ENUM('gcash','maya') NOT NULL,
  `File_Path` VARCHAR(255) NOT NULL,
  `Updated_At` DATETIME NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`ID`),
  UNIQUE KEY `Provider_UNIQUE` (`Provider`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Repairman proof-of-payment submissions (files live in uploads/payments)
CREATE TABLE IF NOT EXISTS `repairman_subscription_payments` (
  `ID` INT(11) NOT NULL AUTO_INCREMENT,
  `Repairman_ID` INT(11) NOT NULL,
  `Plan_ID` INT(11) NOT NULL,
  `Plan_Name` VARCHAR(100) NOT NULL,
  `Duration_Months` TINYINT UNSIGNED NOT NULL,
  `Amount` DECIMAL(10,2) NOT NULL,
  `Payment_Method` ENUM('gcash','maya') NOT NULL,
  `Reference_No` VARCHAR(100) NULL DEFAULT NULL,
  `Proof_Path` VARCHAR(255) NOT NULL,
  `Status` ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `Admin_Note` VARCHAR(255) NULL DEFAULT NULL,
  `Submitted_At` DATETIME NOT NULL DEFAULT current_timestamp(),
  `Reviewed_At` DATETIME NULL DEFAULT NULL,
  `Reviewed_By` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `Repairman_ID_IDX` (`Repairman_ID`),
  KEY `Status_IDX` (`Status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Existing approved repairmen get the 3-day trial starting from the migration time
UPDATE `user_repairmanprofile`
   SET `Subscription_Status` = 'trial',
       `Trial_Ends_At` = DATE_ADD(NOW(), INTERVAL 3 DAY)
 WHERE `Status` = 'active' AND `Trial_Ends_At` IS NULL;
