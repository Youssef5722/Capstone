<?php

namespace App\Http\Controllers;

use App\Http\Requests\StudentActivateRequest;
use App\Http\Requests\StudentLoginRequest;
use App\Models\AcademicYear;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class StudentAuthController extends Controller
{
    public function showActivateForm() {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        if (Auth::check()) {
            return redirect()->to(match(Auth::user()->role?->name) {
                'admin'  => route('admin.dashboard'),
                'doctor' => route('doctor.dashboard'),
                default  => route('admin.dashboard'),
            });
        }
        return view('student.activate');
    }

    public function showLoginForm() {
        if (Auth::guard('student')->check()) {
            return redirect()->route('student.dashboard');
        }
        if (Auth::check()) {
            return redirect()->to(match(Auth::user()->role?->name) {
                'admin'  => route('admin.dashboard'),
                'doctor' => route('doctor.dashboard'),
                default  => route('admin.dashboard'),
            });
        }
        return view('student.login');
    }

    public function activate(StudentActivateRequest $request) {
        // Task 08 / Sprint 2 redesign: activation_code is the lookup key.
        // Email is NOT stored at import time — the student registers it here.
        $activeYear = AcademicYear::active();
        if (! $activeYear) {
            abort(403, 'Registration is currently closed.');
        }

        $s = Student::where('activation_code', $request->activation_code)
                    ->where('academic_year_id', $activeYear->id)
                    ->first();

        if (! $s) {
            return back()->withErrors(['activation_code' => __('cms.student.activation_invalid')]);
        }

        if ($s->activation_code_expires_at && now()->isAfter($s->activation_code_expires_at)) {
            return back()->withErrors(['activation_code' => __('cms.student.activation_expired')]);
        }

        if ($s->is_active) {
            return redirect()->route('student.login')
                ->with('info', __('cms.student.already_active'));
        }

        $s->update([
            'email'           => $request->email,
            'password'        => Hash::make($request->password),
            'is_active'       => true,
            'activation_code' => null,
        ]);

        return redirect()->route('student.login')
            ->with('success', __('cms.student.activated_success'));
    }

    public function login(StudentLoginRequest $request) {
        if (!Auth::guard('student')->attempt($request->only('email','password')))
            return back()->withErrors(['email' => __('cms.student.invalid_credentials')])->withInput();
        
        if (!Auth::guard('student')->user()->is_active) {
            Auth::guard('student')->logout();
            return back()->withErrors(['email' => __('cms.student.account_inactive')]);
        }
        
        $request->session()->regenerate();
        return redirect()->route('student.dashboard');
    }

    public function logout(Request $request) {
        Auth::guard('student')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('student.login');
    }
}
