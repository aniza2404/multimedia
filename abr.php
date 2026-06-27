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
    
    // Check if it's an audio file
    $audio_exts = ['mp3', 'wav', 'ogg', 'flac', 'm4a', 'aac', 'wma'];
    $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    if (!in_array($ext, $audio_exts)) {
        $error = "Please upload an audio file! Supported formats: MP3, WAV, OGG, FLAC, M4A, AAC, WMA";
    } elseif ($file_size > MAX_FILE_SIZE) {
        $error = "File too large! Maximum size is 50MB.";
    } else {
        // Create upload directory for audio
        $audio_upload_dir = UPLOAD_DIR . 'audio/';
        if (!is_dir($audio_upload_dir)) {
            mkdir($audio_upload_dir, 0777, true);
        }
        
        $new_filename = time() . '_' . $file_name;
        $file_path = $audio_upload_dir . $new_filename;
        
        if (move_uploaded_file($file_tmp, $file_path)) {
            $pdo->beginTransaction();
            
            try {
                // Save file to database
                $stmt = $pdo->prepare("INSERT INTO multimedia_files (user_id, file_name, file_type, file_path, file_size) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $file_name, 'audio', $file_path, $file_size]);
                $file_id = $pdo->lastInsertId();
                
                // Perform ABR Audio Emotion Analysis
                $analysis_result = analyzeAudioABR($file_path);
                
                // Save emotion analysis
                foreach ($analysis_result['emotions'] as $emotion => $score) {
                    $stmt = $pdo->prepare("INSERT INTO emotion_analysis (file_id, emotion_type, confidence_score) VALUES (?, ?, ?)");
                    $stmt->execute([$file_id, $emotion, $score]);
                }
                
                // Save audio metadata
                if (isset($analysis_result['audio_metadata'])) {
                    $meta = $analysis_result['audio_metadata'];
                    $stmt = $pdo->prepare("INSERT INTO audio_analysis (file_id, duration_seconds, sample_rate, bit_depth, channels, dominant_emotion, confidence_score) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([
                        $file_id,
                        intval($meta['duration']),
                        $meta['sample_rate'],
                        $meta['bit_depth'],
                        $meta['channels'],
                        $analysis_result['dominant'],
                        $analysis_result['confidence']
                    ]);
                }
                
                $pdo->commit();
                $uploaded_file = $file_path;
                
                // Log search
                logSearch($_SESSION['user_id'], $analysis_result['dominant'], 'ABR');
                
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
    <title>ABR Detection - Research Archive</title>
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
            <li><a href="tbr.php"><i class="fas fa-file-pdf"></i> TBR Detection</a></li>
            <li><a href="abr.php" class="active"><i class="fas fa-music"></i> ABR Detection</a></li>
            <li><a href="history.php"><i class="fas fa-history"></i> Database Records</a></li>
            <li class="divider">Settings</li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <h1>Attribute-Based Retrieval (ABR)</h1>
            <div class="user-info">
                <span><?= $_SESSION['user_name'] ?></span>
                <div class="avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
            </div>
        </div>
        
        <div class="container">
            <div class="card">
                <div class="card-title">Upload Audio for Emotion Analysis</div>
                <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">
                    Multi-modal emotion analysis and metadata extraction for psychological research datasets.
                    Upload audio files to detect emotional tone (Happy, Sad, Angry, Neutral, Surprise).
                </p>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="upload-area" id="abr-upload">
                        <div class="upload-icon">🎵</div>
                        <p>Drag and drop audio files for ABR processing</p>
                        <small>Supported formats: MP3, WAV, OGG, FLAC, M4A, AAC, WMA • Max size: 50MB</small>
                        <input type="file" name="file" accept="audio/*" required>
                        <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1rem;">
                            <button type="button" class="btn btn-primary" onclick="document.querySelector('input[name=file]').click();">
                                <i class="fas fa-folder-open"></i> Select Audio File
                            </button>
                            <button type="submit" class="btn btn-success" id="analyze-btn" style="display: none;">
                                <i class="fas fa-microscope"></i> Analyze Emotion
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            
            <?php if($analysis_result && $uploaded_file): ?>
                <div class="card">
                    <div class="card-title">Emotion Analysis Results</div>
                    <div class="analysis-result">
                        <div>
                            <h4>Emotion Distribution</h4>
                            <div class="emotion-chart">
                                <?php 
                                $colors = [
                                    'neutral' => '#4f8cff',
                                    'happy' => '#00d4aa',
                                    'surprise' => '#ff9f4a',
                                    'sad' => '#5a6f8c',
                                    'angry' => '#ff5a7a'
                                ];
                                foreach($analysis_result['emotions'] as $emotion => $score): 
                                ?>
                                    <div class="emotion-bar">
                                        <span class="label"><?= ucfirst($emotion) ?></span>
                                        <div class="bar-track">
                                            <div class="bar-fill" style="width: <?= $score * 100 ?>%; background: <?= $colors[$emotion] ?? '#4f8cff' ?>;"></div>
                                        </div>
                                        <span class="percentage"><?= $score * 100 ?>%</span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <div style="margin-top: 1.5rem; padding: 1rem; background: var(--bg-secondary); border-radius: 8px;">
                                <div><strong>Dominant Emotion:</strong> 
                                    <span style="color: <?= $colors[$analysis_result['dominant']] ?? '#4f8cff' ?>; font-weight: 700;">
                                        <?= ucfirst($analysis_result['dominant']) ?>
                                    </span>
                                </div>
                                <div><strong>Confidence:</strong> <?= $analysis_result['confidence'] * 100 ?>%</div>
                                <div><strong>Method:</strong> <?= $analysis_result['method'] ?></div>
                                <div><strong>Process Time:</strong> <?= $analysis_result['process_time'] ?>s</div>
                            </div>
                        </div>
                        
                        <div>
                            <h4>Audio Metadata</h4>
                            <?php if(isset($analysis_result['audio_metadata'])): ?>
                                <div style="background: var(--bg-secondary); padding: 1rem; border-radius: 8px;">
                                    <div><strong>Duration:</strong> <?= $analysis_result['audio_metadata']['duration'] ?></div>
                                    <div><strong>Sample Rate:</strong> <?= $analysis_result['audio_metadata']['sample_rate'] ?></div>
                                    <div><strong>Bit Depth:</strong> <?= $analysis_result['audio_metadata']['bit_depth'] ?></div>
                                    <div><strong>Channels:</strong> <?= $analysis_result['audio_metadata']['channels'] ?></div>
                                    <div style="margin-top: 0.5rem; padding-top: 0.5rem; border-top: 1px solid var(--border-color);">
                                        <strong>Spectral Features:</strong>
                                        <div style="font-size: 0.85rem; color: var(--text-secondary);">
                                            MFCC Mean: <?= $analysis_result['audio_metadata']['spectral_features']['mfcc_mean'] ?><br>
                                            Spectral Centroid: <?= $analysis_result['audio_metadata']['spectral_features']['spectral_centroid'] ?><br>
                                            Zero Crossing Rate: <?= $analysis_result['audio_metadata']['spectral_features']['zero_crossing_rate'] ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <div style="margin-top: 1rem;">
                                <div class="tags">
                                    <span class="tag">#audio_analysis</span>
                                    <span class="tag">#<?= $analysis_result['dominant'] ?></span>
                                    <span class="tag">#emotion_detection</span>
                                    <span class="tag" style="border: 1px dashed var(--border-color); cursor: pointer;">+ Add Tag</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card" style="text-align: center;">
                    <button class="btn btn-primary" onclick="alert('Analysis committed to archive!')">
                        <i class="fas fa-archive"></i> Commit Analysis to Archive
                    </button>
                </div>
            <?php endif; ?>
        </div>
    </main>
    
    <script>
        document.querySelector('input[name="file"]').addEventListener('change', function() {
            const btn = document.getElementById('analyze-btn');
            if (this.files.length > 0) {
                btn.style.display = 'inline-flex';
                // Show file info
                const file = this.files[0];
                const size = (file.size / (1024 * 1024)).toFixed(2);
                const uploadArea = document.querySelector('.upload-area');
                const p = uploadArea.querySelector('p');
                if (p) {
                    p.textContent = `Selected: ${file.name} (${size} MB)`;
                }
            }
        });
    </script>
    <script src="script.js"></script>
</body>
</html>
