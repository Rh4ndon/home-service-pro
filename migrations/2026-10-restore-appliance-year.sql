-- 2026-10: Restore the optional clientappliances.Year column.
-- The year is useful context for repairs but is not required when adding an
-- appliance, so the column is nullable.

SET @year_exists = (
  SELECT COUNT(*)
  FROM information_schema.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'clientappliances'
    AND COLUMN_NAME = 'Year'
);

SET @year_sql = IF(
  @year_exists = 0,
  'ALTER TABLE `clientappliances` ADD COLUMN `Year` int(11) DEFAULT NULL AFTER `Make`',
  'SELECT 1'
);

PREPARE year_stmt FROM @year_sql;
EXECUTE year_stmt;
DEALLOCATE PREPARE year_stmt;
