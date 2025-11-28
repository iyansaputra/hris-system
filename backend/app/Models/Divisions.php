<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Divisions extends Model
{
    use HasFactory;
    protected $table = 'divisions';
    protected $guarded = ['id'];

    /**
     * Get the karyawans associated with the Divisions
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function karyawans(): HasOne
    {
        return $this->hasOne(Karyawans::class, 'divisi_id');
    }
}
