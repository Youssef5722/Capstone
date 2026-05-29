<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    /** GET /forgot-password */
    public function showRequest()
    {
        return view('auth.forgot-password');
    }

    /** POST /forgot-password */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $user = DB::table('users')->where('email', $request->email)->first();

        // Always return success — do not reveal whether the email exists
        if ($user) {
            $token = Str::random(64);

            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                ['token' => $token, 'created_at' => now()]
            );

            Mail::to($request->email)->send(new ResetPasswordMail($token, $request->email));
        }

        return back()->with('status', __('cms.auth.reset_link_sent'));
    }

    /** GET /reset-password/{token} */
    public function showReset(string $token)
    {
        $record = DB::table('password_reset_tokens')->where('token', $token)->first();

        if (! $record || Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return redirect()->route('password.request')
                ->withErrors(['email' => __('cms.auth.token_expired')]);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $record->email]);
    }

    /** POST /reset-password */
    public function reset(Request $request)
    {
        $request->validate([
            'token'    => ['required'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', $request->token)
            ->first();

        if (! $record || Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return back()->withErrors(['email' => __('cms.auth.token_expired')]);
        }

        $updated = DB::table('users')
            ->where('email', $request->email)
            ->update(['password' => Hash::make($request->password)]);

        if (! $updated) {
            return back()->withErrors(['email' => __('cms.auth.token_expired')]);
        }

        // Single-use: delete token immediately after reset
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')
            ->with('success', __('cms.auth.reset_success'));
    }
}
