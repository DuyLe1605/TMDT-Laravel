<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('categories.index') }}">
            <i class="bi bi-bag-check-fill fs-4 text-warning"></i>
            <span>TMDT E-Commerce</span>
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto ps-lg-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                        <i class="bi bi-grid-fill me-1"></i> Danh mục (Categories)
                    </a>
                </li>
            </ul>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-white text-primary px-3 py-2 rounded-pill fw-semibold shadow-sm">
                    <i class="bi bi-shield-check me-1 text-success"></i> Laravel Architecture
                </span>
            </div>
        </div>
    </div>
</nav>
