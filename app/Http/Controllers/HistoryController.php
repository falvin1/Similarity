<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DocumentHistory;
use Illuminate\Support\Facades\Auth;

class HistoryController extends Controller {
    public function index() {
        $histories = DocumentHistory::where('user_id', Auth::id())->with('document')->latest()->get();
        return view('history.index', compact('histories'));
    }
}
