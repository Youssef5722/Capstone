<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Requests\StudentProfileUpdateRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        return view('student.profile.show', [
            'student' => Auth::guard('student')->user(),
        ]);
    }

    public function update(StudentProfileUpdateRequest $request)
    {
        $student = Auth::guard('student')->user();
        $student->update(['email' => $request->email]);

        return back()->with('success', __('cms.profile.update_success'));
    }

    public function updatePassword(PasswordUpdateRequest $request)
    {
        $student = Auth::guard('student')->user();

        if (! Hash::check($request->current_password, $student->password)) {
            return back()
                ->withErrors(['current_password' => __('cms.profile.current_password_wrong')])
                ->withInput();
        }

        $student->update(['password' => Hash::make($request->password)]);

        return back()->with('success', __('cms.profile.password_updated'));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
        ]);

        $student = Auth::guard('student')->user();

        // Delete old avatar if exists
        if ($student->avatar && Storage::disk('public')->exists($student->avatar)) {
            Storage::disk('public')->delete($student->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $student->update(['avatar' => $path]);

        return back()->with('success', __('cms.profile.avatar_updated'));
    }
}
