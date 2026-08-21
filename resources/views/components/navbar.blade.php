<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('categories.index') }}">
            <i data-lucide="shopping-bag" class="text-warning" style="width: 24px; height: 24px;"></i>
            <span>TMDT E-Commerce</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto ps-lg-3">
                <li class="nav-item">
                    <a class="nav-link d-inline-flex align-items-center gap-2 {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                        <i data-lucide="folder-tree" style="width: 18px; height: 18px;"></i>
                        <span>Danh mục (Categories)</span>
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-semibold shadow-sm d-inline-flex align-items-center gap-1">
                    <i data-lucide="shield-check" class="text-success" style="width: 16px; height: 16px;"></i>
                    <span>Laravel Architecture</span>
                </span>
            </div>
        </div>
    </div>
</nav>
