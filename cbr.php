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
    
    if ($file_type !== 'image') {
        $error = "Please upload an image file!";
    } elseif ($file_size > MAX_FILE_SIZE) {
        $error = "File too large! Maximum size is 50MB.";
    } else {
        // Create upload directory if it doesn't exist
        if (!is_dir(IMAGE_UPLOAD_DIR)) {
            mkdir(IMAGE_UPLOAD_DIR, 0777, true);
        }
        
        $new_filename = time() . '_' . $file_name;
        $file_path = IMAGE_UPLOAD_DIR . $new_filename;
        
        if (move_uploaded_file($file_tmp, $file_path)) {
            // Save file to database
            $pdo->beginTransaction();
            
            try {
                $stmt = $pdo->prepare("INSERT INTO multimedia_files (user_id, file_name, file_type, file_path, file_size) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $file_name, 'image', $file_path, $file_size]);
                $file_id = $pdo->lastInsertId();
                
                // Perform CBR analysis
                $analysis_result = analyzeImageCBR($file_path);
                
                // Save analysis results
                $stmt = $pdo->prepare("INSERT INTO image_analysis (file_id, image_category, confidence_score) VALUES (?, ?, ?)");
                $stmt->execute([$file_id, $analysis_result['category'], $analysis_result['confidence']]);
                
                $pdo->commit();
                $uploaded_file = $file_path;
                
                // Log search
                logSearch($_SESSION['user_id'], $analysis_result['category'], 'CBR');
                
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
    <title>CBR Detection - Research Archive</title>
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
            <li><a href="cbr.php" class="active"><i class="fas fa-image"></i> CBR Detection</a></li>
            <li><a href="tbr.php"><i class="fas fa-file-pdf"></i> TBR Detection</a></li>
            <li><a href="abr.php"><i class="fas fa-smile"></i> ABR Detection</a></li>
            <li><a href="history.php"><i class="fas fa-history"></i> Database Records</a></li>
            <li class="divider">Settings</li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <h1>Content-Based Retrieval</h1>
            <div class="user-info">
                <span><?= $_SESSION['user_name'] ?></span>
                <div class="avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
            </div>
        </div>
        
        <div class="container">
            <div class="card">
                <div class="card-title">Upload Image for CBR Analysis</div>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Upload an image to perform deep semantic analysis across the institutional research archive. 
                    Our CBR engine utilizes neural embeddings to match visual patterns and classification standards.
                </p>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="upload-area" id="cbr-upload">
                        <div class="upload-icon">📁</div>
                        <p>Drag and drop research media</p>
                        <small>Support for high-resolution PNG, TIFF, and DICOM formats. Maximum file size: 50MB per instance.</small>
                        <input type="file" name="file" accept="image/*" required>
                        <button type="button" class="btn btn-primary" onclick="document.querySelector('input[name=file]').click();" style="margin-top: 1rem;">
                            <i class="fas fa-folder-open"></i> Browse Files
                        </button>
                        <button type="submit" class="btn btn-success" id="analyze-btn" style="margin-top: 0.5rem; display: none;">
                            <i class="fas fa-microscope"></i> Analyze Image
                        </button>
                    </div>
                </form>
            </div>
            
            <?php if($analysis_result && $uploaded_file): ?>
                <div class="card">
                    <div class="card-title">Uploaded Asset Preview</div>
                    <div class="analysis-result">
                        <div class="result-preview">
                            <?php if(file_exists($uploaded_file)): ?>
                                <img src="<?= $uploaded_file ?>" alt="Uploaded image" style="max-width: 100%; max-height: 400px; border-radius: 8px;">
                            <?php endif; ?>
                            <p style="color: var(--text-secondary); margin-top: 1rem; font-size: 0.85rem;">
                                Metadata detected: <?= $analysis_result['metadata']['dimensions'] ?? 'Unknown' ?>, 
                                <?= $analysis_result['metadata']['color_space'] ?? 'sRGB' ?>, 
                                <?= $analysis_result['metadata']['bit_depth'] ?? '16-bit' ?> depth.
                            </p>
                        </div>
                        
                        <div>
                            <h3 style="margin-bottom: 1rem;">Latest Detection</h3>
                            <div style="background: var(--bg-secondary); padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem;">
                                <div style="font-size: 2rem; font-weight: 700; color: <?= $analysis_result['category'] === 'formal' ? 'var(--accent-blue)' : 'var(--accent-orange)' ?>;">
                                    <?= ucfirst($analysis_result['category']) ?>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <div><strong>Confidence Score</strong></div>
                                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--accent-green);">
                                        <?= $analysis_result['confidence'] * 100 ?>%
                                    </div>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <div><strong>Process Time</strong></div>
                                    <div><?= $analysis_result['process_time'] ?>s</div>
                                </div>
                                <div style="margin-top: 1rem;">
                                    <div><strong>Method</strong></div>
                                    <div><?= $analysis_result['method'] ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-title">Recent History</div>
                    <ul class="history-list">
                        <li>
                            <div class="item-info">
                                <span class="icon">🖼️</span>
                                <div class="details">
                                    <div class="name"><?= $_FILES['file']['name'] ?></div>
                                    <div class="meta">Just now • <?= ucfirst($analysis_result['category']) ?> • <?= $analysis_result['confidence'] * 100 ?>%</div>
                                </div>
                            </div>
                            <span class="badge">CBR</span>
                        </li>
                        <?php 
                        $recent = getRecentUploads($_SESSION['user_id'], 3);
                        foreach($recent as $file): 
                        ?>
                            <li>
                                <div class="item-info">
                                    <span class="icon">🖼️</span>
                                    <div class="details">
                                        <div class="name"><?= substr($file['file_name'], 0, 30) ?>...</div>
                                        <div class="meta"><?= time_elapsed_string($file['upload_date']) ?> • File</div>
                                    </div>
                                </div>
                                <span class="badge">CBR</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script>
        // Show analyze button when file is selected
        document.querySelector('input[name="file"]').addEventListener('change', function() {
            const btn = document.getElementById('analyze-btn');
            if (this.files.length > 0) {
                btn.style.display = 'inline-flex';
            }
        });
    </script>
    <script src="script.js"></script>
</body>
</html>
