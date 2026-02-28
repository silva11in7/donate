<?php
// admin/pix.php
require_once 'config.php';
require_once 'layout.php';
check_auth();

$filter = $_GET['filter'] ?? 'all';
$query = "SELECT * FROM leads WHERE pix_code IS NOT NULL";
if ($filter == 'approved') $query .= " AND status = 'approved'";
if ($filter == 'pending') $query .= " AND status = 'pending'";
$query .= " ORDER BY created_at DESC";

$pix_list = $pdo->query($query)->fetchAll();

echo get_header("Pix");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <header class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-3xl font-bold text-slate-800">Gerenciamento de Pix</h2>
            <p class="text-slate-500 mt-1">Monitore todos os pagamentos via Pix gerados.</p>
        </div>
        <div class="flex card p-1 gap-1">
            <a href="?filter=all" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?php echo $filter == 'all' ? 'bg-black dark:bg-white text-white dark:text-black' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5'; ?>">Todos</a>
            <a href="?filter=approved" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?php echo $filter == 'approved' ? 'bg-emerald-500 text-white' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5'; ?>">Aprovados</a>
            <a href="?filter=pending" class="px-4 py-2 rounded-lg text-sm font-bold transition-all <?php echo $filter == 'pending' ? 'bg-orange-500 text-white' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5'; ?>">Pendentes</a>
        </div>
    </header>

    <div class="card overflow-hidden">
        <table class="w-full text-left">
            <thead>
                <tr class="text-[11px] text-slate-400 uppercase tracking-wider border-b dark:border-[#1e1e1e]">
                    <th class="py-4 px-5 font-semibold">Data</th>
                    <th class="py-4 px-5 font-semibold">Cliente</th>
                    <th class="py-4 px-5 font-semibold">Valor</th>
                    <th class="py-4 px-5 font-semibold">Gateway</th>
                    <th class="py-4 px-5 font-semibold">Status</th>
                    <th class="py-4 px-5 font-semibold">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y dark:divide-[#1e1e1e]">
                <?php foreach ($pix_list as $pix): ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="py-4 px-5 text-sm text-slate-500">
                        <?php echo date('d/m/Y H:i', strtotime($pix['created_at'])); ?>
                    </td>
                    <td class="py-4 px-5">
                        <div class="font-semibold text-slate-800 text-sm"><?php echo htmlspecialchars($pix['name'] ?: 'N/A'); ?></div>
                        <div class="text-xs text-slate-400 mt-0.5"><?php echo htmlspecialchars($pix['email'] ?: 'N/A'); ?></div>
                    </td>
                    <td class="py-4 px-5 font-bold text-slate-700 text-sm">R$ <?php echo number_format($pix['amount'], 2, ',', '.'); ?></td>
                    <td class="py-4 px-5 text-sm text-slate-500"><?php echo htmlspecialchars($pix['gateway'] ?: 'Perfect Pay'); ?></td>
                    <td class="py-4 px-5">
                        <?php if ($pix['status'] == 'approved'): ?>
                            <span class="bg-emerald-500/10 text-emerald-500 px-2.5 py-1 rounded-md text-[11px] font-bold">Aprovado</span>
                        <?php else: ?>
                            <span class="bg-orange-500/10 text-orange-500 px-2.5 py-1 rounded-md text-[11px] font-bold">Pendente</span>
                        <?php endif; ?>
                    </td>
                    <td class="py-4 px-5">
                        <?php if ($pix['status'] == 'pending'): ?>
                        <button onclick="copyToClipboard('<?php echo $pix['pix_code']; ?>')" class="text-blue-500 hover:text-blue-400 text-sm font-bold transition-colors">Copiar Hash</button>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($pix_list)): ?>
                <tr>
                    <td colspan="6" class="py-20 text-center text-slate-400 italic">Nenhum registro de Pix encontrado.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Código Pix copiado!');
    });
}
</script>

<?php echo get_footer(); ?>
