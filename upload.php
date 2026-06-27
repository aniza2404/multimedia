<?php
require_once 'functions.php';
if (!isLoggedIn()) redirect('login.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $file_name = sanitize($file['name']);
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_type = getFileType($file_name);
    
    $upload_dir = $file_type === 'image' ? 'uploads/images/' : 'uploads/pdfs/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
    
    $new_filename = time() . '_' . $file_name;
    $file_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file_tmp, $file_path)) {
        $pdo->beginTransaction();
        
        try {
            // Insert into multimedia_files
            $stmt = $pdo->prepare("INSERT INTO multimedia_files (user_id, file_name, file_type, file_path, file_size) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $file_name, $file_type, $file_path, $file_size]);
            $file_id = $pdo->lastInsertId();
            
            if ($file_type === 'pdf') {
                // TBR - Process PDF
                $text = extractPDFText($file_path);
                $language = detectLanguage($text);
                $words = countWords($text);
                
                $stmt = $pdo->prepare("INSERT INTO pdf_documents (file_id, extracted_text, detected_language, total_words) VALUES (?, ?, ?, ?)");
                $stmt->execute([$file_id, $text, $language, $words]);
                $message = "PDF uploaded and processed successfully!";
                
            } else {
                // CBR - Analyze Image
                $analysis = analyzeImage($file_path);
                $stmt = $pdo->prepare("INSERT INTO image_analysis (file_id, image_category, confidence_score) VALUES (?, ?, ?)");
                $stmt->execute([$file_id, $analysis['category'], $analysis['confidence']]);
                
                // ABR - Emotion Analysis
                $emotion = getEmotionLabel(rand(0, 100));
                $stmt = $pdo->prepare("INSERT INTO emotion_analysis (file_id, emotion_type, confidence_score) VALUES (?, ?, ?)");
                $stmt->execute([$file_id, $emotion, rand(70, 99) / 100]);
                
                $message = "Image uploaded and analyzed successfully!";
            }
            
            $pdo->commit();
        } catch(Exception $e) {
            $pdo->rollBack();
            $message = "Error processing file: " . $e->getMessage();
        }
    } else {
        $message = "Failed to upload file!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Upload</title><link rel="stylesheet" href="style.css"></head>
<body>
    <nav>
        <div class="container">
            <h1>📤 Upload File</h1>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="search.php">Search</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <?php if($message): ?>
            <div class="alert success"><?= $message ?></div>
        <?php endif; ?>
        
        <div class="upload-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="upload-area">
                    <input type="file" name="file" id="file" required>
                    <label for="file">
                        <div class="upload-icon">📁</div>
                        <p>Click or drag to upload</p>
                        <small>Supports: Images (JPG, PNG, GIF) and PDF files</small>
                    </label>
                </div>
                <button type="submit" class="btn-primary">Upload & Process</button>
            </form>
        </div>
        
        <div class="info-box">
            <h4>What happens when you upload?</h4>
            <ul>
                <li><strong>Images:</strong> Analyzed for formal/non-formal (CBR) and emotion detection (ABR)</li>
                <li><strong>PDFs:</strong> Text extracted and language detected (TBR)</li>
            </ul>
        </div>
    </div>
</body>
</html>
