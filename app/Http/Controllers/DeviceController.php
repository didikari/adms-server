<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;

class DeviceController extends Controller
{
    public function destroy($id)
    {
        $device = Device::findOrFail($id);
        $device->delete();

        // Flash a success message to the session
        session()->flash('message', 'Device deleted successfully');

        return redirect()->route('devices');
    }
}
