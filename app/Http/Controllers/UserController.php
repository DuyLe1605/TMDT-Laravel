<?php

namespace App\Http\Controllers;

use App\Constants\AppConstants;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * UserController constructor with Dependency Injection.
     *
     * @param UserService $userService
     */
    public function __construct(
        protected UserService $userService
    ) {}

    /**
     * Display a listing of the users.khoang
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $role = $request->filled('role') ? $request->input('role') : null;
        $search = $request->input('search');
        $sort = $request->input('sort', 'created_desc');

        $users = $this->userService->getPaginatedUsers($role, $search, $sort);
        $totalAdmins = User::where('role', 'admin')->count();
        $totalCustomers = User::where('role', 'customer')->count();

        return view('admin.users.index', compact('users', 'role', 'search', 'sort', 'totalAdmins', 'totalCustomers'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return View
     */
    public function create(): View
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->createUser($request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_USER_CREATED);
    }

    /**
     * Display the specified user profile.
     *
     * @param User $user
     * @return View
     */
    public function show(User $user): View
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param User $user
     * @return View
     */
    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->updateUser($user, $request->validated());

        return redirect()
            ->route('admin.users.index')
            ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_USER_UPDATED);
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->userService->deleteUser($user);

            return redirect()
                ->route('admin.users.index')
                ->with(AppConstants::FLASH_SUCCESS, AppConstants::MSG_USER_DELETED);
        } catch (ValidationException $e) {
            $errorMsg = $e->validator->errors()->first('user') ?: 'Không thể xóa tài khoản này.';
            return redirect()
                ->route('admin.users.index')
                ->with(AppConstants::FLASH_ERROR, $errorMsg);
        }
    }
}
