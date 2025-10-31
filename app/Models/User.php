<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Determinar si el usuario puede acceder al panel de Filament
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Permitir acceso a todos los usuarios autenticados
        return true;
    }

    /**
     * Verificar si el usuario es administrador
     */
    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    /**
     * Verificar si el usuario es cajera
     */
    public function esCajera(): bool
    {
        return $this->rol === 'cajera';
    }
}
