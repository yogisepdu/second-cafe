<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Menu extends Model
{
    //
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'description',
        'price',
        'image',
        'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Menu $menu) {
            if (blank($menu->slug)) {
                $menu->slug = Str::slug($menu->name);
            }
        });

        static::updating(function (Menu $menu) {
            if (
                $menu->isDirty('name')
                && ! $menu->isDirty('slug')
            ) {
                $menu->slug = Str::slug($menu->name);
            }
        });

        /*
    |--------------------------------------------------------------------------
    | Menghapus gambar lama ketika gambar diganti
    |--------------------------------------------------------------------------
    */
        static::updated(function (Menu $menu) {
            if (! $menu->wasChanged('image')) {
                return;
            }

            $oldImage = $menu->getOriginal('image');

            if (
                filled($oldImage)
                && $oldImage !== $menu->image
            ) {
                Storage::disk('public')->delete($oldImage);
            }
        });

        /*
    |--------------------------------------------------------------------------
    | Menghapus gambar ketika menu dihapus
    |--------------------------------------------------------------------------
    */
        static::deleting(function (Menu $menu) {
            if (filled($menu->image)) {
                Storage::disk('public')->delete($menu->image);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
