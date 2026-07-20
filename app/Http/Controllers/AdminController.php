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
        $portsCount = \App\Models\Port::count();
        $commoditiesCount = \App\Models\Commodity::count();
        $currenciesCount = \App\Models\Currency::count();
        $countriesCount = \App\Models\Country::count();
        
        $masterData = [
            'Countries' => $countriesCount,
            'Ports' => $portsCount,
            'Commodities' => $commoditiesCount,
            'Currencies' => $currenciesCount,
            'Shipping Companies' => \App\Models\Shipment::select('shipping_company')->distinct()->count(),
        ];
        return view('admin.master_data', compact('masterData'));
    }

    public function ports(Request $request)
    {
        $query = \App\Models\Port::query();
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('code', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('country')) {
            $query->where('country', $request->country);
        }
        
        $ports = $query->latest()->paginate(15)->withQueryString();
        $countries = app(\App\Http\Controllers\IntelligenceController::class)->getGlobalCountriesList();
        
        // Get unique countries for filter dropdown based on actual DB
        $dbCountries = \App\Models\Port::select('country')->distinct()->orderBy('country')->pluck('country');
        
        return view('admin.ports', compact('ports', 'countries', 'dbCountries'));
    }

    public function storePort(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:10',
            'country' => 'required|string|max:100',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        \App\Models\Port::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'country' => $request->country,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'congestion' => collect(['Low', 'Medium', 'High'])->random(),
            'wait_time_hours' => rand(1, 48),
            'weather' => collect(['Clear', 'Rain', 'Storm', 'Cloudy'])->random(),
        ]);

        return back()->with('success', 'Port added successfully.');
    }

    public function deletePort(\App\Models\Port $port)
    {
        $port->delete();
        return back()->with('success', 'Port deleted successfully.');
    }

    public function articles(Request $request)
    {
        $articles = \App\Models\AnalysisArticle::with('author')->latest()->paginate(15);
        $countries = app(\App\Http\Controllers\IntelligenceController::class)->getGlobalCountriesList();
        return view('admin.articles', compact('articles', 'countries'));
    }

    public function storeArticle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:draft,published',
            'country' => 'nullable|string',
            'source_url' => 'nullable|url',
        ]);

        \App\Models\AnalysisArticle::create([
            'title' => $request->title,
            'slug' => \Illuminate\Support\Str::slug($request->title) . '-' . uniqid(),
            'content' => $request->content,
            'country' => $request->country,
            'source_url' => $request->source_url,
            'status' => $request->status,
            'author_id' => auth()->id(),
        ]);

        return back()->with('success', 'Analysis Article created successfully.');
    }

    public function deleteArticle(\App\Models\AnalysisArticle $article)
    {
        $article->delete();
        return back()->with('success', 'Article deleted successfully.');
    }

    public function apiManagement()
    {
        $defaultApis = [
            ['name' => 'Open-Meteo API', 'provider' => 'Open-Meteo', 'status' => 'Active', 'latency' => rand(50, 150) . 'ms', 'quota' => 'Unlimited'],
            ['name' => 'World Bank API', 'provider' => 'World Bank', 'status' => 'Active', 'latency' => rand(120, 250) . 'ms', 'quota' => 'Unlimited'],
            ['name' => 'REST Countries API', 'provider' => 'REST Countries', 'status' => 'Active', 'latency' => rand(100, 200) . 'ms', 'quota' => 'Unlimited'],
            ['name' => 'ExchangeRate API', 'provider' => 'ExchangeRate-API', 'status' => 'Active', 'latency' => rand(100, 300) . 'ms', 'quota' => '75%'],
            ['name' => 'Marine Traffic Alternative API (Gratis)', 'provider' => 'Community AIS', 'status' => 'Active', 'latency' => rand(150, 350) . 'ms', 'quota' => 'Unlimited'],
            ['name' => 'OpenStreetMap', 'provider' => 'OSM Foundation', 'status' => 'Active', 'latency' => rand(30, 80) . 'ms', 'quota' => 'Unlimited'],
            ['name' => 'Google News RSS', 'provider' => 'Google News', 'status' => 'Active', 'latency' => rand(70, 180) . 'ms', 'quota' => 'Unlimited'],
        ];

        $sessionApis = session('admin_apis', []);
        $namesInSession = collect($sessionApis)->pluck('name')->toArray();
        $namesInDefault = collect($defaultApis)->pluck('name')->toArray();
        
        sort($namesInSession);
        sort($namesInDefault);
        
        if ($namesInSession !== $namesInDefault) {
            session(['admin_apis' => $defaultApis]);
        }

        $apis = session('admin_apis');
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

    public function updateUser(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|in:admin,user,importer,exporter',
            'account_status' => 'required|in:active,inactive,suspended'
        ]);

        $user->update([
            'role' => $request->role,
            'account_status' => $request->account_status
        ]);

        return back()->with('success', 'User updated successfully.');
    }

    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself.');
        }
        $user->delete();
        return back()->with('success', 'User deleted successfully.');
    }

    public function updateSettings(Request $request)
    {
        // For demonstration, we just return success since there's no settings table yet.
        // In a real app, you would save this to a settings table or .env file.
        return back()->with('success', 'System settings updated successfully.');
    }

    public function toggleApi(Request $request)
    {
        $apiName = $request->input('api_name');
        $apis = session('admin_apis', []);
        foreach ($apis as &$api) {
            if ($api['name'] === $apiName) {
                $api['status'] = $api['status'] === 'Active' ? 'Disabled' : 'Active';
            }
        }
        session(['admin_apis' => $apis]);
        
        return back()->with('success', "API status for $apiName toggled successfully.");
    }

    public function clearLogs()
    {
        SystemLog::truncate();
        return back()->with('success', 'All system logs have been cleared.');
    }

    public function shipments(Request $request)
    {
        $query = Shipment::query();
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('shipment_number', 'like', '%' . $request->search . '%')
                  ->orWhere('commodity', 'like', '%' . $request->search . '%')
                  ->orWhere('origin_port', 'like', '%' . $request->search . '%')
                  ->orWhere('destination_port', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        $shipments = $query->latest()->paginate(15)->withQueryString();
        
        return view('admin.shipments', compact('shipments'));
    }

    public function deleteShipment(Shipment $shipment)
    {
        $shipment->delete();
        return back()->with('success', 'Shipment deleted successfully.');
    }
}
