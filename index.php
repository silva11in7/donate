<?php
require_once 'admin/config.php';

// Fetch settings
$settings_raw = $pdo->query("SELECT * FROM settings")->fetchAll();
$settings = [];
foreach ($settings_raw as $s) {
    $settings[$s['key']] = $s['value'];
}

// Calculate real progress
$real_donations = $pdo->query("SELECT SUM(amount) FROM leads WHERE status = 'approved'")->fetchColumn() ?: 0;
$base_raised = floatval($settings['vakinha_raised'] ?? 12500);
$total_raised = $base_raised + $real_donations;
$goal = floatval($settings['vakinha_goal'] ?? 50000);
$progress_pct = min(100, round(($total_raised / $goal) * 100));

$title = $settings['vakinha_title'] ?? 'SOS SOS SOS';
$description = $settings['vakinha_description'] ?? '';
$vid_url = $settings['vid_url'] ?? '';
$campaign_date = $settings['campaign_date'] ?? '26 de fevereiro de 2026';
$campaign_days_left = $settings['campaign_days_left'] ?? '26';
$about_title = $settings['about_title'] ?? '✅ Vakinha verificada e confirmada. Sua doação é segura e fará a diferença!';

$banner_url = $settings['banner_url'] ?? '';
$banner_author = $settings['banner_author'] ?? 'Criado por';
$banner_title = $settings['banner_title'] ?? '';
$banner_location_1 = $settings['banner_location_1'] ?? '';
$banner_location_2 = $settings['banner_location_2'] ?? '';

// Fetch active gateway for checkout
$active_gw = $pdo->query("SELECT name FROM gateways WHERE active = 1 LIMIT 1")->fetchColumn() ?: 'Amplo';
$gateway_api = 'api pix/' . strtolower($active_gw) . '.php';

// SEO Settings
$seo_title = $settings['seo_title'] ?? $title;
$seo_description = $settings['seo_description'] ?? $description;
$seo_keywords = $settings['seo_keywords'] ?? 'doação, solidariedade, vakinha';

// Helper for currency
function format_brl($val) {
    return 'R$ ' . number_format($val, 2, ',', '.');
}
?>
<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"><title><?php echo htmlspecialchars($seo_title); ?></title><meta name="description" content="<?php echo htmlspecialchars($seo_description); ?>"><meta name="keywords" content="<?php echo htmlspecialchars($seo_keywords); ?>"><link rel="icon" href="images/favicon_69977b85d15b2.png" type="image/png">
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap"><script src="https://cdn.tailwindcss.com"></script><script>
      !function (f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function () {
          n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = "2.0";
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = v;
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s);
      }(window, document, "script", "https://connect.facebook.net/en_US/fbevents.js");

      fbq("init", "1081229410748354");
      fbq("track", "PageView");
    </script></head>
   
    
    
    

    
    
    

 <!-- Meta Pixel Code -->
    
    <body class="min-h-screen"><noscript>
      <img
        height="1"
        width="1"
        style="display: none"
        src="https://www.facebook.com/tr?id=1081229410748354&ev=PageView&noscript=1"
        alt=""
      />
    </noscript>
    <!-- End Meta Pixel Code -->
    
    <!-- Meta Pixel Code -->
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};
if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];
s.parentNode.insertBefore(t,s)}(window, document,'script',
'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '869640629355987');
fbq('track', 'PageView');
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=869640629355987&ev=PageView&noscript=1"
/></noscript>
<!-- End Meta Pixel Code -->

<!-- Google tag (gtag.js) -->
<script async="" src="https://www.googletagmanager.com/gtag/js?id=AW-17890987311"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'AW-17890987311');
</script>

<!-- Event snippet for Compra (1) conversion page -->
<script>
  gtag('event', 'conversion', {
      'send_to': 'AW-17890987311/UcelCPnl9v8bEK-ai9NC',
      'value': 1.0,
      'currency': 'BRL',
      'transaction_id': ''
  });
</script>

        <script>
      window.pixelId = "6972e8740eb04ca5851aba0b";
      var a = document.createElement("script");
      a.setAttribute("async", "");
      a.setAttribute("defer", "");
      a.setAttribute("src", "https://cdn.utmify.com.br/scripts/pixel/pixel.js");
      document.head.appendChild(a);
    </script>
    <!-- Meta Pixel (2) -->
    <script>
      !(function (f, b, e, v, n, t, s) {
        if (f.fbq) return;
        n = f.fbq = function () {
          n.callMethod ? n.callMethod.apply(n, arguments) : n.queue.push(arguments);
        };
        if (!f._fbq) f._fbq = n;
        n.push = n;
        n.loaded = !0;
        n.version = "2.0";
        n.queue = [];
        t = b.createElement(e);
        t.async = !0;
        t.src = "https://connect.facebook.net/en_US/fbevents.js";
        s = b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t, s);
      })(window, document, "script");

      fbq("init", "2263530037385902");
      fbq("track", "PageView");
    </script>
    <noscript>
      <img
        height="1"
        width="1"
        style="display: none"
        src="https://www.facebook.com/tr?id=2263530037385902&ev=PageView&noscript=1"
        alt=""
      />
    </noscript>

    <script defer="" src="https://app.analyeasy.com/pixel/XwH1Hxay1xoHaZY9"></script>

    <style>
      :root {
        --main: #36c66c;
        --sec: #36c66c;
        color-scheme: light;
      }
      body {
        font-family: "Montserrat", sans-serif;
        background: #f9f9f9;
        color: #282828;
      }
      .shadow-custom {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      }
      .hover-scale {
        transition: transform 0.25s ease;
      }
      .hover-scale:hover {
        transform: scale(1.05);
      }
      .modal-hidden {
        display: none;
      }
      .modal-visible {
        display: block;
      }
      .hero-img {
        height: clamp(14rem, 38vw, 20rem);
      }
      @media (min-width: 768px) {
        .hero-img {
          height: 22rem;
        }
      }
      @media (min-width: 1024px) {
        .hero-img {
          height: 24rem;
        }
      }

      /* Comentários visual (estilo do exemplo) */
      .plugin-facebook {
        margin-top: 24px;
      }
      .facebook-comments .content-box {
        background: #fff;
        border: 1px solid #f1f0f0;
        border-radius: 12px;
        padding: 16px;
      }
      .facebook-comments h4 {
        margin: 0 0 12px;
        font-size: 16px;
        font-weight: 700;
        color: #282828;
      }
      .comentario {
        padding: 12px 0;
        border-top: 1px solid #efefef;
      }
      .comentario:first-child {
        border-top: 0;
      }
      .content-comentario {
        display: flex;
        align-items: flex-start;
        gap: 12px;
      }
      .comentario .avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
        border: 2px solid #f1f0f0;
        flex-shrink: 0;
        background: #c2c2c2;
      }
      .comentario .avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      .text-comentario h3 {
        margin: 0 0 4px;
        font-size: 14px;
        color: #282828;
        font-weight: 700;
      }
      .text-comentario p {
        margin: 0 0 6px;
        font-size: 14px;
        color: #282828;
        line-height: 1.5;
      }
      .text-comentario .d-flex {
        gap: 12px;
        flex-wrap: wrap;
      }
      .text-comentario .d-flex span {
        font-size: 12px;
        color: #8a8a8a;
        cursor: pointer;
      }
      .text-comentario .d-flex span:hover {
        color: var(--main);
      }
      .facebook-comments .final {
        margin-top: 8px;
        font-size: 12px;
        color: #8a8a8a;
      }

      /* Resumo Mobile */
      .resumo-mobile {
        display: block;
      }
      @media (min-width: 1024px) {
        .resumo-mobile {
          display: none;
        }
      }

      /* Footer (igual ao exemplo) */
      .aviso {
        background: #f9f9f9;
        padding: 10px 0;
        text-align: center;
        font-size: 12px;
        color: #8a8a8a;
      }
      .content-footer {
        padding: 40px 0;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
      }
      .aba h4 {
        font-size: 16px;
        color: var(--sec);
        margin-bottom: 12px;
        font-weight: 700;
      }
      .aba ul {
        list-style: none;
        padding: 0;
      }
      .aba ul li a {
        color: #fff;
        font-size: 12px;
        transition: opacity 0.3s;
        text-decoration: none;
      }
      .aba ul li a:hover {
        opacity: 0.8;
      }
      .footer-dark {
        background: #282828;
        color: #fff;
      }
      .copyright {
        background: #5a5a5a;
        padding: 10px 0;
        text-align: center;
        font-size: 12px;
        color: #fff;
      }

      /* Pix Checkout Premium Styles */
      .pix-container {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 24px;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.05);
      }
      .qr-wrapper {
        background: white;
        padding: 12px;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        display: inline-block;
        transition: transform 0.3s ease;
      }
      .qr-wrapper:hover {
        transform: scale(1.02);
      }
      .btn-copy-pix {
        background: #000;
        color: #fff;
        font-weight: 700;
        letter-spacing: -0.01em;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      }
      .btn-copy-pix:active {
        transform: scale(0.96);
      }
      .copy-success {
        background: var(--main) !important;
      }
      .pulse-indicator {
        width: 8px;
        height: 8px;
        background: var(--main);
        border-radius: 50%;
        display: inline-block;
        margin-right: 6px;
        animation: pulse 2s infinite;
      }
      @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(54, 198, 108, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(54, 198, 108, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(54, 198, 108, 0); }
      }
      .shimmer {
        background: linear-gradient(90deg, #f0f0f0 25%, #f7f7f7 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite;
      }
      @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
      }
    </style>
  

  
    <!-- HEADER (sem botão de voltar) -->
    <header class="bg-white/95 backdrop-blur-sm border-b border-gray-200 sticky top-0 z-40 shadow-custom">
      <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-6 py-2.5 sm:py-3 flex items-center justify-center lg:justify-between">
        <div class="hidden lg:block w-10"></div>

        <a href="https://cauane.site" class="shrink-0">
          <img src="images/logo_69977b85d1555.png" alt="Acelera Vaquinha Logo" class="h-8 sm:h-10 w-auto object-contain">
        </a>

        <div class="hidden lg:block w-10"></div>
      </div>
    </header>

    <!-- MAIN -->
    <main class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Left Column -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">
          <!-- New Banner Styled -->
          <div class="relative w-full aspect-video sm:aspect-[21/9] rounded-3xl overflow-hidden shadow-2xl group mb-6">
            <?php if (!empty($vid_url) && empty($banner_url)): ?>
               <iframe class="w-full h-full" src="<?php echo htmlspecialchars($vid_url); ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            <?php else: ?>
               <img src="<?php echo htmlspecialchars($banner_url ?: 'images/banner_default.jpg'); ?>" alt="Banner Campanha" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
               
               <!-- Glass Overlay -->
               <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent"></div>
               
               <!-- Content -->
               <div class="absolute inset-0 p-4 sm:p-8 flex flex-col justify-between">
                 <!-- Author Tag -->
                 <div class="self-start px-3 py-1.5 bg-black/40 backdrop-blur-md rounded-xl border border-white/10">
                    <p class="text-[10px] text-white/60 uppercase tracking-widest font-bold mb-0.5">Criado por</p>
                    <p class="text-xs text-white font-black"><?php echo htmlspecialchars($banner_author); ?></p>
                 </div>

                 <div class="flex items-end justify-between gap-4">
                   <div class="space-y-2">
                     <h2 class="text-3xl sm:text-5xl lg:text-6xl font-black text-white leading-tight drop-shadow-2xl italic tracking-tighter">
                       <?php echo htmlspecialchars($banner_title); ?>
                     </h2>
                     <div class="flex flex-wrap items-center gap-x-4 gap-y-2 text-white/80 font-bold">
                        <?php if (!empty($banner_location_1)): ?>
                        <div class="flex items-center gap-1.5 text-[10px] sm:text-xs">
                           <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                           <span class="uppercase tracking-widest"><?php echo htmlspecialchars($banner_location_1); ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($banner_location_2)): ?>
                        <div class="flex items-center gap-1.5 text-[10px] sm:text-xs">
                           <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                           <span class="uppercase tracking-widest"><?php echo htmlspecialchars($banner_location_2); ?></span>
                        </div>
                        <?php endif; ?>
                     </div>
                   </div>

                   <!-- Mini Logo Badge -->
                   <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white/10 backdrop-blur-xl rounded-2xl border border-white/20 flex items-center justify-center p-3 shadow-2xl">
                        <img src="images/logo_69977b85d1555.png" alt="V Logo" class="w-full h-auto brightness-0 invert opacity-80">
                   </div>
                 </div>
               </div>
            <?php endif; ?>
          </div>

<!-- Título -->
<h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-bold text-center text-gray-900">
  <?php echo htmlspecialchars($title); ?>
</h1>

<!-- Badges + datas -->
<div class="flex flex-wrap items-center gap-2 sm:gap-3">
  <div class="flex items-center gap-2 bg-green-50 border border-green-200 px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-lg shadow-sm">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="color: var(--main)">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"></path>
    </svg>
    <span class="text-xs sm:text-sm font-medium" style="color: var(--main)">Campanha Verificada</span>
  </div>

  <div class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-2.5 sm:px-3 py-1.5 sm:py-2 rounded-full text-xs sm:text-sm font-semibold shadow-sm">
    Doação Protegida
  </div>

  <div class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm text-gray-500">
    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l4 2m0-10a10 10 0 11-10 10 10 10 0 0110-10z"></path>
    </svg>
    <span>Criada em <?php echo htmlspecialchars($campaign_date); ?></span>
  </div>

  <div class="flex items-center gap-1.5 sm:gap-2 text-xs sm:text-sm" style="color: var(--sec); font-weight: 600">
    ⏰ Termina em <?php echo htmlspecialchars($campaign_days_left); ?> dias
  </div>
</div>

<!-- Resumo Mobile: Arrecadado | Meta | Progresso -->
<div class="resumo-mobile">
  <div class="bg-white rounded-lg shadow-custom p-4 border border-gray-100">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-xs text-gray-600">Arrecadado</div>
        <div class="text-2xl font-bold" style="color: var(--main)"><?php echo format_brl($total_raised); ?></div>
      </div>
      <div class="text-right">
        <div class="text-xs text-gray-600">Meta</div>
        <div class="text-sm font-semibold text-gray-800"><?php echo format_brl($goal); ?></div>
      </div>
    </div>

    <div class="mt-3">
      <div class="w-full h-2 bg-gray-200 rounded-full overflow-hidden">
        <div class="h-2 rounded-full" style="width: <?php echo $progress_pct; ?>%; background: var(--main)"></div>
      </div>
      <div class="mt-1 text-xs text-gray-600"><?php echo $progress_pct; ?>%</div>
    </div>
  </div>
</div>

<!-- SOBRE -->
<section id="tab-sobre" class="tabcontent">
  <div class="text-sm text-gray-600 mb-2">
    <strong>Vaquinha criada em:</strong> <?php echo htmlspecialchars($campaign_date); ?>
  </div>

  <h3 class="text-lg sm:text-xl font-semibold text-gray-900 mb-2">
    <?php echo htmlspecialchars($about_title); ?>
  </h3>

  <div class="text-gray-700 leading-relaxed">
    <?php echo nl2br(htmlspecialchars($description)); ?>
  </div>

  <div class="mt-4">
    <button class="w-full lg:w-auto text-white font-semibold px-4 py-2 rounded-lg hover:opacity-90 transition" style="background: var(--main)" data-donation-button="true">
      Quero Ajudar
    </button>
  </div>
</section>
        <!-- Right Column (Desktop) -->
        <aside class="hidden lg:block lg:sticky lg:top-24">
          <div class="bg-white rounded-lg shadow-custom p-5 sm:p-6 space-y-5">
            <div class="flex items-center gap-4">
              <div class="flex-1 min-w-0">
                <div class="text-base sm:text-lg font-bold" style="color: var(--main)"><?php echo format_brl($total_raised); ?> arrecadados</div>
                <div class="text-xs sm:text-sm font-medium text-gray-600">Meta: <?php echo format_brl($goal); ?></div>
              </div>

              <div class="relative shrink-0">
                <svg width="80" height="80" class="transform -rotate-90">
                  <circle cx="40" cy="40" r="35" fill="none" stroke="#e5e7eb" stroke-width="10" class="opacity-80"></circle>
                  <circle cx="40" cy="40" r="35" fill="none" stroke="url(#progressGradient)" stroke-width="10" stroke-dasharray="219.91" stroke-dashoffset="<?php echo 219.91 * (1 - ($progress_pct / 100)); ?>" stroke-linecap="round"></circle>
                  <defs>
                    <linearGradient id="progressGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                      <stop offset="0%" stop-color="#36c66c"></stop>
                      <stop offset="100%" stop-color="#36c66c"></stop>
                    </linearGradient>
                  </defs>
                </svg>

                <div class="absolute inset-0 flex items-center justify-center">
                  <span class="text-[11px] font-bold text-gray-900"><?php echo $progress_pct; ?>%</span>
                </div>
              </div>
            </div>

            <div class="border-t border-gray-200"></div>

            <button class="w-full text-white font-semibold text-base sm:text-lg py-3 rounded-lg transition-all duration-300 shadow-md hover-scale" style="background: var(--main)" data-donation-button="true">
              <img src="images/pix.svg" class="h-5 w-5 inline mr-2 align-[-2px]" alt="PIX" style="filter: invert(1) brightness(100%)">
              Doar Agora
            </button>
          </div>
        </aside>
      </div>

      <!-- Mobile Sticky Donation Button -->
      <div class="fixed bottom-3 left-0 right-0 z-30 px-3 sm:px-4 lg:hidden">
        <button class="w-full text-white font-semibold text-base py-3 rounded-lg transition-all duration-300 shadow-lg hover-scale" style="background: var(--main)" data-donation-button="true">
          <img src="images/pix.svg" class="h-4 w-4 inline mr-2 align-[-2px]" alt="PIX" style="filter: invert(1) brightness(100%)">
          Doar Agora
        </button>
      </div>
    </div></main>

    <!-- FOOTER COMPLETO -->
    <footer>
      <div class="aviso">
        <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-6">
          <span>
            AVISO LEGAL: O texto e as imagens incluídos nessa página são de única e exclusiva responsabilidade do criador da vaquinha e não representam a opinião ou endosso da plataforma.
          </span>
        </div>
      </div>

      <div class="footer-dark">
        <div class="max-w-6xl mx-auto px-3 sm:px-4 lg:px-6">
          <div class="content-footer">
            <div class="aba">
              <div class="logo-rodape mb-3">
                <img src="images/logo_69977b85d1555.png" alt="Logo" class="h-8 w-auto object-contain bg-white rounded p-1">
              </div>
            </div>

            <div class="aba">
              <h4>Links rápidos</h4>
              <ul>
                <li><a href="#">Quem somos</a></li>
                <li><a href="#">Vaquinhas</a></li>
                <li><a href="#">Criar vaquinha</a></li>
                <li><a href="#">Login</a></li>
                <li><a href="#">Vaquinhas mais amadas</a></li>
                <li><a href="#">Política de privacidade</a></li>
                <li><a href="#">Termos de uso</a></li>
              </ul>
            </div>

            <div class="aba">
              <h4>Suporte</h4>
              <ul>
                <li><a href="#">Dúvidas frequentes</a></li>
                <li><a href="#">Taxas e prazos</a></li>
                <li><a href="#">Segurança e transparência</a></li>
                <li><a href="#">Busca por recibo</a></li>
              </ul>
            </div>

            <div class="aba">
              <h4>Fale conosco</h4>
              <ul>
                <li><a href="#">Clique aqui para falar conosco</a></li>
                <li>De Segunda à Sexta</li>
                <li>Das 9:30 às 17:00</li>
              </ul>
            </div>

            <div class="aba">
              <h4>Baixe nosso App</h4>
              <ul>
                <li>
                  <a href="#">
                    <img src="images/Google_Play_Store_badge_EN.svg" alt="Google Play" style="height: 40px">
                  </a>
                </li>
                <li>
                  <a href="#">
                    <img src="images/download-on-the-app-store.svg" alt="Apple Store" style="height: 40px">
                  </a>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <div class="copyright">
          <span>© 2026 - Todos direitos reservados</span>
        </div>
      </div>
    </footer>

    <!-- MODAL FULLSCREEN (Doação em 2 passos) -->
    <div id="donation-modal" class="fixed inset-0 z-50 modal-hidden">
      <div class="absolute inset-0 bg-gray-900/60"></div>

      <div class="relative min-h-[100dvh] w-full flex flex-col">
        <!-- STEP 1 -->
        <section id="step-1" class="flex-1 flex flex-col bg-gradient-to-b from-white to-gray-50">
          <div class="border-b bg-white/85 backdrop-blur-sm sticky top-0 z-10 shadow-custom">
            <div class="max-w-2xl mx-auto w-full px-3 sm:px-4 py-3">
              <div class="flex items-center gap-3 sm:gap-4">
                <div class="min-w-0">
                  <h1 class="text-sm sm:text-base font-semibold flex items-center gap-2">
                    <img src="images/pix.svg" class="h-5 w-5" alt="PIX" style="filter: invert(1) brightness(100%); background: #16a34a; border-radius: 9999px">
                    Valor da Doação
                  </h1>
                  <p class="text-xs sm:text-sm text-gray-500">Passo 1 de 2</p>
                </div>
              </div>

              <div class="relative w-full overflow-hidden rounded-full bg-gray-200 h-1 mt-3">
                <div class="h-full" style="background: var(--main); width: 50%"></div>
              </div>
            </div>
          </div>

          <div class="max-w-2xl mx-auto w-full px-3 sm:px-4 py-4 sm:py-6">
            <div class="rounded-lg border bg-white text-gray-900 shadow-custom mb-4 sm:mb-6">
              <div class="p-4">
                <h2 class="font-semibold text-sm sm:text-base line-clamp-2"><?php echo htmlspecialchars($title); ?></h2>

                <div class="mt-2 space-y-2">
                  <div class="flex items-center justify-between text-xs sm:text-sm">
                    <span class="text-gray-500">Meta:</span>
                    <span class="font-medium"><?php echo format_brl($goal); ?></span>
                  </div>

                  <div class="relative w-full overflow-hidden rounded-full bg-gray-200 h-2">
                    <div class="h-full" style="background: var(--main); width: <?php echo $progress_pct; ?>%"></div>
                  </div>

                  <div class="flex justify-between text-[11px] sm:text-xs text-gray-500">
                    <span><?php echo format_brl($total_raised); ?> arrecadado</span>
                    <span><?php echo $progress_pct; ?>%</span>
                  </div>
                </div>
              </div>
            </div>

            <div class="rounded-lg border bg-white text-gray-900 shadow-custom">
              <div class="flex flex-col space-y-1.5 p-4 text-center">
                <h3 class="font-semibold tracking-tight text-base sm:text-lg">Escolha o valor da sua doação</h3>
                <p class="text-gray-500 text-sm">Todo valor faz a diferença ❤️</p>
              </div>

              <div class="p-4 sm:p-6 pt-0 space-y-4">
                <div class="grid grid-cols-2 gap-2 sm:gap-3">
                  <button class="donation-amount relative inline-flex items-center justify-center rounded-md font-medium border border-[color:var(--main)] bg-transparent text-[color:var(--main)] hover:bg-[color:var(--main)] hover:text-white transition-all px-3 sm:px-4 py-2 h-10 text-sm w-full hover-scale" data-amount-label="R$ 30,00" data-amount-number="30">
                    R$ 30,00
                  </button>

                  <button class="donation-amount relative inline-flex items-center justify-center rounded-md font-medium border border-[color:var(--main)] bg-transparent text-[color:var(--main)] hover:bg-[color:var(--main)] hover:text-white transition-all px-3 sm:px-4 py-2 h-10 text-sm w-full hover-scale" data-amount-label="R$ 50,00" data-amount-number="50">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-[color:var(--main)] text-white text-[10px] px-2 py-0.5 rounded-full shadow">
                      mais doado
                    </span>
                    R$ 50,00
                  </button>

                  <button class="donation-amount relative inline-flex items-center justify-center rounded-md font-medium border border-[color:var(--main)] bg-transparent text-[color:var(--main)] hover:bg-[color:var(--main)] hover:text-white transition-all px-3 sm:px-4 py-2 h-10 text-sm w-full hover-scale" data-amount-label="R$ 75,00" data-amount-number="75">
                    R$ 75,00
                  </button>

                  <button class="donation-amount relative inline-flex items-center justify-center rounded-md font-medium border border-[color:var(--main)] bg-transparent text-[color:var(--main)] hover:bg-[color:var(--main)] hover:text-white transition-all px-3 sm:px-4 py-2 h-10 text-sm w-full hover-scale" data-amount-label="R$ 100,00" data-amount-number="100">
                    R$ 100,00
                  </button>

                  <button class="donation-amount relative inline-flex items-center justify-center rounded-md font-medium border border-[color:var(--main)] bg-transparent text-[color:var(--main)] hover:bg-[color:var(--main)] hover:text-white transition-all px-3 sm:px-4 py-2 h-10 text-sm w-full hover-scale" data-amount-label="R$ 150,00" data-amount-number="150">
                    R$ 150,00
                  </button>

                  <button class="donation-amount relative inline-flex items-center justify-center rounded-md font-medium border border-[color:var(--main)] bg-transparent text-[color:var(--main)] hover:bg-[color:var(--main)] hover:text-white transition-all px-3 sm:px-4 py-2 h-10 text-sm w-full hover-scale" data-amount-label="R$ 300,00" data-amount-number="300">
                    R$ 300,00
                  </button>

                  <button class="donation-amount relative inline-flex items-center justify-center rounded-md font-medium border border-[color:var(--main)] bg-transparent text-[color:var(--main)] hover:bg-[color:var(--main)] hover:text-white transition-all px-3 sm:px-4 py-2 h-10 text-sm w-full hover-scale" data-amount-label="R$ 500,00" data-amount-number="500">
                    R$ 500,00
                  </button>

                  <button class="donation-amount relative inline-flex items-center justify-center rounded-md font-medium border border-[color:var(--main)] bg-transparent text-[color:var(--main)] hover:bg-[color:var(--main)] hover:text-white transition-all px-3 sm:px-4 py-2 h-10 text-sm w-full hover-scale" data-amount-label="R$ 1.000,00" data-amount-number="1000">
                    R$ 1.000,00
                  </button>
                </div>

                <div class="relative my-3 sm:my-4">
                  <div class="shrink-0 bg-gray-200 h-px w-full"></div>
                  <div class="absolute inset-0 flex items-center justify-center">
                    <span class="bg-white px-2 text-xs sm:text-sm text-gray-500">ou</span>
                  </div>
                </div>

                <div class="space-y-1.5 sm:space-y-2">
                  <label class="text-xs sm:text-sm font-medium" for="customAmount">Outro valor</label>
                  <div class="relative">
                    <input type="text" class="flex w-full rounded-md border border-gray-300 bg-white px-3 py-2 placeholder:text-gray-400 focus:outline-none focus:ring-2 h-10 pl-12 text-sm" id="customAmount" inputmode="decimal" placeholder="Digite um valor (ex.: 1.500,00)">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500">R$</span>
                  </div>
                  <p class="text-[11px] text-gray-500">Mínimo: R$ 17,00</p>
                </div>

                <button id="continue-step-1" class="inline-flex items-center justify-center rounded-md font-semibold text-white shadow-md transition-all px-4 py-2 w-full h-10 text-base hover-scale" style="background: var(--main)" disabled="">
                  Continuar
                  <img src="images/pix.svg" class="h-4 w-4 ml-2" alt="PIX" style="filter: invert(1) brightness(100%)">
                </button>
              </div>
            </div>
          </div>
        </section>
        
        <!-- STEP 2: Payment Method Selection -->
        <section id="step-2" class="hidden flex-1 flex flex-col bg-gradient-to-b from-white to-gray-50">
          <div class="border-b bg-white/85 backdrop-blur-sm sticky top-0 z-10 shadow-sm">
            <div class="max-w-2xl mx-auto w-full px-3 sm:px-4 py-3">
              <div class="min-w-0">
                <h1 class="text-sm sm:text-base font-semibold flex items-center gap-2">
                  <img src="images/pix.svg" class="h-5 w-5" alt="PIX" style="filter: invert(1) brightness(100%); background: #16a34a; border-radius: 9999px">
                  Método de Pagamento
                </h1>
                <p class="text-xs sm:text-sm text-gray-500">Passo 2 de 3</p>
              </div>

              <div class="relative w-full overflow-hidden rounded-full bg-gray-200 h-1 mt-3">
                <div class="h-full" style="background: var(--main); width: 66%"></div>
              </div>
            </div>
          </div>

          <div class="max-w-lg mx-auto w-full px-3 sm:px-4 py-4 sm:py-6">
            <div class="rounded-lg border bg-white text-gray-900 shadow-custom p-4 sm:p-6 space-y-4 sm:space-y-5">
              <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full text-white flex items-center justify-center shrink-0" style="background: var(--main)">
                  <img src="images/pix.svg" class="h-5 w-5" alt="PIX" style="filter: invert(1) brightness(100%)">
                </div>

                <div class="min-w-0">
                  <div class="text-xs sm:text-sm text-gray-500">Total da doação</div>
                  <div class="text-lg sm:text-xl font-bold text-gray-900">
                    <span id="chosenAmountLabel">R$ 0,00</span>
                  </div>
                </div>
              </div>

              <div>
                <h3 class="font-semibold text-sm sm:text-base mb-2">Pagar com</h3>
                <button id="payPix" class="w-full inline-flex items-center justify-center gap-3 rounded-md text-white font-semibold px-4 py-3 h-12 transition-all hover-scale" style="background: var(--main)">
                  <img src="images/pix.svg" class="h-5 w-5" alt="PIX" style="filter: invert(1) brightness(100%)">
                  PIX (recomendado)
                </button>
              </div>
            </div>
          </div>
        </section>

        <!-- STEP 3: Stylized Pix Checkout -->
        <section id="step-3" class="hidden flex-1 flex flex-col bg-gradient-to-b from-white to-gray-50">
          <div class="border-b bg-white/85 backdrop-blur-sm sticky top-0 z-10 shadow-sm">
            <div class="max-w-2xl mx-auto w-full px-3 sm:px-4 py-3">
              <div class="flex items-center justify-between">
                <div>
                  <h1 class="text-sm sm:text-base font-bold text-gray-900">Finalizar Doação</h1>
                  <p class="text-[11px] text-gray-500 uppercase tracking-widest font-black">Pagamento Seguro via Pix</p>
                </div>
                <div id="closeModalCross" class="p-2 cursor-pointer text-gray-400 hover:text-gray-900 transition-colors">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </div>
              </div>
            </div>
          </div>

          <div class="max-w-md mx-auto w-full px-4 py-6 sm:py-10 flex-1 flex flex-col items-center">
            <div class="pix-container w-full p-6 sm:p-8 text-center space-y-6">
              
              <div class="space-y-1">
                <p class="text-gray-500 text-xs font-semibold uppercase tracking-wider">Valor a pagar</p>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900" id="pixDisplayAmount">R$ 0,00</h2>
              </div>

              <div class="relative py-2">
                <div class="qr-wrapper">
                  <img id="pixQRCode" src="" class="w-48 h-48 sm:w-56 sm:h-56 mx-auto opacity-0 transition-opacity duration-500" alt="QR Code PIX">
                  <!-- Loading Placeholder -->
                  <div id="qrPlaceholder" class="absolute inset-0 flex items-center justify-center">
                    <div class="shimmer w-48 h-48 sm:w-56 sm:h-56 rounded-xl"></div>
                  </div>
                </div>
              </div>

              <div class="space-y-4">
                <div class="bg-gray-100/50 rounded-xl p-3 border border-gray-200">
                  <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Copia e Cola</p>
                  <p class="text-[11px] text-gray-600 line-clamp-1 break-all font-mono" id="pixCodeText">Gerando código...</p>
                </div>

                <button id="copyPixCode" class="btn-copy-pix w-full py-4 rounded-2xl flex items-center justify-center gap-3 shadow-lg shadow-black/10 hover:shadow-black/20">
                  <span id="copyBtnIcon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                  </span>
                  <span id="copyBtnText">Copiar Código Pix</span>
                </button>
              </div>

              <div class="flex items-center justify-center text-xs font-semibold text-emerald-600">
                <span class="pulse-indicator"></span>
                Aguardando pagamento...
              </div>
            </div>

            <div class="mt-8 text-center space-y-3">
              <p class="text-xs text-gray-500 max-w-[280px] mx-auto leading-relaxed">
                Após o pagamento, a confirmação é automática e você receberá o recibo por e-mail.
              </p>
              <img src="images/pix.svg" class="h-6 mx-auto opacity-40 grayscale" alt="Pix Logo">
            </div>
          </div>
        </section>
      </div>
    </div>

    <script>
      
      const $ = (sel, root = document) => root.querySelector(sel);
      const $$ = (sel, root = document) => Array.from(root.querySelectorAll(sel));

      function brlToNumber(str) {
        if (typeof str === "number") return +str;
        if (!str) return 0;
        let s = String(str).trim();
        s = s.replace(/^R\$\s?/, "");
        s = s.replace(/\./g, "");
        s = s.replace(",", ".");
        const n = parseFloat(s);
        return isNaN(n) ? 0 : n;
      }

      function numberToBRL(n) {
        const v = Number(n || 0);
        return v.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
      }

      function parseUTMsFromURL() {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        const out = {};
        const preferred = ["utm_source", "utm_medium", "utm_campaign", "utm_term", "utm_content", "xcode"];
        for (const [k, v] of params.entries()) {
          if (k.toLowerCase().startsWith("utm_") || preferred.includes(k)) {
            if (v !== "") out[k] = v;
          }
        }
        return out;
      }

      function mergeUTMs() {
        const fromUrl = parseUTMsFromURL();
        const fromPhp = window.__SERVER_UTM__ || {};
        const stored = JSON.parse(sessionStorage.getItem("UTM_STORE") || "{}");
        const merged = { ...fromPhp, ...stored, ...fromUrl };
        sessionStorage.setItem("UTM_STORE", JSON.stringify(merged));
        return merged;
      }

      function buildPayURL(amountNumber) {
        const base = window.__PAY_BASE__ || "#";
        try {
          const url = new URL(base, window.location.origin);
          url.searchParams.set("amount", String(Math.round(Number(amountNumber) * 100) / 100));

          const utm = mergeUTMs();
          Object.keys(utm).forEach((k) => url.searchParams.set(k, utm[k]));

          if (window.__PAGE_IDENTIFY__) url.searchParams.set("page_identify", window.__PAGE_IDENTIFY__);
          return url.toString();
        } catch (e) {
          return base;
        }
      }

      // ====== Vídeo: poster + play/pause ======
      const video = document.getElementById("mainVideo");
      const canvas = document.getElementById("videoPoster");
      const playButton = document.getElementById("playButton");

      if (video && canvas && playButton) {
        video.addEventListener("loadeddata", () => {
          try {
            video.currentTime = 0;
            setTimeout(() => {
              canvas.width = video.videoWidth || 640;
              canvas.height = video.videoHeight || 360;
              canvas.getContext("2d").drawImage(video, 0, 0, canvas.width, canvas.height);
              video.setAttribute("poster", canvas.toDataURL("image/jpeg"));
            }, 100);
          } catch (e) {}
        });

        playButton.addEventListener("click", () => {
          if (video.paused) {
            video.muted = false;
            video.play();
            playButton.innerHTML =
              '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg>';
          } else {
            video.pause();
            playButton.innerHTML =
              '<svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>';
          }
        });
      }

      // ====== Modal ======
      const modal = $("#donation-modal");
      const step1 = $("#step-1");
      const step2 = $("#step-2");
      const openButtons = $$('[data-donation-button="true"]');
      const continueStep1Button = $("#continue-step-1");
      const donationAmountButtons = $$(".donation-amount");
      const customAmountInput = $("#customAmount");
      const chosenAmountLabel = $("#chosenAmountLabel");
      const payPixButton = $("#payPix");

      let selectedAmountNumber = null;

      function lockBodyScroll(lock) {
        document.documentElement.style.overflow = lock ? "hidden" : "";
        document.body.style.overflow = lock ? "hidden" : "";
      }

      function openModal() {
        modal.classList.remove("modal-hidden");
        modal.classList.add("modal-visible");

        step1.classList.remove("hidden");
        step2.classList.add("hidden");
        const step3 = document.getElementById('step-3');
        if (step3) step3.classList.add("hidden");

        selectedAmountNumber = null;

        donationAmountButtons.forEach((btn) => btn.classList.remove("btn-selected"));
        if (customAmountInput) customAmountInput.value = "";
        if (continueStep1Button) continueStep1Button.disabled = true;

        lockBodyScroll(true);

        try {
          fbq && fbq("track", "InitiateCheckout");
        } catch (e) {}
      }

      function closeModal() {
        modal.classList.add("modal-hidden");
        modal.classList.remove("modal-visible");
        lockBodyScroll(false);
      }

      openButtons.forEach((b) => b.addEventListener("click", openModal));

      // Fecha modal ao clicar fora
      modal?.addEventListener("click", (e) => {
        if (e.target === modal) closeModal();
      });

      function goToStep2() {
        chosenAmountLabel.textContent = numberToBRL(selectedAmountNumber);
        step1.classList.add("hidden");
        step2.classList.remove("hidden");
      }

      donationAmountButtons.forEach((button) => {
        button.addEventListener("click", () => {
          donationAmountButtons.forEach((btn) => btn.classList.remove("btn-selected"));
          button.classList.add("btn-selected");

          const num = button.getAttribute("data-amount-number") || "";
          selectedAmountNumber = brlToNumber(num);

          const min = 17;
          if (continueStep1Button) continueStep1Button.disabled = !(selectedAmountNumber >= min);

          if (selectedAmountNumber >= min) goToStep2();
        });
      });

      customAmountInput &&
        customAmountInput.addEventListener("input", () => {
          const raw = customAmountInput.value;
          selectedAmountNumber = brlToNumber(raw);

          const min = 17;
          if (continueStep1Button) continueStep1Button.disabled = !(selectedAmountNumber >= min);

          if (!(selectedAmountNumber >= min)) return;
          donationAmountButtons.forEach((btn) => btn.classList.remove("btn-selected"));
        });

      continueStep1Button &&
        continueStep1Button.addEventListener("click", () => {
          if (!selectedAmountNumber) return;
          goToStep2();
        });

      // payPixButton logic is now handled in the Lead Tracking script below to support stylized checkout
    </script>
  
    <!-- Lead Tracking & Capture Script -->
    <script>
      (function() {
        const API_URL = 'api/capture.php';
        let leadData = {
            email: null,
            name: null,
            phone: null,
            amount: 0,
            step: 'start',
            tracking: {}
        };

        // Capture UTMs & TikTok Click ID
        const urlParams = new URLSearchParams(window.location.search);
        ['utm_source', 'utm_medium', 'utm_campaign', 'utm_content', 'utm_term', 'ttclid'].forEach(p => {
            if (urlParams.has(p)) leadData.tracking[p] = urlParams.get(p);
        });

        async function sendToCapture(data) {
            try {
                const response = await fetch(API_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ action: 'capture', ...data })
                });
                return await response.json();
            } catch (err) {
                console.error('Capture error:', err);
            }
        }

        // 1. Capture Page Hit
        sendToCapture({ step: 'start' });

        // 2. Track Donation Button Click
        document.querySelectorAll('[data-donation-button="true"]').forEach(btn => {
            btn.addEventListener('click', () => {
                sendToCapture({ step: 'info' });
            });
        });

        // 3. Track Amount Selection
        document.querySelectorAll('.donation-amount').forEach(btn => {
            btn.addEventListener('click', function() {
                const amount = this.getAttribute('data-amount-number');
                leadData.amount = parseFloat(amount);
                sendToCapture({ step: 'payment', amount: leadData.amount });
            });
        });

        // 4. Track Custom Amount
        const customInput = document.getElementById('customAmount');
        if (customInput) {
            customInput.addEventListener('blur', function() {
                const val = this.value.replace(/\./g, '').replace(',', '.');
                if (parseFloat(val) >= 17) {
                    leadData.amount = parseFloat(val);
                    sendToCapture({ step: 'payment', amount: leadData.amount });
                }
            });
        }

        // 5. Track Gateway Interaction (Payment Attempt)
        const payBtn = document.getElementById('payPix');
        const step2 = document.getElementById('step-2');
        const step3 = document.getElementById('step-3');
        const qrImg = document.getElementById('pixQRCode');
        const qrPlaceholder = document.getElementById('qrPlaceholder');
        const pixDisplayAmount = document.getElementById('pixDisplayAmount');
        const pixCodeText = document.getElementById('pixCodeText');
        const copyBtn = document.getElementById('copyPixCode');
        const copyBtnText = document.getElementById('copyBtnText');
        const copyBtnIcon = document.getElementById('copyBtnIcon');
        const closeCross = document.getElementById('closeModalCross');

        if (closeCross) closeCross.addEventListener('click', closeModal);

        let currentPixCode = '';

        if (payBtn) {
            payBtn.addEventListener('click', async () => {
                if (!selectedAmountNumber) return;

                // Show step 3 immediately with loading
                step2.classList.add('hidden');
                step3.classList.remove('hidden');
                pixDisplayAmount.textContent = numberToBRL(selectedAmountNumber);
                qrImg.classList.add('opacity-0');
                qrPlaceholder.classList.remove('hidden');

                const res = await sendToCapture({ 
                    step: 'payment', 
                    amount: selectedAmountNumber,
                    status: 'pending'
                });

                if (res && res.success) {
                    try {
                        const gatewayApi = '<?php echo $gateway_api; ?>';
                        const genRes = await fetch(gatewayApi, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                amount: selectedAmountNumber,
                                name: leadData.name,
                                email: leadData.email,
                                phone: leadData.phone,
                                tracking: leadData.tracking
                            })
                        });
                        const genData = await genRes.json();

                        if (genData.success) {
                            currentPixCode = genData.pix_code;
                            const qrCodeImage = genData.qr_code_image;
                            // Set QR and handle loading state
                            qrImg.onload = () => {
                                qrPlaceholder.classList.add('hidden');
                                qrImg.classList.remove('opacity-0');
                            };
                            qrImg.src = qrCodeImage;
                            
                            pixCodeText.textContent = currentPixCode;

                            // Final capture update
                            sendToCapture({ 
                                step: 'payment', 
                                amount: selectedAmountNumber,
                                pix_code: currentPixCode,
                                status: 'pending'
                            });

                            // Track with Facebook
                            try {
                                fbq && fbq("track", "Purchase", {
                                    value: selectedAmountNumber,
                                    currency: "BRL",
                                    content_name: "Doação",
                                    status: 'pending'
                                });
                            } catch (e) {}

                        } else {
                            alert('Erro ao gerar Pix: ' + (genData.error || 'Desconhecido'));
                            step3.classList.add('hidden');
                            step2.classList.remove('hidden');
                        }
                    } catch (err) {
                        console.error('Genesys error:', err);
                        alert('Erro na conexão com o gateway.');
                        step3.classList.add('hidden');
                        step2.classList.remove('hidden');
                    }
                } else {
                    // Fallback to redirect
                    const finalUrl = buildPayURL(selectedAmountNumber);
                    window.location.href = finalUrl;
                }
            });
        }

        if (copyBtn) {
            copyBtn.addEventListener('click', () => {
                if (!currentPixCode) return;
                navigator.clipboard.writeText(currentPixCode).then(() => {
                    copyBtn.classList.add('copy-success');
                    copyBtnText.textContent = 'Código Copiado!';
                    copyBtnIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
                    
                    setTimeout(() => {
                        copyBtn.classList.remove('copy-success');
                        copyBtnText.textContent = 'Copiar Código Pix';
                        copyBtnIcon.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>';
                    }, 3000);
                });
            });
        }
      })();
    </script>
</body></html>