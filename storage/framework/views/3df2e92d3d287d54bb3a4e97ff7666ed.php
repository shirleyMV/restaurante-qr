

<?php $__env->startSection('title', 'Menú - Mesa ' . $mesa->numero); ?>

<?php $__env->startSection('content'); ?>
<div x-data="menuApp()" x-cloak class="min-h-screen bg-gradient-to-br from-orange-50 to-red-50">
    <!-- Header -->
    <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white shadow-lg sticky top-0 z-50">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="bg-white rounded-full p-2">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            <circle cx="12" cy="12" r="10" stroke-width="2"></circle>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-wide">Doña Julia</h1>
                        <p class="text-sm text-orange-100 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            Mesa <?php echo e($mesa->numero); ?>

                        </p>
                    </div>
                </div>
                <button 
                    @click="mostrarCarrito = true" 
                    class="relative bg-white text-orange-600 rounded-full p-3 shadow-lg hover:scale-110 transition-transform">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <span x-show="carrito.length > 0" 
                          x-text="carrito.length"
                          class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold">
                    </span>
                </button>
            </div>
        </div>
    </div>

    <!-- Categorías y Productos -->
    <div class="container mx-auto px-4 py-6">
        <?php $__currentLoopData = $categorias; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4 flex items-center">
                <span class="bg-orange-600 text-white rounded-full w-8 h-8 flex items-center justify-center mr-3">
                    <?php echo e($loop->iteration); ?>

                </span>
                <?php echo e($categoria->nombre); ?>

            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php $__currentLoopData = $categoria->productos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $producto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow">
                    <div class="h-40 bg-gradient-to-br from-orange-200 to-red-200 flex items-center justify-center">
                        <?php if($producto->imagen): ?>
                            <img src="<?php echo e(asset('imagenes/' . $producto->imagen)); ?>" 
                                 alt="<?php echo e($producto->nombre); ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <i class="fas fa-utensils text-6xl text-orange-400"></i>
                        <?php endif; ?>
                    </div>
                    
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-800 mb-1"><?php echo e($producto->nombre); ?></h3>
                        <p class="text-gray-600 text-sm mb-3"><?php echo e($producto->descripcion); ?></p>
                        
                        <div class="flex justify-between items-center">
                            <span class="text-2xl font-bold text-orange-600">
                                Bs <?php echo e(number_format($producto->precio, 2)); ?>

                            </span>
                            <button 
                                @click="agregarAlCarrito(<?php echo e($producto->id); ?>, '<?php echo e($producto->nombre); ?>', <?php echo e($producto->precio); ?>)"
                                class="bg-orange-600 text-white px-4 py-2 rounded-lg hover:bg-orange-700 transition-colors flex items-center gap-2">
                                <i class="fas fa-plus"></i>
                                Agregar
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    <!-- Modal del Carrito -->
    <div x-show="mostrarCarrito" 
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-end md:items-center justify-center">
        <div @click.away="mostrarCarrito = false" 
             class="bg-white w-full md:max-w-2xl md:rounded-t-2xl md:rounded-b-2xl rounded-t-2xl max-h-[90vh] flex flex-col">
            
            <!-- Header del Carrito -->
            <div class="bg-gradient-to-r from-orange-600 to-red-600 text-white p-4 flex justify-between items-center md:rounded-t-2xl rounded-t-2xl">
                <h3 class="text-xl font-bold">🛒 Tu Pedido</h3>
                <button @click="mostrarCarrito = false" class="text-2xl hover:scale-110 transition-transform">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Contenido del Carrito -->
            <div class="flex-1 overflow-y-auto p-4">
                <template x-if="carrito.length === 0">
                    <div class="text-center py-12 text-gray-400">
                        <i class="fas fa-shopping-cart text-6xl mb-4"></i>
                        <p class="text-lg">Tu carrito está vacío</p>
                    </div>
                </template>

                <template x-if="carrito.length > 0">
                    <div class="space-y-3">
                        <template x-for="(item, index) in carrito" :key="index">
                            <div class="bg-gray-50 rounded-lg p-3 flex items-center gap-3">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800" x-text="item.nombre"></h4>
                                    <p class="text-orange-600 font-bold">Bs <span x-text="item.precio.toFixed(2)"></span></p>
                                </div>
                                
                                <div class="flex items-center gap-2">
                                    <button @click="disminuirCantidad(index)" 
                                            class="bg-gray-300 text-gray-700 w-8 h-8 rounded-full hover:bg-gray-400">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <span class="font-bold text-lg w-8 text-center" x-text="item.cantidad"></span>
                                    <button @click="aumentarCantidad(index)" 
                                            class="bg-orange-600 text-white w-8 h-8 rounded-full hover:bg-orange-700">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                
                                <button @click="eliminarDelCarrito(index)" 
                                        class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Footer del Carrito -->
            <div x-show="carrito.length > 0" class="border-t p-4 space-y-3">
                <div class="flex justify-between items-center text-xl font-bold">
                    <span>Total:</span>
                    <span class="text-orange-600">Bs <span x-text="calcularTotal().toFixed(2)"></span></span>
                </div>
                
               <button @click="enviarPedido()" 
        :disabled="enviando"
        class="w-full bg-gradient-to-r from-green-600 to-green-700 text-white py-4 rounded-xl font-bold text-lg hover:shadow-lg transition-all disabled:opacity-50">
    <span x-show="!enviando">
        <i class="fas fa-paper-plane mr-2"></i>
        Enviar Pedido y Pagar
    </span>
    <span x-show="enviando">
        <i class="fas fa-spinner fa-spin mr-2"></i>
        Enviando...
    </span>
</button>
            </div>
        </div>
    </div>

    <!-- Modal de Datos del Cliente -->
    <div x-show="mostrarDatosCliente" 
         x-transition
         class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div @click.away="mostrarDatosCliente = false" 
             class="bg-white rounded-2xl max-w-md w-full p-6">
            <h3 class="text-2xl font-bold text-gray-800 mb-4">👤 Tus Datos</h3>
            <p class="text-gray-600 mb-6">Por favor, ingresa tus datos para procesar el pedido</p>
            
            <form @submit.prevent="confirmarPedido()" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nombre *</label>
                    <input type="text" 
                           x-model="clienteNombre"
                           required
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="Tu nombre completo">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Teléfono</label>
                    <input type="tel" 
                           x-model="clienteTelefono"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="Ej: 70123456">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Correo</label>
                    <input type="email" 
                           x-model="clienteCorreo"
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent"
                           placeholder="tu@email.com">
                </div>
                
                <div class="flex gap-3 mt-6">
                    <button type="button" 
                            @click="mostrarDatosCliente = false"
                            class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg font-bold hover:bg-gray-300 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            :disabled="enviando || !clienteNombre"
                            class="flex-1 bg-gradient-to-r from-green-600 to-green-700 text-white py-3 rounded-lg font-bold hover:shadow-lg transition-all disabled:opacity-50">
                        <span x-show="!enviando">Confirmar Pedido</span>
                        <span x-show="enviando">
                            <i class="fas fa-spinner fa-spin"></i> Enviando...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notificación de Éxito -->
    <div x-show="mostrarExito" 
         x-transition
         class="fixed top-20 left-1/2 transform -translate-x-1/2 bg-green-500 text-white px-6 py-4 rounded-lg shadow-xl z-50 flex items-center gap-3">
        <i class="fas fa-check-circle text-2xl"></i>
        <span class="font-semibold">¡Pedido enviado con éxito!</span>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
function menuApp() {
    return {
        mesaId: <?php echo e($mesa->id); ?>,
        carrito: [],
        mostrarCarrito: false,
        mostrarDatosCliente: false,
        mostrarExito: false,
        enviando: false,
        clienteNombre: '',
        clienteTelefono: '',
        clienteCorreo: '',

        agregarAlCarrito(id, nombre, precio) {
            const existe = this.carrito.find(item => item.id === id);
            
            if (existe) {
                existe.cantidad++;
            } else {
                this.carrito.push({
                    id: id,
                    nombre: nombre,
                    precio: precio,
                    cantidad: 1
                });
            }
        },

        aumentarCantidad(index) {
            this.carrito[index].cantidad++;
        },

        disminuirCantidad(index) {
            if (this.carrito[index].cantidad > 1) {
                this.carrito[index].cantidad--;
            }
        },

        eliminarDelCarrito(index) {
            this.carrito.splice(index, 1);
        },

        calcularTotal() {
            return this.carrito.reduce((total, item) => {
                return total + (item.precio * item.cantidad);
            }, 0);
        },

        enviarPedido() {
            if (this.carrito.length === 0) return;
            
            // Mostrar modal para capturar datos del cliente
            this.mostrarCarrito = false;
            this.mostrarDatosCliente = true;
        },

        async confirmarPedido() {
            if (this.carrito.length === 0 || !this.clienteNombre) return;

            this.enviando = true;

            try {
                const response = await fetch('/cliente/pedido', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        mesa_id: this.mesaId,
                        productos: this.carrito,
                        cliente: {
                            nombre: this.clienteNombre,
                            telefono: this.clienteTelefono,
                            correo: this.clienteCorreo
                        }
                    })
                });

                const data = await response.json();

                if (data.success) {
                    // Redirigir a la página de pago
                    window.location.href = '/cliente/pago/' + data.pedido.id;
                } else {
                    alert('Error al enviar el pedido: ' + data.message);
                }
            } catch (error) {
                alert('Error de conexión');
                console.error(error);
            } finally {
                this.enviando = false;
            }
        }
    }
}
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>




<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\wamp64\www\restaurante-filament\resources\views/cliente/menu.blade.php ENDPATH**/ ?>