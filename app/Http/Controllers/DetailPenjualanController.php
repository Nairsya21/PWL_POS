<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
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
        $penjualan = PenjualanModel::all();
        $detailpenjualan = DetailPenjualanModel::all();
        $barang = BarangModel::all();
        $page = (object) [
            'title' => 'Detail Penjualan dari Transaksi '
        ];
        $activeMenu = 'detailpenjualan'; // Set menu yang sedang aktif


        return view('detailpenjualan.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'barang' => $barang,
            'penjualan' => $penjualan,
            'detailpenjualan' => $detailpenjualan,
            'activeMenu' => $activeMenu
        ]);
    }

    // List Detail Penjualan berdasarkan penjualan_id
    public function list()
    {
        $details = DetailPenjualanModel::with(['barang', 'penjualan'])->select('detail_id', 'penjualan_id', 'barang_id', 'harga', 'jumlah');
        return DataTables::of($details)
            ->addIndexColumn()
            ->addColumn('aksi', function ($details) {
                $btn  = '<button onclick="modalAction(\'' . url('/detail/' . $details->detail_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/detail/' . $details->detail_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/detail/' . $details->detail_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }
    public function create_ajax()
    {
        $penjualans = PenjualanModel::all();
        $barangs = BarangModel::all();
        return view('detailpenjualan.create_ajax', ['penjualans' => $penjualans, 'barangs' => $barangs]);
    }

    public function getHargaBarang(string $id)
    {
        // Log ID barang yang diterima
        Log::info('ID barang yang diterima:', ['id' => $id]);

        $barang = BarangModel::find($id);

        if ($barang) {
            Log::info('Barang ditemukan:', ['harga' => $barang->harga_jual]);
            return response()->json(['status' => true, 'harga' => $barang->harga_jual]);
        } else {
            Log::warning('Barang tidak ditemukan:', ['id' => $id]);
            return response()->json(['status' => false, 'message' => 'Barang tidak ditemukan']);
        }
    }


    public function show_ajax(string $id)
    {
        // Mengambil data penjualan berdasarkan id
        $penjualan = PenjualanModel::findOrFail($id);

        // Mengambil detail penjualan berdasarkan penjualan_id
        $details = DetailPenjualanModel::where('penjualan_id', $id)->with('barang')->get();

        // $barangs = BarangModel::find($details->barang_id);
        // Mengirimkan data ke view untuk di-load menggunakan DataTables
        return view('penjualan.show_ajax', [
            'penjualan' => $penjualan,
            // 'barangs' => $barangs,
            'details' => $details
        ]);
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
