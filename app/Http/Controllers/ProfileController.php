<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function show()
    {
        return view('profile.show', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'      => 'required|string|max:255',
            'matric_id' => 'required|string|max:50|unique:users,matric_id,' . $user->id,
            'email'     => 'required|email|max:255|unique:users,email,' . $user->id,
            'password'  => ['nullable', 'confirmed', Password::min(6)],
        ], [
            'name.required'      => 'Sila masukkan nama.',
            'matric_id.unique'   => 'Matric Number ini sudah digunakan.',
            'email.unique'       => 'Email ini sudah digunakan.',
            'password.confirmed' => 'Password tidak sepadan.',
        ]);

        $user->name      = $request->name;
        $user->matric_id = $request->matric_id;
        $user->email     = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berjaya dikemaskini.');
    }
}
