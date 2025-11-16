<?php
// Autoload from vendor if available
$autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($autoload)) {
    require_once $autoload;
}

// Minimal bootstrap: nothing else here. Integration tests may require WP test harness.
