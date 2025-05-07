-- First, check and ensure the tenant database staff table has the necessary columns
ALTER TABLE `tenant_informationtechnology`.`staff` 
ADD COLUMN IF NOT EXISTS `staff_id` VARCHAR(255) NULL AFTER `id`,
ADD COLUMN IF NOT EXISTS `role` ENUM('instructor', 'admin', 'staff') NULL DEFAULT 'instructor' AFTER `email`,
ADD COLUMN IF NOT EXISTS `department_id` BIGINT UNSIGNED NULL AFTER `role`,
ADD COLUMN IF NOT EXISTS `status` ENUM('active', 'inactive') NULL DEFAULT 'active' AFTER `department_id`,
ADD COLUMN IF NOT EXISTS `remember_token` VARCHAR(100) NULL AFTER `password`;

-- Add unique constraints if needed
ALTER TABLE `tenant_informationtechnology`.`staff` 
ADD UNIQUE INDEX IF NOT EXISTS `staff_id_unique` (`staff_id` ASC);

-- Insert staff data from the main database to the tenant database
INSERT INTO `tenant_informationtechnology`.`staff` 
(`name`, `email`, `password`, `role`, `department_id`, `status`, `staff_id`, `remember_token`, `created_at`, `updated_at`)
SELECT 
    `name`, 
    `email`, 
    `password`, 
    `role`, 
    `department_id`, 
    `status`, 
    `staff_id`, 
    `remember_token`, 
    `created_at`, 
    `updated_at`
FROM 
    `bukskwela`.`staff`
WHERE 
    `tenant_id` = 'informationtechnology'
ON DUPLICATE KEY UPDATE
    `name` = VALUES(`name`),
    `email` = VALUES(`email`),
    `password` = VALUES(`password`),
    `role` = VALUES(`role`),
    `department_id` = VALUES(`department_id`),
    `status` = VALUES(`status`),
    `remember_token` = VALUES(`remember_token`),
    `updated_at` = VALUES(`updated_at`);

-- After confirming the data has been migrated successfully, delete the original records
-- DELETE FROM `bukskwela`.`staff` WHERE `tenant_id` = 'informationtechnology';
-- (Uncomment the line above after verifying the data was successfully migrated) 