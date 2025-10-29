@extends('layouts.app')

@section('title', 'Pagar - Mesa ' . $pedido->mesa->numero)

@section('content')
<div x-data="pagoApp()" class="min-h-screen bg-gray-100">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center space-x-3">
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold tracking-wide">Realizar Pago</h1>
                    <p class="text-sm text-orange-100 flex items-center">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                        </svg>
                        Mesa {{ $pedido->mesa->numero }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container mx-auto px-4 py-6 max-w-2xl">
        <!-- Resumen del Pedido -->
        <div class="bg-white rounded-xl shadow-lg p-6 mb-6 border border-gray-200">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center border-b pb-3">
                Resumen de tu Pedido
            </h2>

            <div class="space-y-3 mb-4">
                @foreach($pedido->detalles as $detalle)
                <div class="flex justify-between items-center py-2 border-b">
                    <div>
                        <p class="font-semibold text-gray-800">{{ $detalle->producto->nombre }}</p>
                        <p class="text-sm text-gray-500">Cantidad: {{ $detalle->cantidad }}</p>
                    </div>
                    <p class="font-bold text-orange-600">Bs {{ number_format($detalle->subtotal, 2) }}</p>
                </div>
                @endforeach
            </div>

            <div class="border-t-2 border-orange-200 pt-4">
                <div class="flex justify-between items-center text-2xl font-bold">
                    <span class="text-gray-800">TOTAL:</span>
                    <span class="text-orange-600">Bs {{ number_format($pedido->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Métodos de Pago -->
        <div class="bg-white rounded-xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-credit-card text-orange-600 mr-2"></i>
                Selecciona tu Método de Pago
            </h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                @foreach($metodosPago as $metodo)
                <button 
                    @click="seleccionarMetodo({{ $metodo->id }}, '{{ $metodo->nombre }}')"
                    :class="metodoSeleccionado === {{ $metodo->id }} ? 'border-orange-600 bg-orange-50' : 'border-gray-300'"
                    class="border-2 rounded-xl p-6 hover:border-orange-400 transition-all text-center">
                    <div class="mb-3 flex justify-center">
                        @if($metodo->nombre === 'QR')
                            <div class="bg-blue-100 rounded-lg p-4">
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                            </div>
                        @elseif($metodo->nombre === 'Efectivo')
                            <div class="bg-green-100 rounded-lg p-4">
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        @else
                            <div class="bg-purple-100 rounded-lg p-4">
                                <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                        @endif
                    </div>
                    <p class="font-bold text-gray-800">{{ $metodo->nombre }}</p>
                </button>
                @endforeach
            </div>

           <!-- Botón de Pago -->
<button 
    @click="procesarPago()"
    :disabled="!metodoSeleccionado || procesando"
    class="w-full bg-gradient-to-r from-orange-600 to-red-600 text-white py-4 rounded-xl font-bold text-lg hover:shadow-lg transition-all disabled:opacity-50 disabled:cursor-not-allowed">
    <span x-show="!procesando">
        <i class="fas fa-check-circle mr-2"></i>
        <span x-text="metodoNombre === 'QR' ? 'Generar QR de Pago' : 'Confirmar Pago'"></span>
    </span>
    <span x-show="procesando">
        <i class="fas fa-spinner fa-spin mr-2"></i>
        Procesando...
    </span>
</button>
        </div>

        <!-- Modal QR de Pago -->
        <div x-show="mostrarQR" 
             x-transition
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 relative">
                <button @click="cerrarQR()" 
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl">
                    <i class="fas fa-times"></i>
                </button>

                <div class="text-center">
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Escanea para Pagar</h3>
                    <p class="text-gray-600 mb-6">Usa tu app bancaria para escanear</p>

                   <!-- QR Code -->
<div class="bg-white border-4 border-orange-600 rounded-xl p-4 mb-4 inline-block">
    <div x-html="decodificarQR()"></div>
</div>

                    <div class="bg-orange-50 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-600 mb-1">Monto a Pagar:</p>
                        <p class="text-3xl font-bold text-orange-600">Bs {{ number_format($pedido->total, 2) }}</p>
                        <p class="text-xs text-gray-500 mt-2">Código: <span x-text="codigoTransaccion"></span></p>
                    </div>

                    <button @click="confirmarPagoQR()" 
                            :disabled="confirmando"
                            class="w-full bg-green-600 text-white py-3 rounded-lg font-bold hover:bg-green-700 transition-colors disabled:opacity-50">
                        <span x-show="!confirmando">
                            <i class="fas fa-check-circle mr-2"></i>
                            Ya Pagué - Confirmar
                        </span>
                        <span x-show="confirmando">
                            <i class="fas fa-spinner fa-spin mr-2"></i>
                            Verificando...
                        </span>
                    </button>
                    
                    <p class="text-xs text-gray-500 mt-3">
                        * En producción, el pago se verificará automáticamente
                    </p>
                </div>
            </div>
        </div>

        <!-- Modal de Éxito -->
        <div x-show="pagoExitoso" 
             x-transition
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-8 text-center">
                <div class="mb-4 flex justify-center">
                    <div class="bg-green-100 rounded-full p-4">
                        <svg class="w-16 h-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-green-600 mb-2">¡Pago Exitoso!</h3>
                <p class="text-gray-600 mb-6">Gracias por tu preferencia</p>
                <a href="{{ route('cliente.menu', $pedido->mesa->codigo_qr) }}" 
                   class="inline-block bg-orange-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-orange-700">
                    Volver al Menú
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function pagoApp() {
    return {
        pedidoId: {{ $pedido->id }},
        metodoSeleccionado: null,
        metodoNombre: '',
        procesando: false,
        mostrarQR: false,
        qrCode: '',
        codigoTransaccion: '',
        confirmando: false,
        pagoExitoso: false,  // ← Esta línea faltaba

        decodificarQR() {
            if (!this.qrCode) return '';
            const svgContent = atob(this.qrCode);
            return svgContent;
        },

        seleccionarMetodo(id, nombre) {
            this.metodoSeleccionado = id;
            this.metodoNombre = nombre;
        },

        async procesarPago() {
            if (!this.metodoSeleccionado) {
                alert('Por favor selecciona un método de pago');
                return;
            }

            if (this.metodoNombre === 'QR') {
                await this.generarQRPago();
            } else {
                await this.pagoEfectivo();
            }
        },

        async generarQRPago() {
            this.procesando = true;

            try {
                const response = await fetch('/cliente/pago/generar-qr', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        pedido_id: this.pedidoId,
                        metodo_pago_id: this.metodoSeleccionado
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.qrCode = data.qr_code;
                    this.codigoTransaccion = data.codigo_transaccion;
                    this.mostrarQR = true;
                } else {
                    alert('Error al generar QR: ' + data.message);
                }
            } catch (error) {
                alert('Error de conexión');
                console.error(error);
            } finally {
                this.procesando = false;
            }
        },

        async confirmarPagoQR() {
            this.confirmando = true;

            try {
                const response = await fetch('/cliente/pago/confirmar', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        codigo_transaccion: this.codigoTransaccion
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.mostrarQR = false;
                    this.pagoExitoso = true;
                } else {
                    alert('Error al confirmar pago: ' + data.message);
                }
            } catch (error) {
                alert('Error de conexión');
                console.error(error);
            } finally {
                this.confirmando = false;
            }
        },

        async pagoEfectivo() {
            if (!confirm('¿Confirmas que pagarás en efectivo con la cajera?')) {
                return;
            }

            this.procesando = true;

            try {
                const response = await fetch('/cliente/pago/efectivo', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        pedido_id: this.pedidoId,
                        metodo_pago_id: this.metodoSeleccionado
                    })
                });

                const data = await response.json();

                if (data.success) {
                    this.pagoExitoso = true;
                } else {
                    alert('Error: ' + data.message);
                }
            } catch (error) {
                alert('Error de conexión');
                console.error(error);
            } finally {
                this.procesando = false;
            }
        },

        cerrarQR() {
            this.mostrarQR = false;
        }
    }
}
</script>
@endpush
@endsection