<style>
  .hero.hub-hero.hero--compact{min-height:380px;padding:6.75rem 0 3rem;display:flex;align-items:center;text-align:center;position:relative;overflow:hidden;background:#102033;}
  .hero.hub-hero.hero--compact .hero-bg{position:absolute;inset:0;z-index:0;}
  .hero.hub-hero.hero--compact .hero-img,.hero.hub-hero.hero--compact .hero-visual-fallback{width:100%;height:100%;display:block;object-fit:cover;object-position:var(--hero-image-position,center center);}
  .hero.hub-hero.hero--compact .hero-visual-fallback{background:linear-gradient(135deg,#0f2437 0%,#174d72 45%,#eef7ff 140%);}
  .hero.hub-hero.hero--compact .hero-visual-fallback:before{content:"";position:absolute;inset:0;background:radial-gradient(circle at 22% 28%,rgba(0,116,232,.28),transparent 32%),linear-gradient(120deg,rgba(255,255,255,.08),transparent 45%);}
  .hero.hub-hero.hero--compact .hero-overlay{position:absolute;inset:0;background:rgba(5,16,28,.48);}
  .hero.hub-hero.hero--compact .hero-overlay--strong{background:rgba(5,16,28,.58);}
  .hero.hub-hero.hero--compact .hero-overlay--soft{background:linear-gradient(180deg,rgba(5,16,28,.48),rgba(5,16,28,.38));}
  .hero.hub-hero.hero--compact .container{max-width:960px;margin:0 auto;}
  .hero.hub-hero.hero--compact>.container{position:relative;z-index:1;}
  .hero.hub-hero.hero--compact h1{font-size:3rem;line-height:1.12;margin:0 auto .85rem;}
  .hero.hub-hero.hero--compact p{max-width:760px;margin:0 auto;color:#eef5fb;line-height:1.65;font-size:1.08rem;}
  .hero.hub-hero.hero--compact .cta-group{margin-top:1.25rem;}
  .hub-section{padding:3.5rem 0;background:#fff;}
  .hub-section.alt{background:#f7faff;}
  .hub-section .container>.section-title,.hub-section .container>.hub-kicker{text-align:center;}
  .hub-section .container>.hub-muted{max-width:860px;margin-left:auto;margin-right:auto;text-align:center;}
  .hub-kicker{color:#0074e8;font-weight:800;text-transform:uppercase;font-size:.78rem;letter-spacing:.08em;margin-bottom:.6rem;}
  .hub-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:18px;margin-top:1.5rem;align-items:stretch;}
  .hub-grid.services-grid{grid-template-columns:repeat(5,minmax(0,1fr));}
  .hub-grid.guides-grid{grid-template-columns:repeat(3,minmax(0,1fr));}
  .hub-card{border:1px solid #e7edf5;border-radius:14px;background:#fff;padding:22px;box-shadow:0 5px 18px rgba(15,23,42,.06);display:flex;flex-direction:column;height:100%;transition:transform .16s ease,box-shadow .16s ease,border-color .16s ease;}
  .hub-card:hover{transform:translateY(-3px);box-shadow:0 12px 26px rgba(15,23,42,.09);border-color:#d6e6f6;}
  .hub-card h2,.hub-card h3{font-size:1.12rem;font-weight:800;margin:0 0 .7rem;color:#142033;}
  .hub-card p{color:#425466;line-height:1.65;margin-bottom:1rem;flex:1;}
  .hub-card .btn{align-self:center;margin-top:auto;min-width:160px;}
  .hub-links{display:flex;flex-wrap:wrap;justify-content:center;gap:.55rem;margin-top:1rem;}
  .hub-pill{border:1px solid #dbe7f5;border-radius:999px;padding:.45rem .72rem;text-decoration:none;color:#12324f;background:#fff;font-weight:700;font-size:.92rem;}
  .hub-pill:hover{border-color:#0074e8;color:#0074e8;background:#f4f9ff;}
  .hub-cta{margin-top:1.4rem;display:flex;flex-wrap:wrap;justify-content:center;gap:.75rem;}
  .hub-muted{color:#5c6b7c;line-height:1.75;}
  .service-zones-panel,.zone-layout{display:grid;grid-template-columns:1.05fr .95fr;gap:28px;align-items:center;margin-top:1.75rem;}
  .service-zones-copy,.mini-map-card,.zone-map-card,.zone-list-card{background:#fff;border:1px solid #e4edf8;border-radius:16px;box-shadow:0 8px 24px rgba(15,23,42,.06);padding:24px;}
  .mini-map-card,.zone-map-card{background:linear-gradient(180deg,#f8fbff 0%,#eef7ff 100%);}
  .marina-map{width:100%;height:auto;display:block;}
  .marina-map .sea{fill:#dff3ff;}
  .marina-map .coast{fill:none;stroke:#7db6e8;stroke-width:3;stroke-linecap:round;}
  .marina-map .route{fill:none;stroke:#c7d9ec;stroke-width:2;stroke-dasharray:5 7;stroke-linecap:round;}
  .marina-map .zone-shape{fill:#eaf4ff;stroke:#0074e8;stroke-width:1.5;transition:fill .16s ease,stroke .16s ease,transform .16s ease;}
  .marina-map .map-label{font-size:12px;font-weight:800;fill:#12324f;opacity:.82;pointer-events:none;transition:opacity .16s ease,fill .16s ease;}
  .marina-map .map-zone:hover .zone-shape,.marina-map .map-zone:focus .zone-shape{fill:#bfe0ff;stroke:#005fc0;}
  .marina-map .map-zone:hover .map-label,.marina-map .map-zone:focus .map-label{fill:#005fc0;opacity:1;}
  .zone-list-card .hub-muted{text-align:center;}
  .service-detail-grid,.service-step-grid,.service-faq-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px;margin-top:1.5rem;align-items:stretch;}
  .service-step-grid{grid-template-columns:repeat(3,minmax(0,1fr));}
  .service-detail-card,.service-step,.service-faq-card{border:1px solid #e7edf5;border-radius:14px;background:#fff;padding:22px;box-shadow:0 5px 18px rgba(15,23,42,.06);}
  .service-detail-card h3,.service-faq-card h3{font-size:1.08rem;font-weight:800;margin:0 0 .75rem;color:#142033;}
  .service-detail-card ul{margin:0;padding-left:1.15rem;color:#425466;line-height:1.72;}
  .service-detail-card li+li{margin-top:.45rem;}
  .service-step{display:flex;gap:1rem;align-items:flex-start;}
  .service-step-number{width:2rem;height:2rem;border-radius:50%;background:#eaf4ff;color:#005fc0;display:inline-flex;align-items:center;justify-content:center;font-weight:900;flex:0 0 auto;}
  .service-step p,.service-faq-card p{margin:0;color:#425466;line-height:1.68;}
  .service-inline-cta{margin-top:1.7rem;}
  .image-text-block{display:grid;grid-template-columns:minmax(0,.95fr) minmax(0,1.05fr);gap:28px;align-items:center;margin:1.75rem 0;}
  .image-text-block--reverse .image-text-block__media{order:2;}
  .image-text-block__media{border:1px solid #e4edf8;border-radius:16px;box-shadow:0 8px 24px rgba(15,23,42,.06);overflow:hidden;background:#f7faff;}
  .image-text-block__media img,.image-text-block__fallback{width:100%;aspect-ratio:4/3;display:block;object-fit:cover;}
  .image-text-block__fallback{background:linear-gradient(135deg,#eef7ff,#dceeff);}
  .image-text-block__caption{font-size:.88rem;color:#5c6b7c;margin:0;padding:.8rem 1rem;background:#fff;}
  .image-text-block__content{padding:6px 0;}
  @media (max-width:1100px){.hub-grid.services-grid{grid-template-columns:repeat(3,minmax(0,1fr));}.hub-grid.guides-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
  @media (max-width:900px){.service-zones-panel,.zone-layout,.image-text-block{grid-template-columns:1fr}.mini-map-card{order:-1}.image-text-block--reverse .image-text-block__media{order:0}}
  @media (max-width:700px){.service-detail-grid,.service-step-grid,.service-faq-grid{grid-template-columns:1fr}.service-detail-card,.service-step,.service-faq-card{padding:18px}.image-text-block__media img,.image-text-block__fallback{aspect-ratio:16/10}}
  @media (max-width:700px){.hero.hub-hero.hero--compact{min-height:330px;padding:6rem 0 2.4rem}.hero.hub-hero.hero--compact h1{font-size:2rem}.hero.hub-hero.hero--compact p{font-size:1rem}.hub-section{padding:2.6rem 0}.hub-grid.services-grid,.hub-grid.guides-grid{grid-template-columns:1fr}.service-zones-copy,.mini-map-card,.zone-map-card,.zone-list-card{padding:18px}}

  /* =============================================
     MC-MAP — Marina Baixa SVG (Costa Blanca)
     ============================================= */

  /* SVG canvas */
  .mc-map-svg{width:100%;height:auto;display:block;border-radius:10px;overflow:hidden;}
  .mc-map-full{max-height:380px;}

  /* Geography layers */
  .mc-sea{fill:#c4e4f6;}
  .mc-land{fill:#f0ebe2;}
  .mc-mtn{fill:#d6e0cc;opacity:.55;}
  .mc-coast-line{fill:none;stroke:#78aed0;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;}

  /* Mountain peak decorations */
  .mc-peaks path{fill:#b8a880;opacity:.45;stroke:#a0906a;stroke-width:.5;}
  .mc-peaks{pointer-events:none;}

  /* Region / sea labels */
  .mc-sea-label{font-size:10px;fill:#2a6080;font-style:italic;opacity:.65;pointer-events:none;font-family:inherit;}
  .mc-region-label{font-size:9px;fill:#1e4f70;font-weight:700;text-transform:uppercase;letter-spacing:.05em;opacity:.6;pointer-events:none;font-family:inherit;}

  /* Zone groups */
  .mc-zone{outline:none;}
  a.mc-zone{cursor:pointer;}
  a.mc-zone:focus{outline:2px solid #0074e8;outline-offset:2px;border-radius:4px;}

  /* Primary dot (secondary localities) */
  .mc-dot-sm{fill:#5e9bbf;stroke:#fff;stroke-width:1.5;transition:fill .14s ease;}
  .mc-zone-secondary:hover .mc-dot-sm,
  .mc-zone-secondary:focus .mc-dot-sm{fill:#0074e8;}

  /* Primary dot (main localities) */
  .mc-dot{fill:#0074e8;stroke:#fff;stroke-width:2;transition:fill .16s ease;}
  .mc-zone-primary:hover .mc-dot,
  .mc-zone-primary:focus .mc-dot{fill:#004fa3;}

  /* Benidorm — featured dot */
  .mc-dot-main{fill:#00387a;stroke:#fff;stroke-width:2.5;transition:fill .16s ease;}
  .mc-zone-main:hover .mc-dot-main,
  .mc-zone-main:focus .mc-dot-main{fill:#0056b3;}

  /* Labels — base */
  .mc-label{font-family:inherit;pointer-events:none;}

  /* Always-visible labels (primary localities) */
  .mc-label-visible{font-size:12px;fill:#1a2e45;font-weight:700;opacity:.88;transition:fill .16s ease;}
  .mc-zone-primary:hover .mc-label-visible,
  .mc-zone-primary:focus .mc-label-visible{fill:#003d80;opacity:1;}

  /* Benidorm label */
  .mc-label-main{font-size:13px;fill:#0a1e35;font-weight:800;opacity:1;transition:fill .16s ease;}
  .mc-zone-main:hover .mc-label-main,
  .mc-zone-main:focus .mc-label-main{fill:#003d80;}

  /* Hover-only labels (secondary localities) */
  .mc-label-hover{font-size:10.5px;fill:#1a2e45;font-weight:600;opacity:0;transition:opacity .15s ease;}
  .mc-zone-secondary:hover .mc-label-hover,
  .mc-zone-secondary:focus .mc-label-hover{opacity:1;}

  /* Responsive: on small screens the map shrinks gracefully */
  @media (max-width:520px){
    .mc-map-full{max-height:260px;}
    .mc-label-visible{font-size:10px;}
    .mc-label-main{font-size:11px;}
  }
</style>
