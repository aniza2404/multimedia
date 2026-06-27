<?php
// Database configuration
$host = 'localhost';
$dbname = 'multimedia_retrieval';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

session_start();

// Constants
define('UPLOAD_DIR', 'uploads/');
define('IMAGE_UPLOAD_DIR', UPLOAD_DIR . 'images/');
define('PDF_UPLOAD_DIR', UPLOAD_DIR . 'pdfs/');
define('MAX_FILE_SIZE', 50 * 1024 * 1024); // 50MB
?>
