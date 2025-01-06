<?php

namespace App\Http\Controllers;

use App\Models\Log;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::all();
        $products = Product::all();

        $startDate = $request->start_date ?? null;
        $endDate = $request->end_date ?? null;
        if($startDate && !$endDate){
            $currentStartDate = Carbon::parse($startDate);
            $startDate = $currentStartDate->startOfDay()->addMinute()->toDateTimeString();
            $endDate = $currentStartDate->endOfDay()->subMinute()->toDateTimeString();
        }

        $query = Log::query();

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->product_ids) {
            $query->where('changes', 'LIKE', '%' . $request->product_ids . '%');
        }

        $logs = $query->orderBy('id', 'desc')->paginate(10);

        return view('pages.log.index', [
            'logs' => $logs,
            'start_date' => $startDate ?? null,
            'end_date' => $endDate ?? null,
            'user_id' => $request->user_id ?? null,
            'product_ids' => $request->product_ids ?? null,
            'users' => $users,
            'products' => $products,
        ]);
    }
}
