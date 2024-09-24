<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LevelModel extends Model
{
    use HasFactory;
    // Mendefinisikan nama tabel dan primary key
    protected $table = 'm_level'; // Nama tabel
    protected $primaryKey = 'level_id'; // Nama primary key

    public function users(): HasMany
{
    return $this->hasMany(UserModel::class, 'level_id', 'level_id');
}

}
