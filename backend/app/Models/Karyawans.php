<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Karyawans extends Model
{
    use HasFactory;
    protected $table = 'karyawans';
    protected $guarded = ['id'];
    
    /**
     * Get the user that owns the Karyawans
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the divisions that owns the Karyawans
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function divisions(): BelongsTo
    {
        return $this->belongsTo(Divisions::class, 'divisi_id');
    }
}
