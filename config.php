<?php
/**
 * Database Configuration - FJA Professional Cleaning
 * 
 * UPDATE these values with your Hostinger database credentials.
 * You can find them in Hostinger > hPanel > Databases > MySQL Databases
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'u942549985_fja_contacts');
define('DB_USER', 'u942549985_fja_admin');
define('DB_PASS', 'Fja2026Clean!$');

// Email where you want to receive notifications
define('NOTIFY_EMAIL', 'fjaprofessionalcleaning@gmail.com');

// Website name (used in email subjects)
define('SITE_NAME', 'FJA Professional Cleaning');

// Timezone
date_default_timezone_set('America/Denver'); // Utah timezone
