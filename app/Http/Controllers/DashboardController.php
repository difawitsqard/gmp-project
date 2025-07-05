<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    private function dashboardPelanggan()
    {
        // Logic to get customer dashboard data
    }

    private function dashboardStaffGudang()
    {
        // Logic to get product dashboard data
    }

    private function dashboardAdmin()
    {
        // Logic to get cashier dashboard data
    }

    private function dashboardManajerStok()
    {
        // Logic to get admin dashboard data
    }
}
