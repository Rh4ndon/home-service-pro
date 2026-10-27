-- 2026-10: Remove the redundant appliance name field and add repair estimates.
-- Estimates are created when a repairman accepts a schedule and are shared with
-- the customer through chat (with a downloadable PDF invoice).

-- 1. clientappliances: Name is no longer needed. The appliance type and
--    make/brand are enough to identify an appliance. Year remains optional.
ALTER TABLE `clientappliances`
  DROP COLUMN `Name`;

-- 2. Repair estimate header (one or more per ticket; the latest one is active).
--    The actual cost lines live in repair_estimate_items so a repairman can
--    add as many Materials / Service / Price rows as needed.
CREATE TABLE IF NOT EXISTS `repair_estimates` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Ticket_ID` int(11) NOT NULL,
  `Schedule_ID` int(11) DEFAULT NULL,
  `Repairman_ID` int(11) NOT NULL,
  `Appliance_Name` varchar(255) NOT NULL DEFAULT '',
  `Created_At` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`ID`),
  KEY `idx_estimate_ticket` (`Ticket_ID`),
  KEY `idx_estimate_schedule` (`Schedule_ID`),
  KEY `idx_estimate_repairman` (`Repairman_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2b. Estimate line items.
CREATE TABLE IF NOT EXISTS `repair_estimate_items` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Estimate_ID` int(11) NOT NULL,
  `Materials` varchar(255) NOT NULL DEFAULT '',
  `Service` varchar(255) NOT NULL DEFAULT '',
  `Price` decimal(10,2) NOT NULL DEFAULT 0.00,
  `Sort_Order` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`ID`),
  KEY `idx_estimate_item_estimate` (`Estimate_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Chat messages can now carry a reference (e.g. an estimate for a ticket)
ALTER TABLE `chat_messages`
  ADD COLUMN `Message_Type` varchar(20) NOT NULL DEFAULT 'text' AFTER `Message`,
  ADD COLUMN `Reference_ID` int(11) DEFAULT NULL AFTER `Message_Type`,
  ADD KEY `idx_chat_reference` (`Reference_ID`);
