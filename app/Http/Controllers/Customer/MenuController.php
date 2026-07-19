<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use App\Models\Category;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(string $token): View
    {
        $cafeTable = CafeTable::query()
            ->where('qr_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('menus', function ($query) {
                $query->where('is_available', true);
            })
            ->with([
                'menus' => function ($query) {
                    $query
                        ->where('is_available', true)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('customer.menu', compact(
            'cafeTable',
            'categories',
        ));
    }
}
