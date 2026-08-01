<?php

namespace App\Policies\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

trait AdminOnlyPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function view(
        User $user,
        Model $record,
    ): bool {
        return $user->canManageMasterData();
    }

    public function create(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function update(
        User $user,
        Model $record,
    ): bool {
        return $user->canManageMasterData();
    }

    public function delete(
        User $user,
        Model $record,
    ): bool {
        return $user->canManageMasterData();
    }

    public function deleteAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function restore(
        User $user,
        Model $record,
    ): bool {
        return $user->canManageMasterData();
    }

    public function restoreAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function forceDelete(
        User $user,
        Model $record,
    ): bool {
        return $user->canManageMasterData();
    }

    public function forceDeleteAny(User $user): bool
    {
        return $user->canManageMasterData();
    }

    public function replicate(
        User $user,
        Model $record,
    ): bool {
        return $user->canManageMasterData();
    }

    public function reorder(User $user): bool
    {
        return $user->canManageMasterData();
    }
}
