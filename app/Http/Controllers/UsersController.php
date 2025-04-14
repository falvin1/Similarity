<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}
