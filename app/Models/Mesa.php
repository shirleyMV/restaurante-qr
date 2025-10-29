<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class Mesa extends Model
{
    protected $fillable = [
        'numero',
        'capacidad',
        'codigo_qr',
        'estado'
    ];

    // Relación: Una mesa puede tener muchos pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class);
    }

    // Evento: Generar QR automáticamente al crear
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($mesa) {
            if (empty($mesa->codigo_qr)) {
                $mesa->codigo_qr = 'MESA-' . $mesa->numero . '-' . uniqid();
            }
        });
    }

    // Método para generar código QR
    public function generarCodigoQr()
    {
        // Genera un código único para la mesa
        $this->codigo_qr = 'MESA-' . $this->numero . '-' . uniqid();
        $this->save();
        
        return $this->codigo_qr;
    }

    // Obtener URL para escanear QR
    public function getUrlMenuAttribute()
    {
        return url('/cliente/menu/' . $this->codigo_qr);
    }
    
    // Generar imagen QR
    public function getQrImageAttribute()
    {
        if (!$this->codigo_qr) {
            return null;
        }
        
        return QrCode::size(300)
            ->format('svg')
            ->generate($this->url_menu);
    }
}