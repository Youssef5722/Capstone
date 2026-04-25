<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    /**
     * Guard: already-authenticated users (any guard) must not see the login form.
     * The route-level 'guest' middleware only checks the web guard;
     * a logged-in student-guard user would otherwise slip through to this page.
     */
    public function showLoginForm() {
        if (Auth::check()) {
            return redirect()->to(match(Auth::user()->role?->name) {
                'admin'  => route('admin.dashboard'),
                'doctor' => route('doctor.dashboard'),
                default  => route('admin.dashboard'),
            });
        }
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        return view('auth.login');
    }

    public function showRegisterForm() {
        if (Auth::check()) {
            return redirect()->to(match(Auth::user()->role?->name) {
                'admin'  => route('admin.dashboard'),
                'doctor' => route('doctor.dashboard'),
                default  => route('admin.dashboard'),
            });
        }
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        return view('auth.register');
    }

    public function login(LoginRequest $request) {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return back()->withErrors(['email' => __('cms.auth.invalid_credentials')])->withInput();
        }

        $user = Auth::user();
        // status check applies ONLY to doctor — admin passes freely
        if ($user->role?->name === 'doctor' && $user->status !== 'approved') {
            Auth::logout();
            $message = match($user->status) {
                'pending'  => __('cms.auth.pending_status'),
                'rejected' => __('cms.auth.rejected_status'),
                default    => __('cms.auth.unauthorized'),
            };
            return back()->withErrors(['email' => $message])->withInput();
        }
        
        $request->session()->regenerate();
        
        return match($user->role->name) {
            'admin'  => redirect()->route('admin.dashboard'),
            'doctor' => redirect()->route('doctor.dashboard'),
            default  => redirect('/'),
        };
    }

    public function register(RegisterRequest $request) {
        $role = Role::where('name', 'doctor')->firstOrFail();

        // Fetch level names for display
        $levelNames = collect($request->requested_levels ?? [])
            ->map(fn($id) => \App\Models\Level::find($id)?->name)
            ->filter()
            ->values()
            ->toArray();
        
        $data = array_merge($request->only('name','email','national_id'), [
            'password' => Hash::make($request->password),
            'role_id'  => $role->id,
            'status'   => 'pending',
            'requested_levels' => $levelNames,
        ]);

        if ($request->has('phone')) {
            $data['phone'] = $request->phone;
        }

        User::create($data);
        
        return redirect()->route('login')->with('success', __('cms.auth.registered_wait'));
    }

    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
