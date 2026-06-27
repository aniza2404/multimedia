<?php
require_once 'functions.php';
requireLogin();

$stats = getStats($_SESSION['user_id']);
$recent_files = getRecentUploads($_SESSION['user_id'], 5);
$recent_history = getRecentHistory($_SESSION['user_id'], 5);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Research Archive</title>
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
            <li><a href="dashboard.php" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="cbr.php"><i class="fas fa-image"></i> CBR Detection</a></li>
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
            <h1>Dashboard</h1>
            <div class="user-info">
                <span><?= $_SESSION['user_name'] ?></span>
                <div class="avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
            </div>
        </div>
        
        <div class="container">
            <!-- Stats Grid -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📁</div>
                    <div class="stat-value"><?= $stats['total_files'] ?></div>
                    <div class="stat-label">Total Files</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🖼️</div>
                    <div class="stat-value"><?= $stats['images'] ?></div>
                    <div class="stat-label">Images</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">📄</div>
                    <div class="stat-value"><?= $stats['pdfs'] ?></div>
                    <div class="stat-label">PDF Documents</div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🔍</div>
                    <div class="stat-value"><?= $stats['searches'] ?></div>
                    <div class="stat-label">Searches</div>
                </div>
            </div>
            
            <!-- Recent Files -->
            <div class="card">
                <div class="card-title">Recent Uploads</div>
                <?php if(count($recent_files) > 0): ?>
                    <ul class="history-list">
                        <?php foreach($recent_files as $file): ?>
                            <li>
                                <div class="item-info">
                                    <span class="icon">
                                        <i class="fas <?= getFileIcon($file['file_type']) ?>"></i>
                                    </span>
                                    <div class="details">
                                        <div class="name"><?= $file['file_name'] ?></div>
                                        <div class="meta">
                                            <?= formatFileSize($file['file_size']) ?> • 
                                            <?= date('M d, Y H:i', strtotime($file['upload_date'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="badge"><?= strtoupper($file['file_type']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: var(--text-secondary); text-align: center; padding: 2rem 0;">
                        No files uploaded yet. <a href="cbr.php">Upload now</a>
                    </p>
                <?php endif; ?>
            </div>
            
            <!-- Recent Searches -->
            <div class="card">
                <div class="card-title">Recent Searches</div>
                <?php if(count($recent_history) > 0): ?>
                    <ul class="history-list">
                        <?php foreach($recent_history as $search): ?>
                            <li>
                                <div class="item-info">
                                    <span class="icon">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <div class="details">
                                        <div class="name">"<?= $search['search_keyword'] ?>"</div>
                                        <div class="meta">
                                            <?= $search['retrieval_type'] ?> • 
                                            <?= date('M d, Y H:i', strtotime($search['search_date'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <?php if(isset($search['result_type'])): ?>
                                    <span class="badge"><?= $search['result_type'] ?></span>
                                <?php endif; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: var(--text-secondary); text-align: center; padding: 2rem 0;">
                        No searches yet. Start searching <a href="cbr.php">here</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script src="script.js"></script>
</body>
</html>
