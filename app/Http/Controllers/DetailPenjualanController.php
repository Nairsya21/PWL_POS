<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\DetailPenjualanModel;
use App\Models\PenjualanModel;
use App\Models\BarangModel;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;



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
    public function list(Request $request)
    {
        $details = DetailPenjualanModel::with(['barang', 'penjualan'])->select('detail_id', 'penjualan_id', 'barang_id', 'harga', 'jumlah');
        //Filter detail penjualan berdasarkan user
        if ($request->penjualan_id) {
            $details->where('penjualan_id', $request->penjualan_id);
        }
        return DataTables::of($details)
            ->addIndexColumn()
            ->addColumn('aksi', function ($details) {
                $btn  = '<button onclick="modalAction(\'' . url('/detailpenjualan/' . $details->detail_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/detailpenjualan/' . $details->detail_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/detailpenjualan/' . $details->detail_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button>';
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
        $details = DetailPenjualanModel::findOrFail($id);
        $penjualans = PenjualanModel::find($details->penjualan_id);
        $barangs = BarangModel::find($details->barang_id);


        // Mengirimkan data ke view untuk di-load menggunakan DataTables
        return view('detailpenjualan.show_ajax', [
            'penjualans' => $penjualans,
            'barangs' => $barangs,
            'details' => $details
        ]);
    }

    // Menambahkan Detail Penjualan baru via AJAX
    public function store_ajax(Request $request)
    {
        // Validasi data
        if ($request->ajax() || $request->wantsJson()) {
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
            DetailPenjualanModel::create($request->all());
            return response()->json([
                'status' => true,
                'message' => 'Detail penjualan berhasil disimpan'
            ]);
        }
        return redirect('/');
    }

    // Mengedit Detail Penjualan via AJAX
    public function edit_ajax(string $id)
    {
        $penjualans = PenjualanModel::all();
        $detail = DetailPenjualanModel::find($id);
        $barangs = BarangModel::all();
        return view('detailpenjualan.edit_ajax', ['penjualans' => $penjualans, 'detail' => $detail, 'barangs' => $barangs]);
    }

    // Memperbarui Detail Penjualan via AJAX
    public function update_ajax(Request $request, $detail_id)
    {
        // Validasi data
        if ($request->ajax() || $request->wantsJson()) {
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
    }
    public function confirm_ajax(string $id)
    {
        $detail = DetailPenjualanModel::find($id);
        $penjualan = PenjualanModel::find($id);
        $barang = BarangModel::find($id);
        return view('detailpenjualan.confirm_ajax', ['detail' => $detail, 'penjualan' => $penjualan, 'barang' => $barang]);
    }

    // Menghapus Detail Penjualan via AJAX
    public function delete_ajax(Request $request, $id)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $detail = DetailPenjualanModel::find($id);
            if ($detail) {
                $detail->delete();
                return response()->json([
                    'status' => true,
                    'message' => 'Detail penjualan berhasil dihapus'
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'Detail penjualan tidak ditemukan'
                ]);
            }
        }
        return redirect('/');
    }
    public function export_pdf()
    {
        $detail = DetailPenjualanModel::select('detail_id', 'penjualan_id', 'barang_id', 'harga', 'jumlah')
            ->orderBy('penjualan_id')
            ->orderBy('barang_id')
            ->with('penjualan')
            ->get();

        $pdf = Pdf::loadView('detailpenjualan.export_pdf', ['detail' => $detail]);
        $pdf->setPaper('a4', 'portrait'); //set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); //set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data Detail Penjualan ' . date('Y-m-d H:i:s') . 'pdf');
    }
    public function import()
    {
        return view('detailpenjualan.import');
    }

    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'file_detail_penjualan' => ['required', 'mimes:xlsx', 'max:1024'],
            ];
            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Failed',
                    'msgField' => $validator->errors(),
                ]);
            }

            $file = $request->file('file_detail_penjualan'); // Get file from request
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
                            'penjualan_id' => $value['A'], // Adjust column index as per your template
                            'barang_id' => $value['B'], // Adjust column index as per your template
                            'harga' => $value['C'], // Adjust column index as per your template
                            'jumlah' => $value['D'], // Adjust column index as per your template
                            'created_at' => now(),
                        ];
                    }
                }
                if (count($insert) > 0) {
                    DetailPenjualanModel::insertOrIgnore($insert); // Insert into the penjualan table
                }
                return response()->json([
                    'status' => true,
                    'message' => 'Data successfully imported',
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'message' => 'No data to import',
                ]);
            }
        }
        return redirect('/');
    }

    public function export_excel()
    {
        $detail = DetailPenjualanModel::with('penjualan', 'barang')->orderBy('penjualan_id')->get();
        // Load Excel library
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set headers for Excel sheet
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Kode Transaksi');
        $sheet->setCellValue('C1', 'Barang');
        $sheet->setCellValue('D1', 'Harga');
        $sheet->setCellValue('E1', 'Jumlah');

        // Bold the headers
        $sheet->getStyle('A1:E1')->getFont()->setBold(true);

        // Populate the sheet with data
        $no = 1;
        $row = 2;
        foreach ($detail as $d) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $d->penjualan->penjualan_kode);
            $sheet->setCellValue('C' . $row, $d->barang->barang_nama);
            $sheet->setCellValue('D' . $row, $d->harga);
            $sheet->setCellValue('E' . $row, $d->jumlah);
            $row++;
            $no++;
        }

        // Auto size the columns
        foreach (range('A', 'E') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet->setTitle('Data Penjualan');

        // Create Excel file and prompt download
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Detail Penjualan ' . date('Y-m-d H:i:s') . '.xlsx';

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
}
