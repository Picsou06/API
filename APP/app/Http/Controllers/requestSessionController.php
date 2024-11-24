<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SportSessionRequest;


class requestSessionController extends Controller
{
    public function index()
    {
        return view('sportsession.request.request');
    }

    public function store(Request $request)
    {
        $request->validate([
            'goal' => 'required|string|max:255',
            'level' => 'required|string|in:beginner,intermediate,advanced',
            'machines' => 'required|string|max:255',
            'duration' => 'required|integer|min:1|max:8',
        ]);

        $jobData = $request->only(['goal', 'level', 'machines', 'duration']);

        dispatch(new SportSessionRequest($jobData));

        return redirect()->back()->with('success', 'You will receive a email when your request is processed');
    }
}
