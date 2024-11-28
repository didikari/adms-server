<?php

namespace App\Services;

use App\Models\Attendance;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class DeviceService
{
    // Menangani handshake
    public function handshake(Request $request)
    {
        $data = [
            'url' => json_encode($request->all()),
            'data' => $request->getContent(),
            'sn' => $request->input('SN'),
            'option' => $request->input('option'),
        ];

        // Menyimpan log handshake
        DB::table('device_logs')->insert($data);

        // Update status perangkat
        DB::table('devices')->updateOrInsert(
            ['sn' => $request->input('SN')],
            ['name' => "X-302S"],
            ['online' => now()]
        );

        return $this->generateResponse($request->input('SN'));
    }

    // Menangani penerimaan data absensi
    public function receiveRecords(Request $request)
    {
        $content['url'] = json_encode($request->all());
        $content['data'] = $request->getContent();
        DB::table('finger_logs')->insert($content);

        // Memecah data berdasarkan baris
        $arr = preg_split('/\\r\\n|\\r|,|\\n/', $request->getContent());
        $totalRecords = 0;

        try {
            DB::beginTransaction();

            if ($request->input('table') == "OPERLOG") {

                foreach ($arr as $rey) {
                    if (isset($rey) && !empty($rey)) {
                        $totalRecords++;
                    }
                }

                DB::commit();
                return "OK: " . $totalRecords;
            }

            foreach ($arr as $entry) {
                if (empty($entry)) {
                    continue;
                }

                $data = explode("\t", $entry);

                // Memeriksa apakah data cukup panjang
                if (count($data) < 7) {
                    continue;
                }

                $timestamp = $data[1]; // Ambil timestamp dari data
                $time = Carbon::parse($timestamp)->format('H:i');

                $status = (Carbon::parse($time)->between('07:00', '11:00')) ? 'masuk' : 'pulang';


                // Membuat dan menyimpan data absensi
                $attendance = new Attendance([
                    'sn' => $request->input('SN'),
                    'table' => $request->input('table'),
                    'stamp' => $request->input('Stamp'),
                    'employee_id' => $data[0],
                    'timestamp' => $data[1],
                    'status1' => $this->validateAndFormatInteger($data[2] ?? null),
                    'status2' => $this->validateAndFormatInteger($data[3] ?? null),
                    'status3' => $this->validateAndFormatInteger($data[4] ?? null),
                    'status4' => $this->validateAndFormatInteger($data[5] ?? null),
                    'status5' => $this->validateAndFormatInteger($data[6] ?? null),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $attendance->save();

                $user = $attendance->user;
                $name = $user->name;
                if ($user) {
                    $this->sendNotification($name, $data[1]);
                }

                $totalRecords++;
            }

            DB::commit();

            return "OK: " . $totalRecords;
        } catch (Throwable $e) {
            DB::rollBack();

            // Log error yang terjadi
            DB::table('error_log')->insert(['data' => $e->getMessage()]);
            report($e);

            return "ERROR: " . $totalRecords . "\n";
        }
    }

    // Menyusun respon untuk perangkat
    private function generateResponse($sn)
    {
        return "GET OPTION FROM: {$sn}\r\n" .
            "Stamp=9999\r\n" .
            "OpStamp=" . time() . "\r\n" .
            "ErrorDelay=60\r\n" .
            "Delay=30\r\n" .
            "ResLogDay=18250\r\n" .
            "ResLogDelCount=10000\r\n" .
            "ResLogCount=50000\r\n" .
            "TransTimes=00:00;14:05\r\n" .
            "TransInterval=1\r\n" .
            "TransFlag=1111000000\r\n" .
            "Realtime=1\r\n" .
            "Encrypt=0";
    }

    // Validasi dan format integer
    private function validateAndFormatInteger($value)
    {
        return is_numeric($value) ? (int) $value : null;
    }

    private function sendNotification($name, $timestamp)
    {
        $loginUrl = env('API_LOGIN_URL');
        $dataUrl = env('API_MESSAGE_URL');
        $email = env('API_EMAIL');
        $password = env('API_PASSWORD');
        $chatId = env('CHAT_ID');
        $messageThreadId = env('MESSAGE_THREAD_ID');

        $loginData = [
            'email' => $email,
            'password' => $password
        ];

        // Data pesan
        // $time = date('H:i:s');  // Waktu saat ini
        $time = Carbon::parse($timestamp)->format('H:i:s');  // Format waktu dari timestamp
        $dayOfWeek = date('l');  // Hari saat ini

        $message = $this->getAbsensiMessage($name, $time, $dayOfWeek);
        $data = [
            'chat_id' => $chatId,
            'message_thread_id' => $messageThreadId,
            'text' => $message
        ];

        // Kirim permintaan login dan ambil token
        $loginResponse = Http::post($loginUrl, $loginData);

        if ($loginResponse->ok()) {
            // Ambil token dari response login (sesuaikan dengan struktur API)
            $token = $loginResponse->json()['token'];

            // Kirim pesan menggunakan token yang diterima
            $response = Http::withToken($token)->post($dataUrl, $data);

            if ($response->ok()) {
                return response()->json(['status' => 'success', 'data' => $response->json()]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Gagal mengirim pesan'], 400);
            }
        }

        return response()->json(['status' => 'error', 'message' => 'Login gagal'], 400);
    }

    // Fungsi untuk memeriksa waktu absensi
    private function checkAbsensiMasuk($time, $startTime, $endTime)
    {
        return strtotime($time) >= strtotime($startTime) && strtotime($time) <= strtotime($endTime);
    }

    private function checkAbsensiPulang($time, $startTime, $endTime)
    {
        return strtotime($time) >= strtotime($startTime) && strtotime($time) <= strtotime($endTime);
    }

    // Fungsi untuk mendapatkan pesan absensi
    private function getAbsensiMessage($name, $time, $dayOfWeek)
    {
        $satpam = "Zainurrohim";
        if ($name == $satpam) {
            if ($this->checkAbsensiMasuk($time, '17:00:00', '23:59:59')) {
                return $name . ' Melakukan Absensi Masuk Jam ' . $time;
            } elseif ($this->checkAbsensiPulang($time, '00:00:00', '00:00:00')) {
                return $name . ' Melakukan Absensi Pulang Jam ' . $time;
            }
            return $name . ' Waktu absensi tidak valid ' . $time;
        }

        if ($dayOfWeek == 'Saturday') {
            if ($this->checkAbsensiMasuk($time, '06:30:00', '10:00:00')) {
                return $name . ' Melakukan Absensi Masuk Jam ' . $time;
            } elseif ($this->checkAbsensiPulang($time, '10:01:00', '13:00:00')) {
                return $name . ' Melakukan Absensi Pulang Jam ' . $time;
            }
            return $name . ' Waktu absensi tidak valid';
        }

        if (in_array($dayOfWeek, ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'])) {
            if ($this->checkAbsensiMasuk($time, '06:30:00', '12:00:00')) {
                return $name . ' Melakukan Absensi Masuk Jam ' . $time;
            } elseif ($this->checkAbsensiPulang($time, '12:01:00', '17:00:00')) {
                return $name . ' Melakukan Absensi Pulang Jam ' . $time;
            }
            return $name . ' Waktu absensi tidak valid';
        }

        return $name . ' Hari ini bukan hari kerja';
    }

    public function getrequest(Request $request)
    {
        return "OK";
    }
}
