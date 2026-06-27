<?php
require_once 'functions.php';
requireLogin();

$analysis_result = null;
$uploaded_file = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $file_name = sanitize($file['name']);
    $file_size = $file['size'];
    $file_tmp = $file['tmp_name'];
    $file_type = getFileType($file_name);
    
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if ($ext !== 'pdf') {
        $error = "Please upload a PDF file!";
    } elseif ($file_size > MAX_FILE_SIZE) {
        $error = "File too large! Maximum size is 50MB.";
    } else {
        if (!is_dir(PDF_UPLOAD_DIR)) {
            mkdir(PDF_UPLOAD_DIR, 0777, true);
        }
        
        $new_filename = time() . '_' . $file_name;
        $file_path = PDF_UPLOAD_DIR . $new_filename;
        
        if (move_uploaded_file($file_tmp, $file_path)) {
            $pdo->beginTransaction();
            
            try {
                $stmt = $pdo->prepare("INSERT INTO multimedia_files (user_id, file_name, file_type, file_path, file_size) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $file_name, 'pdf', $file_path, $file_size]);
                $file_id = $pdo->lastInsertId();
                
                // Perform TBR analysis - PDF to Text + Language Detection
                $analysis_result = extractPDFTBR($file_path);
                
                // Save analysis results
                $stmt = $pdo->prepare("INSERT INTO pdf_documents (file_id, extracted_text, detected_language, total_words) VALUES (?, ?, ?, ?)");
                $stmt->execute([$file_id, $analysis_result['text'], $analysis_result['language_code'], $analysis_result['total_words']]);
                
                $pdo->commit();
                $uploaded_file = $file_path;
                
                // Log search
                logSearch($_SESSION['user_id'], 'PDF Analysis', 'TBR');
                
            } catch(Exception $e) {
                $pdo->rollBack();
                $error = "Error processing file: " . $e->getMessage();
            }
        } else {
            $error = "Failed to upload file!";
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>TBR Detection - Research Archive</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="sidebar-brand">
            <h2>🔬 Research Archive</h2>
            <small>Data Management System</small>
        </div>
        
        <div class="user-profile" style="padding: 0 1.5rem 1.5rem; border-bottom: 1px solid var(--border-color); margin-bottom: 1.5rem;">
            <div class="avatar" style="width: 48px; height: 48px; border-radius: 50%; background: linear-gradient(135deg, var(--accent-blue), var(--accent-purple)); display: flex; align-items: center; justify-content: center; font-weight: 600; font-size: 1.2rem; margin-bottom: 0.5rem;">
                <?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?>
            </div>
            <div>
                <div style="font-weight: 600;"><?= $_SESSION['user_name'] ?? 'User' ?></div>
                <div style="font-size: 0.8rem; color: var(--text-secondary);">@<?= $_SESSION['username'] ?? 'username' ?></div>
                <span class="status-badge live" style="margin-top: 0.25rem; font-size: 0.65rem;">
                    <span class="dot"></span> Online
                </span>
            </div>
        </div>
        
        <ul class="sidebar-menu">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="cbr.php"><i class="fas fa-image"></i> CBR Detection</a></li>
            <li><a href="tbr.php" class="active"><i class="fas fa-file-pdf"></i> TBR Detection</a></li>
            <li><a href="abr.php"><i class="fas fa-music"></i> ABR Detection</a></li>
            <li><a href="history.php"><i class="fas fa-history"></i> Database Records</a></li>
            <li class="divider">Settings</li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <h1>Text-Based Retrieval (TBR)</h1>
            <div class="user-info">
                <span><?= $_SESSION['user_name'] ?></span>
                <div class="avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
            </div>
        </div>
        
        <div class="container">
            <div class="card">
                <div class="card-title">Source Document</div>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Perform semantic and keyword-based extraction from research documentation.
                    Upload PDF to extract text and detect the document's language.
                </p>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="upload-area">
                        <div class="upload-icon">📄</div>
                        <p>Click or drag PDF to upload</p>
                        <small>Maximum file size: 50MB • Extracted text will be analyzed for language detection</small>
                        <input type="file" name="file" accept=".pdf" required>
                        <div style="margin-top: 1rem;">
                            <button type="button" class="btn btn-primary" onclick="document.querySelector('input[name=file]').click();">
                                <i class="fas fa-folder-open"></i> Browse PDF
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-file-alt"></i> Extract & Detect Language
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if($analysis_result): ?>
                <div class="card">
                    <div class="card-title">📊 Language Detection Result</div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; background: var(--bg-secondary); padding: 1.5rem; border-radius: 8px;">
                        <div>
                            <div style="color: var(--text-secondary); font-size: 0.85rem;">Detected Language</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-blue);">
                                <?= strtoupper($analysis_result['language_code']) ?>
                            </div>
                            <div style="color: var(--text-secondary);"><?= $analysis_result['language_name'] ?></div>
                        </div>
                        <div>
                            <div style="color: var(--text-secondary); font-size: 0.85rem;">Total Words</div>
                            <div style="font-size: 1.5rem; font-weight: 700;"><?= number_format($analysis_result['total_words']) ?></div>
                        </div>
                        <div>
                            <div style="color: var(--text-secondary); font-size: 0.85rem;">OCR Fidelity</div>
                            <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-green);">
                                <?= $analysis_result['ocr_fidelity'] * 100 ?>%
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-title">📝 Extracted Text (Preview)</div>
                    <div style="background: var(--bg-secondary); padding: 1.5rem; border-radius: 8px; max-height: 300px; overflow-y: auto; line-height: 1.8; color: var(--text-secondary);">
                        <?= substr($analysis_result['text'], 0, 1000) ?>...
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-title">🔑 Keyword Retrieval Engine</div>
                    <p style="color: var(--text-secondary); margin-bottom: 1rem;">
                        Analyze documents for specific terms and semantic relevance scores.
                    </p>
                    
                    <div class="tbr-keywords">
                        <?php foreach($analysis_result['keywords'] as $keyword => $data): ?>
                            <div class="keyword-item">
                                <span class="keyword"><?= ucfirst($keyword) ?></span>
                                <div>
                                    <span style="color: var(--text-secondary); font-size: 0.85rem;">
                                        <?= $data['frequency'] ?> times
                                    </span>
                                    <span class="relevance"><?= $data['relevance'] * 100 ?>%</span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap;">
                    <div>
                        <span class="status-badge live">
                            <span class="dot"></span> Sync Status: Live
                        </span>
                    </div>
                    <div>
                        <span style="color: var(--text-secondary);">Process Time: <?= $analysis_result['process_time'] ?>s</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script src="script.js"></script>
</body>
</html>
