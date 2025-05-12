document.addEventListener('DOMContentLoaded', () => {
    setupNavigation();
    setupProfileForm();
    setupAvatarUpload();
    setupCopyLink();
    setupSocialLinks();
});

function setupNavigation() {
    const navLinks = document.querySelectorAll('.nav-links a[data-section]');
    const sections = document.querySelectorAll('.dashboard-section');
    
    navLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Remove active class from all links and sections
            navLinks.forEach(l => l.classList.remove('active'));
            sections.forEach(s => s.classList.remove('active'));
            
            // Add active class to clicked link and corresponding section
            link.classList.add('active');
            const sectionId = link.getAttribute('data-section');
            document.getElementById(sectionId).classList.add('active');
        });
    });
}

function setupProfileForm() {
    const form = document.getElementById('profileForm');
    if (!form) return;
    
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const submitButton = form.querySelector('.save-button');
        const originalText = submitButton.innerHTML;
        
        try {
            submitButton.disabled = true;
            submitButton.innerHTML = '<span class="spinner"></span> Saving...';
            
            const formData = new FormData(form);
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData
            });
            
            const result = await response.json();
            
            if (result.success) {
                showNotification('Changes saved successfully', 'success');
                // Refresh page after 1 second
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showNotification(result.message || 'Error saving changes', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('An unexpected error occurred', 'error');
        } finally {
            submitButton.disabled = false;
            submitButton.innerHTML = originalText;
        }
    });
}

function setupAvatarUpload() {
    const avatarInput = document.getElementById('avatar');
    const avatarPreview = document.getElementById('avatarPreview');
    const profileAvatar = document.querySelector('.profile-avatar');
    
    if (!avatarInput || !avatarPreview || !profileAvatar) return;
    
    profileAvatar.addEventListener('click', () => {
        avatarInput.click();
    });
    
    avatarInput.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (!file) return;
        
        if (!file.type.startsWith('image/')) {
            showNotification('Please select an image file', 'error');
            return;
        }
        
        const reader = new FileReader();
        reader.onload = (e) => {
            avatarPreview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    });
}

function setupCopyLink() {
    const copyButton = document.querySelector('.copy-link');
    const profileLink = document.getElementById('profileLink');
    
    if (!copyButton || !profileLink) return;
    
    copyButton.addEventListener('click', () => {
        profileLink.select();
        document.execCommand('copy');
        showNotification('Link copied to clipboard', 'success');
    });
}

function setupSocialLinks() {
    const addLinkBtn = document.querySelector('.add-link-btn');
    const modal = document.getElementById('linkModal');
    const closeModal = document.querySelector('.close-modal');
    const linkForm = document.getElementById('linkForm');
    
    if (addLinkBtn) {
        addLinkBtn.addEventListener('click', () => {
            modal.classList.add('active');
        });
    }
    
    if (closeModal) {
        closeModal.addEventListener('click', () => {
            modal.classList.remove('active');
        });
    }
    
    if (linkForm) {
        linkForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(linkForm);
            
            try {
                const response = await fetch('add_link.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                showNotification(data.success ? 'success' : 'error', data.message);
                
                if (data.success) {
                    modal.classList.remove('active');
                    location.reload();
                }
            } catch (error) {
                showNotification('error', 'Wystąpił błąd podczas dodawania linku');
            }
        });
    }
    
    // Obsługa edycji i usuwania linków
    document.querySelectorAll('.edit-link').forEach(button => {
        button.addEventListener('click', async (e) => {
            const linkId = e.target.dataset.id;
            // TODO: Implementacja edycji linku
        });
    });
    
    document.querySelectorAll('.delete-link').forEach(button => {
        button.addEventListener('click', async (e) => {
            if (!confirm('Czy na pewno chcesz usunąć ten link?')) return;
            
            const linkId = e.target.dataset.id;
            try {
                const response = await fetch('delete_link.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: linkId })
                });
                
                const data = await response.json();
                showNotification(data.success ? 'success' : 'error', data.message);
                
                if (data.success) {
                    location.reload();
                }
            } catch (error) {
                showNotification('error', 'Wystąpił błąd podczas usuwania linku');
            }
        });
    });
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