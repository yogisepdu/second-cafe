<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\CafeTable;
use App\Models\Category;
use App\Support\CustomerOrderTracker;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function index(
        string $token,
        CustomerOrderTracker $orderTracker,
    ): View {
        $cafeTable = CafeTable::query()
            ->where('qr_token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $categories = Category::query()
            ->where('is_active', true)
            ->whereHas('menus', function ($query): void {
                $query->where('is_available', true);
            })
            ->with([
                'menus' => function ($query): void {
                    $query
                        ->where('is_available', true)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        $trackedOrders = $orderTracker->getForTable(
            $cafeTable,
        );

        return view('customer.menu', compact(
            'cafeTable',
            'categories',
            'trackedOrders',
        ));
    }
}
