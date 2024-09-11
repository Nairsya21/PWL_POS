<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; 


class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'supplier_id' => 1,
                'supplier_kode' => 'IND',
                'supplier_nama' => 'Indofood',
                'supplier_alamat' => 'Perum Pondok Blimbing Indah'
            ],
            [   'supplier_id' => 2,
                'supplier_kode' => 'OTG',
                'supplier_nama' => 'OTGroup',
                'supplier_alamat' => 'Jl. Lingkar Luar Barat Kav. 35 - 36, Cengkareng'
            ],
            [
                'supplier_id' => 3,
                'supplier_kode' => 'JPF',
                'supplier_nama' => 'Japfa Food',
                'supplier_alamat' => 'Jl. MT. Haryono Kav. 16 Jakarta'
            ],
        ];
        DB::table('m_supplier')->insert($data);
    }
}
