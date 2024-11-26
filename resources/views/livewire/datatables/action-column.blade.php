<div class="flex items-center space-x-2">
    @isset($viewLink)
        <a href="{{ $viewLink }}" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-eye"></i></a>
    @endisset

    @isset($editLink)
        <a href="{{ $editLink }}" class="text-yellow-500 hover:text-yellow-700"><i
                class="fa-solid fa-pen-to-square"></i></a>
    @endisset

    @isset($deleteLink)
        <form action="{{ $deleteLink }}" method="POST" x-ref="deleteForm" class="inline"
            @submit.prevent="if (showDeleteConfirm) $refs.deleteForm.submit()">
            @method('DELETE')
            @csrf
            <button type="button" class="text-red-500 hover:text-red-700 p-0"
                @click="
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: 'Data ini akan dihapus secara permanen!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $refs.deleteForm.submit();  // Menggunakan x-ref untuk submit form
                }
            })
        ">
                <i class="fa-solid fa-trash"></i>
            </button>
        </form>
    @endisset
</div>
