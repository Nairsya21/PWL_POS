<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\LevelModel;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\IOFactory;


class LevelController extends Controller
{
    public function index()
    {
        $breadcrumb = (object) [
            'title' => 'Daftar Level',
            'list' => ['Home', 'Level']
        ];
        $page = (object) [
            'title' => 'Daftar Level dalam sistem'
        ];
        $activeMenu = 'level'; // Set menu yang sedang aktif

        return view('level.index', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    // Ambil data level dalam bentuk json untuk datatables 
    public function list(Request $request)
    {
        $levels = LevelModel::select('level_id', 'level_kode', 'level_nama');

        return DataTables::of($levels)
            ->addIndexColumn()
            ->addColumn('aksi', function ($level) { // menambahkan kolom aksi 
                // $btn  = '<a href="'.url('/level/' . $level->level_id).'" class="btn btn-info btn-sm">Detail</a> '; 
                // $btn .= '<a href="'.url('/level/' . $level->level_id . '/edit').'" class="btn btn-warning btn-sm">Edit</a> '; 
                // $btn .= '<form class="d-inline-block" method="POST" action="'.url('/level/'.$level->level_id).'">'.csrf_field().method_field('DELETE') .'<button type="submit" class="btn btn-danger btn-sm" onclick="return confirm(\'Apakah Anda yakin menghapus data ini?\');">Hapus</button></form>';      
                $btn  = '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/show_ajax') . '\')" class="btn btn-info btn-sm">Detail</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/edit_ajax') . '\')" class="btn btn-warning btn-sm">Edit</button> ';
                $btn .= '<button onclick="modalAction(\'' . url('/level/' . $level->level_id . '/delete_ajax') . '\')" class="btn btn-danger btn-sm">Hapus</button> ';
                return $btn;
            })
            ->rawColumns(['aksi']) // memberitahu bahwa kolom aksi adalah html 
            ->make(true);
    }

    public function create()
    {
        $breadcrumb = (object) [
            'title' => 'Tambah Level',
            'list' => ['Home', 'Level', 'Tambah']
        ];
        $page = (object) [
            'title' => 'Tambah level baru'
        ];
        $activeMenu = 'level';

        return view('level.create', ['breadcrumb' => $breadcrumb, 'page' => $page, 'activeMenu' => $activeMenu]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'level_kode' => 'required|string|max:10|unique:m_level,level_kode',
            'level_nama' => 'required|string|max:100',
        ]);

        LevelModel::create([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama,
        ]);

        return redirect('/level')->with('success', 'Data level berhasil disimpan');
    }

    public function show(string $id)
    {
        $level = LevelModel::find($id);
        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        $breadcrumb = (object)[
            'title' => 'Detail Level',
            'list' => ['Home', 'Level', 'Detail']
        ];
        $page = (object)[
            'title' => 'Detail Level'
        ];
        $activeMenu = 'level';

        return view('level.show', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function edit(string $id)
    {
        $level = LevelModel::find($id);
        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        $breadcrumb = (object)[
            'title' => 'Edit Level',
            'list' => ['Home', 'Level', 'Edit']
        ];
        $page = (object)[
            'title' => 'Edit Level'
        ];
        $activeMenu = 'level';

        return view('level.edit', ['breadcrumb' => $breadcrumb, 'page' => $page, 'level' => $level, 'activeMenu' => $activeMenu]);
    }

    public function update(Request $request, string $id)
    {
        $level = LevelModel::find($id);
        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        $request->validate([
            'level_kode' => 'required|string|max:10|unique:m_level,level_kode,' . $id . ',level_id',
            'level_nama' => 'required|string|max:100',
        ]);

        $level->update([
            'level_kode' => $request->level_kode,
            'level_nama' => $request->level_nama,
        ]);

        return redirect('/level')->with('success', 'Data level berhasil diubah');
    }

    public function destroy(string $id)
    {
        $level = LevelModel::find($id);
        if (!$level) {
            return redirect('/level')->with('error', 'Data level tidak ditemukan');
        }

        try {
            $level->delete();
            return redirect('/level')->with('success', 'Data level berhasil dihapus');
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect('/level')->with('error', 'Data level gagal dihapus karena masih terdapat tabel lain yang terkait dengan data ini');
        }
    }

    public function create_ajax()
    {
        return view('level.create_ajax');
    }
    public function show_ajax(string $id)
    {
        // Mencari data level berdasarkan id
        $level = LevelModel::findOrFail($id);

        // Jika data level tidak ditemukan, akan mengembalikan response dengan error
        if (!$level) {
            return response()->json(['error' => 'Data level tidak ditemukan'], 404);
        }

        // Mengembalikan view untuk ditampilkan di modal menggunakan Ajax
        return view('level.show_ajax', compact('level'));
    }


    // Menyimpan data level baru melalui Ajax
    public function store_ajax(Request $request)
    {
        // cek apakah request berupa ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_nama' => 'required|string|min:3|unique:m_level,level_nama',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            LevelModel::create($request->all());

            return response()->json([
                'status' => true,
                'message' => 'Data level berhasil disimpan'
            ]);
        }

        return redirect('/');
    }

    // Menampilkan halaman form edit level Ajax
    public function edit_ajax(string $id)
    {
        $level = LevelModel::find($id);

        return view('level.edit_ajax', ['level' => $level]);
    }

    // Menyimpan perubahan data level melalui Ajax
    public function update_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                'level_nama' => 'required|string|max:100|unique:m_level,level_nama,' . $id . ',level_id',
            ];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validasi Gagal',
                    'msgField' => $validator->errors(),
                ]);
            }

            $check = LevelModel::find($id);
            if ($check) {
                $check->update($request->all());

                return response()->json([
                    'status' => true,
                    'message' => 'Data level berhasil diupdate'
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

    // Konfirmasi penghapusan level Ajax
    public function confirm_ajax(string $id)
    {
        $level = LevelModel::find($id);

        return view('level.confirm_ajax', ['level' => $level]);
    }

    // Menghapus data level melalui Ajax
    public function delete_ajax(Request $request, $id)
    {
        // cek apakah request dari ajax
        if ($request->ajax() || $request->wantsJson()) {
            $level = LevelModel::find($id);

            if ($level) {
                $level->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Data level berhasil dihapus'
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
        $level = LevelModel::select('level_id', 'level_kode', 'level_nama')
            ->orderBy('level_id')
            ->orderBy('level_kode')
            ->get();

        $pdf = Pdf::loadView('level.export_pdf', ['level' => $level]);
        $pdf->setPaper('a4', 'portrait'); //set ukuran kertas dan orientasi
        $pdf->setOption("isRemoteEnabled", true); //set true jika ada gambar dari url
        $pdf->render();

        return $pdf->stream('Data level ' . date('Y-m-d H:i:s') . 'pdf');
    }
    public function import()
    {
        return view('level.import');
    }
    public function import_ajax(Request $request)
    {
        if ($request->ajax() || $request->wantsJson()) {
            $rules = [
                // Validate file must be xlsx and max 1MB
                'file_level' => ['required', 'mimes:xlsx', 'max:1024']
            ];
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation Failed',
                    'msgField' => $validator->errors()
                ]);
            }

            $file = $request->file('file_level'); // Get file from request
            $reader = IOFactory::createReader('Xlsx'); // Load excel file reader
            $reader->setReadDataOnly(true); // Only read data
            $spreadsheet = $reader->load($file->getRealPath()); // Load the excel file
            $sheet = $spreadsheet->getActiveSheet(); // Get active sheet
            $data = $sheet->toArray(null, false, true, true); // Fetch data from excel
            $insert = [];

            if (count($data) > 1) { // If data has more than 1 row
                foreach ($data as $baris => $value) {
                    if ($baris > 1) { // Skip the header (first row)
                        $insert[] = [
                            'level_nama' => $value['A'],
                            'created_at' => now(),
                        ];
                    }
                }
                if (count($insert) > 0) {
                    // Insert data into database, ignore duplicates
                    LevelModel::insertOrIgnore($insert);
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

    public function export_excel()
    {
        $levels = LevelModel::orderBy('level_nama')->get(); // Get level data

        // Load Excel library
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet(); // Get active sheet

        // Set headers for Excel sheet
        $sheet->setCellValue('A1', 'No');
        $sheet->setCellValue('B1', 'Nama Level');

        // Bold the headers
        $sheet->getStyle('A1:B1')->getFont()->setBold(true);

        // Populate the sheet with data
        $no = 1;
        $row = 2;
        foreach ($levels as $key => $level) {
            $sheet->setCellValue('A' . $row, $no);
            $sheet->setCellValue('B' . $row, $level->level_nama);
            $row++;
            $no++;
        }

        // Auto size the columns
        foreach (range('A', 'B') as $columnID) {
            $sheet->getColumnDimension($columnID)->setAutoSize(true);
        }
        $sheet->setTitle('Data Level');

        // Create Excel file and prompt download
        $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
        $filename = 'Data Level ' . date('Y-m-d H:i:s') . '.xlsx';

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
