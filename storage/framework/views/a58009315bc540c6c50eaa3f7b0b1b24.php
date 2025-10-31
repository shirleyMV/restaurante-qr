

<?php $__env->startSection('title', 'Pagar - Mesa ' . $pedido->mesa->numero); ?>

<?php $__env->startSection('content'); ?>
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
                        Mesa <?php echo e($pedido->mesa->numero); ?>

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
                <?php $__currentLoopData = $pedido->detalles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detalle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="flex justify-between items-center py-2 border-b">
                    <div>
                        <p class="font-semibold text-gray-800"><?php echo e($detalle->producto->nombre); ?></p>
                        <p class="text-sm text-gray-500">Cantidad: <?php echo e($detalle->cantidad); ?></p>
                    </div>
                    <p class="font-bold text-orange-600">Bs <?php echo e(number_format($detalle->subtotal, 2)); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="border-t-2 border-orange-200 pt-4">
                <div class="flex justify-between items-center text-2xl font-bold">
                    <span class="text-gray-800">TOTAL:</span>
                    <span class="text-orange-600">Bs <?php echo e(number_format($pedido->total, 2)); ?></span>
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
                <?php $__currentLoopData = $metodosPago; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $metodo): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <button 
                    @click="seleccionarMetodo(<?php echo e($metodo->id); ?>, '<?php echo e($metodo->nombre); ?>')"
                    :class="metodoSeleccionado === <?php echo e($metodo->id); ?> ? 'border-orange-600 bg-orange-50' : 'border-gray-300'"
                    class="border-2 rounded-xl p-6 hover:border-orange-400 transition-all text-center">
                    <div class="mb-3 flex justify-center">
                        <?php if($metodo->nombre === 'QR'): ?>
                            <div class="bg-blue-100 rounded-lg p-4">
                                <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                                </svg>
                            </div>
                        <?php elseif($metodo->nombre === 'Efectivo'): ?>
                            <div class="bg-green-100 rounded-lg p-4">
                                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                        <?php else: ?>
                            <div class="bg-purple-100 rounded-lg p-4">
                                <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p class="font-bold text-gray-800"><?php echo e($metodo->nombre); ?></p>
                </button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="space-y-3">
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
                
                <button 
                    @click="cancelarPedido()"
                    :disabled="procesando"
                    class="w-full bg-red-600 text-white py-3 rounded-xl font-semibold hover:bg-red-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    <i class="fas fa-times-circle mr-2"></i>
                    Cancelar Pedido
                </button>
            </div>
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

                    <div class="bg-white border-4 border-orange-600 rounded-xl p-4 mb-4 inline-block">
                        <div x-html="decodificarQR()"></div>
                    </div>

                    <div class="bg-orange-50 rounded-lg p-4 mb-4">
                        <p class="text-sm text-gray-600 mb-1">Monto a Pagar:</p>
                        <p class="text-3xl font-bold text-orange-600">Bs <?php echo e(number_format($pedido->total, 2)); ?></p>
                        <p class="text-xs text-gray-500 mt-2">Código: <span x-text="codigoTransaccion"></span></p>
                    </div>

                    <button @click="mostrarModalComprobante = true" 
                            class="w-full bg-blue-600 text-white py-4 rounded-lg font-bold hover:bg-blue-700 transition-colors shadow-lg">
                        <i class="fas fa-upload mr-2"></i>
                        Subir Comprobante de Pago
                    </button>
                    
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mt-4 rounded">
                        <div class="flex items-start">
                            <i class="fas fa-info-circle text-yellow-600 mr-2 mt-1"></i>
                            <p class="text-sm text-yellow-800">
                                <strong>Importante:</strong> Después de pagar, sube tu comprobante para que la cajera confirme tu pago y tu pedido vaya a cocina.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Subir Comprobante -->
        <div x-show="mostrarModalComprobante" 
             x-transition
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-6 relative">
                <button @click="mostrarModalComprobante = false" 
                        class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 text-2xl">
                    <i class="fas fa-times"></i>
                </button>

                <div class="text-center">
                    <div class="mb-4 flex justify-center">
                        <div class="bg-blue-100 rounded-full p-4">
                            <i class="fas fa-file-image text-blue-600" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-2">Subir Comprobante</h3>
                    <p class="text-gray-600 mb-6">Sube una foto de tu comprobante de pago</p>

                    <form @submit.prevent="subirComprobante()" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label class="block w-full cursor-pointer">
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 hover:border-blue-500 transition-colors">
                                    <input type="file" 
                                           @change="archivoSeleccionado = $event.target.files[0]" 
                                           accept="image/*" 
                                           class="hidden" 
                                           required>
                                    <div x-show="!archivoSeleccionado">
                                        <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-2"></i>
                                        <p class="text-gray-600">Haz clic para seleccionar imagen</p>
                                    </div>
                                    <div x-show="archivoSeleccionado" class="text-green-600">
                                        <i class="fas fa-check-circle text-4xl mb-2"></i>
                                        <p x-text="archivoSeleccionado ? archivoSeleccionado.name : ''"></p>
                                    </div>
                                </div>
                            </label>
                        </div>

                        <button type="submit" 
                                :disabled="subiendoComprobante"
                                class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 transition-colors disabled:opacity-50">
                            <span x-show="!subiendoComprobante">
                                <i class="fas fa-upload mr-2"></i>
                                Enviar Comprobante
                            </span>
                            <span x-show="subiendoComprobante">
                                <i class="fas fa-spinner fa-spin mr-2"></i>
                                Enviando...
                            </span>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Efectivo -->
        <div x-show="pagoExitoso && metodoNombre === 'Efectivo'" 
             x-transition
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-8 text-center">
                <div class="mb-4 flex justify-center">
                    <div class="bg-blue-100 rounded-full p-4">
                        <i class="fas fa-cash-register text-blue-600" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-blue-600 mb-2">Pedido Registrado</h3>
                <p class="text-gray-600 mb-6">Dirígete a caja para realizar el pago en efectivo</p>
                <a href="<?php echo e(route('cliente.menu', $pedido->mesa->codigo_qr)); ?>" 
                   class="inline-block bg-orange-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-orange-700">
                    Volver al Menú
                </a>
            </div>
        </div>

        <!-- Modal QR Exitoso -->
        <div x-show="pagoExitoso && metodoNombre === 'QR'" 
             x-transition
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl max-w-md w-full p-8 text-center">
                <div class="mb-4 flex justify-center">
                    <div class="bg-green-100 rounded-full p-4">
                        <i class="fas fa-check-circle text-green-600" style="font-size: 4rem;"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-bold text-green-600 mb-2">¡Comprobante Enviado!</h3>
                <p class="text-gray-600 mb-6">La cajera verificará tu pago y tu pedido irá a cocina</p>
                <a href="<?php echo e(route('cliente.menu', $pedido->mesa->codigo_qr)); ?>" 
                   class="inline-block bg-orange-600 text-white px-8 py-3 rounded-lg font-bold hover:bg-orange-700">
                    Volver al Menú
                </a>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function pagoApp() {
    return {
        pedidoId: <?php echo e($pedido->id); ?>,
        metodoSeleccionado: null,
        metodoNombre: '',
        procesando: false,
        mostrarQR: false,
        qrCode: '',
        codigoTransaccion: '',
        confirmando: false,
        pagoExitoso: false,
        comprobanteUrl: '',
        mostrarModalComprobante: false,
        archivoSeleccionado: null,
        subiendoComprobante: false,

        init() {
            // Escuchar notificaciones de pago confirmado
            if (window.Echo) {
                window.Echo.channel('pedido.' + this.pedidoId)
                    .listen('.pago.confirmado', (e) => {
                        console.log('Pago confirmado:', e);
                        this.mostrarModalComprobante = false;
                        this.mostrarQR = false;
                        this.pagoExitoso = true;
                        
                        // Mostrar notificación
                        if (window.Notification && Notification.permission === 'granted') {
                            new Notification('¡Pago Confirmado!', {
                                body: e.mensaje,
                                icon: '/favicon.ico'
                            });
                        }
                    });
            }
        },

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
                    this.comprobanteUrl = data.comprobante_url;
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

        async subirComprobante() {
            if (!this.archivoSeleccionado) {
                alert('Por favor selecciona una imagen');
                return;
            }

            this.subiendoComprobante = true;

            try {
                const formData = new FormData();
                formData.append('comprobante', this.archivoSeleccionado);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

                const response = await fetch(this.comprobanteUrl, {
                    method: 'POST',
                    body: formData
                });

                if (response.ok) {
                    this.mostrarModalComprobante = false;
                    this.mostrarQR = false;
                    this.pagoExitoso = true;
                } else {
                    alert('Error al subir el comprobante');
                }
            } catch (error) {
                alert('Error de conexión');
                console.error(error);
            } finally {
                this.subiendoComprobante = false;
            }
        },

        async pagoEfectivo() {
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
        },
        
        async cancelarPedido() {
            // Crear modal de confirmación personalizado
            const modalConfirm = document.createElement('div');
            modalConfirm.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
            modalConfirm.innerHTML = `
                <div class="bg-white rounded-2xl max-w-md w-full p-8 text-center animate-fade-in">
                    <div class="mb-4 flex justify-center">
                        <div class="bg-red-100 rounded-full p-4">
                            <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-3">¿Cancelar este pedido?</h3>
                    <p class="text-gray-600 mb-6">Esta acción no se puede deshacer. Tu pedido será cancelado completamente.</p>
                    <div class="flex gap-3">
                        <button id="btnNoCancel" class="flex-1 bg-gray-200 text-gray-800 py-3 px-6 rounded-xl font-semibold hover:bg-gray-300 transition-all">
                            <i class="fas fa-arrow-left mr-2"></i>No, volver
                        </button>
                        <button id="btnYesCancel" class="flex-1 bg-red-600 text-white py-3 px-6 rounded-xl font-semibold hover:bg-red-700 transition-all">
                            <i class="fas fa-times-circle mr-2"></i>Sí, cancelar
                        </button>
                    </div>
                </div>
            `;
            document.body.appendChild(modalConfirm);
            
            // Manejar botón "No"
            document.getElementById('btnNoCancel').onclick = () => {
                modalConfirm.remove();
            };
            
            // Manejar botón "Sí"
            document.getElementById('btnYesCancel').onclick = async () => {
                const btnYes = document.getElementById('btnYesCancel');
                btnYes.disabled = true;
                btnYes.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Cancelando...';
                
                this.procesando = true;
                
                try {
                    const response = await fetch('/cliente/pedido/' + this.pedidoId + '/cancelar', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            motivo: 'Cancelado por el cliente'
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (data.success) {
                        // Cerrar modal de confirmación
                        modalConfirm.remove();
                        
                        // Mostrar modal de éxito
                        const modalSuccess = document.createElement('div');
                        modalSuccess.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
                        modalSuccess.innerHTML = `
                            <div class="bg-white rounded-2xl max-w-md w-full p-8 text-center animate-fade-in">
                                <div class="mb-4 flex justify-center">
                                    <div class="bg-green-100 rounded-full p-4 animate-bounce">
                                        <svg class="w-20 h-20 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-3xl font-bold text-green-600 mb-3">¡Pedido Cancelado!</h3>
                                <p class="text-gray-600 mb-2">Tu pedido ha sido cancelado exitosamente.</p>
                                <p class="text-sm text-gray-500">Redirigiendo al menú...</p>
                                <div class="mt-4">
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: 0%; animation: progress 2s ease-in-out forwards;"></div>
                                    </div>
                                </div>
                            </div>
                        `;
                        document.body.appendChild(modalSuccess);
                        
                        // Redirigir después de 2 segundos
                        setTimeout(() => {
                            window.location.href = '<?php echo e(route("cliente.menu", $pedido->mesa->codigo_qr)); ?>';
                        }, 2000);
                    } else {
                        modalConfirm.remove();
                        alert('Error: ' + data.message);
                    }
                } catch (error) {
                    modalConfirm.remove();
                    alert('Error de conexión');
                    console.error(error);
                } finally {
                    this.procesando = false;
                }
            };
        }
    }
}
</script>

<style>
@keyframes progress {
    from { width: 0%; }
    to { width: 100%; }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
</style>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\restaurante-filament\resources\views/cliente/pago.blade.php ENDPATH**/ ?>