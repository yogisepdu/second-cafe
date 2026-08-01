<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canOperateCashier();
    }

    public function view(
        User $user,
        Order $order,
    ): bool {
        return $user->canOperateCashier();
    }

    public function create(User $user): bool
    {
        return $user->canManageMasterData();
    }

    /*
     * Update standar hanya untuk admin.
     * Kasir menggunakan updateStatus().
     */
    public function update(
        User $user,
        Order $order,
    ): bool {
        return $user->canManageMasterData();
    }

    public function updateStatus(
        User $user,
        Order $order,
    ): bool {
        return $user->canOperateCashier();
    }

    public function delete(
        User $user,
        Order $order,
    ): bool {
        return $user->canManageMasterData();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function restore(
        User $user,
        Order $order,
    ): bool {
        return $user->canManageMasterData();
    }

    public function restoreAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function forceDelete(
        User $user,
        Order $order,
    ): bool {
        return $user->canManageMasterData();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function replicate(
        User $user,
        Order $order,
    ): bool {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
