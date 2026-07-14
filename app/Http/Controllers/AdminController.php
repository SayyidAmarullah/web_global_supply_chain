<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Shipment;
use App\Models\SystemLog;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::where('account_status', 'active')->count(),
            'admins' => User::where('role', 'admin')->count(),
            'total_shipments' => Shipment::count(),
            'db_status' => 'Online',
            'api_status' => 'Healthy',
            'cpu_usage' => rand(15, 45) . '%',
            'memory_usage' => rand(30, 60) . '%',
            'storage_usage' => '12.4 GB',
        ];

        $latestLogs = SystemLog::with('user')->latest()->take(5)->get();

        return view('admin.index', compact('stats', 'latestLogs'));
    }

    public function users()
    {
        $users = User::paginate(10);
        return view('admin.users', compact('users'));
    }

    public function masterData()
    {
        // Simple array representing master data management
        $masterData = [
            'Countries' => 195,
            'Ports' => 4500,
            'Commodities' => 120,
            'Currencies' => 35,
            'Shipping Companies' => 85,
        ];
        return view('admin.master_data', compact('masterData'));
    }

    public function apiManagement()
    {
        $apis = [
            ['name' => 'Open Meteo', 'provider' => 'Open-Meteo', 'status' => 'Active', 'latency' => rand(50, 150) . 'ms', 'quota' => 'Unlimited'],
            ['name' => 'ExchangeRates', 'provider' => 'ExchangeRate-API', 'status' => 'Active', 'latency' => rand(100, 300) . 'ms', 'quota' => '75%'],
            ['name' => 'MarineTraffic (Mock)', 'provider' => 'Spire', 'status' => 'Disabled', 'latency' => '-', 'quota' => '0%'],
        ];
        return view('admin.api_management', compact('apis'));
    }

    public function settings()
    {
        return view('admin.settings');
    }

    public function logs()
    {
        $logs = SystemLog::with('user')->latest()->paginate(20);
        return view('admin.logs', compact('logs'));
    }
}
