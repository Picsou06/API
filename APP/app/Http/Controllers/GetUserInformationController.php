<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Models\User_information;

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
            'size' => ['required', 'int', 'min:0'],
            'weight' => ['required', 'int', 'min:0'],
            'sexe' => ['required', 'int', 'in:0,1,2'],
            'nb_session' => ['required', 'int', 'min:0', 'max:7'],
            'session_duration' => ['required', 'int', 'min:0'],
            'max_weight' => ['required', 'int', 'min:0'],

        ]);

        if (!$validatedData['size'])
            return redirect()->back()->withErrors("Vous devez renseigner votre taille!")->withInput();
        if (!$validatedData['weight'])
            return redirect()->back()->withErrors("Vous devez renseigner votre poids!")->withInput();
        if (!$validatedData['sexe'])
            return redirect()->back()->withErrors("Vous devez renseigner votre sexe!")->withInput();
        if (!$validatedData['nb_session'])
            return redirect()->back()->withErrors("Vous devez renseigner le nombre de séances par semaine!")->withInput();
        if (!$validatedData['session_duration'])
            return redirect()->back()->withErrors("Vous devez renseigner la durée de vos séances!")->withInput();
        if (!$validatedData['max_weight'])
            return redirect()->back()->withErrors("Vous devez renseigner le poids maximum que vous pouvez soulever!")->withInput();

        $user = User_information::create([
            'size' => $request->size,
            'weight' => $request->weight,
            'sexe' => $request->sexe,
            'nb_session' => $request->nb_session,
            'session_duration' => $request->session_duration,
            'max_weight' => $request->max_weight,
        ]);

        return redirect(route('dashboard', absolute: false));
    }
}
