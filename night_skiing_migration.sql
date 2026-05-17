-- Migration for Night Skiing Events feature
-- Run this against your MariaDB database (u853012228_skiman)

CREATE TABLE IF NOT EXISTS `game_night_skiing_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_resort` int(11) NOT NULL,
  `date` date NOT NULL,
  `type` varchar(50) NOT NULL COMMENT 'fireworks, concert, night_race',
  `cost` int(11) NOT NULL DEFAULT 0,
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0=scheduled, 1=completed, 2=cancelled',
  `revenue_bonus` decimal(5,2) DEFAULT 0.00,
  `reputation_bonus` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_resort_date` (`id_resort`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add index to game_resort_affluence for faster lookups (optional but recommended)
-- ALTER TABLE `game_resort_affluence` ADD INDEX `idx_date` (`date`);
