<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canOperateCashier();
    }

    public function view(
        User $user,
        Payment $payment,
    ): bool {
        return $user->canOperateCashier();
    }

    public function create(User $user): bool
    {
        return $user->canOperateCashier();
    }

    public function verify(
        User $user,
        Payment $payment,
    ): bool {
        return $user->canOperateCashier();
    }

    public function update(
        User $user,
        Payment $payment,
    ): bool {
        return $user->canManageMasterData();
    }

    public function delete(
        User $user,
        Payment $payment,
    ): bool {
        return $user->canManageMasterData();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function restore(
        User $user,
        Payment $payment,
    ): bool {
        return $user->canManageMasterData();
    }

    public function restoreAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function forceDelete(
        User $user,
        Payment $payment,
    ): bool {
        return $user->canManageMasterData();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function replicate(
        User $user,
        Payment $payment,
    ): bool {
        return false;
    }

    public function reorder(User $user): bool
    {
        return false;
    }
}
