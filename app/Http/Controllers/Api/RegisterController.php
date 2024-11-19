<?php

namespace App\Http\Controllers\Api;

use App\Models\UserModel;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


class RegisterController extends Controller
{
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'nama' => 'required',
            'password' => 'required|min:5|confirmed',
            'level_id' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        // Proses upload gambar
        // if ($request->hasFile('image')) {
        //     $image = $request->file('image');
        //     $imagePath = $image->store('posts', 'public'); // Mengunggah gambar ke storage/app/public/posts
        // } else {
        //     $imagePath = null; // Jika tidak ada gambar yang diunggah
        // }

        $user = UserModel::create([
            'username' => $request->username,
            'nama' => $request->nama,
            'password' => bcrypt($request->password),
            'level_id' => $request->level_id,
            // 'image' => $request->image,
            'image' => $request->image->hashName() // Simpan path gambar
        ]);
        if ($user) {
            return response()->json([
                'success' => true,
                'user' => $user,
            ], 201);
        }

        return response()->json([
            'success' => false,
        ], 409);
    }
    public function show()
    {
        $users = UserModel::all(); // Retrieve all users from the m_user table

        return response()->json([
            'success' => true,
            'users' => $users,
        ], 200);
    }
}
