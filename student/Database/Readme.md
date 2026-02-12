# Student Database Schema

To support the Student Portal features, the following SQL table structure is recommended.

```sql

-- Sample Data
INSERT INTO `students` (`student_id`, `first_name`, `last_name`, `email`, `password`, `course`, `year_level`, `status`) VALUES
('2023-0001', 'John', 'Doe', 'john.doe@student.sms.com', '$2y$10$trl51DfD1SGJ.iqI7.j6/eqdV7UTaWkzd4ZxNjq2Xp2SGeduhh.su', 'BS Information Technology', 3, 'Regular'),
('2023-0002', 'Jane', 'Smith', 'jane.smith@student.sms.com', '$2y$10$trl51DfD1SGJ.iqI7.j6/eqdV7UTaWkzd4ZxNjq2Xp2SGeduhh.su', 'BS Accountancy', 2, 'Regular'),
('2023-0003', 'Michael', 'Brown', 'michael.brown@student.sms.com', '$2y$10$trl51DfD1SGJ.iqI7.j6/eqdV7UTaWkzd4ZxNjq2Xp2SGeduhh.su', 'BS Civil Engineering', 1, 'Regular');
```

## Default Credentials
The password hash in the sample data corresponds to the password: **`123`**

| Student ID | Email | Password |
|------------|-------|----------|
| `2023-0001` | `john.doe@student.sms.com` | `123` |
| `2023-0002` | `jane.smith@student.sms.com` | `123` |
| `2023-0003` | `michael.brown@student.sms.com` | `123` |

## How to Update Password
To generate a new password hash (e.g., to change `123` to something else), run this PHP command:
```bash
php -r "echo password_hash('YOUR_NEW_PASSWORD', PASSWORD_DEFAULT);"
```
Copy the output (the string starting with `$2y$...`) and replace the value in the SQL `INSERT` statement above.

## How to Update Password for Existing Accounts (SQL)
If you have **already imported** the data and want to change a user's password in the database, use the `UPDATE` command:

```sql
-- Example: Change password to '123' for student 2023-0001
UPDATE `students` 
SET `password` = '$2y$10$trl51DfD1SGJ.iqI7.j6/eqdV7UTaWkzd4ZxNjq2Xp2SGeduhh.su' 
WHERE `student_id` = '2023-0001';
```
*(Replace the hash value with your newly generated hash from the PHP command above)*
