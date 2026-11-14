<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdminKantor(): bool
    {
        return $this->role === 'admin_kantor';
    }

    public function isSales(): bool
    {
        return $this->role === 'sales';
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'Administrator Utama',
            'admin_kantor' => 'Admin Kantor',
            'sales' => 'Sales Lapangan',
            default => 'Pengguna',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'super_admin' => 'bg-danger-subtle text-danger border border-danger-subtle',
            'admin_kantor' => 'bg-primary-subtle text-primary border border-primary-subtle',
            'sales' => 'bg-success-subtle text-success border border-success-subtle',
            default => 'bg-secondary-subtle text-secondary',
        };
    }

    public function notas()
    {
        return $this->hasMany(NotaKontan::class, 'user_id');
    }
}
