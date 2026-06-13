<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',     
        'is_admin',
        'role',                
        'ktp_number',          
        'ktp_photo',           
        'selfie_photo',        
        'verification_status',
        'owner_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function venues()
    {
        return $this->hasMany(Venue::class);
    }

    // 👨‍💼 Relasi untuk Staff: Mengetahui siapa Boss / Owner dari staff ini
    public function owner()
    {
        return $table->belongsTo(User::class, 'owner_id');
    }

    // 👥 Relasi untuk Owner: Mengambil semua daftar staff yang dimiliki oleh Owner ini
    public function staffs()
    {
        return $this->hasMany(User::class, 'owner_id');
    }

}
