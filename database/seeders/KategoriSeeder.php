<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data =[
            ['kategori_id' => 1, 'kategori_kode' => 'SNK', 'kategori_nama'=>'Snack'],
            ['kategori_id' => 2, 'kategori_kode' => 'DRY', 'kategori_nama'=>'Dairy'],
            ['kategori_id' => 3, 'kategori_kode' => 'BNF', 'kategori_nama'=>'Beef and Fish'],
            ['kategori_id' => 4, 'kategori_kode' => 'FRT', 'kategori_nama'=>'Fruit'],
            ['kategori_id' => 5, 'kategori_kode' => 'DSR', 'kategori_nama'=>'Dessert'],
        ];
        DB::table('m_kategori')->insert($data);

    }
}
