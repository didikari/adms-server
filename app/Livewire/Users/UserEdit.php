<?php

namespace App\Livewire\Users;

use App\Models\User;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UserEdit extends Component
{
    public $user;
    public $name;
    public $email;

    // Menginisialisasi data yang akan diedit
    public function mount(User $user)
    {
        // Mengambil data pengguna yang akan diedit
        $this->user = $user;
        $this->name = $user->name;
        $this->email = $user->email;
    }

    // Validasi input
    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($this->user->id)],
        ];
    }

    // Menyimpan perubahan data pengguna
    public function update()
    {
        $this->validate();
        $this->user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);

        // Mengirim notifikasi atau feedback
        session()->flash('message', 'User updated successfully!');

        // Redirect atau update tampilan
        return redirect()->route('users');
    }

    public function render()
    {
        return view('livewire.users.user-edit');
    }
}
