<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GiftOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGiftOptionController extends Controller
{
    /**
     * Display a listing of gift options (wrapping papers & greeting cards).
     */
    public function index(Request $request): View
    {
        $query = GiftOption::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($type = $request->query('type')) {
            $query->where('type', $type);
        }

        if ($status = $request->query('status')) {
            if ($status === 'active') {
                $query->where('is_active', true);
            } elseif ($status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        $giftOptions = $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc')->paginate(15)->withQueryString();

        $stats = [
            'total'       => GiftOption::count(),
            'papers'      => GiftOption::where('type', 'paper')->count(),
            'cards'       => GiftOption::where('type', 'card')->count(),
            'active'      => GiftOption::where('is_active', true)->count(),
        ];

        return view('admin.gift-options.index', compact('giftOptions', 'stats'));
    }

    /**
     * Show form to create a new gift option.
     */
    public function create(): View
    {
        return view('admin.gift-options.create');
    }

    /**
     * Store a newly created gift option in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type'        => 'required|in:paper,card',
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:50|unique:gift_options,code',
            'image'       => 'nullable|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        GiftOption::create($validated);

        return redirect()->route('admin.gift-options.index')->with('success', 'Đã thêm tùy chọn quà tặng mới thành công!');
    }

    /**
     * Show form to edit an existing gift option.
     */
    public function edit(GiftOption $giftOption): View
    {
        return view('admin.gift-options.edit', compact('giftOption'));
    }

    /**
     * Update the specified gift option in storage.
     */
    public function update(Request $request, GiftOption $giftOption): RedirectResponse
    {
        $validated = $request->validate([
            'type'        => 'required|in:paper,card',
            'name'        => 'required|string|max:100',
            'code'        => 'required|string|max:50|unique:gift_options,code,' . $giftOption->id,
            'image'       => 'nullable|string|max:255',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:255',
            'sort_order'  => 'nullable|integer|min:0',
            'is_active'   => 'nullable|boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $giftOption->update($validated);

        return redirect()->route('admin.gift-options.index')->with('success', 'Đã cập nhật tùy chọn quà tặng thành công!');
    }

    /**
     * Remove the specified gift option from storage.
     */
    public function destroy(GiftOption $giftOption): RedirectResponse
    {
        $giftOption->delete();
        return redirect()->route('admin.gift-options.index')->with('success', 'Đã xóa tùy chọn quà tặng thành công!');
    }

    /**
     * Toggle active status via AJAX or standard form.
     */
    public function toggleStatus(GiftOption $giftOption, Request $request): JsonResponse|RedirectResponse
    {
        $giftOption->update(['is_active' => !$giftOption->is_active]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success'   => true,
                'is_active' => $giftOption->is_active,
                'message'   => 'Đã cập nhật trạng thái ' . ($giftOption->is_active ? 'kích hoạt' : 'tạm dừng'),
            ]);
        }

        return back()->with('success', 'Đã cập nhật trạng thái thành công!');
    }
}
