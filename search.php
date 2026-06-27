<?php
require_once 'functions.php';
if (!isLoggedIn()) redirect('login.php');

$type = isset($_GET['type']) ? $_GET['type'] : 'ABR';
$results = [];
$search_term = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $search_term = sanitize($_POST['keyword']);
    $type = sanitize($_POST['search_type']);
    
    logSearch($_SESSION['user_id'], $search_term, $type);
    
    switch($type) {
        case 'ABR':
            // Search by emotion
            $stmt = $pdo->prepare("
                SELECT m.*, e.emotion_type, e.confidence_score 
                FROM multimedia_files m
                JOIN emotion_analysis e ON m.file_id = e.file_id
                WHERE m.user_id = ? AND e.emotion_type LIKE ?
            ");
            $stmt->execute([$_SESSION['user_id'], "%$search_term%"]);
            $results = $stmt->fetchAll();
            break;
            
        case 'TBR':
            // Search in PDF text
            $stmt = $pdo->prepare("
                SELECT m.*, p.extracted_text, p.detected_language 
                FROM multimedia_files m
                JOIN pdf_documents p ON m.file_id = p.file_id
                WHERE m.user_id = ? AND p.extracted_text LIKE ?
            ");
            $stmt->execute([$_SESSION['user_id'], "%$search_term%"]);
            $results = $stmt->fetchAll();
            break;
            
        case 'CBR':
            // Search by image category
            $stmt = $pdo->prepare("
                SELECT m.*, i.image_category, i.confidence_score 
                FROM multimedia_files m
                JOIN image_analysis i ON m.file_id = i.file_id
                WHERE m.user_id = ? AND i.image_category LIKE ?
            ");
            $stmt->execute([$_SESSION['user_id'], "%$search_term%"]);
            $results = $stmt->fetchAll();
            break;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Search</title><link rel="stylesheet" href="style.css"></head>
<body>
    <nav>
        <div class="container">
            <h1>🔍 Search</h1>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="upload.php">Upload</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="search-container">
            <h2>Search Multimedia</h2>
            
            <form method="POST" class="search-form">
                <div class="search-type-selector">
                    <label>
                        <input type="radio" name="search_type" value="ABR" <?= $type === 'ABR' ? 'checked' : '' ?>>
                        🏷️ ABR - Emotion
                    </label>
                    <label>
                        <input type="radio" name="search_type" value="TBR" <?= $type === 'TBR' ? 'checked' : '' ?>>
                        📄 TBR - Text
                    </label>
                    <label>
                        <input type="radio" name="search_type" value="CBR" <?= $type === 'CBR' ? 'checked' : '' ?>>
                        🖼️ CBR - Content
                    </label>
                </div>
                
                <div class="search-input-group">
                    <input type="text" name="keyword" placeholder="Enter search keyword..." value="<?= $search_term ?>" required>
                    <button type="submit">Search</button>
                </div>
            </form>
            
            <?php if($search_term): ?>
                <h3>Results for "<?= $search_term ?>" (<?= count($results) ?> found)</h3>
                <div class="results-grid">
                    <?php foreach($results as $result): ?>
                        <div class="result-card">
                            <div class="file-icon"><?= $result['file_type'] === 'image' ? '🖼️' : '📄' ?></div>
                            <h4><?= $result['file_name'] ?></h4>
                            <?php if($type === 'ABR'): ?>
                                <span class="badge">Emotion: <?= $result['emotion_type'] ?></span>
                                <small>Confidence: <?= $result['confidence_score'] * 100 ?>%</small>
                            <?php elseif($type === 'TBR'): ?>
                                <span class="badge">Language: <?= $result['detected_language'] ?></span>
                                <small>Text preview: <?= substr($result['extracted_text'], 0, 100) ?>...</small>
                            <?php elseif($type === 'CBR'): ?>
                                <span class="badge">Category: <?= $result['image_category'] ?></span>
                                <small>Confidence: <?= $result['confidence_score'] * 100 ?>%</small>
                            <?php endif; ?>
                            <small>Uploaded: <?= date('M d, Y', strtotime($result['upload_date'])) ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
