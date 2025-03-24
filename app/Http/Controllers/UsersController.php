<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function usersPage()
    {
        $users = User::all(); // Ambil semua user dari database
        return view('admin.users', compact('users'));
    }
}
