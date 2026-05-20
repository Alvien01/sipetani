<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Forecast;
use App\Models\HasilPeramalan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $data = Cache::remember('dashboard_data', 600, function () {
            $latestTransactionDate = Transaction::max('date_sale');
            $now = $latestTransactionDate ? Carbon::parse($latestTransactionDate) : Carbon::now();
            $startOfThisMonth = $now->copy()->startOfMonth();
            $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
            $endOfLastMonth   = $now->copy()->subMonth()->endOfMonth();

            $totalUsers        = User::count();
            $totalProducts     = Product::count();
            $totalTransactions = Transaction::count();
            $totalOmzet        = Transaction::sum('total_payment');
            $totalForecast     = HasilPeramalan::distinct('id_produk')->count('id_produk');

            // Optimized Counts (using indexed date_sale)
            $trxBulanIni = Transaction::whereBetween('date_sale', [$startOfThisMonth, $now])->count();
            $trxBulanLalu = Transaction::whereBetween('date_sale', [$startOfLastMonth, $endOfLastMonth])->count();
            
            $trxGrowth = $trxBulanLalu > 0
                ? round((($trxBulanIni - $trxBulanLalu) / $trxBulanLalu) * 100, 1)
                : ($trxBulanIni > 0 ? 100 : 0);

            $omzetBulanIni = Transaction::whereBetween('date_sale', [$startOfThisMonth, $now])->sum('total_payment');

            // Optimized Chart Data (leveraging index)
            $twelveMonthsAgo = $now->copy()->subMonths(11)->startOfMonth();
            $monthlyData = Transaction::select(
                    DB::raw('YEAR(date_sale) as year'),
                    DB::raw('MONTH(date_sale) as month'),
                    DB::raw('COUNT(*) as total_trx'),
                    DB::raw('SUM(total_buy) as total_qty'),
                    DB::raw('SUM(total_payment) as total_omzet')
                )
                ->where('date_sale', '>=', $twelveMonthsAgo)
                ->groupBy('year', 'month')
                ->orderBy('year')
                ->orderBy('month')
                ->get();

            $chartLabels = [];
            $chartTrx    = [];
            $chartOmzet  = [];
            $chartQty    = [];

            for ($i = 11; $i >= 0; $i--) {
                $date  = $now->copy()->subMonths($i);
                $y     = (int) $date->year;
                $m     = (int) $date->month;
                $label = $date->translatedFormat('M Y');

                $row = $monthlyData->filter(fn($r) => (int)$r->year === $y && (int)$r->month === $m)->first();

                $chartLabels[] = $label;
                $chartTrx[]    = $row ? (int) $row->total_trx   : 0;
                $chartOmzet[]  = $row ? (float) $row->total_omzet : 0;
                $chartQty[]    = $row ? (int) $row->total_qty    : 0;
            }

            $topProducts = Transaction::select(
                    'product_id',
                    DB::raw('COUNT(*) as total_trx'),
                    DB::raw('SUM(total_buy) as total_qty'),
                    DB::raw('SUM(total_payment) as total_omzet')
                )
                ->with('product:id,product_name')
                ->groupBy('product_id')
                ->orderByDesc('total_trx')
                ->limit(5)
                ->get();

            $recentTransactions = Transaction::with('product:id,product_name')
                ->latest('date_sale')
                ->limit(8)
                ->get();

            return compact(
                'totalUsers', 'totalProducts', 'totalTransactions', 'totalOmzet', 'totalForecast',
                'trxBulanIni', 'trxBulanLalu', 'trxGrowth', 'omzetBulanIni',
                'chartLabels', 'chartTrx', 'chartOmzet', 'chartQty', 'topProducts', 'recentTransactions'
            );
        });

        return view('dashboard', $data);
    }
}
