<?php
require_once 'functions.php';
requireLogin();

$search_history = getSearchHistory($_SESSION['user_id'], 50);
$files = getUserFiles($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Records - Research Archive</title>
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
            <li><a href="abr.php"><i class="fas fa-smile"></i> ABR Detection</a></li>
            <li><a href="history.php" class="active"><i class="fas fa-history"></i> Database Records</a></li>
            <li class="divider">Settings</li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
    
    <!-- Main Content -->
    <main class="main-content">
        <div class="top-bar">
            <h1>Database Records</h1>
            <div class="user-info">
                <span><?= $_SESSION['user_name'] ?></span>
                <div class="avatar"><?= strtoupper(substr($_SESSION['username'] ?? 'U', 0, 1)) ?></div>
            </div>
        </div>
        
        <div class="container">
            <div class="card">
                <div class="card-title">All Files</div>
                <?php if(count($files) > 0): ?>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="border-bottom: 1px solid var(--border-color);">
                                    <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary);">File</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary);">Type</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary);">Size</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary);">Uploaded</th>
                                    <th style="text-align: left; padding: 0.75rem 0.5rem; color: var(--text-secondary);">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($files as $file): ?>
                                    <tr style="border-bottom: 1px solid var(--border-color);">
                                        <td style="padding: 0.75rem 0.5rem;">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <i class="fas <?= getFileIcon($file['file_type']) ?>" style="color: var(--text-secondary);"></i>
                                                <?= $file['file_name'] ?>
                                            </div>
                                        </td>
                                        <td style="padding: 0.75rem 0.5rem;">
                                            <span class="badge"><?= strtoupper($file['file_type']) ?></span>
                                        </td>
                                        <td style="padding: 0.75rem 0.5rem; color: var(--text-secondary);">
                                            <?= formatFileSize($file['file_size']) ?>
                                        </td>
                                        <td style="padding: 0.75rem 0.5rem; color: var(--text-secondary);">
                                            <?= date('M d, Y', strtotime($file['upload_date'])) ?>
                                        </td>
                                        <td style="padding: 0.75rem 0.5rem;">
                                            <span class="status-badge live">
                                                <span class="dot"></span> Processed
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p style="color: var(--text-secondary); text-align: center; padding: 2rem 0;">
                        No files in database. <a href="cbr.php">Upload your first file</a>
                    </p>
                <?php endif; ?>
            </div>
            
            <div class="card">
                <div class="card-title">Search History</div>
                <?php if(count($search_history) > 0): ?>
                    <ul class="history-list">
                        <?php foreach($search_history as $search): ?>
                            <li>
                                <div class="item-info">
                                    <span class="icon">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <div class="details">
                                        <div class="name">"<?= $search['search_keyword'] ?>"</div>
                                        <div class="meta">
                                            <?= $search['retrieval_type'] ?> • 
                                            <?= date('M d, Y H:i:s', strtotime($search['search_date'])) ?>
                                        </div>
                                    </div>
                                </div>
                                <span class="badge"><?= $search['retrieval_type'] ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p style="color: var(--text-secondary); text-align: center; padding: 2rem 0;">
                        No search history yet.
                    </p>
                <?php endif; ?>
            </div>
        </div>
    </main>
    
    <script src="script.js"></script>
</body>
</html>
