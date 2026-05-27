<style>
  .hero.hub-hero.hero--compact{min-height:380px;padding:6.75rem 0 3rem;display:flex;align-items:center;text-align:center;}
  .hero.hub-hero.hero--compact .container{max-width:960px;margin:0 auto;}
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
  @media (max-width:1100px){.hub-grid.services-grid{grid-template-columns:repeat(3,minmax(0,1fr));}.hub-grid.guides-grid{grid-template-columns:repeat(2,minmax(0,1fr));}}
  @media (max-width:900px){.service-zones-panel,.zone-layout{grid-template-columns:1fr}.mini-map-card{order:-1}}
  @media (max-width:700px){.hero.hub-hero.hero--compact{min-height:330px;padding:6rem 0 2.4rem}.hero.hub-hero.hero--compact h1{font-size:2rem}.hero.hub-hero.hero--compact p{font-size:1rem}.hub-section{padding:2.6rem 0}.hub-grid.services-grid,.hub-grid.guides-grid{grid-template-columns:1fr}.service-zones-copy,.mini-map-card,.zone-map-card,.zone-list-card{padding:18px}}
</style>
