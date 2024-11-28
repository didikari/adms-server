<div>
    <form wire:submit.prevent="importExcel">
        <input type="file" wire:model="file">
        @error('file')
            <span class="error">{{ $message }}</span>
        @enderror
        <button type="submit">Import</button>
    </form>

    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif
</div>
