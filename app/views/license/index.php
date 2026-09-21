<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Licencia del Sistema - Sistema Moda</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Inter', sans-serif; background-color: #0f172a; color: white;}
        .glass-panel {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen bg-slate-900 bg-[url('https://images.unsplash.com/photo-1558769132-cb1fac08c04d?q=80&w=2000&auto=format&fit=crop')] bg-cover bg-center p-4">
    
    <div class="absolute inset-0 bg-slate-950/85"></div>
    
    <div class="w-full max-w-lg p-8 rounded-2xl shadow-2xl glass-panel relative z-10">
        <div class="text-center mb-6">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-amber-500/20 text-amber-400 mb-3 border border-amber-500/30">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m0 0v2m0-2h2m-2 0H10m0-6a4 4 0 108 0 4 4 0 00-8 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white mb-1 tracking-tight">Sistema Bloqueado</h1>
            <p class="text-slate-300 text-sm">Ingrese su Token de Licencia JWT para reactivar el servicio.</p>
        </div>

        <?php if(!empty($error)): ?>
            <div class="bg-red-500/20 border border-red-500/60 text-red-200 px-4 py-3 rounded-xl mb-6 text-sm flex items-center gap-3" role="alert">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-400 shrink-0" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                </svg>
                <div>
                    <strong class="font-bold block">Acceso Restringido:</strong>
                    <span><?php echo htmlspecialchars($error); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/license/activar" method="POST" class="space-y-4">
            <input type="hidden" name="from" value="block">
            <div>
                <label for="token" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Token de Licencia (JWT / RSA)</label>
                <textarea 
                    name="token" 
                    id="token" 
                    rows="5" 
                    class="w-full px-4 py-3 bg-slate-900/80 border border-slate-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 text-slate-200 placeholder-slate-500 font-mono text-xs resize-none"
                    placeholder="Pegue aquí el token JWT proporcionado por el proveedor..." 
                    required></textarea>
            </div>
            
            <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-500 text-white font-semibold py-3 px-4 rounded-xl transition-all duration-200 shadow-lg shadow-indigo-500/25 flex items-center justify-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
                Verificar y Activar Licencia
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="<?php echo BASE_URL; ?>/license/revalidar" class="inline-flex items-center gap-1 text-xs text-slate-400 hover:text-white transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                Revalidar con el servidor de licencias
            </a>
        </div>

        <div class="mt-6 pt-4 border-t border-slate-800 text-center text-xs text-slate-400">
            <p>Operación Offline Resiliente. Su licencia local se verifica sin interrumpir el funcionamiento si no hay conexión.</p>
        </div>
    </div>

</body>
</html>
