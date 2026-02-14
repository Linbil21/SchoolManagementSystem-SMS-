<?php
require_once 'Database/config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS admission_applications (
        applicationId INT AUTO_INCREMENT PRIMARY KEY,
        application_no VARCHAR(20) NOT NULL UNIQUE,
        student_id INT NULL, 
        first_name VARCHAR(100) NOT NULL,
        last_name VARCHAR(100) NOT NULL,
        date_of_birth DATE NOT NULL,
        gender VARCHAR(20) NOT NULL,
        email VARCHAR(255) NOT NULL,
        phone_number VARCHAR(20) NOT NULL,
        student_type VARCHAR(50) NOT NULL,
        preferred_course_1 VARCHAR(100) NOT NULL,
        preferred_course_2 VARCHAR(100),
        last_school_attended VARCHAR(255),
        status VARCHAR(50) DEFAULT 'Pending',
        submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL
    )";

    $pdo->exec($sql);
    echo "Table admission_applications created successfully!\n";

    // Insert dummy data if empty
    $check = $pdo->query("SELECT COUNT(*) FROM admission_applications")->fetchColumn();
    if ($check == 0) {
        $dummy = [
            ['APP-2024-001', 'John', 'Doe', '2005-01-01', 'Male', 'john.doe@example.com', '09123456789', 'Incoming Freshman', 'BS Information Technology', 'BS Computer Science', 'Manila High School'],
            ['APP-2024-002', 'Jane', 'Smith', '2005-02-02', 'Female', 'jane.smith@example.com', '09987654321', 'Incoming Freshman', 'BS Computer Science', 'BS Information Technology', 'Quezon High School']
        ];

        $stmt = $pdo->prepare("INSERT INTO admission_applications (application_no, first_name, last_name, date_of_birth, gender, email, phone_number, student_type, preferred_course_1, preferred_course_2, last_school_attended) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        foreach ($dummy as $row) {
            $stmt->execute($row);
        }
        echo "Dummy data inserted successfully!\n";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
