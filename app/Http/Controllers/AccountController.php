<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\AddressService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AccountController extends Controller
{
    public function __construct(
        protected AddressService $addressService
    ) {}

    /**
     * Display order history list for current user.
     */
    public function orders(): View
    {
        $orders = Order::where('user_id', Auth::id())
            ->with(['items'])
            ->latest()
            ->paginate(10);

        return view('account.orders', compact('orders'));
    }

    /**
     * Display order details.
     */
    public function orderDetail(Order $order): View
    {
        if ($order->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        $order->load(['items.product']);

        return view('account.order-detail', compact('order'));
    }

    /**
     * Display address book in account settings.
     */
    public function addresses(): View
    {
        $addresses = $this->addressService->getUserAddresses(Auth::id());

        return view('account.addresses', compact('addresses'));
    }
}
