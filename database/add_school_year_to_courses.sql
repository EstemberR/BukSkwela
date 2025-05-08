USE tenant_john;

-- Check if columns already exist and add them if they don't
SET @columnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'tenant_john' AND TABLE_NAME = 'courses' AND COLUMN_NAME = 'school_year_start');
SET @sql = IF(@columnExists = 0, 'ALTER TABLE courses ADD COLUMN school_year_start YEAR NULL;', 'SELECT "school_year_start column already exists" AS message;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @columnExists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = 'tenant_john' AND TABLE_NAME = 'courses' AND COLUMN_NAME = 'school_year_end');
SET @sql = IF(@columnExists = 0, 'ALTER TABLE courses ADD COLUMN school_year_end YEAR NULL;', 'SELECT "school_year_end column already exists" AS message;');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Show the new structure
DESCRIBE courses; 