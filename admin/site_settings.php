<?php
// admin/site_settings.php
require_once 'config.php';
require_once 'layout.php';
require_once 'upload_helper.php';
check_auth();

$msg = '';
$msg_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_site_settings'])) {
    // CSRF Check
    if (!CSRFProtector::validate($_POST['csrf_token'] ?? '')) {
        die("CSRF validation failed.");
    }
    $updates = [
        'vakinha_goal' => $_POST['vakinha_goal'] ?? '',
        'vakinha_raised' => $_POST['vakinha_raised'] ?? '',
        'vakinha_title' => $_POST['vakinha_title'] ?? '',
        'vakinha_description' => $_POST['vakinha_description'] ?? '',
        'vid_url' => $_POST['vid_url'] ?? '',
        'campaign_date' => $_POST['campaign_date'] ?? '26/02/2026',
        'campaign_days_left' => $_POST['campaign_days_left'] ?? '26',
        'about_title' => $_POST['about_title'] ?? '',
        'seo_title' => $_POST['seo_title'] ?? '',
        'seo_description' => $_POST['seo_description'] ?? '',
        'seo_keywords' => $_POST['seo_keywords'] ?? '',
        'banner_url' => $_POST['banner_url'] ?? '',
        'banner_author' => $_POST['banner_author'] ?? '',
        'banner_title' => $_POST['banner_title'] ?? '',
        'banner_location_1' => $_POST['banner_location_1'] ?? '',
        'banner_location_2' => $_POST['banner_location_2'] ?? ''
    ];

    try {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        
        // Handle File Uploads
        if (isset($_FILES['banner_file']) && $_FILES['banner_file']['error'] === UPLOAD_ERR_OK) {
            $path = UploadHelper::handle($_FILES['banner_file'], 'uploads/', 'banner_');
            if ($path) $updates['banner_url'] = $path;
        }
        if (isset($_FILES['logo_file']) && $_FILES['logo_file']['error'] === UPLOAD_ERR_OK) {
            $path = UploadHelper::handle($_FILES['logo_file'], 'uploads/', 'logo_');
            if ($path) $updates['logo_url'] = $path;
        }
        if (isset($_FILES['favicon_file']) && $_FILES['favicon_file']['error'] === UPLOAD_ERR_OK) {
            $path = UploadHelper::handle($_FILES['favicon_file'], 'uploads/', 'favicon_');
            if ($path) $updates['favicon_url'] = $path;
        }

        foreach ($updates as $key => $val) {
            if ($driver === 'pgsql') {
                // PostgreSQL Upsert
                $stmt = $pdo->prepare("INSERT INTO settings (key, value) VALUES (?, ?) 
                                     ON CONFLICT (key) DO UPDATE SET value = EXCLUDED.value");
                $stmt->execute([$key, $val]);
            } elseif ($driver === 'mysql') {
                // MySQL Upsert
                $stmt = $pdo->prepare("INSERT INTO settings (\"key\", \"value\") VALUES (?, ?) 
                                     ON DUPLICATE KEY UPDATE `value` = ?");
                $stmt->execute([$key, $val, $val]);
            } else {
                // SQLite Upsert
                $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (key, value) VALUES (?, ?)");
                $stmt->execute([$key, $val]);
            }
        }
        $msg = 'Configurações da página publicadas com sucesso! 🎉';
        $msg_type = 'success';
    } catch (Exception $e) {
        $msg = 'Erro ao salvar: ' . $e->getMessage();
        $msg_type = 'error';
    }
}

// Fetch current settings
$settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
$settings = [];
foreach ($settings_raw as $s) {
    if (isset($s['key'])) {
        $settings[$s['key']] = $s['value'];
    }
}

echo get_header("Página da Vakinha");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <div class="max-w-4xl mx-auto">
        <header class="mb-10 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Página da Vakinha</h1>
                <p class="text-slate-500 dark:text-slate-400 mt-1">Configure o conteúdo visual e técnico da doação.</p>
            </div>
            <a href="../index.php" target="_blank" class="px-5 py-2.5 bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 text-xs font-bold rounded-xl border border-slate-200 dark:border-white/10 hover:bg-white transition-all flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/></svg>
                Ver Página
            </a>
        </header>

        <?php if ($msg): ?>
        <div class="mb-8 p-5 rounded-3xl flex items-center gap-4 text-sm font-bold animate-in fade-in slide-in-from-top-4 duration-500 <?php echo $msg_type === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 shadow-lg shadow-emerald-500/5' : 'bg-red-500/10 border border-red-500/20 text-red-500'; ?>">
            <div class="<?php echo $msg_type === 'success' ? 'bg-emerald-500' : 'bg-red-500'; ?> p-1.5 rounded-lg text-white">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><?php echo $msg_type === 'success' ? '<polyline points="20 6 9 17 4 12"/>' : '<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>'; ?></svg>
            </div>
            <?php echo $msg; ?>
        </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="space-y-8">
            <?php echo CSRFProtector::hiddenInput(); ?>
            
            <!-- Metas e Valores -->
            <div class="card p-8 group border border-slate-200/50 dark:border-white/5 transition-all hover:border-emerald-500/30">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">Metas e Progresso</h3>
                        <p class="text-xs text-slate-400">Gerencie os valores da barra de progresso.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="space-y-2">
                        <label class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">
                            <span>Meta Total</span>
                            <span class="text-emerald-500">R$</span>
                        </label>
                        <input type="number" name="vakinha_goal" value="<?php echo htmlspecialchars($settings['vakinha_goal'] ?? '50000'); ?>" 
                               class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 font-bold focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="flex justify-between text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">
                            <span>Início Arrecadado</span>
                            <span class="text-emerald-500">R$</span>
                        </label>
                        <input type="number" name="vakinha_raised" value="<?php echo htmlspecialchars($settings['vakinha_raised'] ?? '12500'); ?>" 
                               class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 font-bold focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                    </div>
                </div>
                <div class="mt-4 flex items-center gap-2 px-1">
                     <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
                     <p class="text-[10px] text-slate-400 font-medium italic">O sistema soma doações reais a este valor inicial automaticamente.</p>
                </div>
            </div>

            <!-- Conteúdo Principal -->
            <div class="card p-8 group border border-slate-200/50 dark:border-white/5 transition-all hover:border-blue-500/30">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-2xl bg-blue-500/10 text-blue-500 flex items-center justify-center shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">Conteúdo Principal</h3>
                        <p class="text-xs text-slate-400">Página Inicial (Títulos e Descrições)</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Data de Criação</label>
                            <input type="text" name="campaign_date" value="<?php echo htmlspecialchars($settings['campaign_date'] ?? '26/02/2026'); ?>" 
                                   class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Dias Restantes</label>
                            <input type="text" name="campaign_days_left" value="<?php echo htmlspecialchars($settings['campaign_days_left'] ?? '26'); ?>" 
                                   class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 transition-all outline-none">
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Título da Doação</label>
                        <input type="text" name="vakinha_title" value="<?php echo htmlspecialchars($settings['vakinha_title'] ?? ''); ?>" 
                               class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 font-medium focus:ring-2 focus:ring-blue-500/20 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Título da Seção Sobre</label>
                        <input type="text" name="about_title" value="<?php echo htmlspecialchars($settings['about_title'] ?? '✅ Vakinha verificada e confirmada. Sua doação é segura e fará a diferença!'); ?>" 
                               class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 font-medium focus:ring-2 focus:ring-blue-500/20 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Texto da História</label>
                        <textarea name="vakinha_description" rows="5" 
                                  class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-sm leading-relaxed focus:ring-2 focus:ring-blue-500/20 transition-all outline-none"><?php echo htmlspecialchars($settings['vakinha_description'] ?? ''); ?></textarea>
                    </div>
                    <div class="space-y-4">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Vídeo da Campanha</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="relative">
                                <input type="text" name="vid_url" value="<?php echo htmlspecialchars($settings['vid_url'] ?? ''); ?>" placeholder="https://www.youtube.com/embed/..."
                                   class="w-full p-4 pl-12 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-blue-500/20 transition-all outline-none">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"/><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"/></svg>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 p-4 bg-blue-500/5 rounded-2xl border border-blue-500/10">
                                 <div class="text-[10px] font-bold text-blue-500 tracking-tighter leading-tight">Dica: Use o link "embed" do YouTube para melhor performance.</div>
                            </div>
                        </div>
                    </div>

                    <!-- Identidade Visual (Logo e Favicon) -->
                    <div class="pt-6 border-t border-slate-100 dark:border-white/5 space-y-6">
                        <h4 class="text-sm font-bold text-slate-700 dark:text-slate-300">Identidade Visual</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Logo Principal</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 flex items-center justify-center overflow-hidden">
                                        <?php if (!empty($settings['logo_url'])): 
                                            $logo_display = (strpos($settings['logo_url'], 'admin/') === 0) ? str_replace('admin/', '', $settings['logo_url']) : $settings['logo_url'];
                                        ?>
                                            <img src="<?php echo htmlspecialchars($logo_display); ?>" class="max-w-full max-h-full object-contain">
                                        <?php else: ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                                        <?php endif; ?>
                                    </div>
                                    <label class="cursor-pointer px-4 py-2 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all shadow-sm">
                                        Upload Logo
                                        <input type="file" name="logo_file" class="hidden" accept="image/*">
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Favicon (Ícone)</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200 dark:border-white/10 flex items-center justify-center overflow-hidden">
                                        <?php if (!empty($settings['favicon_url'])): 
                                            $favicon_display = (strpos($settings['favicon_url'], 'admin/') === 0) ? str_replace('admin/', '', $settings['favicon_url']) : $settings['favicon_url'];
                                        ?>
                                            <img src="<?php echo htmlspecialchars($favicon_display); ?>" class="w-8 h-8 object-contain">
                                        <?php else: ?>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-slate-300"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                                        <?php endif; ?>
                                    </div>
                                    <label class="cursor-pointer px-4 py-2 bg-white dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-xl text-xs font-bold hover:bg-slate-50 transition-all shadow-sm">
                                        Upload Favicon
                                        <input type="file" name="favicon_file" class="hidden" accept="image/*">
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Banner Superior -->
            <div class="card p-8 group border border-slate-200/50 dark:border-white/5 transition-all hover:border-amber-500/30">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-2xl bg-amber-500/10 text-amber-500 flex items-center justify-center shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">Banner Superior</h3>
                        <p class="text-xs text-slate-400">Personalize o banner de destaque da página.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="space-y-3">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Banner da Campanha</label>
                        <div class="relative min-h-[200px] rounded-3xl overflow-hidden border-2 border-dashed border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-black/20 group-hover:border-amber-500/30 transition-all">
                             <?php if (!empty($settings['banner_url'])): 
                                $banner_display = (strpos($settings['banner_url'], 'admin/') === 0) ? str_replace('admin/', '', $settings['banner_url']) : $settings['banner_url'];
                             ?>
                                <img src="<?php echo htmlspecialchars($banner_display); ?>" class="absolute inset-0 w-full h-full object-cover">
                             <?php endif; ?>
                             <div class="absolute inset-0 flex flex-col items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity">
                                <label class="cursor-pointer px-6 py-3 bg-white text-black rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-110 transition-all shadow-2xl">
                                    Alterar Imagem
                                    <input type="file" name="banner_file" class="hidden" accept="image/*">
                                </label>
                             </div>
                        </div>
                        <input type="text" name="banner_url" value="<?php echo htmlspecialchars($settings['banner_url'] ?? ''); ?>" placeholder="Atalho: Cole o link da imagem aqui..."
                               class="w-full mt-2 p-3 bg-transparent border border-slate-100 dark:border-white/5 rounded-xl text-[10px] text-slate-400 focus:outline-none focus:text-slate-600 transition-all">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Título do Banner</label>
                            <input type="text" name="banner_title" value="<?php echo htmlspecialchars($settings['banner_title'] ?? ''); ?>" placeholder="Ex: SOS ENCHENTES"
                                   class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 font-bold focus:ring-2 focus:ring-amber-500/20 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Criado por (Autor)</label>
                            <input type="text" name="banner_author" value="<?php echo htmlspecialchars($settings['banner_author'] ?? ''); ?>" placeholder="Ex: Cruz Vermelha"
                                   class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-amber-500/20 transition-all outline-none">
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Localização 1</label>
                            <input type="text" name="banner_location_1" value="<?php echo htmlspecialchars($settings['banner_location_1'] ?? ''); ?>" placeholder="Ex: JUIZ DE FORA - MG"
                                   class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-amber-500/20 transition-all outline-none">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Localização 2</label>
                            <input type="text" name="banner_location_2" value="<?php echo htmlspecialchars($settings['banner_location_2'] ?? ''); ?>" placeholder="Ex: UBA - MG"
                                   class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-amber-500/20 transition-all outline-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- SEO e Metadados -->
            <div class="card p-8 group border border-slate-200/50 dark:border-white/5 transition-all hover:border-violet-500/30">
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-2xl bg-violet-500/10 text-violet-500 flex items-center justify-center shadow-inner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">SEO & Google</h3>
                        <p class="text-xs text-slate-400">Configure como a página aparece em buscas e redes sociais.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Título da Aba (Browser)</label>
                        <input type="text" name="seo_title" value="<?php echo htmlspecialchars($settings['seo_title'] ?? ''); ?>" placeholder="Ex: Doação Solidária - Ajude Agora"
                               class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-violet-500/20 transition-all outline-none">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Descrição SEO (Meta)</label>
                        <textarea name="seo_description" rows="3" placeholder="Pequeno resumo para o Google..."
                                  class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-violet-500/20 transition-all outline-none"><?php echo htmlspecialchars($settings['seo_description'] ?? ''); ?></textarea>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest px-1">Palavras-chave (Separadas por vírgula)</label>
                        <input type="text" name="seo_keywords" value="<?php echo htmlspecialchars($settings['seo_keywords'] ?? ''); ?>"
                               class="w-full p-4 bg-slate-50 dark:bg-black/40 border border-slate-200 dark:border-white/5 rounded-2xl text-slate-800 dark:text-slate-200 text-xs focus:ring-2 focus:ring-violet-500/20 transition-all outline-none">
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-4 pb-12">
                <button type="submit" name="save_site_settings" class="px-12 py-5 bg-emerald-500 text-white font-black text-[13px] uppercase tracking-widest rounded-[2rem] hover:bg-emerald-600 transition-all duration-500 shadow-2xl shadow-emerald-500/20 hover:scale-[1.05] active:scale-95">
                    Publicar Alterações
                </button>
            </div>
        </form>
    </div>
</main>

<style>
    .card {
        background: rgba(255, 255, 255, 0.8);
        border-radius: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
    }
    .dark .card {
        background: rgba(15, 15, 15, 0.4);
        backdrop-filter: blur(10px);
    }
</style>

<?php echo get_footer(); ?>
