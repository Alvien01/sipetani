<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Forecast;
use App\Models\HasilPeramalan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers        = User::count();
        $totalProducts     = Product::count();
        $totalTransactions = Transaction::count();
        $totalOmzet        = Transaction::sum('total_payment');
        $totalForecast     = Forecast::distinct('product_id')->count('product_id');
        $trxBulanIni   = Transaction::whereMonth('date_sale', now()->month)
                            ->whereYear('date_sale', now()->year)->count();
        $trxBulanLalu  = Transaction::whereMonth('date_sale', now()->subMonth()->month)
                            ->whereYear('date_sale', now()->subMonth()->year)->count();
        $trxGrowth     = $trxBulanLalu > 0
                            ? round((($trxBulanIni - $trxBulanLalu) / $trxBulanLalu) * 100, 1)
                            : ($trxBulanIni > 0 ? 100 : 0);

        $omzetBulanIni = Transaction::whereMonth('date_sale', now()->month)
                            ->whereYear('date_sale', now()->year)->sum('total_payment');
        $monthlyData = Transaction::select(
                DB::raw('YEAR(date_sale) as year'),
                DB::raw('MONTH(date_sale) as month'),
                DB::raw('COUNT(*) as total_trx'),
                DB::raw('SUM(total_buy) as total_qty'),
                DB::raw('SUM(total_payment) as total_omzet')
            )
            ->where('date_sale', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $chartLabels  = [];
        $chartTrx     = [];
        $chartOmzet   = [];
        $chartQty     = [];

        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $y     = (int) $date->format('Y');
            $m     = (int) $date->format('n');
            $label = $date->translatedFormat('M Y');

            $row = $monthlyData->first(fn($r) => (int)$r->year === $y && (int)$r->month === $m);

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

        return view('dashboard', compact(
            'totalUsers',
            'totalProducts',
            'totalTransactions',
            'totalOmzet',
            'totalForecast',
            'trxBulanIni',
            'trxBulanLalu',
            'trxGrowth',
            'omzetBulanIni',
            'chartLabels',
            'chartTrx',
            'chartOmzet',
            'chartQty',
            'topProducts',
            'recentTransactions',
        ));
    }
}
