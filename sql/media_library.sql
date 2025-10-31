-- ================================================
-- PYRAMEDIA - Media Library Table
-- Add this to your database for image tracking
-- ================================================

CREATE TABLE IF NOT EXISTS `media_library` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `filename` VARCHAR(255) NOT NULL,
  `original_name` VARCHAR(255) NOT NULL,
  `mime_type` VARCHAR(100) NOT NULL,
  `file_size` INT NOT NULL,
  `width` INT NULL,
  `height` INT NULL,
  `alt_text` VARCHAR(255) NULL,
  `caption` TEXT NULL,
  `uploaded_by` INT NOT NULL,
  `created_at` DATETIME NOT NULL,
  INDEX `idx_uploaded_by` (`uploaded_by`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
