<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SportSessionRequest;
use Illuminate\Support\Facades\Log;



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
            'machines' => 'required|string|max:5000',
            'duration' => 'required|integer|min:1|max:8',
        ]);

        $jobData = $request->only(['goal', 'level', 'machines', 'duration']);

        Log::info('Requesting a sport session', $jobData);
        Log::info('User token', ['token' => $request->session()->getId()]);

        dispatch(new SportSessionRequest(
            $jobData['goal'],
            $jobData['level'],
            $jobData['machines'],
            $jobData['duration'],
            $request->session()->getId()
        ));

        return redirect()->back()->with('success', 'You will receive a email when your request is processed');
    }
}
