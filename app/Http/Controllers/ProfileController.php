<?php
// app/Http/Controllers/ProfileController.php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman profil user beserta info department & role.
     */
    public function edit(Request $request): View
    {
        $departments = Department::orderBy('dept_name')->get();

        return view('profile.edit', [
            'user'        => $request->user(),
            'departments' => $departments,
        ]);
    }

    /**
     * Update data profil user.
     * Catatan: role TIDAK bisa diubah sendiri oleh user.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        // Isi hanya field yang diizinkan (dept_id boleh diubah, role tidak)
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Hapus akun user.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}