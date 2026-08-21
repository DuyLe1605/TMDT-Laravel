/**
 * TMDT Bespoke Form Validator & Floating Toast System
 * Inspired by Zod schema validation: Disables native browser validation,
 * provides reactive inline feedback and animated floating toast notifications.
 */

(function () {
    'use strict';

    /**
     * Dynamic Floating Toast Generator
     * @param {'success'|'error'|'warning'|'info'} type 
     * @param {string} title 
     * @param {string|string[]} content 
     */
    window.showToast = function (type = 'error', title = 'Thông báo', content = '') {
        let container = document.querySelector('.toast-container-custom');
        if (!container) {
            container = document.createElement('div');
            container.className = 'toast-container-custom';
            document.body.appendChild(container);
        }

        const toastId = 'toast-' + Math.random().toString(36).substr(2, 9);
        const toastEl = document.createElement('div');
        toastEl.id = toastId;
        toastEl.className = `toast-modern toast-modern-${type === 'error' ? 'error' : (type === 'success' ? 'success' : 'error')} shadow-lg`;
        toastEl.setAttribute('role', 'alert');

        let iconName = 'alert-triangle';
        if (type === 'success') iconName = 'check';
        if (type === 'error') iconName = 'alert-circle';
        if (type === 'info') iconName = 'info';

        let bodyHtml = '';
        if (Array.isArray(content)) {
            bodyHtml = `<ul class="mb-0 ps-3 mt-1 small">${content.map(item => `<li>${item}</li>`).join('')}</ul>`;
        } else {
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

        // Re-run lucide icons on the newly created toast
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Auto dismiss after 5 seconds
        setTimeout(() => {
            dismissToast(toastId);
        }, 5000);
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
     * Clear inline error on a single form control
     */
    function clearFieldError(input) {
        input.classList.remove('is-invalid');
        const parent = input.closest('.mb-3, .mb-4, .col-md-4, .col-md-6, .col-md-8, .col-12, div');
        if (parent) {
            const feedbacks = parent.querySelectorAll('.dynamic-invalid-feedback');
            feedbacks.forEach(el => el.remove());
        }
    }

    /**
     * Set inline error on a single form control
     */
    function setFieldError(input, message) {
        input.classList.add('is-invalid');
        const parent = input.closest('.mb-3, .mb-4, .col-md-4, .col-md-6, .col-md-8, .col-12, div') || input.parentElement;
        if (parent) {
            // Remove previous dynamic feedback if exists
            const existing = parent.querySelectorAll('.dynamic-invalid-feedback');
            existing.forEach(el => el.remove());

            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback dynamic-invalid-feedback d-block mt-1';
            feedback.innerText = message;
            input.parentElement.appendChild(feedback);
        }
    }

    /**
     * Validate a single form input element
     */
    function validateField(input) {
        const value = input.value !== undefined ? input.value.trim() : '';
        const isRequired = input.hasAttribute('required') || input.dataset.ruleRequired === 'true';
        const fieldLabel = (input.closest('div')?.querySelector('label')?.innerText || input.placeholder || 'Trường này')
            .replace('Bắt buộc', '')
            .replace('Tùy chọn', '')
            .trim();

        // 1. Required Check
        if (isRequired && !value) {
            setFieldError(input, `${fieldLabel} không được để trống.`);
            return `${fieldLabel} không được để trống.`;
        }

        // If not required and empty, pass
        if (!isRequired && !value) {
            clearFieldError(input);
            return null;
        }

        // 2. Numeric / Min / Max Check
        if (input.type === 'number' || input.dataset.type === 'numeric') {
            const num = parseFloat(value);
            if (isNaN(num)) {
                setFieldError(input, `${fieldLabel} phải là số hợp lệ.`);
                return `${fieldLabel} phải là số hợp lệ.`;
            }

            const min = input.getAttribute('min');
            if (min !== null && num < parseFloat(min)) {
                setFieldError(input, `${fieldLabel} không được nhỏ hơn ${min}.`);
                return `${fieldLabel} không được nhỏ hơn ${min}.`;
            }

            const max = input.getAttribute('max');
            if (max !== null && num > parseFloat(max)) {
                setFieldError(input, `${fieldLabel} không được vượt quá ${max}.`);
                return `${fieldLabel} không được vượt quá ${max}.`;
            }
        }

        // 3. Sale price <= regular price check
        if (input.id === 'sale_price' && value) {
            const priceInput = document.getElementById('price');
            if (priceInput && priceInput.value) {
                const regularPrice = parseFloat(priceInput.value);
                const salePrice = parseFloat(value);
                if (salePrice > regularPrice) {
                    setFieldError(input, 'Giá khuyến mãi phải nhỏ hơn hoặc bằng giá gốc.');
                    return 'Giá khuyến mãi không được lớn hơn giá bán gốc.';
                }
            }
        }

        // 4. URL Check
        if (input.type === 'url' && value) {
            try {
                new URL(value);
            } catch (_) {
                setFieldError(input, 'Đường dẫn liên kết (URL) không đúng định dạng.');
                return 'Đường dẫn ảnh/liên kết không đúng định dạng URL.';
            }
        }

        clearFieldError(input);
        return null;
    }

    /**
     * Attach validator listeners to all forms
     */
    function initFormValidators() {
        // Disable browser native validation on all forms
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.setAttribute('novalidate', 'true');

            // Listen to real-time input / change to clear errors immediately
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('input', () => {
                    if (input.classList.contains('is-invalid')) {
                        validateField(input);
                    }
                });
                input.addEventListener('change', () => validateField(input));
            });

            // Intercept form submit
            form.addEventListener('submit', function (e) {
                const formInputs = form.querySelectorAll('input, select, textarea');
                const errors = [];

                formInputs.forEach(input => {
                    // Ignore hidden fields or CSRF token
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

                    // Trigger animated toast with full error summary
                    window.showToast('error', 'Kiểm tra lại dữ liệu nhập', errors);
                }
            });
        });
    }

    document.addEventListener('DOMContentLoaded', initFormValidators);
})();
