<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function view(
        User $user,
        User $model,
    ): bool {
        return $user->canManageMasterData();
    }

    public function create(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function update(
        User $user,
        User $model,
    ): bool {
        return $user->canManageMasterData();
    }

    public function delete(
        User $user,
        User $model,
    ): bool {
        return $user->canManageMasterData()
            && ! $user->is($model);
    }

    /*
     * Bulk delete dinonaktifkan untuk mencegah
     * admin menghapus akunnya sendiri.
     */
    public function deleteAny(User $user): bool
    {
        return false;
    }

    public function restore(
        User $user,
        User $model,
    ): bool {
        return $user->canManageMasterData();
    }

    public function restoreAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function forceDelete(
        User $user,
        User $model,
    ): bool {
        return $user->canManageMasterData()
            && ! $user->is($model);
    }

    public function forceDeleteAny(User $user): bool
    {
        return false;
    }

    public function replicate(
        User $user,
        User $model,
    ): bool {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
