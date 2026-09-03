<?php

namespace App\Http\Controllers;

use App\Constants\AppConstants;
use App\Http\Requests\Brand\StoreBrandRequest;
use App\Http\Requests\Brand\UpdateBrandRequest;
use App\Models\Brand;
use App\Services\BrandService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BrandController extends Controller
{
    public function __construct(
        protected BrandService $brandService
    ) {}

    /**
     * Display a listing of brands.
     */
    public function index(Request $request): View
    {
        $search = $request->input('search');
        $brands = $this->brandService->getPaginatedBrands($search);

        return view('brands.index', compact('brands', 'search'));
    }

    /**
     * Show form for creating a new brand.
     */
    public function create(): View
    {
        return view('brands.create');
    }

    /**
     * Store a newly created brand.
     */
    public function store(StoreBrandRequest $request): RedirectResponse
    {
        $this->brandService->createBrand($request->validated());

        return redirect()
            ->route('admin.brands.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_BRAND_CREATED);
    }

    /**
     * Display the specified brand.
     */
    public function show(Brand $brand): View
    {
        $brand->load(['products' => function ($q) {
            $q->latest()->take(10);
        }]);

        return view('brands.show', compact('brand'));
    }

    /**
     * Show form for editing brand.
     */
    public function edit(Brand $brand): View
    {
        return view('brands.edit', compact('brand'));
    }

    /**
     * Update brand.
     */
    public function update(UpdateBrandRequest $request, Brand $brand): RedirectResponse
    {
        $this->brandService->updateBrand($brand, $request->validated());

        return redirect()
            ->route('admin.brands.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_BRAND_UPDATED);
    }

    /**
     * Remove brand.
     */
    public function destroy(Brand $brand): RedirectResponse
    {
        $this->brandService->deleteBrand($brand);

        return redirect()
            ->route('admin.brands.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_BRAND_DELETED);
    }
}
