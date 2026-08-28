<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activación - Sistema de Moda</title>
    <!-- Configuración para usar Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: white;}
        .glass-panel {
            background: rgba(30, 41, 59, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-slate-900 bg-[url('https://images.unsplash.com/photo-1558769132-cb1fac08c04d?q=80&w=2000&auto=format&fit=crop')] bg-cover bg-center">
    
    <div class="absolute inset-0 bg-slate-900/80"></div>
    
    <div class="w-full max-w-md p-8 m-4 rounded-2xl shadow-2xl glass-panel relative z-10 animate-[bounce_0.5s_ease-out]">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-white mb-2 tracking-tight">Sistema Bloqueado</h1>
            <p class="text-slate-300 text-sm">Ingrese su Token de Activación Maestro para reactivar el servicio Offline.</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="bg-red-500/10 border border-red-500/50 text-red-200 px-4 py-3 rounded-lg mb-6 text-sm" role="alert">
                <strong class="font-bold">Error:</strong>
                <span class="block sm:inline"><?php echo $error; ?></span>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/license/activar" method="POST">
            <div class="mb-6">
                <label for="token" class="block text-sm font-medium text-slate-300 mb-2">Token de Licencia (RSA Base64)</label>
                <textarea 
                    name="token" 
                    id="token" 
                    rows="4" 
                    class="w-full px-4 py-3 bg-slate-800/50 border border-slate-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-200 placeholder-slate-500 font-mono text-sm resize-none"
                    placeholder="eyJhbGciOiJSU..." 
                    required></textarea>
            </div>
            
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 px-4 rounded-lg transition-colors duration-200 ease-in-out shadow-lg shadow-indigo-500/30">
                Verificar y Activar
            </button>
        </form>

        <div class="mt-8 text-center text-xs text-slate-400">
            <p>Este sistema opera 100% offline. No se transmitirá información a servidores externos durante la activación.</p>
        </div>
    </div>

</body>
</html>
