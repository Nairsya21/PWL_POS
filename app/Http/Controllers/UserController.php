<?php

namespace App\Http\Controllers;

use App\Models\LevelModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
// use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(){
        $breadcrumb = (object) [
            'title' => 'Daftar User',
            'list' => ['Home', 'User']
        ];
        $page = (object) [
            'title' => 'Daftar user yang terdaftar dalam sistem'
        ];
        $activeMenu = 'user'; //set menu yang sedang aktif

        return view('user.index', ['breadcrumb'=> $breadcrumb, 'page'=> $page, 'activeMenu'=> $activeMenu]);
    }
    // public function tambah(){
    //     return view('user_tambah');
    // }
    // public function tambah_simpan(Request $request){
    //     UserModel::create([
    //         'username' =>$request->username,
    //         'nama' =>$request->nama,
    //         'password' =>Hash::make($request->username,),
    //         'level_id' =>$request->level_id,
    //     ]);
    //     return redirect('/user');
    // }
    // public function ubah($id){
    //     $user = UserModel::find($id);
    //     return view('user_ubah', ['data' => $user]);
    // }
    // public function ubah_simpan($id, Request $request){
    //     $user = UserModel::find($id);

    //     $user->username = $request->username;
    //     $user->nama = $request->nama;
    //     $user->password = Hash::make('$request->password');
    //     $user->level_id = $request->level_id;

    //     $user->save();

    //     return redirect('/user');
    // }
    // public function hapus($id){
    //     $user = UserModel::find($id);
    //     $user->delete();

    //     return redirect('/user');
    // }
    // Ambil data user dalam bentuk json untuk datatables 
    public function list(Request $request) 
    { 
        $users = UserModel::select('user_id', 'username', 'nama', 'level_id') 
                    ->with('level'); 
    
        return DataTables::of($users) 
            // menambahkan kolom index / no urut (default nama kolom: DT_RowIndex) 
            ->addIndexColumn()  
            ->addColumn('aksi', function ($user) {  // menambahkan kolom aksi 
                $btn  = '<a href="'.url('/user/' . $user->user_id).'" class="btn btn-info btn-sm">Detail</a> '; 
                $btn .= '<a href="'.url('/user/' . $user->user_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> '; 
                $btn .= '<form class="d-inline-block" method="POST" action="'.url('/user/'.$user->user_id).'">'.csrf_field().method_field('DELETE') .'<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';      
                return $btn; 
            }) 
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html 
            ->make(true); 
        }
    public function create() {
        $breadcrumb = (object) [
            'title' => 'Tambah User',
            'list' => ['Home', 'User', 'Tambah']
        ];
        $page = (object) [
            'title' => 'Tambah user baru'
        ];
        $level = LevelModel::all();
        $activeMenu = 'user';

        return view('user.create', ['breadcrumb'=> $breadcrumb, 'page'=>$page, 'level' =>$level, 'activeMenu'=> $activeMenu]);

    }
    public function store(Request $request){
        $request->validate([
            'username'=> 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password'=> 'required|min:5',
            'level_id'=> 'required|Integer'
        ]);
        
        UserModel::create([
            'username'=> $request->username,
            'nama' => $request->nama,
            'password'=> bcrypt($request->password),
            'level_id'=> $request->level_id
        ]);
        return redirect('/user')->with('success', 'Data user berhasil disimpan');
    }
    public function show(string $id){
        $user = UserModel::with('level')->find($id);
        $breadcrumb = (object)[
            'title' => 'Detail user',
            'list' => ['Home', 'User', 'Detail']
        ];
        $page = (object)[
            'title' => 'Detail user'
        ];
        $activeMenu = 'user';
        return view('user.show', ['breadcrumb'=> $breadcrumb, 'page'=>$page, 'user'=>$user, 'activeMenu'=>$activeMenu]);
    }
    public function edit(string $id){
        $user = UserModel::with('level')->find($id);
        $level = LevelModel::all();

        $breadcrumb = (object)[
            'title' => 'Edit user',
            'list' => ['Home', 'User', 'Edit']
        ];
        $page = (object)[
            'title' => 'Edit user'
        ];
        $activeMenu = 'user';
        return view('user.edit', ['breadcrumb'=> $breadcrumb, 'page'=>$page, 'user'=>$user, 'level'=>$level, 'activeMenu'=>$activeMenu]);
    }
    public function update(Request $request, string $id){
        $request->validate([
            'username'=> 'required|string|min:3|unique:m_user,username',
            'nama' => 'required|string|max:100',
            'password'=> 'nullable|min:5',
            'level_id'=> 'required|Integer'
        ]);
        
        UserModel::find($id)->update([
            'username'=> $request->username,
            'nama' => $request->nama,
            'password'=>$request->password ? bcrypt($request->password) : UserModel::find($id)->password,
            'level_id'=> $request->level_id
        ]);
        return redirect('/user')->with('success', 'Data user berhasil diubah');
    }
    public function destroy(string $id){
        $check = UserModel::find($id);
        if (!$check){
            return redirect('/user')->with('error', 'Data user tidak ditemukan');
        }
        try{
            UserModel::destroy($id);
            return redirect('/user')->with('success' ,'Data user berhasil dihapus');
        }catch(\Illuminate\Database\QueryException $e){
            return redirect('/user')->with('error', 'data user gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }
}
