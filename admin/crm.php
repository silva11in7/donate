<?php
// admin/crm.php
require_once 'config.php';
require_once 'layout.php';

check_auth();

// Stats
$total_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$recovered = $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'approved'")->fetchColumn();
$conversion_rate = $total_leads > 0 ? ($recovered / $total_leads) * 100 : 0;

$stmt = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 50");
$leads = $stmt->fetchAll();

echo get_header("Elite CRM");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <header class="mb-10">
        <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Elite CRM Insight</h2>
        <p class="text-slate-500 dark:text-slate-400 mt-1">Acompanhe a jornada de recuperação dos seus leads em tempo real.</p>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
        <div class="card p-6 flex flex-col gap-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total de Leads</span>
            <span class="text-3xl font-black text-slate-800 dark:text-white"><?php echo $total_leads; ?></span>
        </div>
        <div class="card p-6 flex flex-col gap-1 border-emerald-500/20">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Aprovação / ROI</span>
            <span class="text-3xl font-black text-emerald-500"><?php echo $recovered; ?></span>
        </div>
        <div class="card p-6 flex flex-col gap-1">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Taxa de Conversão</span>
            <span class="text-3xl font-black text-blue-500"><?php echo number_format($conversion_rate, 1); ?>%</span>
        </div>
    </div>

    <div class="card overflow-hidden">
        <div class="p-6 border-b border-slate-100 dark:border-white/5">
            <h3 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Fluxo Recente</h3>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-black/20">
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Identificação</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Data</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                <?php foreach ($leads as $lead): 
                    $status_class = ($lead['status'] === 'approved') 
                        ? 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20' 
                        : 'bg-orange-500/10 text-orange-500 border-orange-500/20';
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($lead['name'] ?: 'Doador #'.$lead['id']); ?></span>
                            <span class="text-[10px] text-slate-400"><?php echo htmlspecialchars($lead['phone'] ?? 'sem_telefone'); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border uppercase tracking-wider <?php echo $status_class; ?>">
                            <?php echo $lead['status'] === 'approved' ? 'Recuperado' : 'Pendente'; ?>
                        </span>
                    </td>
                    <td class="px-6 py-4 text-xs text-slate-400">
                        <?php echo date('H:i - d/m', strtotime($lead['created_at'])); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php echo get_footer(); ?>
