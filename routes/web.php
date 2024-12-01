<?php

use App\Events\AttendanceImported;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\IclockController;
use App\Http\Controllers\UserController;
use App\Livewire\AttendanceReport;
use App\Livewire\AttendanceTable;
use App\Livewire\DevicesTable;
use App\Livewire\FingerLogTable;
use App\Livewire\Pages;
use App\Livewire\Users\UserEdit;
use App\Livewire\UserTable;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('/iclock/cdata', [IclockController::class, 'handshake']);
Route::get('/iclock/getrequest', [IclockController::class, 'getRequest']);
Route::post('/iclock/cdata', [IclockController::class, 'receiveRecords']);

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/devices', DevicesTable::class)->name('devices');
    Route::delete('/devices/destroy/{id}', [DeviceController::class, 'destroy'])->name('devices.destroy');
    Route::get('/attendance', AttendanceTable::class)->name('attendance');
    Route::get('/my-attendance', AttendanceReport::class)->name('my.attendance');
    Route::get('/fingerlog', FingerLogTable::class)->name('fingerlog');
    Route::get('/users', UserTable::class)->name('users');
    Route::get('/users/{user}/edit', UserEdit::class)->name('users.edit');

    // Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
});
