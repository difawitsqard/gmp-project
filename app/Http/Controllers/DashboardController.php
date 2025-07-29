<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Forecast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Pelanggan')) {
            return $this->dashboardPelanggan();
        } elseif ($user->hasRole('Staff Gudang')) {
            return $this->dashboardStaffGudang();
        } elseif ($user->hasRole('Admin')) {
            return $this->dashboardAdmin();
        } elseif ($user->hasRole('Manajer Stok')) {
            return $this->dashboardManajerStok();
        }

        abort(403, 'Unauthorized action.');
    }

    private function dashboardPelanggan()
    {
        $customerId = auth()->id();

        $counts = [
            'totalOrders' => Order::where('customer_id', $customerId)->count(),
            'pendingPayment' => Order::where('customer_id', $customerId)
                ->where('status', 'pending')
                ->whereHas('payments', function ($query) {
                    $query->where('status', 'pending');
                })
                ->count(),
            'pendingConfirmation' => Order::where('customer_id', $customerId)->where('status', 'waiting_confirmation')->count(),
            'completedOrders' => Order::where('customer_id', $customerId)->where('status', 'completed')->count(),
        ];

        return inertia(
            'dashboard/dashboard-pelanggan',
            [
                'dashboardData' => $counts,
            ]
        );
    }

    private function dashboardAdmin()
    {
        $counts = [
            'totalCustomers' => User::whereHas('roles', function ($query) {
                $query->where('name', 'Pelanggan');
            })->count(),
            'totalOrders' => Order::count(),
            'pendingPayment' => Order::where('status', 'pending')->whereHas('payments', function ($query) {
                $query->where('status', 'pending');
            })->count(),
            'pendingConfirmation' => Order::where('status', 'waiting_confirmation')->count(),
            'completedOrders' => Order::where('status', 'completed')->count(),
        ];

        return inertia(
            'dashboard/dashboard-admin',
            [
                'dashboardData' => $counts,
            ]
        );
    }

    private function dashboardManajerStok()
    {
        // 5 produk terlaris
        $topProducts = Product::select('products.*', DB::raw('SUM(order_items.quantity) as total_sold'))
            ->join('order_items', 'products.id', '=', 'order_items.product_id')
            ->groupBy('products.id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get();

        // 5 Riwayat Prediksi
        $recentForecasts = Forecast::with(['createdBy', 'analyses'])
            ->where('status', '=', 'done')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($forecast) {
                $forecast->analysed_products_count = $forecast->analyses->count();
                return $forecast;
            });

        $data = [
            'totalProducts' => Product::count(),
            'lowStockProducts' => Product::whereColumn('qty', '<=', 'min_stock')->count(),
            'outOfStockProducts' => Product::where('qty', 0)->count(),
            'waitingProcessingOrders' => Order::where('status', 'waiting_processing')->count(),
            'topProducts' => $topProducts,
            'recentForecasts' => $recentForecasts,
        ];

        return inertia(
            'dashboard/dashboard-manajer-stok',
            [
                'dashboardData' => $data,
            ]
        );
    }


    private function dashboardStaffGudang()
    {
        $counts = [
            'totalProducts' => Product::count(),
            'lowStockProducts' => Product::whereColumn('qty', '<=', 'min_stock')->count(),
            'outOfStockProducts' => Product::where('qty', 0)->count(),
            'waitingProcessingOrders' => Order::where('status', 'waiting_processing')->count(),
        ];

        return inertia(
            'dashboard/dashboard-staff-gudang',
            [
                'dashboardData' => $counts,
            ]
        );
    }
}
