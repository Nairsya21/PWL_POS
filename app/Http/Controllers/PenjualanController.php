<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PenjualanModel;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class PenjualanController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Penjualan',
            'list' => ['Home', 'Penjualan']
        ];
        $page = (object) [
            'title' => 'Daftar Penjualan dalam sistem'
        ];
        $activeMenu = 'penjualan'; // Set menu yang sedang aktif
        $users = UserModel::all();

        return view('penjualan.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'users' => $users, 'activeMenu' => $activeMenu]);
    }

    // Ambil data penjualan dalam bentuk JSON untuk DataTables
    public function list(Request $request) 
    {
        $penjualans = PenjualanModel::with('user')->select('penjualan_id', 'user_id', 'pembeli', 'penjualan_kode', 'penjualan_tanggal');

        return DataTables::of($penjualans) 
            ->addIndexColumn()  
            ->addColumn('aksi', function ($penjualan) { 
                $btn  = '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/show_ajax').'\')" class="btn btn-info btn-sm">Detail</button> '; 
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/edit_ajax').'\')" class="btn btn-warning btn-sm">Edit</button> '; 
                $btn .= '<button onclick="modalAction(\''.url('/penjualan/' . $penjualan->penjualan_id . '/delete_ajax').'\')" class="btn btn-danger btn-sm">Hapus</button>'; 
                return $btn;
            })
            ->rawColumns(['aksi']) 
            ->make(true); 
    }

    public function create() 
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Penjualan',
            'list' => ['Home', 'Penjualan', 'Tambah']
        ];
        $page = (object) [
            'title' => 'Tambah Penjualan baru'
        ];
        $activeMenu = 'penjualan';
        $users = UserModel::all();

        return view('penjualan.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'users' => $users, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:m_user,user_id',
            'pembeli' => 'required|string|max:100',
            'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode',
            'penjualan_tanggal' => 'required|date',
        ]);
        
        PenjualanModel::create([
            'user_id' => $request->user_id,
            'pembeli' => $request->pembeli,
            'penjualan_kode' => $request->penjualan_kode,
            'penjualan_tanggal' => $request->penjualan_tanggal,
        ]);

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil disimpan');
    }

    public function show(string $id)
    {
        $penjualan = PenjualanModel::with('user')->find($id);
        if (!$penjualan) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }
        
        $breadcrumb = (object)[
            'title' => 'Detail Penjualan',
            'list' => ['Home', 'Penjualan', 'Detail']
        ];
        $page = (object)[
            'title' => 'Detail Penjualan'
        ];
        $activeMenu = 'penjualan';

        return view('penjualan.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'penjualan' => $penjualan, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        if (!$penjualan) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        $breadcrumb = (object)[
            'title' => 'Edit Penjualan',
            'list' => ['Home', 'Penjualan', 'Edit']
        ];
        $page = (object)[
            'title' => 'Edit Penjualan'
        ];
        $activeMenu = 'penjualan';
        $users = UserModel::all();

        return view('penjualan.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'penjualan' => $penjualan, 'users' => $users, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, string $id)
    {
        $penjualan = PenjualanModel::find($id);
        if (!$penjualan) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        $request->validate([
            'user_id' => 'required|exists:m_user,user_id',
            'pembeli' => 'required|string|max:100',
            'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode,'.$id.',penjualan_id',
            'penjualan_tanggal' => 'required|date',
        ]);
        
        $penjualan->update([
            'user_id' => $request->user_id,
            'pembeli' => $request->pembeli,
            'penjualan_kode' => $request->penjualan_kode,
            'penjualan_tanggal' => $request->penjualan_tanggal,
        ]);

        return redirect('/penjualan')->with('success', 'Data penjualan berhasil diubah');
    }

    public function destroy(string $id)
    {
        $penjualan = PenjualanModel::find($id);
        if (!$penjualan) {
            return redirect('/penjualan')->with('error', 'Data penjualan tidak ditemukan');
        }

        try {
            $penjualan->delete();
            return redirect('/penjualan')->with('success', 'Data penjualan berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/penjualan')->with('error', 'Data penjualan gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax() {
        $users = UserModel::select('user_id', 'name')->get();
        return view('penjualan.create_ajax')->with('users', $users);
    }

    // Store a newly created item via AJAX
    public function store_ajax(Request $request) {
        // Check if the request is an AJAX request
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'user_id' => 'required|integer|exists:m_user,user_id',
                'pembeli' => 'required|string|max:100',
                'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode',
                'penjualan_tanggal' => 'required|date',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            PenjualanModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data penjualan berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    // Display the form for editing an item via AJAX
    public function edit_ajax(string $id) {
        $penjualan = PenjualanModel::find($id);
        $users = UserModel::select('user_id', 'name')->get();
        return view('penjualan.edit_ajax', ['penjualan' => $penjualan, 'users' => $users]);
    }

    // Update an existing item via AJAX
    public function update_ajax(Request $request, $id) { 
        // Check if the request is an AJAX request
        if ($request->ajax() || $request->wantsJson()) { 
            $rules = [ 
                'user_id' => 'required|integer|exists:m_user,user_id', 
                'pembeli' => 'required|string|max:100', 
                'penjualan_kode' => 'required|string|unique:t_penjualan,penjualan_kode,'.$id.',penjualan_id', 
                'penjualan_tanggal' => 'required|date',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) { 
                return response()->json([ 
                    'status' => false, 
                    'message' => 'Validasi gagal.', 
                    'msgField' => $validator->errors() 
                ]); 
            }

            $penjualan = PenjualanModel::find($id); 
            if ($penjualan) { 
                $penjualan->update($request->all()); 
                return response()->json([ 
                    'status' => true, 
                    'message' => 'Data penjualan berhasil diperbarui.' 
                ]); 
            } 
    
            return response()->json([ 
                'status' => false, 
                'message' => 'Data penjualan tidak ditemukan.' 
            ]); 
        } 
    }
    
        // Delete an existing item via AJAX
        public function delete_ajax(string $id) {
            $penjualan = PenjualanModel::find($id);
            if (!$penjualan) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan tidak ditemukan.'
                ]);
            }
    
            try {
                $penjualan->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Data penjualan berhasil dihapus.'
                ]);
            } catch (\Illuminate\Database\QueryException $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data penjualan gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini.'
                ]);
            }
        }
    }
    