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
        'role',
        'unique_code',
        'daily_salary',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            if (empty($user->unique_code)) {
                $user->unique_code = 'JR-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                while (static::where('unique_code', $user->unique_code)->exists()) {
                    $user->unique_code = 'JR-' . str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                }
            }
        });
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

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

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }

    public function isPegawai()
    {
        return $this->role === 'pegawai';
    }

    public function isKetua()
    {
        return $this->role === 'ketua';
    }

    public function isAdminAbsensi()
    {
        return $this->role === 'admin_absensi';
    }
}
