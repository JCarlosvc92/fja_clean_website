<?php
/**
 * Database Setup Script - Run ONCE to create the contacts table.
 * 
 * HOW TO USE:
 * 1. First update config.php with your Hostinger database credentials
 * 2. Upload both files to Hostinger
 * 3. Visit https://yourdomain.com/setup_db.php in your browser
 * 4. After successful setup, DELETE this file from the server for security
 */

require_once 'config.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Create the contacts table
    $sql = "CREATE TABLE IF NOT EXISTS contact_submissions (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(50) NOT NULL,
        email VARCHAR(255) DEFAULT NULL,
        service VARCHAR(255) DEFAULT NULL,
        message TEXT DEFAULT NULL,
        source_page VARCHAR(100) DEFAULT 'contact',
        ip_address VARCHAR(45) DEFAULT NULL,
        submitted_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        is_read TINYINT(1) DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    $pdo->exec($sql);

    echo "<!DOCTYPE html><html><head><title>DB Setup</title>
    <style>body{font-family:Arial,sans-serif;max-width:600px;margin:50px auto;padding:20px;}
    .success{background:#d4edda;border:1px solid #c3e6cb;padding:20px;border-radius:8px;color:#155724;}
    .warning{background:#fff3cd;border:1px solid #ffc107;padding:15px;border-radius:8px;color:#856404;margin-top:15px;}
    </style></head><body>";
    echo "<div class='success'><h2>✅ Database setup complete!</h2>";
    echo "<p>The <strong>contact_submissions</strong> table has been created successfully.</p></div>";
    echo "<div class='warning'><strong>⚠️ IMPORTANT:</strong> Delete this file (setup_db.php) from your server now for security.</div>";
    echo "</body></html>";

} catch (PDOException $e) {
    echo "<!DOCTYPE html><html><head><title>DB Setup Error</title>
    <style>body{font-family:Arial,sans-serif;max-width:600px;margin:50px auto;padding:20px;}
    .error{background:#f8d7da;border:1px solid #f5c6cb;padding:20px;border-radius:8px;color:#721c24;}
    </style></head><body>";
    echo "<div class='error'><h2>❌ Database Error</h2>";
    echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Please check your credentials in <strong>config.php</strong></p></div>";
    echo "</body></html>";
}
