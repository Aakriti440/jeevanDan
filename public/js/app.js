/**
 * JeevanDaan - Main JavaScript
 */

// Portal Modal
function openPortal() {
    document.getElementById('loginPortal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closePortal() {
    document.getElementById('loginPortal').classList.remove('active');
    document.body.style.overflow = '';
}

// Close modal on outside click
document.addEventListener('click', function(e) {
    const modal = document.getElementById('loginPortal');
    if (e.target === modal) closePortal();
});

// Close modal on ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePortal();
});

// Mobile Menu Toggle
function toggleMobileMenu() {
    const navLinks = document.querySelector('.nav-links');
    navLinks.classList.toggle('mobile-active');
}

// ==================== MAIN INITIALIZATION ====================
document.addEventListener('DOMContentLoaded', function() {

    // File Upload Preview
    const uploadInputs = document.querySelectorAll('.upload-box input[type="file"]');
    uploadInputs.forEach(function(input) {
        const uploadArea = input.previousElementSibling;
        if (uploadArea) {
            uploadArea.addEventListener('click', () => input.click());
        }
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                const area = this.previousElementSibling;
                reader.onload = function(e) {
                    if (area) {
                        area.innerHTML = `<img src="${e.target.result}" alt="Preview"><span class="uploaded-badge"><i class="fas fa-check"></i> Selected</span>`;
                    }
                };
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Province-District Filter
    const provinceSelect = document.getElementById('province');
    const districtSelect = document.getElementById('district');

    if (provinceSelect && districtSelect) {
        const allOptions = Array.from(districtSelect.options);
        provinceSelect.addEventListener('change', function() {
            const selectedProvince = this.value;
            districtSelect.innerHTML = '<option value="">Select District</option>';
            allOptions.forEach(option => {
                if (!option.value || option.dataset.province === selectedProvince) {
                    districtSelect.appendChild(option.cloneNode(true));
                }
            });
        });
    }

    // Flash messages auto-hide
    document.querySelectorAll('.alert').forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });

    // DOB age auto-fill
    const dobField = document.getElementById('dob');
    if (dobField && dobField.value) autoFillAge(dobField.value, 'profileAge');

    // Email validation
    document.querySelectorAll('input[type="email"]').forEach(el => {
        el.addEventListener('blur', () => validateEmailField(el));
        el.addEventListener('input', () => {
            const err = el.parentNode.querySelector('.email-error');
            if (err) { err.remove(); el.style.borderColor = ''; }
        });
    });

    // Password toggle wrappers
    wrapPasswordFields();

    // Notification bell
    initNotificationBell();
});

// ====================== PASSWORD VISIBILITY ======================

/**
 * Toggle password visibility.
 * Supports two call signatures:
 *   togglePasswordVisibility(inputId, iconId)   — called from inline onclick in views
 *   togglePasswordVisibility(buttonElement)      — called internally from wrapPasswordFields
 */
function togglePasswordVisibility(inputIdOrBtn, iconId) {
    if (typeof inputIdOrBtn === 'string') {
        // Inline onclick signature: togglePasswordVisibility('myInput', 'myIcon')
        var input = document.getElementById(inputIdOrBtn);
        var icon  = document.getElementById(iconId);
        if (!input) return;
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
        } else {
            input.type = 'password';
            if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
        }
    } else {
        // Button-element signature (from wrapPasswordFields)
        var btn     = inputIdOrBtn;
        var wrapper = btn.closest('.password-field-wrapper');
        if (!wrapper) return;
        var input = wrapper.querySelector('input');
        if (!input) return;
        var icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            if (icon) { icon.classList.remove('fa-eye'); icon.classList.add('fa-eye-slash'); }
        } else {
            input.type = 'password';
            if (icon) { icon.classList.remove('fa-eye-slash'); icon.classList.add('fa-eye'); }
        }
    }
}

function wrapPasswordFields() {
    document.querySelectorAll('input[type="password"]').forEach(function(input) {
        // Skip if already wrapped or has an inline eye button nearby
        if (input.closest('.password-field-wrapper')) return;
        // Skip inputs that already have a manually placed toggle (detect by parent having position:relative with a span.fa-eye sibling)
        var parent = input.parentNode;
        if (parent && parent.querySelector('span [class*="fa-eye"]')) return;

        var wrapper = document.createElement('div');
        wrapper.className = 'password-field-wrapper';
        input.parentNode.insertBefore(wrapper, input);
        wrapper.appendChild(input);

        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn-pw-toggle';
        btn.innerHTML = '<i class="fas fa-eye"></i>';
        btn.addEventListener('click', function() { togglePasswordVisibility(this); });
        wrapper.appendChild(btn);
    });
}

// ====================== VALIDATION HELPERS ======================

function autoFillAge(dobValue, targetFieldId) {
    if (!dobValue) return;
    var today = new Date();
    var dob   = new Date(dobValue);
    var age   = today.getFullYear() - dob.getFullYear();
    var m     = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;
    var target = document.getElementById(targetFieldId);
    if (target) {
        target.value = age >= 0 ? age + ' years' : '';
    }
}

function validateEmailField(input) {
    var existingErr = input.parentNode.querySelector('.email-error');
    if (existingErr) existingErr.remove();
    var value = input.value.trim();
    if (value === '' && !input.required) return true;
    var valid = /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value);
    if (!valid) {
        var err = document.createElement('small');
        err.className = 'email-error';
        err.style.color = '#dc3545';
        err.innerHTML = '<i class="fas fa-exclamation-circle"></i> Please enter a valid email';
        input.parentNode.insertBefore(err, input.nextSibling);
        input.style.borderColor = '#dc3545';
        return false;
    }
    input.style.borderColor = '';
    return true;
}

function validateDOB(input) {
    var errEl  = document.getElementById('dobError');
    var dob    = new Date(input.value);
    var today  = new Date();
    var minAge = 18;
    if (!input.value) return true;

    if (dob > today) {
        if (errEl) { errEl.textContent = 'Date of birth cannot be in the future.'; errEl.style.display = 'block'; }
        input.style.borderColor = '#dc3545';
        return false;
    }

    var age = today.getFullYear() - dob.getFullYear();
    var m = today.getMonth() - dob.getMonth();
    if (m < 0 || (m === 0 && today.getDate() < dob.getDate())) age--;

    if (age < minAge) {
        if (errEl) { errEl.textContent = 'You must be at least ' + minAge + ' years old.'; errEl.style.display = 'block'; }
        input.style.borderColor = '#dc3545';
        return false;
    }

    if (errEl) errEl.style.display = 'none';
    input.style.borderColor = '';
    autoFillAge(input.value, 'profileAge');
    return true;
}

function validateLocation(input, errorId) {
    var errEl = document.getElementById(errorId);
    var value = input.value.trim();
    if (value === '' && !input.required) {
        if (errEl) errEl.style.display = 'none';
        input.style.borderColor = '';
        return true;
    }
    var hasLetter = /[a-zA-Z\u0900-\u097F]/.test(value);
    if (!hasLetter) {
        if (errEl) errEl.style.display = 'block';
        input.style.borderColor = '#dc3545';
        return false;
    }
    if (errEl) errEl.style.display = 'none';
    input.style.borderColor = '';
    return true;
}

// ====================== TERMS CHECKBOX ======================

/**
 * Called from user/profile.php submit button onclick.
 * Returns false to block submission if terms not agreed.
 */
function checkTerms() {
    var cb  = document.getElementById('agreeTerms');
    var err = document.getElementById('termsError');
    if (cb && !cb.checked) {
        if (err) err.style.display = 'block';
        cb.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return false;
    }
    if (err) err.style.display = 'none';
    return true;
}

// ====================== CONFIRM PASSWORD ======================

/**
 * Real-time confirm-password matching feedback.
 * Attach to the confirm field's input event.
 */
function checkPasswordMatch(confirmInput, originalInputId, errorId) {
    var original = document.getElementById(originalInputId);
    var errEl    = document.getElementById(errorId);
    if (!original || !errEl) return true;

    var match = original.value === confirmInput.value;
    if (!match && confirmInput.value.length > 0) {
        errEl.style.display = 'block';
        confirmInput.style.borderColor = '#dc3545';
    } else {
        errEl.style.display = 'none';
        confirmInput.style.borderColor = match && confirmInput.value ? '#28a745' : '';
    }
    return match || confirmInput.value.length === 0;
}

// ====================== NOTIFICATION BELL ======================

function initNotificationBell() {
    var bellBtn = document.getElementById('notifBellBtn');
    var dropdown = document.getElementById('notifDropdown');
    if (!bellBtn || !dropdown) return;

    bellBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        var isOpen = dropdown.classList.toggle('open');
        if (isOpen) loadBellNotifications();
    });

    document.addEventListener('click', function() {
        if (dropdown) dropdown.classList.remove('open');
    });

    dropdown.addEventListener('click', function(e) { e.stopPropagation(); });
}

function loadBellNotifications() {
    var dropdown = document.getElementById('notifDropdown');
    var list     = document.getElementById('notifList');
    if (!list) return;

    list.innerHTML = '<p style="text-align:center;padding:20px;color:#888;"><i class="fas fa-spinner fa-spin"></i> Loading...</p>';

    fetch(window._notifJsonUrl)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            var items = data.notifications || [];
            if (items.length === 0) {
                list.innerHTML = '<p style="text-align:center;padding:20px;color:#888;"><i class="fas fa-bell-slash"></i> No notifications</p>';
                return;
            }
            var html = '';
            items.forEach(function(n) {
                var icon = n.type === 'blood_request' ? 'tint' : (n.type === 'verification' ? 'check-circle' : (n.type === 'appointment' ? 'calendar' : 'bell'));
                html += '<div class="notif-bell-item' + (n.is_read == 0 ? ' unread' : '') + '">';
                html += '<i class="fas fa-' + icon + ' notif-bell-icon"></i>';
                html += '<div class="notif-bell-content">';
                html += '<strong>' + escapeHtml(n.title) + '</strong>';
                html += '<p>' + escapeHtml(n.message) + '</p>';
                html += '<small>' + timeAgo(n.created_at) + '</small>';
                html += '</div>';
                if (n.link) {
                    html += '<a href="' + n.link + '" class="notif-bell-action"><i class="fas fa-arrow-right"></i></a>';
                }
                html += '</div>';
            });
            list.innerHTML = html;
        })
        .catch(function() {
            list.innerHTML = '<p style="text-align:center;padding:20px;color:#c0392b;">Failed to load.</p>';
        });
}

function escapeHtml(str) {
    if (!str) return '';
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function timeAgo(dateStr) {
    var d     = new Date(dateStr);
    var now   = new Date();
    var secs  = Math.floor((now - d) / 1000);
    if (secs < 60)   return 'Just now';
    if (secs < 3600) return Math.floor(secs / 60) + 'm ago';
    if (secs < 86400) return Math.floor(secs / 3600) + 'h ago';
    return Math.floor(secs / 86400) + 'd ago';
}

console.log('JeevanDaan loaded successfully!');
