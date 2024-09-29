<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangModel extends Model
{
    use HasFactory;
    // Mendefinisikan nama tabel dan primary key
    protected $table = 'm_barang'; // Nama tabel
    protected $primaryKey = 'barang_id'; // Nama primary key

    protected $fillable = [
        'kategori_id',  
        'barang_kode',  
        'barang_nama',
        'harga_beli',
        'harga_jual',
    ];
    public function kategori(): BelongsTo
{
    return $this->belongsTo(KategoriModel::class, 'kategori_id', 'kategori_id');
}

}
