<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\sportSession;

class SessionListController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $sessions = sportSession::where('user_id', $user->id)->get();
        return view('sportsession.show.index', compact('sessions'));
    }
}
