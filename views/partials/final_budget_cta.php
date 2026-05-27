<?php
$lang = $lang ?? 'es';
if ($lang !== 'es') {
    return;
}
?>
<style>
  .budget-panel.budget-panel--final{background:#f7f9fc;padding:2.75rem 0;}
  .budget-panel.budget-panel--final .box{background:rgba(255,255,255,.96);backdrop-filter:saturate(180%) blur(10px);-webkit-backdrop-filter:saturate(180%) blur(10px);border:1px solid rgba(0,0,0,.06);border-radius:16px;box-shadow:0 12px 34px rgba(0,0,0,.10);padding:2.5rem 2.25rem;}
  .budget-panel.budget-panel--final .grid{display:grid;grid-template-columns:1.35fr 1fr;gap:2.25rem;align-items:center;}
  .budget-panel.budget-panel--final h2{margin:0 0 .6rem;font-weight:900;letter-spacing:-.02em;color:var(--main,#111);}
  .budget-panel.budget-panel--final p{margin:0;color:#3b4a5a;opacity:.95;line-height:1.75;}
  .budget-panel.budget-panel--final .bullets{margin:1.1rem 0 0;padding:0;list-style:none;color:#425264;}
  .budget-panel.budget-panel--final .bullets li{margin:.35rem 0;display:flex;gap:.6rem;align-items:flex-start;}
  .budget-panel.budget-panel--final .bullets .dot{width:.5rem;height:.5rem;margin-top:.55rem;border-radius:50%;background:#c9d7e6;flex:0 0 auto;}
  .budget-panel.budget-panel--final .cta-stack{display:flex;flex-direction:column;gap:.75rem;}
  .budget-panel.budget-panel--final .cta-btn2{display:flex;align-items:center;justify-content:center;gap:.6rem;width:100%;padding:.9rem 1.05rem;border-radius:10px;font-weight:700;text-decoration:none!important;background:transparent;border:2px solid;box-shadow:none;transition:transform .15s ease,background-color .15s ease,color .15s ease,border-color .15s ease;}
  .budget-panel.budget-panel--final .cta-btn2 svg{width:20px;height:20px;flex:0 0 auto;}
  .budget-panel.budget-panel--final .cta-outline-blue{color:var(--accent,#0074e8);border-color:var(--accent,#0074e8);}
  .budget-panel.budget-panel--final .cta-outline-blue:hover{background:rgba(0,116,232,.06);transform:translateY(-1px);}
  .budget-panel.budget-panel--final .cta-outline-dark{color:#1b2a3a;border-color:#d7e0ea;}
  .budget-panel.budget-panel--final .cta-outline-dark:hover{background:rgba(24,32,45,.045);transform:translateY(-1px);}
  .budget-panel.budget-panel--final .cta-outline-wa{color:var(--wa,#25d366);border-color:var(--wa,#25d366);}
  .budget-panel.budget-panel--final .cta-outline-wa:hover{background:rgba(37,211,102,.08);transform:translateY(-1px);}
  .budget-panel.budget-panel--final .cta-note{margin-top:.9rem;font-size:.9rem;color:#667789;text-align:center;}
  @media (max-width:991.98px){.budget-panel.budget-panel--final{padding:3.25rem 0}.budget-panel.budget-panel--final .box{padding:2rem 1.35rem}.budget-panel.budget-panel--final .grid{grid-template-columns:1fr;gap:1.5rem}}
</style>
<section class="budget-panel budget-panel--final" id="presupuesto">
  <div class="container">
    <div class="box">
      <div class="grid">
        <div>
          <h2>Solicita tu presupuesto</h2>
          <p>Instalaci&oacute;n, mantenimiento o reparaci&oacute;n. Te asesoramos con una propuesta clara y honesta.</p>
          <ul class="bullets" aria-hidden="true">
            <li><span class="dot"></span><span>Respuesta r&aacute;pida y sin compromiso</span></li>
            <li><span class="dot"></span><span>Servicio en Benidorm y Marina Baixa</span></li>
            <li><span class="dot"></span><span>Trabajo profesional con garant&iacute;a real</span></li>
          </ul>
        </div>
        <div>
          <div class="cta-stack">
            <a class="cta-btn2 cta-outline-blue js-track" data-ev="cta_quote_final" data-bs-toggle="modal" data-bs-target="#quoteModal" href="#quote" aria-controls="quoteModal">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="5" width="18" height="14" rx="2" ry="2"></rect>
                <path d="m3 7 9 6 9-6"></path>
              </svg>
              <span>Enviar formulario</span>
            </a>
            <a class="cta-btn2 cta-outline-dark js-track" data-ev="cta_call_final" href="tel:+34613026600" aria-label="Llamar por tel&eacute;fono">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.8 19.8 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.08 4.18 2 2 0 0 1 4.06 2h3a2 2 0 0 1 2 1.72c.12.86.31 1.7.56 2.5a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.58-1.08a2 2 0 0 1 2.11-.45c.8.25 1.64.44 2.5.56A2 2 0 0 1 22 16.92z"></path>
              </svg>
              <span>Llamar +34 613 02 66 00</span>
            </a>
            <a class="cta-btn2 cta-outline-wa js-track" data-ev="cta_whatsapp_final" href="https://wa.me/34613026600" target="_blank" rel="noopener" aria-label="Escr&iacute;benos por WhatsApp">
              <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M.057 24l1.687-6.163a11.867 11.867 0 1 1 4.279 4.29L.057 24Zm6.597-3.807.39.232a9.868 9.868 0 1 0-3.63-3.63l.232.39-.99 3.62 3.998-1.612ZM8.85 7.845c-.176-.392-.362-.4-.53-.407-.137-.006-.294-.006-.451-.006a.868.868 0 0 0-.626.294c-.215.23-.827.807-.827 1.968s.846 2.282.964 2.44c.118.157 1.63 2.618 4.02 3.563.562.227 1 .363 1.341.465.563.179 1.075.153 1.48.093.451-.068 1.39-.567 1.586-1.115.196-.548.196-1.018.137-1.115-.059-.098-.215-.157-.451-.274-.235-.118-1.39-.685-1.604-.763-.215-.078-.373-.117-.53.118-.157.235-.607.763-.744.92-.137.157-.274.176-.51.059-.235-.118-.993-.366-1.89-1.17-.699-.622-1.172-1.39-1.309-1.625-.137-.235-.014-.362.104-.48.107-.106.235-.274.353-.411.117-.137.156-.235.235-.392.078-.157.039-.294-.02-.411-.059-.118-.51-1.246-.716-1.706Z"></path>
              </svg>
              <span>Escr&iacute;benos por WhatsApp</span>
            </a>
          </div>
          <div class="cta-note">Elige c&oacute;mo prefieres que te contactemos. Respuesta r&aacute;pida.</div>
        </div>
      </div>
    </div>
  </div>
</section>
