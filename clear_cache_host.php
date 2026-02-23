<?php
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo 'OPCache cleared! ' . date('c');
} else {
    echo 'OPCache reset function not available. ' . date('c');
}
?>