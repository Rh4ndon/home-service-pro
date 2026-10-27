-- Store an optional valid ID image path on each profile.
-- Existing customers and repairmen keep NULL until they upload an ID.
ALTER TABLE `user_clientprofile`
  ADD COLUMN `Valid_ID` varchar(255) DEFAULT NULL AFTER `Email`;

ALTER TABLE `user_repairmanprofile`
  ADD COLUMN `Valid_ID` varchar(255) DEFAULT NULL AFTER `Email`;
