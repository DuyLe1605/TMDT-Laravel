@if (session('success') || session('error') || $errors->any())
    <div class="toast-container-custom">
        @if (session('success'))
            <div class="toast-modern toast-modern-success shadow-lg" role="alert" id="toast-success">
                <div class="toast-icon-wrapper">
                    <i data-lucide="check" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="toast-title">Thao tác thành công</div>
                    <div class="toast-desc">{{ session('success') }}</div>
                </div>
                <button type="button" class="btn-close ms-2" onclick="dismissToast('toast-success')" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="toast-modern toast-modern-error shadow-lg" role="alert" id="toast-error">
                <div class="toast-icon-wrapper">
                    <i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="toast-title">Đã xảy ra lỗi</div>
                    <div class="toast-desc">{{ session('error') }}</div>
                </div>
                <button type="button" class="btn-close ms-2" onclick="dismissToast('toast-error')" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="toast-modern toast-modern-error shadow-lg" role="alert" id="toast-validation">
                <div class="toast-icon-wrapper">
                    <i data-lucide="alert-triangle" style="width: 18px; height: 18px;"></i>
                </div>
                <div class="flex-grow-1">
                    <div class="toast-title">Kiểm tra lại dữ liệu nhập</div>
                    <div class="toast-desc">
                        <ul class="mb-0 ps-3 mt-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <button type="button" class="btn-close ms-2" onclick="dismissToast('toast-validation')" aria-label="Close"></button>
            </div>
        @endif
    </div>

    <script>
        function dismissToast(id) {
            const toastEl = document.getElementById(id);
            if (toastEl) {
                toastEl.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                toastEl.style.opacity = '0';
                toastEl.style.transform = 'translateY(-10px) scale(0.95)';
                setTimeout(() => toastEl.remove(), 250);
            }
        }

        // Auto dismiss success toast after 4.5 seconds
        setTimeout(() => {
            dismissToast('toast-success');
        }, 4500);
    </script>
@endif
