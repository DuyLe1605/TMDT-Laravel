<?php

namespace App\Http\Controllers;

use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Exception;

class AddressController extends Controller
{
    public function __construct(
        protected AddressService $addressService
    ) {}

    /**
     * Display user addresses in account settings.
     */
    public function index(): View|JsonResponse
    {
        $addresses = $this->addressService->getUserAddresses(Auth::id());

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'addresses' => $addresses,
            ]);
        }

        return view('account.addresses', compact('addresses'));
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(StoreAddressRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $address = $this->addressService->createAddress(Auth::id(), $request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã thêm địa chỉ nhận hàng mới thành công!',
                    'address' => $address,
                    'full_address' => $address->full_address,
                ]);
            }

            return redirect()->back()->with('success', 'Đã thêm địa chỉ nhận hàng thành công!');
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Update the specified address.
     */
    public function update(UpdateAddressRequest $request, Address $address): JsonResponse|RedirectResponse
    {
        try {
            $updated = $this->addressService->updateAddress($address, $request->validated());

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã cập nhật địa chỉ thành công!',
                    'address' => $updated,
                    'full_address' => $updated->full_address,
                ]);
            }

            return redirect()->back()->with('success', 'Đã cập nhật địa chỉ thành công!');
        } catch (Exception $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Set address as default.
     */
    public function setDefault(Address $address): JsonResponse|RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        $this->addressService->setDefault($address);

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thiết lập làm địa chỉ mặc định!',
                'address' => $address,
            ]);
        }

        return redirect()->back()->with('success', 'Đã thiết lập làm địa chỉ mặc định!');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address): JsonResponse|RedirectResponse
    {
        if ($address->user_id !== Auth::id()) {
            abort(403);
        }

        try {
            $this->addressService->deleteAddress($address);

            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Đã xóa địa chỉ thành công!',
                ]);
            }

            return redirect()->back()->with('success', 'Đã xóa địa chỉ thành công!');
        } catch (Exception $e) {
            if (request()->wantsJson() || request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
