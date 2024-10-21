<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use App\Models\StokModel;
use App\Models\SupplierModel;
use App\Models\BarangModel;
use App\Models\UserModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;


class StokController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Stok',
            'list' => ['Home', 'Stok']
        ];
        $page = (object) [
            'title' => 'Daftar Stok dalam sistem'
        ];
        $activeMenu = 'stok'; // Set menu yang sedang aktif
        $suppliers = SupplierModel::all();
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.index', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'users' => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    // Ambil data stok dalam bentuk JSON untuk DataTables
    public function list(Request $request)
    {
        $stocks = StokModel::with(['supplier', 'barang', 'user'])->select('stok_id', 'supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah');

        return DataTables::of($stocks)
            ->addIndexColumn()
            ->addColumn('aksi', function ($stock) {
                $btn  = '<button onclick="modalAction(\'' . url('/stok/' . $stock->stok_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stock->stok_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/stok/' . $stock->stok_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi'])
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Stok',
            'list' => ['Home', 'Stok', 'Tambah']
        ];
        $page = (object) [
            'title' => 'Tambah stok baru'
        ];
        $activeMenu = 'stok';
        $suppliers = SupplierModel::all();
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.create', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'users' => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:m_supplier,supplier_id',
            'barang_id' => 'required|exists:m_barang,barang_id',
            'user_id' => 'required|exists:m_user,user_id',
            'stok_tanggal' => 'required|date',
            'stok_jumlah' => 'required|numeric',
        ]);

        StokModel::create([
            'supplier_id' => $request->supplier_id,
            'barang_id' => $request->barang_id,
            'user_id' => $request->user_id,
            'stok_tanggal' => $request->stok_tanggal,
            'stok_jumlah' => $request->stok_jumlah,
        ]);

        return redirect('/stok')->with('success', 'Data stok berhasil disimpan');
    }

    public function show(string $id)
    {
        $stock = StokModel::with(['supplier', 'barang', 'user'])->find($id);
        if (!$stock) {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }

        $breadcrumb = (object)[
            'title' => 'Detail Stok',
            'list' => ['Home', 'Stok', 'Detail']
        ];
        $page = (object)[
            'title' => 'Detail Stok'
        ];
        $activeMenu = 'stok';

        return view('stok.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'stock' => $stock, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id)
    {
        $stock = StokModel::find($id);
        if (!$stock) {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }

        $breadcrumb = (object)[
            'title' => 'Edit Stok',
            'list' => ['Home', 'Stok', 'Edit']
        ];
        $page = (object)[
            'title' => 'Edit Stok'
        ];
        $activeMenu = 'stok';
        $suppliers = SupplierModel::all();
        $barangs = BarangModel::all();
        $users = UserModel::all();

        return view('stok.edit', [
            'breadcrumb' => $breadcrumb,
            'page' => $page,
            'stock' => $stock,
            'suppliers' => $suppliers,
            'barangs' => $barangs,
            'users' => $users,
            'activeMenu' => $activeMenu
        ]);
    }

    public function update(Request $request, string $id)
    {
        $stock = StokModel::find($id);
        if (!$stock) {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }

        $request->validate([
            'supplier_id' => 'required|exists:m_supplier,supplier_id',
            'barang_id' => 'required|exists:m_barang,barang_id',
            'user_id' => 'required|exists:m_user,user_id',
            'stok_tanggal' => 'required|date',
            'stok_jumlah' => 'required|numeric',
        ]);

        $stock->update([
            'supplier_id' => $request->supplier_id,
            'barang_id' => $request->barang_id,
            'user_id' => $request->user_id,
            'stok_tanggal' => $request->stok_tanggal,
            'stok_jumlah' => $request->stok_jumlah,
        ]);

        return redirect('/stok')->with('success', 'Data stok berhasil diubah');
    }

    public function destroy(string $id)
    {
        $stock = StokModel::find($id);
        if (!$stock) {
            return redirect('/stok')->with('error', 'Data stok tidak ditemukan');
        }

        try {
            $stock->delete();
            return redirect('/stok')->with('success', 'Data stok berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/stok')->with('error', 'Data stok gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        $suppliers = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barangs = BarangModel::select('barang_id', 'barang_nama')->get();
        return view('stok.create_ajax', ['suppliers' => $suppliers, 'barangs' => $barangs]);
    }
    public function show_ajax(string $id)
    {
        $stock = StokModel::findOrFail($id);
        $suppliers = SupplierModel::find($stock->supplier_id);
        $barangs = BarangModel::find($stock->barang_id);
        return view('stok.show_ajax', ['suppliers' => $suppliers, 'barangs' => $barangs, 'stock' => $stock]);
    }

    // Store a newly created item via AJAX
    public function store_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'user_id' => 'required|integer|exists:m_user,user_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|numeric|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            StokModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    // Display the form for editing an item via AJAX
    public function edit_ajax(string $id)
    {
        $stock = StokModel::find($id);
        $suppliers = SupplierModel::select('supplier_id', 'supplier_nama')->get();
        $barangs = BarangModel::select('barang_id', 'barang_nama')->get();
        return view('stok.edit_ajax', ['stock' => $stock, 'suppliers' => $suppliers, 'barangs' => $barangs]);
    }

    // Update an existing item via AJAX
    public function update_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'supplier_id' => 'required|integer|exists:m_supplier,supplier_id',
                'barang_id' => 'required|integer|exists:m_barang,barang_id',
                'user_id' => 'required|integer|exists:m_user,user_id',
                'stok_tanggal' => 'required|date',
                'stok_jumlah' => 'required|numeric|min:0',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $stock = StokModel::find($id);
            if (!$stock) {
                return response()->json([
                    'status' => false,
                    'message' => 'Data stok tidak ditemukan',
                ]);
            }

            $stock->update($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data stok berhasil diubah'
            ]);
        }
        return redirect('/');
    }
    public function confirm_ajax(string $id)
    {
        $stok = StokModel::find($id);

        return view('stok.confirm_ajax', ['stok' => $stok]);
    }

    // Deleting Supplier via AJAX
    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $stok = StokModel::find($id);

            if ($stok) {
                $stok->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data stok berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Data tidak ditemukan'
                ]);
            }
        }

        return redirect('/');
    }
    public function export_pdf()
    {
        $stok = StokModel::select('stok_id', 'supplier_id', 'barang_id', 'user_id', 'stok_tanggal', 'stok_jumlah')
            ->orderBy('stok_id')
            ->with('supplier')
            ->with('barang')
            ->with('user')
            ->get();

        $pdf = Pdf::loadView('stok.export_pdf', ['stok' => $stok]);
        $pdf->setPaper('a4', 'portrait'); //set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); //set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data stok ' . date('Y-m-d H:i:s') . 'pdf');
    }
    public function export_excel()
    {
        $stok = StokModel::with(['supplier', 'barang', 'user'])->orderBy('stok_tanggal')->get();

        // Load Excel library
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for Excel sheet
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Barang');
        $sheet->setCellValue('C1', 'Nama Barang');
        $sheet->setCellValue('D1', 'Nama Supplier');
        $sheet->setCellValue('E1', 'User');
        $sheet->setCellValue('F1', 'Tanggal Stok');
        $sheet->setCellValue('G1', 'Jumlah Stok');

        // Bold the headers
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);

        // Populate the sheet with data
        $no = 1;
        $row = 2;
        foreach ($stok as $stock) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $stock->barang->barang_kode);
            $sheet->setCellValue('C' . $row, $stock->barang->barang_nama);
            $sheet->setCellValue('D' . $row, $stock->supplier->supplier_nama);
            $sheet->setCellValue('E' . $row, $stock->user->nama);
            $sheet->setCellValue('F' . $row, $stock->stok_tanggal);
            $sheet->setCellValue('G' . $row, $stock->stok_jumlah);
            $row++;
            $no++;
        }

        // Auto size the columns
        foreach (range('A', 'G') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet->setTitle('Data Stok');

        // Create Excel file and prompt download
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Stok ' . date('Y-m-d H:i:s') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Cache-Control: max-age=1');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        $writer->save('php://output');
        exit;
    }
    public function import()
    {
        return view('stok.import');
    }
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_stok' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Failed',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_stok'); // Get file from request
            $reader = IOFactory::createReader('Xlsx');
            $reader->setReadDataOnly(true);
            $spreadsheet = $reader->load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray(null, false, true, true);
            $insert = [];

            if (count($data) > 1) {
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // Skip header row
                        $insert[] = [
                            'supplier_id' => SupplierModel::where('supplier_nama', $value['C'])->first()->supplier_id,
                            'barang_id' => BarangModel::where('barang_nama', $value['B'])->first()->barang_id,
                            'user_id' => auth()->user()->user_id, // Assuming the logged-in user is adding the stock
                            'stok_tanggal' => $value['F'],
                            'stok_jumlah' => $value['G'],
                            'created_at' => now(),
                        ];
                    }
                }
                if (count($insert) > 0) {
                    StokModel::insertOrIgnore($insert); // Insert into the stock table
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Data successfully imported'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No data to import'
                ]);
            }
        }
        return redirect('/');
    }
}
