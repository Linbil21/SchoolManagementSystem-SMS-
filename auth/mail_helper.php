<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../PHPMailer/src/Exception.php';
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';

$last_mail_error = '';

function get_last_mail_error() {
    global $last_mail_error;
    return $last_mail_error;
}

function sendOTP($recipientEmail, $otp, $type = 'Verification', $details = null)
{
    global $last_mail_error;
    // Skip sending for dummy/test emails to avoid "Address not found" bounces
    $dummy_domains = ['@example.com', '@test.com', '@mailinator.com', '@yopmail.com'];
    foreach ($dummy_domains as $domain) {
        if (strpos($recipientEmail, $domain) !== false) {
            return true; // Simulate success
        }
    }

    $mail = new PHPMailer(true);

    try {
        // SMTP Settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'linbilcelestre31@gmail.com';
        $mail->Password = 'vzbu ldsf mwvm qnva'; // New App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        // Timeout settings
        $mail->Timeout = 10;
        $mail->SMTPKeepAlive = true; 

        // Recipients
        $mail->setFrom('linbilcelestre31@gmail.com', 'SMS Official');
        $mail->addAddress($recipientEmail);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "$type Code - SMS Official";

        $summaryHtml = "";
        if ($details) {
            $fullName = strtoupper($details['first_name'] . ' ' . ($details['middle_name'] ?? '') . ' ' . $details['last_name']);
            $course = $details['course'] ?? '---';
            $year = $details['year_level'] ?? '---';
            $contact = $details['contact_number'] ?? '---';
            $address = $details['address'] ?? '---';
            $profileImg = $details['profile_image'] ?? '';
            $domain = "https://ems.jampzdev.com/";
            
            $imgHtml = "";
            if ($profileImg) {
                $fullImgPath = $domain . str_replace('../', '', $profileImg);
                $imgHtml = "
                <div style='text-align: center; margin-bottom: 20px;'>
                    <img src='$fullImgPath' alt='Student Photo' style='width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.1);'>
                </div>";
            }

            $summaryHtml = "
            <div style='background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 25px; margin-bottom: 25px;'>
                <h3 style='color: #1e3a8a; margin-top: 0; margin-bottom: 20px; font-size: 1.1rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; text-align: center;'>Enrollment Summary</h3>
                
                $imgHtml
                
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='width: 50%; padding-bottom: 15px; vertical-align: top;'>
                            <p style='color: #64748b; font-size: 0.8rem; margin: 0; text-transform: uppercase;'>Full Name</p>
                            <p style='color: #0f172a; font-weight: 700; margin: 3px 0; font-size: 0.95rem;'>$fullName</p>
                        </td>
                        <td style='width: 50%; padding-bottom: 15px; vertical-align: top;'>
                            <p style='color: #64748b; font-size: 0.8rem; margin: 0; text-transform: uppercase;'>Course</p>
                            <p style='color: #0f172a; font-weight: 700; margin: 3px 0; font-size: 0.95rem;'>$course</p>
                        </td>
                    </tr>
                    <tr>
                        <td style='padding-bottom: 15px; vertical-align: top;'>
                            <p style='color: #64748b; font-size: 0.8rem; margin: 0; text-transform: uppercase;'>Year Level</p>
                            <p style='color: #0f172a; font-weight: 700; margin: 3px 0; font-size: 0.95rem;'>$year</p>
                        </td>
                        <td style='padding-bottom: 15px; vertical-align: top;'>
                            <p style='color: #64748b; font-size: 0.8rem; margin: 0; text-transform: uppercase;'>Contact</p>
                            <p style='color: #0f172a; font-weight: 700; margin: 3px 0; font-size: 0.95rem;'>$contact</p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan='2' style='vertical-align: top;'>
                            <p style='color: #64748b; font-size: 0.8rem; margin: 0; text-transform: uppercase;'>Address</p>
                            <p style='color: #0f172a; font-weight: 700; margin: 3px 0; font-size: 0.95rem;'>$address</p>
                        </td>
                    </tr>
                </table>
            </div>";
        }

        $mail->Body = "
        <div style='font-family: \"Poppins\", Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 30px; border: 1px solid #f1f5f9; border-radius: 20px; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);'>
            <div style='text-align: center; margin-bottom: 25px;'>
                <img src='https://ems.jampzdev.com/Assets/image/logo.png' alt='SMS Logo' style='width: 70px;'>
            </div>
            
            $summaryHtml

            <div style='background: linear-gradient(135deg, #2563eb, #1d4ed8); padding: 30px; border-radius: 12px; text-align: center; color: white;'>
                <h2 style='margin-top: 0; font-weight: 800; font-size: 1.5rem;'>Verification Code</h2>
                <p style='opacity: 0.9; margin-bottom: 25px;'>Hello! Please use the code below to verify your $type.</p>
                <div style='background: white; color: #1e40af; padding: 15px 30px; border-radius: 10px; font-weight: 800; font-size: 2.5rem; letter-spacing: 12px; display: inline-block; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);'>
                    $otp
                </div>
            </div>

            <p style='font-size: 0.85rem; color: #64748b; text-align: center; margin-top: 30px;'>
                This code will expire in <b>10 minutes</b>. If you did not request this, please ignore this email.
            </p>
            <hr style='border: 0; border-top: 1px solid #f1f5f9; margin: 30px 0;'>
            <p style='font-size: 0.75rem; color: #94a3b8; text-align: center;'>
                &copy; " . date('Y') . " SMS Official Portal. Empowering Education.<br>
                Administered by Jampz Dev
            </p>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        $last_mail_error = $e->getMessage();
        error_log("PHPMailer Error: " . $last_mail_error);
        return false;
    }
}

function sendEnrollmentEmail($recipientEmail, $details)
{
    global $last_mail_error;
    // Skip sending for dummy/test emails
    $dummy_domains = ['@example.com', '@test.com', '@mailinator.com', '@yopmail.com'];
    foreach ($dummy_domains as $domain) {
        if (strpos($recipientEmail, $domain) !== false) {
            return true;
        }
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'linbilcelestre31@gmail.com';
        $mail->Password = 'vzbu ldsf mwvm qnva';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('linbilcelestre31@gmail.com', 'SMS Official');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = "Official Enrollment Notification - SMS";

        $student_name = strtoupper($details['first_name'] . ' ' . $details['last_name']);
        $student_id = $details['student_id'];
        $course = $details['course'];
        $year = $details['year_level'];
        $ref = $details['reference_code'];

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
            <div style='background: #1e40af; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 24px;'>Official Enrollment Slip</h1>
            </div>
            <div style='padding: 30px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 10px 10px;'>
                <p>Dear <strong>$student_name</strong>,</p>
                <p>Congratulations! You have been successfully pre-enrolled in our system. Below are your official enrollment details:</p>
                
                <div style='background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <table style='width: 100%; font-size: 14px;'>
                        <tr><td style='color: #64748b; padding-bottom: 5px;'>Student ID:</td><td><strong>$student_id</strong></td></tr>
                        <tr><td style='color: #64748b; padding-bottom: 5px;'>Reference Code:</td><td><strong>$ref</strong></td></tr>
                        <tr><td style='color: #64748b; padding-bottom: 5px;'>Course:</td><td><strong>$course</strong></td></tr>
                        <tr><td style='color: #64748b; padding-bottom: 5px;'>Year Level:</td><td>$year</td></tr>
                        <tr><td style='color: #64748b; padding-bottom: 5px;'>Status:</td><td><span style='color: #16a34a; font-weight: 700;'>PRE-ENROLLED</span></td></tr>
                    </table>
                </div>

                <p>Please use your registered email and password to log in to the student portal. You will need to verify your account using the code sent in a separate email.</p>
                
                <p style='font-size: 13px; color: #64748b; margin-top: 30px;'>
                    This is an automated notification. Please do not reply to this email.
                </p>
                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                <p style='text-align: center; font-size: 12px; color: #94a3b8;'>
                    &copy; 2026 SMS Official Portal. Empowering Your Academic Journey.
                </p>
            </div>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        $last_mail_error = $e->getMessage();
        error_log("PHPMailer Error: " . $last_mail_error);
        return false;
    }
}

function sendPaymentInstructionEmail($recipientEmail, $details)
{
    global $last_mail_error;
    // Skip sending for dummy/test emails
    $dummy_domains = ['@example.com', '@test.com', '@mailinator.com', '@yopmail.com'];
    foreach ($dummy_domains as $domain) {
        if (strpos($recipientEmail, $domain) !== false) {
            return true;
        }
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'linbilcelestre31@gmail.com';
        $mail->Password = 'vzbu ldsf mwvm qnva';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('linbilcelestre31@gmail.com', 'SMS Admission Office');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = "Admission Approved: Payment Instructions - SMS";

        $student_name = strtoupper($details['first_name'] . ' ' . $details['last_name']);
        $ref = $details['reference_code'];
        $total_fee = number_format($details['total_fee'], 2);
        $balance = number_format($details['balance'] ?? $details['total_fee'], 2);
        $paid = number_format($details['paid'] ?? 0, 2);

        $assessment_row = "<tr><td style='color: #64748b; padding-bottom: 5px;'>Total Assessment:</td><td style='color: #1e293b; font-weight: 700;'>₱$total_fee</td></tr>";
        if (floatval($paid) > 0) {
            $assessment_row .= "<tr><td style='color: #64748b; padding-bottom: 5px;'>Total Settled:</td><td style='color: #16a34a; font-weight: 700;'>₱$paid</td></tr>";
            $assessment_row .= "<tr><td style='color: #64748b; padding-bottom: 5px;'>Remaining Balance:</td><td style='color: #ef4444; font-weight: 700;'>₱$balance</td></tr>";
        } else {
             $assessment_row .= "<tr><td style='color: #64748b; padding-bottom: 5px;'>Outstanding Balance:</td><td style='color: #1e293b; font-weight: 700;'>₱$balance</td></tr>";
        }

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
            <div style='background: #2563eb; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 24px;'>Admission Approved!</h1>
            </div>
            <div style='padding: 30px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 10px 10px;'>
                <p>Dear <strong>$student_name</strong>,</p>
                <p>We are pleased to inform you that your admission application has been approved by the Admission Office.</p>
                <p style='color: #ef4444; font-weight: 700;'>CRITICAL STEP: You must settle your initial payment (Downpayment) before you can be officially enrolled.</p>
                
                <div style='background: #f8fafc; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <h3 style='margin-top: 0; color: #1e293b;'>Payment Summary</h3>
                    <table style='width: 100%; font-size: 14px;'>
                        <tr><td style='color: #64748b; padding-bottom: 5px;'>Reference Code:</td><td><strong>$ref</strong></td></tr>
                        $assessment_row
                    </table>
                </div>

                <p><strong>Payment Options:</strong></p>
                <ul>
                    <li><strong>Walk-in:</strong> Visit the School Cashier and present your Reference Code.</li>
                    <li><strong>Online:</strong> You can pay via <b>Hello Money (AUB)</b>, <b>GCash</b>, or <b>Bank Transfer</b> through the student portal.</li>
                </ul>

                <p>After paying online, please upload your proof of payment in the student portal for validation.</p>
                
                <div style='text-align: center; margin-top: 30px;'>
                    <a href='https://ems.jampzdev.com/student/auth/Login.php' style='background: #2563eb; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 700;'>Go to Student Portal</a>
                </div>

                <p style='font-size: 13px; color: #64748b; margin-top: 30px;'>
                    This is an automated notification from the Admission Office.
                </p>
                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                <p style='text-align: center; font-size: 12px; color: #94a3b8;'>
                    &copy; 2026 SMS Official Portal. Admission Department.
                </p>
            </div>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        $last_mail_error = $e->getMessage();
        error_log("PHPMailer Error: " . $last_mail_error);
        return false;
    }
}

function sendPaymentReceiptEmail($recipientEmail, $details)
{
    global $last_mail_error;
    $dummy_domains = ['@example.com', '@test.com', '@mailinator.com', '@yopmail.com'];
    foreach ($dummy_domains as $domain) {
        if (strpos($recipientEmail, $domain) !== false) return true;
    }

    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'linbilcelestre31@gmail.com';
        $mail->Password = 'vzbu ldsf mwvm qnva';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;
        
        $mail->setFrom('linbilcelestre31@gmail.com', 'SMS Cashier');
        $mail->addAddress($recipientEmail);

        $mail->isHTML(true);
        $mail->Subject = "Payment Receipt: " . $details['transaction_id'] . " - SMS Official";

        $student_name = strtoupper($details['first_name'] . ' ' . $details['last_name']);
        $amount = number_format($details['amount'], 2);
        $method = $details['method'];
        $ref = $details['transaction_id'];
        $desc = $details['description'] ?? 'General Payment';
        $balance = number_format($details['new_balance'], 2);

        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; color: #333;'>
            <div style='background: #16a34a; padding: 30px; text-align: center; border-radius: 10px 10px 0 0;'>
                <h1 style='color: white; margin: 0; font-size: 24px;'>Payment Received!</h1>
            </div>
            <div style='padding: 30px; border: 1px solid #e2e8f0; border-top: none; border-radius: 0 0 10px 10px;'>
                <p>Dear <strong>$student_name</strong>,</p>
                <p>Thank you for your payment. Your transaction has been successfully processed and verified.</p>
                
                <div style='background: #f0fdf4; padding: 25px; border-radius: 12px; margin: 20px 0; border: 1px solid #bbf7d0;'>
                    <h3 style='margin-top: 0; color: #166534; font-size: 1.1rem;'>Transaction Details</h3>
                    <table style='width: 100%; font-size: 14px;'>
                        <tr><td style='color: #64748b; padding-bottom: 8px;'>Amount Paid:</td><td style='color: #166534; font-weight: 800; font-size: 1.2rem;'>₱$amount</td></tr>
                        <tr><td style='color: #64748b; padding-bottom: 8px;'>Payment For:</td><td><strong>$desc</strong></td></tr>
                        <tr><td style='color: #64748b; padding-bottom: 8px;'>Channel:</td><td>$method</td></tr>
                        <tr><td style='color: #64748b; padding-bottom: 8px;'>Reference No:</td><td><strong>$ref</strong></td></tr>
                        <tr style='border-top: 1px solid #bbf7d0;'><td style='color: #64748b; padding-top: 10px;'>Remaining Balance:</td><td style='padding-top: 10px; font-weight: 700; color: #ef4444;'>₱$balance</td></tr>
                    </table>
                </div>

                <p>You can view and print your full electronic receipt through the student portal.</p>
                
                <div style='text-align: center; margin-top: 30px;'>
                    <a href='https://ems.jampzdev.com/student/Modules/Payments/History.php' style='background: #16a34a; color: white; padding: 12px 25px; text-decoration: none; border-radius: 8px; font-weight: 700;'>View History</a>
                </div>

                <p style='font-size: 13px; color: #64748b; margin-top: 30px; text-align: center;'>
                    This is an official automated receipt. No signature required.
                </p>
                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                <p style='text-align: center; font-size: 12px; color: #94a3b8;'>
                    &copy; 2026 SMS Official Portal. Office of the Cashier.
                </p>
            </div>
        </div>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        $last_mail_error = $e->getMessage();
        error_log("PHPMailer Error: " . $last_mail_error);
        return false;
    }
}
?>
