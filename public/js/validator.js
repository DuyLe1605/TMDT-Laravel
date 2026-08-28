/**
 * TMDT Bespoke Form Validator & Floating Toast System
 * Handles Client-Side Validation for Auth, Storefront, and Admin forms:
 * - Disables native browser tooltip popups (novalidate)
 * - Validates Required, Email regex, Password length, Password confirmation match, Numbers/Min/Max
 * - Injects clean, beautiful inline error messages below each field
 * - Live real-time validation on typing/blur
 */

(function () {
    'use strict';

    /**
     * Dynamic Floating Toast Generator
     * Supports:
     * - showToast("Message text", "success")
     * - showToast("success", "Title", "Content")
     */
    window.showToast = function (arg1 = 'Thông báo', arg2 = 'info', arg3 = '') {
        let type = 'info';
        let title = 'Thông báo';
        let content = '';

        const validTypes = ['success', 'error', 'warning', 'info'];

        if (validTypes.includes(arg1)) {
            type = arg1;
            title = arg2 || 'Thông báo';
            content = arg3 || '';
        } else {
            // First argument is the message content or title
            content = arg1;
            type = validTypes.includes(arg2) ? arg2 : 'success';
            title = type === 'success' ? 'Thành công' : (type === 'error' ? 'Có lỗi xảy ra' : (type === 'warning' ? 'Cảnh báo' : 'Thông báo'));
        }

        let container = document.querySelector('.toast-container-custom');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container-custom';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Math.random().toString(36).substr(2, 9);
        const toastEl = document.createElement('div');
        toastEl.id = toastId;
        toastEl.className = `toast-modern toast-modern-${type} shadow-lg`;
        toastEl.setAttribute('role', 'alert');

        let iconName = 'alert-triangle';
        if (type === 'success') iconName = 'check-circle-2';
        if (type === 'error') iconName = 'alert-circle';
        if (type === 'warning') iconName = 'alert-triangle';
        if (type === 'info') iconName = 'info';

        let bodyHtml = '';
        if (Array.isArray(content)) {
            bodyHtml = `<ul class="mb-0 ps-3 mt-1 small">${content.map(item => `<li>${item}</li>`).join('')}</ul>`;
        } else if (content) {
            bodyHtml = `<div class="toast-desc">${content}</div>`;
        }

        toastEl.innerHTML = `
            <div class="toast-icon-wrapper">
                <i data-lucide="${iconName}" style="width: 18px; height: 18px;"></i>
            </div>
            <div class="flex-grow-1">
                <div class="toast-title">${title}</div>
                ${bodyHtml}
            </div>
            <button type="button" class="btn-close ms-2" onclick="dismissToast('${toastId}')" aria-label="Close"></button>
        `;

        container.appendChild(toastEl);

        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        setTimeout(() => {
            dismissToast(toastId);
        }, 4000);
    };

    window.dismissToast = function (id) {
        const toastEl = document.getElementById(id);
        if (toastEl) {
            toastEl.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
            toastEl.style.opacity = '0';
            toastEl.style.transform = 'translateY(-12px) scale(0.95)';
            setTimeout(() => toastEl.remove(), 250);
        }
    };

    /**
     * Get the outer form group element containing both input and its error message
     */
    function getFieldGroup(input) {
        return input.closest('.form-group-modern') || 
               input.closest('.mb-4') || 
               input.closest('.mb-3') || 
               input.closest('.col-12') || 
               input.closest('.col-md-6') || 
               input.closest('.col-md-4') || 
               input.parentElement;
    }

    /**
     * Clear inline error on a single form control (both client and server errors)
     */
    function clearFieldError(input) {
        input.classList.remove('is-invalid');
        const group = getFieldGroup(input);
        if (group) {
            const feedbacks = group.querySelectorAll('.dynamic-invalid-feedback, .server-invalid-feedback, .invalid-feedback-msg');
            feedbacks.forEach(el => el.remove());
        }
    }

    /**
     * Set inline error on a single form control below the input/input-group
     */
    function setFieldError(input, message) {
        input.classList.add('is-invalid');
        const group = getFieldGroup(input);
        if (group) {
            // Remove ALL existing feedbacks inside the entire field group to prevent duplicates
            const existing = group.querySelectorAll('.dynamic-invalid-feedback, .server-invalid-feedback, .invalid-feedback-msg');
            existing.forEach(el => el.remove());

            const feedback = document.createElement('div');
            feedback.className = 'text-danger small mt-1.5 d-flex align-items-center gap-1.5 dynamic-invalid-feedback invalid-feedback-msg';
            feedback.innerHTML = `
                <i data-lucide="alert-circle" style="width: 15px; height: 15px; flex-shrink: 0;"></i>
                <span>${message}</span>
            `;

            // Insert after input-group-modern if present, otherwise after input
            const targetContainer = input.closest('.input-group-modern') || input;
            targetContainer.insertAdjacentElement('afterend', feedback);

            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }
    }

    /**
     * Validate a single form input element with comprehensive client-side rules
     */
    function validateField(input) {
        // Skip hidden, tokens, submit buttons, checkboxes with no requirements
        if (input.type === 'hidden' || input.type === 'submit' || input.type === 'button') return null;

        const value = input.value !== undefined ? input.value.trim() : '';
        const name = input.name || input.id || '';
        const isRequired = input.hasAttribute('required') || input.dataset.ruleRequired === 'true' || ['email', 'password', 'name', 'password_confirmation'].includes(name);

        const labelEl = input.closest('.form-group-modern, .mb-3, .mb-4, div')?.querySelector('label');
        let fieldLabel = labelEl ? labelEl.innerText.replace('*', '').replace('Bắt buộc', '').replace('Tùy chọn', '').trim() : (input.placeholder || 'Trường này');

        // 1. Required Check
        if (isRequired && !value) {
            const msg = `Vui lòng nhập ${fieldLabel.toLowerCase()}.`;
            setFieldError(input, msg);
            return msg;
        }

        // If field is optional and empty, pass
        if (!value) {
            clearFieldError(input);
            return null;
        }

        // 2. Email Format Check
        if (input.type === 'email' || name === 'email') {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                const msg = 'Địa chỉ email không đúng định dạng (ví dụ: name@example.com).';
                setFieldError(input, msg);
                return msg;
            }
        }

        // 3. Password Length Check (min 8 chars)
        if (input.type === 'password' && name === 'password') {
            if (value.length < 8) {
                const msg = 'Mật khẩu phải có ít nhất 8 ký tự.';
                setFieldError(input, msg);
                return msg;
            }
        }

        // 4. Password Confirmation Match Check
        if (name === 'password_confirmation') {
            const form = input.closest('form');
            const passInput = form?.querySelector('input[name="password"]');
            if (passInput && value !== passInput.value.trim()) {
                const msg = 'Xác nhận mật khẩu không khớp với mật khẩu đã nhập.';
                setFieldError(input, msg);
                return msg;
            }
        }

        // 5. Numeric / Min / Max Check
        if (input.type === 'number' || input.dataset.type === 'numeric') {
            const num = parseFloat(value);
            if (isNaN(num)) {
                const msg = `${fieldLabel} phải là số hợp lệ.`;
                setFieldError(input, msg);
                return msg;
            }

            const min = input.getAttribute('min');
            if (min !== null && num < parseFloat(min)) {
                const msg = `${fieldLabel} không được nhỏ hơn ${min}.`;
                setFieldError(input, msg);
                return msg;
            }

            const max = input.getAttribute('max');
            if (max !== null && num > parseFloat(max)) {
                const msg = `${fieldLabel} không được vượt quá ${max}.`;
                setFieldError(input, msg);
                return msg;
            }
        }

        // 6. Sale price <= regular price check
        if (input.id === 'sale_price' && value) {
            const priceInput = document.getElementById('price');
            if (priceInput && priceInput.value) {
                const regularPrice = parseFloat(priceInput.value);
                const salePrice = parseFloat(value);
                if (salePrice > regularPrice) {
                    const msg = 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.';
                    setFieldError(input, msg);
                    return msg;
                }
            }
        }

        // 7. URL Check
        if (input.type === 'url' && value) {
            try {
                new URL(value);
            } catch (_) {
                const msg = 'Đường dẫn liên kết (URL) không đúng định dạng.';
                setFieldError(input, msg);
                return msg;
            }
        }

        clearFieldError(input);
        return null;
    }

    /**
     * Attach validator listeners to all forms
     */
    function initFormValidators() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            // Disable default browser tooltips
            form.setAttribute('novalidate', 'true');

            // Real-time input validation: clear error or re-validate as user types
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        validateField(input);
                    }
                });
                input.addEventListener('blur', () => {
                    if (input.value && input.value.trim().length > 0) {
                        validateField(input);
                    }
                });
                input.addEventListener('change', () => validateField(input));
            });

            // Intercept form submit and validate all fields client-side first
            form.addEventListener('submit', function (e) {
                // Ignore search-only GET forms with empty inputs
                if (form.method.toUpperCase() === 'GET' && form.id === 'shopFilterForm') return;

                const formInputs = form.querySelectorAll('input, select, textarea');
                const errors = [];

                formInputs.forEach(input => {
                    if (input.type === 'hidden' || input.name === '_token' || input.name === '_method') return;

                    const err = validateField(input);
                    if (err) {
                        errors.push(err);
                    }
                });

                if (errors.length > 0) {
                    e.preventDefault();
                    e.stopPropagation();

                    // Focus first invalid element
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }

                    // Optional toast alert
                    if (typeof window.showToast === 'function' && errors.length > 1) {
                        window.showToast('error', 'Vui lòng kiểm tra lại thông tin', errors);
                    }
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initFormValidators);
})();
