<?php

namespace App\Http\Controllers;

use App\Services\IntelligenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IntelligenceController extends Controller
{
    protected $intelligenceService;

    public function __construct(IntelligenceService $intelligenceService)
    {
        $this->intelligenceService = $intelligenceService;
    }

    public function index()
    {
        $country = $this->intelligenceService->getCountryIntelligence();
        $commodities = $this->intelligenceService->getCommodityIntelligence();
        $ports = $this->intelligenceService->getPortIntelligence();
        $risk = $this->intelligenceService->getGlobalRiskScore();
        $recommendations = $this->intelligenceService->getAIRecommendations();

        return view('intelligence.index', compact('country', 'commodities', 'ports', 'risk', 'recommendations'));
    }

    public function countries()
    {
        $favorites = \Illuminate\Support\Facades\DB::table('user_favorite_countries')
            ->where('user_id', auth()->id())
            ->pluck('country_code')
            ->toArray();
        return view('intelligence.countries', compact('favorites'));
    }

    public function toggleFavorite(\Illuminate\Http\Request $request, $code)
    {
        $userId = auth()->id();
        $exists = \Illuminate\Support\Facades\DB::table('user_favorite_countries')
            ->where('user_id', $userId)
            ->where('country_code', $code)
            ->exists();

        if ($exists) {
            \Illuminate\Support\Facades\DB::table('user_favorite_countries')
                ->where('user_id', $userId)
                ->where('country_code', $code)
                ->delete();
            $status = 'removed';
        } else {
            \Illuminate\Support\Facades\DB::table('user_favorite_countries')->insert([
                'user_id' => $userId,
                'country_code' => $code,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $status = 'added';
        }

        return response()->json(['success' => true, 'status' => $status]);
    }

    public function commodities(\Illuminate\Http\Request $request)
    {
        $allCommodities = $this->intelligenceService->getCommodityIntelligence();
        $recommendations = $this->intelligenceService->getAIRecommendations();
        
        $perPage = 12;
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        
        $itemsForCurrentPage = array_slice($allCommodities, $offset, $perPage);
        
        $commodities = new \Illuminate\Pagination\LengthAwarePaginator(
            $itemsForCurrentPage,
            count($allCommodities),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'total' => count($allCommodities),
            'top' => collect($allCommodities)->sortByDesc('trend')->first(),
            'bottom' => collect($allCommodities)->sortBy('trend')->first(),
            'highRiskCount' => collect($allCommodities)->where('risk', 'High')->count(),
        ];

        return view('intelligence.commodities', compact('commodities', 'recommendations', 'stats'));
    }

    public function commodityPrices($commodity)
    {
        $countries = [
            // North America
            ['name' => 'United States', 'code' => 'us'], ['name' => 'Canada', 'code' => 'ca'], ['name' => 'Mexico', 'code' => 'mx'],
            // South America
            ['name' => 'Brazil', 'code' => 'br'], ['name' => 'Argentina', 'code' => 'ar'], ['name' => 'Colombia', 'code' => 'co'], ['name' => 'Chile', 'code' => 'cl'], ['name' => 'Peru', 'code' => 'pe'],
            // Europe
            ['name' => 'Germany', 'code' => 'de'], ['name' => 'United Kingdom', 'code' => 'gb'], ['name' => 'France', 'code' => 'fr'], ['name' => 'Italy', 'code' => 'it'], 
            ['name' => 'Spain', 'code' => 'es'], ['name' => 'Netherlands', 'code' => 'nl'], ['name' => 'Switzerland', 'code' => 'ch'], ['name' => 'Sweden', 'code' => 'se'],
            ['name' => 'Poland', 'code' => 'pl'], ['name' => 'Norway', 'code' => 'no'], ['name' => 'Ukraine', 'code' => 'ua'],
            // Asia
            ['name' => 'China', 'code' => 'cn'], ['name' => 'Japan', 'code' => 'jp'], ['name' => 'India', 'code' => 'in'], ['name' => 'South Korea', 'code' => 'kr'],
            ['name' => 'Indonesia', 'code' => 'id'], ['name' => 'Vietnam', 'code' => 'vn'], ['name' => 'Thailand', 'code' => 'th'], ['name' => 'Malaysia', 'code' => 'my'],
            ['name' => 'Philippines', 'code' => 'ph'], ['name' => 'Singapore', 'code' => 'sg'], ['name' => 'Taiwan', 'code' => 'tw'],
            // Middle East & Russia
            ['name' => 'Saudi Arabia', 'code' => 'sa'], ['name' => 'United Arab Emirates', 'code' => 'ae'], ['name' => 'Turkey', 'code' => 'tr'], 
            ['name' => 'Israel', 'code' => 'il'], ['name' => 'Qatar', 'code' => 'qa'], ['name' => 'Russia', 'code' => 'ru'],
            // Africa
            ['name' => 'South Africa', 'code' => 'za'], ['name' => 'Egypt', 'code' => 'eg'], ['name' => 'Nigeria', 'code' => 'ng'], 
            ['name' => 'Kenya', 'code' => 'ke'], ['name' => 'Morocco', 'code' => 'ma'],
            // Oceania
            ['name' => 'Australia', 'code' => 'au'], ['name' => 'New Zealand', 'code' => 'nz']
        ];

        $commoditiesList = $this->intelligenceService->getCommodityIntelligence();
        $baseCommodity = collect($commoditiesList)->firstWhere('name', urldecode($commodity));
        
        if (!$baseCommodity) {
            return response()->json(['success' => false, 'message' => 'Commodity not found']);
        }

        $basePrice = $baseCommodity['price'];
        $unit = $baseCommodity['unit'];
        
        $localPrices = [];
        
        foreach ($countries as $country) {
            // Randomize price between -8% and +12% based on local market factors
            $variance = rand(-80, 120) / 1000; 
            $localPrice = $basePrice * (1 + $variance);
            
            // Randomize supply trend
            $trend = rand(-30, 30) / 10;
            
            $localPrices[] = [
                'country' => $country['name'],
                'code' => $country['code'],
                'price' => round($localPrice, 2),
                'unit' => $unit,
                'trend' => $trend,
                'status' => $variance > 0.06 ? 'Premium' : ($variance < -0.04 ? 'Discount' : 'Market Rate')
            ];
        }

        // Sort by price descending
        usort($localPrices, function($a, $b) {
            return $b['price'] <=> $a['price'];
        });

        return response()->json([
            'success' => true,
            'commodity' => $baseCommodity['name'],
            'data' => $localPrices
        ]);
    }

    public function currencies()
    {
        // (existing array omitted for brevity, I'll just append weather at the end of the file)
        $countries = [
            ['code' => 'AF', 'name' => 'Afghanistan', 'region' => 'Asia'],
            ['code' => 'AL', 'name' => 'Albania', 'region' => 'Europe'],
            ['code' => 'DZ', 'name' => 'Algeria', 'region' => 'Africa'],
            ['code' => 'AD', 'name' => 'Andorra', 'region' => 'Europe'],
            ['code' => 'AO', 'name' => 'Angola', 'region' => 'Africa'],
            ['code' => 'AG', 'name' => 'Antigua and Barbuda', 'region' => 'Americas'],
            ['code' => 'AR', 'name' => 'Argentina', 'region' => 'Americas'],
            ['code' => 'AM', 'name' => 'Armenia', 'region' => 'Asia'],
            ['code' => 'AU', 'name' => 'Australia', 'region' => 'Oceania'],
            ['code' => 'AT', 'name' => 'Austria', 'region' => 'Europe'],
            ['code' => 'AZ', 'name' => 'Azerbaijan', 'region' => 'Asia'],
            ['code' => 'BS', 'name' => 'Bahamas', 'region' => 'Americas'],
            ['code' => 'BH', 'name' => 'Bahrain', 'region' => 'Asia'],
            ['code' => 'BD', 'name' => 'Bangladesh', 'region' => 'Asia'],
            ['code' => 'BB', 'name' => 'Barbados', 'region' => 'Americas'],
            ['code' => 'BY', 'name' => 'Belarus', 'region' => 'Europe'],
            ['code' => 'BE', 'name' => 'Belgium', 'region' => 'Europe'],
            ['code' => 'BZ', 'name' => 'Belize', 'region' => 'Americas'],
            ['code' => 'BJ', 'name' => 'Benin', 'region' => 'Africa'],
            ['code' => 'BT', 'name' => 'Bhutan', 'region' => 'Asia'],
            ['code' => 'BO', 'name' => 'Bolivia', 'region' => 'Americas'],
            ['code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'region' => 'Europe'],
            ['code' => 'BW', 'name' => 'Botswana', 'region' => 'Africa'],
            ['code' => 'BR', 'name' => 'Brazil', 'region' => 'Americas'],
            ['code' => 'BN', 'name' => 'Brunei', 'region' => 'Asia'],
            ['code' => 'BG', 'name' => 'Bulgaria', 'region' => 'Europe'],
            ['code' => 'BF', 'name' => 'Burkina Faso', 'region' => 'Africa'],
            ['code' => 'BI', 'name' => 'Burundi', 'region' => 'Africa'],
            ['code' => 'CV', 'name' => 'Cabo Verde', 'region' => 'Africa'],
            ['code' => 'KH', 'name' => 'Cambodia', 'region' => 'Asia'],
            ['code' => 'CM', 'name' => 'Cameroon', 'region' => 'Africa'],
            ['code' => 'CA', 'name' => 'Canada', 'region' => 'Americas'],
            ['code' => 'CF', 'name' => 'Central African Republic', 'region' => 'Africa'],
            ['code' => 'TD', 'name' => 'Chad', 'region' => 'Africa'],
            ['code' => 'CL', 'name' => 'Chile', 'region' => 'Americas'],
            ['code' => 'CN', 'name' => 'China', 'region' => 'Asia'],
            ['code' => 'CO', 'name' => 'Colombia', 'region' => 'Americas'],
            ['code' => 'KM', 'name' => 'Comoros', 'region' => 'Africa'],
            ['code' => 'CG', 'name' => 'Congo', 'region' => 'Africa'],
            ['code' => 'CR', 'name' => 'Costa Rica', 'region' => 'Americas'],
            ['code' => 'HR', 'name' => 'Croatia', 'region' => 'Europe'],
            ['code' => 'CU', 'name' => 'Cuba', 'region' => 'Americas'],
            ['code' => 'CY', 'name' => 'Cyprus', 'region' => 'Europe'],
            ['code' => 'CZ', 'name' => 'Czechia', 'region' => 'Europe'],
            ['code' => 'CD', 'name' => 'DR Congo', 'region' => 'Africa'],
            ['code' => 'DK', 'name' => 'Denmark', 'region' => 'Europe'],
            ['code' => 'DJ', 'name' => 'Djibouti', 'region' => 'Africa'],
            ['code' => 'DM', 'name' => 'Dominica', 'region' => 'Americas'],
            ['code' => 'DO', 'name' => 'Dominican Republic', 'region' => 'Americas'],
            ['code' => 'EC', 'name' => 'Ecuador', 'region' => 'Americas'],
            ['code' => 'EG', 'name' => 'Egypt', 'region' => 'Africa'],
            ['code' => 'SV', 'name' => 'El Salvador', 'region' => 'Americas'],
            ['code' => 'GQ', 'name' => 'Equatorial Guinea', 'region' => 'Africa'],
            ['code' => 'ER', 'name' => 'Eritrea', 'region' => 'Africa'],
            ['code' => 'EE', 'name' => 'Estonia', 'region' => 'Europe'],
            ['code' => 'SZ', 'name' => 'Eswatini', 'region' => 'Africa'],
            ['code' => 'ET', 'name' => 'Ethiopia', 'region' => 'Africa'],
            ['code' => 'FJ', 'name' => 'Fiji', 'region' => 'Oceania'],
            ['code' => 'FI', 'name' => 'Finland', 'region' => 'Europe'],
            ['code' => 'FR', 'name' => 'France', 'region' => 'Europe'],
            ['code' => 'GA', 'name' => 'Gabon', 'region' => 'Africa'],
            ['code' => 'GM', 'name' => 'Gambia', 'region' => 'Africa'],
            ['code' => 'GE', 'name' => 'Georgia', 'region' => 'Asia'],
            ['code' => 'DE', 'name' => 'Germany', 'region' => 'Europe'],
            ['code' => 'GH', 'name' => 'Ghana', 'region' => 'Africa'],
            ['code' => 'GR', 'name' => 'Greece', 'region' => 'Europe'],
            ['code' => 'GD', 'name' => 'Grenada', 'region' => 'Americas'],
            ['code' => 'GT', 'name' => 'Guatemala', 'region' => 'Americas'],
            ['code' => 'GN', 'name' => 'Guinea', 'region' => 'Africa'],
            ['code' => 'GW', 'name' => 'Guinea-Bissau', 'region' => 'Africa'],
            ['code' => 'GY', 'name' => 'Guyana', 'region' => 'Americas'],
            ['code' => 'HT', 'name' => 'Haiti', 'region' => 'Americas'],
            ['code' => 'HN', 'name' => 'Honduras', 'region' => 'Americas'],
            ['code' => 'HU', 'name' => 'Hungary', 'region' => 'Europe'],
            ['code' => 'IS', 'name' => 'Iceland', 'region' => 'Europe'],
            ['code' => 'IN', 'name' => 'India', 'region' => 'Asia'],
            ['code' => 'ID', 'name' => 'Indonesia', 'region' => 'Asia'],
            ['code' => 'IR', 'name' => 'Iran', 'region' => 'Asia'],
            ['code' => 'IQ', 'name' => 'Iraq', 'region' => 'Asia'],
            ['code' => 'IE', 'name' => 'Ireland', 'region' => 'Europe'],
            ['code' => 'IL', 'name' => 'Israel', 'region' => 'Asia'],
            ['code' => 'IT', 'name' => 'Italy', 'region' => 'Europe'],
            ['code' => 'CI', 'name' => 'Ivory Coast', 'region' => 'Africa'],
            ['code' => 'JM', 'name' => 'Jamaica', 'region' => 'Americas'],
            ['code' => 'JP', 'name' => 'Japan', 'region' => 'Asia'],
            ['code' => 'JO', 'name' => 'Jordan', 'region' => 'Asia'],
            ['code' => 'KZ', 'name' => 'Kazakhstan', 'region' => 'Asia'],
            ['code' => 'KE', 'name' => 'Kenya', 'region' => 'Africa'],
            ['code' => 'KI', 'name' => 'Kiribati', 'region' => 'Oceania'],
            ['code' => 'KW', 'name' => 'Kuwait', 'region' => 'Asia'],
            ['code' => 'KG', 'name' => 'Kyrgyzstan', 'region' => 'Asia'],
            ['code' => 'LA', 'name' => 'Laos', 'region' => 'Asia'],
            ['code' => 'LV', 'name' => 'Latvia', 'region' => 'Europe'],
            ['code' => 'LB', 'name' => 'Lebanon', 'region' => 'Asia'],
            ['code' => 'LS', 'name' => 'Lesotho', 'region' => 'Africa'],
            ['code' => 'LR', 'name' => 'Liberia', 'region' => 'Africa'],
            ['code' => 'LY', 'name' => 'Libya', 'region' => 'Africa'],
            ['code' => 'LI', 'name' => 'Liechtenstein', 'region' => 'Europe'],
            ['code' => 'LT', 'name' => 'Lithuania', 'region' => 'Europe'],
            ['code' => 'LU', 'name' => 'Luxembourg', 'region' => 'Europe'],
            ['code' => 'MG', 'name' => 'Madagascar', 'region' => 'Africa'],
            ['code' => 'MW', 'name' => 'Malawi', 'region' => 'Africa'],
            ['code' => 'MY', 'name' => 'Malaysia', 'region' => 'Asia'],
            ['code' => 'MV', 'name' => 'Maldives', 'region' => 'Asia'],
            ['code' => 'ML', 'name' => 'Mali', 'region' => 'Africa'],
            ['code' => 'MT', 'name' => 'Malta', 'region' => 'Europe'],
            ['code' => 'MH', 'name' => 'Marshall Islands', 'region' => 'Oceania'],
            ['code' => 'MR', 'name' => 'Mauritania', 'region' => 'Africa'],
            ['code' => 'MU', 'name' => 'Mauritius', 'region' => 'Africa'],
            ['code' => 'MX', 'name' => 'Mexico', 'region' => 'Americas'],
            ['code' => 'FM', 'name' => 'Micronesia', 'region' => 'Oceania'],
            ['code' => 'MD', 'name' => 'Moldova', 'region' => 'Europe'],
            ['code' => 'MC', 'name' => 'Monaco', 'region' => 'Europe'],
            ['code' => 'MN', 'name' => 'Mongolia', 'region' => 'Asia'],
            ['code' => 'ME', 'name' => 'Montenegro', 'region' => 'Europe'],
            ['code' => 'MA', 'name' => 'Morocco', 'region' => 'Africa'],
            ['code' => 'MZ', 'name' => 'Mozambique', 'region' => 'Africa'],
            ['code' => 'MM', 'name' => 'Myanmar', 'region' => 'Asia'],
            ['code' => 'NA', 'name' => 'Namibia', 'region' => 'Africa'],
            ['code' => 'NR', 'name' => 'Nauru', 'region' => 'Oceania'],
            ['code' => 'NP', 'name' => 'Nepal', 'region' => 'Asia'],
            ['code' => 'NL', 'name' => 'Netherlands', 'region' => 'Europe'],
            ['code' => 'NZ', 'name' => 'New Zealand', 'region' => 'Oceania'],
            ['code' => 'NI', 'name' => 'Nicaragua', 'region' => 'Americas'],
            ['code' => 'NE', 'name' => 'Niger', 'region' => 'Africa'],
            ['code' => 'NG', 'name' => 'Nigeria', 'region' => 'Africa'],
            ['code' => 'KP', 'name' => 'North Korea', 'region' => 'Asia'],
            ['code' => 'MK', 'name' => 'North Macedonia', 'region' => 'Europe'],
            ['code' => 'NO', 'name' => 'Norway', 'region' => 'Europe'],
            ['code' => 'OM', 'name' => 'Oman', 'region' => 'Asia'],
            ['code' => 'PK', 'name' => 'Pakistan', 'region' => 'Asia'],
            ['code' => 'PW', 'name' => 'Palau', 'region' => 'Oceania'],
            ['code' => 'PA', 'name' => 'Panama', 'region' => 'Americas'],
            ['code' => 'PG', 'name' => 'Papua New Guinea', 'region' => 'Oceania'],
            ['code' => 'PY', 'name' => 'Paraguay', 'region' => 'Americas'],
            ['code' => 'PE', 'name' => 'Peru', 'region' => 'Americas'],
            ['code' => 'PH', 'name' => 'Philippines', 'region' => 'Asia'],
            ['code' => 'PL', 'name' => 'Poland', 'region' => 'Europe'],
            ['code' => 'PT', 'name' => 'Portugal', 'region' => 'Europe'],
            ['code' => 'QA', 'name' => 'Qatar', 'region' => 'Asia'],
            ['code' => 'RO', 'name' => 'Romania', 'region' => 'Europe'],
            ['code' => 'RU', 'name' => 'Russia', 'region' => 'Europe'],
            ['code' => 'RW', 'name' => 'Rwanda', 'region' => 'Africa'],
            ['code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'region' => 'Americas'],
            ['code' => 'LC', 'name' => 'Saint Lucia', 'region' => 'Americas'],
            ['code' => 'VC', 'name' => 'Saint Vincent', 'region' => 'Americas'],
            ['code' => 'WS', 'name' => 'Samoa', 'region' => 'Oceania'],
            ['code' => 'SM', 'name' => 'San Marino', 'region' => 'Europe'],
            ['code' => 'ST', 'name' => 'Sao Tome and Principe', 'region' => 'Africa'],
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'region' => 'Asia'],
            ['code' => 'SN', 'name' => 'Senegal', 'region' => 'Africa'],
            ['code' => 'RS', 'name' => 'Serbia', 'region' => 'Europe'],
            ['code' => 'SC', 'name' => 'Seychelles', 'region' => 'Africa'],
            ['code' => 'SL', 'name' => 'Sierra Leone', 'region' => 'Africa'],
            ['code' => 'SG', 'name' => 'Singapore', 'region' => 'Asia'],
            ['code' => 'SK', 'name' => 'Slovakia', 'region' => 'Europe'],
            ['code' => 'SI', 'name' => 'Slovenia', 'region' => 'Europe'],
            ['code' => 'SB', 'name' => 'Solomon Islands', 'region' => 'Oceania'],
            ['code' => 'SO', 'name' => 'Somalia', 'region' => 'Africa'],
            ['code' => 'ZA', 'name' => 'South Africa', 'region' => 'Africa'],
            ['code' => 'KR', 'name' => 'South Korea', 'region' => 'Asia'],
            ['code' => 'SS', 'name' => 'South Sudan', 'region' => 'Africa'],
            ['code' => 'ES', 'name' => 'Spain', 'region' => 'Europe'],
            ['code' => 'LK', 'name' => 'Sri Lanka', 'region' => 'Asia'],
            ['code' => 'SD', 'name' => 'Sudan', 'region' => 'Africa'],
            ['code' => 'SR', 'name' => 'Suriname', 'region' => 'Americas'],
            ['code' => 'SE', 'name' => 'Sweden', 'region' => 'Europe'],
            ['code' => 'CH', 'name' => 'Switzerland', 'region' => 'Europe'],
            ['code' => 'SY', 'name' => 'Syria', 'region' => 'Asia'],
            ['code' => 'TJ', 'name' => 'Tajikistan', 'region' => 'Asia'],
            ['code' => 'TZ', 'name' => 'Tanzania', 'region' => 'Africa'],
            ['code' => 'TH', 'name' => 'Thailand', 'region' => 'Asia'],
            ['code' => 'TL', 'name' => 'Timor-Leste', 'region' => 'Asia'],
            ['code' => 'TG', 'name' => 'Togo', 'region' => 'Africa'],
            ['code' => 'TO', 'name' => 'Tonga', 'region' => 'Oceania'],
            ['code' => 'TT', 'name' => 'Trinidad and Tobago', 'region' => 'Americas'],
            ['code' => 'TN', 'name' => 'Tunisia', 'region' => 'Africa'],
            ['code' => 'TR', 'name' => 'Turkey', 'region' => 'Asia'],
            ['code' => 'TM', 'name' => 'Turkmenistan', 'region' => 'Asia'],
            ['code' => 'TV', 'name' => 'Tuvalu', 'region' => 'Oceania'],
            ['code' => 'UG', 'name' => 'Uganda', 'region' => 'Africa'],
            ['code' => 'UA', 'name' => 'Ukraine', 'region' => 'Europe'],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'region' => 'Asia'],
            ['code' => 'GB', 'name' => 'United Kingdom', 'region' => 'Europe'],
            ['code' => 'US', 'name' => 'United States', 'region' => 'Americas'],
            ['code' => 'UY', 'name' => 'Uruguay', 'region' => 'Americas'],
            ['code' => 'UZ', 'name' => 'Uzbekistan', 'region' => 'Asia'],
            ['code' => 'VU', 'name' => 'Vanuatu', 'region' => 'Oceania'],
            ['code' => 'VE', 'name' => 'Venezuela', 'region' => 'Americas'],
            ['code' => 'VN', 'name' => 'Vietnam', 'region' => 'Asia'],
            ['code' => 'YE', 'name' => 'Yemen', 'region' => 'Asia'],
            ['code' => 'ZM', 'name' => 'Zambia', 'region' => 'Africa'],
            ['code' => 'ZW', 'name' => 'Zimbabwe', 'region' => 'Africa']
        ];
        
        return view('intelligence.currencies', compact('countries'));
    }

    public function weather()
    {
        return view('intelligence.weather');
    }

    public function exchangeRate()
    {
        return view('intelligence.exchange_rate');
    }


    public function ports(\Illuminate\Http\Request $request)
    {
        $search = $request->query('search');
        // Back to 15 per page for performance, but pass search parameter for server-side filtering
        $ports = $this->intelligenceService->getPortIntelligence(15, $search);
        $mapPorts = $this->intelligenceService->getAllPortMapData();
        $risk = $this->intelligenceService->getGlobalRiskScore();
        
        $totalPorts = \App\Models\Port::count();
        $highCongestionCount = \App\Models\Port::where('congestion', 'High')->count();
        $mediumCongestionCount = \App\Models\Port::where('congestion', 'Medium')->count();
        
        $highCongestionPercent = $totalPorts > 0 ? round(($highCongestionCount / $totalPorts) * 100) : 0;
        $avgWaitTime = $totalPorts > 0 ? round(\App\Models\Port::avg('wait_time_hours')) : 0;
        
        // AI Port Risk Analysis calculations
        $stressLevel = $totalPorts > 0 ? round((($highCongestionCount * 100) + ($mediumCongestionCount * 50)) / $totalPorts) : 0;
        
        $worstPorts = \App\Models\Port::where('congestion', 'High')->orderBy('wait_time_hours', 'desc')->limit(2)->get();
        $bestPorts = \App\Models\Port::where('congestion', 'Low')->where('wait_time_hours', '>', 0)->orderBy('wait_time_hours', 'asc')->limit(2)->get();
        
        return view('intelligence.ports', compact(
            'ports', 'mapPorts', 'risk', 'totalPorts', 'highCongestionPercent', 'avgWaitTime',
            'stressLevel', 'worstPorts', 'bestPorts'
        ));
    }

    public function deepAnalysis()
    {
        // Select 100 ports to scan: First 15 (so user sees changes on page 1) + 85 random ports
        $first15 = \App\Models\Port::orderBy('id')->limit(15)->get();
        $random85 = \App\Models\Port::whereNotIn('id', $first15->pluck('id'))->inRandomOrder()->limit(85)->get();
        
        $portsToScan = $first15->merge($random85);
        
        if ($portsToScan->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No ports available to scan.']);
        }

        $lats = $portsToScan->pluck('latitude')->implode(',');
        $lngs = $portsToScan->pluck('longitude')->implode(',');

        try {
            // Call Open-Meteo API for real-time weather at these coordinates
            $response = \Illuminate\Support\Facades\Http::timeout(10)->get("https://api.open-meteo.com/v1/forecast", [
                'latitude' => $lats,
                'longitude' => $lngs,
                'current_weather' => true
            ]);

            if ($response->successful()) {
                $weatherData = $response->json();
                
                // If multiple coordinates are requested, Open-Meteo returns an array of responses
                // If only one, it returns a single object. We'll handle both.
                $results = isset($weatherData['latitude']) ? [$weatherData] : $weatherData;

                foreach ($portsToScan as $index => $port) {
                    if (isset($results[$index]['current_weather'])) {
                        $current = $results[$index]['current_weather'];
                        $windSpeed = $current['windspeed'] ?? 0;
                        $weatherCode = $current['weathercode'] ?? 0;
                        
                        // Map WMO Weather codes to our simple categories
                        // 0-3: Clear/Cloudy, 51-67: Rain, 71-77: Snow, 95-99: Storm
                        $weatherCat = 'Clear';
                        if ($weatherCode >= 51 && $weatherCode <= 67) $weatherCat = 'Rain';
                        if ($weatherCode >= 71 && $weatherCode <= 77) $weatherCat = 'Snow';
                        if ($weatherCode >= 95) $weatherCat = 'Storm';
                        
                        // Determine congestion and wait time based on wind and weather
                        if ($windSpeed > 35 || $weatherCat == 'Storm') {
                            $congestion = 'High';
                            $waitTime = rand(36, 72);
                        } elseif ($windSpeed > 20 || $weatherCat == 'Rain' || $weatherCat == 'Snow') {
                            $congestion = 'Medium';
                            $waitTime = rand(12, 35);
                        } else {
                            $congestion = 'Low';
                            $waitTime = rand(1, 11);
                        }
                        
                        // Update DB
                        $port->update([
                            'weather' => $weatherCat,
                            'congestion' => $congestion,
                            'wait_time_hours' => $waitTime
                        ]);
                    }
                }
                return response()->json(['success' => true]);
            }
        } catch (\Exception $e) {
            // Silently fail if external API is unreachable during demo
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
        
        return response()->json(['success' => false, 'message' => 'API Error']);
    }

    public function riskAlerts()
    {
        $globalRisk = $this->intelligenceService->getGlobalRiskScore();
        $recommendations = $this->intelligenceService->getAIRecommendations();
        
        // Let's filter high impact alerts
        $criticalAlerts = collect($recommendations)->where('impact', 'High')->count();
        $totalAlerts = collect($recommendations)->count();
        
        // Active threats simulating real-time events
        $activeThreats = [
            ['id' => 'TRT-101', 'title' => 'Red Sea Escalation', 'severity' => 'Critical', 'category' => 'Geopolitical', 'affected' => 'Suez Canal Transit', 'time' => '10 mins ago'],
            ['id' => 'TRT-102', 'title' => 'Typhoon Yagi Developing', 'severity' => 'High', 'category' => 'Weather', 'affected' => 'South China Sea Ports', 'time' => '1 hour ago'],
            ['id' => 'TRT-103', 'title' => 'Copper Price Flash Crash', 'severity' => 'High', 'category' => 'Economic', 'affected' => 'Global Metals Trade', 'time' => '3 hours ago'],
            ['id' => 'TRT-104', 'title' => 'Customs IT Outage', 'severity' => 'Medium', 'category' => 'Cyber/IT', 'affected' => 'Port of Rotterdam', 'time' => '5 hours ago'],
            ['id' => 'TRT-105', 'title' => 'Labor Strike Planned', 'severity' => 'Medium', 'category' => 'Social', 'affected' => 'US East Coast Ports', 'time' => '8 hours ago'],
        ];

        return view('intelligence.risk_alerts', compact('globalRisk', 'recommendations', 'criticalAlerts', 'totalAlerts', 'activeThreats'));
    }

    public function news()
    {
        $countries = $this->getGlobalCountriesList();
        return view('intelligence.news', compact('countries'));
    }

    public function fetchGoogleNews(\Illuminate\Http\Request $request)
    {
        $query = urlencode($request->input('q', 'logistics supply chain'));
        $url = "https://news.google.com/rss/search?q={$query}&hl=en-US&gl=US&ceid=US:en";
        
        $articles = [];
        
        // Attempt to fetch from Google News
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(5)->get($url); // 5s timeout max
            if ($response->successful()) {
                $rss = simplexml_load_string($response->body());
                $count = 0;
                
                if ($rss && isset($rss->channel->item)) {
                    foreach ($rss->channel->item as $item) {
                        if ($count >= 7) break;
                        
                        $descHtml = (string) $item->description;
                        $imageUrl = '';
                        if (preg_match('/<img[^>]+src="([^">]+)"/i', $descHtml, $matches)) {
                            $imageUrl = $matches[1];
                        }
                        
                        $cleanDesc = trim(preg_replace('/\s+/', ' ', strip_tags(str_replace(['<', '>'], [' <', '> '], $descHtml))));
                        if (strlen($cleanDesc) > 200) {
                            $cleanDesc = substr($cleanDesc, 0, 197) . '...';
                        }
                        
                        $title = (string) $item->title;
                        $sourceName = (string) $item->source;
                        if (str_ends_with($title, ' - ' . $sourceName)) {
                            $title = substr($title, 0, -(strlen(' - ' . $sourceName)));
                        }

                        $articles[] = [
                            'title' => $title,
                            'description' => $cleanDesc,
                            'url' => (string) $item->link,
                            'source' => [
                                'name' => (string) $item->source
                            ],
                            'publishedAt' => date('c', strtotime((string) $item->pubDate)),
                            'image' => $imageUrl
                        ];
                        $count++;
                    }
                }
            }
        } catch (\Exception $e) {
            // Silently fail Google News fetch so we can still return local articles
        }
        
        try {
            // --- Merge Local Internal Articles ---
            $countryFilter = $request->input('country');
            
            $localQuery = \App\Models\AnalysisArticle::where('status', 'published');
            
            if (!empty($countryFilter)) {
                $localQuery->where('country', $countryFilter);
                $localArticles = $localQuery->latest()->limit(8)->get();
            } else {
                // If global view, only show articles that are truly "Global" (no specific country tag)
                $localQuery->whereNull('country');
                $localArticles = $localQuery->latest()->limit(2)->get();
            }
            
            $internalArticles = [];
            foreach ($localArticles as $article) {
                $internalArticles[] = [
                    'title' => '[Internal Analysis] ' . $article->title,
                    'description' => \Illuminate\Support\Str::limit(strip_tags($article->content), 200),
                    'url' => $article->source_url ?? 'javascript:void(0)',
                    'source' => [
                        'name' => 'Internal (' . ($article->country ?? 'Global') . ')'
                    ],
                    'publishedAt' => $article->created_at->format('c'),
                    'image' => ''
                ];
            }
            
            // Prepend internal articles so they appear at the top
            $articles = array_merge($internalArticles, $articles);
            
            // --- LEXICON BASED SENTIMENT ANALYSIS ---
            $positiveWords = \Illuminate\Support\Facades\DB::table('positive_words')->pluck('word')->toArray();
            $negativeWords = \Illuminate\Support\Facades\DB::table('negative_words')->pluck('word')->toArray();
            
            foreach ($articles as &$article) {
                $fullText = strtolower($article['title'] . ' ' . $article['description']);
                // Remove punctuation
                $fullText = preg_replace('/[^\w\s]/', '', $fullText);
                $words = explode(' ', $fullText);
                $words = array_filter($words); // remove empty spaces
                
                $positiveScore = 0;
                $negativeScore = 0;
                $totalWords = count($words);
                
                if ($totalWords > 0) {
                    foreach ($words as $word) {
                        if (in_array($word, $positiveWords)) {
                            $positiveScore++;
                        }
                        if (in_array($word, $negativeWords)) {
                            $negativeScore++;
                        }
                    }
                    
                    $neutralScore = $totalWords - ($positiveScore + $negativeScore);
                    
                    $posPct = round(($positiveScore / $totalWords) * 100);
                    $negPct = round(($negativeScore / $totalWords) * 100);
                    $neuPct = round(($neutralScore / $totalWords) * 100);
                    
                    // Prevent total exceeding 100 due to rounding
                    $diff = 100 - ($posPct + $negPct + $neuPct);
                    $neuPct += $diff; 
                } else {
                    $posPct = 0;
                    $negPct = 0;
                    $neuPct = 100;
                    $positiveScore = 0;
                    $negativeScore = 0;
                }
                
                if ($positiveScore > $negativeScore) {
                    $sentiment = 'Positive';
                } elseif ($negativeScore > $positiveScore) {
                    $sentiment = 'Negative';
                } else {
                    $sentiment = 'Neutral';
                }
                
                $article['sentiment'] = [
                    'label' => $sentiment,
                    'positive_pct' => $posPct,
                    'neutral_pct' => $neuPct,
                    'negative_pct' => $negPct,
                    'positive_score' => $positiveScore,
                    'negative_score' => $negativeScore
                ];
            }
            
            return response()->json([
                'success' => true,
                'articles' => $articles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'errors' => ['Failed to fetch News: ' . $e->getMessage()]
            ], 500);
        }
    }

    public function getGlobalCountriesList()
    {
        return [
            ['code' => 'AF', 'name' => 'Afghanistan', 'region' => 'Asia'],
            ['code' => 'AL', 'name' => 'Albania', 'region' => 'Europe'],
            ['code' => 'DZ', 'name' => 'Algeria', 'region' => 'Africa'],
            ['code' => 'AD', 'name' => 'Andorra', 'region' => 'Europe'],
            ['code' => 'AO', 'name' => 'Angola', 'region' => 'Africa'],
            ['code' => 'AG', 'name' => 'Antigua and Barbuda', 'region' => 'Americas'],
            ['code' => 'AR', 'name' => 'Argentina', 'region' => 'Americas'],
            ['code' => 'AM', 'name' => 'Armenia', 'region' => 'Asia'],
            ['code' => 'AU', 'name' => 'Australia', 'region' => 'Oceania'],
            ['code' => 'AT', 'name' => 'Austria', 'region' => 'Europe'],
            ['code' => 'AZ', 'name' => 'Azerbaijan', 'region' => 'Asia'],
            ['code' => 'BS', 'name' => 'Bahamas', 'region' => 'Americas'],
            ['code' => 'BH', 'name' => 'Bahrain', 'region' => 'Asia'],
            ['code' => 'BD', 'name' => 'Bangladesh', 'region' => 'Asia'],
            ['code' => 'BB', 'name' => 'Barbados', 'region' => 'Americas'],
            ['code' => 'BY', 'name' => 'Belarus', 'region' => 'Europe'],
            ['code' => 'BE', 'name' => 'Belgium', 'region' => 'Europe'],
            ['code' => 'BZ', 'name' => 'Belize', 'region' => 'Americas'],
            ['code' => 'BJ', 'name' => 'Benin', 'region' => 'Africa'],
            ['code' => 'BT', 'name' => 'Bhutan', 'region' => 'Asia'],
            ['code' => 'BO', 'name' => 'Bolivia', 'region' => 'Americas'],
            ['code' => 'BA', 'name' => 'Bosnia and Herzegovina', 'region' => 'Europe'],
            ['code' => 'BW', 'name' => 'Botswana', 'region' => 'Africa'],
            ['code' => 'BR', 'name' => 'Brazil', 'region' => 'Americas'],
            ['code' => 'BN', 'name' => 'Brunei', 'region' => 'Asia'],
            ['code' => 'BG', 'name' => 'Bulgaria', 'region' => 'Europe'],
            ['code' => 'BF', 'name' => 'Burkina Faso', 'region' => 'Africa'],
            ['code' => 'BI', 'name' => 'Burundi', 'region' => 'Africa'],
            ['code' => 'CV', 'name' => 'Cabo Verde', 'region' => 'Africa'],
            ['code' => 'KH', 'name' => 'Cambodia', 'region' => 'Asia'],
            ['code' => 'CM', 'name' => 'Cameroon', 'region' => 'Africa'],
            ['code' => 'CA', 'name' => 'Canada', 'region' => 'Americas'],
            ['code' => 'CF', 'name' => 'Central African Republic', 'region' => 'Africa'],
            ['code' => 'TD', 'name' => 'Chad', 'region' => 'Africa'],
            ['code' => 'CL', 'name' => 'Chile', 'region' => 'Americas'],
            ['code' => 'CN', 'name' => 'China', 'region' => 'Asia'],
            ['code' => 'CO', 'name' => 'Colombia', 'region' => 'Americas'],
            ['code' => 'KM', 'name' => 'Comoros', 'region' => 'Africa'],
            ['code' => 'CG', 'name' => 'Congo', 'region' => 'Africa'],
            ['code' => 'CR', 'name' => 'Costa Rica', 'region' => 'Americas'],
            ['code' => 'HR', 'name' => 'Croatia', 'region' => 'Europe'],
            ['code' => 'CU', 'name' => 'Cuba', 'region' => 'Americas'],
            ['code' => 'CY', 'name' => 'Cyprus', 'region' => 'Europe'],
            ['code' => 'CZ', 'name' => 'Czechia', 'region' => 'Europe'],
            ['code' => 'CD', 'name' => 'DR Congo', 'region' => 'Africa'],
            ['code' => 'DK', 'name' => 'Denmark', 'region' => 'Europe'],
            ['code' => 'DJ', 'name' => 'Djibouti', 'region' => 'Africa'],
            ['code' => 'DM', 'name' => 'Dominica', 'region' => 'Americas'],
            ['code' => 'DO', 'name' => 'Dominican Republic', 'region' => 'Americas'],
            ['code' => 'EC', 'name' => 'Ecuador', 'region' => 'Americas'],
            ['code' => 'EG', 'name' => 'Egypt', 'region' => 'Africa'],
            ['code' => 'SV', 'name' => 'El Salvador', 'region' => 'Americas'],
            ['code' => 'GQ', 'name' => 'Equatorial Guinea', 'region' => 'Africa'],
            ['code' => 'ER', 'name' => 'Eritrea', 'region' => 'Africa'],
            ['code' => 'EE', 'name' => 'Estonia', 'region' => 'Europe'],
            ['code' => 'SZ', 'name' => 'Eswatini', 'region' => 'Africa'],
            ['code' => 'ET', 'name' => 'Ethiopia', 'region' => 'Africa'],
            ['code' => 'FJ', 'name' => 'Fiji', 'region' => 'Oceania'],
            ['code' => 'FI', 'name' => 'Finland', 'region' => 'Europe'],
            ['code' => 'FR', 'name' => 'France', 'region' => 'Europe'],
            ['code' => 'GA', 'name' => 'Gabon', 'region' => 'Africa'],
            ['code' => 'GM', 'name' => 'Gambia', 'region' => 'Africa'],
            ['code' => 'GE', 'name' => 'Georgia', 'region' => 'Asia'],
            ['code' => 'DE', 'name' => 'Germany', 'region' => 'Europe'],
            ['code' => 'GH', 'name' => 'Ghana', 'region' => 'Africa'],
            ['code' => 'GR', 'name' => 'Greece', 'region' => 'Europe'],
            ['code' => 'GD', 'name' => 'Grenada', 'region' => 'Americas'],
            ['code' => 'GT', 'name' => 'Guatemala', 'region' => 'Americas'],
            ['code' => 'GN', 'name' => 'Guinea', 'region' => 'Africa'],
            ['code' => 'GW', 'name' => 'Guinea-Bissau', 'region' => 'Africa'],
            ['code' => 'GY', 'name' => 'Guyana', 'region' => 'Americas'],
            ['code' => 'HT', 'name' => 'Haiti', 'region' => 'Americas'],
            ['code' => 'HN', 'name' => 'Honduras', 'region' => 'Americas'],
            ['code' => 'HU', 'name' => 'Hungary', 'region' => 'Europe'],
            ['code' => 'IS', 'name' => 'Iceland', 'region' => 'Europe'],
            ['code' => 'IN', 'name' => 'India', 'region' => 'Asia'],
            ['code' => 'ID', 'name' => 'Indonesia', 'region' => 'Asia'],
            ['code' => 'IR', 'name' => 'Iran', 'region' => 'Asia'],
            ['code' => 'IQ', 'name' => 'Iraq', 'region' => 'Asia'],
            ['code' => 'IE', 'name' => 'Ireland', 'region' => 'Europe'],
            ['code' => 'IL', 'name' => 'Israel', 'region' => 'Asia'],
            ['code' => 'IT', 'name' => 'Italy', 'region' => 'Europe'],
            ['code' => 'CI', 'name' => 'Ivory Coast', 'region' => 'Africa'],
            ['code' => 'JM', 'name' => 'Jamaica', 'region' => 'Americas'],
            ['code' => 'JP', 'name' => 'Japan', 'region' => 'Asia'],
            ['code' => 'JO', 'name' => 'Jordan', 'region' => 'Asia'],
            ['code' => 'KZ', 'name' => 'Kazakhstan', 'region' => 'Asia'],
            ['code' => 'KE', 'name' => 'Kenya', 'region' => 'Africa'],
            ['code' => 'KI', 'name' => 'Kiribati', 'region' => 'Oceania'],
            ['code' => 'KW', 'name' => 'Kuwait', 'region' => 'Asia'],
            ['code' => 'KG', 'name' => 'Kyrgyzstan', 'region' => 'Asia'],
            ['code' => 'LA', 'name' => 'Laos', 'region' => 'Asia'],
            ['code' => 'LV', 'name' => 'Latvia', 'region' => 'Europe'],
            ['code' => 'LB', 'name' => 'Lebanon', 'region' => 'Asia'],
            ['code' => 'LS', 'name' => 'Lesotho', 'region' => 'Africa'],
            ['code' => 'LR', 'name' => 'Liberia', 'region' => 'Africa'],
            ['code' => 'LY', 'name' => 'Libya', 'region' => 'Africa'],
            ['code' => 'LI', 'name' => 'Liechtenstein', 'region' => 'Europe'],
            ['code' => 'LT', 'name' => 'Lithuania', 'region' => 'Europe'],
            ['code' => 'LU', 'name' => 'Luxembourg', 'region' => 'Europe'],
            ['code' => 'MG', 'name' => 'Madagascar', 'region' => 'Africa'],
            ['code' => 'MW', 'name' => 'Malawi', 'region' => 'Africa'],
            ['code' => 'MY', 'name' => 'Malaysia', 'region' => 'Asia'],
            ['code' => 'MV', 'name' => 'Maldives', 'region' => 'Asia'],
            ['code' => 'ML', 'name' => 'Mali', 'region' => 'Africa'],
            ['code' => 'MT', 'name' => 'Malta', 'region' => 'Europe'],
            ['code' => 'MH', 'name' => 'Marshall Islands', 'region' => 'Oceania'],
            ['code' => 'MR', 'name' => 'Mauritania', 'region' => 'Africa'],
            ['code' => 'MU', 'name' => 'Mauritius', 'region' => 'Africa'],
            ['code' => 'MX', 'name' => 'Mexico', 'region' => 'Americas'],
            ['code' => 'FM', 'name' => 'Micronesia', 'region' => 'Oceania'],
            ['code' => 'MD', 'name' => 'Moldova', 'region' => 'Europe'],
            ['code' => 'MC', 'name' => 'Monaco', 'region' => 'Europe'],
            ['code' => 'MN', 'name' => 'Mongolia', 'region' => 'Asia'],
            ['code' => 'ME', 'name' => 'Montenegro', 'region' => 'Europe'],
            ['code' => 'MA', 'name' => 'Morocco', 'region' => 'Africa'],
            ['code' => 'MZ', 'name' => 'Mozambique', 'region' => 'Africa'],
            ['code' => 'MM', 'name' => 'Myanmar', 'region' => 'Asia'],
            ['code' => 'NA', 'name' => 'Namibia', 'region' => 'Africa'],
            ['code' => 'NR', 'name' => 'Nauru', 'region' => 'Oceania'],
            ['code' => 'NP', 'name' => 'Nepal', 'region' => 'Asia'],
            ['code' => 'NL', 'name' => 'Netherlands', 'region' => 'Europe'],
            ['code' => 'NZ', 'name' => 'New Zealand', 'region' => 'Oceania'],
            ['code' => 'NI', 'name' => 'Nicaragua', 'region' => 'Americas'],
            ['code' => 'NE', 'name' => 'Niger', 'region' => 'Africa'],
            ['code' => 'NG', 'name' => 'Nigeria', 'region' => 'Africa'],
            ['code' => 'KP', 'name' => 'North Korea', 'region' => 'Asia'],
            ['code' => 'MK', 'name' => 'North Macedonia', 'region' => 'Europe'],
            ['code' => 'NO', 'name' => 'Norway', 'region' => 'Europe'],
            ['code' => 'OM', 'name' => 'Oman', 'region' => 'Asia'],
            ['code' => 'PK', 'name' => 'Pakistan', 'region' => 'Asia'],
            ['code' => 'PW', 'name' => 'Palau', 'region' => 'Oceania'],
            ['code' => 'PA', 'name' => 'Panama', 'region' => 'Americas'],
            ['code' => 'PG', 'name' => 'Papua New Guinea', 'region' => 'Oceania'],
            ['code' => 'PY', 'name' => 'Paraguay', 'region' => 'Americas'],
            ['code' => 'PE', 'name' => 'Peru', 'region' => 'Americas'],
            ['code' => 'PH', 'name' => 'Philippines', 'region' => 'Asia'],
            ['code' => 'PL', 'name' => 'Poland', 'region' => 'Europe'],
            ['code' => 'PT', 'name' => 'Portugal', 'region' => 'Europe'],
            ['code' => 'QA', 'name' => 'Qatar', 'region' => 'Asia'],
            ['code' => 'RO', 'name' => 'Romania', 'region' => 'Europe'],
            ['code' => 'RU', 'name' => 'Russia', 'region' => 'Europe'],
            ['code' => 'RW', 'name' => 'Rwanda', 'region' => 'Africa'],
            ['code' => 'KN', 'name' => 'Saint Kitts and Nevis', 'region' => 'Americas'],
            ['code' => 'LC', 'name' => 'Saint Lucia', 'region' => 'Americas'],
            ['code' => 'VC', 'name' => 'Saint Vincent', 'region' => 'Americas'],
            ['code' => 'WS', 'name' => 'Samoa', 'region' => 'Oceania'],
            ['code' => 'SM', 'name' => 'San Marino', 'region' => 'Europe'],
            ['code' => 'ST', 'name' => 'Sao Tome and Principe', 'region' => 'Africa'],
            ['code' => 'SA', 'name' => 'Saudi Arabia', 'region' => 'Asia'],
            ['code' => 'SN', 'name' => 'Senegal', 'region' => 'Africa'],
            ['code' => 'RS', 'name' => 'Serbia', 'region' => 'Europe'],
            ['code' => 'SC', 'name' => 'Seychelles', 'region' => 'Africa'],
            ['code' => 'SL', 'name' => 'Sierra Leone', 'region' => 'Africa'],
            ['code' => 'SG', 'name' => 'Singapore', 'region' => 'Asia'],
            ['code' => 'SK', 'name' => 'Slovakia', 'region' => 'Europe'],
            ['code' => 'SI', 'name' => 'Slovenia', 'region' => 'Europe'],
            ['code' => 'SB', 'name' => 'Solomon Islands', 'region' => 'Oceania'],
            ['code' => 'SO', 'name' => 'Somalia', 'region' => 'Africa'],
            ['code' => 'ZA', 'name' => 'South Africa', 'region' => 'Africa'],
            ['code' => 'KR', 'name' => 'South Korea', 'region' => 'Asia'],
            ['code' => 'SS', 'name' => 'South Sudan', 'region' => 'Africa'],
            ['code' => 'ES', 'name' => 'Spain', 'region' => 'Europe'],
            ['code' => 'LK', 'name' => 'Sri Lanka', 'region' => 'Asia'],
            ['code' => 'SD', 'name' => 'Sudan', 'region' => 'Africa'],
            ['code' => 'SR', 'name' => 'Suriname', 'region' => 'Americas'],
            ['code' => 'SE', 'name' => 'Sweden', 'region' => 'Europe'],
            ['code' => 'CH', 'name' => 'Switzerland', 'region' => 'Europe'],
            ['code' => 'SY', 'name' => 'Syria', 'region' => 'Asia'],
            ['code' => 'TJ', 'name' => 'Tajikistan', 'region' => 'Asia'],
            ['code' => 'TZ', 'name' => 'Tanzania', 'region' => 'Africa'],
            ['code' => 'TH', 'name' => 'Thailand', 'region' => 'Asia'],
            ['code' => 'TL', 'name' => 'Timor-Leste', 'region' => 'Asia'],
            ['code' => 'TG', 'name' => 'Togo', 'region' => 'Africa'],
            ['code' => 'TO', 'name' => 'Tonga', 'region' => 'Oceania'],
            ['code' => 'TT', 'name' => 'Trinidad and Tobago', 'region' => 'Americas'],
            ['code' => 'TN', 'name' => 'Tunisia', 'region' => 'Africa'],
            ['code' => 'TR', 'name' => 'Turkey', 'region' => 'Asia'],
            ['code' => 'TM', 'name' => 'Turkmenistan', 'region' => 'Asia'],
            ['code' => 'TV', 'name' => 'Tuvalu', 'region' => 'Oceania'],
            ['code' => 'UG', 'name' => 'Uganda', 'region' => 'Africa'],
            ['code' => 'UA', 'name' => 'Ukraine', 'region' => 'Europe'],
            ['code' => 'AE', 'name' => 'United Arab Emirates', 'region' => 'Asia'],
            ['code' => 'GB', 'name' => 'United Kingdom', 'region' => 'Europe'],
            ['code' => 'US', 'name' => 'United States', 'region' => 'Americas'],
            ['code' => 'UY', 'name' => 'Uruguay', 'region' => 'Americas'],
            ['code' => 'UZ', 'name' => 'Uzbekistan', 'region' => 'Asia'],
            ['code' => 'VU', 'name' => 'Vanuatu', 'region' => 'Oceania'],
            ['code' => 'VE', 'name' => 'Venezuela', 'region' => 'Americas'],
            ['code' => 'VN', 'name' => 'Vietnam', 'region' => 'Asia'],
            ['code' => 'YE', 'name' => 'Yemen', 'region' => 'Asia'],
            ['code' => 'ZM', 'name' => 'Zambia', 'region' => 'Africa'],
            ['code' => 'ZW', 'name' => 'Zimbabwe', 'region' => 'Africa']
        ];
    }

    public function compare()
    {
        $countries = $this->getGlobalCountriesList();
        return view('intelligence.compare', compact('countries'));
    }

    public function commodityCompare()
    {
        $commodities = $this->intelligenceService->getCommodityIntelligence();
        return view('intelligence.commodity_compare', compact('commodities'));
    }
}
