<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriModel extends Model
{
    use HasFactory;
    // Mendefinisikan nama tabel dan primary key
    protected $table = 'm_kategori'; // Nama tabel
    protected $primaryKey = 'kategori_id'; // Nama primary key

    protected $fillable = [
        'kategori_kode',  // Tambahkan kategori_kode di sini
        'kategori_nama',
    ];
    public function barangs(): HasMany
{
    return $this->hasMany(BarangModel::class, 'kategori_id', 'kategori_id');
}

}
