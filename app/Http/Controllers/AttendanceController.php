<?php

namespace App\Http\Controllers;

use App\Imports\AttendanceImport;
use App\Imports\AttendanceImport2;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class AttendanceController extends Controller
{
    /**
     * Display attendances filtered by date.
     * Default date is today.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));

        $attendances = Attendance::where('date', $date)
            ->orderBy('first_in', 'asc')
            ->with('user')
            ->paginate(50);

        return view('attendances.index', compact('attendances', 'date'));
    }

    /**
     * Show the upload form.
     *
     * @param Request $request
     * @return View
     */
    public function showUploadForm(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        $type = $request->input('type', 'kirish');

        return view('attendances.upload', compact('date', 'type'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240',
            'type' => 'required|in:kirish,chiqish',
            'date' => 'required|date',
        ]);

        try {
            $type     = $request->input('type');
            $date     = $request->input('date');
            $filePath = $request->file('file')->getRealPath();

            if ($type === 'kirish') {
                $result = (new \App\Imports\AttendanceImport2)->import($filePath);
            } else {
                $result = (new \App\Imports\AttendanceImport)->import($filePath);
            }

            $typeName = $type === 'kirish' ? 'Kirish' : 'Chiqish';
            $message  = "{$typeName} ma'lumotlari muvaffaqiyatli yuklandi! ({$result['imported']} ta yozuv)";

            if (!empty($result['errors'])) {
                $message .= ' Xatolar: ' . implode(', ', array_slice($result['errors'], 0, 3));
            }

            return redirect()->route('attendances.index', ['date' => $date])
                ->with('message', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['file' => 'Xatolik yuz berdi: ' . $e->getMessage()]);
        }
    }

    /**
     * Update attendance status and comment.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $attendance = Attendance::findOrFail($id);

        $validated = $request->validate([
            'status' => 'nullable|string|max:255',
            'comment' => 'nullable|string|max:500',
        ]);

        $attendance->update([
            'status' => $validated['status'] ?? null,
            'comment' => $validated['comment'] ?? $attendance->comment,
        ]);

        return redirect()->route('attendances.index', ['date' => $attendance->date])
            ->with('message', 'Davomat holati muvaffaqiyatli yangilandi!');
    }
}
