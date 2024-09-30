<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierModel extends Model
{
    use HasFactory;
    // Mendefinisikan nama tabel dan primary key
    protected $table = 'm_supplier'; // Nama tabel
    protected $primaryKey = 'supplier_id'; // Nama primary key

    protected $fillable = [
        'supplier_kode',  // Tambahkan supplier_kode di sini
        'supplier_nama',
        'supplier_alamat',
    ];
    

}