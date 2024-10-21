<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetailPenjualanModel;
use App\Models\PenjualanModel;
use App\Models\BarangModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;

class DetailPenjualanController extends Controller
{
    // Menampilkan halaman detail dari transaksi penjualan
    public function index(string $id)
    {
        $breadcrumb = (object) [
            'title' => 'Detail Penjualan',
            'list' => ['Home', 'Penjualan', 'Detail Penjualan']
        ];
        $penjualan = PenjualanModel::findOrFail($id);
        $barang = BarangModel::all();
        $page = (object) [
            'title' => 'Detail Penjualan dari Transaksi ' . $penjualan->penjualan_kode
        ];
        $activeMenu = 'penjualan'; // Set menu yang sedang aktif

        // Mengambil detail penjualan berdasarkan penjualan_id
        $details = DetailPenjualanModel::where('penjualan_id', $id)->with('barang')->get();

        return view('detailpenjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barang' => $barang,
            'penjualan' => $penjualan,
            'activeMenu' => $activeMenu
        ]);
    }

    // List Detail Penjualan berdasarkan penjualan_id
    public function list(string $id)
    {
        $details = DetailPenjualanModel::where('penjualan_id', $id)->with('barang')->get();

        // Mengembalikan data untuk DataTables
        return DataTables::of($details)
            ->addIndexColumn()  // Menambahkan nomor urut otomatis
            ->addColumn('aksi', function ($detail) {
                $btn  = '<button onclick="modalAction(\'' . url('/penjualan/detail/' . $detail->detail_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/penjualan/detail/' . $detail->detail_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])  // Mengizinkan HTML di kolom aksi
            ->make(true);
    }

    // Menambahkan Detail Penjualan baru via AJAX
    public function store_ajax(Request $request, $penjualan_id)
    {
        // Validasi data
        $rules = [
            'barang_id' => 'required|integer|exists:m_barang,barang_id',
            'harga' => 'required|numeric',
            'jumlah' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        // Menyimpan data detail penjualan
        DetailPenjualanModel::create([
            'penjualan_id' => $penjualan_id,
            'barang_id' => $request->barang_id,
            'harga' => $request->harga,
            'jumlah' => $request->jumlah,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Detail penjualan berhasil disimpan'
        ]);
    }

    // Mengedit Detail Penjualan via AJAX
    public function edit_ajax($detail_id)
    {
        $detail = DetailPenjualanModel::find($detail_id);
        $barangs = BarangModel::all();
        return view('penjualan.detail_edit_ajax', ['detail' => $detail, 'barangs' => $barangs]);
    }

    // Memperbarui Detail Penjualan via AJAX
    public function update_ajax(Request $request, $detail_id)
    {
        // Validasi data
        $rules = [
            'barang_id' => 'required|integer|exists:m_barang,barang_id',
            'harga' => 'required|numeric',
            'jumlah' => 'required|integer|min:1',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validasi Gagal',
                'msgField' => $validator->errors(),
            ]);
        }

        $detail = DetailPenjualanModel::find($detail_id);
        if ($detail) {
            $detail->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Detail penjualan berhasil diperbarui'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Detail penjualan tidak ditemukan'
        ]);
    }

    // Menghapus Detail Penjualan via AJAX
    public function delete_ajax(Request $request, $detail_id)
    {
        $detail = DetailPenjualanModel::find($detail_id);
        if ($detail) {
            $detail->delete();
            return response()->json([
                'status' => true,
                'message' => 'Detail penjualan berhasil dihapus'
            ]);
        }

        return response()->json([
            'status' => false,
            'message' => 'Detail penjualan tidak ditemukan'
        ]);
    }
}
