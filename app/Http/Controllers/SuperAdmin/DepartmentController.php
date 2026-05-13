<?php
// app/Http/Controllers/SuperAdmin/DepartmentController.php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepartmentController extends Controller
{
    public function index(): View
    {
        $departments = Department::withCount('users')
            ->with('currentBudget')
            ->orderBy('dept_name')
            ->get();

        return view('superadmin.departments.index', compact('departments'));
    }

    public function create(): View
    {
        return view('superadmin.departments.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dept_name'   => ['required', 'string', 'max:255', 'unique:departments,dept_name'],
            'budget_code' => ['required', 'string', 'max:50', 'unique:departments,budget_code'],
        ]);

        Department::create($validated);

        return redirect()->route('superadmin.departments.index')
            ->with('success', 'Departemen berhasil ditambahkan.');
    }

    public function edit(Department $department): View
    {
        return view('superadmin.departments.edit', compact('department'));
    }

    public function update(Request $request, Department $department): RedirectResponse
    {
        $validated = $request->validate([
            'dept_name'   => ['required', 'string', 'max:255', 'unique:departments,dept_name,' . $department->id],
            'budget_code' => ['required', 'string', 'max:50', 'unique:departments,budget_code,' . $department->id],
        ]);

        $department->update($validated);

        return redirect()->route('superadmin.departments.index')
            ->with('success', 'Departemen berhasil diperbarui.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        // Validasi: jangan hapus jika masih ada user atau budget aktif
        if ($department->users()->count() > 0) {
            return back()->with('warning', 'Departemen masih memiliki user terkait. Pindahkan user terlebih dahulu.');
        }

        if ($department->currentBudget) {
            return back()->with('warning', 'Departemen masih memiliki budget aktif tahun ini.');
        }

        $department->delete();

        return redirect()->route('superadmin.departments.index')
            ->with('success', 'Departemen berhasil dihapus.');
    }
}
