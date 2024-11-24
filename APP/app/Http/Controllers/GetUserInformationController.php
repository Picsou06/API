<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User_information;
use Illuminate\Support\Facades\Log;

class GetUserInformationController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.forms');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validatedData = $request->validate([
            'size' => ['required', 'integer', 'min:0'],
            'weight' => ['required', 'integer', 'min:0'],
            'sex' => ['required', 'integer'],
            'sessions_per_week' => ['required', 'integer', 'min:0', 'max:7'],
            'session_duration' => ['required', 'integer', 'min:0'],
            'max_weight' => ['required', 'integer', 'min:0'],
        ]);

        Log::info($validatedData);

        if (!$validatedData['size'])
            return redirect()->back()->withErrors("Vous devez renseigner votre taille!")->withInput();
        if (!$validatedData['weight'])
            return redirect()->back()->withErrors("Vous devez renseigner votre poids!")->withInput();
        if (!$validatedData['sex'])
            return redirect()->back()->withErrors("Vous devez renseigner votre sexe!")->withInput();
        if (!$validatedData['sessions_per_week'])
            return redirect()->back()->withErrors("Vous devez renseigner le nombre de séances par semaine!")->withInput();
        if (!$validatedData['session_duration'])
            return redirect()->back()->withErrors("Vous devez renseigner la durée de vos séances!")->withInput();
        if (!$validatedData['max_weight'])
            return redirect()->back()->withErrors("Vous devez renseigner le poids maximum que vous pouvez soulever!")->withInput();

        $user = User_information::create([
            'user_id' => Auth::id(),
            'size' => $request->size,
            'weight' => $request->weight,
            'sexe' => $request->sex,
            'nb_session' => $request->sessions_per_week,
            'session_duration' => $request->session_duration,
            'max_weight' => $request->max_weight,
        ]);

        return redirect(route('dashboard', absolute: false));
    }
}
