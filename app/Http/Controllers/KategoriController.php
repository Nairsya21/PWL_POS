<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KategoriController extends Controller
{
    public function index(){
        // $data = [
        //     'kategori_kode'=> 'SPC',
        //     'kategori_nama' => 'Spice/Bumbu',
        //     'created_at' => now()
        // ];
        // DB::table('m_kategori')->insert($data);
        // return 'Insert data baru berhasil';

        // $row = DB::table('m_kategori')->where('kategori_kode', 'SPC')->update(['kategori_nama' => 'Seasoning']);
        // return 'Update data berhasil. Jumlah data yang diupdate: '.$row.' baris';

        // $row = DB::table('m_kategori') ->where('kategori_kode', 'SPC')->delete();
        // return 'Delete data berhasil. Jumlah data yang dihapus: '.$row. ' baris';

        $data = DB::table('m_kategori')->get();
        return view('kategori', ['data'=>$data]);
    }
}
