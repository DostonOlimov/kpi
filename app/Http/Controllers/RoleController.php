<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RoleController extends Controller
{
    /**
     * Display a listing of the roles.
     */
    public function index(): View
    {
        $roles = Role::orderBy('id', 'asc')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create(): View
    {
        return view('roles.create');
    }

    /**
     * Store a newly created role in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Role::create($request->only('name'));

        return redirect()->route('roles.index')
            ->with('success', 'Role muvaffaqatli yaratildi.');
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role): View
    {
        return view('roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role): View
    {
        return view('roles.edit', compact('role'));
    }

    /**
     * Update the specified role in storage.
     */
    public function update(Request $request, Role $role): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $role->update($request->only('name'));

        return redirect()->route('roles.index')
            ->with('success', 'Ma\'lumotlar muvaffaqiyatli o\'zgartirildi.');
    }

    /**
     * Remove the specified role from storage.
     * Prevent deletion if role is assigned to any users.
     */
    public function destroy(Role $role): RedirectResponse
    {
        if ($role->users()->exists()) {
            return redirect()->route('roles.index')
                ->with('error', 'Ushbu rol foydalanuvchilarga biriktirilganligi sababli o‘chirib bo‘lmaydi.');
        }

        $role->delete();

        return redirect()->route('roles.index')
            ->with('success', 'Ma\'lumotlar o\'chirildi.');
    }
}
