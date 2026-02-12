CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL UNIQUE,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL UNIQUE,
  `password` varchar(255) NOT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year_level` varchar(50) DEFAULT 'First Year',
  `status` enum('Regular','Irregular','Transferee') DEFAULT 'Regular',
  `profile_image` varchar(255) DEFAULT 'default.jpg',
  `created_at` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Sample Data
INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `email`, `password`, `course`, `year_level`, `status`) VALUES
('2023-0001', 'John', 'Doe', 'john.doe@student.sms.com', '$2y$10$trl51DfD1SGJ.iqI7.j6/eqdV7UTaWkzd4ZxNjq2Xp2SGeduhh.su', 'BS Information Technology', 3, 'Regular'),
('2023-0002', 'Jane', 'Smith', 'jane.smith@student.sms.com', '$2y$10$trl51DfD1SGJ.iqI7.j6/eqdV7UTaWkzd4ZxNjq2Xp2SGeduhh.su', 'BS Accountancy', 2, 'Regular'),
('2023-0003', 'Michael', 'Brown', 'michael.brown@student.sms.com', '$2y$10$trl51DfD1SGJ.iqI7.j6/eqdV7UTaWkzd4ZxNjq2Xp2SGeduhh.su', 'BS Civil Engineering', 1, 'Regular');
