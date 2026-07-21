<?php
/**
 * Form Submission Handler - FJA Professional Cleaning
 * 
 * Receives form data, saves to database, and sends email notification.
 */

require_once 'config.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

// Simple CSRF / bot protection
$honeypot = isset($_POST['website']) ? trim($_POST['website']) : '';
if (!empty($honeypot)) {
    // Bot detected (filled hidden field)
    header('Location: contact.html?status=success'); // Fake success to fool bots
    exit;
}

// Sanitize input
function clean($val) {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

$name    = isset($_POST['name'])    ? clean($_POST['name'])    : '';
$phone   = isset($_POST['phone'])   ? clean($_POST['phone'])   : '';
$email   = isset($_POST['email'])   ? clean($_POST['email'])   : '';
$service = isset($_POST['service']) ? clean($_POST['service']) : '';
$message = isset($_POST['message']) ? clean($_POST['message']) : '';
$source  = isset($_POST['source_page']) ? clean($_POST['source_page']) : 'contact';

// Validate required fields
if (empty($name) || empty($phone)) {
    header('Location: contact.html?status=error&msg=missing_fields');
    exit;
}

// Get IP address
$ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

try {
    // Connect to database
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO contact_submissions (name, phone, email, service, message, source_page, ip_address) VALUES (:name, :phone, :email, :service, :message, :source, :ip)");
    
    $stmt->execute([
        ':name'    => $name,
        ':phone'   => $phone,
        ':email'   => $email,
        ':service' => $service,
        ':message' => $message,
        ':source'  => $source,
        ':ip'      => $ip
    ]);

    // Send email notification
    $to = NOTIFY_EMAIL;
    $subject = "📩 New Contact - " . SITE_NAME . " (" . ucfirst($source) . ")";
    
    $body  = "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $body .= "  NEW CONTACT FORM SUBMISSION\n";
    $body .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";
    $body .= "👤 Name:     $name\n";
    $body .= "📞 Phone:    $phone\n";
    if (!empty($email)) {
        $body .= "📧 Email:    $email\n";
    }
    if (!empty($service)) {
        $body .= "🔧 Service:  $service\n";
    }
    if (!empty($message)) {
        $body .= "\n💬 Message:\n$message\n";
    }
    $body .= "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $body .= "📄 Source: $source page\n";
    $body .= "🕐 Date:  " . date('M d, Y h:i A') . "\n";
    $body .= "🌐 IP:    $ip\n";

    $headers  = "From: noreply@" . $_SERVER['HTTP_HOST'] . "\r\n";
    $headers .= "Reply-To: " . (!empty($email) ? $email : NOTIFY_EMAIL) . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "X-Mailer: FJA-Website/1.0\r\n";

    mail($to, $subject, $body, $headers);

    // Redirect with success
    $redirect = ($source === 'contact') ? 'contact.html' : 'index.html';
    header("Location: $redirect?status=success");
    exit;

} catch (PDOException $e) {
    error_log("FJA Form Error: " . $e->getMessage());
    header('Location: contact.html?status=error');
    exit;
}
