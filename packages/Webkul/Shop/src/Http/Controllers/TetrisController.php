<?php

namespace Webkul\Shop\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TetrisController extends Controller
{
    use DispatchesJobs, ValidatesRequests;

    public function index()
    {
        $scores = DB::table('tetris_scores')
            ->join('customers', 'tetris_scores.customer_id', '=', 'customers.id')
            ->select('customers.first_name', 'customers.last_name', DB::raw('MAX(tetris_scores.score) as score'))
            ->groupBy('customers.id', 'customers.first_name', 'customers.last_name')
            ->orderByDesc('score')
            ->limit(10)
            ->get();

        return view('shop::tetris.index', compact('scores'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'score' => 'required|integer',
        ]);

        $customer = Auth::guard('customer')->user();

        if ($customer) {
            DB::table('tetris_scores')->insert([
                'customer_id' => $customer->id,
                'score' => $request->score,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Score saved!']);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
