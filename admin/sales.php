<?php
// admin/sales.php
require_once 'config.php';
require_once 'layout.php';

check_auth();

$stmt = $pdo->query("SELECT * FROM leads WHERE status = 'approved' ORDER BY updated_at DESC");
$sales = $stmt->fetchAll();

echo get_header("Vendas");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <header class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Vendas Realizadas</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Histórico de doações confirmadas via Pix.</p>
        </div>
        <button class="flex items-center gap-2 px-5 py-2.5 bg-white dark:bg-[#111] border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-sm font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition-all">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Exportar CSV
        </button>
    </header>

    <div class="card overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-black/20">
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Doador</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Valor</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Método</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Status</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Data Aprovação</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                <?php foreach ($sales as $sale): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($sale['name'] ?: 'Doador Anônimo'); ?></span>
                            <span class="text-[10px] text-slate-400">#<?php echo $sale['id']; ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-sm font-black text-emerald-500">R$ <?php echo number_format($sale['amount'], 2, ',', '.'); ?></span>
                    </td>
                    <td class="px-6 py-4">
                         <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400">
                             <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>
                             Pix Instantâneo
                         </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 uppercase tracking-wider">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Aprovada
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400">
                        <?php echo date('d/m/Y H:i', strtotime($sale['updated_at'])); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($sales)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center text-slate-400 text-sm">Nenhuma venda realizada ainda.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php echo get_footer(); ?>
