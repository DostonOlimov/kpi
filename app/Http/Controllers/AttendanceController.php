<?php

namespace App\Http\Controllers;

use App\Imports\AttendanceImport;
use App\Imports\AttendanceImport2;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $year     = session('year') ?? (int) date('Y');
        $selected = array_filter(array_map('intval', (array) $request->input('months', [])));
        $day      = $request->input('day');

        if (empty($selected)) {
            $selected = [(int) date('m')];
        }

        $query = Attendance::whereYear('date', $year)
            ->whereIn(DB::raw('MONTH(date)'), $selected)
            ->orderBy('date', 'asc')
            ->orderBy('first_in', 'asc')
            ->with('user');

        if ($day) {
            $query->whereDay('date', (int) $day);
        }

        $attendances = $query->paginate(50)->appends($request->query());

        $daysInMonth = (count($selected) === 1)
            ? cal_days_in_month(CAL_GREGORIAN, $selected[0], $year)
            : null;

        return view('attendances.index', compact('attendances', 'year', 'selected', 'day', 'daysInMonth'));
    }

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

        return redirect()->route('attendances.index')
            ->with('message', 'Davomat holati muvaffaqiyatli yangilandi!');
    }

    /**
     * Display the authenticated user's own attendance records.
     */
    public function myAttendances(Request $request)
    {
        $user     = auth()->user();
        $year     = session('year') ?? (int) date('Y');
        $selected = array_filter(array_map('intval', (array) $request->input('months', [])));

        if (empty($selected)) {
            $selected = [(int) date('m')];
        }

        $attendances = Attendance::where('external_id', $user->ch_id)
            ->whereYear('date', $year)
            ->whereIn(DB::raw('MONTH(date)'), $selected)
            ->orderBy('date', 'asc')
            ->get();

        return view('attendances.my', compact('attendances', 'year', 'selected'));
    }
}
