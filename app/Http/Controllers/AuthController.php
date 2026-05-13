<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle login request
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // If an intended URL exists, try to resolve it to a named route
            // and allow the redirect only if the user has the corresponding permission.
            $intended = $request->session()->pull('url.intended');
            if ($intended) {
                try {
                    $intendedRequest = \Illuminate\Http\Request::create($intended);
                    $matched = app('router')->getRoutes()->match($intendedRequest);
                    $intendedRouteName = $matched ? $matched->getName() : null;
                    if ($intendedRouteName) {
                        $perm = User::permissionFromRouteName($intendedRouteName, $intendedRequest->method());
                        if ($perm && $user->hasPermission($perm)) {
                            return redirect()->to($intended)->with('success', 'Login berhasil');
                        }
                    }
                } catch (\Throwable $e) {
                    // ignore and fall back to priority route
                }
            }

            // Redirect to the first accessible route based on priority
            $first = $user->getFirstAccessibleRoute();
            if ($first) {
                return redirect()->route($first)->with('success', 'Login berhasil');
            }

            // No accessible route: show authorization error (keeps middleware protections intact)
            abort(403, 'Anda tidak memiliki izin untuk mengakses halaman ini.');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah',
        ])->withInput($request->only('email'));
    }

    /**
     * Handle logout request
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Logout berhasil');
    }

    /**
     * Show profile edit form
     */
    public function showProfile()
    {
        return view('auth.profile', ['user' => Auth::user()]);
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Update name and email
        $user->name = $validated['name'];
        $user->email = $validated['email'];

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui');
    }
}
