<style>

.header-top-section{
    background-color:#0c142e !important;
}
  .hero-section,
  .navbar,
  header,
  .main-header,
  .site-header,
  nav,
  .navigation-menu,
  .top-header,
  .header-area,
  .banner-section,
  .page-header,
  .breadcrumb-area,
  .page-banner,
  .header-top,
  .main-nav,
  .site-nav,
  .primary-nav,
  .top-bar,
  .menu-bar,
  .header-wrapper,
  .site-navigation,
  .main-navigation,
  .page-hero,
  .hero-area,
  .inner-header,
  .page-title-area,
  .breadcrumb-wrapper,
  .hero-banner,
  .page-banner-area,
  .top-header-area,
  .middle-header,
  .bottom-header,
  .header-section,
  .site-branding,
  .menu-main-container,
  .primary-menu,
  .main-menu,
  .navbar-header,
  .navbar-collapse,
  .nav-header,
  .nav-wrapper,
  .header-nav,
  .header-menu,
  .page-top-header,
  .page-navigation,
  .page-nav,
  .theme-header,
  .theme-nav,
  .custom-header,
  .wp-block-template-part {
    display: none !important;
    visibility: hidden !important;
    opacity: 0 !important;
    height: 0 !important;
    min-height: 0 !important;
    max-height: 0 !important;
    overflow: hidden !important;
    position: absolute !important;
    z-index: -9999 !important;
    pointer-events: none !important;
  }
  
  footer,
  .footer,
  .site-footer,
  .main-footer,
  .footer-section,
  .footer-area,
  .copyright-area,
  .footer-widgets,
  .footer-bottom {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    height: auto !important;
    min-height: auto !important;
    max-height: none !important;
    overflow: visible !important;
    position: relative !important;
    z-index: 1 !important;
    pointer-events: auto !important;
  }
  
  .social-icon,
  .social-links,
  .footer-social,
  .social-media,
  [class*="social-icon"],
  [class*="social-link"] {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 15px !important;
    visibility: visible !important;
    opacity: 1 !important;
    height: auto !important;
    min-height: 40px !important;
    overflow: visible !important;
    position: relative !important;
    z-index: 100 !important;
    margin: 15px 0 !important;
    padding: 5px 0 !important;
  }
  
  .social-icon a,
  .social-links a,
  .footer-social a,
  [class*="social-icon"] a,
  [class*="social-link"] a {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 38px !important;
    height: 38px !important;
    background: white !important;
    border-radius: 50% !important;
    color: #36454F!important;
    text-decoration: none !important;
    transition: all 0.3s ease !important;
  }
  
  .social-icon a:hover,
  .social-links a:hover,
  .footer-social a:hover {
    background: #006d7c !important;
    transform: translateY(-3px) !important;
  }
  
  .social-icon i,
  .social-links i,
  .footer-social i,
  [class*="social-icon"] i {
    font-size: 18px !important;
    color: #36454F!important;
  }
  
  .social-icon img,
  .social-links img {
    width: 20px !important;
    height: 20px !important;
    filter: brightness(0) invert(1) !important;
  }
  
  .footer .container,
  footer .container,
  .footer-section .container {
    display: block !important;
    visibility: visible !important;
    overflow: visible !important;
  }
  
  .footer-row,
  .footer .row,
  footer .row {
    display: flex !important;
    flex-wrap: wrap !important;
    visibility: visible !important;
  }
  
  body,
  html {
    margin-top: 0 !important;
    padding-top: 0 !important;
  }
  
  body > *:first-child {
    margin-top: 0 !important;
  }
  
@media(max-width:650px){
  .mobile-top-logo {
    display: block !important;
    position: relative;
    top: 0;
    left: 0;
    width: 100%;
    z-index: 999;
  }
}

@media(min-width:651px){
  .mobile-top-logo {
    display: none !important;
  }
}
</style>

<div class="mobile-top-logo" style="display:none; background:#0c142e; padding:15px 0; text-align:center;">
  <img src="assets/img/logo/white-logo-2.png" alt="Logo" style="height:45px; width:auto;">
</div>

<script>
  (function() {
    var hideSelectors = [
      '.hero-section', '.navbar', 'header', '.main-header', '.site-header',
      'nav', '.navigation-menu', '.top-header', '.header-area', 
      '.banner-section', '.page-header', '.breadcrumb-area', '.page-banner'
    ];
    hideSelectors.forEach(function(sel) {
      var els = document.querySelectorAll(sel);
      for (var i = 0; i < els.length; i++) {
        els[i].style.display = 'none';
        els[i].style.visibility = 'hidden';
        els[i].style.height = '0';
      }
    });
    document.body.style.marginTop = '0';
    document.body.style.paddingTop = '0';
    setTimeout(function() {
      var socialIcons = document.querySelectorAll('.social-icon, .social-links, .footer-social, [class*="social-icon"]');
      socialIcons.forEach(function(icon) {
        icon.style.display = 'flex';
        icon.style.visibility = 'visible';
        icon.style.opacity = '1';
      });
    }, 100);
  })();
</script>

<?php
$headTitle = 'Passenger Information';
$img = \App\Support\GalleryPath::path('i/149');
$Title = 'Home';
$Title2 = 'Passenger Information';
$SubTitle = 'Complete Your Booking';

$stripePublishableKey = (function () {
  $defaultKey = (string) config('services.stripe.key', '');
  $tables = [
    'executiveairport_database.admin_settings',
    'admin_settings',
  ];

  foreach ($tables as $table) {
    try {
      $row = \Illuminate\Support\Facades\DB::table($table)->first();
      if (!$row) {
        continue;
      }

      $misc = [];
      $rawMisc = $row->misc ?? null;

      if (is_array($rawMisc)) {
        $misc = $rawMisc;
      } elseif (is_string($rawMisc) && trim($rawMisc) !== '') {
        $decoded = json_decode($rawMisc, true);
        if (is_array($decoded)) {
          $misc = $decoded;
        }
      }

      $dbKey = trim((string) ($misc['stripe_public_key'] ?? ''));
      if ($dbKey !== '') {
        return $dbKey;
      }
    } catch (\Throwable $e) {
      continue;
    }
  }

  return $defaultKey;
})();
?>

<?php echo $__env->make('partials.layouts.layoutsTop', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    var selectors = [
      '.hero-section', '.navbar', 'header', '.main-header', '.site-header',
      'nav', '.navigation-menu', '.top-header', '.header-area', 
      '.banner-section', '.page-header', '.breadcrumb-area'
    ];
    selectors.forEach(function(sel) {
      var elements = document.querySelectorAll(sel);
      elements.forEach(function(el) {
        if (el) el.style.display = 'none';
      });
    });
  });
</script>

<link href="https://fonts.googleapis.com/css2?family=Inter:opsz,wght@14..32,300;14..32,400;14..32,500;14..32,600;14..32,700;14..32,800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<!-- Leaflet CSS for interactive map -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }

  :root {
    --primary: #008B9E;
    --primary-dark: #006d7c;
    --primary-light: rgba(0, 139, 158, 0.08);
    --primary-glow: rgba(0, 139, 158, 0.15);
    --secondary: #2c3e50;
    --white: #ffffff;
    --gray-50: #fafbfc;
    --gray-100: #f5f7fa;
    --gray-200: #eef2f6;
    --gray-300: #e2e8f0;
    --gray-400: #cbd5e1;
    --gray-500: #94a3b8;
    --gray-600: #64748b;
    --gray-700: #475569;
    --gray-800: #1e293b;
    --gray-900: #0f172a;
    --shadow-sm: 0 1px 2px rgba(0,0,0,0.04);
    --shadow: 0 4px 12px rgba(0,0,0,0.06);
    --shadow-md: 0 8px 24px rgba(0,0,0,0.08);
    --shadow-lg: 0 16px 32px rgba(0,0,0,0.1);
    --radius: 20px;
    --radius-md: 16px;
    --radius-sm: 12px;
  }

  body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
    min-height: 100vh;
  }

  /* PROGRESS BAR */
  .progress-bar-wrap {
    max-width: 1300px;
    margin: 0 auto;
    padding: 32px 24px 0;
    display: flex;
    justify-content: center;
  }

  .progress-steps {
    display: flex;
    align-items: center;
    gap: 0;
    margin-bottom: 0;
    width: 100%;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
  }

  .ps-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    position: relative;
    z-index: 1;
    flex-shrink: 0;
  }

  .ps-connector {
    flex: 1;
    height: 2px;
    background: var(--gray-300);
    position: relative;
    top: -17px;
    z-index: 0;
  }

  .ps-connector.done {
    background: var(--gray-800);
  }

  .ps-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    border: 2px solid var(--gray-300);
    background: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 700;
    color: var(--gray-400);
    position: relative;
    z-index: 2;
    transition: all 0.3s;
  }

  .ps-item.done .ps-circle {
    background: var(--gray-900);
    border-color: var(--gray-900);
    color: white;
  }

  .ps-item.active .ps-circle {
    background: var(--white);
    border: 2px solid var(--primary);
    color: var(--primary);
    box-shadow: none;
    font-weight: 800;
  }

  .ps-label {
    font-size: 11px;
    font-weight: 600;
    color: var(--gray-400);
    margin-top: 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    white-space: nowrap;
  }

  .ps-item.done .ps-label {
    color: var(--gray-700);
    font-weight: 600;
  }

  .ps-item.active .ps-label {
    color: var(--primary);
    font-weight: 700;
  }

  /* BOOKING LAYOUT */
  .booking-modern {
    max-width: 1300px;
    margin: 0 auto;
    padding: 32px 24px 40px;
  }

  .booking-header {
    text-align: center;
    margin-bottom: 32px;
  }

  .booking-header h1 {
    font-size: 32px;
    font-weight: 800;
    background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-700) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 8px;
    letter-spacing: -0.02em;
  }

  .booking-header p {
    font-size: 15px;
    color: var(--gray-600);
    max-width: 500px;
    margin: 0 auto;
  }

  .booking-grid {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 32px;
    align-items: start;
  }

  /* =============================================
     RIGHT SIDE SUMMARY - REDESIGNED
     ============================================= */

  .summary-modern {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    transition: transform 0.2s, box-shadow 0.2s;
  }

  .summary-modern:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }

  /* Make the whole aside sticky */
  .booking-grid > aside {
    position: sticky;
    top: 24px;
  }

  /* Summary header (dark top bar) */
  .summary-head {
    background: linear-gradient(135deg, var(--gray-900) 0%, var(--gray-800) 100%);
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 14px;
  }

  .summary-icon-wrapper {
    width: 44px;
    height: 44px;
    background: var(--primary);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 139, 158, 0.3);
    flex-shrink: 0;
  }

  .summary-icon-wrapper i { font-size: 20px; color: white !important; }

  .summary-head-text h3 {
    font-size: 17px;
    font-weight: 700;
    color: white;
    margin-bottom: 2px;
  }

  .summary-head-text p { font-size: 12px; color: rgba(255,255,255,0.7); }

  /* Inner card area */
  .summary-card-body {
    padding: 18px 20px 20px 20px;
  }

  /* Location rows (Ashford / London Luton) */
  .sb-location-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 0;
  }

  .sb-location-dot {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
  }

  .sb-location-dot.pickup {
    background: rgba(255, 165, 0, 0.12);
  }
  .sb-location-dot.pickup i { color: #f59e0b !important; font-size: 13px; }

  .sb-location-dot.dropoff {
    background: rgba(0, 139, 158, 0.1);
  }
  .sb-location-dot.dropoff i { color: var(--primary) !important; font-size: 13px; }

  .sb-location-text {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-800);
    line-height: 1.4;
  }

  .sb-location-label {
    font-size: 10px;
    font-weight: 600;
    color: var(--gray-500);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
  }

  .sb-loc-divider {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0 0 0 9px;
  }

  .sb-loc-divider-line {
    width: 1px;
    height: 14px;
    background: var(--gray-300);
    margin-left: 4px;
  }

  /* Vehicle row */
  .sb-vehicle-row {
    display: flex;
    align-items: center;
    gap: 12px;
    background: var(--gray-50);
    border-radius: var(--radius-sm);
    padding: 12px 14px;
    margin: 4px 0 14px 0;
    border: 1px solid var(--gray-200);
  }

  .sb-vehicle-icon {
    width: 36px;
    height: 36px;
    background: var(--primary-light);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
  }

  .sb-vehicle-icon i { color: var(--primary) !important; font-size: 16px; }

  .sb-vehicle-name {
    font-size: 14px;
    font-weight: 700;
    color: var(--gray-800);
    margin-bottom: 2px;
  }

  .sb-vehicle-meta {
    font-size: 11px;
    color: var(--gray-500);
    font-weight: 500;
  }

  /* Date / Time row */
  .sb-datetime-row {
    display: flex;
    gap: 10px;
    margin-bottom: 14px;
  }

  .sb-datetime-chip {
    display: flex;
    align-items: center;
    gap: 7px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 8px 12px;
    flex: 1;
  }

  .sb-datetime-chip i { color: var(--primary) !important; font-size: 13px; }

  .sb-datetime-val {
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-800);
  }

  /* Total Fare */
  .sb-fare-summary {
    background: #f5fcfe;
    border: 1px solid rgba(0, 139, 158, 0.18);
    border-radius: 18px;
    padding: 18px 20px;
    margin-bottom: 18px;
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .sb-fare-row,
  .sb-vat-row,
  .sb-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  .sb-vat-row {
    font-size: 12px;
    color: var(--gray-600);
    letter-spacing: 0.2px;
    font-weight: 600;
    text-transform: uppercase;
  }

  .sb-total-row {
    padding-top: 8px;
    border-top: 1px solid rgba(0, 139, 158, 0.12);
    margin-top: 6px;
  }

  .sb-fare-label,
  .sb-total-label {
    font-size: 13px;
    font-weight: 700;
    color: var(--gray-800);
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }

  .sb-fare-amount,
  .sb-vat-amount,
  .sb-total-amount {
    font-weight: 800;
    color: var(--primary);
  }

  .sb-fare-amount,
  .sb-vat-amount {
    font-size: 20px;
  }

  .sb-total-amount {
    font-size: 30px;
  }

  .sb-fare-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
  }

  /* Divider — kept for potential use */
  .sb-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, var(--gray-300), transparent);
    margin: 0 0 0 0;
  }

  /* Map tabs + embedded map — BELOW stats */
  .sb-map-section {
    margin: 0 0 0 0;
  }

  .sb-map-tabs {
    display: flex;
    border: 1px solid var(--gray-300);
    border-radius: 8px 8px 0 0;
    overflow: hidden;
    background: var(--gray-100);
  }

  .sb-map-tab {
    flex: 1;
    padding: 8px 14px;
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-600);
    background: var(--gray-100);
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 5px;
  }

  .sb-map-tab.active {
    background: var(--white);
    color: var(--gray-900);
    box-shadow: inset 0 -2px 0 var(--primary);
  }

  .sb-map-tab:hover:not(.active) {
    background: var(--gray-200);
  }

  /* Embedded map container - sits freely below tabs */
  .sb-map-embed {
    width: 100%;
    height: 220px;
    border: 1px solid var(--gray-300);
    border-top: none;
    border-radius: 0 0 var(--radius-sm) var(--radius-sm);
    overflow: hidden;
    background: var(--gray-100);
  }

  #routeMap {
    width: 100%;
    height: 100%;
  }

  /* Google Maps satellite embed (hidden by default) */
  .sb-map-satellite-frame {
    width: 100%;
    height: 100%;
    display: none;
  }

  .sb-map-satellite-frame iframe {
    width: 100%;
    height: 100%;
    border: none;
  }

  /* Back button */
  .back-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 20px;
    margin-bottom: 40px;
    padding: 14px;
    background: var(--white);
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-sm);
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    color: var(--gray-700);
    transition: all 0.2s;
  }

  .back-btn:hover {
    border-color: var(--primary);
    color: var(--primary);
    background: var(--primary-light);
  }

  /* Map directions link */
  .sb-map-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-top: none;
    padding: 9px 14px;
    text-decoration: none;
    font-size: 12px;
    font-weight: 600;
    color: var(--primary);
    transition: all 0.2s;
    margin-bottom: 20px;
  }

  .sb-map-link:hover {
    background: var(--primary-light);
    color: var(--primary-dark);
  }

  .sb-map-link i { font-size: 11px; color: var(--primary) !important; }

  /* ── SECOND CARD: route stats + map ── */
  .route-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
    overflow: hidden;
    margin-top: 13px;
    padding: 16px 20px 0 20px;
    transition: transform 0.2s, box-shadow 0.2s;
    margin-bottom:34px;
    height:310px;
  }

  .route-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
  }

  /* Miles / minutes rows — matches reference image style */
  .rc-stat-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 0;
    border-bottom: 1px solid var(--gray-100);
    font-size: 14px;
    font-weight: 600;
    color: var(--gray-700);
  }

  .rc-stat-row:last-of-type {
    border-bottom: none;
    margin-bottom: 12px;
  }

  .rc-stat-row i {
    color: var(--primary) !important;
    font-size: 15px;
    width: 18px;
    text-align: center;
  }

  /* =============================================
     FORM CARD (LEFT SIDE — UNCHANGED)
     ============================================= */

  .form-modern {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow-md);
    border: 1px solid var(--gray-200);
    overflow: hidden;
  }

  .form-header-modern {
    padding: 20px 24px 16px 24px;
    border-bottom: 1px solid var(--gray-200);
    text-align: center;
  }

  .form-header-modern h2 {
    font-size: 20px;
    font-weight: 700;
    color: var(--gray-900);
    margin-bottom: 2px;
  }

  .form-header-modern p {
    font-size: 13px;
    color: var(--gray-600);
  }

  .form-body-modern {
    padding: 20px 24px 24px 24px;
  }

  .field-group {
    margin-bottom: 12px;
  }

  .field-row-2 {
    display: flex;
    gap: 12px;
    margin-bottom: 12px;
  }

  .field-row-3 {
    display: grid;
    grid-template-columns: 1fr 1fr 1fr;
    gap: 12px;
    margin-bottom: 12px;
    align-items: end;
  }

  .input-modern {
    display: flex;
    flex-direction: column;
    gap: 5px;
  }

  .input-modern label {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-700);
    display: flex;
    align-items: center;
    gap: 5px;
  }

  .input-modern label i { color: var(--primary); font-size: 11px; }

  .input-modern input,
  .input-modern select,
  .input-modern textarea {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    padding: 9px 13px;
    border: 1.5px solid var(--gray-300);
    border-radius: 10px;
    background: var(--white);
    transition: all 0.2s;
    outline: none;
    width: 100%;
    color: var(--gray-800);
  }

  .input-modern select {
    display: block !important;
    width: 100% !important;
    padding: 9px 36px 9px 13px !important;
    border: 1.5px solid var(--gray-300) !important;
    border-radius: 10px !important;
    background-color: var(--white) !important;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") !important;
    background-repeat: no-repeat !important;
    background-position: right 12px center !important;
    appearance: none !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    font-family: 'Inter', sans-serif !important;
    font-size: 13px !important;
    color: var(--gray-800) !important;
    cursor: pointer !important;
    outline: none !important;
  }

  .input-modern input:focus,
  .input-modern select:focus,
  .input-modern textarea:focus {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px var(--primary-glow);
  }

  .input-modern input::placeholder,
  .input-modern textarea::placeholder { color: var(--gray-400); }

  .input-modern input[type="date"] { color: var(--gray-800) !important; }
  .input-modern input[type="date"]::-webkit-datetime-edit { color: var(--gray-800) !important; }
  .input-modern input[type="date"]::-webkit-datetime-edit-text { color: var(--gray-800) !important; }
  .input-modern input[type="date"]::-webkit-datetime-edit-month-field { color: var(--gray-800) !important; }
  .input-modern input[type="date"]::-webkit-datetime-edit-day-field { color: var(--gray-800) !important; }
  .input-modern input[type="date"]::-webkit-datetime-edit-year-field { color: var(--gray-800) !important; }

  /* Phone input wrapper */
  .phone-input-wrapper {
    display: flex;
    align-items: stretch;
    border: 1.5px solid var(--gray-300);
    border-radius: 10px;
    overflow: hidden;
    transition: border-color 0.2s, box-shadow 0.2s;
    background: var(--white);
  }

  .phone-input-wrapper:focus-within {
    border-color: var(--primary) !important;
    box-shadow: 0 0 0 3px var(--primary-glow);
  }

  .phone-code-select {
    width: 95px;
    min-width: 95px;
    background: var(--gray-100);
    border: none;
    padding: 9px 6px 9px 12px;
    font-family: 'Inter', sans-serif;
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-700);
    cursor: pointer;
    outline: none;
    border-right: 1.5px solid var(--gray-300);
  }

  .phone-input-wrapper input[type="tel"] {
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    flex: 1;
    min-width: 0;
    padding: 9px 12px !important;
    color: var(--gray-800) !important;
  }

  .phone-input-wrapper input[type="tel"]:focus {
    border: none !important;
    box-shadow: none !important;
    outline: none !important;
  }

  /* Child seat toggle */
  .toggle-row {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0;
  }

  .toggle-label {
    font-size: 12px;
    font-weight: 600;
    color: var(--gray-700);
    white-space: nowrap;
  }

  .toggle-switch {
    position: relative;
    display: inline-block;
    width: 44px;
    height: 24px;
    flex-shrink: 0;
  }

  .toggle-switch input { opacity: 0; width: 0; height: 0; }

  .toggle-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background: var(--gray-300);
    border-radius: 24px;
    transition: 0.3s;
  }

  .toggle-slider:before {
    position: absolute;
    content: "";
    height: 18px; width: 18px;
    left: 3px; bottom: 3px;
    background: white;
    border-radius: 50%;
    transition: 0.3s;
  }

  .toggle-switch input:checked + .toggle-slider { background: var(--primary); }
  .toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }

  /* Meet & Greet & Return checkbox rows */
  .checkbox-row {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px 13px;
    border: 1.5px solid var(--gray-200);
    border-radius: 10px;
    cursor: pointer;
    margin-bottom: 10px;
    transition: all 0.2s;
    background: var(--white);
  }

  .checkbox-row:hover {
    border-color: var(--primary);
    background: var(--primary-light);
  }

  .checkbox-row input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--primary);
    cursor: pointer;
    flex-shrink: 0;
  }

  .checkbox-row-label {
    display: flex;
    align-items: center;
    gap: 7px;
    font-size: 13px;
    font-weight: 600;
    color: var(--gray-800);
    cursor: pointer;
    user-select: none;
    flex: 1;
  }

  .checkbox-row-label .pin-icon { color: var(--primary) !important; font-size: 12px; }

  /* Return expanded section */
  .return-modern {
    display: none;
    background: var(--gray-50);
    border-radius: 10px;
    padding: 16px;
    margin-bottom: 10px;
    border: 1px solid var(--gray-200);
  }

  .return-modern.visible { display: block; }

  .return-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 14px;
  }

  .return-badge-modern {
    background: var(--primary);
    color: white;
    padding: 4px 10px;
    border-radius: 30px;
    font-size: 11px;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 5px;
  }

  .terms-modern {
    text-align: center;
    font-size: 12px;
    color: var(--gray-500);
    margin-bottom: 12px;
    line-height: 1.6;
  }

  .terms-modern a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 500;
  }

  .terms-modern a:hover { text-decoration: underline; }

  /* Payment buttons */
  .payment-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
  }

  .btn-payment {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 14px 24px;
    border: none;
    border-radius: var(--radius-md);
    font-family: 'Inter', sans-serif;
    font-size: 15px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
    letter-spacing: 0.01em;
  }

  .btn-card-modern {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
  }

  .btn-card-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 139, 158, 0.3);
  }

  .btn-cash-modern {
    background: linear-gradient(135deg, var(--gray-800) 0%, var(--gray-900) 100%);
    color: white;
  }

  .btn-cash-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
  }

  .btn-payment:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
  }

  /* Icon colour overrides */
  .sb-card i:not(.custom-footer i):not(.social-icons-wrapper i),
  .sb-head i, .sj-label i, .sb-meta-row i, .sb-map-label i,
  .sb-trust-row i, .sb-pay-lbl i, .help-icon i,
  .summary-modern i:not(.custom-footer i),
  .location-icon i, .info-label-modern i, .price-modern i,
  .back-btn i { color: #4a5568 !important; }

  .summary-head .summary-icon-wrapper i { color: white !important; }
  .summary-icon-wrapper .fas.fa-map-marked-alt { color: white !important; }

  .vcard i:not(.custom-footer i):not(.social-icons-wrapper i),
  .vc-pvt i, .vc-stars i, .vc-cap-pill i, .vc-feats li i,
  .safe-badge i, .more-toggle i, .vehicle-highlights i, .feat-dot { color: #4a5568 !important; }

  .trust-bar i, .ti-icon i { color: #4a5568 !important; }
  .prog-bar i, .s-circle i { color: #4a5568 !important; }
  .s-item.done .s-circle i { color: white !important; }
  .s-item.active .s-circle { background: var(--primary) !important; border-color: var(--primary) !important; }
  .s-item.active .s-circle i { color: white !important; }

  .form-modern i, .input-modern label i,
  .return-badge-modern i, .checkbox-row i, .terms-modern i { color: #4a5568 !important; }

  .btn-payment i, .btn-cash-modern i, .btn-card-modern i { color: white !important; }
  .checkbox-row-label .pin-icon { color: var(--primary) !important; }
  .ps-item.done .ps-circle i { color: white !important; }

  .input-modern select + * { display: none !important; }

  .header-top-section {
    background: #0c142e url('assets/img/logo/white-logo-2.png') no-repeat left 24px center !important;
    background-size: 120px auto !important;
    padding: 12px 24px !important;
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    flex-wrap: wrap !important;
    position: relative !important;
  }
  .header-top-section::before { content: none !important; display: none !important; }
  
  .header-top-section img { display: block !important; height: 45px !important; width: auto !important; visibility: visible !important; opacity: 1 !important; }
  .header-top-section .container, .header-top-section .container-fluid, .header-top-section > div { display: flex !important; align-items: center !important; justify-content: flex-end !important; gap: 30px !important; flex: 1 !important; }
  .header-top-section span, .header-top-section a, .header-top-section p { color: rgba(255,255,255,0.85) !important; font-size: 13px !important; text-decoration: none !important; white-space: nowrap !important; }
  .header-top-section a:hover { color: #008B9E !important; }

  @media (max-width: 650px) { .header-top-section { display: none !important; } .mobile-top-logo { display: block !important; } }
  @media (min-width: 651px) { .mobile-top-logo { display: none !important; } }

  html, body { overflow-x: hidden !important; width: 100% !important; }

  .cta-cheap-rental { visibility: hidden !important; }
  .cta-cheap-rental-section { display: none !important; visibility: hidden !important; height: 0 !important; min-height: 0 !important; max-height: 0 !important; overflow: hidden !important; position: absolute !important; z-index: -9999 !important; pointer-events: none !important; margin: 0 !important; padding: 0 !important; }

  footer:not(.custom-footer), .footer:not(.custom-footer), .site-footer:not(.custom-footer),
  .main-footer:not(.custom-footer), .footer-section:not(.custom-footer),
  .footer-area:not(.custom-footer), .copyright-area:not(.custom-footer),
  .footer-widgets:not(.custom-footer), .footer-bottom:not(.custom-footer) {
    display: none !important; visibility: hidden !important; height: 0 !important;
    overflow: hidden !important; position: absolute !important; z-index: -9999 !important;
  }

  .custom-footer { display: block !important; visibility: visible !important; height: auto !important; position: relative !important; z-index: 1 !important; }
  .footer .container, footer .container, .footer-section .container { display: none !important; }
  .top-list { display: none !important; }

  /* RESPONSIVE */
  @media (max-width: 1000px) {
    .booking-grid { grid-template-columns: 1fr 320px; gap: 24px; }
  }

  @media (max-width: 850px) {
    .booking-grid { grid-template-columns: 1fr; }
    .booking-grid > aside { position: static; }
    .booking-modern { padding: 24px 16px; }
    .booking-header h1 { font-size: 26px; }
    .field-row-3 { grid-template-columns: 1fr 1fr; }
  }

  @media (max-width: 640px) {
    .field-row-2, .field-row-3 { grid-template-columns: 1fr; }
    .form-header-modern { padding: 16px 16px 12px 16px; }
    .form-body-modern { padding: 14px 14px 20px 14px; }
    .btn-payment { padding: 13px 18px; font-size: 14px; }
    .sb-fare-amount { font-size: 22px; }
    .ps-label { font-size: 9px; }
    .ps-circle { width: 30px; height: 30px; font-size: 12px; }
    .progress-bar-wrap { padding: 20px 16px 0; }
  }

  /* FOOTER */
  .custom-footer {
    background: linear-gradient(135deg, #0c142e 0%, #1a2340 100%);
    padding: 50px 24px 30px;
    margin-top: 60px;
    border-top: 1px solid rgba(255,255,255,0.1);
  }
  .custom-footer-container { max-width: 1300px; margin: 0 auto; }
  .custom-footer-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 40px; align-items: center; }
  .custom-footer-logo { text-align: left; }
  .custom-footer-logo img { height: 55px; width: auto; }
  .custom-footer-social { text-align: center; }
  .custom-footer-social h4 { color: white; font-size: 16px; font-weight: 700; margin-bottom: 20px; letter-spacing: 1px; }
  .social-icons-wrapper { display: flex; justify-content: center; gap: 15px; flex-wrap: wrap; }
  .social-icons-wrapper a { width: 42px; height: 42px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; transition: all 0.3s ease; color: white; text-decoration: none; }
  .social-icons-wrapper a:hover { background: #008B9E; transform: translateY(-3px); }
  .social-icons-wrapper a i { font-size: 20px; }
  .custom-footer-contact { text-align: right; }
  .custom-footer-contact h4 { color: white; font-size: 16px; font-weight: 700; margin-bottom: 20px; letter-spacing: 1px; }
  .contact-item { display: flex; align-items: center; justify-content: flex-end; gap: 12px; margin-bottom: 15px; color: rgba(255,255,255,0.7); font-size: 14px; text-decoration: none; transition: color 0.3s; }
  .contact-item:hover { color: #008B9E; }
  .contact-item i { width: 20px; color: #008B9E; font-size: 16px; }
  .custom-footer-copyright { text-align: center; padding-top: 40px; margin-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); color: rgba(255,255,255,0.5); font-size: 12px; }

  @media (max-width: 850px) {
    .custom-footer-grid { grid-template-columns: 1fr; gap: 30px; text-align: center; }
    .custom-footer-logo { text-align: center; }
    .custom-footer-contact { text-align: center; }
    .contact-item { justify-content: center; }
  }
  @media (max-width: 480px) {
    .custom-footer-logo img { height: 45px; }
    .social-icons-wrapper a { width: 38px; height: 38px; }
    .social-icons-wrapper a i { font-size: 18px; }
  }

  .footer-section .social-icon, footer .social-icon { display: flex !important; justify-content: center !important; gap: 20px !important; margin-top: 20px !important; margin-bottom: 20px !important; }
  .footer-section .social-icon a, footer .social-icon a { width: 42px !important; height: 42px !important; background: #008B9E !important; border-radius: 50% !important; display: flex !important; align-items: center !important; justify-content: center !important; transition: all 0.3s !important; }
  .footer-section .social-icon a:hover, footer .social-icon a:hover { background: #006d7c !important; transform: translateY(-3px) !important; }
  .footer-section .social-icon i, footer .social-icon i { font-size: 20px !important; color: white !important; }

.header-top-wrapper .contact-list {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-right: 120px;
}
.header-top-wrapper .header-top-right{
       margin-left: 350px;
}
.field-row-2 {
    display: flex;
    gap: 20px;
    width: 100%;
}

.field-row-2 .input-modern {
    flex: 1;
    width: 100%;
}

.phone-input-wrapper {
    display: flex !important;
    flex-direction: row !important;
    align-items: center !important;
    background: #fff;
    border: 1px solid #e0e6ed;
    border-radius: 12px;
    width: 100%;
}

.phone-code-select {
    width: 95px !important;
    min-width: 95px !important;
    max-width: 95px !important;
    background: #f8fafc;
    border: none !important;
    padding: 12px 6px 12px 12px !important;
    font-family: inherit;
    font-size: 0.9rem;
    font-weight: 500;
    color: #1e293b;
    cursor: pointer;
    outline: none;
    border-right: 1px solid #e0e6ed !important;
    border-radius: 12px 0 0 12px !important;
    margin: 0 !important;
    box-sizing: border-box !important;
}

.phone-input-wrapper input {
    flex: 1 !important;
    border: none !important;
    background: transparent !important;
    padding: 12px 12px 12px 8px !important;
    font-size: 0.95rem;
    outline: none;
    border-radius: 0 12px 12px 0 !important;
    min-width: 0;
    box-sizing: border-box !important;
}

.input-modern input:not(.phone-input-wrapper input) {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #e0e6ed;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s;
    box-sizing: border-box;
}

.phone-prefix {
    display: none !important;
}

* {
    box-sizing: border-box;
}


</style>

<!-- PROGRESS BAR -->
<div class="progress-bar-wrap">
  <div class="progress-steps">
    <div class="ps-item done">
      <div class="ps-circle"><i class="fas fa-check"></i></div>
      <div class="ps-label">Locations</div>
    </div>
    <div class="ps-connector done"></div>
    <div class="ps-item done">
      <div class="ps-circle"><i class="fas fa-check"></i></div>
      <div class="ps-label">Vehicle</div>
    </div>
    <div class="ps-connector done"></div>
    <div class="ps-item active">
      <div class="ps-circle">3</div>
      <div class="ps-label">Details</div>
    </div>
    <div class="ps-connector"></div>
    <div class="ps-item">
      <div class="ps-circle">4</div>
      <div class="ps-label">Complete</div>
    </div>
  </div>
</div>

<!-- MAIN BOOKING SECTION -->
<div class="booking-modern">
  <div class="booking-header">
    <h1>Complete Your Booking</h1>
    <p>Review your journey and provide passenger details</p>
  </div>

  <div class="booking-grid">

    <!-- LEFT: Form — UNCHANGED -->
    <div>
      <div class="form-modern">
        <div class="form-header-modern">
          <h2>Passenger Information</h2>
          <p>We need a few details to confirm your booking</p>
        </div>
        <div class="form-body-modern">
          <form id="passengerForm">
            <input type="hidden" name="payment_type" id="paymentType" value="cash">

            <div class="field-group">
              <div class="input-modern">
                <label><i class="fas fa-user"></i> Passenger Name</label>
                <input type="text" name="passenger_name" placeholder="Passenger Name" required>
              </div>
            </div>

            <div class="field-row-2">
                <div class="input-modern">
                    <label><i class="fas fa-envelope"></i> Contact Email</label>
                    <input type="email" name="email" placeholder="Contact Email" required>
                </div>
                <div class="input-modern">
                    <label><i class="fas fa-phone"></i> Mobile Number</label>
                    <div class="phone-input-wrapper">
                        <select name="phone_code" class="phone-code-select" required>
                            <option value="93">🇦🇫 +93</option>
                            <option value="355">🇦🇱 +355</option>
                            <option value="213">🇩🇿 +213</option>
                            <option value="376">🇦🇩 +376</option>
                            <option value="244">🇦🇴 +244</option>
                            <option value="54">🇦🇷 +54</option>
                            <option value="374">🇦🇲 +374</option>
                            <option value="61">🇦🇺 +61</option>
                            <option value="43">🇦🇹 +43</option>
                            <option value="994">🇦🇿 +994</option>
                            <option value="973">🇧🇭 +973</option>
                            <option value="880">🇧🇩 +880</option>
                            <option value="375">🇧🇾 +375</option>
                            <option value="32">🇧🇪 +32</option>
                            <option value="501">🇧🇿 +501</option>
                            <option value="229">🇧🇯 +229</option>
                            <option value="975">🇧🇹 +975</option>
                            <option value="591">🇧🇴 +591</option>
                            <option value="387">🇧🇦 +387</option>
                            <option value="267">🇧🇼 +267</option>
                            <option value="55">🇧🇷 +55</option>
                            <option value="673">🇧🇳 +673</option>
                            <option value="359">🇧🇬 +359</option>
                            <option value="226">🇧🇫 +226</option>
                            <option value="257">🇧🇮 +257</option>
                            <option value="855">🇰🇭 +855</option>
                            <option value="237">🇨🇲 +237</option>
                            <option value="1">🇨🇦 +1</option>
                            <option value="238">🇨🇻 +238</option>
                            <option value="236">🇨🇫 +236</option>
                            <option value="235">🇹🇩 +235</option>
                            <option value="56">🇨🇱 +56</option>
                            <option value="86">🇨🇳 +86</option>
                            <option value="57">🇨🇴 +57</option>
                            <option value="269">🇰🇲 +269</option>
                            <option value="242">🇨🇬 +242</option>
                            <option value="506">🇨🇷 +506</option>
                            <option value="385">🇭🇷 +385</option>
                            <option value="53">🇨🇺 +53</option>
                            <option value="357">🇨🇾 +357</option>
                            <option value="420">🇨🇿 +420</option>
                            <option value="45">🇩🇰 +45</option>
                            <option value="253">🇩🇯 +253</option>
                            <option value="1">🇩🇲 +1</option>
                            <option value="1">🇩🇴 +1</option>
                            <option value="593">🇪🇨 +593</option>
                            <option value="20">🇪🇬 +20</option>
                            <option value="503">🇸🇻 +503</option>
                            <option value="240">🇬🇶 +240</option>
                            <option value="291">🇪🇷 +291</option>
                            <option value="372">🇪🇪 +372</option>
                            <option value="251">🇪🇹 +251</option>
                            <option value="679">🇫🇯 +679</option>
                            <option value="358">🇫🇮 +358</option>
                            <option value="33">🇫🇷 +33</option>
                            <option value="241">🇬🇦 +241</option>
                            <option value="220">🇬🇲 +220</option>
                            <option value="995">🇬🇪 +995</option>
                            <option value="49">🇩🇪 +49</option>
                            <option value="233">🇬🇭 +233</option>
                            <option value="30">🇬🇷 +30</option>
                            <option value="502">🇬🇹 +502</option>
                            <option value="224">🇬🇳 +224</option>
                            <option value="245">🇬🇼 +245</option>
                            <option value="592">🇬🇾 +592</option>
                            <option value="509">🇭🇹 +509</option>
                            <option value="504">🇭🇳 +504</option>
                            <option value="36">🇭🇺 +36</option>
                            <option value="354">🇮🇸 +354</option>
                            <option value="91">🇮🇳 +91</option>
                            <option value="62">🇮🇩 +62</option>
                            <option value="98">🇮🇷 +98</option>
                            <option value="964">🇮🇶 +964</option>
                            <option value="353">🇮🇪 +353</option>
                            <option value="972">🇮🇱 +972</option>
                            <option value="39">🇮🇹 +39</option>
                            <option value="1">🇯🇲 +1</option>
                            <option value="81">🇯🇵 +81</option>
                            <option value="962">🇯🇴 +962</option>
                            <option value="7">🇰🇿 +7</option>
                            <option value="254">🇰🇪 +254</option>
                            <option value="686">🇰🇮 +686</option>
                            <option value="383">🇽🇰 +383</option>
                            <option value="965">🇰🇼 +965</option>
                            <option value="996">🇰🇬 +996</option>
                            <option value="856">🇱🇦 +856</option>
                            <option value="371">🇱🇻 +371</option>
                            <option value="961">🇱🇧 +961</option>
                            <option value="266">🇱🇸 +266</option>
                            <option value="231">🇱🇷 +231</option>
                            <option value="218">🇱🇾 +218</option>
                            <option value="423">🇱🇮 +423</option>
                            <option value="370">🇱🇹 +370</option>
                            <option value="352">🇱🇺 +352</option>
                            <option value="261">🇲🇬 +261</option>
                            <option value="265">🇲🇼 +265</option>
                            <option value="60">🇲🇾 +60</option>
                            <option value="960">🇲🇻 +960</option>
                            <option value="223">🇲🇱 +223</option>
                            <option value="356">🇲🇹 +356</option>
                            <option value="692">🇲🇭 +692</option>
                            <option value="222">🇲🇷 +222</option>
                            <option value="230">🇲🇺 +230</option>
                            <option value="52">🇲🇽 +52</option>
                            <option value="691">🇫🇲 +691</option>
                            <option value="373">🇲🇩 +373</option>
                            <option value="377">🇲🇨 +377</option>
                            <option value="976">🇲🇳 +976</option>
                            <option value="382">🇲🇪 +382</option>
                            <option value="212">🇲🇦 +212</option>
                            <option value="258">🇲🇿 +258</option>
                            <option value="95">🇲🇲 +95</option>
                            <option value="264">🇳🇦 +264</option>
                            <option value="674">🇳🇷 +674</option>
                            <option value="977">🇳🇵 +977</option>
                            <option value="31">🇳🇱 +31</option>
                            <option value="64">🇳🇿 +64</option>
                            <option value="505">🇳🇮 +505</option>
                            <option value="227">🇳🇪 +227</option>
                            <option value="234">🇳🇬 +234</option>
                            <option value="850">🇰🇵 +850</option>
                            <option value="389">🇲🇰 +389</option>
                            <option value="47">🇳🇴 +47</option>
                            <option value="968">🇴🇲 +968</option>
                            <option value="92" selected>🇵🇰 +92</option>
                            <option value="680">🇵🇼 +680</option>
                            <option value="970">🇵🇸 +970</option>
                            <option value="507">🇵🇦 +507</option>
                            <option value="675">🇵🇬 +675</option>
                            <option value="595">🇵🇾 +595</option>
                            <option value="51">🇵🇪 +51</option>
                            <option value="63">🇵🇭 +63</option>
                            <option value="48">🇵🇱 +48</option>
                            <option value="351">🇵🇹 +351</option>
                            <option value="974">🇶🇦 +974</option>
                            <option value="40">🇷🇴 +40</option>
                            <option value="7">🇷🇺 +7</option>
                            <option value="250">🇷🇼 +250</option>
                            <option value="1">🇰🇳 +1</option>
                            <option value="1">🇱🇨 +1</option>
                            <option value="1">🇻🇨 +1</option>
                            <option value="685">🇼🇸 +685</option>
                            <option value="378">🇸🇲 +378</option>
                            <option value="239">🇸🇹 +239</option>
                            <option value="966">🇸🇦 +966</option>
                            <option value="221">🇸🇳 +221</option>
                            <option value="381">🇷🇸 +381</option>
                            <option value="248">🇸🇨 +248</option>
                            <option value="232">🇸🇱 +232</option>
                            <option value="65">🇸🇬 +65</option>
                            <option value="421">🇸🇰 +421</option>
                            <option value="386">🇸🇮 +386</option>
                            <option value="677">🇸🇧 +677</option>
                            <option value="252">🇸🇴 +252</option>
                            <option value="27">🇿🇦 +27</option>
                            <option value="82">🇰🇷 +82</option>
                            <option value="211">🇸🇸 +211</option>
                            <option value="34">🇪🇸 +34</option>
                            <option value="94">🇱🇰 +94</option>
                            <option value="249">🇸🇩 +249</option>
                            <option value="597">🇸🇷 +597</option>
                            <option value="46">🇸🇪 +46</option>
                            <option value="41">🇨🇭 +41</option>
                            <option value="963">🇸🇾 +963</option>
                            <option value="886">🇹🇼 +886</option>
                            <option value="992">🇹🇯 +992</option>
                            <option value="255">🇹🇿 +255</option>
                            <option value="66">🇹🇭 +66</option>
                            <option value="670">🇹🇱 +670</option>
                            <option value="228">🇹🇬 +228</option>
                            <option value="676">🇹🇴 +676</option>
                            <option value="1">🇹🇹 +1</option>
                            <option value="216">🇹🇳 +216</option>
                            <option value="90">🇹🇷 +90</option>
                            <option value="993">🇹🇲 +993</option>
                            <option value="688">🇹🇻 +688</option>
                            <option value="256">🇺🇬 +256</option>
                            <option value="380">🇺🇦 +380</option>
                            <option value="971">🇦🇪 +971</option>
                            <option value="44">🇬🇧 +44</option>
                            <option value="1">🇺🇸 +1</option>
                            <option value="598">🇺🇾 +598</option>
                            <option value="998">🇺🇿 +998</option>
                            <option value="678">🇻🇺 +678</option>
                            <option value="379">🇻🇦 +379</option>
                            <option value="58">🇻🇪 +58</option>
                            <option value="84">🇻🇳 +84</option>
                            <option value="967">🇾🇪 +967</option>
                            <option value="260">🇿🇲 +260</option>
                            <option value="263">🇿🇼 +263</option>
                        </select>
                        <input type="tel" name="phone" placeholder="0348 9921931" required>
                    </div>
                </div>
            </div>

            <div class="field-row-3">
              <div class="input-modern">
                <label><i class="fas fa-users"></i> Passengers</label>
                <select name="passengers" required>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5</option>
                  <option value="6">6</option>
                </select>
              </div>
              <div class="input-modern">
                <label><i class="fas fa-suitcase"></i> Suitcases</label>
                <select name="suitcases" required>
                  <option value="none">None</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                  <option value="4">4</option>
                  <option value="5">5+</option>
                </select>
              </div>
              <div class="input-modern" style="justify-content: flex-end; padding-bottom: 2px;">
                <label style="margin-bottom: 6px;"><i class="fas fa-baby"></i> Child Seat Age</label>
                <select name="child_seat" id="childSeat" required>
                  <option value="none">No seat</option>
                  <option value="0-1">0 to 1 Years</option>
                  <option value="1-3">1 to 3 Years</option>
                  <option value="4-7">4 to 7 Years</option>
                  <option value="8-12">8 to 12 Years</option>
                </select>
              </div>
            </div>

            <div class="field-row-2">
              <div class="input-modern">
                <label><i class="fas fa-calendar-alt"></i> Pick-up Date</label>
                <input type="date" name="pickup_date" id="pickupDate" required>
              </div>
              <div class="input-modern">
                <label><i class="fas fa-clock"></i> Pick-up Time</label>
                <select name="pickup_time" required>
                  <option value="">Select Pickup Time</option>
                </select>
              </div>
            </div>

              <div class="field-row-2 airport-service-field">
                <div class="input-modern">
                  <label><i class="fas fa-plane"></i> Flight Number</label>
                  <input type="text" name="flight_number" placeholder="Flight Number">
                </div>
                <div class="input-modern">
                  <label><i class="fas fa-clock"></i> Flight Landing Time</label>
                  <select name="flight_landing_time">
                    <option value="">Select Landing Time</option>
                  </select>
                </div>
              </div>

              <!-- Meet and Greet -->
              <div class="airport-service-field">
                <label class="checkbox-row" for="meetGreet">
                  <input type="checkbox" name="meet_and_greet" id="meetGreet" value="1">
                  <span class="checkbox-row-label">
                    Meet &amp; Greet Service £20 Extra
                  </span>
                </label>
              </div>

            <label class="checkbox-row" for="returnCheckbox">
              <input type="checkbox" id="returnCheckbox" name="has_return" value="1">
              <span class="checkbox-row-label">
                <i class="fas fa-map-marker-alt pin-icon"></i>
                Book smart! Add a return journey
              </span>
            </label>

            <div class="return-modern" id="returnSection">
              <div class="return-header">
                <span class="return-badge-modern"><i class="fas fa-exchange-alt"></i> Return Journey Details</span>
              </div>

              <div class="field-row-2">
                <div class="input-modern">
                  <label><i class="fas fa-calendar-alt"></i> Return Pickup Date</label>
                  <input type="date" name="return_pickup_date" id="returnDate">
                </div>
                <div class="input-modern">
                  <label><i class="fas fa-clock"></i> Return Pickup Time</label>
                  <select name="return_pickup_time">
                    <option value="">Select Return Time</option>
                  </select>
                </div>
              </div>

              <div class="field-row-2 airport-service-field" style="margin-bottom:0;">
                <div class="input-modern">
                  <label><i class="fas fa-plane"></i> Flight Number</label>
                  <input type="text" name="return_flight_number" placeholder="Flight Number">
                </div>
                <div class="input-modern">
                  <label><i class="fas fa-clock"></i> Flight Landing Time</label>
                  <select name="return_flight_landing_time">
                    <option value="">Select Landing Time</option>
                  </select>
                </div>
              </div>
              <br>
              <div class="airport-service-field">
                <label class="checkbox-row" for="meetGreetReturn">
                  <input type="checkbox" name="meet_and_greet_return" id="meetGreetReturn" value="1">
                  <span class="checkbox-row-label">
                    Meet &amp; Greet Service £20 Extra
                  </span>
                </label>
              </div>
            </div>

            <div class="field-group">
              <div class="input-modern">
                <label><i class="fas fa-comment-dots"></i> Instructions for Driver (optional)</label>
                <textarea name="message_to_driver" rows="2" placeholder="Instructions for Driver (optional)"></textarea>
              </div>
            </div>

            <div class="terms-modern">
              By Clicking <strong>Book Now</strong> I confirm that I have read and agree to the
              <a href="#">privacy policy</a> &amp; <a href="#">terms of booking</a>.
            </div>

            <div class="payment-buttons">
              <button type="button" class="btn-payment btn-card-modern" id="cardBtn">
                <i class="fas fa-credit-card"></i> Book Now (Card)
              </button>
              <button type="button" class="btn-payment btn-cash-modern" id="cashBtn">
                <i class="fas fa-money-bill-wave"></i> Book Now (Cash)
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>

    <!-- RIGHT: Summary — REDESIGNED layout -->
    <aside>
      <div class="summary-modern">

        <!-- Dark header bar -->
        <div class="summary-head">
          <div class="summary-icon-wrapper">
            <i class="fas fa-map-marked-alt"></i>
          </div>
          <div class="summary-head-text">
            <h3>Trip Summary</h3>
            <p>Booking summary</p>
          </div>
        </div>

        <!-- Card body -->
        <div class="summary-card-body" id="bookingSummary">
          <div style="text-align: center; padding: 20px;">
            <i class="fas fa-spinner fa-spin" style="color: var(--primary);"></i>
            <p style="margin-top: 10px; color: var(--gray-500);">Loading journey data...</p>
          </div>
        </div>

      </div>

      <!-- SECOND CARD: miles + minutes + map — separate card below -->
      <div class="route-card" id="routeCard" style="display:none;">
        <!-- content injected by JS -->
      </div>

      <a href="quote-results" class="back-btn">
        <i class="fas fa-arrow-left"></i> Back to Vehicles
      </a>
    </aside>

  </div>
</div>

<script>
  var bookingSubmitUrl = <?php echo json_encode(route('booking.submit'), 15, 512) ?>;
  var stripePublishableKey = <?php echo json_encode($stripePublishableKey, 15, 512) ?>;
  var GOOGLE_MAPS_API_KEY = <?php echo json_encode(env('GOOGLE_MAPS_API_KEY'), 15, 512) ?>;
  var stripeJsLoader = null;

  var bookingData = {};
  try {
    bookingData = JSON.parse(localStorage.getItem('booking_data') || '{}');
  } catch(e) {}
  
  
  
  
  logBookingAirportCharges(bookingData);

  function logBookingAirportCharges(data) {
    var pickup = (data.pickup || '').toLowerCase();
    var dropoff = (data.dropoff || '').toLowerCase();
    var charge = Number(data.airport_charges || 0);
    if (charge > 0) {
      console.log('Booking confirmation airport charges: £' + charge);
      if (pickup.includes('airport') && dropoff.includes('airport')) {
        console.log('Airport charges applied on pickup and dropoff.');
      } else if (pickup.includes('airport')) {
        console.log('Airport charges applied on pickup.');
      } else if (dropoff.includes('airport')) {
        console.log('Airport charges applied on dropoff.');
      } else {
        console.log('Airport charges exist, but pickup/dropoff do not include airport text.');
        console.log('Pickup:', data.pickup, 'Dropoff:', data.dropoff);
      }
    } else {
      console.log('Booking confirmation: no airport charges. Pickup:', data.pickup, 'Dropoff:', data.dropoff);
    }
  }
  
  

  var VAT_RATE = 0.20;
  var MEET_AND_GREET_FEE = 20.00;

  if (bookingData.pickup_date) {
    document.getElementById('pickupDate').value = bookingData.pickup_date;
  }

  var distanceMiles = bookingData.distance_miles || null;
  var durationMins  = bookingData.duration_mins  || null;

  function getVehicleCapacity() {
    var type = (bookingData.vehicle_type || '').toLowerCase();
    if (type === 'saloon' || type === 'business') {
      return { passengers: 4, suitcases: 2 };
    }
    if (type === 'mpv6') {
      return { passengers: 5, suitcases: 3 };
    }
    if (type === 'mpv8') {
      return { passengers: 8, suitcases: 8 };
    }
    return { passengers: 8, suitcases: 8 };
  }

  function normalizeSuitcaseValue(value) {
    return value === 'none' ? 0 : parseInt(value, 10) || 0;
  }

  function updateCapacityOptions() {
    var capacity = getVehicleCapacity();
    var passengerSelect = document.querySelector('[name="passengers"]');
    if (passengerSelect) {
      var currentValue = passengerSelect.value || '1';
      passengerSelect.innerHTML = '';
      for (var p = 1; p <= capacity.passengers; p++) {
        passengerSelect.innerHTML += '<option value="' + p + '">' + p + '</option>';
      }
      if (parseInt(currentValue, 10) > capacity.passengers) {
        passengerSelect.value = String(capacity.passengers);
      } else {
        passengerSelect.value = currentValue;
      }
    }

    var suitcaseSelect = document.querySelector('[name="suitcases"]');
    if (suitcaseSelect) {
      var currentSuitcase = suitcaseSelect.value || 'none';
      suitcaseSelect.innerHTML = '<option value="none">None</option>';
      for (var s = 1; s <= capacity.suitcases; s++) {
        suitcaseSelect.innerHTML += '<option value="' + s + '">' + s + '</option>';
      }
      if (currentSuitcase === 'none' || normalizeSuitcaseValue(currentSuitcase) <= capacity.suitcases) {
        suitcaseSelect.value = currentSuitcase;
      } else {
        suitcaseSelect.value = String(capacity.suitcases);
      }
    }
  }

  var returnCheckbox = document.getElementById('returnCheckbox');
  var returnSection  = document.getElementById('returnSection');

  function isAirportAddress(address) {
    return address && /airport/i.test(address);
  }

  function updateAirportServiceFields() {
    var hasAirportCharges = bookingData && bookingData.airport_charges && parseFloat(bookingData.airport_charges) > 0;
    var pickupAirport = isAirportAddress(bookingData.pickup);
    var dropoffAirport = isAirportAddress(bookingData.dropoff);
    var showMainFields = hasAirportCharges && pickupAirport;
    var showReturnFields = hasAirportCharges && returnCheckbox && returnCheckbox.checked && dropoffAirport;
    var airportFields = document.querySelectorAll('.airport-service-field');
    if (!airportFields) return;
    airportFields.forEach(function(el) {
      var isReturnField = el.closest('#returnSection') !== null;
      el.style.display = (isReturnField ? showReturnFields : showMainFields) ? 'block' : 'none';
    });
  }

  updateCapacityOptions();

  if (!distanceMiles || !durationMins) {
    var pickup  = (bookingData.pickup  || '').toLowerCase();
    var dropoff = (bookingData.dropoff || '').toLowerCase();
    if (pickup.includes('ashford') && dropoff.includes('luton')) {
      distanceMiles = distanceMiles || 99.1;
      durationMins  = durationMins  || 108;
    }
    if (!distanceMiles && bookingData.route_info && bookingData.route_info.distance) {
      distanceMiles = bookingData.route_info.distance;
      durationMins  = bookingData.route_info.duration;
    }
  }

  function setReturnVisible(show) {
    if (show) {
      returnSection.classList.add('visible');
      if (bookingData.pickup_date && !document.getElementById('returnDate').value) {
        document.getElementById('returnDate').value = bookingData.pickup_date;
      }
    } else {
      returnSection.classList.remove('visible');
    }
  }

  if (bookingData.trip_type === 'return') {
    returnCheckbox.checked = true;
    setReturnVisible(true);
  }

  updateAirportServiceFields();

  returnCheckbox.addEventListener('change', function() {
    setReturnVisible(this.checked);
    updateAirportServiceFields();
    renderSummary();
  });

  /* Populate all time selects */
  function populateTimes(selectEl) {
    if (!selectEl) return;
    selectEl.innerHTML = '<option value="">Select Time</option>';
    for (var h = 0; h < 24; h++) {
      for (var m = 0; m < 60; m += 30) {
        var hour   = h < 10 ? '0' + h : '' + h;
        var minute = m === 0 ? '00' : '30';
        var time   = hour + ':' + minute;
        var option = document.createElement('option');
        option.value       = time;
        option.textContent = time;
        selectEl.appendChild(option);
      }
    }
  }

  var timeSelects = document.querySelectorAll(
    'select[name="pickup_time"], select[name="flight_landing_time"], select[name="return_pickup_time"], select[name="return_flight_landing_time"]'
  );
  timeSelects.forEach(populateTimes);

  /* Format duration */
  function formatDuration(minutes) {
    if (!minutes) return '--';
    if (minutes < 60) return minutes + ' mins';
    var hours = Math.floor(minutes / 60);
    var mins  = minutes % 60;
    return hours + ' hr ' + (mins > 0 ? mins + ' mins' : '');
  }

  /* Format date nicely */
  function formatDate(dateStr) {
    if (!dateStr) return '';
    var parts = dateStr.split('-');
    if (parts.length === 3) {
      var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
      var d = parseInt(parts[2], 10);
      var m = parseInt(parts[1], 10) - 1;
      var y = parts[0];
      if (!isNaN(d) && !isNaN(m)) return d + '-' + months[m] + '-' + y;
    }
    return dateStr;
  }

  /* Escape HTML */
  function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/[&<>]/g, function(c) {
      return c === '&' ? '&amp;' : c === '<' ? '&lt;' : c === '&gt;';
    });
  }

  function getMeetGreetFee() {
    var total = 0;
    var meetEl = document.querySelector('[name="meet_and_greet"]');
    var meetReturnEl = document.querySelector('[name="meet_and_greet_return"]');
    if (meetEl && meetEl.checked) total += MEET_AND_GREET_FEE;
    if (meetReturnEl && meetReturnEl.checked) total += MEET_AND_GREET_FEE;
    return total;
  }

  function getBaseFare() {
    var price = parseFloat(bookingData.price) || 0;
    if (bookingData.trip_type === 'return') {
      return price / 2;
    }
    return price;
  }

  function getReturnFee() {
    if (!returnCheckbox || !returnCheckbox.checked) {
      return 0;
    }
    return getBaseFare();
  }

  function calculatePricingSummary(baseFare, returnFee, meetFee) {
    var totalFare = parseFloat((baseFare + returnFee + meetFee).toFixed(2));
    var vatAmount = parseFloat((totalFare * VAT_RATE).toFixed(2));
    var total = parseFloat((totalFare + vatAmount).toFixed(2));
    return { baseFare: baseFare, returnFee: returnFee, totalFare: totalFare, vatAmount: vatAmount, meetFee: meetFee, total: total };
  }

  function loadStripeJs() {
    if (window.Stripe) {
      return Promise.resolve(window.Stripe);
    }

    if (stripeJsLoader) {
      return stripeJsLoader;
    }

    stripeJsLoader = new Promise(function(resolve, reject) {
      var script = document.createElement('script');
      script.src = 'https://js.stripe.com/v3/';
      script.async = true;
      script.onload = function() {
        if (window.Stripe) {
          resolve(window.Stripe);
        } else {
          reject(new Error('Stripe.js failed to initialize.'));
        }
      };
      script.onerror = function() {
        reject(new Error('Unable to load Stripe.js.'));
      };
      document.head.appendChild(script);
    });

    return stripeJsLoader;
  }

  function redirectToStripeCheckout(sessionId, fallbackUrl) {
    if (!sessionId || !stripePublishableKey) {
      if (fallbackUrl) {
        window.location.href = fallbackUrl;
      } else {
        alert('Card payment could not be started. Please try again.');
      }
      return;
    }

    loadStripeJs()
      .then(function(Stripe) {
        var stripe = Stripe(stripePublishableKey);
        return stripe.redirectToCheckout({ sessionId: sessionId });
      })
      .then(function(result) {
        if (result && result.error) {
          if (fallbackUrl) {
            window.location.href = fallbackUrl;
            return;
          }

          alert(result.error.message || 'Unable to redirect to card checkout.');
        }
      })
      .catch(function(err) {
        if (fallbackUrl) {
          window.location.href = fallbackUrl;
          return;
        }

        alert('Unable to open Stripe checkout: ' + err.message);
      });
  }

  /* Leaflet map instance */
  var leafletMap = null;

  /* Tab state */
  var activeTab = 'map';

  /* Render the right-side summary */
  function renderSummary() {
    var container = document.getElementById('bookingSummary');
    if (!container) return;

    var hasData = bookingData && (bookingData.pickup || bookingData.dropoff);
    if (!hasData) {
      container.innerHTML = '<div style="text-align:center;padding:20px;"><i class="fas fa-info-circle" style="color:var(--gray-400);"></i><p style="margin-top:10px;color:var(--gray-500);">No journey data available</p></div>';
      return;
    }

    var finalDistance = bookingData.distance_miles || distanceMiles;
    var finalDuration = bookingData.duration_mins  || durationMins;
    var pickupDateFmt = formatDate(bookingData.pickup_date);

    /* Build vehicle meta string */
    var vehicleMeta = '';
    if (bookingData.max_passengers) vehicleMeta += 'Max ' + bookingData.max_passengers + ' passengers';
    if (bookingData.max_suitcases)  vehicleMeta += (vehicleMeta ? ' • ' : '') + bookingData.max_suitcases + ' suitcases';

    var html = '';

    /* ── 1. Pickup / Dropoff locations ── */
    if (bookingData.pickup || bookingData.dropoff) {
      if (bookingData.pickup) {
        html += '<div class="sb-location-row">'
          + '<div class="sb-location-dot pickup"><i class="fas fa-map-marker-alt"></i></div>'
          + '<div><div class="sb-location-label">From</div>'
          + '<div class="sb-location-text">' + escapeHtml(bookingData.pickup) + '</div></div>'
          + '</div>';
      }
      if (bookingData.pickup && bookingData.dropoff) {
        html += '<div class="sb-loc-divider"><div class="sb-loc-divider-line"></div></div>';
      }
      if (bookingData.dropoff) {
        html += '<div class="sb-location-row">'
          + '<div class="sb-location-dot dropoff"><i class="fas fa-flag-checkered"></i></div>'
          + '<div><div class="sb-location-label">To</div>'
          + '<div class="sb-location-text">' + escapeHtml(bookingData.dropoff) + '</div></div>'
          + '</div>';
      }
    }

    /* ── 2. Vehicle row (FontAwesome car icon — NO SVG) ── */
    if (bookingData.vehicle_type) {
      var vehicleDisplay = bookingData.vehicle_name || bookingData.vehicle_type;
      html += '<div class="sb-vehicle-row">'
        + '<div class="sb-vehicle-icon"><i class="fas fa-car"></i></div>'
        + '<div>'
        + '<div class="sb-vehicle-name">' + escapeHtml(vehicleDisplay) + '</div>'
        + (vehicleMeta ? '<div class="sb-vehicle-meta">' + escapeHtml(vehicleMeta) + '</div>' : '')
        + '</div></div>';
    }

    /* ── 3. Date / Time chips ── */
    if (pickupDateFmt || bookingData.pickup_time) {
      html += '<div class="sb-datetime-row">';
      if (pickupDateFmt) {
        html += '<div class="sb-datetime-chip">'
          + '<i class="fas fa-calendar-alt"></i>'
          + '<span class="sb-datetime-val">' + escapeHtml(pickupDateFmt) + '</span>'
          + '</div>';
      }
      if (bookingData.pickup_time) {
        html += '<div class="sb-datetime-chip">'
          + '<i class="fas fa-clock"></i>'
          + '<span class="sb-datetime-val">' + escapeHtml(bookingData.pickup_time) + '</span>'
          + '</div>';
      }
      html += '</div>';
    }

    /* ── 4. Total Fare, VAT and Total ── */
    var baseFare = getBaseFare();
    var returnFee = getReturnFee();
    var meetFee = getMeetGreetFee();

    html += '<div id="sbFareSummary"></div>';

    container.innerHTML = html;

    var calc = calculatePricingSummary(baseFare, returnFee, meetFee);
    var summaryHtml = '';
    summaryHtml += '<div class="sb-fare-summary">'
      + '<div class="sb-fare-row">'
      + '<span class="sb-fare-label">Service Fare</span>'
      + '<span class="sb-fare-amount">£ ' + calc.totalFare.toFixed(2) + '</span>'
      + '</div>'
      + '<div class="sb-vat-row">'
      + '<span class="sb-vat-label">VAT (' + (VAT_RATE * 100).toFixed(1) + '%)</span>'
      + '<span class="sb-vat-amount">£ ' + calc.vatAmount.toFixed(2) + '</span>'
      + '</div>'
      + '<div class="sb-total-row">'
      + '<span class="sb-total-label">GRAND TOTAL AMOUNT</span>'
      + '<span class="sb-total-amount">£ ' + calc.total.toFixed(2) + '</span>'
      + '</div>'
      + '</div>';

    var priceContainer = document.getElementById('sbFareSummary');
    if (priceContainer) {
      priceContainer.innerHTML = summaryHtml;
    }

    /* ── Card 2: miles + minutes + map (separate card) ── */
    var routeCard = document.getElementById('routeCard');
    if (!routeCard) return;

    var html2 = '';

    /* Miles & minutes — stacked list style like reference image */
    if (finalDistance || finalDuration) {
      if (finalDistance) {
        html2 += '<div class="rc-stat-row">'
          + '<i class="fas fa-book-open"></i>'
          + '<span class="rc-stat-text">' + finalDistance + ' miles</span>'
          + '</div>';
      }
      if (finalDuration) {
        html2 += '<div class="rc-stat-row">'
          + '<i class="fas fa-clock"></i>'
          + '<span class="rc-stat-text">' + formatDuration(finalDuration) + '</span>'
          + '</div>';
      }
    }

    /* Map tabs */
    html2 += '<div class="sb-map-section">'
      + '<div class="sb-map-tabs">'
      + '<button class="sb-map-tab active" id="tabMap" onclick="switchTab(\'map\')"><i class="fas fa-map"></i> Map</button>'
      + '<button class="sb-map-tab" id="tabSat" onclick="switchTab(\'satellite\')"><i class="fas fa-satellite"></i> Satellite</button>'
      + '</div>'
      + '<div class="sb-map-embed" id="sbMapLeaflet"><div id="routeMap" style="width:100%;height:100%;"></div></div>'
      + '<div class="sb-map-embed sb-map-satellite-frame" id="sbMapSat">'
      + (bookingData.pickup && bookingData.dropoff && GOOGLE_MAPS_API_KEY
          ? '<iframe src="https://www.google.com/maps/embed/v1/directions?key=' + encodeURIComponent(GOOGLE_MAPS_API_KEY) + '&origin=' + encodeURIComponent(bookingData.pickup) + '&destination=' + encodeURIComponent(bookingData.dropoff) + '&mode=driving" frameborder="0" style="width:100%;height:100%;border:none;"></iframe>'
          : (bookingData.pickup && bookingData.dropoff
              ? '<iframe src="https://maps.google.com/maps?q=' + encodeURIComponent(bookingData.pickup + ' to ' + bookingData.dropoff) + '&output=embed&t=k" frameborder="0" style="width:100%;height:100%;border:none;"></iframe>'
              : '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:var(--gray-500);font-size:13px;">No route data</div>'))
      + '</div>'
      + '</div>';

    routeCard.innerHTML = html2;
    routeCard.style.display = 'block';

    /* Init Leaflet map after DOM update */
    setTimeout(initLeafletMap, 120);
  }

  /* Switch between Map/Satellite tabs */
  function switchTab(tab) {
    activeTab = tab;
    var tabMap     = document.getElementById('tabMap');
    var tabSat     = document.getElementById('tabSat');
    var leafletDiv = document.getElementById('sbMapLeaflet');
    var satDiv     = document.getElementById('sbMapSat');
    if (!tabMap || !tabSat) return;
    if (tab === 'map') {
      tabMap.classList.add('active');
      tabSat.classList.remove('active');
      if (leafletDiv) leafletDiv.style.display = 'block';
      if (satDiv)     satDiv.style.display     = 'none';
      /* Refresh leaflet size after showing */
      if (leafletMap) setTimeout(function(){ leafletMap.invalidateSize(); }, 50);
    } else {
      tabSat.classList.add('active');
      tabMap.classList.remove('active');
      if (satDiv)     satDiv.style.display     = 'block';
      if (leafletDiv) leafletDiv.style.display = 'none';
    }
  }

  /* Init Leaflet embedded map */
  function initLeafletMap() {
    var mapEl = document.getElementById('routeMap');
    if (!mapEl || !bookingData.pickup || !bookingData.dropoff) return;

    var geocodeBase = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q=';

    Promise.all([
      fetch(geocodeBase + encodeURIComponent(bookingData.pickup),  { headers: { 'Accept-Language': 'en' } }).then(r => r.json()),
      fetch(geocodeBase + encodeURIComponent(bookingData.dropoff), { headers: { 'Accept-Language': 'en' } }).then(r => r.json())
    ]).then(function(results) {
      var pd = results[0];
      var dd = results[1];
      if (!pd || !pd.length || !dd || !dd.length) return;

      var pLatLng = [parseFloat(pd[0].lat), parseFloat(pd[0].lon)];
      var dLatLng = [parseFloat(dd[0].lat), parseFloat(dd[0].lon)];

      if (leafletMap) { leafletMap.remove(); leafletMap = null; }

      leafletMap = L.map('routeMap', { zoomControl: true, scrollWheelZoom: false });

      L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OSM</a> &copy; CartoDB',
        maxZoom: 19
      }).addTo(leafletMap);

      /* Custom marker icons using FA colours */
      var pickupIcon = L.divIcon({
        html: '<div style="background:#f59e0b;width:12px;height:12px;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.3);"></div>',
        className: '',
        iconSize: [12, 12],
        iconAnchor: [6, 6]
      });
      var dropIcon = L.divIcon({
        html: '<div style="background:#008B9E;width:12px;height:12px;border-radius:50%;border:2px solid #fff;box-shadow:0 1px 4px rgba(0,0,0,0.3);"></div>',
        className: '',
        iconSize: [12, 12],
        iconAnchor: [6, 6]
      });

      L.marker(pLatLng, { icon: pickupIcon }).addTo(leafletMap).bindPopup('<b>Pickup:</b> ' + bookingData.pickup);
      L.marker(dLatLng, { icon: dropIcon  }).addTo(leafletMap).bindPopup('<b>Dropoff:</b> ' + bookingData.dropoff);

      /* Route polyline */
      L.polyline([pLatLng, dLatLng], { color: '#008B9E', weight: 3, opacity: 0.75, dashArray: '6, 4' }).addTo(leafletMap);

      leafletMap.fitBounds(L.latLngBounds([pLatLng, dLatLng]), { padding: [30, 30] });

    }).catch(function(err) { console.warn('Map geocoding error:', err); });
  }

  /* Render on load */
  renderSummary();

  var meetGreetCheckbox = document.querySelector('[name="meet_and_greet"]');
  var meetGreetReturnCheckbox = document.querySelector('[name="meet_and_greet_return"]');
  if (meetGreetCheckbox) {
    meetGreetCheckbox.addEventListener('change', function() {
      renderSummary();
    });
  }
  if (meetGreetReturnCheckbox) {
    meetGreetReturnCheckbox.addEventListener('change', function() {
      renderSummary();
    });
  }

  /* Listen for storage changes */
  window.addEventListener('storage', function(e) {
    if (e.key === 'booking_data') {
      try {
        bookingData = JSON.parse(e.newValue || '{}');
        renderSummary();
        updateCapacityOptions();
        if (bookingData.pickup_date && document.getElementById('pickupDate')) {
          document.getElementById('pickupDate').value = bookingData.pickup_date;
        }
      } catch(err) {}
    }
  });

  /* Submit booking */
  async function submitBooking(paymentType) {
    var requiredFields = ['passenger_name', 'email', 'phone', 'pickup_date', 'pickup_time'];
    for (var i = 0; i < requiredFields.length; i++) {
      var input = document.querySelector('[name="' + requiredFields[i] + '"]');
      if (!input || !input.value) {
        alert('Please fill in ' + requiredFields[i].replace(/_/g, ' '));
        return;
      }
    }

    var phoneCodeSelect = document.querySelector('[name="phone_code"]');
    var phoneInput      = document.querySelector('[name="phone"]');
    var fullPhone = (phoneCodeSelect ? '+' + phoneCodeSelect.value + ' ' : '') + (phoneInput ? phoneInput.value.trim() : '');

    var returnFlightLandingInput = document.querySelector('[name="return_flight_landing_time"]');
    var returnMeetAndGreetInput = document.querySelector('[name="meet_and_greet_return"]');
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = csrfMeta ? csrfMeta.getAttribute('content') : '';

    var formData = {
      quote_ref:           bookingData.quote_ref || null,
      return_ref:          bookingData.return_ref || null,
      pickup:              bookingData.pickup,
      dropoff:             bookingData.dropoff,
      pickup_date:         document.querySelector('[name="pickup_date"]').value,
      pickup_time:         document.querySelector('[name="pickup_time"]').value,
      passenger_name:      document.querySelector('[name="passenger_name"]').value,
      email:               document.querySelector('[name="email"]').value,
      phone:               fullPhone,
      passengers:          document.querySelector('[name="passengers"]').value,
      suitcases:           normalizeSuitcaseValue(document.querySelector('[name="suitcases"]').value),
      flight_number:       document.querySelector('[name="flight_number"]').value,
      flight_time:         document.querySelector('[name="flight_landing_time"]').value,
      flight_landing_time: document.querySelector('[name="flight_landing_time"]').value,
      meet_and_greet:      document.querySelector('[name="meet_and_greet"]').checked ? 1 : 0,
      baby_seat:           (document.querySelector('[name="child_seat"]').value !== 'none') ? 1 : 0,
      baby_seat_age:       document.querySelector('[name="child_seat"]').value === 'none' ? '' : document.querySelector('[name="child_seat"]').value,
      child_seat:          (document.querySelector('[name="child_seat"]').value !== 'none') ? 1 : 0,
      has_return:          returnCheckbox.checked ? 1 : 0,
      message_to_driver:   document.querySelector('[name="message_to_driver"]').value,
      vehicle_type:        bookingData.vehicle_type,
      price:               parseFloat((getBaseFare() + getReturnFee()).toFixed(2)),
      trip_type:           returnCheckbox.checked ? 'return' : 'one-way',
      payment_type:        paymentType,
      source_url:          window.location.href,
      distance_miles:      bookingData.distance_miles || distanceMiles,
      duration_mins:       bookingData.duration_mins  || durationMins
    };

    if (returnCheckbox.checked) {
      formData.return_pickup_date         = document.querySelector('[name="return_pickup_date"]').value;
      formData.return_pickup_time         = document.querySelector('[name="return_pickup_time"]').value;
      formData.return_flight_number       = document.querySelector('[name="return_flight_number"]').value;
      formData.return_flight_time         = returnFlightLandingInput ? returnFlightLandingInput.value : '';
      formData.return_flight_landing_time = returnFlightLandingInput ? returnFlightLandingInput.value : '';
      formData.return_meet_and_greet      = returnMeetAndGreetInput && returnMeetAndGreetInput.checked ? 1 : 0;
      formData.return_baby_seat           = 0;
    }

    var capacity = getVehicleCapacity();
    var passengerCount = parseInt(document.querySelector('[name="passengers"]').value, 10);
    var suitcaseCount = normalizeSuitcaseValue(document.querySelector('[name="suitcases"]').value);
    if (passengerCount > capacity.passengers) {
      alert('Selected vehicle allows a maximum of ' + capacity.passengers + ' passengers.');
      return;
    }
    if (suitcaseCount > capacity.suitcases) {
      alert('Selected vehicle allows a maximum of ' + capacity.suitcases + ' suitcases.');
      return;
    }

    var cashBtn = document.getElementById('cashBtn');
    var cardBtn = document.getElementById('cardBtn');
    cashBtn.disabled = true;
    cardBtn.disabled = true;
    var origCash = cashBtn.innerHTML;
    var origCard = cardBtn.innerHTML;
    if (paymentType === 'cash') {
      cashBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    } else {
      cardBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
    }

    try {
      var response = await fetch(bookingSubmitUrl, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify(formData)
      });
      var result = await response.json();
      if (!response.ok || !result.success) {
        alert('Error: ' + (result.message || 'Could not complete your booking.'));
        return;
      }

      try {
        localStorage.setItem('booking_result', JSON.stringify(result));
      } catch (storageError) {}

      if (paymentType === 'card' && result.stripe_session_id) {
        redirectToStripeCheckout(result.stripe_session_id, result.redirect_url || '');
        return;
      }

      if (result.redirect_url) {
        window.location.href = result.redirect_url;
      } else {
        alert('✓ Booking submitted successfully.');
      }
    } catch(error) {
      console.error('Error:', error);
      alert('Request failed: ' + error.message);
    } finally {
      cashBtn.innerHTML = origCash;
      cardBtn.innerHTML = origCard;
      cashBtn.disabled  = false;
      cardBtn.disabled  = false;
    }
  }

  document.getElementById('cashBtn').addEventListener('click', function() { submitBooking('cash'); });
  document.getElementById('cardBtn').addEventListener('click', function() { submitBooking('card'); });
</script>

<footer class="custom-footer">
  <div class="custom-footer-container">
    <div class="custom-footer-grid">

      <div class="custom-footer-logo">
        <a href="https://executiveairportcars.com/design">
          <img src="assets/img/logo/white-logo-2.png" alt="A1 Airport Cars">
        </a>
      </div>

      <div class="custom-footer-social">
        <h4>FOLLOW US</h4>
        <div class="social-icons-wrapper">
          <a href="#" target="_blank"><i class="fab fa-facebook-f"></i></a>
          <a href="#" target="_blank"><i class="fab fa-twitter"></i></a>
          <a href="#" target="_blank"><i class="fab fa-instagram"></i></a>
          <a href="#" target="_blank"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>

      <div class="custom-footer-contact">
        <h4>CONTACT</h4>
        <a href="tel:+440123456789" class="contact-item">
          <i class="fas fa-phone-alt"></i>
          <span>+44 (0) 123 456 789</span>
        </a>
        <a href="mailto:info@executiveairportcars.com" class="contact-item">
          <i class="fas fa-envelope"></i>
          <span>info@executiveairportcars.com</span>
        </a>
        <div class="contact-item">
          <i class="fas fa-map-marker-alt"></i>
          <span>London, United Kingdom</span>
        </div>
      </div>

    </div>
    <div class="custom-footer-copyright">
      <p>&copy; <?php echo e(date('Y')); ?> A1 Airport Cars. All rights reserved.</p>
    </div>
  </div>
</footer>

<?php echo $__env->make('partials.layouts.layoutsBottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\frontend\resources\views/booking-confirmation.blade.php ENDPATH**/ ?>