<?php

namespace App\Http\Controllers;

use App\Models\StockRecommendation;
use Illuminate\Http\Request;

class StockRecommendationController extends Controller
{
    public function index(Request $request)
    {
        $query = StockRecommendation::with('product')->latest();

        if ($request->filled('tipe_periode')) {
            $query->where('tipe_periode', $request->tipe_periode);
        }

        $recommendations = $query->paginate(20)->withQueryString();

        return view('stock-recommendations.index', compact('recommendations'));
    }
    
    public function destroyFilter(Request $request) {
        $query = StockRecommendation::query();
        if ($request->filled('tipe_periode')) {
             $query->where('tipe_periode', $request->tipe_periode);
        }
        $query->delete();
        return back()->with('success', 'Riwayat rekomendasi berhasil dihapus.');
    }
}
