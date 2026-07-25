<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Register hanya terbuka bila belum ada user sama sekali (admin pertama).
     */
    public function create(): View|RedirectResponse
    {
        if (User::count() > 0) {
            return redirect()->route('login')
                ->with('status', 'Registrasi ditutup. Akun dibuat oleh admin melalui menu Kelola User.');
        }

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     * Registrasi pertama otomatis menjadi admin; setelah ada user, diblokir.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // blokir registrasi bila sudah ada user (admin sudah dibuat)
        if (User::count() > 0) {
            abort(403, 'Registrasi ditutup. Akun baru dibuat oleh admin melalui menu Kelola User.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => User::ROLE_ADMIN, // user pertama = admin
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }
}
