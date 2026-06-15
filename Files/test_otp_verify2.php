<?php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'http://localhost/sms/student/auth/verify_otp.php');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'email' => 'linbilcelestre3@gmail.com',
    'otp' => '411969',
    'type' => 'login'
]));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
echo "Response: " . $response . "\n";
?>
