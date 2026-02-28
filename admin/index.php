<?php
// admin/index.php
require_once 'config.php';
require_once 'layout.php';
check_auth();

// Stats queries
$total_revenue = $pdo->query("SELECT SUM(amount) FROM leads WHERE status = 'approved'")->fetchColumn() ?: 0;
$total_leads = $pdo->query("SELECT COUNT(*) FROM leads")->fetchColumn();
$pending_pix = $pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'pending' AND pix_code IS NOT NULL")->fetchColumn();
$conversion_rate = $total_leads > 0 ? round(($pdo->query("SELECT COUNT(*) FROM leads WHERE status = 'approved'")->fetchColumn() / $total_leads) * 100, 1) : 0;

echo get_header("Home");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <header class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Visão Geral</h2>
            <p class="text-slate-500 mt-1">Confira o desempenho da sua oferta em tempo real.</p>
        </div>
        <div class="flex items-center space-x-4">
            <span class="bg-emerald-500/10 text-emerald-500 border border-emerald-500/20 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></span>
                Live
            </span>
        </div>
    </header>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <!-- Revenue Card -->
        <div class="card p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-emerald-500/10 text-emerald-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Receita Total</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5">R$ <?php echo number_format($total_revenue, 2, ',', '.'); ?></p>
                </div>
            </div>
        </div>

        <!-- Leads Card -->
        <div class="card p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-blue-500/10 text-blue-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Leads</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5"><?php echo $total_leads; ?></p>
                </div>
            </div>
        </div>

        <!-- Pending Card -->
        <div class="card p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-orange-500/10 text-orange-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Pix Pendentes</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5"><?php echo $pending_pix; ?></p>
                </div>
            </div>
        </div>

        <!-- Conversion Card -->
        <div class="card p-5 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="p-3 rounded-xl bg-purple-500/10 text-purple-500 mr-4">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Conversão</p>
                    <p class="text-2xl font-bold text-slate-800 mt-0.5"><?php echo $conversion_rate; ?>%</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card overflow-hidden">
            <div class="p-5 border-b dark:border-[#1e1e1e]">
                <h3 class="text-base font-bold text-slate-800">Leads Recentes</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="text-[11px] text-slate-400 uppercase tracking-wider border-b dark:border-[#1e1e1e]">
                            <th class="py-3 px-5 font-semibold">Lead</th>
                            <th class="py-3 px-5 font-semibold">Valor</th>
                            <th class="py-3 px-5 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-[#1e1e1e]">
                        <?php
                        $recent_leads = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC LIMIT 5")->fetchAll();
                        foreach ($recent_leads as $lead):
                            $status_class = $lead['status'] == 'approved' ? 'bg-emerald-500/10 text-emerald-500' : 'bg-orange-500/10 text-orange-500';
                            $status_label = $lead['status'] == 'approved' ? 'Aprovado' : 'Pendente';
                        ?>
                        <tr class="text-sm hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                            <td class="py-3.5 px-5">
                                <div class="font-semibold text-slate-800"><?php echo htmlspecialchars($lead['name'] ?: 'Desconhecido'); ?></div>
                                <div class="text-xs text-slate-400 mt-0.5"><?php echo htmlspecialchars($lead['email'] ?: 'sem email'); ?></div>
                            </td>
                            <td class="py-3.5 px-5 font-semibold text-slate-700">R$ <?php echo number_format($lead['amount'], 2, ',', '.'); ?></td>
                            <td class="py-3.5 px-5">
                                <span class="px-2.5 py-1 rounded-md text-[11px] font-bold <?php echo $status_class; ?>"><?php echo $status_label; ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($recent_leads)): ?>
                        <tr>
                            <td colspan="3" class="py-10 text-center text-slate-400">Nenhum lead registrado até agora.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card overflow-hidden">
            <div class="p-5 border-b dark:border-[#1e1e1e] flex justify-between items-center">
                <h3 class="text-base font-bold text-slate-800">Receita (Últimos 7 dias)</h3>
                <span class="text-[10px] font-bold text-emerald-500 uppercase tracking-widest bg-emerald-500/10 px-2 py-1 rounded">Live Data</span>
            </div>
            <div class="p-5">
                <canvas id="revenueChart" height="250"></canvas>
            </div>
        </div>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php
    // Fetch last 7 days of revenue
    $daily_revenue = [];
    $labels = [];
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $display_date = date('d/m', strtotime("-$i days"));
        $labels[] = $display_date;
        $rev = $pdo->query("SELECT SUM(amount) FROM leads WHERE status = 'approved' AND DATE(updated_at) = '$date'")->fetchColumn() ?: 0;
        $daily_revenue[] = $rev;
    }
    ?>

    const ctx = document.getElementById('revenueChart').getContext('2d');
    const isDark = document.documentElement.classList.contains('dark');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                label: 'Receita (R$)',
                data: <?php echo json_encode($daily_revenue); ?>,
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#10b981',
                pointRadius: 4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.05)' },
                    ticks: { color: isDark ? '#64748b' : '#94a3b8', font: { size: 10 } }
                },
                x: {
                    grid: { display: false },
                    ticks: { color: isDark ? '#64748b' : '#94a3b8', font: { size: 10 } }
                }
            }
        }
    });
});
</script>

<?php echo get_footer(); ?>
