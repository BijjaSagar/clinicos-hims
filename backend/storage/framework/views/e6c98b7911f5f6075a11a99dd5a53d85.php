<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ClinicOS — Specialty-First EMR for India</title>
  <link rel="icon" type="image/png" href="<?php echo e(asset('images/clinicos-logo.png')); ?>" />
  <meta name="theme-color" content="#ffffff" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Sora:wght@400;600;700;800&family=Instrument+Serif:ital@0;1&display=swap" rel="stylesheet" />
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    :root {
      --blue: #1447e6;
      --blue-dark: #0f35b8;
      --blue-light: #eff3ff;
      --teal: #0891b2;
      --teal-light: #e0f2fe;
      --green: #059669;
      --green-light: #ecfdf5;
      --amber: #d97706;
      --amber-light: #fffbeb;
      --dark: #0d1117;
      --dark2: #161b27;
      --text: #1a1f2e;
      --text2: #4b5563;
      --text3: #9ca3af;
      --border: #e5e7eb;
      --bg: #f9fafb;
      --white: #ffffff;
      --radius: 12px;
      --radius-lg: 20px;
      --shadow: 0 1px 3px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.06);
      --shadow-md: 0 4px 12px rgba(0,0,0,.1), 0 12px 40px rgba(0,0,0,.08);
    }

    html { scroll-behavior: smooth; }

    body {
      font-family: 'Inter', system-ui, sans-serif;
      color: var(--text);
      background: var(--white);
      line-height: 1.6;
      -webkit-font-smoothing: antialiased;
    }

    /* ── NAV ── */
    :root { --nav-h: 64px; }
    /* Taller bar on large screens so the wordmark can match link + CTA scale */
    @media (min-width: 1025px) {
      :root { --nav-h: 76px; }
    }
    .site-nav {
      position: sticky; top: 0; z-index: 200;
      background: rgba(255, 255, 255, 0.88);
      backdrop-filter: blur(20px);
      -webkit-backdrop-filter: blur(20px);
      border-bottom: 1px solid rgba(226, 232, 240, 0.9);
      box-shadow: 0 1px 0 rgba(255, 255, 255, 0.8) inset;
    }
    /* Desktop: logo | centered links | CTAs */
    .nav-inner {
      position: relative;
      z-index: 210;
      max-width: 1200px; margin: 0 auto;
      padding: 0 24px;
      height: var(--nav-h);
      display: grid;
      grid-template-columns: minmax(0, 1fr) auto minmax(0, 1fr);
      align-items: center;
      column-gap: 20px;
    }
    /* Do not use min-width:0 here — it lets the grid shrink the logo below its image size */
    .nav-inner .logo { justify-self: start; min-width: max-content; }
    .nav-inner .nav-links { justify-self: center; }
    .nav-inner .nav-cta { justify-self: end; display: flex; align-items: center; gap: 4px; flex-wrap: nowrap; }
    .nav-inner .nav-burger { display: none; }
    .logo {
      display: flex; align-items: center;
      text-decoration: none; color: var(--text);
      flex-shrink: 0;
    }
    .logo-img {
      width: auto;
      height: 48px;
      max-height: none;
      /* Wide cap — wordmark is horizontal; don’t starve width */
      max-width: min(78vw, 340px);
      object-fit: contain;
      object-position: left center;
      display: block;
      flex-shrink: 0;
    }
    @media (min-width: 480px) {
      .logo-img { height: 52px; max-width: min(78vw, 380px); }
    }
    @media (min-width: 900px) {
      .logo-img { height: 56px; max-width: min(70vw, 420px); }
    }
    @media (min-width: 1025px) {
      .logo-img {
        height: 60px;
        max-width: min(52vw, 440px);
      }
    }
    .nav-links {
      display: flex; align-items: center; gap: clamp(4px, 1.2vw, 12px); list-style: none;
      flex-shrink: 0;
    }
    .nav-links li { flex-shrink: 0; }
    .nav-links a {
      display: inline-flex; align-items: center;
      padding: 8px 12px; border-radius: 9px;
      text-decoration: none; color: #475569; font-size: 13.5px; font-weight: 500;
      transition: background .15s ease, color .15s ease;
      white-space: nowrap;
    }
    .nav-links a:hover { background: #f1f5f9; color: #0f172a; }
    .nav-links a:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }
    .nav-cta .btn { flex-shrink: 0; }
    .nav-burger {
      display: none; align-items: center; justify-content: center;
      width: 44px; height: 44px; border-radius: 11px;
      border: 1px solid #e2e8f0; background: #fff;
      cursor: pointer; flex-shrink: 0;
      transition: background .15s ease, border-color .15s ease;
    }
    .nav-burger:hover { background: #f8fafc; border-color: #cbd5e1; }
    .nav-burger:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }
    .nav-burger-lines { position: relative; width: 22px; height: 16px; display: block; }
    .nav-burger-lines span {
      position: absolute; left: 0; right: 0; height: 2px; border-radius: 1px;
      background: #1e293b;
      transition: transform .22s ease, opacity .18s ease, top .22s ease;
    }
    .nav-burger-lines span:nth-child(1) { top: 2px; }
    .nav-burger-lines span:nth-child(2) { top: 7px; }
    .nav-burger-lines span:nth-child(3) { top: 12px; }
    body.nav-drawer-open .nav-burger-lines span:nth-child(1) { top: 7px; transform: rotate(45deg); }
    body.nav-drawer-open .nav-burger-lines span:nth-child(2) { opacity: 0; transform: scaleX(0); }
    body.nav-drawer-open .nav-burger-lines span:nth-child(3) { top: 7px; transform: rotate(-45deg); }
    /* Tablet / mobile: dim overlay */
    .nav-backdrop {
      display: none;
      position: fixed; left: 0; right: 0; bottom: 0;
      top: var(--nav-h);
      z-index: 150;
      background: rgba(15, 23, 42, 0.45);
      opacity: 0;
      visibility: hidden;
      transition: opacity .25s ease, visibility .25s ease;
    }
    body.nav-drawer-open .nav-backdrop {
      display: block;
      opacity: 1;
      visibility: visible;
    }
    /*
      Drawer lives OUTSIDE <nav> so position:fixed is viewport-relative (backdrop-filter on nav
      would otherwise trap fixed children → “short” panel on mobile).
    */
    .nav-drawer {
      position: fixed;
      top: 0;
      right: 0;
      bottom: 0;
      left: auto;
      z-index: 160;
      width: min(100%, 400px);
      max-width: 100vw;
      /* top+bottom:0 = full viewport height (avoids 100vh mobile toolbar bugs) */
      display: flex;
      flex-direction: column;
      background: #fff;
      border-left: 1px solid #e2e8f0;
      box-shadow: -8px 0 40px rgba(15, 23, 42, 0.12);
      padding: 0;
      transform: translateX(100%);
      visibility: hidden;
      transition: transform .28s cubic-bezier(0.4, 0, 0.2, 1), visibility .28s;
    }
    body.nav-drawer-open .nav-drawer {
      transform: translateX(0);
      visibility: visible;
    }
    .nav-drawer-body {
      flex: 1;
      min-height: 0;
      overflow-y: auto;
      overflow-x: hidden;
      -webkit-overflow-scrolling: touch;
      overscroll-behavior: contain;
      padding: calc(var(--nav-h) + 12px) 20px 16px;
      padding-top: calc(var(--nav-h) + 12px + env(safe-area-inset-top, 0px));
    }
    .nav-drawer-label {
      font-size: 11px; font-weight: 700; letter-spacing: .1em; text-transform: uppercase;
      color: #94a3b8; margin-bottom: 12px; padding-left: 4px;
    }
    .nav-drawer-links { list-style: none; margin: 0; padding: 0; display: flex; flex-direction: column; gap: 4px; }
    .nav-drawer-links li { margin: 0; }
    .nav-drawer-links a {
      display: block; padding: 14px 14px; border-radius: 10px;
      font-size: 16px; font-weight: 600; color: #0f172a;
      text-decoration: none;
      transition: background .15s ease, color .15s ease;
    }
    .nav-drawer-links a:hover, .nav-drawer-links a:active { background: #f1f5f9; color: var(--blue); }
    .nav-drawer-links a:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }
    .nav-drawer-foot {
      flex-shrink: 0;
      margin-top: auto;
      padding: 16px 20px calc(20px + env(safe-area-inset-bottom, 0px));
      border-top: 1px solid #e2e8f0;
      background: #fafbfc;
    }
    .nav-drawer-cta {
      display: flex;
      flex-direction: column;
      gap: 10px;
    }
    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 18px; border-radius: 8px; font-size: 14px; font-weight: 600; cursor: pointer; border: none; transition: all .2s; text-decoration: none; }
    .btn-ghost { background: transparent; color: var(--text2); }
    .btn-ghost:hover { background: var(--bg); color: var(--text); }
    .btn-primary { background: var(--blue); color: white; }
    .btn-primary:hover { background: var(--blue-dark); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(20,71,230,.3); }
    .btn-lg { padding: 13px 28px; font-size: 15px; border-radius: 10px; }
    .btn-outline { background: transparent; color: var(--blue); border: 1.5px solid var(--blue); }
    .btn-outline:hover { background: var(--blue-light); }

    /* ── HERO ── */
    .hero-section {
      background: linear-gradient(180deg, #f4f6f9 0%, #ffffff 55%);
      border-bottom: 1px solid #eef0f4;
    }
    .hero {
      padding: 96px 24px 88px;
      max-width: 1200px; margin: 0 auto;
      display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center;
    }
    .hero-badge {
      display: inline-flex; align-items: center; gap: 8px;
      background: var(--blue-light); color: var(--blue);
      padding: 6px 14px; border-radius: 100px; font-size: 13px; font-weight: 600;
      margin-bottom: 24px;
    }
    .hero-badge span { width: 6px; height: 6px; border-radius: 50%; background: var(--blue); animation: pulse 2s infinite; }
    @keyframes pulse { 0%,100% { opacity:1; } 50% { opacity:.4; } }
    h1 {
      font-family: 'Sora', sans-serif;
      font-size: clamp(36px, 4vw, 54px); font-weight: 800; line-height: 1.1;
      letter-spacing: -1.5px; margin-bottom: 20px; color: var(--dark);
    }
    h1 .accent { color: var(--blue); }
    .hero-sub { font-size: 17px; color: var(--text2); line-height: 1.7; margin-bottom: 32px; max-width: 520px; }
    /* Two primary actions on row 1; secondary CTA full width row 2 — no orphan “staircase” button */
    .hero-actions {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 12px;
      margin-bottom: 44px;
      max-width: 500px;
    }
    .hero-actions .hero-cta-secondary {
      grid-column: 1 / -1;
      justify-content: center;
    }
    .hero-stats { display: flex; gap: clamp(20px, 4vw, 36px); flex-wrap: wrap; }
    .stat-item { }
    .stat-num { font-family: 'Sora', sans-serif; font-size: 24px; font-weight: 700; color: var(--dark); }
    .stat-label { font-size: 13px; color: var(--text3); margin-top: 2px; }

    .hero-visual {
      position: relative;
    }
    .hero-card-main {
      background: var(--dark2);
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 24px 80px rgba(0,0,0,.2);
    }
    .card-topbar {
      background: #1e2535;
      padding: 12px 20px;
      display: flex; align-items: center; gap: 8px;
    }
    .dot { width: 10px; height: 10px; border-radius: 50%; }
    .dot-r { background: #ff5f57; }
    .dot-y { background: #febc2e; }
    .dot-g { background: #28c840; }
    .card-topbar-title { margin-left: 8px; font-size: 12px; color: #6b7280; font-weight: 500; }
    .card-body { padding: 20px; }
    .patient-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
    .patient-avatar {
      width: 44px; height: 44px; border-radius: 50%;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      display: flex; align-items: center; justify-content: center;
      color: white; font-weight: 700; font-size: 16px;
    }
    .patient-info h4 { color: white; font-size: 14px; font-weight: 600; }
    .patient-info p { color: #6b7280; font-size: 12px; margin-top: 2px; }
    .badge-specialty {
      margin-left: auto;
      background: rgba(20,71,230,.2); color: #93c5fd;
      padding: 4px 10px; border-radius: 100px; font-size: 11px; font-weight: 600;
    }
    .emr-section-title { color: #6b7280; font-size: 10px; font-weight: 600; letter-spacing: .08em; text-transform: uppercase; margin-bottom: 8px; }
    .body-map-wrapper {
      background: #111827; border-radius: 10px; padding: 12px;
      display: flex; align-items: center; gap: 12px; margin-bottom: 12px;
    }
    .body-svg { width: 56px; flex-shrink: 0; }
    .lesion-tags { display: flex; flex-wrap: wrap; gap: 6px; }
    .lesion-tag {
      background: rgba(239,68,68,.15); color: #fca5a5;
      padding: 3px 9px; border-radius: 6px; font-size: 11px;
    }
    .lesion-tag.g { background: rgba(16,185,129,.15); color: #6ee7b7; }
    .lesion-tag.b { background: rgba(59,130,246,.15); color: #93c5fd; }
    .fields-row { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-bottom: 12px; }
    .field-block { background: #1e2535; border-radius: 8px; padding: 8px 10px; }
    .field-label { color: #6b7280; font-size: 10px; font-weight: 500; }
    .field-value { color: #e5e7eb; font-size: 12px; font-weight: 600; margin-top: 2px; }
    .ai-strip {
      background: linear-gradient(135deg, rgba(20,71,230,.15), rgba(8,145,178,.15));
      border: 1px solid rgba(20,71,230,.2);
      border-radius: 8px; padding: 8px 12px;
      display: flex; align-items: center; gap: 8px;
    }
    .ai-icon { width: 24px; height: 24px; border-radius: 6px; background: var(--blue); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .ai-text { color: #93c5fd; font-size: 11px; }
    .ai-text strong { display: block; font-weight: 600; font-size: 12px; }

    .floating-card {
      position: absolute;
      background: white;
      border-radius: 14px;
      box-shadow: var(--shadow-md);
      padding: 12px 16px;
      font-size: 12px;
    }
    .fc-whatsapp { bottom: -20px; left: -24px; display: flex; align-items: center; gap: 8px; min-width: 220px; }
    .fc-abha { top: 24px; right: -24px; display: flex; align-items: center; gap: 8px; min-width: 180px; }
    .wa-icon { width: 32px; height: 32px; border-radius: 8px; background: #25D366; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .abha-icon { width: 32px; height: 32px; border-radius: 8px; background: linear-gradient(135deg,#f97316,#ef4444); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .fc-text strong { font-weight: 600; font-size: 12px; color: var(--text); display: block; }
    .fc-text span { color: var(--text3); font-size: 11px; }

    /* ── SPECIALTIES ── */
    .section { padding: 80px 24px; }
    .section-inner { max-width: 1200px; margin: 0 auto; }
    .section-label { color: var(--blue); font-size: 13px; font-weight: 600; letter-spacing: .06em; text-transform: uppercase; margin-bottom: 12px; }
    h2 {
      font-family: 'Sora', sans-serif;
      font-size: clamp(28px, 3vw, 42px); font-weight: 800; line-height: 1.15;
      letter-spacing: -1px; color: var(--dark); margin-bottom: 16px;
    }
    .specialties-bg h2 .headline-accent {
      font-family: 'Instrument Serif', Georgia, 'Times New Roman', serif;
      font-style: italic;
      font-weight: 500;
      color: var(--blue);
    }
    .section-sub { font-size: 17px; color: var(--text2); max-width: 560px; line-height: 1.7; }

    .specialties-bg { background: var(--bg); }
    .specialties-tabs { display: flex; gap: 10px; margin: 48px 0 36px; flex-wrap: wrap; }
    .spec-tab {
      display: inline-flex; align-items: center; gap: 8px;
      padding: 10px 16px; border-radius: 999px;
      background: white; border: 1px solid #e2e8f0;
      font-size: 13px; font-weight: 600; color: var(--text2);
      cursor: pointer; transition: border-color .2s, color .2s, box-shadow .2s, background .2s;
      box-shadow: 0 1px 2px rgba(15, 23, 42, .04);
    }
    .spec-tab:hover { border-color: #c7d2fe; color: var(--blue); box-shadow: 0 2px 8px rgba(20, 71, 230, .08); }
    .spec-tab.active { background: var(--blue); border-color: var(--blue); color: white; box-shadow: 0 4px 14px rgba(20, 71, 230, .25); }
    .spec-tab-icon { display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
    .spec-tab-icon svg { width: 18px; height: 18px; }
    .spec-tab-icon svg path { fill: none; stroke: currentColor; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; }
    .spec-content { display: none; }
    .spec-content.active { display: grid; grid-template-columns: 1fr 1fr; gap: 48px; align-items: start; }
    .spec-features h3 { font-family: 'Sora', sans-serif; font-size: 24px; font-weight: 700; color: var(--dark); margin-bottom: 8px; }
    .spec-features p { color: var(--text2); margin-bottom: 24px; line-height: 1.7; }
    .spec-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .spec-list li { display: flex; align-items: flex-start; gap: 10px; font-size: 14px; color: var(--text2); }
    .check { width: 20px; height: 20px; border-radius: 50%; background: var(--green-light); color: var(--green); display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 11px; margin-top: 1px; }
    .spec-card-preview {
      background: var(--dark2); border-radius: 16px; overflow: hidden;
      box-shadow: 0 12px 40px rgba(0,0,0,.15);
    }
    .spec-card-header {
      background: #1e2535; padding: 14px 20px;
      display: flex; align-items: center; gap: 10px;
    }
    .spec-card-header-icon {
      width: 34px; height: 34px; border-radius: 9px;
      background: rgba(255, 255, 255, .12);
      border: 1px solid rgba(255, 255, 255, .2);
      display: flex; align-items: center; justify-content: center;
    }
    .spec-card-header-icon svg { width: 18px; height: 18px; }
    .spec-card-header-icon svg path { fill: none; stroke: #fff; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; }
    .spec-photo-strip {
      display: flex; align-items: center; gap: 10px;
      background: rgba(16, 185, 129, .1);
      border: 1px solid rgba(16, 185, 129, .25);
      border-radius: 8px;
      padding: 10px 12px;
      font-size: 12px;
      color: #6ee7b7;
    }
    .spec-photo-strip svg { width: 18px; height: 18px; flex-shrink: 0; }
    .spec-photo-strip svg path { fill: none; stroke: #34d399; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; }
    .spec-card-header h4 { color: white; font-size: 13px; font-weight: 600; }
    .spec-card-header p { color: #6b7280; font-size: 11px; }
    .spec-card-body { padding: 16px 20px; }

    /* ── MODULES GRID (editorial + stroke icons) ── */
    #features.features-section { background: linear-gradient(180deg, #fafbfc 0%, #ffffff 45%); border-top: 1px solid #eef0f4; border-bottom: 1px solid #eef0f4; }
    .features-section .section-label { letter-spacing: .12em; font-size: 0.75rem; color: #64748b; font-weight: 600; }
    .features-section h2.features-heading {
      font-family: 'Instrument Serif', Georgia, 'Times New Roman', serif;
      font-weight: 500; font-style: normal;
      font-size: clamp(2rem, 4vw, 2.85rem);
      font-weight: 500; line-height: 1.12; letter-spacing: -0.02em;
      color: #0f1419; max-width: 18ch;
    }
    .features-section .section-sub { max-width: 38rem; color: #64748b; font-size: 1.05rem; line-height: 1.65; }
    .modules-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 52px; }
    .module-card {
      background: #fff; border: 1px solid #e8eaef; border-radius: 16px;
      padding: 26px 24px 24px; transition: border-color .2s, box-shadow .2s, transform .2s;
    }
    .module-card:hover {
      border-color: #c7d2fe; box-shadow: 0 12px 40px rgba(15, 23, 42, .06);
      transform: translateY(-2px);
    }
    .module-icon {
      width: 44px; height: 44px; border-radius: 11px; margin-bottom: 18px;
      display: flex; align-items: center; justify-content: center;
      background: #f8fafc; border: 1px solid #e8eaef; color: #334155;
    }
    .module-icon svg { width: 22px; height: 22px; flex-shrink: 0; stroke-width: 1.75; }
    .module-card h3 { font-size: 1.05rem; font-weight: 600; color: #0f1419; margin-bottom: 8px; letter-spacing: -0.02em; }
    .module-card p { font-size: 0.875rem; color: #64748b; line-height: 1.62; }
    .module-chip {
      display: inline-block; margin-top: 14px;
      background: transparent; color: #475569; border: 1px solid #e2e8f0;
      padding: 4px 11px; border-radius: 999px; font-size: 11px; font-weight: 600;
    }

    /* ── HOW IT WORKS ── */
    .steps-bg { background: var(--dark); }
    .steps-bg h2, .steps-bg .section-label { color: white; }
    .steps-bg .section-sub { color: #94a3b8; }
    .steps-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px; margin-top: 56px; position: relative; }
    .steps-grid::before {
      content: ''; position: absolute; top: 36px; left: 20%; right: 20%;
      height: 1px; background: linear-gradient(90deg, transparent, #334155, #334155, transparent);
    }
    .step { padding: 32px; position: relative; }
    .step-num {
      width: 48px; height: 48px; border-radius: 50%;
      border: 1.5px solid #334155; background: var(--dark);
      display: flex; align-items: center; justify-content: center;
      font-size: 16px; font-weight: 700; color: #94a3b8; margin-bottom: 24px;
    }
    .step.active-step .step-num { border-color: var(--blue); background: var(--blue); color: white; }
    .step h3 { color: white; font-size: 17px; font-weight: 700; margin-bottom: 10px; }
    .step p { color: #64748b; font-size: 14px; line-height: 1.65; }

    /* ── ABDM ── */
    .abdm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; margin-top: 56px; }
    .abdm-features { display: flex; flex-direction: column; gap: 20px; }
    .abdm-item { display: flex; gap: 16px; align-items: flex-start; }
    .abdm-dot {
      width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
      background: var(--green-light); display: flex; align-items: center; justify-content: center;
      color: var(--green); font-size: 16px;
    }
    .abdm-item h4 { font-size: 15px; font-weight: 700; color: var(--dark); margin-bottom: 4px; }
    .abdm-item p { font-size: 13px; color: var(--text2); line-height: 1.6; }
    .abdm-visual {
      background: var(--dark2); border-radius: 20px; padding: 28px;
      box-shadow: 0 16px 56px rgba(0,0,0,.12);
    }
    .abdm-visual h4 { color: white; font-size: 14px; font-weight: 600; margin-bottom: 20px; }
    .abdm-milestone { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 16px; }
    .milestone-dot {
      width: 32px; height: 32px; border-radius: 8px; flex-shrink: 0; font-size: 11px;
      display: flex; align-items: center; justify-content: center; font-weight: 700;
    }
    .ms-done { background: rgba(5,150,105,.2); color: #6ee7b7; }
    .ms-prog { background: rgba(20,71,230,.2); color: #93c5fd; }
    .ms-pend { background: #1e2535; color: #6b7280; }
    .milestone-text h5 { font-size: 13px; font-weight: 600; color: #e5e7eb; margin-bottom: 2px; }
    .milestone-text p { font-size: 12px; color: #6b7280; line-height: 1.5; }

    /* ── AI ── */
    .ai-bg { background: linear-gradient(135deg, #0d1117 0%, #0d1f3c 100%); }
    .ai-bg h2 { color: white; }
    .ai-bg .section-label { color: #60a5fa; }
    .ai-bg .section-sub { color: #94a3b8; }
    .ai-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 64px; align-items: center; margin-top: 56px; }
    .ai-cards { display: flex; flex-direction: column; gap: 16px; }
    .ai-card {
      background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.08);
      border-radius: 14px; padding: 20px; display: flex; gap: 16px; align-items: flex-start;
    }
    .ai-card-icon { width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; font-size: 18px; }
    .ai-card h4 { color: white; font-size: 14px; font-weight: 600; margin-bottom: 6px; }
    .ai-card p { color: #6b7280; font-size: 13px; line-height: 1.6; }
    .dictation-demo {
      background: rgba(255,255,255,.04); border: 1px solid rgba(255,255,255,.06);
      border-radius: 16px; padding: 24px;
    }
    .demo-header { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
    .rec-btn {
      width: 36px; height: 36px; border-radius: 50%;
      background: rgba(239,68,68,.2); border: 1.5px solid rgba(239,68,68,.4);
      display: flex; align-items: center; justify-content: center; color: #fca5a5; font-size: 14px;
    }
    .demo-header h4 { color: white; font-size: 13px; font-weight: 600; }
    .demo-header p { color: #6b7280; font-size: 11px; }
    .transcript-box {
      background: rgba(0,0,0,.3); border-radius: 10px; padding: 14px;
      font-size: 13px; color: #94a3b8; line-height: 1.6; margin-bottom: 16px;
      border-left: 2px solid #3b82f6;
      font-style: italic;
    }
    .mapped-fields { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
    .map-field { background: rgba(16,185,129,.08); border: 1px solid rgba(16,185,129,.2); border-radius: 8px; padding: 8px 12px; }
    .map-field span { color: #6b7280; font-size: 10px; font-weight: 500; display: block; }
    .map-field strong { color: #6ee7b7; font-size: 12px; }

    /* ── PRICING ── */
    .pricing-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 56px; }
    .price-card {
      background: white; border: 1.5px solid var(--border);
      border-radius: var(--radius-lg); padding: 32px; position: relative;
    }
    .price-card.popular {
      border-color: var(--blue);
      box-shadow: 0 8px 40px rgba(20,71,230,.12);
    }
    .popular-badge {
      position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
      background: var(--blue); color: white;
      padding: 4px 14px; border-radius: 100px; font-size: 11px; font-weight: 700;
      white-space: nowrap;
    }
    .price-plan { font-size: 13px; font-weight: 600; color: var(--text3); margin-bottom: 8px; }
    .price-hi { font-size: 12px; color: var(--text3); }
    .price-amount { margin: 16px 0; }
    .price-amount .rupee { font-size: 20px; font-weight: 700; color: var(--dark); vertical-align: top; margin-top: 6px; display: inline-block; }
    .price-amount .num { font-family: 'Sora', sans-serif; font-size: 44px; font-weight: 800; color: var(--dark); line-height: 1; }
    .price-amount .period { font-size: 13px; color: var(--text3); }
    .price-desc { font-size: 13px; color: var(--text2); line-height: 1.6; margin-bottom: 24px; padding-bottom: 24px; border-bottom: 1px solid var(--border); }
    .price-features { list-style: none; display: flex; flex-direction: column; gap: 10px; margin-bottom: 28px; }
    .price-features li { display: flex; align-items: center; gap: 8px; font-size: 13px; color: var(--text2); }
    .price-features li::before { content: '✓'; width: 18px; height: 18px; border-radius: 50%; background: var(--green-light); color: var(--green); font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    /* ── COMPARISON ── */
    .compare-table { width: 100%; border-collapse: collapse; margin-top: 48px; }
    .compare-table th { padding: 16px 20px; text-align: left; font-size: 13px; font-weight: 700; color: var(--text3); background: var(--bg); }
    .compare-table th:first-child { border-radius: 12px 0 0 12px; }
    .compare-table th:last-child { border-radius: 0 12px 12px 0; text-align: center; color: var(--blue); }
    .compare-table td { padding: 14px 20px; font-size: 14px; color: var(--text2); border-bottom: 1px solid var(--border); }
    .compare-table tr:last-child td { border-bottom: none; }
    .compare-table td:last-child { text-align: center; }
    .yes { color: var(--green); font-size: 18px; }
    .no { color: var(--text3); font-size: 18px; }
    .partial { color: var(--amber); font-size: 13px; font-weight: 600; }

    /* ── CTA ── */
    .cta-section {
      background: linear-gradient(135deg, var(--blue) 0%, #0f35b8 100%);
      padding: 80px 24px; text-align: center;
    }
    .cta-section h2 { color: white; max-width: 600px; margin: 0 auto 16px; }
    .cta-section p { color: rgba(255,255,255,.7); font-size: 17px; max-width: 480px; margin: 0 auto 40px; }
    .cta-actions { display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; }
    .btn-white { background: white; color: var(--blue); }
    .btn-white:hover { background: var(--blue-light); transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.15); }
    .btn-outline-white { background: transparent; color: white; border: 1.5px solid rgba(255,255,255,.4); }
    .btn-outline-white:hover { background: rgba(255,255,255,.1); }

    /* ── FOOTER ── */
    footer { background: var(--dark); color: #94a3b8; padding: 64px 24px 32px; }
    .footer-inner { max-width: 1200px; margin: 0 auto; }
    .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 48px; }
    .footer-brand p { font-size: 13px; color: #64748b; line-height: 1.7; margin-top: 12px; max-width: 260px; }
    .footer-col h5 { color: white; font-size: 13px; font-weight: 600; margin-bottom: 16px; }
    .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .footer-col a { color: #64748b; font-size: 13px; text-decoration: none; transition: color .2s; }
    .footer-col a:hover { color: white; }
    .footer-bottom { border-top: 1px solid #1e293b; padding-top: 28px; display: flex; justify-content: space-between; align-items: center; }
    .footer-bottom p { font-size: 12px; color: #475569; }
    .footer-logo { color: white; font-family: 'Sora', sans-serif; font-weight: 700; font-size: 16px; }
    .trust-chips { display: flex; gap: 12px; }
    .trust-chip {
      display: flex; align-items: center; gap: 6px;
      background: rgba(255,255,255,.04); border: 1px solid #1e293b;
      border-radius: 8px; padding: 6px 12px; font-size: 11px; color: #64748b;
    }

    /* Tablet / phone: hamburger + slide drawer (≤1024px) */
    @media (max-width: 1024px) {
      :root { --nav-h: 56px; }
      .nav-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        padding: 0 16px;
        height: var(--nav-h);
      }
      .nav-inner .logo { margin-right: auto; min-width: 0; }
      .nav-links { display: none !important; }
      .nav-inner .nav-burger { display: flex !important; }
      .nav-cta { gap: 8px; flex-shrink: 0; }
      body.nav-drawer-open { overflow: hidden; touch-action: none; }
    }
    @media (min-width: 1025px) {
      .nav-backdrop { display: none !important; visibility: hidden !important; opacity: 0 !important; }
      .nav-drawer { display: none !important; }
      body.nav-drawer-open { overflow: auto !important; touch-action: auto !important; }
    }
    /* mobile */
    @media (max-width: 768px) {
      .hero { grid-template-columns: 1fr; gap: 48px; padding: 64px 20px 48px; }
      .hero-visual { display: none; }
      .modules-grid { grid-template-columns: 1fr; }
      .pricing-grid { grid-template-columns: 1fr; }
      .steps-grid { grid-template-columns: 1fr; }
      .steps-grid::before { display: none; }
      .spec-content.active, .abdm-grid, .ai-grid { grid-template-columns: 1fr; }
      .footer-grid { grid-template-columns: 1fr; gap: 28px; }
      .nav-cta .btn-ghost { display: none; }
      .nav-cta .btn-primary { padding: 10px 14px; font-size: 13px; }
      .hero-stats { flex-wrap: wrap; gap: 20px 28px; }
      .fields-row { grid-template-columns: 1fr; }
      h2 { font-size: 28px; }
    }
  </style>
</head>
<body>

<!-- NAV -->
<nav class="site-nav" aria-label="Primary">
  <div class="nav-inner">
    <a class="logo" href="<?php echo e(url('/')); ?>" aria-label="ClinicOS — Home">
      <img
        class="logo-img"
        src="<?php echo e(asset('images/clinicos-logo.png')); ?>"
        alt="ClinicOS"
        width="440"
        height="60"
        loading="eager"
        decoding="async"
      />
    </a>
    <ul class="nav-links">
      <li><a href="<?php echo e(route('public.booking.directory')); ?>">Book a visit</a></li>
      <li><a href="#specialties">Specialties</a></li>
      <li><a href="#features">Features</a></li>
      <li><a href="#abdm">ABDM</a></li>
      <li><a href="#pricing">Pricing</a></li>
      <li><a href="#about">About</a></li>
    </ul>
    <div class="nav-cta">
      <a href="<?php echo e(route('public.booking.directory')); ?>" class="btn btn-ghost" title="For patients — find a clinic and book">Patients</a>
      <a href="<?php echo e(route('login')); ?>" class="btn btn-ghost">Sign in</a>
      <a href="<?php echo e(route('register')); ?>" class="btn btn-primary">Start free trial</a>
    </div>
    <button type="button" class="nav-burger" id="navBurger" aria-label="Open menu" aria-expanded="false" aria-controls="navDrawer">
      <span class="nav-burger-lines" aria-hidden="true"><span></span><span></span><span></span></span>
    </button>
  </div>
</nav>

<div class="nav-backdrop" id="navBackdrop" aria-hidden="true"></div>
<div class="nav-drawer" id="navDrawer" role="dialog" aria-modal="true" aria-label="Main menu" aria-hidden="true">
  <div class="nav-drawer-body">
    <div class="nav-drawer-label">Menu</div>
    <ul class="nav-drawer-links">
      <li><a href="<?php echo e(route('public.booking.directory')); ?>">Book a visit (patients)</a></li>
      <li><a href="#specialties">Specialties</a></li>
      <li><a href="#features">Features</a></li>
      <li><a href="#abdm">ABDM</a></li>
      <li><a href="#pricing">Pricing</a></li>
      <li><a href="#about">About</a></li>
    </ul>
  </div>
  <div class="nav-drawer-foot">
    <div class="nav-drawer-label" style="margin-bottom:10px">Account</div>
    <div class="nav-drawer-cta">
      <a href="<?php echo e(route('login')); ?>" class="btn btn-ghost" style="width:100%;justify-content:center;border:1px solid var(--border);background:#fff">Sign in</a>
      <a href="<?php echo e(route('register')); ?>" class="btn btn-primary" style="width:100%;justify-content:center;">Start free trial</a>
    </div>
  </div>
</div>
<script>
(function () {
  console.log('[ClinicOS][welcome]', { page: 'marketing', logo: <?php echo json_encode(asset('images/clinicos-logo.png'), 15, 512) ?>, path: window.location.pathname });
  console.log('[welcome] nav: drawer full-height (sibling of nav), backdrop 1024px');
  var b = document.getElementById('navBurger');
  var d = document.getElementById('navDrawer');
  var k = document.getElementById('navBackdrop');
  if (!b || !d) return;
  function isMobileNav() { return window.matchMedia('(max-width: 1024px)').matches; }
  function close() {
    document.body.classList.remove('nav-drawer-open');
    b.setAttribute('aria-expanded', 'false');
    b.setAttribute('aria-label', 'Open menu');
    d.setAttribute('aria-hidden', 'true');
    if (k) k.setAttribute('aria-hidden', 'true');
  }
  function open() {
    if (!isMobileNav()) return;
    document.body.classList.add('nav-drawer-open');
    b.setAttribute('aria-expanded', 'true');
    b.setAttribute('aria-label', 'Close menu');
    d.setAttribute('aria-hidden', 'false');
    if (k) k.setAttribute('aria-hidden', 'false');
  }
  function toggle() {
    if (document.body.classList.contains('nav-drawer-open')) close(); else open();
  }
  b.addEventListener('click', function () { toggle(); });
  if (k) k.addEventListener('click', function () { close(); });
  d.querySelectorAll('a').forEach(function (a) {
    a.addEventListener('click', function () { if (isMobileNav()) close(); });
  });
  document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && document.body.classList.contains('nav-drawer-open')) close();
  });
  window.addEventListener('resize', function () {
    if (window.innerWidth > 1024) close();
  });
})();
</script>

<!-- HERO -->
<section class="hero-section" style="overflow: hidden;">
  <div class="hero">
    <div class="hero-content">
      <div class="hero-badge">
        <span></span>
        Built for India's 8 lakh+ specialty clinics
      </div>
      <h1>The clinic OS that <span class="accent">thinks like a specialist</span></h1>
      <p class="hero-sub">Specialty-first EMR, intelligent scheduling, GST billing, ABDM compliance, and WhatsApp communication — in one platform designed for Indian specialty clinics and hospitals. From solo practice to full HIMS with IPD, pharmacy, and lab.</p>
      <div class="hero-actions">
        <a href="<?php echo e(route('register')); ?>" class="btn btn-primary btn-lg">Start 30-day free trial</a>
        <a href="<?php echo e(route('public.booking.directory')); ?>" class="btn btn-outline btn-lg">Book a visit (patients)</a>
        <a href="#features" class="btn btn-outline btn-lg hero-cta-secondary">View demo</a>
      </div>
      <div class="hero-stats">
        <div class="stat-item">
          <div class="stat-num">8L+</div>
          <div class="stat-label">Target clinics</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">15+</div>
          <div class="stat-label">Modules</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">6</div>
          <div class="stat-label">Roles</div>
        </div>
        <div class="stat-item">
          <div class="stat-num">₹1,499</div>
          <div class="stat-label">Starting/month</div>
        </div>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-card-main">
        <div class="card-topbar">
          <div class="dot dot-r"></div>
          <div class="dot dot-y"></div>
          <div class="dot dot-g"></div>
          <span class="card-topbar-title">ClinicOS — Patient Visit</span>
        </div>
        <div class="card-body">
          <div class="patient-header">
            <div class="patient-avatar">P</div>
            <div class="patient-info">
              <h4>Priya Mehta, 28F</h4>
              <p>ABHA: 91-2847-3910-4562 · Visit #4</p>
            </div>
            <div class="badge-specialty">Dermatology</div>
          </div>
          <div class="emr-section-title">Lesion Map</div>
          <div class="body-map-wrapper">
            <svg class="body-svg" viewBox="0 0 60 120" fill="none" xmlns="http://www.w3.org/2000/svg">
              <ellipse cx="30" cy="12" rx="10" ry="11" fill="#374151"/>
              <rect x="16" y="24" width="28" height="36" rx="6" fill="#374151"/>
              <rect x="5" y="25" width="10" height="28" rx="5" fill="#374151"/>
              <rect x="45" y="25" width="10" height="28" rx="5" fill="#374151"/>
              <rect x="17" y="62" width="12" height="36" rx="6" fill="#374151"/>
              <rect x="31" y="62" width="12" height="36" rx="6" fill="#374151"/>
              <circle cx="23" cy="38" r="4" fill="#ef4444" opacity=".7"/>
              <circle cx="36" cy="32" r="3" fill="#ef4444" opacity=".5"/>
              <circle cx="28" cy="45" r="2.5" fill="#f59e0b" opacity=".8"/>
            </svg>
            <div class="lesion-tags">
              <span class="lesion-tag">Left Cheek · Plaque 2cm</span>
              <span class="lesion-tag">Forehead · Papule</span>
              <span class="lesion-tag b">Jaw · Macule</span>
              <span class="lesion-tag g">PASI: 8.4</span>
              <span class="lesion-tag g">IGA: Grade 3</span>
            </div>
          </div>
          <div class="fields-row">
            <div class="field-block">
              <div class="field-label">Chief Complaint</div>
              <div class="field-value">Acne · Worsening</div>
            </div>
            <div class="field-block">
              <div class="field-label">Duration</div>
              <div class="field-value">8 months</div>
            </div>
            <div class="field-block">
              <div class="field-label">Procedure Today</div>
              <div class="field-value">Chemical Peel 30%</div>
            </div>
            <div class="field-block">
              <div class="field-label">Next Review</div>
              <div class="field-value">6 weeks · WhatsApp ✓</div>
            </div>
          </div>
          <div class="ai-strip">
            <div class="ai-icon">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M12 2l3 7h7l-5.5 4 2 7L12 16l-6.5 4 2-7L2 9h7z"/></svg>
            </div>
            <div class="ai-text">
              <strong>AI Summary generated</strong>
              Grade 3 acne, treatment plan: topical retinoid + peel series × 4
            </div>
          </div>
        </div>
      </div>
      <div class="floating-card fc-whatsapp">
        <div class="wa-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="white"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.025.507 3.934 1.395 5.605L0 24l6.537-1.368A11.945 11.945 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22c-1.844 0-3.574-.489-5.07-1.344l-.364-.214-3.78.791.812-3.682-.236-.38A9.96 9.96 0 012 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
        </div>
        <div class="fc-text">
          <strong>WhatsApp sent</strong>
          <span>Prescription + next appt. reminder</span>
        </div>
      </div>
      <div class="floating-card fc-abha">
        <div class="abha-icon">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
        </div>
        <div class="fc-text">
          <strong>ABDM Compliant</strong>
          <span>FHIR R4 record synced</span>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- SPECIALTIES -->
<section class="section specialties-bg" id="specialties">
  <div class="section-inner">
    <div class="section-label">Specialty-First Design</div>
    <h2>Built for how <span class="headline-accent">you</span> practise</h2>
    <p class="section-sub">Every specialty gets its own purpose-built clinical documentation experience — not a generic form with a different label.</p>
    <div class="specialties-tabs">
      <button type="button" class="spec-tab active" onclick="switchSpec('derm', event)"><span class="spec-tab-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" /></svg></span> Dermatology</button>
      <button type="button" class="spec-tab" onclick="switchSpec('physio', event)"><span class="spec-tab-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" /></svg></span> Physiotherapy</button>
      <button type="button" class="spec-tab" onclick="switchSpec('dental', event)"><span class="spec-tab-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg></span> Dental</button>
      <button type="button" class="spec-tab" onclick="switchSpec('ophthal', event)"><span class="spec-tab-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg></span> Ophthalmology</button>
      <button type="button" class="spec-tab" onclick="switchSpec('ortho', event)"><span class="spec-tab-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" /></svg></span> Orthopaedics</button>
      <button type="button" class="spec-tab" onclick="switchSpec('ent', event)"><span class="spec-tab-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 15.553Z" /></svg></span> ENT</button>
      <button type="button" class="spec-tab" onclick="switchSpec('gynae', event)"><span class="spec-tab-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg></span> Gynaecology</button>
    </div>

    <!-- DERM -->
    <div class="spec-content active" id="spec-derm">
      <div class="spec-features">
        <h3>Dermatology EMR</h3>
        <p>From lesion mapping to before/after photo management — every tool a dermatologist actually needs, built in from day one.</p>
        <ul class="spec-list">
          <li><span class="check">✓</span> Interactive body diagram — tap to annotate lesion site, type, size, colour</li>
          <li><span class="check">✓</span> Auto-calculated PASI, IGA, and DLQI grading scales</li>
          <li><span class="check">✓</span> Before/after photo vault with side-by-side comparison per body region</li>
          <li><span class="check">✓</span> Procedure codes: LASER, PRP, Botox, Chemical Peel, Fillers</li>
          <li><span class="check">✓</span> 6-week follow-up trigger auto-sent via WhatsApp</li>
          <li><span class="check">✓</span> Digital consent for photos, stored with timestamp</li>
        </ul>
      </div>
      <div class="spec-card-preview">
        <div class="spec-card-header">
          <div class="spec-card-header-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" /></svg></div>
          <div><h4>Dermatology Visit Note</h4><p>Psoriasis — Follow-up #3</p></div>
        </div>
        <div class="spec-card-body">
          <div style="display:flex;gap:8px;margin-bottom:12px;flex-wrap:wrap">
            <span style="background:rgba(239,68,68,.12);color:#fca5a5;padding:3px 10px;border-radius:6px;font-size:11px">PASI Score: 8.4</span>
            <span style="background:rgba(20,71,230,.12);color:#93c5fd;padding:3px 10px;border-radius:6px;font-size:11px">IGA: Grade 3</span>
            <span style="background:rgba(245,158,11,.12);color:#fcd34d;padding:3px 10px;border-radius:6px;font-size:11px">DLQI: 14</span>
          </div>
          <div style="background:#111827;border-radius:10px;padding:14px;margin-bottom:12px">
            <div style="font-size:10px;color:#6b7280;font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px">Today's Procedure</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Type</div><div style="color:#e5e7eb;font-size:12px;font-weight:600">Chemical Peel 30% SA</div></div>
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Area</div><div style="color:#e5e7eb;font-size:12px;font-weight:600">Face, T-zone</div></div>
            </div>
          </div>
          <div class="spec-photo-strip">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0ZM18.75 10.5h.008v.008h-.008V10.5Z" /></svg>
            <span>Before/After photos captured — linked to this visit</span>
          </div>
        </div>
      </div>
    </div>

    <!-- PHYSIO -->
    <div class="spec-content" id="spec-physio">
      <div class="spec-features">
        <h3>Physiotherapy EMR</h3>
        <p>Session-by-session clinical documentation with goal tracking, ROM measurements, and WhatsApp home exercise delivery.</p>
        <ul class="spec-list">
          <li><span class="check">✓</span> Initial assessment: ROM table, MMT grading 0–5, VAS pain scale</li>
          <li><span class="check">✓</span> Session notes with delta indicators (today vs last session ROM)</li>
          <li><span class="check">✓</span> Treatment modality codes: TENS, ultrasound, manual therapy, exercise</li>
          <li><span class="check">✓</span> Outcome measures: Barthel, FIM, DASH, WOMAC — auto-scored</li>
          <li><span class="check">✓</span> Home Exercise Programme sent to patient via WhatsApp with images</li>
          <li><span class="check">✓</span> SMART goal setting framework (2-week and 8-week targets)</li>
        </ul>
      </div>
      <div class="spec-card-preview">
        <div class="spec-card-header">
          <div class="spec-card-header-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" /></svg></div>
          <div><h4>Session Note #6</h4><p>Right Knee — ACL Rehab Protocol</p></div>
        </div>
        <div class="spec-card-body">
          <div style="background:#111827;border-radius:10px;padding:14px;margin-bottom:12px">
            <div style="font-size:10px;color:#6b7280;font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px">ROM Today vs Last Session</div>
            <div style="display:flex;flex-direction:column;gap:6px">
              <div style="display:flex;justify-content:space-between;align-items:center">
                <span style="color:#94a3b8;font-size:12px">Knee Flexion (Active)</span>
                <div style="display:flex;align-items:center;gap:8px">
                  <span style="color:#6b7280;font-size:11px">Prev: 85°</span>
                  <span style="color:#6ee7b7;font-size:13px;font-weight:700">Now: 102°</span>
                  <span style="background:rgba(16,185,129,.15);color:#6ee7b7;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:700">+17°</span>
                </div>
              </div>
              <div style="display:flex;justify-content:space-between;align-items:center">
                <span style="color:#94a3b8;font-size:12px">VAS Pain Score</span>
                <div style="display:flex;align-items:center;gap:8px">
                  <span style="color:#6b7280;font-size:11px">Prev: 5/10</span>
                  <span style="color:#6ee7b7;font-size:13px;font-weight:700">Now: 3/10</span>
                  <span style="background:rgba(16,185,129,.15);color:#6ee7b7;padding:2px 8px;border-radius:4px;font-size:10px;font-weight:700">↓2</span>
                </div>
              </div>
            </div>
          </div>
          <div style="background:rgba(20,71,230,.08);border:1px solid rgba(20,71,230,.2);border-radius:8px;padding:10px 12px;font-size:12px;color:#93c5fd">
            🏃 HEP sent via WhatsApp: 4 exercises with video links
          </div>
        </div>
      </div>
    </div>

    <!-- DENTAL -->
    <div class="spec-content" id="spec-dental">
      <div class="spec-features">
        <h3>Dental EMR</h3>
        <p>Full 32-tooth FDI chart with per-tooth treatment history, X-ray attachments, lab work orders, and sterilisation logs.</p>
        <ul class="spec-list">
          <li><span class="check">✓</span> Interactive 32-tooth FDI notation chart — tap any tooth to record</li>
          <li><span class="check">✓</span> Per-tooth: restorations, caries, periodontal status, treatment history</li>
          <li><span class="check">✓</span> X-ray (RVG/OPG) attached directly to the relevant tooth</li>
          <li><span class="check">✓</span> Treatment plan with cost estimates, priority, and status tracking</li>
          <li><span class="check">✓</span> Lab work order (crown, bridge) sent to lab via WhatsApp/PDF</li>
          <li><span class="check">✓</span> Full 6-point periodontal probing chart with BOP and recession</li>
        </ul>
      </div>
      <div class="spec-card-preview">
        <div class="spec-card-header">
          <div class="spec-card-header-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" /></svg></div>
          <div><h4>Tooth #36 — Lower Left 6</h4><p>Root Canal + Crown Planned</p></div>
        </div>
        <div class="spec-card-body">
          <div style="background:#111827;border-radius:10px;padding:14px;margin-bottom:12px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Status</div><div style="color:#fca5a5;font-size:12px;font-weight:600">Caries — Advanced</div></div>
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Procedure</div><div style="color:#e5e7eb;font-size:12px;font-weight:600">RCT + PFM Crown</div></div>
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Estimate</div><div style="color:#fcd34d;font-size:12px;font-weight:600">₹12,000</div></div>
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Lab Delivery</div><div style="color:#6ee7b7;font-size:12px;font-weight:600">2025-04-08</div></div>
            </div>
          </div>
          <div style="background:rgba(245,158,11,.08);border:1px solid rgba(245,158,11,.2);border-radius:8px;padding:10px 12px;font-size:12px;color:#fcd34d">
            🔬 RVG X-ray attached · Lab work order sent to DentPro Lab
          </div>
        </div>
      </div>
    </div>

    <!-- OPHTHAL -->
    <div class="spec-content" id="spec-ophthal">
      <div class="spec-features">
        <h3>Ophthalmology EMR</h3>
        <p>Structured VA logs, refraction prescriptions, slit lamp findings, and auto-generated spectacle prescription PDFs.</p>
        <ul class="spec-list">
          <li><span class="check">✓</span> VA log per visit: unaided, pinhole, BCVA — plotted as trend</li>
          <li><span class="check">✓</span> Refraction: Sphere / Cylinder / Axis / Add for distance and near</li>
          <li><span class="check">✓</span> Slit lamp: AC grade, corneal clarity, LOCS lens grading</li>
          <li><span class="check">✓</span> IOP Goldmann applanation with time and method</li>
          <li><span class="check">✓</span> Fundus photo attachment with optic disc CDR recording</li>
          <li><span class="check">✓</span> Auto-generate spectacle prescription PDF for optical dispensing</li>
        </ul>
      </div>
      <div class="spec-card-preview">
        <div class="spec-card-header">
          <div class="spec-card-header-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg></div>
          <div><h4>Refraction &amp; VA Log</h4><p>Ramesh Kumar, 52M · Visit #2</p></div>
        </div>
        <div class="spec-card-body">
          <div style="background:#111827;border-radius:10px;padding:14px;margin-bottom:12px">
            <div style="font-size:10px;color:#6b7280;font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px">Refraction</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div>
                <div style="color:#6b7280;font-size:10px;margin-bottom:4px">Right Eye (OD)</div>
                <div style="color:#e5e7eb;font-size:12px;font-weight:600">-2.50 / -0.75 × 180</div>
                <div style="color:#6b7280;font-size:11px;margin-top:2px">VA: 6/6</div>
              </div>
              <div>
                <div style="color:#6b7280;font-size:10px;margin-bottom:4px">Left Eye (OS)</div>
                <div style="color:#e5e7eb;font-size:12px;font-weight:600">-3.00 / -1.00 × 175</div>
                <div style="color:#6b7280;font-size:11px;margin-top:2px">VA: 6/9</div>
              </div>
            </div>
          </div>
          <div style="background:rgba(16,185,129,.08);border:1px solid rgba(16,185,129,.2);border-radius:8px;padding:10px 12px;font-size:12px;color:#6ee7b7">
            📄 Spectacle prescription PDF auto-generated &amp; sent via WhatsApp
          </div>
        </div>
      </div>
    </div>

    <!-- ORTHO -->
    <div class="spec-content" id="spec-ortho">
      <div class="spec-features">
        <h3>Orthopaedics EMR</h3>
        <p>Joint examination templates, AO fracture classification, implant records, and surgery follow-up milestones.</p>
        <ul class="spec-list">
          <li><span class="check">✓</span> Joint templates: shoulder, knee, hip, spine, wrist, ankle with special tests</li>
          <li><span class="check">✓</span> AO/OTA fracture classification with X-ray annotation capability</li>
          <li><span class="check">✓</span> Implant records: manufacturer, catalogue number, torque values</li>
          <li><span class="check">✓</span> Post-surgical protocol with auto-scheduled follow-up milestones</li>
          <li><span class="check">✓</span> Physiotherapy referral with specific instructions from same visit</li>
          <li><span class="check">✓</span> ROM and power grading per joint with trend tracking</li>
        </ul>
      </div>
      <div class="spec-card-preview">
        <div class="spec-card-header">
          <div class="spec-card-header-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h6c.621 0 1.125.504 1.125 1.125v3.75c0 .621-.504 1.125-1.125 1.125h-6a1.125 1.125 0 0 1-1.125-1.125v-3.75ZM14.25 8.625c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v8.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-8.25ZM3.75 16.125c0-.621.504-1.125 1.125-1.125h5.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-5.25a1.125 1.125 0 0 1-1.125-1.125v-2.25Z" /></svg></div>
          <div><h4>Knee Examination</h4><p>Right Knee — ACL Injury</p></div>
        </div>
        <div class="spec-card-body">
          <div style="background:#111827;border-radius:10px;padding:14px;margin-bottom:12px">
            <div style="font-size:10px;color:#6b7280;font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px">Special Tests</div>
            <div style="display:flex;flex-direction:column;gap:6px">
              <div style="display:flex;justify-content:space-between"><span style="color:#94a3b8;font-size:12px">Lachman Test</span><span style="background:rgba(239,68,68,.15);color:#fca5a5;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">Positive</span></div>
              <div style="display:flex;justify-content:space-between"><span style="color:#94a3b8;font-size:12px">Anterior Drawer</span><span style="background:rgba(239,68,68,.15);color:#fca5a5;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">Positive</span></div>
              <div style="display:flex;justify-content:space-between"><span style="color:#94a3b8;font-size:12px">McMurray's Test</span><span style="background:rgba(16,185,129,.15);color:#6ee7b7;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600">Negative</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ENT -->
    <div class="spec-content" id="spec-ent">
      <div class="spec-features">
        <h3>ENT EMR</h3>
        <p>Audiogram entry, tympanogram results, nasal endoscopy findings, and vertigo assessment scales — all structured.</p>
        <ul class="spec-list">
          <li><span class="check">✓</span> Audiogram: air and bone conduction thresholds plotted visually</li>
          <li><span class="check">✓</span> Tympanogram: Type A/B/C/As/Ad with peak compliance</li>
          <li><span class="check">✓</span> Nasal endoscopy and otoscopy structured findings</li>
          <li><span class="check">✓</span> DHI (Dizziness Handicap Inventory) auto-scored from inputs</li>
          <li><span class="check">✓</span> BPPV documentation with Dix-Hallpike test results</li>
          <li><span class="check">✓</span> Hearing aid prescription with fitting parameters</li>
        </ul>
      </div>
      <div class="spec-card-preview">
        <div class="spec-card-header">
          <div class="spec-card-header-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m9 9 10.5-3m0 6.553v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 1 1-.99-3.467l2.31-.66a2.25 2.25 0 0 0 1.632-2.163Zm0 0V2.25L9 5.25v10.303m0 0v3.75a2.25 2.25 0 0 1-1.632 2.163l-1.32.377a1.803 1.803 0 0 1-.99-3.467l2.31-.66A2.25 2.25 0 0 0 9 15.553Z" /></svg></div>
          <div><h4>Audiogram Report</h4><p>Right Ear — Sensorineural Loss</p></div>
        </div>
        <div class="spec-card-body">
          <div style="background:#111827;border-radius:10px;padding:14px;margin-bottom:12px">
            <div style="font-size:10px;color:#6b7280;font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:10px">Pure Tone Average</div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Right (AC)</div><div style="color:#fca5a5;font-size:16px;font-weight:700">52 dB HL</div><div style="color:#6b7280;font-size:11px">Moderate Loss</div></div>
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Left (AC)</div><div style="color:#6ee7b7;font-size:16px;font-weight:700">22 dB HL</div><div style="color:#6b7280;font-size:11px">Normal</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- GYNAE -->
    <div class="spec-content" id="spec-gynae">
      <div class="spec-features">
        <h3>Gynaecology EMR</h3>
        <p>Obstetric history, antenatal visit sequences by gestational week, menstrual tracking, and colposcopy findings.</p>
        <ul class="spec-list">
          <li><span class="check">✓</span> Obstetric history: LMP, EDD auto-calculated, gravida/para notation</li>
          <li><span class="check">✓</span> Structured antenatal visit protocol by gestational week (8W to 38W)</li>
          <li><span class="check">✓</span> Menstrual history: cycle tracking, PCOD markers over multiple visits</li>
          <li><span class="check">✓</span> Colposcopy: VIA/VILI results, Pap smear history, cervical biopsy</li>
          <li><span class="check">✓</span> USG report attachment linked to visit date</li>
          <li><span class="check">✓</span> Contraception counselling records with follow-up scheduling</li>
        </ul>
      </div>
      <div class="spec-card-preview">
        <div class="spec-card-header">
          <div class="spec-card-header-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z" /></svg></div>
          <div><h4>Antenatal Visit — 20W</h4><p>Sneha Patil · G2P1</p></div>
        </div>
        <div class="spec-card-body">
          <div style="background:#111827;border-radius:10px;padding:14px;margin-bottom:12px">
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">LMP</div><div style="color:#e5e7eb;font-size:12px;font-weight:600">2024-11-10</div></div>
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">EDD</div><div style="color:#fcd34d;font-size:12px;font-weight:600">2025-08-17</div></div>
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Gestational Age</div><div style="color:#6ee7b7;font-size:12px;font-weight:600">20 weeks 3 days</div></div>
              <div style="background:#1e2535;border-radius:8px;padding:8px 10px"><div style="color:#6b7280;font-size:10px">Next Visit</div><div style="color:#e5e7eb;font-size:12px;font-weight:600">24W — 4 weeks</div></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- MODULES -->
<section class="section features-section" id="features">
  <div class="section-inner">
    <div class="section-label">15+ integrated modules</div>
    <h1 class="features-heading">Everything your clinic needs—without the bloat.</h1>
    <p class="section-sub">Each module is designed to save time or generate revenue. If it does neither, it does not ship.</p>
    <div class="modules-grid">
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-1.5a2.251 2.251 0 01-2.25-2.25V6.75a2.25 2.25 0 012.25-2.25z" /></svg></div>
        <h3>Specialty EMR</h3>
        <p>10 specialty-specific template packs. Custom field builder. Voice-to-EMR AI assistant. Every doctor sees their own home screen.</p>
        <span class="module-chip">10 specialties</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5a2.25 2.25 0 012.25 2.25v7.5" /></svg></div>
        <h3>Smart Scheduling</h3>
        <p>Multi-resource management — doctor slots, treatment rooms, and equipment. Procedure-aware booking with advance payment.</p>
        <span class="module-chip">Multi-resource</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg></div>
        <h3>Patient Profile</h3>
        <p>Longitudinal record — all visits, prescriptions, photos, and communications in one timeline. ABHA-linked for national portability.</p>
        <span class="module-chip">ABHA-linked</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m10.5 20.5 10-10a4.95 4.95 0 1 0-7-7l-10 10a4.95 4.95 0 1 0 7 7Z" /><path stroke-linecap="round" stroke-linejoin="round" d="m8.5 8.5 7 7" /></svg></div>
        <h3>Digital Prescription</h3>
        <p>40,000+ Indian drug database with generic and brand names. Dosage templates, interaction checking, HPR-signed for ABDM.</p>
        <span class="module-chip">HPR-signed</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15a2.25 2.25 0 002.25-2.25V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" /><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM12 9.75h.008v.008H12V9.75z" /></svg></div>
        <h3>Photo Vault</h3>
        <p>Before/after photo management with body-map tagging, side-by-side comparison, and encrypted cloud storage. Consent-first.</p>
        <span class="module-chip">Dermatology</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg></div>
        <h3>GST Billing</h3>
        <p>Medical SAC codes for every service type. GST invoice PDF. Razorpay UPI collection. TDS tracking. e-Invoice for high turnover.</p>
        <span class="module-chip">India-native</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155" /></svg></div>
        <h3>WhatsApp Comms</h3>
        <p>Full patient communication workflow via WhatsApp — bookings, reminders, prescriptions, payment links, follow-up triggers.</p>
        <span class="module-chip">Two-way</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" /></svg></div>
        <h3>ABDM Compliance</h3>
        <p>ABHA creation, HFR registration, FHIR R4 health record sharing, consent management. Full ABDM stack certified.</p>
        <span class="module-chip">FHIR R4</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z" /></svg></div>
        <h3>Clinic Analytics</h3>
        <p>Revenue, appointment, and patient retention reports. Specialty KPIs — session completion rates, treatment response tracking.</p>
        <span class="module-chip">Specialty KPIs</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg></div>
        <h3>IPD Management</h3>
        <p>Full admission-discharge-transfer workflow. Bed management across wards. Visiting card printing. Treatment tracking and nursing notes.</p>
        <span class="module-chip">ADT Workflow</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg></div>
        <h3>Pharmacy &amp; Inventory</h3>
        <p>FIFO batch dispensing, stock alerts, expiry tracking, pharmacist portal with dedicated work queue. Drug database with 40,000+ Indian medicines.</p>
        <span class="module-chip">FIFO Dispensing</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5" /></svg></div>
        <h3>Laboratory (LIS)</h3>
        <p>Full lab information system. Test catalog, sample collection, result entry, doctor notification. Dedicated lab technician portal.</p>
        <span class="module-chip">Full LIS</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.09 9.09 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg></div>
        <h3>OPD Queue Management</h3>
        <p>Real-time patient queue with auto-refresh. Walk-in registration. Wait time tracking. Multi-doctor queue support.</p>
        <span class="module-chip">Real-time Queue</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.37.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.37-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg></div>
        <h3>Hospital Settings</h3>
        <p>Ward &amp; bed configuration. Department management. Multi-role access control. Feature flag per tenant.</p>
        <span class="module-chip">Multi-tenant</span>
      </div>
      <div class="module-card">
        <div class="module-icon" aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z" /></svg></div>
        <h3>Role-Based Access</h3>
        <p>Doctor, nurse, receptionist, pharmacist, lab technician roles. Owner dashboard. Granular permission control per module.</p>
        <span class="module-chip">6 Roles</span>
      </div>
    </div>
  </div>
</section>

<!-- HOW IT WORKS -->
<section class="section steps-bg">
  <div class="section-inner">
    <div class="section-label" style="color:#60a5fa">Getting started</div>
    <h2>From sign-up to first patient<br>in under 30 minutes</h2>
    <p class="section-sub" style="color:#94a3b8">No IT team required. No data migration nightmare. No week-long training. Just a clinic that runs better from day one.</p>
    <div class="steps-grid">
      <div class="step active-step">
        <div class="step-num">1</div>
        <h3>Set up your clinic</h3>
        <p>Enter your clinic name, select your specialties, add your doctors, and configure appointment types. ABDM HFR registration guided step-by-step.</p>
      </div>
      <div class="step">
        <div class="step-num">2</div>
        <h3>Register patients</h3>
        <p>Create patient profiles with ABHA ID in one tap — Aadhaar OTP or mobile. Medical history intake form sent automatically via WhatsApp.</p>
      </div>
      <div class="step">
        <div class="step-num">3</div>
        <h3>See patients, not screens</h3>
        <p>Tap the specialty template. Dictate or type. AI fills the fields. WhatsApp sends the prescription. Invoice raised in 2 clicks. Done.</p>
      </div>
    </div>
  </div>
</section>

<!-- ABDM -->
<section class="section" id="abdm">
  <div class="section-inner">
    <div class="section-label">Regulatory Compliance</div>
    <h2>ABDM compliance built in,<br>not bolted on</h2>
    <p class="section-sub">As AB-PMJAY empanelment moves towards mandatory ABDM compliance, ClinicOS ensures you're never behind.</p>
    <div class="abdm-grid">
      <div class="abdm-features">
        <div class="abdm-item">
          <div class="abdm-dot">🆔</div>
          <div>
            <h4>ABHA Patient ID</h4>
            <p>Create or link a patient's 14-digit ABHA health ID at registration — scan Aadhaar OTP or mobile. One tap, done.</p>
          </div>
        </div>
        <div class="abdm-item">
          <div class="abdm-dot">🏥</div>
          <div>
            <h4>Health Facility Registry (HFR)</h4>
            <p>Guided HFR registration for your clinic. Auto-generates your facility QR code for Scan &amp; Share OPD registration.</p>
          </div>
        </div>
        <div class="abdm-item">
          <div class="abdm-dot">📤</div>
          <div>
            <h4>FHIR R4 Health Records</h4>
            <p>All clinical records generated in ClinicOS are stored and shareable in FHIR R4 — prescriptions, visit notes, lab reports.</p>
          </div>
        </div>
        <div class="abdm-item">
          <div class="abdm-dot">🤝</div>
          <div>
            <h4>Consent Management</h4>
            <p>Patient approves record sharing in-app. Consent log maintained for audit. DPDP Act compliant at every step.</p>
          </div>
        </div>
      </div>
      <div class="abdm-visual">
        <h4>ABDM Certification Milestones</h4>
        <div class="abdm-milestone">
          <div class="milestone-dot ms-done">M1</div>
          <div class="milestone-text">
            <h5>ABHA Creation &amp; Linking</h5>
            <p>ABHA creation API, patient record linking, facility QR for Scan &amp; Share</p>
          </div>
        </div>
        <div class="abdm-milestone">
          <div class="milestone-dot ms-prog">M2</div>
          <div class="milestone-text">
            <h5>Health Information Provider (HIP)</h5>
            <p>FHIR R4 care contexts, consent handling, HIE-CM integration, WASA certificate</p>
          </div>
        </div>
        <div class="abdm-milestone">
          <div class="milestone-dot ms-pend">M3</div>
          <div class="milestone-text">
            <h5>Health Information User (HIU)</h5>
            <p>Receive records from other providers with patient consent — full portability</p>
          </div>
        </div>
        <div style="margin-top:16px;padding:12px;background:rgba(5,150,105,.08);border:1px solid rgba(5,150,105,.2);border-radius:8px;font-size:12px;color:#6ee7b7">
          ✅ CERT-IN WASA Security Audit included in Group Practice plan
        </div>
      </div>
    </div>
  </div>
</section>

<!-- AI -->
<section class="section ai-bg">
  <div class="section-inner">
    <div class="section-label">AI Documentation</div>
    <h2>5 minutes saved per patient.<br>2 extra slots per day.</h2>
    <p class="section-sub">The AI documentation assistant understands clinical context — not just voice-to-text, but voice-to-structured-EMR-fields.</p>
    <div class="ai-grid">
      <div class="ai-cards">
        <div class="ai-card">
          <div class="ai-card-icon" style="background:rgba(239,68,68,.15)">🎙️</div>
          <div>
            <h4>Voice-to-EMR Dictation</h4>
            <p>Speak naturally in English or Hinglish after the consultation. AI maps speech to the correct structured fields in your specialty template. Works offline with Whisper-small on-device.</p>
          </div>
        </div>
        <div class="ai-card">
          <div class="ai-card-icon" style="background:rgba(20,71,230,.15)">📝</div>
          <div>
            <h4>Consultation Summary Generator</h4>
            <p>One tap after completing the EMR. AI writes a patient-friendly summary in simple English or Hindi — diagnosis, treatment plan, and what to do next.</p>
          </div>
        </div>
        <div class="ai-card">
          <div class="ai-card-icon" style="background:rgba(16,185,129,.15)">💊</div>
          <div>
            <h4>Smart Prescription Templates</h4>
            <p>AI analyses the diagnosis and suggests the standard prescription. 40,000+ Indian drug database. Drug interaction checker. Previous prescription reference.</p>
          </div>
        </div>
      </div>
      <div class="dictation-demo">
        <div class="demo-header">
          <div class="rec-btn">⏺</div>
          <div>
            <h4>Live Dictation Demo</h4>
            <p>Dermatology — Priya Mehta, 28F</p>
          </div>
        </div>
        <div class="transcript-box">
          "Patient has a 2 centimetre raised red plaque on the left cheek for 2 months. PASI score is 8.4. Starting on topical betamethasone and oral doxycycline 100mg. Scheduled chemical peel in 2 weeks."
        </div>
        <div style="font-size:10px;color:#6b7280;font-weight:600;letter-spacing:.06em;text-transform:uppercase;margin-bottom:8px">AI-Mapped Fields</div>
        <div class="mapped-fields">
          <div class="map-field"><span>Location</span><strong>Left cheek</strong></div>
          <div class="map-field"><span>Lesion Type</span><strong>Plaque (raised)</strong></div>
          <div class="map-field"><span>Size</span><strong>2 cm</strong></div>
          <div class="map-field"><span>Duration</span><strong>2 months</strong></div>
          <div class="map-field"><span>PASI Score</span><strong>8.4</strong></div>
          <div class="map-field"><span>Next Procedure</span><strong>Chemical Peel</strong></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- PRICING -->
<section class="section specialties-bg" id="pricing">
  <div class="section-inner">
    <div class="section-label">Simple Pricing</div>
    <h2>Less than one consultation fee<br>per month</h2>
    <p class="section-sub">No setup fee. No per-patient charge. Cancel anytime. All plans include 30-day free trial.</p>
    <div class="pricing-grid">
      <div class="price-card">
        <div class="price-plan">Solo Practice</div>
        <div class="price-hi">एकल क्लिनिक</div>
        <div class="price-amount"><span class="rupee">₹</span><span class="num">1,499</span><span class="period">/month</span></div>
        <p class="price-desc">1 doctor, 1 location. Full specialty EMR, scheduling, billing, WhatsApp, and ABHA. 500 patient records.</p>
        <ul class="price-features">
          <li>Full specialty EMR template</li>
          <li>Smart scheduling calendar</li>
          <li>GST-compliant billing</li>
          <li>WhatsApp patient comms</li>
          <li>ABHA patient ID creation</li>
          <li>Before/After photo vault</li>
          <li>500 patient records</li>
        </ul>
        <a href="<?php echo e(route('register')); ?>" class="btn btn-outline btn-lg" style="width:100%;justify-content:center">Start free trial</a>
      </div>
      <div class="price-card popular">
        <div class="popular-badge">Most Popular</div>
        <div class="price-plan">Small Clinic</div>
        <div class="price-hi">छोटी क्लिनिक</div>
        <div class="price-amount"><span class="rupee">₹</span><span class="num">2,999</span><span class="period">/month</span></div>
        <p class="price-desc">Up to 3 doctors, 1 location. Multi-doctor scheduling, room management, analytics, and AI assistant.</p>
        <ul class="price-features">
          <li>Everything in Solo</li>
          <li>Up to 3 doctors</li>
          <li>Multi-doctor scheduling</li>
          <li>Room &amp; equipment slots</li>
          <li>Clinic analytics dashboard</li>
          <li>AI documentation assistant</li>
          <li>Unlimited patient records</li>
        </ul>
        <a href="<?php echo e(route('register')); ?>" class="btn btn-primary btn-lg" style="width:100%;justify-content:center">Start free trial</a>
      </div>
      <div class="price-card">
        <div class="price-plan">Group Practice</div>
        <div class="price-hi">समूह क्लिनिक</div>
        <div class="price-amount"><span class="rupee">₹</span><span class="num">5,999</span><span class="period">/month</span></div>
        <p class="price-desc">Up to 8 doctors, 2 locations. Full ABDM stack, insurance/TPA billing, white-label option.</p>
        <ul class="price-features">
          <li>Everything in Small Clinic</li>
          <li>Up to 8 doctors, 2 locations</li>
          <li>Full ABDM stack (M1+M2+M3)</li>
          <li>Insurance &amp; TPA billing</li>
          <li>WASA security audit</li>
          <li>White-label branding</li>
          <li>Priority support</li>
        </ul>
        <a href="<?php echo e(route('register')); ?>" class="btn btn-outline btn-lg" style="width:100%;justify-content:center">Start free trial</a>
      </div>
      <div class="price-card">
        <div class="price-plan">Hospital / HIMS</div>
        <div class="price-hi">अस्पताल</div>
        <div class="price-amount"><span class="rupee">₹</span><span class="num">14,999</span><span class="period">/month</span></div>
        <p class="price-desc">Full hospital management. IPD, pharmacy, lab, OPD queue, multi-role access. Up to 20 doctors, unlimited beds.</p>
        <ul class="price-features">
          <li>Everything in Group Practice</li>
          <li>IPD bed &amp; ward management</li>
          <li>Pharmacy inventory (FIFO)</li>
          <li>Laboratory Information System</li>
          <li>OPD queue management</li>
          <li>Role-based access (6 roles)</li>
          <li>Dedicated portals (pharmacist, lab tech)</li>
          <li>Daily automated backups</li>
        </ul>
        <a href="#about" class="btn btn-primary btn-lg" style="width:100%;justify-content:center">Contact sales</a>
      </div>
    </div>
    <p style="text-align:center;color:var(--text3);font-size:13px;margin-top:24px">Additional doctor seats: ₹299/month · Hospital OPD departments: custom pricing</p>
  </div>
</section>

<!-- COMPARISON -->
<section class="section">
  <div class="section-inner">
    <div class="section-label">Why ClinicOS</div>
    <h2>Built for specialists,<br>not adapted for them</h2>
    <p class="section-sub">Generic EMRs treat specialty needs as afterthoughts. ClinicOS treats them as the whole point.</p>
    <table class="compare-table">
      <thead>
        <tr>
          <th>Feature</th>
          <th>Practo / HealthPlix</th>
          <th>DocPulse / Eka</th>
          <th>ClinicOS</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>Specialty-specific EMR templates</td>
          <td class="no">✕ Generic forms</td>
          <td class="partial">Partial</td>
          <td class="yes">✓ 10 specialty packs</td>
        </tr>
        <tr>
          <td>Lesion / body diagram annotation</td>
          <td class="no">✕</td>
          <td class="no">✕</td>
          <td class="yes">✓ Derm + Ortho</td>
        </tr>
        <tr>
          <td>Dental FDI tooth chart (32-tooth)</td>
          <td class="no">✕</td>
          <td class="no">✕</td>
          <td class="yes">✓ Full chart + X-ray</td>
        </tr>
        <tr>
          <td>Physio session tracking + HEP</td>
          <td class="no">✕</td>
          <td class="no">✕</td>
          <td class="yes">✓ ROM + MMT + WhatsApp HEP</td>
        </tr>
        <tr>
          <td>Full ABDM stack (ABHA + HFR + FHIR)</td>
          <td class="partial">ABHA only</td>
          <td class="partial">ABHA only</td>
          <td class="yes">✓ M1 + M2 + M3</td>
        </tr>
        <tr>
          <td>WhatsApp prescription + follow-up</td>
          <td class="partial">Booking only</td>
          <td class="no">✕</td>
          <td class="yes">✓ Full workflow</td>
        </tr>
        <tr>
          <td>AI voice-to-EMR documentation</td>
          <td class="no">✕</td>
          <td class="no">✕</td>
          <td class="yes">✓ Whisper + Claude AI</td>
        </tr>
        <tr>
          <td>Medical GST SAC code billing</td>
          <td class="partial">Basic</td>
          <td class="partial">Basic</td>
          <td class="yes">✓ All SAC codes + TDS</td>
        </tr>
        <tr>
          <td>IPD Bed Management</td>
          <td class="no">✕</td>
          <td class="partial">Partial</td>
          <td class="yes">✓ Full ADT</td>
        </tr>
        <tr>
          <td>Pharmacy FIFO Dispensing</td>
          <td class="no">✕</td>
          <td class="no">✕</td>
          <td class="yes">✓ Batch tracking</td>
        </tr>
        <tr>
          <td>Lab Information System</td>
          <td class="no">✕</td>
          <td class="partial">Partial</td>
          <td class="yes">✓ Full LIS</td>
        </tr>
        <tr>
          <td>Role-based access (6 roles)</td>
          <td class="partial">Partial</td>
          <td class="partial">Partial</td>
          <td class="yes">✓ 6 roles</td>
        </tr>
        <tr>
          <td>Starting price / month</td>
          <td>₹1,499–₹5,000</td>
          <td>₹2,000+</td>
          <td class="yes">✓ ₹1,499</td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<!-- CTA -->
<section class="cta-section">
  <h2>Ready to run a clinic that runs itself?</h2>
  <p>Join specialists and hospitals across Dermatology, Physiotherapy, Dental, Ophthalmology, and full HIMS who've replaced paper and generic tools with ClinicOS.</p>
  <div class="cta-actions">
    <a href="<?php echo e(route('register')); ?>" class="btn btn-white btn-lg">Start 30-day free trial</a>
    <a href="#features" class="btn btn-outline-white btn-lg">See a live demo</a>
  </div>
</section>

<!-- FOOTER -->
<footer id="about">
  <div class="footer-inner">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-logo">ClinicOS · क्लिनिक ओएस</div>
        <p>Complete SaaS platform for India's specialty clinics. Built by RH Technology, Pune. Specialty-first, ABDM-compliant, India-native.</p>
        <div class="trust-chips" style="margin-top:16px">
          <div class="trust-chip">🔒 AES-256</div>
          <div class="trust-chip">🇮🇳 AWS Mumbai</div>
          <div class="trust-chip">✅ ABDM</div>
        </div>
      </div>
      <div class="footer-col">
        <h5>Product</h5>
        <ul>
          <li><a href="#features">Specialty EMR</a></li>
          <li><a href="#features">Smart Scheduling</a></li>
          <li><a href="#features">GST Billing</a></li>
          <li><a href="#features">WhatsApp Comms</a></li>
          <li><a href="#abdm">ABDM Compliance</a></li>
          <li><a href="#features">AI Assistant</a></li>
          <li><a href="#features">IPD Management</a></li>
          <li><a href="#features">Pharmacy &amp; Lab</a></li>
          <li><a href="#features">Hospital HIMS</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Specialties</h5>
        <ul>
          <li><a href="#specialties">Dermatology</a></li>
          <li><a href="#specialties">Physiotherapy</a></li>
          <li><a href="#specialties">Dental</a></li>
          <li><a href="#specialties">Ophthalmology</a></li>
          <li><a href="#specialties">Orthopaedics</a></li>
          <li><a href="#specialties">ENT &amp; Gynaecology</a></li>
        </ul>
      </div>
      <div class="footer-col">
        <h5>Company</h5>
        <ul>
          <li><a href="#about">About RH Technology</a></li>
          <li><a href="#pricing">Pricing</a></li>
          <li><a href="#">Security</a></li>
          <li><a href="#">DPDP Privacy Policy</a></li>
          <li><a href="#">Terms of Service</a></li>
          <li><a href="#">Contact</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2026 RH Technology Pvt. Ltd., Pune, Maharashtra. All rights reserved.</p>
      <p>CIN: U72900MH2026PTC000000 · GSTIN: 27XXXXX0000X1Z0</p>
    </div>
  </div>
</footer>

<script>
  function switchSpec(id, event) {
    console.log('[ClinicOS][welcome:switchSpec]', { id: id });
    document.querySelectorAll('.spec-content').forEach(el => el.classList.remove('active'));
    document.querySelectorAll('.spec-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('spec-' + id).classList.add('active');
    event.currentTarget.classList.add('active');
  }
  console.log('[ClinicOS][welcome:footer]', { ready: true });
</script>
</body>
</html>
<?php /**PATH /Users/akash/Downloads/clinicos-main/backend/resources/views/welcome.blade.php ENDPATH**/ ?>