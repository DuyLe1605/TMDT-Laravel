<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Voucher\StoreVoucherRequest;
use App\Http\Requests\Voucher\UpdateVoucherRequest;
use App\Models\Voucher;
use App\Models\VoucherUsage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminVoucherController extends Controller
{
    /**
     * Display a listing of vouchers with filters & stats.
     */
    public function index(Request $request): View
    {
        $query = Voucher::query();

        // Search by code or name
        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Filter by discount_type
        if ($type = $request->query('type')) {
            $query->where('discount_type', $type);
        }

        // Filter by status (active, inactive, expired)
        if ($status = $request->query('status')) {
            match ($status) {
                'active'   => $query->active(),
                'inactive' => $query->where('is_active', false),
                'expired'  => $query->where('expires_at', '<', now()),
                default    => null,
            };
        }

        $vouchers = $query->latest()->paginate(15)->withQueryString();

        // Stats for summary cards
        $stats = [
            'total'          => Voucher::count(),
            'active'         => Voucher::active()->count(),
            'total_used'     => VoucherUsage::count(),
            'total_discount' => VoucherUsage::sum('discount_amount'),
        ];

        return view('admin.vouchers.index', compact('vouchers', 'stats'));
    }

    /**
     * Show the form for creating a new voucher.
     */
    public function create(): View
    {
        return view('admin.vouchers.create');
    }

    /**
     * Store a newly created voucher.
     */
    public function store(StoreVoucherRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        $voucher = Voucher::create($data);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', "Mã giảm giá '{$voucher->code}' đã được tạo thành công.");
    }

    /**
     * Display the specified voucher and its redemption history.
     */
    public function show(Voucher $voucher): View
    {
        $voucher->loadCount('usages');
        $usages = $voucher->usages()
            ->with(['user', 'order'])
            ->latest()
            .paginate(15);

        $stats = [
            'total_redemptions' => $voucher->usages()->count(),
            'unique_users'      => $voucher->usages()->distinct('user_id')->count('user_id'),
            'total_saved'       => $voucher->usages()->sum('discount_amount'),
        ];

        return view('admin.vouchers.show', compact('voucher', 'usages', 'stats'));
    }

    /**
     * Show the form for editing the specified voucher.
     */
    public function edit(Voucher $voucher): View
    {
        return view('admin.vouchers.edit', compact('voucher'));
    }

    /**
     * Update the specified voucher.
     */
    public function update(UpdateVoucherRequest $request, Voucher $voucher): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', false);

        $voucher->update($data);

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', "Cập nhật mã giảm giá '{$voucher->code}' thành công.");
    }

    /**
     * Remove or deactivate the specified voucher.
     */
    public function destroy(Voucher $voucher): RedirectResponse
    {
        // If voucher has been used in orders, soft deactivate instead of deleting to preserve order history
        if ($voucher->usages()->exists()) {
            $voucher->update(['is_active' => false]);
            return redirect()
                ->route('admin.vouchers.index')
                ->with('info', "Voucher '{$voucher->code}' đã có lượt sử dụng thực tế nên được chuyển sang trạng thái Ngưng hoạt động thay vì xóa để bảo toàn lịch sử.");
        }

        $code = $voucher->code;
        $voucher->delete();

        return redirect()
            ->route('admin.vouchers.index')
            ->with('success', "Mã giảm giá '{$code}' đã được xóa vĩnh viễn.");
    }

    /**
     * Toggle the active status of a voucher via AJAX or POST.
     */
    public function toggleStatus(Voucher $voucher): JsonResponse|RedirectResponse
    {
        $voucher->update([
            'is_active' => !$voucher->is_active,
        ]);

        $statusText = $voucher->is_active ? 'Đã kích hoạt' : 'Đã vô hiệu hóa';

        if (request()->wantsJson()) {
            return response()->json([
                'success'   => true,
                'is_active' => $voucher->is_active,
                'message'   => "{$statusText} mã '{$voucher->code}'.",
            ]);
        }

        return back()->with('success', "{$statusText} mã '{$voucher->code}'.");
    }
}
