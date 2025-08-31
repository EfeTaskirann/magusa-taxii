// Mağusa Taxi Admin Panel JavaScript

document.addEventListener('DOMContentLoaded', function() {
    initializeAdminPanel();
    initializeFormValidation();
    initializeImageUpload();
    initializeSidebar();
    initializeNotifications();
});

// Initialize admin panel functionality
function initializeAdminPanel() {
    // Auto-save drafts for content forms
    const contentTextarea = document.getElementById('blog_content');
    if (contentTextarea) {
        let saveTimer;
        contentTextarea.addEventListener('input', function() {
            clearTimeout(saveTimer);
            saveTimer = setTimeout(() => {
                saveDraft('blog_content', this.value);
            }, 2000);
        });
        
        // Load saved draft
        loadDraft('blog_content', contentTextarea);
    }
    
    // Confirm before leaving with unsaved changes
    let hasUnsavedChanges = false;
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, textarea, select');
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                hasUnsavedChanges = true;
            });
        });
        
        form.addEventListener('submit', () => {
            hasUnsavedChanges = false;
        });
    });
    
    window.addEventListener('beforeunload', function(e) {
        if (hasUnsavedChanges) {
            e.preventDefault();
            e.returnValue = 'Kaydedilmemiş değişiklikleriniz var. Sayfadan ayrılmak istediğinizden emin misiniz?';
        }
    });
}

// Form validation
function initializeFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
                showNotification('Lütfen tüm gerekli alanları doldurun.', 'error');
            }
        });
    });
    
    // Real-time validation
    const inputs = document.querySelectorAll('input[required], textarea[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateInput(this);
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('error')) {
                validateInput(this);
            }
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    
    inputs.forEach(input => {
        if (!validateInput(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateInput(input) {
    const value = input.value.trim();
    let isValid = true;
    
    // Remove previous error styling
    input.classList.remove('error');
    const existingError = input.parentNode.querySelector('.error-message');
    if (existingError) {
        existingError.remove();
    }
    
    // Required field validation
    if (input.hasAttribute('required') && !value) {
        isValid = false;
        showInputError(input, 'Bu alan zorunludur.');
    }
    
    // Email validation
    if (input.type === 'email' && value && !isValidEmail(value)) {
        isValid = false;
        showInputError(input, 'Geçerli bir e-posta adresi girin.');
    }
    
    // Phone validation
    if (input.type === 'tel' && value && !isValidPhone(value)) {
        isValid = false;
        showInputError(input, 'Geçerli bir telefon numarası girin.');
    }
    
    // Password validation
    if (input.type === 'password' && input.name === 'new_password' && value && value.length < 6) {
        isValid = false;
        showInputError(input, 'Şifre en az 6 karakter olmalıdır.');
    }
    
    return isValid;
}

function showInputError(input, message) {
    input.classList.add('error');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    errorDiv.style.cssText = `
        color: #e74c3c;
        font-size: 0.8rem;
        margin-top: 5px;
    `;
    
    input.parentNode.appendChild(errorDiv);
}

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

function isValidPhone(phone) {
    return /^\+?[1-9]\d{1,14}$/.test(phone.replace(/\s/g, ''));
}

// Image upload functionality
function initializeImageUpload() {
    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                if (!allowedTypes.includes(file.type)) {
                    showNotification('Sadece JPEG, PNG ve WebP formatları desteklenir.', 'error');
                    this.value = '';
                    return;
                }
                
                // Validate file size (5MB)
                const maxSize = 5 * 1024 * 1024;
                if (file.size > maxSize) {
                    showNotification('Dosya boyutu 5MB\'dan küçük olmalıdır.', 'error');
                    this.value = '';
                    return;
                }
                
                // Show preview
                showImagePreview(file);
            }
        });
    }
    
    // Drag and drop functionality
    const uploadArea = document.querySelector('.upload-form');
    if (uploadArea) {
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            this.classList.add('drag-over');
        });
        
        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
        });
        
        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            this.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                imageInput.files = files;
                showImagePreview(files[0]);
            }
        });
    }
}

function showImagePreview(file) {
    const reader = new FileReader();
    reader.onload = function(e) {
        let preview = document.querySelector('.image-preview-temp');
        if (!preview) {
            preview = document.createElement('div');
            preview.className = 'image-preview-temp';
            preview.innerHTML = `
                <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <p style="margin: 0 0 10px 0; font-weight: 500;">Önizleme:</p>
                    <img style="max-width: 200px; max-height: 150px; border-radius: 8px;">
                </div>
            `;
            document.querySelector('.upload-form').appendChild(preview);
        }
        preview.querySelector('img').src = e.target.result;
    };
    reader.readAsDataURL(file);
}

// Sidebar functionality
function initializeSidebar() {
    const sidebarToggle = document.createElement('button');
    sidebarToggle.innerHTML = '<i class="fas fa-bars"></i>';
    sidebarToggle.className = 'sidebar-toggle';
    sidebarToggle.style.cssText = `
        display: none;
        position: fixed;
        top: 20px;
        left: 20px;
        z-index: 1000;
        background: #3498db;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
    `;
    
    document.body.appendChild(sidebarToggle);
    
    // Mobile sidebar toggle
    sidebarToggle.addEventListener('click', function() {
        const sidebar = document.querySelector('.sidebar');
        sidebar.classList.toggle('mobile-open');
    });
    
    // Show toggle button on mobile
    function checkMobile() {
        if (window.innerWidth <= 768) {
            sidebarToggle.style.display = 'block';
        } else {
            sidebarToggle.style.display = 'none';
            document.querySelector('.sidebar').classList.remove('mobile-open');
        }
    }
    
    window.addEventListener('resize', checkMobile);
    checkMobile();
}

// Notification system
function initializeNotifications() {
    // Create notification container
    const notificationContainer = document.createElement('div');
    notificationContainer.id = 'notification-container';
    notificationContainer.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 10000;
        max-width: 400px;
    `;
    document.body.appendChild(notificationContainer);
}

function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    
    notification.innerHTML = `
        <i class="${icons[type] || icons.info}"></i>
        <span>${message}</span>
        <button class="notification-close">&times;</button>
    `;
    
    notification.style.cssText = `
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 15px 20px;
        margin-bottom: 10px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        transform: translateX(100%);
        transition: transform 0.3s ease;
        background: ${getNotificationColor(type)};
        color: white;
        font-weight: 500;
    `;
    
    const closeBtn = notification.querySelector('.notification-close');
    closeBtn.style.cssText = `
        background: none;
        border: none;
        color: white;
        font-size: 1.2rem;
        cursor: pointer;
        margin-left: auto;
    `;
    
    document.getElementById('notification-container').appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Close functionality
    closeBtn.addEventListener('click', () => {
        closeNotification(notification);
    });
    
    // Auto close
    if (duration > 0) {
        setTimeout(() => {
            closeNotification(notification);
        }, duration);
    }
}

function getNotificationColor(type) {
    const colors = {
        success: '#28a745',
        error: '#dc3545',
        warning: '#ffc107',
        info: '#17a2b8'
    };
    return colors[type] || colors.info;
}

function closeNotification(notification) {
    notification.style.transform = 'translateX(100%)';
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 300);
}

// Draft saving functionality
function saveDraft(key, value) {
    try {
        localStorage.setItem(`magusa_taxi_draft_${key}`, value);
        showNotification('Taslak kaydedildi', 'info', 2000);
    } catch (e) {
        console.warn('Draft could not be saved:', e);
    }
}

function loadDraft(key, element) {
    try {
        const draft = localStorage.getItem(`magusa_taxi_draft_${key}`);
        if (draft && !element.value) {
            element.value = draft;
            showNotification('Kaydedilmiş taslak yüklendi', 'info', 3000);
        }
    } catch (e) {
        console.warn('Draft could not be loaded:', e);
    }
}

function clearDraft(key) {
    try {
        localStorage.removeItem(`magusa_taxi_draft_${key}`);
    } catch (e) {
        console.warn('Draft could not be cleared:', e);
    }
}

// Utility functions
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(() => {
            showNotification('Panoya kopyalandı!', 'success', 2000);
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Panoya kopyalandı!', 'success', 2000);
    }
}

// Add CSS for mobile sidebar
const mobileStyles = document.createElement('style');
mobileStyles.textContent = `
    @media (max-width: 768px) {
        .sidebar {
            position: fixed;
            left: -250px;
            top: 0;
            height: 100vh;
            z-index: 999;
            transition: left 0.3s ease;
        }
        
        .sidebar.mobile-open {
            left: 0;
        }
        
        .main-content {
            margin-left: 0;
        }
        
        .upload-form.drag-over {
            background: #e3f2fd;
            border: 2px dashed #3498db;
        }
        
        .notification {
            margin-bottom: 10px;
        }
        
        .error {
            border-color: #e74c3c !important;
            box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1) !important;
        }
    }
`;
document.head.appendChild(mobileStyles);

// Export functions for global use
window.showNotification = showNotification;
window.copyToClipboard = copyToClipboard;
window.formatFileSize = formatFileSize;
