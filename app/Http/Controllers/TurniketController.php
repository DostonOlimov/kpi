<?php

namespace App\Http\Controllers;

use Rats\Zkteco\Lib\ZKTeco;
use Illuminate\Http\Request;

class TurniketController extends Controller
{
    public function getLogs()
    {
        // IP va Portni rasmdagidek kiriting
        $zk = new ZKTeco('194.93.24.34', 8003);

        if ($zk->connect()) {
            // Qurilmadagi barcha davomat yozuvlarini olish
            $attendance = $zk->getAttendance();

            // Ma'lumotlarni tartiblash (oxirgisini birinchi chiqarish)
            $logs = collect($attendance)->reverse()->values();

            return response()->json([
                'status' => 'success',
                'device_time' => $zk->getTime(),
                'total_records' => $logs->count(),
                'data' => $logs->take(20) // Oxirgi 20 ta yozuvni ko'rish
            ]);
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'Turniketga ulanib bo‘lmadi. Port yoki IP noto‘g‘ri.'
            ], 500);
        }
    }
}