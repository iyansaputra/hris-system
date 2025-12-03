<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Profile\UpdateProfileRequest;

class ProfileController extends Controller
{
    //Untuk Upload Avatar
    public function update(UpdateProfileRequest $request)
    {
        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $path = $request->file('avatar')->store('avatar', 'public');
            $user->avatar = $path;
        }

        //Ganti Password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return response()->json([
            'status' => 'succes',
            'message' => 'Profil Berhasil Diperbarui',
            'data' => $user,
            'avatar_url' => $user->avatar ? asset('storage/' . $user->avatar) : null
        ]);
    }
}
