<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public const ROLE_ADMIN = 'admin';

    public const ROLE_CASHIER = 'cashier';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Admin dan kasir dapat masuk ke panel Filament
     * selama akun masih aktif.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active
            && in_array(
                $this->role,
                [
                    self::ROLE_ADMIN,
                    self::ROLE_CASHIER,
                ],
                true,
            );
    }

    public function isAdmin(): bool
    {
        return $this->role === self::ROLE_ADMIN;
    }

    public function isCashier(): bool
    {
        return $this->role === self::ROLE_CASHIER;
    }

    public function canManageMasterData(): bool
    {
        return $this->is_active
            && $this->isAdmin();
    }

    public function canOperateCashier(): bool
    {
        return $this->is_active
            && (
                $this->isAdmin()
                || $this->isCashier()
            );
    }

    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(
            Payment::class,
            'verified_by',
        );
    }
}
