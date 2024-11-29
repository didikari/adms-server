<div>
    <form wire:submit.prevent="importExcel">
        <input type="file" wire:model="file"
            class="border border-gray-300 rounded-md p-2 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        @error('file')
            <span class="error">{{ $message }}</span>
        @enderror
        <button type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-opacity-75">
            Import
        </button>
    </form>
</div>
