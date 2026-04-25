<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminDoctorController extends Controller
{
    public function index() {
        $doctors = User::whereHas('role', fn($q) => $q->where('name','doctor'))
                       ->where('status','approved')->get();
        return view('admin.doctors.index', compact('doctors'));
    }

    public function pending() {
        $doctors = User::whereHas('role', fn($q) => $q->where('name','doctor'))
                       ->where('status','pending')->get();
        return view('admin.doctors', compact('doctors'));
    }
    
    public function approve($id) {
        $user = User::findOrFail($id);
        abort_if($user->role?->name !== 'doctor', 403);

        if ($user->status === 'approved') {
            return back()->with('info', __('cms.doctors.already_approved'));
        }

        $user->update(['status' => 'approved']);
        return back()->with('success', __('cms.doctors.approved_success'));
    }
    
    public function reject($id) {
        $user = User::findOrFail($id);
        abort_if($user->role?->name !== 'doctor', 403);

        if ($user->status === 'rejected') {
            return back()->with('info', __('cms.doctors.already_rejected'));
        }

        $user->update(['status' => 'rejected']);
        return back()->with('success', __('cms.doctors.rejected_success'));
    }

    public function rejected(): View
    {
        $doctors = User::whereHas('role', fn($q) => $q->where('name', 'doctor'))
            ->where('status', 'rejected')
            ->latest()
            ->get();

        return view('admin.doctors.rejected', compact('doctors'));
    }

    public function restore(Request $request, User $doctor): RedirectResponse
    {
        if ($doctor->status !== 'rejected') {
            return redirect()->back()->with('error', __('cms.doctors.not_rejected'));
        }

        $newStatus = $request->input('status');

        if (!in_array($newStatus, ['pending', 'approved'])) {
            return redirect()->back()->with('error', __('cms.doctors.invalid_status'));
        }

        $doctor->update(['status' => $newStatus]);

        $message = $newStatus === 'approved'
            ? __('cms.doctors.restored_approved')
            : __('cms.doctors.restored_pending');

        return redirect()->back()->with('success', $message);
    }
}
