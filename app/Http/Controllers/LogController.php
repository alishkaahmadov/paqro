<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $logs = Log::orderBy('id', 'desc')->paginate(10);
        return view('pages.log.index', ['logs' => $logs]);
    }
}
