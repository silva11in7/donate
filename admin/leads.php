<?php
// admin/leads.php
require_once 'config.php';
require_once 'layout.php';
check_auth();

// Pagination Logic
$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$total_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$total_pages = ceil($total_leads / $limit);

$leads = $pdo->prepare("SELECT * FROM leads ORDER BY created_at DESC LIMIT ? OFFSET ?");
$leads->execute([$limit, $offset]);
$leads = $leads->fetchAll();

// Funnel stats
$steps = [
    'start' => ['label' => 'Acesso Página', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/></svg>'],
    'info' => ['label' => 'Preencheu Dados', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>'],
    'payment' => ['label' => 'Gerou Pix', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>'],
    'complete' => ['label' => 'Pago (Final)', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>']
];

// Optimized Funnel stats (Single roundtrip)
$funnel_raw = $pdo->query("SELECT step, COUNT(*) as count FROM leads GROUP BY step")->fetchAll();
$funnel_data = array_fill_keys(array_keys($steps), 0);
foreach ($funnel_raw as $row) {
    if (isset($funnel_data[$row['step']])) {
        $funnel_data[$row['step']] = (int)$row['count'];
    }
}

echo get_header("Funil de Leads");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <header class="mb-10">
        <h2 class="text-3xl font-bold text-slate-800">Funil do Lead</h2>
        <p class="text-slate-500 mt-1">Veja onde seus potenciais doadores estão parando.</p>
    </header>

    <!-- Visual Funnel -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-10">
        <?php 
        $step_keys = array_keys($steps);
        foreach ($steps as $key => $step): 
            $count = $funnel_data[$key];
            $idx = array_search($key, $step_keys);
            $prev_count = ($idx > 0) ? $funnel_data[$step_keys[$idx - 1]] : 0;
            $drop = ($prev_count > 0) ? round((1 - ($count / $prev_count)) * 100) : 0;
        ?>
        <div class="card p-6 relative overflow-hidden text-center hover:scale-[1.02] transition-all duration-200">
            <div class="mb-3 text-slate-400 flex justify-center">
                <?php echo $step['icon']; ?>
            </div>
            <h4 class="font-bold text-slate-400 uppercase text-[10px] tracking-widest"><?php echo $step['label']; ?></h4>
            <div class="text-3xl font-black text-slate-800 my-2"><?php echo $count; ?></div>
            <?php if ($key != 'start' && $prev_count > 0): ?>
                <div class="text-[10px] font-bold text-red-500">- <?php echo $drop; ?>% evadiu</div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Leads Detailed List -->
    <div class="card overflow-hidden">
        <div class="p-5 border-b dark:border-[#1e1e1e]">
            <h3 class="font-bold text-slate-800">Listagem de Leads</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="text-[11px] text-slate-400 uppercase tracking-wider border-b dark:border-[#1e1e1e]">
                        <th class="py-4 px-5 font-semibold">Cliente</th>
                        <th class="py-4 px-5 font-semibold">Etapa Atual</th>
                        <th class="py-4 px-5 font-semibold">Última Atividade</th>
                        <th class="py-4 px-5 font-semibold">Status</th>
                        <th class="py-4 px-5 font-semibold text-center">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y dark:divide-[#1e1e1e] text-sm">
                    <?php foreach ($leads as $lead): ?>
                    <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                        <td class="py-4 px-5">
                            <div class="font-semibold text-slate-800"><?php echo htmlspecialchars($lead['name'] ?: 'Visitante'); ?></div>
                            <div class="text-xs text-slate-400 mt-0.5"><?php echo htmlspecialchars($lead['phone'] ?: ($lead['email'] ?: 'sem contato')); ?></div>
                        </td>
                        <td class="py-4 px-5">
                            <span class="px-2.5 py-1 rounded-md bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 font-medium text-xs">
                                <?php echo $steps[$lead['step']]['label'] ?? 'Desconhecido'; ?>
                            </span>
                        </td>
                        <td class="py-4 px-5 text-slate-400 text-xs">
                            <?php echo date('d/m/Y H:i', strtotime($lead['updated_at'])); ?>
                        </td>
                        <td class="py-4 px-5">
                            <?php 
                            $status_map = ['pending' => 'Pendente', 'approved' => 'Aprovado'];
                            $clr_map = ['pending' => 'text-orange-500', 'approved' => 'text-emerald-500'];
                            ?>
                            <span class="font-bold text-xs <?php echo $clr_map[$lead['status']]; ?>">
                                <?php echo $status_map[$lead['status']]; ?>
                            </span>
                        </td>
                        <td class="py-4 px-5 text-center">
                            <?php if ($lead['phone'] && $lead['status'] === 'pending'): 
                                $clean_phone = preg_replace('/\D/', '', $lead['phone']);
                                $msg = "Olá " . ($lead['name'] ?: 'doador') . "! Tudo bem? Vi que você iniciou uma doação para nossa campanha mas ainda não finalizou o Pix. Precisa de alguma ajuda?";
                                $wa_url = "https://wa.me/55$clean_phone?text=" . urlencode($msg);
                            ?>
                            <a href="<?php echo $wa_url; ?>" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-500/10 text-emerald-500 rounded-lg text-xs font-bold hover:bg-emerald-500 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/></svg>
                                Recuperar
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Controls -->
        <?php if ($total_pages > 1): ?>
        <div class="p-5 border-t dark:border-[#1e1e1e] flex items-center justify-between">
            <p class="text-xs text-slate-400">Mostrando <?php echo $offset + 1; ?>-<?php echo min($offset + $limit, $total_leads); ?> de <?php echo $total_leads; ?> leads</p>
            <div class="flex gap-2">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="px-3 py-1.5 bg-slate-100 dark:bg-white/5 rounded-lg text-xs font-bold hover:bg-emerald-500 hover:text-white transition-all">Anterior</a>
                <?php endif; ?>
                
                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="px-3 py-1.5 bg-slate-100 dark:bg-white/5 rounded-lg text-xs font-bold hover:bg-emerald-500 hover:text-white transition-all">Próxima</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php echo get_footer(); ?>
