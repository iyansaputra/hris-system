<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $guarded = ['id'];

    protected $hidden = [
        'password',
        'remember_token'
    ];

    protected $casts = [
        'email_verifited_at' => 'daytime'
    ];

    //Relasi Table User
    public function karyawans()
    {
        return $this->hasOne(Karyawans::class, 'user_id');
    }

    /**
     * Get the attendances associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function attendances(): HasOne
    {
        return $this->hasOne(Attendances::class, 'user_id');
    }

    /**
     * Get the leave associated with the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function leaves(): HasOne
    {
        return $this->hasOne(Leave::class, 'user_id');
    }
}
