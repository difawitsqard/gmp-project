<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

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
        // Logic to get admin dashboard data
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
