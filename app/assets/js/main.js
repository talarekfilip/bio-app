document.addEventListener('DOMContentLoaded', () => {
    setupStars();
    setupForms();
    setupNotifications();
});

function setupStars() {
    const starsContainer = document.querySelector('.stars');
    if (!starsContainer) return;

    const starCount = 100;
    for (let i = 0; i < starCount; i++) {
        const star = document.createElement('div');
        star.className = 'star';
        star.style.left = `${Math.random() * 100}%`;
        star.style.top = `${Math.random() * 100}%`;
        star.style.width = `${Math.random() * 3 + 1}px`;
        star.style.height = star.style.width;
        star.style.animationDelay = `${Math.random() * 3}s`;
        star.style.setProperty('--duration', `${Math.random() * 3 + 2}s`);
        starsContainer.appendChild(star);
    }
}

function setupForms() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('error');
                    showFieldError(field, 'To pole jest wymagane');
                } else {
                    field.classList.remove('error');
                    clearFieldError(field);
                }
            });
            
            const password = form.querySelector('#password');
            const confirmPassword = form.querySelector('#confirmPassword');
            
            if (password && confirmPassword) {
                if (password.value !== confirmPassword.value) {
                    isValid = false;
                    confirmPassword.classList.add('error');
                    showFieldError(confirmPassword, 'Hasła nie są identyczne');
                }
            }
            
            if (!isValid) {
                showNotification('Wypełnij wszystkie wymagane pola poprawnie', 'error');
                return;
            }
            
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            try {
                submitButton.disabled = true;
                submitButton.innerHTML = '<span class="spinner"></span> Przetwarzanie...';
                
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    showNotification(result.message || 'Sukces!', 'success');
                    if (result.redirect) {
                        setTimeout(() => {
                            window.location.href = result.redirect;
                        }, 1000);
                    }
                } else {
                    showNotification(result.message || 'Wystąpił błąd', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Wystąpił nieoczekiwany błąd', 'error');
            } finally {
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            }
        });
        
        const inputs = form.querySelectorAll('input');
        inputs.forEach(input => {
            input.addEventListener('input', () => {
                if (input.hasAttribute('required')) {
                    if (!input.value.trim()) {
                        input.classList.add('error');
                        showFieldError(input, 'To pole jest wymagane');
                    } else {
                        input.classList.remove('error');
                        clearFieldError(input);
                    }
                }
            });
        });
    });
}

function showFieldError(field, message) {
    const formGroup = field.closest('.form-group');
    let errorElement = formGroup.querySelector('.error-message');
    
    if (!errorElement) {
        errorElement = document.createElement('div');
        errorElement.className = 'error-message';
        formGroup.appendChild(errorElement);
    }
    
    errorElement.textContent = message;
}

function clearFieldError(field) {
    const formGroup = field.closest('.form-group');
    const errorElement = formGroup.querySelector('.error-message');
    
    if (errorElement) {
        errorElement.remove();
    }
}

function setupNotifications() {
    if (!document.querySelector('.notification-container')) {
        const container = document.createElement('div');
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
}

function showNotification(message, type = 'success') {
    const container = document.querySelector('.notification-container');
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    container.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Copy to clipboard functionality
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showNotification('Copied to clipboard!', 'success');
    }).catch(() => {
        showNotification('Failed to copy', 'error');
    });
}

// Handle file uploads
function handleFileUpload(input, previewElement) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = (e) => {
            if (previewElement) {
                previewElement.src = e.target.result;
            }
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Debounce function for performance
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Handle responsive navigation
function setupResponsiveNav() {
    const navToggle = document.querySelector('.nav-toggle');
    const nav = document.querySelector('.nav');
    
    if (navToggle && nav) {
        navToggle.addEventListener('click', () => {
            nav.classList.toggle('active');
        });
    }
}

// Initialize all components
setupResponsiveNav(); 