<div class="p-6">
    <div class="text-center">
        <div class="bg-white p-6 rounded-lg inline-block mb-4">
            {!! $mesa->qr_image !!}
        </div>
        
        <div class="space-y-3">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600 mb-1">URL del Menú:</p>
                <p class="font-mono text-sm break-all bg-white p-2 rounded border">
                    {{ $mesa->url_menu }}
                </p>
            </div>
            
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="text-sm text-blue-800">
                    <strong>ℹ️ Instrucción:</strong> Los clientes deben escanear este QR para acceder al menú.
                </p>
            </div>
        </div>
    </div>
</div>