<?php

namespace App\Http\Controllers;

use App\Models\WorkZone;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WorkController extends Controller
{
    /**
     * Display a listing of the work zones.
     */
    public function index(): View
    {
        $works = WorkZone::orderBy('id', 'asc')->paginate(10);
        return view('works.index', compact('works'));
    }

    /**
     * Show the form for creating a new work zone.
     */
    public function create(): View
    {
        return view('works.create');
    }

    /**
     * Store a newly created work zone in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        WorkZone::create($request->only('name'));

        return redirect()->route('works.index')
            ->with('success', 'Ish joyi muvaffaqatli yaratildi.');
    }

    /**
     * Display the specified work zone.
     */
    public function show(WorkZone $work): View
    {
        return view('works.show', compact('work'));
    }

    /**
     * Show the form for editing the specified work zone.
     */
    public function edit(WorkZone $work): View
    {
        return view('works.edit', compact('work'));
    }

    /**
     * Update the specified work zone in storage.
     */
    public function update(Request $request, WorkZone $work): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $work->update($request->only('name'));

        return redirect()->route('works.index')
            ->with('success', 'Ma\'lumotlar muvaffaqiyatli o\'zgartirildi.');
    }

    /**
     * Remove the specified work zone from storage.
     */
    public function destroy(WorkZone $work): RedirectResponse
    {
        // Optional: Check for related models before deleting
         if ($work->users()->exists()) {
             return redirect()->route('works.index')
                 ->with('error', 'Ushbu ish joyi bog\'langan foydalanuvchilar mavjudligi sababli o‘chirib bo‘lmaydi.');
         }

        $work->delete();

        return redirect()->route('works.index')
            ->with('success', 'Ma\'lumotlar muvaffaqiyatli o\'chirildi.');
    }
}
