<?php
/**
 * Executive Airport Cars — Public Quote Page
 * Deploy this file (and its config block) on executiveairportcars.com
 *
 * The only thing this page does server-side is embed safe config values.
 * All pricing logic lives in the Laravel backend at admin.executiveairportcars.com.
 */

// ── Configuration ────────────────────────────────────────────────────────────
// You can move these to a separate config.php and require it here.

$config = [
    // Backend API (Laravel on admin subdomain)
    'api_url'       => 'https://admin.executiveairportcars.com/api/quote',

    // Google Maps – must have Maps JavaScript API + Places API enabled
    'maps_api_key'  => 'YOUR_GOOGLE_MAPS_API_KEY',

    // Contact details shown in the page
    'phone'         => '(+44) 1234 567 890',
    'phone_href'    => '+441234567890',
    'email'         => 'bookings@executiveairportcars.com',
    'whatsapp'      => '441234567890',

    // Where the "Book Now" button sends the user (set to your booking page URL,
    // or leave as null to keep the WhatsApp fallback)
    'booking_url'   => null,

    // Site meta
    'site_name'     => 'Executive Airport Cars',
    'tagline'       => 'Premium Airport Transfers to and from all UK Airports',
];

// Escape all config values for safe HTML/JS output
function e(string $v): string { return htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); }
function js(mixed $v): string { return json_encode($v, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($config['site_name']) ?> – <?= e($config['tagline']) ?></title>
  <meta name="description" content="Premium airport taxi transfers to and from all UK airports. Fixed prices, no hidden charges. Book your Executive Airport Car today." />

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }
    body { font-family: 'Poppins', sans-serif; color: #1a1a2e; background: #fff; line-height: 1.6; }

    :root {
      --gold: #c9a84c; --gold-lt: #f0d080;
      --navy: #1a1a2e; --navy-lt: #16213e;
      --gray: #6b7280; --light: #f8f9fc; --white: #ffffff;
      --radius: 12px;
      --shadow: 0 4px 24px rgba(0,0,0,.10);
      --shadow-lg: 0 8px 40px rgba(0,0,0,.16);
    }

    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-thumb { background: var(--gold); border-radius: 3px; }

    /* ── Top bar ── */
    .topbar {
      background: var(--navy); color: rgba(255,255,255,.85);
      font-size: .8rem; padding: .45rem 1.5rem;
      display: flex; gap: 1.5rem; align-items: center; flex-wrap: wrap;
    }
    .topbar a { color: var(--gold-lt); text-decoration: none; }
    .topbar .spacer { flex: 1; }
    .topbar .social { display: flex; gap: .75rem; }
    .topbar .social a { font-size: 1rem; }

    /* ── Navbar ── */
    nav.navbar {
      position: sticky; top: 0; z-index: 100;
      background: var(--white); border-bottom: 2px solid var(--gold);
      padding: .9rem 2rem; display: flex; align-items: center; gap: 2rem;
      box-shadow: 0 2px 12px rgba(0,0,0,.07);
    }
    .nav-logo { display: flex; align-items: center; gap: .6rem; text-decoration: none; }
    .nav-logo .logo-icon {
      width: 44px; height: 44px; background: var(--gold);
      border-radius: 8px; display: flex; align-items: center; justify-content: center;
      font-size: 1.4rem; color: var(--navy);
    }
    .nav-logo .logo-text strong { display: block; font-size: 1rem; font-weight: 700; color: var(--navy); }
    .nav-logo .logo-text span  { font-size: .72rem; color: var(--gray); text-transform: uppercase; letter-spacing:.5px; }
    .nav-links { display: flex; gap: 1.75rem; list-style: none; flex: 1; }
    .nav-links a { text-decoration: none; color: var(--navy); font-size: .88rem; font-weight: 500; transition: color .2s; }
    .nav-links a:hover { color: var(--gold); }
    .nav-cta {
      background: var(--gold); color: var(--navy);
      font-weight: 700; font-size: .82rem; padding: .55rem 1.2rem;
      border-radius: 6px; text-decoration: none; white-space: nowrap;
      margin-left: auto; transition: background .2s;
    }
    .nav-cta:hover { background: var(--gold-lt); }
    .hamburger { display: none; background: none; border: none; cursor: pointer; font-size: 1.4rem; color: var(--navy); }

    /* ── Hero ── */
    .hero {
      background: var(--navy);
      background-image: linear-gradient(135deg,#1a1a2e 0%,#0f3460 60%,#1a1a2e 100%);
      min-height: 82vh; display: flex; align-items: center;
      position: relative; overflow: hidden;
    }
    .hero::before {
      content: ''; position: absolute; inset: 0;
      background: url('https://images.unsplash.com/photo-1490650404312-a2175773bbf5?w=1400&auto=format&fit=crop&q=60') center/cover no-repeat;
      opacity: .12;
    }
    .hero-inner {
      position: relative; z-index: 2;
      max-width: 1200px; margin: 0 auto; padding: 4rem 2rem;
      display: grid; grid-template-columns: 1fr 1fr; gap: 3.5rem;
      align-items: center; width: 100%;
    }
    .hero-text h1 {
      font-size: clamp(1.8rem,3.5vw,2.8rem); font-weight: 800;
      color: var(--white); line-height: 1.25; margin-bottom: 1.2rem;
    }
    .hero-text h1 span { color: var(--gold); }
    .hero-text p { font-size: 1rem; color: rgba(255,255,255,.78); margin-bottom: 1.8rem; max-width: 470px; }
    .hero-badges { display: flex; gap: 1rem; flex-wrap: wrap; }
    .hero-badge {
      display: flex; align-items: center; gap: .5rem;
      background: rgba(255,255,255,.08); border: 1px solid rgba(201,168,76,.35);
      border-radius: 50px; padding: .4rem .9rem; font-size: .78rem; color: rgba(255,255,255,.85);
    }
    .hero-badge i { color: var(--gold); }

    /* ── Quote card ── */
    .quote-card { background: var(--white); border-radius: var(--radius); padding: 2rem; box-shadow: var(--shadow-lg); }
    .quote-card h2 {
      text-align: center; font-size: 1.1rem; font-weight: 700;
      letter-spacing: 1.5px; text-transform: uppercase; color: var(--navy);
      margin-bottom: 1.5rem; padding-bottom: .75rem; border-bottom: 2px solid var(--gold);
    }
    .form-group { margin-bottom: 1rem; }
    .form-group label { display: block; font-size: .78rem; font-weight: 600; text-transform: uppercase; letter-spacing: .5px; color: var(--gray); margin-bottom: .35rem; }
    .input-wrap { position: relative; display: flex; align-items: center; }
    .input-wrap i { position: absolute; left: .85rem; color: var(--gold); font-size: .9rem; z-index: 1; }
    .input-wrap input,
    .input-wrap select {
      width: 100%; padding: .75rem .85rem .75rem 2.35rem;
      border: 1.5px solid #e2e8f0; border-radius: 8px;
      font-size: .9rem; font-family: 'Poppins',sans-serif;
      color: var(--navy); background: #fafbfd;
      transition: border-color .2s, box-shadow .2s; outline: none;
    }
    .input-wrap input:focus, .input-wrap select:focus {
      border-color: var(--gold); box-shadow: 0 0 0 3px rgba(201,168,76,.12); background: var(--white);
    }
    .input-row { display: grid; grid-template-columns: 1fr 1fr; gap: .85rem; }
    .quote-btn {
      width: 100%; padding: .95rem; background: var(--navy); color: var(--white);
      border: none; border-radius: 8px; font-size: 1rem; font-weight: 700;
      letter-spacing: 1px; text-transform: uppercase; cursor: pointer; margin-top: .5rem;
      transition: background .2s, transform .1s;
      display: flex; align-items: center; justify-content: center; gap: .6rem;
    }
    .quote-btn:hover { background: #0f3460; transform: translateY(-1px); }
    .quote-btn:disabled { opacity: .65; cursor: not-allowed; transform: none; }
    .quote-note { text-align: center; font-size: .74rem; color: var(--gray); margin-top: .75rem; }
    .quote-note a { color: var(--gold); text-decoration: none; font-weight: 600; }
    .pac-container { z-index: 9999 !important; border-radius: 8px; box-shadow: var(--shadow-lg); }
    .pac-item { font-family: 'Poppins',sans-serif; font-size: .85rem; padding: .4rem .75rem; }

    /* ── Spinner ── */
    .spinner { display: inline-block; width: 18px; height: 18px; border: 2px solid rgba(255,255,255,.4); border-top-color: #fff; border-radius: 50%; animation: spin .7s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    /* ── Results ── */
    #results-section { padding: 3rem 2rem; background: var(--light); display: none; }
    #results-section.visible { display: block; }
    .results-inner { max-width: 1200px; margin: 0 auto; }
    .results-header { text-align: center; margin-bottom: 2.5rem; }
    .journey-summary {
      display: inline-flex; align-items: center; gap: .6rem;
      background: var(--navy); color: var(--white);
      border-radius: 50px; padding: .5rem 1.2rem; font-size: .83rem;
      margin-bottom: 1rem; flex-wrap: wrap; justify-content: center;
    }
    .journey-summary i { color: var(--gold); }
    .results-header h2 { font-size: 1.8rem; font-weight: 700; color: var(--navy); }
    .results-header p { color: var(--gray); font-size: .9rem; margin-top: .3rem; }

    .vehicles-grid { display: grid; grid-template-columns: repeat(auto-fit,minmax(240px,1fr)); gap: 1.5rem; }
    .vehicle-card {
      background: var(--white); border-radius: var(--radius);
      overflow: hidden; box-shadow: var(--shadow);
      transition: transform .25s, box-shadow .25s;
      display: flex; flex-direction: column;
      border: 2px solid transparent;
    }
    .vehicle-card:hover { transform: translateY(-5px); box-shadow: var(--shadow-lg); border-color: var(--gold); }
    .vehicle-img-wrap { height: 160px; background: #f0f4f8; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; }
    .vehicle-img-wrap img { width: 100%; height: 100%; object-fit: cover; }
    .vehicle-badge { position: absolute; top: .6rem; right: .6rem; background: var(--navy); color: var(--gold); font-size: .68rem; font-weight: 700; letter-spacing: .5px; padding: .2rem .55rem; border-radius: 4px; text-transform: uppercase; }
    .vehicle-body { padding: 1.2rem; flex: 1; display: flex; flex-direction: column; }
    .vehicle-name { font-size: 1.05rem; font-weight: 700; color: var(--navy); margin-bottom: .4rem; }
    .vehicle-specs { display: flex; gap: .75rem; font-size: .78rem; color: var(--gray); margin-bottom: .8rem; }
    .vehicle-specs span { display: flex; align-items: center; gap: .3rem; }
    .vehicle-features { list-style: none; margin-bottom: 1rem; }
    .vehicle-features li { font-size: .8rem; color: var(--gray); display: flex; align-items: center; gap: .4rem; margin-bottom: .25rem; }
    .vehicle-features li i { color: #22c55e; font-size: .72rem; }
    .vehicle-price-block { margin-top: auto; }
    .price-label { font-size: .72rem; color: var(--gray); text-transform: uppercase; letter-spacing: .5px; }
    .price-value { font-size: 2rem; font-weight: 800; color: var(--navy); line-height: 1; }
    .price-value .currency { font-size: 1.25rem; vertical-align: super; }
    .price-note { font-size: .72rem; color: #22c55e; margin-top: .15rem; font-weight: 600; }
    .book-btn {
      display: block; width: 100%; margin-top: 1rem; padding: .72rem;
      background: var(--gold); color: var(--navy); border: none; border-radius: 8px;
      font-size: .88rem; font-weight: 700; text-align: center; cursor: pointer;
      text-decoration: none; letter-spacing: .3px; transition: background .2s;
    }
    .book-btn:hover { background: var(--gold-lt); }

    /* ── No price ── */
    .no-price { text-align: center; padding: 2.5rem; background: var(--white); border-radius: var(--radius); box-shadow: var(--shadow); }
    .no-price i { font-size: 2.5rem; color: var(--gold); margin-bottom: 1rem; display: block; }
    .no-price h3 { font-size: 1.2rem; margin-bottom: .5rem; }
    .no-price p { color: var(--gray); font-size: .9rem; margin-bottom: .75rem; }
    .no-price a { color: var(--gold); font-weight: 600; text-decoration: none; }

    /* ── Features strip ── */
    .features-strip { background: var(--navy); padding: 2.5rem 2rem; }
    .features-inner { max-width: 1100px; margin: 0 auto; display: grid; grid-template-columns: repeat(auto-fit,minmax(180px,1fr)); gap: 1.5rem; }
    .feat-item { text-align: center; }
    .feat-icon { width: 52px; height: 52px; background: rgba(201,168,76,.15); border: 1.5px solid rgba(201,168,76,.3); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--gold); margin: 0 auto .75rem; }
    .feat-title { font-weight: 700; color: var(--white); font-size: .92rem; margin-bottom: .2rem; }
    .feat-desc  { font-size: .78rem; color: rgba(255,255,255,.6); }

    /* ── Contact ── */
    #contact { background: var(--light); padding: 3.5rem 2rem; text-align: center; }
    #contact h2 { font-size: 1.75rem; font-weight: 700; margin-bottom: .5rem; }
    #contact p  { color: var(--gray); margin-bottom: 2rem; }
    .contact-cards { display: flex; gap: 1.5rem; justify-content: center; flex-wrap: wrap; }
    .contact-card {
      background: var(--white); border-radius: var(--radius); padding: 1.5rem 2rem;
      min-width: 200px; box-shadow: var(--shadow); text-decoration: none; color: var(--navy);
      transition: transform .2s, box-shadow .2s;
      display: flex; flex-direction: column; align-items: center; gap: .4rem;
    }
    .contact-card i { font-size: 1.8rem; color: var(--gold); }
    .contact-card span { font-size: .75rem; color: var(--gray); text-transform: uppercase; letter-spacing: .5px; }
    .contact-card strong { font-size: 1rem; }
    .contact-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-lg); }

    /* ── Footer ── */
    footer { background: var(--navy); color: rgba(255,255,255,.5); text-align: center; padding: 1.5rem 2rem; font-size: .8rem; }
    footer a { color: var(--gold); text-decoration: none; }

    /* ── Responsive ── */
    @media (max-width: 900px) {
      .hero-inner { grid-template-columns: 1fr; gap: 2rem; }
      .hero-text { order: 2; text-align: center; }
      .hero-text p { margin: 0 auto 1.5rem; }
      .hero-badges { justify-content: center; }
      .quote-card { order: 1; }
      .nav-links { display: none; }
      .hamburger { display: block; }
    }
    @media (max-width: 560px) {
      .input-row { grid-template-columns: 1fr; }
      .topbar    { justify-content: center; }
    }

    /* ── Mobile drawer ── */
    .nav-drawer { display: none; position: fixed; inset: 0; background: rgba(0,0,0,.5); z-index: 200; }
    .nav-drawer.open { display: flex; }
    .nav-drawer .drawer-panel { background: var(--white); width: 270px; padding: 2rem 1.5rem; display: flex; flex-direction: column; gap: 1.25rem; }
    .nav-drawer .drawer-panel a { text-decoration: none; color: var(--navy); font-weight: 600; font-size: 1rem; padding: .5rem 0; border-bottom: 1px solid #f0f0f0; }
    .nav-drawer .close-btn { background: none; border: none; cursor: pointer; font-size: 1.4rem; color: var(--gray); align-self: flex-end; margin-bottom: .5rem; }
  </style>
</head>
<body>

<!-- ── Top bar ── -->
<div class="topbar">
  <i class="fa-solid fa-phone"></i>
  <a href="tel:<?= e($config['phone_href']) ?>"><?= e($config['phone']) ?></a>
  <i class="fa-regular fa-envelope"></i>
  <a href="mailto:<?= e($config['email']) ?>"><?= e($config['email']) ?></a>
  <div class="spacer"></div>
  <div class="social">
    <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
    <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
    <a href="https://wa.me/<?= e($config['whatsapp']) ?>" aria-label="WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
  </div>
</div>

<!-- ── Navbar ── -->
<nav class="navbar">
  <a href="/" class="nav-logo">
    <div class="logo-icon"><i class="fa-solid fa-car-side"></i></div>
    <div class="logo-text">
      <strong>Executive</strong>
      <span>Airport Cars</span>
    </div>
  </a>
  <ul class="nav-links">
    <li><a href="/">Home</a></li>
    <li><a href="#quote-form">Get a Quote</a></li>
    <li><a href="#features">Our Service</a></li>
    <li><a href="#contact">Contact</a></li>
  </ul>
  <a href="tel:<?= e($config['phone_href']) ?>" class="nav-cta">
    <i class="fa-solid fa-phone-volume"></i> <?= e($config['phone']) ?>
  </a>
  <button class="hamburger" onclick="document.getElementById('navDrawer').classList.add('open')" aria-label="Open menu">
    <i class="fa-solid fa-bars"></i>
  </button>
</nav>

<!-- Mobile drawer -->
<div class="nav-drawer" id="navDrawer">
  <div class="drawer-panel">
    <button class="close-btn" onclick="document.getElementById('navDrawer').classList.remove('open')"><i class="fa-solid fa-xmark"></i></button>
    <a href="/">Home</a>
    <a href="#quote-form">Get a Quote</a>
    <a href="#features">Our Service</a>
    <a href="#contact">Contact</a>
  </div>
  <div style="flex:1" onclick="document.getElementById('navDrawer').classList.remove('open')"></div>
</div>

<!-- ── Hero + Quote form ── -->
<section class="hero" id="quote-form">
  <div class="hero-inner">

    <div class="hero-text">
      <h1>Premium Airport Transfers <span>to and from all UK Airports</span></h1>
      <p>Fixed-price airport taxi with no hidden charges. Professional, punctual and comfortable — every time.</p>
      <div class="hero-badges">
        <span class="hero-badge"><i class="fa-solid fa-ban"></i> Free Cancellation</span>
        <span class="hero-badge"><i class="fa-solid fa-plane-arrival"></i> Flight Tracking</span>
        <span class="hero-badge"><i class="fa-solid fa-lock"></i> Fixed Price</span>
        <span class="hero-badge"><i class="fa-solid fa-headset"></i> 24/7 Support</span>
      </div>
    </div>

    <div class="quote-card">
      <h2><i class="fa-solid fa-taxi" style="color:var(--gold);margin-right:.4rem"></i> Get Your Quote</h2>

      <div class="form-group">
        <label for="pickup">Pickup Location</label>
        <div class="input-wrap">
          <i class="fa-solid fa-location-dot"></i>
          <input type="text" id="pickup" placeholder="Enter pickup address or postcode" autocomplete="off" />
        </div>
      </div>

      <div class="form-group">
        <label for="dropoff">Drop-off Location</label>
        <div class="input-wrap">
          <i class="fa-solid fa-flag-checkered"></i>
          <input type="text" id="dropoff" placeholder="Enter drop-off address or postcode" autocomplete="off" />
        </div>
      </div>

      <div class="input-row">
        <div class="form-group">
          <label for="travel-date">Date</label>
          <div class="input-wrap">
            <i class="fa-regular fa-calendar"></i>
            <input type="date" id="travel-date" />
          </div>
        </div>
        <div class="form-group">
          <label for="passengers">Passengers</label>
          <div class="input-wrap">
            <i class="fa-solid fa-users"></i>
            <select id="passengers">
              <?php for ($i = 1; $i <= 8; $i++): ?>
              <option value="<?= $i ?>" <?= $i === 4 ? 'selected' : '' ?>><?= $i ?> <?= $i === 1 ? 'Passenger' : 'Passengers' ?></option>
              <?php endfor; ?>
            </select>
          </div>
        </div>
      </div>

      <button class="quote-btn" id="quoteBtn" onclick="getQuote()">
        <i class="fa-solid fa-search"></i>
        <span id="quoteBtnText">Get Taxi Quote</span>
      </button>
      <p class="quote-note">
        Free Cancellation up to 12 hours before pickup.<br>
        Need help? <a href="tel:<?= e($config['phone_href']) ?>"><?= e($config['phone']) ?></a>
      </p>
    </div>

  </div>
</section>

<!-- ── Results ── -->
<section id="results-section">
  <div class="results-inner">
    <div class="results-header">
      <div class="journey-summary">
        <i class="fa-solid fa-location-dot"></i>
        <span id="summaryFrom">–</span>
        <i class="fa-solid fa-arrow-right"></i>
        <span id="summaryTo">–</span>
        <span id="summaryDate" style="color:rgba(255,255,255,.55)"></span>
      </div>
      <h2>Select Your Vehicle</h2>
      <p>Fixed price — no hidden charges. All prices include meet &amp; greet service.</p>
    </div>
    <div class="vehicles-grid" id="vehiclesGrid"></div>
  </div>
</section>

<!-- ── Features ── -->
<section class="features-strip" id="features">
  <div class="features-inner">
    <div class="feat-item">
      <div class="feat-icon"><i class="fa-solid fa-ban"></i></div>
      <div class="feat-title">Free Cancellation</div>
      <div class="feat-desc">Up to 12 hours before pickup</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon"><i class="fa-solid fa-plane-arrival"></i></div>
      <div class="feat-title">Flight Tracking</div>
      <div class="feat-desc">Driver monitors your flight</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon"><i class="fa-solid fa-id-card"></i></div>
      <div class="feat-title">Licensed Drivers</div>
      <div class="feat-desc">Maximum comfort and safety</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon"><i class="fa-solid fa-lock"></i></div>
      <div class="feat-title">Fixed Price</div>
      <div class="feat-desc">No hidden charges, ever</div>
    </div>
    <div class="feat-item">
      <div class="feat-icon"><i class="fa-solid fa-headset"></i></div>
      <div class="feat-title">24/7 Support</div>
      <div class="feat-desc">Always here to help</div>
    </div>
  </div>
</section>

<!-- ── Contact ── -->
<section id="contact">
  <h2>Ready to Book?</h2>
  <p>Get in touch with our team or use the quote form above.</p>
  <div class="contact-cards">
    <a class="contact-card" href="tel:<?= e($config['phone_href']) ?>">
      <i class="fa-solid fa-phone"></i>
      <span>Call Us</span>
      <strong><?= e($config['phone']) ?></strong>
    </a>
    <a class="contact-card" href="mailto:<?= e($config['email']) ?>">
      <i class="fa-regular fa-envelope"></i>
      <span>Email</span>
      <strong><?= e($config['email']) ?></strong>
    </a>
    <a class="contact-card" href="https://wa.me/<?= e($config['whatsapp']) ?>">
      <i class="fa-brands fa-whatsapp"></i>
      <span>WhatsApp</span>
      <strong>Chat With Us</strong>
    </a>
  </div>
</section>

<!-- ── Footer ── -->
<footer>
  <p>&copy; <?= date('Y') ?> <?= e($config['site_name']) ?>. All rights reserved.
    &nbsp;|&nbsp; <a href="/privacy">Privacy Policy</a>
    &nbsp;|&nbsp; <a href="/terms">Terms &amp; Conditions</a>
  </p>
</footer>

<!-- ══════════════════════════════════════════════════════════════ -->
<!--  JavaScript — config values injected safely from PHP          -->
<!-- ══════════════════════════════════════════════════════════════ -->
<script>
  // Values injected by PHP — never trust raw user input here
  const QUOTE_API  = <?= js($config['api_url']) ?>;
  const PHONE      = <?= js($config['phone']) ?>;
  const PHONE_HREF = <?= js($config['phone_href']) ?>;
  const WHATSAPP   = <?= js($config['whatsapp']) ?>;
  const BOOKING_URL = <?= js($config['booking_url']) ?>; // null = WhatsApp fallback

  // ── Vehicle catalogue ────────────────────────────────────────────
  const VEHICLES = [
    {
      key: 'saloon', name: 'Saloon', badge: 'Standard',
      img: 'https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?w=600&auto=format&fit=crop&q=70',
      desc: 'Toyota Prius, Ford Mondeo or similar', pax: 4, bags: 2,
      features: ['Meet & Greet', 'Free Waiting Time', 'Door-to-Door', 'Fixed Price'],
    },
    {
      key: 'business', name: 'Business Class', badge: 'Executive',
      img: 'https://images.unsplash.com/photo-1555215695-3004980ad54e?w=600&auto=format&fit=crop&q=70',
      desc: 'Mercedes E-Class or similar', pax: 4, bags: 2,
      features: ['Meet & Greet', 'Free Waiting Time', 'Door-to-Door', 'Fixed Price'],
    },
    {
      key: 'mpv6', name: 'MPV 6', badge: 'People Carrier',
      img: 'https://images.unsplash.com/photo-1519641471654-76ce0107ad1b?w=600&auto=format&fit=crop&q=70',
      desc: 'Mercedes Vito, VW Sharan or similar', pax: 6, bags: 4,
      features: ['Meet & Greet', 'Free Waiting Time', 'Door-to-Door', 'Fixed Price'],
    },
    {
      key: 'mpv8', name: 'MPV 8', badge: 'Mini Van',
      img: 'https://images.unsplash.com/photo-1558618666-fcd25c85cd64?w=600&auto=format&fit=crop&q=70',
      desc: 'Mercedes Sprinter, Ford Transit or similar', pax: 8, bags: 8,
      features: ['Meet & Greet', 'Free Waiting Time', 'Door-to-Door', 'Fixed Price'],
    },
  ];

  // ── State ────────────────────────────────────────────────────────
  let pickupPlace  = null;
  let dropoffPlace = null;

  // Default date to today
  document.getElementById('travel-date').valueAsDate = new Date();

  // ── Google Places Autocomplete ───────────────────────────────────
  function initAutocomplete() {
    const opts = {
      componentRestrictions: { country: 'gb' },
      fields: ['geometry', 'name', 'formatted_address', 'address_components'],
    };

    const pickupAC  = new google.maps.places.Autocomplete(document.getElementById('pickup'),  opts);
    const dropoffAC = new google.maps.places.Autocomplete(document.getElementById('dropoff'), opts);

    pickupAC.addListener('place_changed', () => {
      const p = pickupAC.getPlace();
      if (p.geometry) { pickupPlace = extractPlace(p); document.getElementById('pickup').value  = pickupPlace.address; }
    });
    dropoffAC.addListener('place_changed', () => {
      const p = dropoffAC.getPlace();
      if (p.geometry) { dropoffPlace = extractPlace(p); document.getElementById('dropoff').value = dropoffPlace.address; }
    });
  }

  function extractPlace(p) {
    const lat = p.geometry.location.lat();
    const lng = p.geometry.location.lng();
    const address = p.formatted_address || p.name || '';
    let postcode = '';
    if (p.address_components) {
      const pc = p.address_components.find(c => c.types.includes('postal_code'));
      if (pc) postcode = pc.long_name;
    }
    return { lat, lng, address, postcode };
  }

  // ── Geocode fallback (address typed without selecting suggestion) ─
  async function geocodeAddress(address) {
    return new Promise((resolve, reject) => {
      if (!window.google) return reject('Please select a location from the dropdown suggestions.');
      const gc = new google.maps.Geocoder();
      gc.geocode({ address, componentRestrictions: { country: 'gb' } }, (results, status) => {
        if (status === 'OK' && results[0]) {
          const loc = results[0].geometry.location;
          let postcode = '';
          const pc = results[0].address_components?.find(c => c.types.includes('postal_code'));
          if (pc) postcode = pc.long_name;
          resolve({ lat: loc.lat(), lng: loc.lng(), address: results[0].formatted_address, postcode });
        } else {
          reject('Could not find location: ' + address + '. Please try a more specific address.');
        }
      });
    });
  }

  // ── Main: get quote ──────────────────────────────────────────────
  async function getQuote() {
    const pickupInput  = document.getElementById('pickup').value.trim();
    const dropoffInput = document.getElementById('dropoff').value.trim();
    const date         = document.getElementById('travel-date').value;

    if (!pickupInput)  { showFieldError('pickup',  'Please enter a pickup location.');  return; }
    if (!dropoffInput) { showFieldError('dropoff', 'Please enter a drop-off location.'); return; }
    if (!date)         { alert('Please select a travel date.'); return; }

    clearFieldErrors();
    setLoading(true);

    try {
      // Geocode if the autocomplete place object doesn't match what's typed
      if (!pickupPlace  || pickupPlace.address  !== pickupInput)  pickupPlace  = await geocodeAddress(pickupInput);
      if (!dropoffPlace || dropoffPlace.address !== dropoffInput) dropoffPlace = await geocodeAddress(dropoffInput);

      const body = {
        pickup_lat:       pickupPlace.lat,
        pickup_lon:       pickupPlace.lng,
        dropoff_lat:      dropoffPlace.lat,
        dropoff_lon:      dropoffPlace.lng,
        pickup_address:   pickupPlace.address,
        dropoff_address:  dropoffPlace.address,
        pickup_postcode:  pickupPlace.postcode  || null,
        dropoff_postcode: dropoffPlace.postcode || null,
        date,
        source_url:       window.location.href,
        distance_miles:   haversineMiles(pickupPlace.lat, pickupPlace.lng, dropoffPlace.lat, dropoffPlace.lng),
      };

      const resp = await fetch(QUOTE_API, {
        method:  'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body:    JSON.stringify(body),
      });

      if (!resp.ok && resp.status !== 422) {
        throw new Error('Server error (' + resp.status + '). Please try again or call us.');
      }

      const data = await resp.json();

      if (data.success) {
        renderResults(data, pickupPlace.address, dropoffPlace.address, date);
      } else {
        showNoPrice(data.message || 'No pricing found for this route online.');
      }

    } catch (err) {
      const msg = typeof err === 'string' ? err : (err.message || 'Something went wrong. Please call us for a quote.');
      showNoPrice(msg);
    } finally {
      setLoading(false);
    }
  }

  // ── Render vehicle cards ─────────────────────────────────────────
  function renderResults(data, from, to, date) {
    const p = data.pricing;

    document.getElementById('summaryFrom').textContent = truncate(from, 35);
    document.getElementById('summaryTo').textContent   = truncate(to,   35);
    document.getElementById('summaryDate').textContent = '· ' + formatDate(date);

    const priceMap = {
      saloon:   parseFloat(p.saloon_price)   || 0,
      business: parseFloat(p.business_price) || 0,
      mpv6:     parseFloat(p.mpv6_price)     || 0,
      mpv8:     parseFloat(p.mpv8_price)     || 0,
    };

    const grid = document.getElementById('vehiclesGrid');
    grid.innerHTML = '';

    let rendered = 0;
    VEHICLES.forEach(v => {
      const price = priceMap[v.key];
      if (price <= 0) return;

      rendered++;
      const card = document.createElement('div');
      card.className = 'vehicle-card';
      card.innerHTML = `
        <div class="vehicle-img-wrap">
          <img src="${v.img}" alt="${v.name}" loading="lazy" />
          <span class="vehicle-badge">${v.badge}</span>
        </div>
        <div class="vehicle-body">
          <div class="vehicle-name">${v.name}</div>
          <div class="vehicle-specs">
            <span><i class="fa-solid fa-users"></i> Up to ${v.pax}</span>
            <span><i class="fa-solid fa-suitcase"></i> ${v.bags} Bags</span>
          </div>
          <ul class="vehicle-features">
            ${v.features.map(f => `<li><i class="fa-solid fa-check-circle"></i> ${f}</li>`).join('')}
          </ul>
          <div class="vehicle-price-block">
            <div class="price-label">Total one-way price</div>
            <div class="price-value"><span class="currency">£</span>${price.toFixed(2)}</div>
            <div class="price-note"><i class="fa-solid fa-lock" style="font-size:.65rem"></i> Fixed Price — No Hidden Charges</div>
          </div>
          <a href="${bookingLink(v.name, price, from, to, date)}" class="book-btn" target="_blank" rel="noopener">
            Book Now →
          </a>
        </div>`;
      grid.appendChild(card);
    });

    if (rendered === 0) {
      showNoPrice('All vehicle prices for this route are currently unavailable. Please call us.');
      return;
    }

    const sec = document.getElementById('results-section');
    sec.classList.add('visible');
    setTimeout(() => sec.scrollIntoView({ behavior: 'smooth', block: 'start' }), 80);
  }

  function showNoPrice(msg) {
    const grid = document.getElementById('vehiclesGrid');
    grid.innerHTML = `
      <div class="no-price" style="grid-column:1/-1">
        <i class="fa-solid fa-circle-info"></i>
        <h3>No Online Price Available</h3>
        <p>${escHtml(msg)}</p>
        <p>Call us and we'll quote you instantly:<br>
           <a href="tel:${PHONE_HREF}"><strong>${PHONE}</strong></a></p>
      </div>`;
    document.getElementById('results-section').classList.add('visible');
    setTimeout(() => document.getElementById('results-section').scrollIntoView({ behavior: 'smooth' }), 80);
  }

  // ── Build the "Book Now" URL ─────────────────────────────────────
  // If $config['booking_url'] is set, pass vehicle/price as query-string params.
  // Otherwise fall back to a WhatsApp message.
  function bookingLink(vehicle, price, from, to, date) {
    if (BOOKING_URL) {
      const u = new URL(BOOKING_URL);
      u.searchParams.set('vehicle', vehicle);
      u.searchParams.set('price',   price.toFixed(2));
      u.searchParams.set('pickup',  from);
      u.searchParams.set('dropoff', to);
      u.searchParams.set('date',    date);
      return u.toString();
    }
    // WhatsApp fallback
    const msg = `Hi, I'd like to book a ${vehicle} transfer.\n\nFrom: ${from}\nTo: ${to}\nDate: ${formatDate(date)}\nPrice: £${price.toFixed(2)}\n\nPlease confirm availability.`;
    return `https://wa.me/${WHATSAPP}?text=${encodeURIComponent(msg)}`;
  }

  // ── Field error helpers ──────────────────────────────────────────
  function showFieldError(id, msg) {
    const wrap = document.getElementById(id)?.closest('.form-group');
    if (!wrap) return;
    clearFieldErrors();
    const span = document.createElement('span');
    span.className = 'field-error';
    span.style.cssText = 'color:#ef4444;font-size:.75rem;display:block;margin-top:.25rem';
    span.textContent = msg;
    wrap.appendChild(span);
    document.getElementById(id)?.focus();
  }
  function clearFieldErrors() {
    document.querySelectorAll('.field-error').forEach(el => el.remove());
  }

  // ── Loading state ────────────────────────────────────────────────
  function setLoading(on) {
    const btn  = document.getElementById('quoteBtn');
    const text = document.getElementById('quoteBtnText');
    btn.disabled       = on;
    text.innerHTML     = on ? '<span class="spinner"></span>&nbsp;Calculating…' : 'Get Taxi Quote';
  }

  // ── Haversine distance (miles) ───────────────────────────────────
  function haversineMiles(lat1, lon1, lat2, lon2) {
    const R = 3958.8;
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2)**2 + Math.cos(lat1*Math.PI/180)*Math.cos(lat2*Math.PI/180)*Math.sin(dLon/2)**2;
    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
  }

  // ── Misc helpers ─────────────────────────────────────────────────
  function truncate(s, n) { return s.length > n ? s.slice(0, n-1) + '…' : s; }
  function formatDate(d)  {
    if (!d) return '';
    return new Date(d + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
  }
  function escHtml(s) {
    return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
  }

  // Enter-key shortcut on form fields
  ['pickup','dropoff','travel-date'].forEach(id => {
    document.getElementById(id)?.addEventListener('keydown', e => { if (e.key === 'Enter') getQuote(); });
  });
</script>

<!-- Google Maps Places API
     Replace YOUR_GOOGLE_MAPS_API_KEY below with your actual key.
     Required APIs: Maps JavaScript API + Places API
     Restrict the key to your domain in Google Cloud Console. -->
<script
  src="https://maps.googleapis.com/maps/api/js?key=<?= e($config['maps_api_key']) ?>&libraries=places&callback=initAutocomplete"
  async defer
  onerror="console.warn('Google Maps failed to load. Autocomplete is disabled.')">
</script>

</body>
</html>
