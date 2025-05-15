<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
class UsersController extends Controller
{
    public function usersPage()
    {
        $users = User::all(); // Ambil semua user dari database
        return view('admin.users', compact('users'));
    }
    public function destroy(User $user)
{
    // Mencegah admin menghapus dirinya sendiri
    if (Auth::id() === $user->id)  {
        return redirect()->back()->with('error', 'You cannot delete your own account.');
    }

    $user->delete();

    return redirect()->back()->with('success', 'User deleted successfully.');
}
public function update(Request $request, User $user)
{
    $validated = $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => [
            'required', 
            'string', 
            'email', 
            'max:255', 
            Rule::unique('users')->ignore($user->id)
        ],
        'role' => ['required', 'string', 'in:user,admin'],
        'password' => ['nullable', 'string', 'min:8'],
    ]);

    $user->name = $validated['name'];
    $user->email = $validated['email'];
    $user->role = $validated['role'];
    
    if (!empty($validated['password'])) {
        $user->password = Hash::make($validated['password']);
    }
    
    $user->save();

    return redirect()->route('admin.users')
        ->with('success', 'User updated successfully.');
}
}
