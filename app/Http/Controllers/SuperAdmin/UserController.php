<?php
// app/Http/Controllers/SuperAdmin/UserController.php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::with('department')
            ->where('role', '!=', 'superadmin')
            ->orderByDesc('created_at');

        // Filter by role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter by department
        if ($request->filled('dept_id')) {
            $query->where('dept_id', $request->dept_id);
        }

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users       = $query->paginate(15)->withQueryString();
        $departments = Department::orderBy('dept_name')->get();
        $roles       = ['staff', 'ka_sie', 'ka_dept', 'ka_div', 'fin_dir', 'man_dir', 'prod_dir', 'pres_dir'];

        return view('superadmin.users.index', compact('users', 'departments', 'roles'));
    }

    public function create(): View
    {
        $departments = Department::orderBy('dept_name')->get();
        $roles       = ['staff', 'ka_sie', 'ka_dept', 'ka_div', 'fin_dir', 'man_dir', 'prod_dir', 'pres_dir'];
        
        $sectionsByDept = User::where('role', 'ka_sie')
            ->whereNotNull('section')
            ->whereNotNull('dept_id')
            ->get()
            ->groupBy('dept_id')
            ->map(fn($users) => $users->pluck('section')->unique()->values()->all());

        return view('superadmin.users.create', compact('departments', 'roles', 'sectionsByDept'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'=> ['required', 'string', 'min:8', 'confirmed'],
            'role'    => ['required', Rule::in(['staff', 'ka_sie', 'ka_dept', 'ka_div', 'fin_dir', 'man_dir', 'prod_dir', 'pres_dir'])],
            'dept_id' => ['required_if:role,staff,ka_sie', 'nullable', 'exists:departments,id'],
            'section' => ['required_if:role,staff,ka_sie', 'nullable', 'string', 'max:255'],
            'cost_center_id' => ['nullable', 'exists:cost_centers,id'],
        ]);

        User::create([
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'role'           => $validated['role'],
            'dept_id'        => $validated['dept_id'] ?? null,
            'section'        => $validated['section'] ?? null,
            'cost_center_id' => $validated['cost_center_id'] ?? null,
        ]);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user): View
    {
        $departments = Department::orderBy('dept_name')->get();
        $roles       = ['staff', 'ka_sie', 'ka_dept', 'ka_div', 'fin_dir', 'man_dir', 'pres_dir'];
        
        $sectionsByDept = User::where('role', 'ka_sie')
            ->whereNotNull('section')
            ->whereNotNull('dept_id')
            ->get()
            ->groupBy('dept_id')
            ->map(fn($users) => $users->pluck('section')->unique()->values()->all());

        return view('superadmin.users.edit', compact('user', 'departments', 'roles', 'sectionsByDept'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'=> ['nullable', 'string', 'min:8', 'confirmed'],
            'role'    => ['required', Rule::in(['staff', 'ka_sie', 'ka_dept', 'ka_div', 'fin_dir', 'man_dir', 'prod_dir', 'pres_dir'])],
            'dept_id' => ['required_if:role,staff,ka_sie', 'nullable', 'exists:departments,id'],
            'section' => ['required_if:role,staff,ka_sie', 'nullable', 'string', 'max:255'],
            'cost_center_id' => ['nullable', 'exists:cost_centers,id'],
        ]);

        $data = [
            'name'           => $validated['name'],
            'email'          => $validated['email'],
            'role'           => $validated['role'],
            'dept_id'        => $validated['dept_id'] ?? null,
            'section'        => $validated['section'] ?? null,
            'cost_center_id' => $validated['cost_center_id'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isSuperAdmin()) {
            return back()->with('warning', 'Tidak bisa menghapus superadmin.');
        }

        $user->delete();

        return redirect()->route('superadmin.users.index')
            ->with('success', 'User berhasil dihapus.');
    }
}
