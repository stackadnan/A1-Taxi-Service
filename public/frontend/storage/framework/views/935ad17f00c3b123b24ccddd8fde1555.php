<style>
.header-top-section{background-color:#0c142e!important;}
.hero-section,.navbar,header,.main-header,.site-header,nav,.navigation-menu,.top-header,.header-area,.banner-section,.page-header,.breadcrumb-area,.page-banner,.header-top,.main-nav,.site-nav,.primary-nav,.top-bar,.menu-bar,.header-wrapper,.site-navigation,.main-navigation,.page-hero,.hero-area,.inner-header,.page-title-area,.breadcrumb-wrapper,.hero-banner,.page-banner-area,.top-header-area,.middle-header,.bottom-header,.header-section,.site-branding,.menu-main-container,.primary-menu,.main-menu,.navbar-header,.navbar-collapse,.nav-header,.nav-wrapper,.header-nav,.header-menu,.page-top-header,.page-navigation,.page-nav,.theme-header,.theme-nav,.custom-header,.wp-block-template-part{display:none!important;visibility:hidden!important;opacity:0!important;height:0!important;min-height:0!important;max-height:0!important;overflow:hidden!important;position:absolute!important;z-index:-9999!important;pointer-events:none!important;}
footer,.footer,.site-footer,.main-footer,.footer-section,.footer-area,.copyright-area,.footer-widgets,.footer-bottom{display:block!important;visibility:visible!important;opacity:1!important;height:auto!important;min-height:auto!important;max-height:none!important;overflow:visible!important;position:relative!important;z-index:1!important;pointer-events:auto!important;}
body,html{margin-top:0!important;padding-top:0!important;}
</style>
<?php
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$api_url = str_contains($host,'executiveairportcars.com')
  ? 'https://admin.executiveairportcars.com/api/quote'
  : 'http://localhost/AirportServices/public/api/quote';
$headTitle='Quote Results';
$img=\App\Support\GalleryPath::path('i/149');
$Title='Home'; $Title2='Quote Results'; $SubTitle='Choose Your Vehicle';
?>
<?php echo $__env->make('partials.layouts.layoutsTop', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800;900&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
:root{
  --teal:     #008B9E;
  --teal-dk:  #006f7f;
  --teal-xlt: #eaf6f8;
  --teal-lt:  #b8e5ec;
  --navy:     #1c2b3a;
  --navy-s:   #2a3d52;
  --txt:      #222d3b;
  --txt-m:    #4a5568;
  --txt-l:    #718096;
  --border:   #e2e8ef;
  --bg:       #eef1f5;
  --green:    #16a34a;
  --shadow:   0 2px 10px rgba(0,0,0,.06);
  --shadow-h: 0 8px 28px rgba(0,0,0,.11);
  --r:        13px;
}
body{font-family:'Inter',sans-serif;background:var(--bg);color:var(--txt);-webkit-font-smoothing:antialiased;}
.wrap{max-width:1200px;margin:0 auto;padding:0 16px;}

.trust-bar{background:linear-gradient(135deg,#0d1b2e 0%,#1c2b3a 100%);border-bottom:1px solid rgba(255,255,255,.06);padding:9px 0;}
.trust-inner{display:flex;flex-wrap:wrap;justify-content:center;align-items:center;gap:5px 26px;}
.ti{display:flex;align-items:center;gap:9px;}
.ti-icon{width:32px;height:32px;border-radius:50%;background:rgba(0,139,158,.18);border:1px solid rgba(0,139,158,.32);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.ti-icon i{font-size:.78rem;color:#4dd8e8;}
.ti-txt strong{display:block;font-family:'Nunito',sans-serif;font-size:.7rem;font-weight:800;color:#fff;line-height:1.2;}
.ti-txt span{font-size:.6rem;color:rgba(255,255,255,.5);}

.prog-bar{background:#fff;border-bottom:1px solid var(--border);padding:11px 0;box-shadow:0 1px 4px rgba(0,0,0,.04);}
.steps{display:flex;align-items:center;justify-content:center;max-width:460px;margin:0 auto;padding:0 10px;}
.s-item{display:flex;flex-direction:column;align-items:center;gap:4px;}
.s-circle{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-family:'Nunito',sans-serif;font-weight:900;font-size:.7rem;border:2px solid #d0d8e4;background:#f0f3f7;color:#9baab8;transition:all .25s;}
.s-lbl{font-size:.53rem;font-weight:700;text-transform:uppercase;letter-spacing:.4px;color:#9baab8;white-space:nowrap;}
.s-item.done .s-circle{background:var(--navy);border-color:var(--navy);color:#fff;}
.s-item.done .s-lbl{color:var(--navy);}
.s-item.active .s-circle{background:var(--teal);border-color:var(--teal);color:#fff;box-shadow:0 0 0 5px rgba(0,139,158,.13);}
.s-item.active .s-lbl{color:var(--teal);font-weight:800;}
.s-line{flex:1;height:2px;background:#d0d8e4;min-width:28px;margin-bottom:17px;}
.s-line.done{background:var(--navy);}

.page-body{padding:16px 0 52px;}
.main-grid{display:grid;grid-template-columns:1fr 262px;gap:15px;align-items:start;}

.stat-bar{display:flex;align-items:center;gap:9px;padding:9px 14px;border-radius:9px;font-size:.75rem;font-weight:600;margin-bottom:12px;border:1px solid transparent;}
.stat-bar.loading{background:var(--teal-xlt);border-color:rgba(0,139,158,.18);color:var(--teal-dk);}
.stat-bar.success{background:#f0fdf4;border-color:rgba(22,163,74,.2);color:#15803d;}
.stat-bar.error{background:#fef2f2;border-color:rgba(239,68,68,.18);color:#b91c1c;}

.vcard{
  background:#fff;
  border:1.5px solid var(--border);
  border-radius:var(--r);
  margin-bottom:12px;
  box-shadow:var(--shadow);
  overflow:hidden;
  transition:transform .2s ease,box-shadow .2s,border-color .18s;
  animation:fadeUp .34s ease both;
  position:relative;
}
.vcard::after{
  content:'';position:absolute;top:0;left:0;right:0;height:3px;
  background:linear-gradient(90deg,var(--teal),var(--teal-lt));
  opacity:0;transition:opacity .2s;
}
.vcard:hover::after{opacity:1;}
.vcard:nth-child(1){animation-delay:.04s;}
.vcard:nth-child(2){animation-delay:.09s;}
.vcard:nth-child(3){animation-delay:.14s;}
.vcard:nth-child(4){animation-delay:.19s;}
.vcard:hover{transform:translateY(-3px);box-shadow:var(--shadow-h);border-color:var(--teal-lt);}

.vcard-grid{
  display:grid;
  grid-template-columns:230px 1fr 220px;
}

.vc-img{
  background:#ffffff;
  border-right:1.5px solid var(--border);
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:flex-start;
  padding:0;
  overflow:hidden;
}
.vc-img-top{
  width:100%;
  height:140px;
  overflow:hidden;
  background:#f8fafc;
  display:flex;
  align-items:center;
  justify-content:center;
}
.vc-img-top img{
  width:100%;
  height:100%;
  object-fit:cover;
  object-position:center;
  display:block;
  transition:transform .3s ease;
}
.vcard:hover .vc-img-top img{transform:scale(1.05);}
.vc-img-bottom{
  padding:12px 10px 16px;
  text-align:center;
  background:#ffffff;
  width:100%;
}
.vc-model{font-size:.55rem;font-weight:800;text-transform:uppercase;letter-spacing:.4px;color:var(--txt-l);line-height:1.3;margin-bottom:4px;}
.vc-pvt{font-size:.6rem;color:var(--txt-m);display:flex;align-items:center;justify-content:center;gap:4px;margin-bottom:4px;}
.vc-pvt i{color:var(--teal);font-size:.55rem;}
.vc-stars{display:flex;align-items:center;justify-content:center;gap:2px;font-size:.6rem;color:var(--teal);}
.vc-stars span{color:var(--txt-m);font-size:.55rem;margin-left:4px;font-weight:700;}

.vc-det{
  padding:14px 16px 12px;
  border-right:1.5px solid var(--border);
  display:flex;
  flex-direction:column;
  justify-content:space-between;
  background:#ffffff;
}
.vc-name{font-family:'Nunito',sans-serif;font-size:1.25rem;font-weight:900;color:var(--navy);line-height:1.2;margin-bottom:10px;}
.vc-caps{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:12px;}
.vc-cap-pill{display:inline-flex;align-items:center;gap:6px;background:var(--teal-xlt);border:1px solid rgba(0,139,158,.14);border-radius:20px;padding:4px 12px;font-size:.65rem;color:var(--teal-dk);font-weight:700;}
.vc-cap-pill i{font-size:.6rem;}
.vc-feats{list-style:none;display:flex;flex-wrap:wrap;gap:8px 16px;margin-bottom:12px;}
.vc-feats li{display:flex;align-items:center;gap:6px;font-size:.68rem;color:var(--txt-m);font-weight:500;}
.feat-dot{width:6px;height:6px;border-radius:50%;background:var(--teal);display:inline-block;}
.safe-badge{display:inline-flex;align-items:center;gap:6px;font-size:.65rem;font-weight:700;color:var(--teal);background:var(--teal-xlt);border:1px solid rgba(0,139,158,.14);border-radius:20px;padding:4px 12px;margin-bottom:12px;}
.more-toggle{background:none;border:none;cursor:pointer;font-size:.65rem;font-weight:700;color:var(--teal);display:inline-flex;align-items:center;gap:5px;padding:5px 0;transition:color .2s;}
.more-toggle:hover{color:var(--teal-dk);}
.more-toggle i.plus{font-size:.6rem;transition:transform .2s;}
.more-toggle.open i.plus{transform:rotate(45deg);}
.expand-box{display:none;margin-top:8px;padding:10px 12px;background:var(--teal-xlt);border-radius:8px;border-left:3px solid var(--teal);font-size:.65rem;color:#1a4a56;line-height:1.5;}

.vc-pricing{padding:14px 14px;background:#ffffff;display:flex;flex-direction:column;gap:16px;justify-content:center;}
.price-card{text-align:center;background:#ffffff;border-radius:10px;padding:8px 6px;}
.p-lbl{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.5px;color:var(--txt-l);margin-bottom:4px;}
.p-amount{font-family:'Nunito',sans-serif;font-size:1.8rem;font-weight:900;color:var(--navy);line-height:1;margin:4px 0;}
.p-amount .sym{font-size:.9rem;font-weight:800;vertical-align:super;margin-right:2px;}
.p-fixed{display:flex;align-items:center;justify-content:center;gap:5px;font-size:.58rem;font-weight:600;color:var(--green);margin-bottom:10px;}
.p-fixed i{font-size:.55rem;}
.btn{width:100%;padding:10px 8px;border:none;border-radius:40px;font-family:'Nunito',sans-serif;font-size:.72rem;font-weight:800;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px;transition:all .17s ease;letter-spacing:.2px;}
.btn-ow{background:var(--teal);color:#fff;box-shadow:0 3px 9px rgba(0,139,158,.25);}
.btn-ow:hover:not(:disabled){background:var(--teal-dk);transform:translateY(-2px);box-shadow:0 5px 15px rgba(0,139,158,.33);}
.btn-ret{background:var(--navy);color:#fff;box-shadow:0 3px 9px rgba(28,43,58,.2);}
.btn-ret:hover:not(:disabled){background:var(--navy-s);transform:translateY(-2px);box-shadow:0 5px 15px rgba(28,43,58,.28);}
.btn:disabled{opacity:.3;cursor:not-allowed;transform:none!important;box-shadow:none!important;}

.sidebar{display:flex;flex-direction:column;gap:10px;position:sticky;top:14px;}

.sb-card{
  background:#fff;
  border:1.5px solid var(--border);
  border-radius:var(--r);
  box-shadow:var(--shadow);
  overflow:hidden;
}

.sb-head{
  padding:11px 15px;
  font-family:'Nunito',sans-serif;
  font-weight:900;
  font-size:.8rem;
  color:var(--navy);
  display:flex;
  align-items:center;
  gap:6px;
  background:#f5f9fa;
  border-bottom:1.5px solid var(--border);
}

.sb-journey{padding:12px 15px 14px;}
.sj-label{font-size:.6rem;font-weight:700;color:var(--txt-l);margin-bottom:10px;display:flex;align-items:center;gap:5px;}
.sj-stop{display:flex;align-items:flex-start;gap:10px;}
.sj-dot{width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.6rem;font-weight:900;flex-shrink:0;margin-top:1px;}
.sj-dot.a{background:var(--teal);color:#fff;}
.sj-dot.b{background:var(--navy);color:#fff;}
.sj-addr{font-size:.72rem;font-weight:600;color:var(--txt);line-height:1.4;}
.sj-vline{width:2px;height:18px;background:var(--border);margin:4px 0 4px 10px;}

.sb-trust{
  padding:10px 15px 12px;
  border-top:1px solid var(--border);
  background:#fafcfd;
}
.sb-trust-row{
  display:flex;
  align-items:center;
  gap:8px;
  font-size:.65rem;
  font-weight:600;
  color:var(--txt-m);
  padding:3px 0;
}
.sb-trust-row i{color:var(--teal);font-size:.6rem;flex-shrink:0;}

.sb-meta{padding:12px 15px 0;}
.sb-meta-row{
  display:flex;
  align-items:center;
  gap:10px;
  padding:5px 0;
  font-size:.73rem;
  font-weight:700;
  color:#1a2533;
  border-bottom:1px solid var(--border);
}
.sb-meta-row:last-child{border-bottom:none;}
.sb-meta-row i{color:var(--teal);font-size:.72rem;width:16px;text-align:center;flex-shrink:0;}

.sb-map-label{
  padding:9px 15px 7px;
  font-size:.68rem;
  font-weight:800;
  color:var(--navy);
  display:flex;
  align-items:center;
  gap:6px;
  border-top:1px solid var(--border);
  background:#f5f9fa;
}

.sb-payment{padding:12px 15px;}
.sb-pay-lbl{font-size:.65rem;font-weight:800;text-transform:uppercase;letter-spacing:.4px;color:var(--txt-l);margin-bottom:9px;}
.pay-logos{display:flex;flex-wrap:wrap;gap:6px;}
.pay-logo{display:inline-flex;align-items:center;gap:4px;padding:4px 10px;border:1px solid var(--border);border-radius:6px;font-size:.6rem;font-weight:800;background:#f9fbfc;color:var(--navy);}
.pay-logo.visa{color:#1a1f71;letter-spacing:.5px;}
.pay-logo.mc{color:#eb001b;}
.mc-circles{display:inline-flex;}
.pay-logo.amex{color:#007bc1;}
.pay-logo.debit{color:var(--navy);}

.sb-help{border-top:1px solid var(--border);}
.help-card{
  display:flex;
  align-items:center;
  gap:10px;
  padding:10px 15px;
  text-decoration:none;
  border-bottom:1px solid var(--border);
  transition:background .15s;
}
.help-card:last-child{border-bottom:none;}
.help-card:hover{background:#f5f9fa;}
.help-icon{width:30px;height:30px;border-radius:50%;background:var(--teal-xlt);border:1px solid rgba(0,139,158,.18);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.help-icon i{font-size:.75rem;color:var(--teal);}
.help-txt{display:flex;flex-direction:column;gap:1px;}
.help-txt strong{font-size:.68rem;font-weight:800;color:var(--navy);}
.help-txt span{font-size:.58rem;color:var(--txt-l);}

@keyframes fadeUp{from{opacity:0;transform:translateY(12px);}to{opacity:1;transform:translateY(0);}}

@media(max-width:920px){
  .main-grid{grid-template-columns:1fr;}
  .sidebar{position:static;}
  .vcard-grid{grid-template-columns:200px 1fr 200px;}
}
@media(max-width:780px){
  .vcard-grid{grid-template-columns:180px 1fr 180px;}
}

@media(max-width:650px){
  .stat-bar.success {
    font-size: 0.68rem !important;
  }
  .steps {
    max-width: 100%;
  }
  .s-circle {
    width: 26px;
    height: 26px;
    font-size: 0.6rem;
  }
  .s-lbl {
    font-size: 0.45rem !important;
  }

  .vcard{
    margin-bottom:10px;
    border-radius:12px;
  }

  .vcard-grid{
    display:flex;
    flex-direction:column;
  }

  .vc-img{
    order:1;
    flex-direction:row;
    border-right:none;
    border-bottom:1.5px solid var(--border);
    align-items:stretch;
    background:#fff;
    padding:0;
  }

  .vc-img-top{
    width:110px;
    height:100px;
    flex-shrink:0;
    border-radius:0;
    background:#f5f8fa;
  }
  .vc-img-top img{
    object-fit:contain;
    padding:6px;
  }

  .vc-img-bottom{
    flex:1;
    text-align:left;
    padding:10px 12px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    gap:3px;
  }
  .vc-model{
    font-size:.58rem;
    font-weight:700;
    color:var(--txt-l);
    text-align:left;
  }
  .vc-pvt{
    justify-content:flex-start;
    font-size:.6rem;
  }
  .vc-stars{
    justify-content:flex-start;
    font-size:.62rem;
  }

  .vc-det{
    order:2;
    border-right:none;
    border-bottom:1.5px solid var(--border);
    padding:8px 12px;
    gap:0;
  }
  .vc-name{
    font-size:1rem;
    font-weight:900;
    margin-bottom:5px;
  }
  .vc-caps{
    gap:5px;
    margin-bottom:6px;
    flex-wrap:wrap;
  }
  .vc-cap-pill{
    font-size:.58rem;
    padding:3px 8px;
    gap:4px;
  }
  .vc-feats{
    gap:4px 10px;
    margin-bottom:6px;
  }
  .vc-feats li{
    font-size:.62rem;
  }
  .safe-badge{
    font-size:.6rem;
    padding:3px 9px;
    margin-bottom:6px;
  }
  
  .vc-det .more-toggle {
    display: none !important;
  }
  
  .mobile-more-wrapper {
    display: block !important;
    order: 4;
  }
  
  .mobile-more-wrapper .more-toggle {
    background: none;
    border: none;
    cursor: pointer;
    font-size: .62rem;
    font-weight: 700;
    color: var(--teal);
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 10px 0 5px 0;
    width: 100%;
    text-align: center;
    justify-content: center;
  }
  
  .mobile-more-wrapper .more-toggle i.plus {
    font-size: .6rem;
    transition: transform .2s;
  }
  
  .mobile-more-wrapper .more-toggle.open i.plus {
    transform: rotate(45deg);
  }
  
  .mobile-more-wrapper .expand-box {
    font-size: .62rem;
    padding: 8px 10px;
    margin-top: 6px;
    background: var(--teal-xlt);
    border-radius: 8px;
    border-left: 3px solid var(--teal);
    display: none;
  }
  
  .mobile-more-wrapper .expand-box.show,
  .mobile-more-wrapper .expand-box.open {
    display: block;
  }

  .vc-pricing{
    order:3;
    flex-direction:row;
    gap:0;
    padding:0;
    border-top:none;
    align-items:stretch;
  }

  .price-card{
    flex:1;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    padding:10px 8px;
    border-radius:0;
    gap:1px;
    background:#fff;
  }

  .price-card:first-child{
    border-right:1px solid var(--border);
  }

  .p-lbl{
    font-size:.58rem;
    font-weight:800;
    letter-spacing:.3px;
    margin-bottom:2px;
    color:var(--txt-l);
  }
  .p-amount{
    font-size:1.35rem;
    margin:2px 0 4px;
    font-weight:900;
  }
  .p-amount .sym{
    font-size:.75rem;
  }

  .p-fixed{
    display:none;
  }

  .btn{
    width:100%;
    padding:9px 6px;
    font-size:.68rem;
    font-weight:800;
    border-radius:8px;
    gap:4px;
    letter-spacing:.1px;
  }

  .btn i{
    display:none;
  }
}

@media(min-width:651px){
  .mobile-more-wrapper {
    display: none !important;
  }
}

@media(max-width:360px){
  .vc-img-top{
    width:90px;
    height:88px;
  }
  .vc-name{font-size:.9rem;}
  .p-amount{font-size:1.15rem;}
  .btn{font-size:.62rem;padding:8px 4px;}
}

.social-icon,.social-links,.footer-social,[class*="social-icon"],[class*="social-link"]{display:flex!important;align-items:center!important;justify-content:center!important;gap:15px!important;visibility:visible!important;opacity:1!important;height:auto!important;overflow:visible!important;position:relative!important;z-index:100!important;}
.social-icon a,.social-links a,.footer-social a,[class*="social-icon"] a{display:flex!important;align-items:center!important;justify-content:center!important;width:38px!important;height:38px!important;background:white!important;border-radius:50%!important;color:#36454F!important;text-decoration:none!important;}
.social-icon i,.social-links i,.footer-social i{font-size:18px!important;color:#36454F!important;}
.footer .container,footer .container{display:block!important;visibility:visible!important;overflow:visible!important;}
.footer-row,.footer .row,footer .row{display:flex!important;flex-wrap:wrap!important;visibility:visible!important;}
body>*:first-child{margin-top:0!important;}
.pay-logo.mc .mc-circles {
  display: inline-flex;
  position: relative;
  width: 28px;
  height: 18px;
  flex-shrink: 0;
  margin-right: 4px;
}

.pay-logo.mc .mc-circles .mc-red,
.pay-logo.mc .mc-circles .mc-yellow {
  content: '';
  position: absolute;
  width: 16px;
  height: 16px;
  border-radius: 50%;
  top: 1px;
}

.pay-logo.mc .mc-circles .mc-red {
  background: #eb001b;
  left: 0;
  z-index: 1;
}

.pay-logo.mc .mc-circles .mc-yellow {
  background: #f79e1b;
  right: 0;
  z-index: 2;
  mix-blend-mode: multiply;
}

.pay-logo.mc span {
  font-size: .54rem;
  font-weight: 700;
  color: #333;
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

.header-top-section::before,
.header-top-section::after {
  content: none !important;
  display: none !important;
}

.header-top-section {
  background: #0c142e url('assets/img/logo/white-logo-2.png') no-repeat left 24px center !important;
  background-size: 120px auto !important;
  padding: 15px 0 !important;
  position: relative !important;
  width: 100% !important;
  min-height: 70px !important;
}

.header-top-section .container,
.header-top-section .container-fluid,
.header-top-wrapper {
  max-width: 1300px !important;
  margin: 0 auto !important;
  padding: 0 24px !important;
  padding-left: 40px !important;
  display: flex !important;
  align-items: center !important;
  justify-content: flex-end !important;
  flex-wrap: wrap !important;
  gap: 25px !important;
}

.header-top-section span,
.header-top-section a,
.header-top-section p {
  color: rgba(255, 255, 255, 0.85) !important;
  font-size: 13px !important;
  text-decoration: none !important;
  transition: color 0.3s !important;
  margin-left: 5px !important;
  margin-right: 5px !important;
}

.header-top-section a:hover {
  color: #008B9E !important;
}

.header-top-section span:last-of-type {
  margin-right: 25px !important;
  padding-right: 25px !important;
  border-right: 1px solid rgba(255, 255, 255, 0.25) !important;
}

body {
  margin: 0 !important;
  overflow-x: hidden !important;
}

html, body {
  overflow-x: hidden !important;
  width: 100% !important;
}

@media (max-width: 650px) {
  .header-top-section {
    display: none !important;
    background-position: center !important;
    background-size: 100px auto !important;
  }
  .mobile-top-logo {
    display: block !important;
  }
}

@media (min-width: 651px) {
  .header-top-section {
    display: block !important;
  }
  .mobile-top-logo {
    display: none !important;
  }
}

footer:not(.custom-footer),
.footer:not(.custom-footer),
.site-footer:not(.custom-footer),
.main-footer:not(.custom-footer),
.footer-section:not(.custom-footer),
.footer-area:not(.custom-footer),
.copyright-area:not(.custom-footer),
.footer-widgets:not(.custom-footer),
.footer-bottom:not(.custom-footer) {
  display: none !important;
  visibility: hidden !important;
  height: 0 !important;
  overflow: hidden !important;
  position: absolute !important;
  z-index: -9999 !important;
}

.custom-footer {
  display: block !important;
  visibility: visible !important;
  height: auto !important;
  position: relative !important;
  z-index: 1 !important;
}

.footer .container,
footer .container,
.footer-section .container {
  display: none !important;
}

.cta-cheap-rental-section,
.cta-cheap-rental {
  display: none !important;
  visibility: hidden !important;
  height: 0 !important;
  overflow: hidden !important;
  position: absolute !important;
  z-index: -9999 !important;
  pointer-events: none !important;
}

.top-list {
  display: none !important;
}

.vc-pvt i,
.vc-stars i,
.vc-cap-pill i,
.vc-feats li i,
.safe-badge i,
.more-toggle i,
.vehicle-highlights i,
.feat-dot {
  color: #4a5568 !important;
}

.sb-head i,
.sj-label i,
.sb-meta-row i,
.sb-map-label i,
.sb-trust-row i,
.sb-pay-lbl i,
.help-icon i {
  color: #4a5568 !important;
}

.location-icon i,
.info-label-modern i,
.price-modern i,
.back-btn i {
  color: #4a5568 !important;
}

.s-circle i {
  color: #4a5568 !important;
}

.ti-icon i {
  color: #4a5568 !important;
}

.input-modern label i,
.return-badge-modern i,
.meet-greet-modern i,
.terms-modern i {
  color: #4a5568 !important;
}

.summary-head .summary-icon-wrapper i {
  color: white !important;
}

.s-item.done .s-circle i {
  color: white !important;
}

.s-item.active .s-circle {
  background: var(--teal) !important;
  border-color: var(--teal) !important;
}
.s-item.active .s-circle i {
  color: white !important;
}

.btn i,
.btn-ow i,
.btn-ret i,
.btn-payment i,
.btn-cash-modern i,
.btn-card-modern i {
  color: white !important;
}

.pay-logo.visa i {
  color: #1a1f71 !important;
}
.pay-logo.mc i {
  color: #eb001b !important;
}
.pay-logo.amex i {
  color: #007bc1 !important;
}
.pay-logo.debit i {
  color: #4a5568 !important;
}

.header-top-wrapper .contact-list

 {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-left: 400px !important;
}
.header-top-wrapper .header-top-right {
    display: flex;
    align-items: center;
    gap: 70px;
    margin-left: 54px;
}
</style>

<div class="mobile-top-logo" style="display:none; background:#0c142e; padding:15px 0; text-align:center;">
  <img src="assets/img/logo/white-logo-2.png" alt="Logo" style="height:45px; width:auto;">
</div>

<script>
(function(){
  ['.hero-section','.navbar','header','.main-header','.site-header','nav','.navigation-menu','.top-header','.header-area','.banner-section','.page-header','.breadcrumb-area','.page-banner']
  .forEach(function(s){document.querySelectorAll(s).forEach(function(el){el.style.cssText='display:none!important;visibility:hidden!important;height:0!important;overflow:hidden!important;';});});
  document.body.style.marginTop='0';document.body.style.paddingTop='0';
})();
</script>

<div class="prog-bar">
  <div class="wrap">
    <div class="steps">
      <div class="s-item done"><div class="s-circle"><i class="fa-solid fa-check" style="font-size:.7rem;"></i></div><span class="s-lbl">Locations</span></div>
      <div class="s-line done"></div>
      <div class="s-item active"><div class="s-circle">2</div><span class="s-lbl">Vehicle</span></div>
      <div class="s-line"></div>
      <div class="s-item"><div class="s-circle">3</div><span class="s-lbl">Details</span></div>
      <div class="s-line"></div>
      <div class="s-item"><div class="s-circle">4</div><span class="s-lbl">Complete</span></div>
    </div>
  </div>
</div>

<section class="page-body">
  <div class="wrap">
    <div class="main-grid">

      <div>
        <div id="qstatus" class="stat-bar loading">
          <i class="fa-solid fa-circle-notch fa-spin"></i>
          <span>Calculating best prices for your journey…</span>
        </div>
        <div id="results"></div>
      </div>

      <div>
        <div class="sidebar">

          <div class="sb-card">
            <div class="sb-head"><i class="fa-solid fa-location-dot"></i> Your transfer</div>
            <div id="route-summary"></div>
            <div id="journey-meta" style="padding: 0 15px 12px 15px; border-top: 1px solid var(--border); margin-top: 8px;">
              <div class="sb-meta-row" style="display:flex; align-items:center; gap:10px; padding:8px 0 4px 0; font-size:.73rem; font-weight:700; color:#1a2533;">
                <i class="fa-regular fa-clock" style="color:var(--teal);"></i>
                <span id="journey-minutes">199 min</span>
                <i class="fa-regular fa-calendar-days" style="color:var(--teal); margin-left:auto;"></i>
                <span id="journey-date">23-04-2026</span>
              </div>
            </div>
          </div>

          <div class="sb-card">
            <div id="map-section"></div>
          </div>

          <div class="sb-card">
            <div class="sb-payment">
              <div class="sb-trust" style="border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); background:#fafcfd; padding:10px 15px;">
                <div class="sb-trust-row"><i class="fa-solid fa-check"></i> &gt;100,000 passengers transported</div>
                <div class="sb-trust-row"><i class="fa-solid fa-check"></i> Instant confirmation</div>
                <div class="sb-trust-row"><i class="fa-solid fa-check"></i> All-inclusive pricing</div>
                <div class="sb-trust-row"><i class="fa-solid fa-check"></i> Secure Payment by card</div>
              </div>
              <div class="sb-pay-lbl">Accepted payments</div>
              <div class="pay-logos">
                <div class="pay-logo visa">
                  <i class="fa-brands fa-cc-visa" style="color: #1a1f71; font-size: 1.1rem;"></i>
                  <span>VISA</span>
                </div>
                <div class="pay-logo mc">
                  <div class="mc-circles">
                    <div class="mc-red"></div>
                    <div class="mc-yellow"></div>
                  </div>
                  <span>Mastercard</span>
                </div>
                <div class="pay-logo amex">
                  <i class="fa-brands fa-cc-amex" style="color: #007bc1; font-size: 1.1rem;"></i>
                  <span>Amex</span>
                </div>
                <div class="pay-logo debit">
                  <i class="fa-solid fa-credit-card" style="color: #5a6e7a;"></i>
                  <span>Debit</span>
                </div>
              </div>
            </div>
           
            <div class="sb-help">
              <a class="help-card" href="#" onclick="return false;">
                <div class="help-icon"><i class="fa-solid fa-circle-question"></i></div>
                <div class="help-txt"><strong>Help Centre</strong><span>Frequently asked questions</span></div>
              </a>
              <a class="help-card" href="#" onclick="return false;">
                <div class="help-icon"><i class="fa-regular fa-comment-dots"></i></div>
                <div class="help-txt"><strong>Need help?</strong><span>Start a Chat</span></div>
              </a>
            </div>
          </div>

        </div>
      </div>

    </div>
  </div>
</section>

<script>
var API_URL=<?php echo json_encode($api_url, 15, 512) ?>;
var VEHICLES=[
  {key:'saloon',name:'Standard',img:'assets/img/car/saloon.png',model:'VW PASSAT, TOYOTA PRIUS OR SIMILAR.',c1:'Up to 4 Passengers',c2:'2 medium suitcases',feats:['2 handcarry','Friendly Drivers','Free Waiting time','Door-to-door'],extra:'Child restraint devices available on request. Vehicle may differ from image; exact model subject to availability.',base:45},
  {key:'business',name:'First Class',img:'assets/img/car/executive.png',model:'E CLASS MERCEDES OR SIMILAR.',c1:'Up to 4 Passengers',c2:'2 medium suitcases',feats:['2 handcarry','Friendly Drivers','Free Waiting time','Door-to-door'],extra:'Child restraint devices available on request. Vehicle may differ from image; exact model subject to availability.',base:75},
  {key:'mpv6',name:'People Carrier',img:'assets/img/car/mpv6.png',model:'VW SHARAN, SEAT ALHAMBRA OR SIMILAR.',c1:'Up to 5 Passengers',c2:'3 medium suitcases',feats:['2 handcarry','Friendly Drivers','Free Waiting time','Door-to-door'],extra:'Child restraint devices available on request. Vehicle may differ from image; exact model subject to availability.',base:65},
  {key:'mpv8',name:'Mini Van 8',img:'assets/img/car/mpv8.png',model:'MERCEDES V CLASS OR SIMILAR.',c1:'Up to 8 Passengers',c2:'8 Suitcases',feats:['8 handcarry','Friendly Drivers','Free Waiting time','Door-to-door'],extra:'Child restraint devices available on request. Vehicle may differ from image; exact model subject to availability.',base:95}
];

var qd={};
try{qd=JSON.parse(localStorage.getItem('quote_data')||'{}');}catch(e){}

renderRoute(qd);
renderMeta(qd);
renderMap(qd);

if(!qd.pickup_lat||!qd.dropoff_lat){
  setStatus('No quote data found. Please start from the home page.','error');
  document.getElementById('results').innerHTML='<div class="empty-st"><i class="fa-solid fa-map-location-dot"></i><h3>No Journey Details</h3><p>Return to the home page to search for a quote.</p><a href="./"><i class="fa-solid fa-arrow-left"></i> Start New Quote</a></div>';
}else{loadQuote();}

function loadQuote(){
  fetch(API_URL,{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({pickup_lat:qd.pickup_lat,pickup_lon:qd.pickup_lon,dropoff_lat:qd.dropoff_lat,dropoff_lon:qd.dropoff_lon,pickup_postcode:qd.pickup_postcode||'',dropoff_postcode:qd.dropoff_postcode||'',pickup_address:qd.pickup,dropoff_address:qd.dropoff,date:qd.date,distance_miles:qd.distance_miles,source_url:qd.source_url||''})})
  .then(r=>r.json()).then(resp=>{if(!resp.success)throw new Error(resp.message);setStatus('Great news! Select your preferred vehicle below.','success');renderAPI(resp);})
  .catch(function(err){setStatus('Request failed: '+(err&&err.message?err.message:'Unable to fetch prices right now.'),'error');document.getElementById('results').innerHTML='<div class="empty-st"><i class="fa-solid fa-triangle-exclamation"></i><h3>Unable to Fetch Prices</h3><p>Please try again in a moment.</p></div>';});
}

function renderAPI(resp){var html='';VEHICLES.forEach(function(v){var ow=resp.pricing?resp.pricing[v.key+'_price']:resp[v.key+'_price'];ow=(ow!==undefined&&!isNaN(ow))?parseFloat(ow):null;html+=card(v,ow,ow!==null?ow*2:null);});document.getElementById('results').innerHTML=html;}

function card(v,ow,ret){
  var os=ow!==null?ow.toFixed(2):null,rs=ret!==null?ret.toFixed(2):null;
  var owDisabled=!os?'disabled':'';
  var retDisabled=!rs?'disabled':'';
  return '<div class="vcard">'+
    '<div class="vcard-grid">'+
      '<div class="vc-img">'+
        '<div class="vc-img-top"><img src="'+h(v.img)+'" alt="'+h(v.name)+'"></div>'+
        '<div class="vc-img-bottom">'+
          '<div class="vc-model">'+h(v.model)+'</div>'+
          '<div class="vc-pvt"><i class="fa-solid fa-user-check"></i> Private transfer</div>'+
          '<div class="vc-stars"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><span>5.0</span></div>'+
        '</div>'+
      '</div>'+
      '<div class="vc-det">'+
        '<div>'+
          '<div class="vc-name">'+h(v.name)+'</div>'+
          '<div class="vc-caps">'+
            '<span class="vc-cap-pill"><i class="fa-solid fa-users"></i> '+h(v.c1)+'</span>'+
            '<span class="vc-cap-pill"><i class="fa-solid fa-suitcase"></i> '+h(v.c2)+'</span>'+
          '</div>'+
          '<ul class="vc-feats">'+v.feats.map(function(f){return '<li><span class="feat-dot"></span>'+h(f)+'</li>';}).join('')+'</ul>'+
          '<div class="safe-badge"><i class="fa-solid fa-shield-halved"></i> Safe and secure travel</div>'+
          '<div class="vehicle-highlights" style="margin: 10px 0 8px 0; padding: 6px 0; border-top: 1px solid rgba(0,0,0,0.05); border-bottom: 1px solid rgba(0,0,0,0.05);">'+
            '<span style="display:inline-flex; align-items:center; gap:12px; font-size:.62rem; color:#2d4a5c;">'+
              '<span><i class="fa-regular fa-clock" style="color:#008b9e;"></i> Free waiting time</span>'+
              '<span><i class="fa-solid fa-door-open" style="color:#008b9e;"></i> Door-to-door</span>'+
              '<span><i class="fa-regular fa-face-smile" style="color:#008b9e;"></i> Friendly Drivers</span>'+
            '</span>'+
          '</div>'+
        '</div>'+
        '<div>'+
          '<button class="more-toggle desktop-toggle"><i class="fa-solid fa-plus plus"></i> <span class="tl">Show more information</span></button>'+
          '<div class="expand-box" style="display:none;">'+h(v.extra)+'</div>'+
        '</div>'+
      '</div>'+
      '<div class="vc-pricing">'+
        '<div class="price-card">'+
          '<div class="p-lbl">Total one-way price</div>'+
          '<div class="p-amount">'+(os?'<span class="sym">£</span>'+os:'N/A')+'</div>'+
          '<div class="p-fixed"><i class="fa-solid fa-circle-check"></i> Fixed Price No Hidden Charges</div>'+
          '<button class="btn btn-ow" data-book="1" data-vehicle="'+h(v.name)+'" data-trip="one-way" data-price="'+(os||'')+'" '+owDisabled+'><i class="fa-solid fa-arrow-right"></i> Select one-way</button>'+
        '</div>'+
        '<div class="price-card">'+
          '<div class="p-lbl">Total return price</div>'+
          '<div class="p-amount">'+(rs?'<span class="sym">£</span>'+rs:'N/A')+'</div>'+
          '<button class="btn btn-ret" data-book="1" data-vehicle="'+h(v.name)+'" data-trip="return" data-price="'+(rs||'')+'" '+retDisabled+'><i class="fa-solid fa-rotate"></i> Select return</button>'+
        '</div>'+
      '</div>'+
      '<div class="mobile-more-wrapper" style="display:none; padding:0 12px 12px 12px; border-top:1px solid var(--border);">'+
        '<button class="more-toggle mobile-toggle"><i class="fa-solid fa-plus plus"></i> <span class="tl">Show more information</span></button>'+
        '<div class="expand-box" style="display:none;">'+h(v.extra)+'</div>'+
      '</div>'+
    '</div>'+
  '</div>';
}

function renderRoute(d){
  var el=document.getElementById('route-summary');
  if(!el) return;
  
  var minutes = (d.distance_miles && !isNaN(d.distance_miles)) ? Math.max(1, Math.round(d.distance_miles * 2)) + ' min' : '199 min';
  var dateStr = d.date || '23-04-2026';
  
  var minutesSpan = document.getElementById('journey-minutes');
  var dateSpan = document.getElementById('journey-date');
  if(minutesSpan) minutesSpan.textContent = minutes;
  if(dateSpan) dateSpan.textContent = dateStr;
  
  if(!d||!d.pickup||!d.dropoff){
    el.innerHTML='<div class="sb-journey"><div class="sj-stop"><div class="sj-dot a">A</div><div class="sj-addr">No journey data</div></div></div>';
    return;
  }
  el.innerHTML=
    '<div class="sb-journey">'+
      '<div class="sj-label"><i class="fa-regular fa-circle-dot"></i> Outward journey / Inward journey</div>'+
      '<div class="sj-stop" style="margin-top:8px;"><div class="sj-dot a">A</div><div class="sj-addr">'+h(d.pickup)+'</div></div>'+
      '<div class="sj-vline"></div>'+
      '<div class="sj-stop"><div class="sj-dot b">B</div><div class="sj-addr">'+h(d.dropoff)+'</div></div>'+
    '</div>';
}

function renderMeta(d){
  var el=document.getElementById('meta-section');
  if(!el) return;
  el.innerHTML='';
}

function renderMap(d){
  var el = document.getElementById('map-section');
  if(!el) return;
  
  var dist = (d.distance_miles && !isNaN(d.distance_miles)) ? parseFloat(d.distance_miles).toFixed(1) + ' Miles' : '—';
  
  el.innerHTML =
   '<div style="display:flex; align-items:center; gap:10px; font-size:.73rem; font-weight:700; color:#1a2533; padding-bottom:8px; margin-left:14px; margin-top:12px;">' +
        '<i class="fa-solid fa-route" style="color:var(--teal);"></i>' +
        '<span>' + h(dist) + '</span>' +
      '</div>' +
    '<div class="sb-map-label"><i class="fa-solid fa-map"></i> Route Map</div>' +
    '<div style="padding: 8px 15px 0 15px; border-bottom: none;">' +
    '</div>' +
    '<div style="width:100%;height:160px;overflow:hidden;background:#e8f0f5;position:relative;">' +
      '<div id="map-placeholder" style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:6px;color:#9baab8;">' +
        '<i class="fa-solid fa-spinner fa-spin" style="font-size:1.2rem;"></i>' +
        '<span style="font-size:.6rem;font-weight:600;">Loading map…</span>' +
      '</div>' +
      '<iframe id="route-iframe" style="width:100%;height:100%;border:none;display:none;" loading="lazy" allowfullscreen referrerpolicy="no-referrer-when-downgrade"></iframe>' +
    '</div>';
  
  initMap(d);
}

function initMap(d){
  var iframe=document.getElementById('route-iframe');
  var placeholder=document.getElementById('map-placeholder');
  if(!iframe) return;
  if(d&&d.pickup_lat){
    var src='https://www.openstreetmap.org/export/embed.html?marker='+
      (d.pickup_lat||51.5)+'%2C'+(d.pickup_lon||-0.1)+'&layer=mapnik';
    iframe.src=src;
    iframe.onload=function(){placeholder.style.display='none';iframe.style.display='block';};
  } else {
    iframe.src='https://www.openstreetmap.org/export/embed.html?bbox=-0.5,51.3,0.3,51.6&layer=mapnik';
    iframe.onload=function(){placeholder.style.display='none';iframe.style.display='block';};
  }
}

function setStatus(msg,type){var ic={loading:'fa-circle-notch fa-spin',success:'fa-circle-check',error:'fa-circle-exclamation'},el=document.getElementById('qstatus');el.className='stat-bar '+type;el.innerHTML='<i class="fa-solid '+ic[type]+'"></i><span>'+h(msg)+'</span>';}

function doBook(vehicle,price,trip){
  var dp=(qd.date||'').split('-'),fmtDate=dp.length===3?dp[2]+'-'+dp[1]+'-'+dp[0]:qd.date;
  fetch(API_URL.replace('/quote','/quote/save'),{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json'},body:JSON.stringify({pickup_address:qd.pickup,dropoff_address:qd.dropoff,pickup_date:fmtDate,vehicle_type:vehicle,price:price,trip_type:trip,source_url:qd.source_url||''})})
  .then(r=>r.json()).then(function(resp){if(resp.success){localStorage.setItem('booking_data',JSON.stringify({quote_ref:resp.quote_ref,return_ref:resp.return_ref,pickup:qd.pickup,dropoff:qd.dropoff,pickup_date:fmtDate,vehicle_type:vehicle,price:price,trip_type:trip}));window.location.href='booking-confirmation';}else alert('Error: '+(resp.message||'unknown'));})
  .catch(function(e){alert(e.message);});
}

function h(s){return String(s||'').replace(/[&<>"']/g,function(m){return({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m];});}

document.addEventListener('click', function(e) {
  var b = e.target.closest('.btn[data-book]');
  if (b && !b.disabled) {
    doBook(b.dataset.vehicle, +b.dataset.price, b.dataset.trip);
    return;
  }
  
  var t = e.target.closest('.more-toggle');
  if (t) {
    var card = t.closest('.vcard');
    var expandBox;
    
    if (card) {
      if (t.classList.contains('desktop-toggle')) {
        expandBox = card.querySelector('.vc-det .expand-box');
      } else {
        expandBox = card.querySelector('.mobile-more-wrapper .expand-box');
      }
      
      if (expandBox) {
        var isOpen = expandBox.style.display === 'block';
        expandBox.style.display = isOpen ? 'none' : 'block';
        t.classList.toggle('open', !isOpen);
        var textSpan = t.querySelector('.tl');
        if (textSpan) {
          textSpan.textContent = isOpen ? 'Show more information' : 'Show less information';
        }
      }
    }
    return;
  }
});
</script>

<style>
  .custom-footer {
    background: linear-gradient(135deg, #0c142e 0%, #1a2340 100%);
    padding: 50px 24px 30px;
    margin-top: 60px;
    border-top: 1px solid rgba(255,255,255,0.1);
  }
  
  .custom-footer-container {
    max-width: 1300px;
    margin: 0 auto;
  }
  
  .custom-footer-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 40px;
    align-items: center;
  }
  
  .custom-footer-logo {
    text-align: left;
  }
  
  .custom-footer-logo img {
    height: 55px;
    width: auto;
  }
  
  .custom-footer-social {
    text-align: center;
  }
  
  .custom-footer-social h4 {
    color: white;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 20px;
    letter-spacing: 1px;
  }
  
  .social-icons-wrapper {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
  }
  
  .social-icons-wrapper a {
    width: 42px;
    height: 42px;
    background: rgba(255,255,255,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    color: white;
    text-decoration: none;
  }
  
  .social-icons-wrapper a:hover {
    background: #008B9E;
    transform: translateY(-3px);
  }
  
  .social-icons-wrapper a i {
    font-size: 20px;
  }
  
  .custom-footer-contact {
    text-align: right;
  }
  
  .custom-footer-contact h4 {
    color: white;
    font-size: 16px;
    font-weight: 700;
    margin-bottom: 20px;
    letter-spacing: 1px;
  }
  
  .contact-item {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 12px;
    margin-bottom: 15px;
    color: rgba(255,255,255,0.7);
    font-size: 14px;
    text-decoration: none;
    transition: color 0.3s;
  }
  
  .contact-item:hover {
    color: #008B9E;
  }
  
  .contact-item i {
    width: 20px;
    color: #008B9E;
    font-size: 16px;
  }
  
  .custom-footer-copyright {
    text-align: center;
    padding-top: 40px;
    margin-top: 40px;
    border-top: 1px solid rgba(255,255,255,0.1);
    color: rgba(255,255,255,0.5);
    font-size: 12px;
  }
  
  @media (max-width: 850px) {
    .custom-footer-grid {
      grid-template-columns: 1fr;
      gap: 30px;
      text-align: center;
    }
    
    .custom-footer-logo {
      text-align: center;
    }
    
    .custom-footer-contact {
      text-align: center;
    }
    
    .contact-item {
      justify-content: center;
    }
  }
  
  @media (max-width: 480px) {
    .custom-footer-logo img {
      height: 45px;
    }
    
    .social-icons-wrapper a {
      width: 38px;
      height: 38px;
    }
    
    .social-icons-wrapper a i {
      font-size: 18px;
    }
  }
</style>

<footer class="custom-footer">
  <div class="custom-footer-container">
    <div class="custom-footer-grid">
      
      <div class="custom-footer-logo">
        <img src="assets/img/logo/white-logo-2.png" alt="A1 Airport Cars">
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

<?php echo $__env->make('partials.layouts.layoutsBottom', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/executiveairport/public_html/frontend/resources/views/quote-results.blade.php ENDPATH**/ ?>