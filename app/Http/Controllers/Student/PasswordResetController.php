<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Mail\StudentResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /**
     * Student tokens are stored with 'student:' prefix in password_reset_tokens
     * to prevent collision with user (Admin/Doctor) tokens.
     */
    private function tokenKey(string $email): string
    {
        return 'student:' . $email;
    }

    /** GET /student/forgot-password */
    public function showRequest()
    {
        return view('student.forgot-password');
    }

    /** POST /student/forgot-password */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $student = DB::table('students')->where('email', $request->email)->first();

        // Always return success — do not reveal whether the email exists
        if ($student) {
            $token    = Str::random(64);
            $tokenKey = $this->tokenKey($request->email);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $tokenKey],
                ['token' => $token, 'created_at' => now()]
            );

            Mail::to($request->email)->send(
                new StudentResetPasswordMail($token, $request->email)
            );
        }

        return back()->with('status', __('cms.auth.reset_link_sent'));
    }

    /** GET /student/reset-password/{token} */
    public function showReset(string $token)
    {
        $record = DB::table('password_reset_tokens')
            ->where('token', $token)
            ->whereRaw("email LIKE 'student:%'")
            ->first();

        if (! $record || Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return redirect()->route('student.password.request')
                ->withErrors(['email' => __('cms.auth.token_expired')]);
        }

        // Strip the 'student:' prefix to show the real email to user
        $email = substr($record->email, 8);

        return view('student.reset-password', ['token' => $token, 'email' => $email]);
    }

    /** POST /student/reset-password */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $tokenKey = $this->tokenKey($request->email);

        $record = DB::table('password_reset_tokens')
            ->where('email', $tokenKey)
            ->where('token', $request->token)
            ->first();

        if (! $record || Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => __('cms.auth.token_expired')]);
        }

        $updated = DB::table('students')
            ->where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        if (! $updated) {
            return back()->withErrors(['email' => __('cms.auth.token_expired')]);
        }

        // Single-use: delete token immediately after successful reset
        DB::table('password_reset_tokens')->where('email', $tokenKey)->delete();

        return redirect()->route('student.login')
            ->with('success', __('cms.auth.reset_success'));
    }
}
