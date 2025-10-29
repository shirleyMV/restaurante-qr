<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BnbQrService
{
    private $sandboxUrl;
    private $tokenUrl;
    private $accountId;
    private $authorizationId;
    private $userKey;
    private $token;

    public function __construct()
    {
        $this->sandboxUrl = config('bnb.sandbox_url');
        $this->tokenUrl = config('bnb.token_url');
        $this->accountId = config('bnb.account_id');
        $this->authorizationId = config('bnb.authorization_id');
        $this->userKey = config('bnb.user_key');
    }

    /**
     * Obtener token de autenticación
     */
    public function getToken()
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post($this->tokenUrl . '/auth/token', [
                    'accountId' => $this->accountId,
                    'authorizationId' => $this->authorizationId,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] ?? false) {
                    $this->token = $data['token'] ?? null;
                    return $this->token;
                }
            }

            // Si falla, usamos un token ficticio para desarrollo
            $this->token = 'DEMO_TOKEN_' . time();
            return $this->token;

        } catch (\Exception $e) {
            Log::error('Error obteniendo token BNB: ' . $e->getMessage());
            // Token ficticio para desarrollo
            $this->token = 'DEMO_TOKEN_' . time();
            return $this->token;
        }
    }

    /**
     * Generar QR Simple
     */
    public function generarQrSimple($monto, $glosa, $fechaExpiracion = null)
    {
        try {
            // Obtener token primero
            if (!$this->token) {
                $this->getToken();
            }

            // Si no hay fecha de expiración, usar 24 horas
            if (!$fechaExpiracion) {
                $fechaExpiracion = now()->addDay()->format('Y-m-d');
            }

            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->token,
                ])
                ->post($this->sandboxUrl . '/main/getQRWithImageAsync', [
                    'currency' => config('bnb.currency'),
                    'gloss' => $glosa,
                    'amount' => (string)$monto,
                    'singleUse' => config('bnb.single_use') ? 'true' : 'false',
                    'expirationDate' => $fechaExpiracion,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                if ($data['success'] ?? false) {
                    return [
                        'success' => true,
                        'qr_image' => $data['qr'] ?? null,
                        'qr_code' => $data['qrCode'] ?? null,
                        'message' => $data['message'] ?? 'QR generado exitosamente',
                    ];
                }
            }

            // Si la API falla, generamos QR simulado
            return $this->generarQrSimulado($monto, $glosa);

        } catch (\Exception $e) {
            Log::error('Error generando QR BNB: ' . $e->getMessage());
            // Fallback a QR simulado
            return $this->generarQrSimulado($monto, $glosa);
        }
    }

    /**
     * Generar QR simulado (cuando no hay conexión con BNB)
     */
    private function generarQrSimulado($monto, $glosa)
    {
        $datosQr = json_encode([
            'banco' => 'BNB - Banco Nacional de Bolivia',
            'monto' => $monto,
            'moneda' => 'BOB',
            'glosa' => $glosa,
            'fecha' => now()->format('Y-m-d H:i:s'),
            'tipo' => 'SIMULADO',
            'mensaje' => 'Este es un QR de prueba. En producción se conectará con el BNB.',
        ]);

        // Generar QR usando la librería
        $qrCodeSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($datosQr);

        return [
            'success' => true,
            'qr_image' => base64_encode($qrCodeSvg),
            'qr_code' => 'QR_SIMULADO_' . time(),
            'message' => 'QR de prueba generado (sin conexión real al BNB)',
            'simulado' => true,
        ];
    }

    /**
     * Verificar estado del pago (simulado)
     */
    public function verificarPago($codigoQr)
    {
        // En producción, aquí harías una petición al BNB para verificar
        // Por ahora, simulamos que el pago está pendiente
        
        return [
            'success' => true,
            'estado' => 'pendiente',
            'message' => 'Pago en proceso de verificación',
        ];
    }
}