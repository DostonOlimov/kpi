<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login()
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function authenticate(Request $request): RedirectResponse
    {
        // 1. Validate required fields first (basic)
        $validator = Validator::make($request->all(), [
            'username' => ['required'],
            'password' => ['required'],
        ], [
            'username.required' => 'Foydalanuvchi nomi kiritilishi shart.',
            'password.required' => 'Parol kiritilishi shart.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::where('username', $request->username)->first();

        // 2. Check if user exists
        if (!$user) {
            return back()->withErrors([
                'username' => 'Bunday foydalanuvchi mavjud emas.',
            ])->withInput();
        }

        // 3. Check password
        if (!Auth::attempt([
            'username' => $request->username,
            'password' => $request->password,
        ])) {
            return back()->withErrors([
                'password' => 'Parol noto‘g‘ri.',
            ])->withInput();
        }

        // 4. Success
        $request->session()->regenerate();
        return redirect()->intended('/');
    }


    /**
     * Log the user out of the application.
     *
     * @param Request $request
     * @return Application|Redirector|RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
