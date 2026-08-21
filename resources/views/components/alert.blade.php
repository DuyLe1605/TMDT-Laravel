@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 animate-fade-in" role="alert">
        <div class="d-flex align-items-center">
            <i data-lucide="check-circle-2" class="me-2 text-success" style="width: 22px; height: 22px;"></i>
            <div>{{ session('success') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 animate-fade-in" role="alert">
        <div class="d-flex align-items-center">
            <i data-lucide="alert-octagon" class="me-2 text-danger" style="width: 22px; height: 22px;"></i>
            <div>{{ session('error') }}</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4 animate-fade-in" role="alert">
        <div class="d-flex align-items-start">
            <i data-lucide="alert-triangle" class="me-2 text-danger mt-1" style="width: 22px; height: 22px;"></i>
            <div>
                <strong class="d-block mb-1">Đã có lỗi xảy ra:</strong>
                <ul class="mb-0 ps-3">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
