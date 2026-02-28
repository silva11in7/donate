<?php
// admin/settings.php
require_once 'config.php';
require_once 'layout.php';
check_auth();

$admin = get_current_admin();
$msg = '';
$msg_type = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        if (!CSRFProtector::validate($_POST['csrf_token'] ?? '')) {
            die("CSRF verification failed.");
        }
        $name = $_POST['name'] ?? '';
        $username = $_POST['username'] ?? '';
        $profile_image = $admin['profile_image'];

        // Handle Image Upload
        if (isset($_FILES['profile_img']) && $_FILES['profile_img']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['profile_img']['tmp_name'];
            $file_name = $_FILES['profile_img']['name'];
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];

            if (in_array($file_ext, $allowed)) {
                $new_file_name = 'profile_' . $admin['id'] . '_' . time() . '.' . $file_ext;
                $upload_dir = 'uploads/profile/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
                
                if (move_uploaded_file($file_tmp, $upload_dir . $new_file_name)) {
                    $profile_image = $upload_dir . $new_file_name;
                }
            }
        }

        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, profile_image = ? WHERE id = ?");
        $stmt->execute([$name, $username, $profile_image, $admin['id']]);
        
        $msg = 'Perfil atualizado com sucesso!';
        $msg_type = 'success';
        $admin = get_current_admin(); // Refresh data
    }

    if (isset($_POST['update_password'])) {
        if (!CSRFProtector::validate($_POST['csrf_token'] ?? '')) {
            die("CSRF verification failed.");
        }
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';
        
        if (password_verify($current, $admin['password_hash'])) {
            if ($new === $confirm && $new !== '') {
                $new_hash = password_hash($new, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$new_hash, $admin['id']]);
                $msg = 'Senha atualizada com sucesso!';
                $msg_type = 'success';
            } else {
                $msg = 'As novas senhas não coincidem ou estão vazias.';
                $msg_type = 'error';
            }
        } else {
            $msg = 'Senha atual incorreta.';
            $msg_type = 'error';
        }
    }
}

echo get_header("Configurações");
echo get_sidebar();
?>

<main class="flex-1 ml-64 p-8">
    <div class="max-w-3xl mx-auto">
        <header class="mb-10">
            <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">Meu Perfil</h1>
            <p class="text-slate-500 dark:text-slate-400 mt-1">Gerencie suas informações de acesso e personalização.</p>
        </header>

        <?php if ($msg): ?>
        <div class="mb-8 p-4 rounded-2xl flex items-center gap-3 text-sm font-bold animate-in fade-in slide-in-from-top-4 duration-300 <?php echo $msg_type === 'success' ? 'bg-emerald-500/10 border border-emerald-500/20 text-emerald-500' : 'bg-red-500/10 border border-red-500/20 text-red-500'; ?>">
            <?php if ($msg_type === 'success'): ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
            <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
            <?php endif; ?>
            <?php echo $msg; ?>
        </div>
        <?php endif; ?>

        <div class="space-y-8">
            <!-- Profile Section -->
            <form method="POST" enctype="multipart/form-data" class="card p-8 group">
                <?php echo CSRFProtector::hiddenInput(); ?>
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">Informações Pessoais</h3>
                        <p class="text-xs text-slate-400">Edite seu nome e foto de exibição.</p>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row items-center md:items-start gap-8 mb-8 pb-8 border-b border-slate-100 dark:border-white/5">
                    <div class="relative group/avatar">
                        <div class="w-24 h-24 rounded-3xl bg-slate-100 dark:bg-white/5 flex items-center justify-center overflow-hidden border-4 border-white dark:border-[#1e1e1e] shadow-2xl transition-all group-hover/avatar:scale-105">
                            <?php if ($admin['profile_image']): ?>
                                <img src="<?php echo htmlspecialchars($admin['profile_image']); ?>" id="preview-img" class="w-full h-full object-cover">
                            <?php else: ?>
                                <svg class="w-12 h-12 text-slate-300 dark:text-slate-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <?php endif; ?>
                        </div>
                        <label for="profile_img" class="absolute -bottom-2 -right-2 w-10 h-10 bg-emerald-500 text-white rounded-xl flex items-center justify-center cursor-pointer shadow-lg shadow-emerald-500/30 hover:scale-110 transition-all border-4 border-white dark:border-[#111]">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/><circle cx="12" cy="13" r="4"/></svg>
                            <input type="file" id="profile_img" name="profile_img" class="hidden" accept="image/*" onchange="previewFile()">
                        </label>
                    </div>
                    <div class="flex-1 space-y-5 w-full">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nome Completo</label>
                                <input type="text" name="name" value="<?php echo htmlspecialchars($admin['full_name']); ?>" required
                                       class="w-full p-3.5 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                            </div>
                            <div class="space-y-1.5">
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Usuário de Acesso</label>
                                <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required
                                       class="w-full p-3.5 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" name="update_profile" class="px-8 py-3.5 bg-black dark:bg-white text-white dark:text-black font-black text-[11px] uppercase tracking-widest rounded-xl hover:bg-emerald-500 hover:text-white transition-all duration-300 shadow-xl shadow-black/5 hover:scale-[1.02]">
                        Salvar Alterações
                    </button>
                </div>
            </form>

            <script>
            function previewFile() {
                const preview = document.getElementById('preview-img');
                const file = document.getElementById('profile_img').files[0];
                const reader = new FileReader();

                reader.addEventListener("load", function () {
                    if(preview) {
                        preview.src = reader.result;
                    } else {
                        const avatarDiv = document.querySelector('.group\\/avatar div');
                        avatarDiv.innerHTML = `<img src="${reader.result}" id="preview-img" class="w-full h-full object-cover">`;
                    }
                }, false);

                if (file) {
                    reader.readAsDataURL(file);
                }
            }
            </script>

            <!-- Password Section -->
            <form method="POST" class="card p-8">
                <?php echo CSRFProtector::hiddenInput(); ?>
                <div class="flex items-center gap-3 mb-8">
                    <div class="w-10 h-10 rounded-xl bg-orange-500/10 text-orange-500 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/></svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-800 dark:text-white">Segurança</h3>
                        <p class="text-xs text-slate-400">Atualize sua senha de acesso ao painel.</p>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="space-y-1.5">
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Senha Atual</label>
                        <input type="password" name="current_password" placeholder="Digite sua senha atual" required
                               class="w-full p-3.5 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Nova Senha</label>
                            <input type="password" name="new_password" placeholder="Mínimo 6 caracteres" required
                                   class="w-full p-3.5 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                        </div>
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Confirmar Nova Senha</label>
                            <input type="password" name="confirm_password" placeholder="Repita a nova senha" required
                                   class="w-full p-3.5 bg-slate-50 dark:bg-black/30 border border-slate-200 dark:border-[#1e1e1e] rounded-xl text-slate-800 dark:text-slate-200 text-sm focus:ring-2 focus:ring-emerald-500/20 transition-all outline-none">
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end">
                    <button type="submit" name="update_password" class="px-8 py-3.5 bg-black dark:bg-white text-white dark:text-black font-black text-[11px] uppercase tracking-widest rounded-xl hover:bg-emerald-500 hover:text-white transition-all duration-300 shadow-xl shadow-black/5 hover:scale-[1.02]">
                        Atualizar Senha
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php echo get_footer(); ?>
