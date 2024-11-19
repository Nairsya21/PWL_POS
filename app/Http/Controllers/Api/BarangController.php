<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BarangModel;

class BarangController extends Controller
{
    public function index()
    {
        return BarangModel::all();
    }
    public function store(Request $request)
    {
        $request->validate([
            'barang_kode'    => 'required|string|min:3|unique:m_barang,barang_kode',
            'kategori_id'    => 'required|integer',
            'barang_nama'    => 'required|string|max:100',
            'harga_beli'     => 'required|integer',
            'harga_jual'     => 'required|integer',
            'image'          => 'required|image|max:2048'
        ]);

        $file = $request->file('image');

        // Replace spaces with underscores in the file name
        $filename = time() . '_' . str_replace(' ', '_', $file->getClientOriginalName());
        $path = public_path('images/barang');

        // Move file to public/images/barang
        $file->move($path, $filename);

        // Save data to the database
        $barang = BarangModel::create([
            'barang_kode'    => $request->barang_kode,
            'kategori_id'    => $request->kategori_id,
            'barang_nama'    => $request->barang_nama,
            'harga_beli'     => $request->harga_beli,
            'harga_jual'     => $request->harga_jual,
            'image'          => $filename
        ]);

        return response()->json($barang, 201);
    }

    public function show(BarangModel $barang)
    {
        return BarangModel::find($barang);
    }
    public function update(Request $request, BarangModel $barang)
    {
        $barang->update($request->all());
        return BarangModel::find($barang);
    }
    public function destroy(BarangModel $barang)
    {
        $barang->delete();
        return response()->json([
            'success' => true,
            'message' => 'Data terhapus',
        ]);
    }
}
