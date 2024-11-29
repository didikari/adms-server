<div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <!-- Filter: Individu -->
        <div>
            <label for="selectedUser" class="block text-sm font-medium text-gray-700 mb-2">User</label>
            <select id="selectedUser" wire:model="selectedUser"
                class="block w-full p-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">All User</option>
                @foreach ($users as $user)
                    @if (empty($searchUser) || stripos($user->name, $searchUser) !== false)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>

        <!-- Filter: Tanggal Mulai -->
        <div>
            <label for="startDate" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
            <input type="date" id="startDate" wire:model="startDate"
                class="block w-full p-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <!-- Filter: Tanggal Akhir -->
        <div>
            <label for="endDate" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Akhir</label>
            <input type="date" id="endDate" wire:model="endDate"
                class="block w-full p-3 rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
        </div>
    </div>

    <!-- Filter Button -->
    <button wire:click="filter"
        class="block w-full sm:w-auto py-3 px-6 bg-indigo-600 text-white rounded-md shadow-md hover:bg-indigo-700 transition duration-300">
        Filter
    </button>

    <!-- Table -->
    <div class="overflow-x-auto mt-8 bg-white shadow rounded-lg border border-gray-200">
        <table class="min-w-full table-auto">
            <thead class="bg-gray-100 border-b">
                <tr>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">No</th>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Nama</th>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Tanggal</th>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Scan Masuk</th>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Scan Pulang</th>
                    <th class="py-3 px-6 text-left text-sm font-medium text-gray-700">Jam Kerja</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attendances as $attendance)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="py-4 px-6 text-sm text-gray-800">{{ $loop->iteration }}</td>
                        <td class="py-4 px-6 text-sm text-gray-800">{{ $attendance->user->name }}</td>
                        <td class="py-4 px-6 text-sm text-gray-800">
                            {{ \Carbon\Carbon::parse($attendance->timestamp)->format('Y-m-d') }}</td>
                        <td>{{ $attendance->scanIn }}</td>
                        <td>{{ $attendance->scanOut }}</td>
                        <!-- Menampilkan Durasi Kerja -->
                        <td class="py-4 px-6 text-sm text-gray-800">
                            @isset($attendance->hoursWorked)
                                {{ $attendance->hoursWorked }}
                            @else
                                <span class="text-red-500">Tidak ada data</span>
                            @endisset
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-sm text-gray-500">Tidak ada data untuk filter
                            yang dipilih</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if ($selectedUser)
            <div class="mt-4 pb-4">
                @isset($totalWorkedHours)
                    @if ($totalWorkedHours > 0)
                        <strong class="ml-5">Total Jam Kerja: </strong>
                        {{ $totalWorkedHours }}
                    @endif
                @endisset
            </div>
        @endif
    </div>
    @if ($attendances->isNotEmpty() && ($selectedUser || $startDate || $endDate))
        <div class="mt-6">
            <button wire:click="export"
                class="py-3 px-6 bg-green-600 text-white rounded-md shadow-md hover:bg-green-700 transition duration-300">
                Export to Excel
            </button>
        </div>
    @endif

</div>
