<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Comprobante - Mesa {{ $pedido->mesa->numero }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-t-2xl shadow-lg p-6 border-b-4 border-indigo-500">
                <h1 class="text-3xl font-bold text-gray-800 text-center">
                    🍽️ Mesa {{ $pedido->mesa->numero }}
                </h1>
                <p class="text-center text-gray-600 mt-2">Pedido #{{ $pedido->id }}</p>
            </div>

            <!-- Pedido Details -->
            <div class="bg-white shadow-lg p-6">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">📋 Detalle del Pedido</h2>
                
                <div class="space-y-3">
                    @foreach($pedido->detalles as $detalle)
                    <div class="flex justify-between items-center border-b pb-2">
                        <div>
                            <span class="font-medium">{{ $detalle->cantidad }}x</span>
                            <span class="text-gray-700">{{ $detalle->producto->nombre }}</span>
                        </div>
                        <span class="font-semibold text-indigo-600">
                            Bs {{ number_format($detalle->subtotal, 2) }}
                        </span>
                    </div>
                    @endforeach
                </div>

                <div class="mt-4 pt-4 border-t-2 border-gray-200">
                    <div class="flex justify-between items-center text-xl font-bold">
                        <span>Total:</span>
                        <span class="text-indigo-600">Bs {{ number_format($pedido->total, 2) }}</span>
                    </div>
                </div>
            </div>

            <!-- Upload Form -->
            <div class="bg-white rounded-b-2xl shadow-lg p-6">
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                    <p class="font-bold">✅ {{ session('success') }}</p>
                </div>
                @endif

                @if(session('error'))
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                    <p class="font-bold">❌ {{ session('error') }}</p>
                </div>
                @endif

                @if($pedido->pagos->first() && $pedido->pagos->first()->comprobante)
                <div class="bg-blue-50 border-l-4 border-blue-500 p-4 mb-4 rounded">
                    <p class="font-semibold text-blue-800 mb-2">📸 Comprobante actual:</p>
                    <img src="{{ Storage::url($pedido->pagos->first()->comprobante) }}" 
                         alt="Comprobante" 
                         class="w-full max-w-md mx-auto rounded-lg shadow-md">
                </div>
                @endif

                <h2 class="text-xl font-semibold text-gray-800 mb-4">📤 Subir Comprobante de Pago</h2>
                
                <form action="{{ route('comprobante.upload', $pedido->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-6">
                        <label class="block text-gray-700 font-medium mb-2">
                            Selecciona la foto de tu comprobante:
                        </label>
                        <input type="file" 
                               name="comprobante" 
                               accept="image/*" 
                               capture="environment"
                               required
                               class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-indigo-500 transition">
                        @error('comprobante')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 px-6 rounded-lg shadow-lg transition transform hover:scale-105">
                        📤 Enviar Comprobante
                    </button>
                </form>

                <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                    <p class="text-sm text-yellow-800">
                        <strong>💡 Importante:</strong> Asegúrate de que la imagen sea clara y se vean todos los datos del pago.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>