<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyPenjualanIdForeignOnTPenjualanDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('t_penjualan_detail', function (Blueprint $table) {
            // Drop the existing foreign key constraint
            $table->dropForeign(['penjualan_id']);

            // Re-add the foreign key with onDelete('cascade')
            $table->foreign('penjualan_id')
                ->references('penjualan_id')
                ->on('t_penjualan')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('t_penjualan_detail', function (Blueprint $table) {
            // Drop the foreign key with cascade
            $table->dropForeign(['penjualan_id']);

            // Re-add the foreign key without cascade
            $table->foreign('penjualan_id')
                ->references('penjualan_id')
                ->on('t_penjualan');
        });
    }
}
