<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class ProfilController extends Controller
{
    public function index()
    {
        $activeMenu = 'profil';
        $breadcrumb = (object) [
            'title' => 'Edit Profil',
            'list' => ['Home', 'Edit Profil']
        ];
        $page = (object) [
            'title' => 'Upload foto'
        ];
        return view('profil', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }
    public function update(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        // Jika ada avatar lama, hapus dari storage
        if ($user->avatar) {
            Storage::delete('public/avatars/' . $user->avatar);
        }

        if ($request->file('avatar')) {
            $avatarName = time() . '.' . $request->avatar->extension();
            $request->avatar->storeAs('public/avatars', $avatarName);
            /** @var \App\Models\User $user **/
            $user->avatar = $avatarName;
            $user->save();
        } // Upload avatar baru

        return redirect('/profil')->with('success', 'Foto Profil Berhasil Diperbarui!');
    }
}
