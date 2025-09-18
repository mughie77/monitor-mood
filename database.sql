-- Mood Tracker Database Schema
-- Version 1.0

-- Drop tables if they exist to start fresh
DROP TABLE IF EXISTS `mood_records`;
DROP TABLE IF EXISTS `students`;
DROP TABLE IF EXISTS `teachers`;
DROP TABLE IF EXISTS `users`;

--
-- Table structure for table `users`
--
CREATE TABLE `users` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `email` VARCHAR(100) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'teacher', 'student') NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `teachers`
--
CREATE TABLE `teachers` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `students`
--
CREATE TABLE `students` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `full_name` VARCHAR(255) NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for table `mood_records`
--
CREATE TABLE `mood_records` (
  `id` INT PRIMARY KEY AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `mood_value` INT NOT NULL COMMENT 'e.g., 1=Sad, 2=Neutral, 3=Happy, 4=Excited, 5=Great',
  `record_date` DATE NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY `user_daily_mood` (`user_id`, `record_date`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Seeding data for testing
-- Note: Passwords are 'password' and should be hashed with password_hash() in PHP.
-- The hash below is for 'password'.
-- <?php echo password_hash('password', PASSWORD_DEFAULT); ?>
--

INSERT INTO `users` (`username`, `email`, `password_hash`, `role`) VALUES
('admin', 'admin@app.com', '$2y$10$N.o.e.0N7s46/2.d.Hl8B.sN1GgwgZl1uS.A/j6Y.l/E.a/9.l.W', 'admin'),
('teacher1', 'teacher1@app.com', '$2y$10$N.o.e.0N7s46/2.d.Hl8B.sN1GgwgZl1uS.A/j6Y.l/E.a/9.l.W', 'teacher'),
('student1', 'student1@app.com', '$2y$10$N.o.e.0N7s46/2.d.Hl8B.sN1GgwgZl1uS.A/j6Y.l/E.a/9.l.W', 'student');

INSERT INTO `teachers` (`user_id`, `full_name`) VALUES
(2, 'Mr. John Doe');

INSERT INTO `students` (`user_id`, `full_name`) VALUES
(3, 'Jane Smith');

-- Sample mood records for charting
INSERT INTO `mood_records` (`user_id`, `mood_value`, `record_date`) VALUES
(2, 4, CURDATE() - INTERVAL 2 DAY),
(3, 3, CURDATE() - INTERVAL 2 DAY),
(2, 5, CURDATE() - INTERVAL 1 DAY),
(3, 4, CURDATE() - INTERVAL 1 DAY),
(3, 5, CURDATE());
