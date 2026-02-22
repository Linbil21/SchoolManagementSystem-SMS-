<?php
$file = 'Admission/Modules/New-Applications.php';
if (file_exists($file)) {
    $lines = file($file);
    echo "DUMPING LINES 280-310 OF $file:\n";
    for ($i = 279; $i < 309 && $i < count($lines); $i++) {
        echo ($i + 1) . ": " . $lines[$i];
    }
} else {
    echo "FILE NOT FOUND: $file\n";
}
?>
