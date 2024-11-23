<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\IclockController;
use App\Livewire\AttendanceTable;
use App\Livewire\DevicesTable;
use App\Livewire\FingerLogTable;
use App\Livewire\UserTable;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/iclock/cdata', [IclockController::class, 'handshake']);
Route::post('/iclock/cdata', [IclockController::class, 'receiveRecords']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/devices', DevicesTable::class)->name('devices');
    Route::delete('/devices/destroy/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::get('/attendance', AttendanceTable::class)->name('attendance');
    Route::get('/fingerlog', FingerLogTable::class)->name('fingerlog');
    Route::get('/users', UserTable::class)->name('users');
});
