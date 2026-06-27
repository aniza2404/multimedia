// Modern Research Archive System JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Mobile sidebar toggle
    const toggleBtn = document.createElement('button');
    toggleBtn.className = 'mobile-toggle';
    toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    toggleBtn.style.display = 'none';
    
    const topBar = document.querySelector('.top-bar');
    if (topBar) {
        topBar.prepend(toggleBtn);
    }
    
    toggleBtn.addEventListener('click', function() {
        document.querySelector('.sidebar').classList.toggle('open');
    });
    
    // File upload drag and drop
    const uploadAreas = document.querySelectorAll('.upload-area');
    uploadAreas.forEach(area => {
        const fileInput = area.querySelector('input[type="file"]');
        if (!fileInput) return;
        
        area.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON' && !e.target.closest('button')) {
                fileInput.click();
            }
        });
        
        area.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.style.borderColor = '#4f8cff';
            this.style.background = 'rgba(79, 140, 255, 0.05)';
        });
        
        area.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.style.borderColor = '#2a3a4f';
            this.style.background = 'transparent';
        });
        
        area.addEventListener('drop', function(e) {
            e.preventDefault();
            this.style.borderColor = '#2a3a4f';
            this.style.background = 'transparent';
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileSelect(fileInput, area);
            }
        });
        
        fileInput.addEventListener('change', function() {
            handleFileSelect(this, area);
        });
    });
    
    function handleFileSelect(input, area) {
        if (input.files.length > 0) {
            const file = input.files[0];
            const fileSize = (file.size / (1024 * 1024)).toFixed(2);
            
            const icon = area.querySelector('.upload-icon');
            const text = area.querySelector('p');
            
            if (icon) {
                if (file.type.includes('image')) {
                    icon.textContent = '🖼️';
                } else if (file.type === 'application/pdf') {
                    icon.textContent = '📄';
                } else {
                    icon.textContent = '📁';
                }
            }
            if (text) {
                text.textContent = `${file.name} (${fileSize} MB)`;
            }
            
            // Show analyze/submit button if exists
            const btn = area.closest('.card')?.querySelector('.btn-success');
            if (btn) {
                btn.style.display = 'inline-flex';
            }
        }
    }
    
    // Initialize emotion bars with animation
    function animateEmotionBars() {
        const bars = document.querySelectorAll('.bar-fill');
        bars.forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0%';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    }
    
    setTimeout(animateEmotionBars, 500);
    
    // File size validation
    document.querySelectorAll('form[enctype="multipart/form-data"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const fileInput = this.querySelector('input[type="file"]');
            if (fileInput && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const maxSize = 50 * 1024 * 1024;
                
                if (file.size > maxSize) {
                    e.preventDefault();
                    showAlert('File too large! Maximum size is 50MB.', 'error');
                    return false;
                }
            }
        });
    });
    
    function showAlert(message, type = 'info') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type}`;
        alertDiv.textContent = message;
        
        const container = document.querySelector('.main-content .container');
        if (container) {
            container.prepend(alertDiv);
            setTimeout(() => {
                alertDiv.style.opacity = '0';
                setTimeout(() => alertDiv.remove(), 500);
            }, 5000);
        }
    }
    
    // Add tag functionality (for ABR page)
    const addTagInput = document.querySelector('.add-tag-input');
    const addTagBtn = document.querySelector('.add-tag-btn');
    const tagsContainer = document.querySelector('.tags');
    
    if (addTagBtn && addTagInput && tagsContainer) {
        addTagBtn.addEventListener('click', function() {
            const tag = addTagInput.value.trim();
            if (tag) {
                const tagEl = document.createElement('span');
                tagEl.className = 'tag';
                tagEl.textContent = '#' + tag;
                tagsContainer.appendChild(tagEl);
                addTagInput.value = '';
            }
        });
        
        addTagInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                addTagBtn.click();
            }
        });
    }
    
    // Sync status animation
    const syncStatus = document.querySelector('.status-badge.live');
    if (syncStatus) {
        setInterval(() => {
            const dot = syncStatus.querySelector('.dot');
            if (dot) {
                dot.style.animation = 'none';
                setTimeout(() => {
                    dot.style.animation = 'pulse 2s infinite';
                }, 10);
            }
        }, 3000);
    }
    
    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            const searchInput = document.querySelector('input[type="text"]');
            if (searchInput) {
                searchInput.focus();
            }
        }
        
        if (e.key === 'Escape') {
            const sidebar = document.querySelector('.sidebar');
            if (sidebar && sidebar.classList.contains('open')) {
                sidebar.classList.remove('open');
            }
        }
    });
    
    // Auto-submit for CBR upload
    const cbrForm = document.querySelector('#cbr-upload')?.closest('form');
    if (cbrForm) {
        const fileInput = cbrForm.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    // Show analyze button
                    const analyzeBtn = document.getElementById('analyze-btn');
                    if (analyzeBtn) {
                        analyzeBtn.style.display = 'inline-flex';
                    }
                }
            });
        }
    }
    
    console.log('🔬 Research Archive System loaded successfully!');
    console.log(`👤 Logged in as: ${document.querySelector('.user-info span')?.textContent || 'User'}`);
});
