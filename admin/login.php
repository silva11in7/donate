<?php
// admin/login.php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Rate Limiting Check
    require_once '../security/rate_limit.php';
    RateLimiter::check('login');

    // 2. CSRF Verification
    if (!CSRFProtector::validate($_POST['csrf_token'] ?? '')) {
        die("CSRF validation failed.");
    }

    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE LOWER(username) = LOWER(?)");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        RateLimiter::clear('login');
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: index.php');
        exit;
    } else {
        RateLimiter::registerAttempt('login');
        $error = 'Credenciais incorretas ou acesso bloqueado.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Access - Premium Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { 
            font-family: 'Outfit', sans-serif;
            background-color: #050505;
            overflow: hidden;
        }

        .bg-mesh {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 0% 0%, rgba(16, 185, 129, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 100% 100%, rgba(16, 185, 129, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(5, 5, 5, 1) 0%, rgba(5, 5, 5, 1) 100%);
            z-index: -1;
        }

        .bg-animate {
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(16, 185, 129, 0.2) 0%, transparent 70%);
            filter: blur(60px);
            animation: move 20s infinite alternate;
            z-index: -1;
        }

        @keyframes move {
            0% { transform: translate(-20%, -20%) rotate(0deg); }
            100% { transform: translate(50%, 50%) rotate(360deg); }
        }

        .glass-card {
            background: rgba(15, 15, 15, 0.7);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .input-glow:focus {
            box-shadow: 0 0 20px rgba(16, 185, 129, 0.1);
            border-color: rgba(16, 185, 129, 0.5);
        }

        .btn-premium {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .btn-premium::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: 0.5s;
        }

        .btn-premium:hover::after {
            left: 100%;
        }

        .fade-in {
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">
    <div class="bg-mesh"></div>
    <div class="bg-animate" style="top: 10%; left: 10%;"></div>
    <div class="bg-animate" style="bottom: 10%; right: 10%; animation-delay: -5s; animation-duration: 25s;"></div>

    <div class="max-w-md w-full glass-card rounded-[2.5rem] p-10 fade-in">
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-emerald-500/10 border border-emerald-500/20 mb-6 group transition-all duration-500 hover:scale-110">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            <h1 class="text-4xl font-bold text-white tracking-tight mb-3">SOS </h1>
            <p class="text-gray-400 font-light tracking-wide">Portal de Acesso Restrito</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-500/10 border border-red-500/20 text-red-400 p-4 mb-8 rounded-2xl text-sm flex items-center gap-3 animate-pulse">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-6">
            <?php echo CSRFProtector::hiddenInput(); ?>
            
            <div class="space-y-2 group">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest px-1">Usuário</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 transition-colors group-focus-within:text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input type="text" name="username" required 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-gray-600 outline-none transition-all input-glow"
                        placeholder="nome de usuário">
                </div>
            </div>

            <div class="space-y-2 group">
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-widest px-1">Senha</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-600 transition-colors group-focus-within:text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <input type="password" name="password" required 
                        class="w-full bg-white/5 border border-white/10 rounded-2xl py-4 pl-12 pr-4 text-white placeholder-gray-600 outline-none transition-all input-glow"
                        placeholder="••••••••">
                </div>
            </div>

            <button type="submit" 
                class="w-full btn-premium text-white font-bold py-5 rounded-2xl shadow-2xl transform active:scale-95 flex items-center justify-center gap-3 mt-4">
                <span>Entrar no Painel</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                </svg>
            </button>
        </form>

        <div class="mt-10 text-center">
            <p class="text-xs text-gray-600 font-medium tracking-tight">
                Protegido por Firewall WAF nível Bancário &copy; <?php echo date('Y'); ?>
            </p>
        </div>
    </div>
</body>
</html>
