<?php
require_once '../Database/config.php';

$dummy_data = [
    [
        'reference_code' => 'QUE-2026-001',
        'admission_type' => 'Freshman',
        'course_id' => 1,
        'year_level' => 'First Year',
        'first_name' => 'Alice',
        'mid_name' => 'M',
        'last_name' => 'Smith',
        'gender' => 'Female',
        'birthdate' => '2005-05-15',
        'contact_number' => '09123456781',
        'email' => 'alice.smith@example.com',
        'address' => '123 Blue St, Manila',
        'id_picture' => 'Assets/image/uploads/students/dummy1.jpg',
        'guardian_first' => 'Robert',
        'guardian_middle' => 'P',
        'guardian_last' => 'Smith',
        'guardian_email' => 'robert.smith@example.com',
        'guardian_contact' => '09171234561',
        'relationship' => 'Father',
        'guardian_address' => '123 Blue St, Manila',
        'primary_school' => 'Manila Elementary',
        'primary_year' => '2017',
        'secondary_school' => 'Manila High',
        'secondary_year' => '2023',
        'status' => 'Pending Review'
    ],
    [
        'reference_code' => 'QUE-2026-002',
        'admission_type' => 'Transferee',
        'course_id' => 2,
        'year_level' => 'Second Year',
        'first_name' => 'Bob',
        'mid_name' => 'D',
        'last_name' => 'Johnson',
        'gender' => 'Male',
        'birthdate' => '2004-10-20',
        'contact_number' => '09123456782',
        'email' => 'bob.johnson@example.com',
        'address' => '456 Green St, Quezon City',
        'id_picture' => 'Assets/image/uploads/students/dummy2.jpg',
        'guardian_first' => 'Mary',
        'guardian_middle' => 'L',
        'guardian_last' => 'Johnson',
        'guardian_email' => 'mary.johnson@example.com',
        'guardian_contact' => '09171234562',
        'relationship' => 'Mother',
        'guardian_address' => '456 Green St, Quezon City',
        'primary_school' => 'QC Elementary',
        'primary_year' => '2016',
        'secondary_school' => 'QC High',
        'secondary_year' => '2022',
        'status' => 'Pending Review'
    ],
    [
        'reference_code' => 'QUE-2026-003',
        'admission_type' => 'Freshman',
        'course_id' => 3,
        'year_level' => 'First Year',
        'first_name' => 'Charlie',
        'mid_name' => 'S',
        'last_name' => 'Brown',
        'gender' => 'Male',
        'birthdate' => '2006-02-12',
        'contact_number' => '09123456783',
        'email' => 'charlie.brown@example.com',
        'address' => '789 Yellow St, Pasig',
        'id_picture' => 'Assets/image/uploads/students/dummy3.jpg',
        'guardian_first' => 'John',
        'guardian_middle' => 'K',
        'guardian_last' => 'Brown',
        'guardian_email' => 'john.brown@example.com',
        'guardian_contact' => '09171234563',
        'relationship' => 'Father',
        'guardian_address' => '789 Yellow St, Pasig',
        'primary_school' => 'Pasig Elementary',
        'primary_year' => '2018',
        'secondary_school' => 'Pasig High',
        'secondary_year' => '2024',
        'status' => 'Pending Review'
    ],
    [
        'reference_code' => 'QUE-2026-004',
        'admission_type' => 'Freshman',
        'course_id' => 4,
        'year_level' => 'First Year',
        'first_name' => 'Diana',
        'mid_name' => 'L',
        'last_name' => 'Prince',
        'gender' => 'Female',
        'birthdate' => '2005-12-25',
        'contact_number' => '09123456784',
        'email' => 'diana.prince@example.com',
        'address' => '101 Red St, Makati',
        'id_picture' => 'Assets/image/uploads/students/dummy4.jpg',
        'guardian_first' => 'Hippolyta',
        'guardian_middle' => 'A',
        'guardian_last' => 'Prince',
        'guardian_email' => 'queen.h@example.com',
        'guardian_contact' => '09171234564',
        'relationship' => 'Mother',
        'guardian_address' => '101 Red St, Makati',
        'primary_school' => 'Makati Elementary',
        'primary_year' => '2017',
        'secondary_school' => 'Makati High',
        'secondary_year' => '2023',
        'status' => 'Pending Review'
    ],
    [
        'reference_code' => 'QUE-2026-005',
        'admission_type' => 'Transferee',
        'course_id' => 5,
        'year_level' => 'Third Year',
        'first_name' => 'Ethan',
        'mid_name' => 'W',
        'last_name' => 'Hunt',
        'gender' => 'Male',
        'birthdate' => '2003-08-14',
        'contact_number' => '09123456785',
        'email' => 'ethan.hunt@example.com',
        'address' => '202 Silver St, Taguig',
        'id_picture' => 'Assets/image/uploads/students/dummy5.jpg',
        'guardian_first' => 'Julia',
        'guardian_middle' => 'M',
        'guardian_last' => 'Hunt',
        'guardian_email' => 'julia.hunt@example.com',
        'guardian_contact' => '09171234565',
        'relationship' => 'Spouse',
        'guardian_address' => '202 Silver St, Taguig',
        'primary_school' => 'Taguig Elementary',
        'primary_year' => '2015',
        'secondary_school' => 'Taguig High',
        'secondary_year' => '2021',
        'status' => 'Pending Review'
    ]
];

try {
    $pdo->beginTransaction();
    
    $stmt = $pdo->prepare("
        INSERT INTO enrollments (
            reference_code, admission_type, course_id, year_level, 
            first_name, mid_name, last_name, gender, birthdate, 
            contact_number, email, address, id_picture, 
            guardian_first, guardian_middle, guardian_last, guardian_email, 
            guardian_contact, relationship, guardian_address, 
            primary_school, primary_year, secondary_school, secondary_year, status
        ) VALUES (
            :reference_code, :admission_type, :course_id, :year_level, 
            :first_name, :mid_name, :last_name, :gender, :birthdate, 
            :contact_number, :email, :address, :id_picture, 
            :guardian_first, :guardian_middle, :guardian_last, :guardian_email, 
            :guardian_contact, :relationship, :guardian_address, 
            :primary_school, :primary_year, :secondary_school, :secondary_year, :status
        )
    ");

    foreach ($dummy_data as $data) {
        $stmt->execute($data);
    }

    $pdo->commit();
    echo "Successfully injected 5 dummy enrollment queue items.";
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "Error: " . $e->getMessage();
}
?>
