<?php
// admin/users.php
require_once 'config.php';
require_once 'layout.php';

check_auth();

$stmt = $pdo->query("SELECT * FROM leads ORDER BY created_at DESC");
$users = $stmt->fetchAll();

echo get_header("Usuários");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <header class="flex justify-between items-center mb-10">
        <div>
            <h2 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Usuários</h2>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Lista de leads que iniciaram o bot no Telegram ou site.</p>
        </div>
        <div class="flex gap-3">
             <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </span>
                <input type="text" placeholder="Buscar usuário..." 
                       class="pl-10 pr-4 py-2.5 bg-white dark:bg-[#111] border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 transition-all w-64">
            </div>
        </div>
    </header>

    <div class="card overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-black/20">
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">ID / Perfil</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Nome</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Username / Email</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Data de Entrada</th>
                    <th class="px-6 py-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest border-b border-slate-100 dark:border-white/5">Ações</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                <?php foreach ($users as $user): 
                    $initial = strtoupper(substr($user['name'] ?? '?', 0, 1));
                ?>
                <tr class="hover:bg-slate-50 dark:hover:bg-white/[0.02] transition-colors">
                    <td class="px-6 py-4">
                        <span class="text-xs font-mono text-slate-400">#<?php echo $user['id']; ?></span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-emerald-500/10 text-emerald-500 flex items-center justify-center font-bold text-xs border border-emerald-500/20">
                                <?php echo $initial; ?>
                            </div>
                            <span class="text-sm font-semibold text-slate-700 dark:text-slate-200"><?php echo htmlspecialchars($user['name'] ?: 'Doador Anônimo'); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-sm text-emerald-500 font-medium"><?php echo htmlspecialchars($user['phone'] ?? 'sem_telefone'); ?></span>
                            <span class="text-[10px] text-slate-400"><?php echo htmlspecialchars($user['email'] ?? 'Não informado'); ?></span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-slate-500 dark:text-slate-400 italic">
                        <?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?>
                    </td>
                    <td class="px-6 py-4">
                        <button class="flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-emerald-500 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            Ver Histórico
                        </button>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if (empty($users)): ?>
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center text-slate-400 text-sm">Nenhum usuário registrado ainda.</td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php echo get_footer(); ?>
