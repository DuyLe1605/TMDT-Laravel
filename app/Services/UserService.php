<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UserService
{
    /**
     * Get paginated users with optional role and search filters.
     *
     * @param string|null $role
     * @param string|null $search
     * @param string|null $sort
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedUsers(
        ?string $role = null,
        ?string $search = null,
        ?string $sort = 'created_desc',
        int $perPage = AppConstants::ADMIN_PAGINATION_LIMIT
    ): LengthAwarePaginator {
        $query = User::query();

        // Filter by role
        if (!empty($role)) {
            $query->where('role', $role);
        }

        // Filter by search keyword (name, email)
        if (!empty($search)) {
            $term = trim($search);
            $query->where(function ($q) use ($term) {
                $q->where('name', 'LIKE', "%{$term}%")
                  ->orWhere('email', 'LIKE', "%{$term}%");
            });
        }

        // Apply sorting
        switch ($sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'created_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'role_asc':
                $query->orderBy('role', 'asc');
                break;
            case 'created_desc':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        return $query->paginate($perPage)->withQueryString();
    }

    /**
     * Create a new user.
     *
     * @param array $data
     * @return User
     */
    public function createUser(array $data): User
    {
        return DB::transaction(function () use ($data) {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'], // Auto-hashed by User model casts
                'role' => $data['role'] ?? 'customer',
            ]);
        });
    }

    /**
     * Update an existing user.
     *
     * @param User $user
     * @param array $data
     * @return User
     */
    public function updateUser(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data) {
            $updateData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'role' => $data['role'] ?? $user->role,
            ];

            // Only update password if provided
            if (!empty($data['password'])) {
                $updateData['password'] = $data['password'];
            }

            $user->update($updateData);

            return $user->fresh();
        });
    }

    /**
     * Delete a user with safeguard checks.
     *
     * @param User $user
     * @return bool
     * @throws ValidationException
     */
    public function deleteUser(User $user): bool
    {
        // Guard 1: Cannot delete currently logged in user
        if (Auth::check() && Auth::id() === $user->id) {
            throw ValidationException::withMessages([
                'user' => AppConstants::MSG_USER_CANNOT_DELETE_SELF,
            ]);
        }

        // Guard 2: Cannot delete the last admin
        if ($user->isAdmin()) {
            $adminCount = User::where('role', 'admin')->count();
            if ($adminCount <= 1) {
                throw ValidationException::withMessages([
                    'user' => AppConstants::MSG_USER_CANNOT_DELETE_LAST_ADMIN,
                ]);
            }
        }

        return DB::transaction(function () use ($user) {
            return (bool) $user->delete();
        });
    }
}
