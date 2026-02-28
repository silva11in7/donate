<?php
// admin/integrations.php
require_once 'config.php';
require_once 'layout.php';

check_auth();

// Ensure settings table exists or use a generic one
$pdo->query("CREATE TABLE IF NOT EXISTS settings (
    key TEXT PRIMARY KEY,
    value TEXT
)");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['type'] ?? '';
    $token = $_POST['api_token'] ?? '';
    $pixel = $_POST['pixel_id'] ?? '';

    if ($type === 'utmfy') {
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (\"key\", value) VALUES ('utmfy_api_token', ?)");
        $stmt->execute([$token]);
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (\"key\", value) VALUES ('utmfy_platform', ?)");
        $stmt->execute([$_POST['platform'] ?? 'Vakinha']);
    } elseif ($type === 'tiktok') {
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('tiktok_token', ?)");
        $stmt->execute([$token]);
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('tiktok_pixel', ?)");
        $stmt->execute([$pixel]);
    } elseif ($type === 'openai') {
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES ('openai_token', ?)");
        $stmt->execute([$token]);
    }
    $msg = "Configuração de " . ucfirst($type) . " salva com sucesso!";
}

// Load current settings
$stmt = $pdo->query("SELECT * FROM settings");
$raw_settings = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

$utmfy_api_token = $raw_settings['utmfy_api_token'] ?? '';
$utmfy_platform = $raw_settings['utmfy_platform'] ?? 'Vakinha';
$tiktok_token = $raw_settings['tiktok_token'] ?? '';
$tiktok_pixel = $raw_settings['tiktok_pixel'] ?? '';
$openai_token = $raw_settings['openai_token'] ?? '';

echo get_header("Integrações");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <header class="mb-10">
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Central de Integrações</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Gerencie suas chaves de API e conexões externas de forma segura.</p>
    </header>

    <?php if (isset($msg)): ?>
    <div class="mb-6 p-4 bg-emerald-500/10 border border-emerald-500/20 rounded-xl text-emerald-500 font-bold text-sm">
        <?php echo $msg; ?>
    </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Utmify Card -->
        <div class="card p-8 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-1 bg-emerald-500 opacity-20 group-hover:opacity-100 transition-opacity"></div>
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-blue-500/10 text-blue-500 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.21 15.89A10 10 0 118 2.83"/><path d="M22 12A10 10 0 0012 2v10z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">Utmify Tracking</h3>
                    <span class="text-xs text-slate-400">Rastreamento de Pedidos e ROI</span>
                </div>
                <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo $utmfy_api_token ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-orange-500/10 text-orange-400 border border-orange-500/20'; ?>">
                    <?php echo $utmfy_api_token ? 'CONECTADO' : 'PENDENTE'; ?>
                </span>
            </div>
            
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Conecte sua conta Utmify para rastrear automaticamente todas as vendas, checkouts e leads gerados pelo robô.</p>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="type" value="utmfy">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">UTMFY API TOKEN</label>
                    <input type="password" name="api_token" value="<?php echo htmlspecialchars($utmfy_api_token); ?>" placeholder="Bearer Token..." 
                           class="w-full p-3 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">PLATAFORMA (UTMFY)</label>
                    <input type="text" name="platform" value="<?php echo htmlspecialchars($utmfy_platform); ?>" placeholder="Ex: Vakinha" 
                           class="w-full p-3 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                </div>
                <button type="submit" class="w-full py-3 bg-slate-800 dark:bg-white text-white dark:text-black rounded-xl font-black text-sm hover:scale-[1.02] transition-all">
                    Salvar Configuração
                </button>
            </form>
        </div>

        <!-- TikTok Ads Card -->
        <div class="card p-8 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-1 bg-pink-500 opacity-20 group-hover:opacity-100 transition-opacity"></div>
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-pink-500/10 text-pink-500 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 1h7l-3 3v8l-2 2-2-2v-1l2-2v-1l-2-2-2 2M12 21a6 6 0 110-12 6 6 0 010 12z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">TikTok Ads API</h3>
                    <span class="text-xs text-slate-400">Pixel & Eventos Offline</span>
                </div>
                <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo $tiktok_token ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-orange-500/10 text-orange-400 border border-orange-500/20'; ?>">
                    <?php echo $tiktok_token ? 'CONECTADO' : 'PENDENTE'; ?>
                </span>
            </div>
            
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed">Envie eventos de conversão diretamente para o TikTok Ads via API Offline para otimizar suas campanhas.</p>

            <form method="POST" class="space-y-4">
                <input type="hidden" name="type" value="tiktok">
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">ACCESS TOKEN</label>
                    <input type="password" name="api_token" value="<?php echo htmlspecialchars($tiktok_token); ?>" placeholder="Access Token do TikTok..." 
                           class="w-full p-3 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                </div>
                <div class="space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">PIXEL ID</label>
                    <input type="text" name="pixel_id" value="<?php echo htmlspecialchars($tiktok_pixel); ?>" placeholder="ID do seu Pixel..." 
                           class="w-full p-3 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                </div>
                <button type="submit" class="w-full py-3 bg-slate-800 dark:bg-white text-white dark:text-black rounded-xl font-black text-sm hover:scale-[1.02] transition-all">
                    Salvar Configuração
                </button>
            </form>
        </div>

        <!-- OpenAI Card -->
        <div class="card p-8 lg:col-span-2 relative overflow-hidden group">
            <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 opacity-20 group-hover:opacity-100 transition-opacity"></div>
            <div class="flex items-center gap-4 mb-6">
                <div class="w-12 h-12 bg-emerald-500/10 text-emerald-500 rounded-xl flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-800 dark:text-white">Inteligência Artificial (OpenAI)</h3>
                    <span class="text-xs text-slate-400">Personalidade & Objeções</span>
                </div>
                <span class="ml-auto px-2 py-0.5 rounded-full text-[10px] font-bold <?php echo $openai_token ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-orange-500/10 text-orange-400 border border-orange-500/20'; ?>">
                    <?php echo $openai_token ? 'ATIVADO' : 'INATIVO'; ?>
                </span>
            </div>
            
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed max-w-2xl">Dê vida à sua assistente virtual. Com a API da OpenAI, seu bot pode conversar naturalmente, tirar dúvidas e contornar objeções de venda automaticamente.</p>

            <form method="POST" class="flex flex-col md:flex-row gap-4">
                <input type="hidden" name="type" value="openai">
                <div class="flex-1 space-y-1.5">
                    <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">OPENAI API KEY</label>
                    <input type="password" name="api_token" value="<?php echo htmlspecialchars($openai_token); ?>" placeholder="sk-..." 
                           class="w-full p-3 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                </div>
                <button type="submit" class="md:w-64 h-[46px] mt-auto bg-emerald-500 text-white rounded-xl font-black text-sm hover:scale-[1.02] transition-all shadow-lg shadow-emerald-500/20">
                    Ativar Inteligência
                </button>
            </form>
        </div>
    </div>
</main>

<?php echo get_footer(); ?>
