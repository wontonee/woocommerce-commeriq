<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Autoload from vendor if available
$commeriq_autoload = __DIR__ . '/../vendor/autoload.php';
if (file_exists($commeriq_autoload)) {
    require_once $commeriq_autoload;
}

// Minimal bootstrap: nothing else here. Integration tests may require WP test harness.
