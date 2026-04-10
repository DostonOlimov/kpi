<?php

namespace App\Http\Controllers;

use App\Models\Edodocument;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class EdodocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $query = Edodocument::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by document type
        if ($request->filled('document_type')) {
            $query->where('document_type', 'like', '%' . $request->document_type . '%');
        }

        // Search by document number or sender
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('document_number', 'like', '%' . $search . '%')
                  ->orWhere('sender', 'like', '%' . $search . '%')
                  ->orWhere('summary', 'like', '%' . $search . '%');
            });
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->where('due_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->where('due_date', '<=', $request->date_to);
        }

        $documents = $query->orderBy('due_date', 'asc')->paginate(20);

        return view('edodocuments.index', compact('documents'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return View
     */
    public function create()
    {
        return view('edodocuments.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'document_type' => 'required|string|max:255',
            'due_date' => 'required|date',
            'sender' => 'nullable|string|max:255',
            'task_created_at' => 'required|date',
            'summary' => 'nullable|string',
        ]);

        Edodocument::create([
            'document_number' => $validated['document_number'],
            'document_date' => $validated['document_date'],
            'document_type' => $validated['document_type'],
            'due_date' => $validated['due_date'],
            'sender' => $validated['sender'],
            'task_created_at' => $validated['task_created_at'],
            'summary' => $validated['summary'],
            'status' => 'pending',
        ]);

        return redirect()->route('edodocuments.index')
            ->with('message', 'Hujjat muvaffaqiyatli yaratildi!');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function show($id)
    {
        $document = Edodocument::findOrFail($id);
        return view('edodocuments.show', compact('document'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return View
     */
    public function edit($id)
    {
        $document = Edodocument::findOrFail($id);
        return view('edodocuments.edit', compact('document'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $document = Edodocument::findOrFail($id);

        $validated = $request->validate([
            'document_number' => 'required|string|max:255',
            'document_date' => 'required|date',
            'document_type' => 'required|string|max:255',
            'due_date' => 'required|date',
            'sender' => 'nullable|string|max:255',
            'task_created_at' => 'required|date',
            'summary' => 'nullable|string',
        ]);

        $document->update($validated);

        return redirect()->route('edodocuments.index')
            ->with('message', 'Hujjat muvaffaqiyatli yangilandi!');
    }

    /**
     * Mark document as completed.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function complete($id)
    {
        $document = Edodocument::findOrFail($id);
        $document->markAsCompleted();

        return redirect()->route('edodocuments.index')
            ->with('message', 'Hujjat holati yangilandi!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $document = Edodocument::findOrFail($id);
        $document->delete();

        return redirect()->route('edodocuments.index')
            ->with('message', 'Hujjat muvaffaqiyatli o\'chirildi!');
    }
}
