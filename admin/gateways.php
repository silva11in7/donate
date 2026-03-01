<?php
// admin/gateways.php
require_once 'config.php';
require_once 'layout.php';
check_auth();

$msg = '';

if (isset($_POST['activate'])) {
    if (!CSRFProtector::validate($_POST['csrf_token'] ?? '')) {
        die("CSRF validation failed.");
    }
    $id = (int)$_POST['activate'];
    $pdo->query("UPDATE gateways SET active = 0");
    $stmt = $pdo->prepare("UPDATE gateways SET active = 1 WHERE id = ?");
    $stmt->execute([$id]);
    $msg = 'Gateway trocado com sucesso!';
}

if (isset($_POST['save_config'])) {
    if (!CSRFProtector::validate($_POST['csrf_token'] ?? '')) {
        die("CSRF validation failed.");
    }
    $id = (int)$_POST['gw_id'];
    $config_arr = [
        'public_key' => $_POST['public_key'] ?? '',
        'secret_key' => $_POST['secret_key'] ?? '',
        'api_key' => $_POST['public_key'] ?? '' // Map public_key to api_key for convenience or specific fields
    ];
    $config = json_encode($config_arr);
    
    $stmt = $pdo->prepare("UPDATE gateways SET config_json = ? WHERE id = ?");
    $stmt->execute([$config, $id]);
    $msg = 'Configuração salva com sucesso!';
}

if (isset($_POST['add_gateway'])) {
    if (!CSRFProtector::validate($_POST['csrf_token'] ?? '')) {
        die("CSRF validation failed.");
    }
    $name = $_POST['name'] ?? '';
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO gateways (name, active) VALUES (?, 0)");
        $stmt->execute([$name]);
        $msg = 'Novo gateway adicionado!';
    }
}

if (isset($_POST['delete_gateway'])) {
    if (!CSRFProtector::validate($_POST['csrf_token'] ?? '')) {
        die("CSRF validation failed.");
    }
    $id = (int)$_POST['delete_gateway'];
    $stmt = $pdo->prepare("DELETE FROM gateways WHERE id = ? AND active = 0");
    $stmt->execute([$id]);
    $msg = 'Gateway removido!';
}

$gateways = $pdo->query("SELECT * FROM gateways")->fetchAll();

echo get_header("Gateways");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <header class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Gestão de Gateways</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Configure seus processadores de pagamento Pix.</p>
        </div>
        <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                class="flex items-center gap-2 px-5 py-2.5 bg-emerald-500 text-white rounded-xl text-sm font-bold hover:scale-[1.02] transition-all shadow-lg shadow-emerald-500/20">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Novo Gateway
        </button>
    </header>

    <?php if ($msg): ?>
        <div class="max-w-xl mb-8 bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 p-4 rounded-2xl flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <span class="font-bold text-sm"><?php echo $msg; ?></span>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        <?php foreach ($gateways as $gw): 
            $config = json_decode($gw['config_json'] ?? '{}', true);
            $api_key = $config['api_key'] ?? '';
        ?>
        <div class="card p-8 flex flex-col relative overflow-hidden group transition-all duration-300 <?php echo $gw['active'] ? 'border-emerald-500/40 ring-4 ring-emerald-500/5' : 'opacity-80 hover:opacity-100'; ?>">
            <?php if ($gw['active']): ?>
                <div class="absolute top-4 right-4 animate-pulse">
                    <span class="bg-emerald-500 text-white text-[9px] font-black uppercase px-2 py-1 rounded-md tracking-tighter">Live</span>
                </div>
            <?php else: ?>
                <form method="POST" class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                    <?php echo CSRFProtector::hiddenInput(); ?>
                    <input type="hidden" name="delete_gateway" value="<?php echo $gw['id']; ?>">
                    <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors" title="Excluir">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 01-2 2H7a2 2 0 01-2-2V6m3 0V4a2 2 0 012-2h4a2 2 0 012 2v2"/><line x1="10" y1="11" x2="10" y2="17"/><line x1="14" y1="11" x2="14" y2="17"/></svg>
                    </button>
                </form>
            <?php endif; ?>

            <div class="w-14 h-14 rounded-2xl bg-slate-50 dark:bg-black/40 flex items-center justify-center mb-4 border border-slate-100 dark:border-white/5">
                <svg class="w-7 h-7 <?php echo $gw['active'] ? 'text-emerald-500' : 'text-slate-400'; ?>" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
            </div>

            <?php 
            $provider_slug = strtolower(str_replace(' ', '', $gw['name']));
            $impl_exists = file_exists(__DIR__ . "/../api pix/{$provider_slug}.php") || ($provider_slug === 'perfectpay');
            ?>
            
            <div class="flex items-center gap-2 mb-4">
                <h3 class="text-xl font-black text-slate-800 dark:text-white"><?php echo htmlspecialchars($gw['name']); ?></h3>
                <?php if ($impl_exists): ?>
                    <span class="text-[8px] bg-emerald-500/10 text-emerald-500 px-1.5 py-0.5 rounded font-bold uppercase">Pronto</span>
                <?php else: ?>
                    <span class="text-[8px] bg-red-500/10 text-red-500 px-1.5 py-0.5 rounded font-bold uppercase">Pendente</span>
                <?php endif; ?>
            </div>
            
            <form method="POST" class="mt-auto space-y-4">
                <?php echo CSRFProtector::hiddenInput(); ?>
                <input type="hidden" name="gw_id" value="<?php echo $gw['id']; ?>">
                
                <div class="space-y-3">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <?php echo (strtolower($gw['name']) == 'babylon') ? 'API KEY (Babylon)' : 'Chave Pública (Client ID)'; ?>
                        </label>
                        <input type="password" name="public_key" value="<?php echo htmlspecialchars($config['public_key'] ?? $config['api_key'] ?? ''); ?>" placeholder="Sua chave pública..." 
                               class="w-full p-3 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                    </div>
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Chave Privada (Client Secret)</label>
                        <input type="password" name="secret_key" value="<?php echo htmlspecialchars($config['secret_key'] ?? ''); ?>" placeholder="Sua chave secreta..." 
                               class="w-full p-3 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <button type="submit" name="save_config" class="py-2.5 rounded-xl font-black text-[10px] uppercase tracking-wider bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/10 transition-all">
                        Salvar Config
                    </button>
                    
                    <?php if (!$gw['active']): ?>
                    <form method="POST" class="contents">
                        <?php echo CSRFProtector::hiddenInput(); ?>
                        <input type="hidden" name="activate" value="<?php echo $gw['id']; ?>">
                        <button type="submit" class="py-2.5 rounded-xl font-black text-[10px] uppercase tracking-wider bg-black dark:bg-white text-white dark:text-black hover:bg-emerald-500 hover:text-white transition-all shadow-lg shadow-black/5">
                            Ativar
                        </button>
                    </form>
                    <?php else: ?>
                    <div class="py-2.5 rounded-xl font-black text-[10px] uppercase tracking-wider bg-emerald-500 text-white text-center cursor-default shadow-lg shadow-emerald-500/20">
                        Ativo
                    </div>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- Modal Add -->
<div id="modal-add" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="this.parentElement.classList.add('hidden')"></div>
    <div class="relative w-full max-w-md bg-white dark:bg-[#111] rounded-3xl p-8 shadow-2xl border border-slate-100 dark:border-white/5">
        <h3 class="text-2xl font-black text-slate-800 dark:text-white mb-2">Novo Gateway</h3>
        <p class="text-slate-500 dark:text-slate-400 text-sm mb-8 italic">Adicione um novo processador de pagamentos.</p>
                <form method="POST" class="space-y-6">
                    <?php echo CSRFProtector::hiddenInput(); ?>
            <input type="hidden" name="add_gateway" value="1">
            <div class="space-y-1.5">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nome do Gateway</label>
                <input type="text" name="name" required placeholder="Ex: Kirvano, Stripe, etc" 
                       class="w-full p-4 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-2xl text-slate-800 dark:text-slate-200 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all">
            </div>
            
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')" 
                        class="flex-1 py-4 text-sm font-bold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 py-4 bg-emerald-500 text-white rounded-2xl text-sm font-black hover:scale-[1.02] transition-all shadow-lg shadow-emerald-500/20">
                    Adicionar
                </button>
            </div>
        </form>
    </div>
</div>

<?php echo get_footer(); ?>
