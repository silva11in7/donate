<?php
// admin/layout.php

function get_sidebar() {
    $current = basename($_SERVER['PHP_SELF']);
    
    $sections = [
        'Principal' => [
            ['url' => 'index.php', 'label' => 'Dashboard', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>'],
            ['url' => 'sales.php', 'label' => 'Vendas', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 002 1.61h9.72a2 2 0 002-1.61L23 6H6"/></svg>'],
        ],
        'Gestão' => [
            ['url' => 'users.php', 'label' => 'Usuários', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>'],
            ['url' => 'pix.php', 'label' => 'Pix Gerados', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>'],
            ['url' => 'leads.php', 'label' => 'Funil / Leads', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 00-4-4H6a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>'],
            ['url' => 'site_settings.php', 'label' => 'Página da Vakinha', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>'],
        ],
        'Ferramentas' => [
            ['url' => 'crm.php', 'label' => 'Elite CRM', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z"/><polyline points="3.27 6.96 12 12.01 20.73 6.96"/><line x1="12" y1="22.08" x2="12" y2="12"/></svg>'],
            ['url' => 'webhooks.php', 'label' => 'Webhooks', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"/><path d="M5.45 5.11L2 12v6a2 2 0 002 2h16a2 2 0 002-2v-6l-3.45-6.89A2 2 0 0016.76 4H7.24a2 2 0 00-1.79 1.11z"/></svg>'],
        ],
        'Configurações' => [
            ['url' => 'gateways.php', 'label' => 'Gateways', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"/><line x1="1" y1="10" x2="23" y2="10"/></svg>'],
            ['url' => 'integrations.php', 'label' => 'Integrações', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2.69l5.66 5.66a8 8 0 11-11.31 0z"/></svg>'],
            ['url' => 'settings.php', 'label' => 'Meu Perfil', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'],
        ]
    ];
    
    $logout = ['url' => 'logout.php', 'label' => 'Sair do Sistema', 'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>'];
    
    ob_start();
    ?>
    <aside class="w-64 bg-black h-screen fixed shadow-2xl flex flex-col z-50">
        <!-- Logo -->
        <div class="p-6 border-b border-white/5">
            <h2 class="text-white text-xl font-black tracking-tight">
                <span class="inline-flex items-center gap-2">
                    <span class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-sm">⚡</span>
                    SOS <span class="text-emerald-400"></span>
                </span>
            </h2>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-3 py-4 space-y-6 overflow-y-auto">
            <?php foreach ($sections as $title => $items): ?>
            <div>
                <p class="px-3 text-[10px] font-bold text-white/20 uppercase tracking-widest mb-3"><?php echo $title; ?></p>
                <div class="space-y-1">
                    <?php foreach ($items as $item): 
                        $isActive = ($current === $item['url']);
                    ?>
                    <a href="<?php echo $item['url']; ?>" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 group
                              <?php echo $isActive 
                                ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 shadow-[0_0_15px_rgba(16,185,129,0.05)]' 
                                : 'text-white/40 hover:text-white hover:bg-white/5'; ?>">
                        <span class="<?php echo $isActive ? 'text-emerald-400' : 'text-white/20 group-hover:text-white/60'; ?> transition-colors">
                            <?php echo $item['icon']; ?>
                        </span>
                        <?php echo $item['label']; ?>
                    </a>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </nav>

        <!-- Theme Toggle + Logout -->
        <div class="p-3 border-t border-white/5 space-y-1">
            <button id="theme-toggle" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 text-white/50 hover:text-white hover:bg-white/5 w-full">
                <span class="text-white/30" id="theme-icon-container">
                    <svg id="icon-moon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/></svg>
                    <svg id="icon-sun" class="hidden" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
                </span>
                <span id="theme-toggle-text">Modo Escuro</span>
            </button>
            <a href="<?php echo $logout['url']; ?>" 
               class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm font-medium transition-all duration-200 text-red-400/60 hover:text-red-400 hover:bg-red-500/5">
                <span class="text-red-400/40"><?php echo $logout['icon']; ?></span>
                <?php echo $logout['label']; ?>
            </a>
        </div>
    </aside>

    <script>
    (function() {
        const btn = document.getElementById("theme-toggle");
        const iconMoon = document.getElementById("icon-moon");
        const iconSun = document.getElementById("icon-sun");
        const text = document.getElementById("theme-toggle-text");

        function applyIcons() {
            const isDark = document.documentElement.classList.contains("dark");
            iconMoon.style.display = isDark ? "none" : "block";
            iconSun.style.display = isDark ? "block" : "none";
            text.textContent = isDark ? "Modo Claro" : "Modo Escuro";
        }

        applyIcons();

        btn.addEventListener("click", function() {
            document.documentElement.classList.toggle("dark");
            const isDark = document.documentElement.classList.contains("dark");
            localStorage.setItem("theme", isDark ? "dark" : "light");
            applyIcons();
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}

function get_header($title = "Painel Administrativo") {
    $title_esc = htmlspecialchars($title);
    $admin = get_current_admin();
    $name = $admin['full_name'] ?? 'Administrador';
    $profile_img = $admin['profile_image'] ?? '';
    
    ob_start();
    ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title_esc; ?> - SOS </title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    fontFamily: {
                        sans: ["Inter", "sans-serif"]
                    }
                }
            }
        }
    </script>
    <style>
        * { font-family: "Inter", sans-serif; }
        
        /* Light Mode */
        body { background-color: #f8fafc; color: #1e293b; }
        .card { background: #fff; border-radius: 0.75rem; border: 1px solid #e2e8f0; transition: all 0.2s ease; }
        .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
        
        /* Dark Mode */
        .dark body { background-color: #000000; color: #e2e8f0; }
        .dark .card { background: #111111; border: 1px solid #1e1e1e; }
        .dark .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.3); border-color: #2a2a2a; }
        
        /* Dark mode text overrides */
        .dark .text-slate-800 { color: #f1f5f9; }
        .dark .text-slate-700 { color: #e2e8f0; }
        .dark .text-slate-600 { color: #cbd5e1; }
        .dark .text-slate-500 { color: #94a3b8; }
        .dark .text-slate-400 { color: #64748b; }
        .dark .text-slate-900 { color: #f8fafc; }
        
        /* Dark mode backgrounds */
        .dark .bg-slate-50 { background-color: #0a0a0a; }
        .dark .bg-white { background-color: #111111; }
        .dark .bg-green-50 { background-color: rgba(16, 185, 129, 0.1); }
        .dark .bg-blue-50 { background-color: rgba(59, 130, 246, 0.1); }
        .dark .bg-orange-50 { background-color: rgba(249, 115, 22, 0.1); }
        .dark .bg-purple-50 { background-color: rgba(168, 85, 247, 0.1); }
        .dark .bg-red-50 { background-color: rgba(239, 68, 68, 0.1); }
        
        /* Dark mode borders */
        .dark .border-b { border-color: #1e1e1e; }
        .dark .border { border-color: #1e1e1e; }
        .dark .divide-y > :not([hidden]) ~ :not([hidden]) { border-color: #1e1e1e; }
        .dark .border-l-4 { }
        .dark .border-slate-200 { border-color: #1e1e1e; }
        
        /* Dark mode hover states */
        .dark .hover\:bg-slate-50:hover { background-color: #1a1a1a; }
        
        /* Dark mode status badges */
        .dark .bg-green-100 { background-color: rgba(16, 185, 129, 0.15); }
        .dark .bg-orange-100 { background-color: rgba(249, 115, 22, 0.15); }
        .dark .bg-yellow-100 { background-color: rgba(234, 179, 8, 0.15); }
        .dark .bg-slate-100 { background-color: #1a1a1a; }
        
        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
        .dark ::-webkit-scrollbar-thumb { background: #333; }
        
        /* Smooth transitions */
        body, .card, main { transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease; }
    </style>
    <script>
        if (localStorage.theme === "dark" || (!("theme" in localStorage) && window.matchMedia("(prefers-color-scheme: dark)").matches)) {
            document.documentElement.classList.add("dark");
        } else {
            document.documentElement.classList.remove("dark");
        }
    </script>
</head>
<body class="flex min-h-screen relative">
    <!-- Top Navbar -->
    <div class="fixed top-0 right-0 left-64 h-16 bg-white/80 dark:bg-black/60 backdrop-blur-md border-b border-slate-200 dark:border-white/5 z-40 flex items-center justify-end px-8">
        <div class="flex items-center gap-4">
            <div class="text-right">
                <p class="text-xs font-bold text-slate-800 dark:text-white uppercase tracking-tight"><?php echo htmlspecialchars($name); ?></p>
                <p class="text-[10px] text-emerald-500 font-black uppercase tracking-tighter">Online</p>
            </div>
            <a href="settings.php" class="relative group">
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/5 overflow-hidden flex items-center justify-center transition-all group-hover:scale-105 group-hover:border-emerald-500/50">
                    <?php if ($profile_img): ?>
                        <img src="<?php echo htmlspecialchars($profile_img); ?>" alt="Profile" class="w-full h-full object-cover">
                    <?php else: ?>
                        <svg class="w-6 h-6 text-slate-400 dark:text-slate-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    <?php endif; ?>
                </div>
                <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-500 border-2 border-white dark:border-black rounded-full"></div>
            </a>
        </div>
    </div>
    
    <!-- Adjust main padding for follow-up content -->
    <style>main { padding-top: 5rem !important; }</style>
    
    <?php if (isset($_SESSION['db_warning'])): ?>
    <div class="fixed bottom-6 right-6 z-[60] animate-in fade-in slide-in-from-bottom-4 duration-500">
        <div class="bg-amber-500 text-white p-4 rounded-2xl shadow-2xl flex items-center gap-4 max-w-md border-4 border-white dark:border-black">
            <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <div class="flex-1">
                <p class="text-[11px] font-black uppercase tracking-widest opacity-80 mb-0.5">Aviso do Sistema</p>
                <p class="text-xs font-bold leading-tight"><?php echo $_SESSION['db_warning']; ?></p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" class="hover:scale-110 transition-transform">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>
    </div>
    <?php unset($_SESSION['db_warning']); endif; ?>

    <?php
    return ob_get_clean();
}

function get_footer() {
    return "</body></html>";
}
?>
