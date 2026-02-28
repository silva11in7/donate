<?php
// admin/webhooks.php
require_once 'config.php';
require_once 'layout.php';
check_auth();

echo get_header("Webhooks");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <div class="max-w-4xl">
        <header class="mb-10">
            <h1 class="text-3xl font-bold text-slate-800">Gerenciar Webhooks</h1>
            <p class="text-slate-500 mt-1">Receba e atualize o status dos seus pagamentos automaticamente.</p>
        </header>

        <div class="grid gap-6">
            <!-- Webhook URL Card -->
            <div class="card p-6">
                <div class="flex items-center gap-3 mb-5">
                    <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Sua URL de Webhook</h3>
                </div>
                <div class="flex gap-2">
                    <input type="text" readonly id="webhook-url" value="<?php echo 'https://' . ($_SERVER['HTTP_HOST'] ?? 'seudominio.com') . '/api/webhook'; ?>" 
                           class="flex-1 p-3 bg-slate-50 dark:bg-black/30 border dark:border-[#1e1e1e] rounded-xl text-slate-600 dark:text-slate-300 text-sm font-mono">
                    <button onclick="copyWebhook()" class="px-5 py-3 bg-black dark:bg-white text-white dark:text-black font-bold text-sm rounded-xl hover:bg-emerald-500 hover:text-white transition-all duration-200">
                        Copiar
                    </button>
                </div>
                <p class="text-xs text-slate-400 mt-4">Configure esta URL no seu gateway de pagamento para receber notificações em tempo real.</p>
            </div>

            <!-- Logs -->
            <div class="card overflow-hidden">
                <div class="p-5 border-b dark:border-[#1e1e1e] flex items-center gap-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-500">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6l-3.45-6.89A2 2 0 0016.76 4H7.24a2 2 0 00-1.79 1.11z"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Logs Recentes</h3>
                </div>
                <div class="divide-y dark:divide-[#1e1e1e]">
                    <?php
                    // Try to fetch recent webhook logs if table exists
                    $logs = [];
                    try {
                        $logs = $pdo->query("SELECT * FROM leads WHERE pix_code IS NOT NULL ORDER BY updated_at DESC LIMIT 10")->fetchAll();
                    } catch (Exception $e) {
                        // Table might not exist
                    }
                    
                    if (!empty($logs)):
                        foreach ($logs as $log):
                            $is_approved = $log['status'] === 'approved';
                    ?>
                    <div class="p-4 flex justify-between items-center hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                        <div class="flex items-center gap-3">
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold <?php echo $is_approved ? 'bg-emerald-500/10 text-emerald-500' : 'bg-orange-500/10 text-orange-500'; ?>">
                                <?php echo $is_approved ? 'Sucesso' : 'Pendente'; ?>
                            </span>
                            <span class="text-sm font-medium text-slate-700">
                                <?php echo htmlspecialchars($log['name'] ?: 'Pagamento #' . $log['id']); ?>
                            </span>
                        </div>
                        <span class="text-xs text-slate-400"><?php echo date('d/m H:i', strtotime($log['updated_at'])); ?></span>
                    </div>
                    <?php
                        endforeach;
                    else:
                    ?>
                    <div class="p-10 text-center text-slate-400 text-sm">Nenhum log de webhook encontrado.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
function copyWebhook() {
    const input = document.getElementById('webhook-url');
    navigator.clipboard.writeText(input.value).then(() => {
        alert('URL copiada!');
    });
}
</script>

<?php echo get_footer(); ?>
