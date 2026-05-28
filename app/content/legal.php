<?php
declare(strict_types=1);

/**
 * Legal pages configuration.
 * Returns an array indexed by URL path, each entry containing:
 *   type       - 'cookies' | 'privacy' | 'legal'
 *   lang       - language code
 *   title      - HTML-safe page title
 *   description- HTML-safe meta description
 *   canonical  - absolute canonical URL
 *   body       - HTML body content (section element)
 */

$_base = 'https://masqueclima.es';

// ---------------------------------------------------------------------------
// Shared inline CSS (included once per legal page body)
// ---------------------------------------------------------------------------
$_css = '<style>
.legal-page{padding:7.5rem 0 3.5rem;background:#fff;}
.legal-page .legal-wrap{max-width:880px;margin:0 auto;}
.legal-page h1{font-size:2.35rem;line-height:1.16;margin:0 0 1rem;color:#102033;font-weight:900;}
.legal-page h2{font-size:1.25rem;margin:2rem 0 .75rem;color:#142033;font-weight:800;}
.legal-page p,.legal-page li{color:#425466;line-height:1.75;}
.legal-page ul{padding-left:1.2rem;}
.legal-page .legal-date{margin-top:2rem;color:#667789;font-size:.95rem;}
@media (max-width:700px){.legal-page{padding:6.5rem 0 2.75rem}.legal-page h1{font-size:2rem}}
</style>';

// ---------------------------------------------------------------------------
// HREFLANG MAPS  (used by render_legal_page to inject <link rel="alternate">)
// ---------------------------------------------------------------------------
// Defined separately so front_controller can access them without loading all bodies.

// ---------------------------------------------------------------------------
// BODIES
// ---------------------------------------------------------------------------

// ── Cookies ─────────────────────────────────────────────────────────────────

$_cookies_es = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Pol&iacute;tica de cookies</h1>
      <p>Esta pol&iacute;tica explica de forma general c&oacute;mo puede utilizar cookies la web de +QUECLIMA y c&oacute;mo puedes gestionarlas desde tu navegador.</p>
      <h2>Qu&eacute; son las cookies</h2>
      <p>Las cookies son peque&ntilde;os archivos que una web puede guardar en el dispositivo del usuario para recordar informaci&oacute;n t&eacute;cnica, facilitar la navegaci&oacute;n o permitir determinadas funciones.</p>
      <h2>Qu&eacute; cookies puede usar esta web</h2>
      <p>La web puede utilizar cookies t&eacute;cnicas necesarias para su funcionamiento, cookies de sesi&oacute;n asociadas a formularios o preferencias b&aacute;sicas, y cookies de terceros cuando se cargan servicios externos.</p>
      <h2>Cookies t&eacute;cnicas necesarias</h2>
      <p>Estas cookies permiten que la web funcione correctamente, mantener una sesi&oacute;n temporal, recordar preferencias b&aacute;sicas como el idioma o proteger formularios frente a env&iacute;os no autorizados.</p>
      <h2>Cookies de sesi&oacute;n y formularios</h2>
      <p>Al usar formularios o ventanas de solicitud de presupuesto, la web puede generar identificadores temporales de sesi&oacute;n para validar el env&iacute;o y mejorar la seguridad. Estas cookies no se utilizan para elaborar perfiles comerciales.</p>
      <h2>Servicios de terceros</h2>
      <p>Algunas p&aacute;ginas pueden cargar contenidos o servicios de terceros, como mapas embebidos, anal&iacute;tica web o widgets externos. Estos proveedores pueden establecer sus propias cookies conforme a sus respectivas pol&iacute;ticas.</p>
      <h2>C&oacute;mo gestionar o bloquear cookies</h2>
      <p>Puedes permitir, bloquear o eliminar cookies desde la configuraci&oacute;n de tu navegador. Ten en cuenta que bloquear algunas cookies t&eacute;cnicas puede afectar al funcionamiento normal de la web o de sus formularios.</p>
      <h2>Contacto</h2>
      <p>Para cualquier consulta sobre esta pol&iacute;tica, puedes contactar con +QUECLIMA a trav&eacute;s de los medios de contacto disponibles en la web.</p>
      <p class="legal-date">Fecha de &uacute;ltima actualizaci&oacute;n: 28 de mayo de 2026.</p>
    </div>
  </div>
</section>
HTML;

$_cookies_en = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Cookie Policy</h1>
      <p>This policy explains how +QUECLIMA may use cookies on this website and how you can manage them from your browser.</p>
      <h2>What are cookies</h2>
      <p>Cookies are small files that a website can save on your device to remember technical information, facilitate browsing or enable certain functions.</p>
      <h2>Cookies this site may use</h2>
      <p>The site may use technically necessary cookies, session cookies associated with forms or basic preferences, and third-party cookies when external services are loaded.</p>
      <h2>Strictly necessary cookies</h2>
      <p>These cookies allow the website to function correctly, maintain a temporary session, remember basic preferences such as language or protect forms against unauthorised submissions.</p>
      <h2>Session and form cookies</h2>
      <p>When using forms or quote request windows, the site may generate temporary session identifiers to validate the submission and improve security. These cookies are not used to build commercial profiles.</p>
      <h2>Third-party services</h2>
      <p>Some pages may load content or services from third parties, such as embedded maps, web analytics or external widgets. These providers may set their own cookies in accordance with their respective policies.</p>
      <h2>How to manage or block cookies</h2>
      <p>You can allow, block or delete cookies from your browser settings. Please note that blocking certain technical cookies may affect the normal operation of the website or its forms.</p>
      <h2>Contact</h2>
      <p>For any queries about this policy, you can contact +QUECLIMA through the contact means available on the site.</p>
      <p class="legal-date">Last updated: 28 May 2026.</p>
    </div>
  </div>
</section>
HTML;

$_cookies_de = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Cookie-Richtlinie</h1>
      <p>Diese Richtlinie erkl&auml;rt allgemein, wie +QUECLIMA auf dieser Website Cookies verwenden kann und wie Sie diese &uuml;ber Ihren Browser verwalten k&ouml;nnen.</p>
      <h2>Was sind Cookies</h2>
      <p>Cookies sind kleine Dateien, die eine Website auf Ihrem Ger&auml;t speichern kann, um technische Informationen zu speichern, die Navigation zu erleichtern oder bestimmte Funktionen bereitzustellen.</p>
      <h2>Welche Cookies diese Website verwenden kann</h2>
      <p>Die Website kann technisch notwendige Cookies, Sitzungs-Cookies f&uuml;r Formulare oder grundlegende Pr&auml;ferenzen sowie Drittanbieter-Cookies verwenden, wenn externe Dienste geladen werden.</p>
      <h2>Technisch notwendige Cookies</h2>
      <p>Diese Cookies erm&ouml;glichen den einwandfreien Betrieb der Website, das Aufrechterhalten einer tempor&auml;ren Sitzung, die Speicherung grundlegender Pr&auml;ferenzen wie der Spracheinstellung und den Schutz von Formularen vor unautorisierten Einsendungen.</p>
      <h2>Sitzungs- und Formular-Cookies</h2>
      <p>Bei der Nutzung von Formularen oder Angebots-Formularen kann die Website tempor&auml;re Sitzungskennungen generieren, um die Einsendung zu validieren und die Sicherheit zu erh&ouml;hen. Diese Cookies werden nicht zur Erstellung kommerzieller Profile genutzt.</p>
      <h2>Drittanbieter-Dienste</h2>
      <p>Einige Seiten k&ouml;nnen Inhalte oder Dienste von Drittanbietern laden, wie eingebettete Karten, Web-Analyse oder externe Widgets. Diese Anbieter k&ouml;nnen gem&auml;&szlig; ihren jeweiligen Richtlinien eigene Cookies setzen.</p>
      <h2>Cookies verwalten oder blockieren</h2>
      <p>Sie k&ouml;nnen Cookies &uuml;ber Ihre Browser-Einstellungen zulassen, blockieren oder l&ouml;schen. Bitte beachten Sie, dass das Blockieren bestimmter technischer Cookies den normalen Betrieb der Website oder ihrer Formulare beeintr&auml;chtigen kann.</p>
      <h2>Kontakt</h2>
      <p>F&uuml;r Fragen zu dieser Richtlinie k&ouml;nnen Sie +QUECLIMA &uuml;ber die auf der Website verf&uuml;gbaren Kontaktm&ouml;glichkeiten erreichen.</p>
      <p class="legal-date">Stand: 28. Mai 2026.</p>
    </div>
  </div>
</section>
HTML;

$_cookies_nl = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Cookiebeleid</h1>
      <p>Dit beleid legt in het algemeen uit hoe +QUECLIMA cookies kan gebruiken op deze website en hoe u ze via uw browser kunt beheren.</p>
      <h2>Wat zijn cookies</h2>
      <p>Cookies zijn kleine bestanden die een website op uw apparaat kan opslaan om technische informatie te onthouden, navigatie te vergemakkelijken of bepaalde functies in te schakelen.</p>
      <h2>Welke cookies deze website kan gebruiken</h2>
      <p>De website kan technisch noodzakelijke cookies, sessie-cookies voor formulieren of basisvoorkeuren, en cookies van derden gebruiken wanneer externe services worden geladen.</p>
      <h2>Strikt noodzakelijke cookies</h2>
      <p>Deze cookies zorgen voor een correcte werking van de website, het handhaven van een tijdelijke sessie, het onthouden van basisvoorkeuren zoals taal, en het beschermen van formulieren tegen ongeautoriseerde inzendingen.</p>
      <h2>Sessie- en formulier-cookies</h2>
      <p>Bij het gebruik van formulieren of offertevensters kan de website tijdelijke sessie-ID's genereren om de inzending te valideren en de beveiliging te verbeteren. Deze cookies worden niet gebruikt om commerci&euml;le profielen op te bouwen.</p>
      <h2>Diensten van derden</h2>
      <p>Sommige pagina's kunnen inhoud of diensten van derden laden, zoals ingesloten kaarten, webanalyse of externe widgets. Deze aanbieders kunnen hun eigen cookies instellen conform hun respectieve beleid.</p>
      <h2>Cookies beheren of blokkeren</h2>
      <p>U kunt cookies toestaan, blokkeren of verwijderen via uw browserinstellingen. Houd er rekening mee dat het blokkeren van bepaalde technische cookies de normale werking van de website of de formulieren kan be&iuml;nvloeden.</p>
      <h2>Contact</h2>
      <p>Voor vragen over dit beleid kunt u contact opnemen met +QUECLIMA via de contactmogelijkheden op de website.</p>
      <p class="legal-date">Laatste update: 28 mei 2026.</p>
    </div>
  </div>
</section>
HTML;

$_cookies_ru = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Политика использования файлов cookie</h1>
      <p>Настоящая политика в общих чертах объясняет, как +QUECLIMA может использовать файлы cookie на этом сайте и как вы можете управлять ими через браузер.</p>
      <h2>Что такое файлы cookie</h2>
      <p>Файлы cookie — это небольшие файлы, которые веб-сайт может сохранять на вашем устройстве для хранения технической информации, упрощения навигации или обеспечения определённых функций.</p>
      <h2>Какие файлы cookie может использовать этот сайт</h2>
      <p>Сайт может использовать технически необходимые файлы cookie, сеансовые файлы cookie, связанные с формами или базовыми настройками, а также сторонние файлы cookie при загрузке внешних сервисов.</p>
      <h2>Технически необходимые файлы cookie</h2>
      <p>Эти файлы cookie обеспечивают корректную работу сайта, сохранение временного сеанса, запоминание базовых настроек, например языка, а также защиту форм от несанкционированных отправок.</p>
      <h2>Сеансовые файлы cookie и файлы cookie форм</h2>
      <p>При использовании форм или окон запроса коммерческого предложения сайт может генерировать временные идентификаторы сеанса для подтверждения отправки и повышения безопасности. Эти файлы cookie не используются для создания коммерческих профилей.</p>
      <h2>Сторонние сервисы</h2>
      <p>На некоторых страницах могут загружаться материалы или сервисы сторонних поставщиков, например встроенные карты, веб-аналитика или внешние виджеты. Такие поставщики могут устанавливать собственные файлы cookie в соответствии со своей политикой.</p>
      <h2>Управление файлами cookie или их блокировка</h2>
      <p>Вы можете разрешить, заблокировать или удалить файлы cookie в настройках браузера. Обратите внимание, что блокировка некоторых технических файлов cookie может повлиять на нормальную работу сайта или его форм.</p>
      <h2>Контакт</h2>
      <p>По любым вопросам, связанным с данной политикой, вы можете связаться с +QUECLIMA через контактные данные, указанные на сайте.</p>
      <p class="legal-date">Дата последнего обновления: 28 мая 2026 г.</p>
    </div>
  </div>
</section>
HTML;

$_cookies_no = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Informasjonskapselerklæring</h1>
      <p>Denne erkl&aelig;ringen forklarer generelt hvordan +QUECLIMA kan bruke informasjonskapsler p&aring; dette nettstedet og hvordan du kan administrere dem fra nettleseren.</p>
      <h2>Hva er informasjonskapsler</h2>
      <p>Informasjonskapsler er sm&aring; filer som et nettsted kan lagre p&aring; enheten din for &aring; huske teknisk informasjon, lette navigasjonen eller aktivere bestemte funksjoner.</p>
      <h2>Hvilke informasjonskapsler dette nettstedet kan bruke</h2>
      <p>Nettstedet kan bruke teknisk n&oslash;dvendige informasjonskapsler, sesjons­informasjonskapsler tilknyttet skjemaer eller grunnleggende innstillinger, samt tredjepartskapsler n&aring;r eksterne tjenester lastes.</p>
      <h2>Strengt n&oslash;dvendige informasjonskapsler</h2>
      <p>Disse kapslene gj&oslash;r det mulig for nettstedet &aring; fungere korrekt, opprettholde en midlertidig &oslash;kt, huske grunnleggende innstillinger som spr&aring;k og beskytte skjemaer mot uautoriserte innsendinger.</p>
      <h2>&Oslash;kt- og skjemainformasjonskapsler</h2>
      <p>N&aring;r du bruker skjemaer eller pristilbudsvinduet, kan nettstedet generere midlertidige sesjons-ID-er for &aring; validere innsendingen og forbedre sikkerheten. Disse kapslene brukes ikke til &aring; bygge kommersielle profiler.</p>
      <h2>Tredjepartstjenester</h2>
      <p>Noen sider kan laste inn innhold eller tjenester fra tredjepart, for eksempel innebygde kart, nettanalyse eller eksterne widgeter. Disse tilbyderne kan sette egne informasjonskapsler i henhold til sin egen politikk.</p>
      <h2>Slik administrerer eller blokkerer du informasjonskapsler</h2>
      <p>Du kan tillate, blokkere eller slette informasjonskapsler fra nettleserinnstillingene dine. Vær oppmerksom p&aring; at blokkering av visse tekniske kapsler kan p&aring;virke normal drift av nettstedet eller skjemaene.</p>
      <h2>Kontakt</h2>
      <p>For sp&oslash;rsm&aring;l om denne erkl&aelig;ringen kan du kontakte +QUECLIMA via kontaktmulighetene p&aring; nettstedet.</p>
      <p class="legal-date">Sist oppdatert: 28. mai 2026.</p>
    </div>
  </div>
</section>
HTML;

// ── Privacy ──────────────────────────────────────────────────────────────────

$_privacy_es = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Pol&iacute;tica de privacidad</h1>
      <p>En cumplimiento del Reglamento (UE) 2016/679 (RGPD) y la Ley Org&aacute;nica 3/2018 (LOPDGDD), le informamos sobre el tratamiento de sus datos personales en esta web.</p>

      <h2>Responsable del tratamiento</h2>
      <p>El responsable del tratamiento es +QUECLIMA, titular de masqueclima.es. Contacto: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>.</p>

      <h2>Datos que recogemos</h2>
      <p>A trav&eacute;s del formulario de contacto recogemos nombre, tel&eacute;fono, direcci&oacute;n de correo electr&oacute;nico (opcional), tipo de servicio solicitado y descripci&oacute;n del trabajo. No se recogen categor&iacute;as especiales de datos.</p>

      <h2>Finalidad y base jur&iacute;dica</h2>
      <p>Los datos se utilizan exclusivamente para gestionar y dar respuesta a su solicitud de informaci&oacute;n o presupuesto. La base jur&iacute;dica del tratamiento es el consentimiento del interesado (art.&nbsp;6.1.a RGPD) y la ejecuci&oacute;n de medidas precontractuales a petici&oacute;n del interesado (art.&nbsp;6.1.b RGPD).</p>

      <h2>Conservaci&oacute;n de datos</h2>
      <p>Los datos se conservan durante el tiempo necesario para atender su solicitud y, posteriormente, durante el plazo m&aacute;ximo de 12 meses, salvo que exista una relaci&oacute;n contractual que justifique un per&iacute;odo mayor.</p>

      <h2>Destinatarios</h2>
      <p>Los datos no se ceden ni venden a terceros con fines comerciales. Para el env&iacute;o del mensaje de respuesta se utiliza un proveedor de servicios de correo electr&oacute;nico que act&uacute;a como encargado del tratamiento. La web puede utilizar Google Analytics 4 con IP anonimizada para anal&iacute;tica de uso agregada.</p>

      <h2>Transferencias internacionales</h2>
      <p>Google Analytics 4 puede implicar una transferencia de datos a Estados Unidos. Google LLC est&aacute; sujeta a las Cla&uacute;sulas Contractuales Est&aacute;ndar aprobadas por la Comisi&oacute;n Europea y cuenta con certificaci&oacute;n en el marco del acuerdo UE&ndash;EE.&nbsp;UU. de protecci&oacute;n de datos.</p>

      <h2>Sus derechos</h2>
      <p>Puede ejercer en cualquier momento sus derechos de acceso, rectificaci&oacute;n, supresi&oacute;n, limitaci&oacute;n del tratamiento, portabilidad y oposici&oacute;n, dirigi&eacute;ndose a <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>. Tambi&eacute;n tiene derecho a presentar una reclamaci&oacute;n ante la Agencia Espa&ntilde;ola de Protecci&oacute;n de Datos (aepd.es).</p>

      <h2>Cookies</h2>
      <p>Para informaci&oacute;n sobre el uso de cookies consulte nuestra <a href="/es/politica-de-cookies/">Pol&iacute;tica de cookies</a>.</p>

      <p class="legal-date">Fecha de &uacute;ltima actualizaci&oacute;n: 28 de mayo de 2026.</p>
    </div>
  </div>
</section>
HTML;

$_privacy_en = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Privacy Policy</h1>
      <p>In compliance with EU Regulation 2016/679 (GDPR), we inform you about the processing of your personal data on this website.</p>

      <h2>Data controller</h2>
      <p>The data controller is +QUECLIMA, owner of masqueclima.es. Contact: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>.</p>

      <h2>Data we collect</h2>
      <p>Through the contact form we collect name, phone number, email address (optional), service type requested and job description. No special categories of data are collected.</p>

      <h2>Purpose and legal basis</h2>
      <p>Data is used exclusively to handle and respond to your information or quote request. The legal basis for processing is the data subject&apos;s consent (Art.&nbsp;6.1.a GDPR) and pre-contractual measures at the data subject&apos;s request (Art.&nbsp;6.1.b GDPR).</p>

      <h2>Retention</h2>
      <p>Data is kept for the time necessary to deal with your request and, thereafter, for a maximum of 12 months, unless a contractual relationship justifies a longer period.</p>

      <h2>Recipients</h2>
      <p>Data is not sold or transferred to third parties for commercial purposes. An email service provider acting as data processor is used to deliver the response message. The site may use Google Analytics 4 with anonymised IP for aggregate usage analytics.</p>

      <h2>International transfers</h2>
      <p>Google Analytics 4 may involve a data transfer to the United States. Google LLC is subject to Standard Contractual Clauses approved by the European Commission and holds certification under the EU&ndash;US Data Privacy Framework.</p>

      <h2>Your rights</h2>
      <p>You may exercise your rights of access, rectification, erasure, restriction of processing, data portability and objection at any time by contacting <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>. You also have the right to lodge a complaint with your national supervisory authority.</p>

      <h2>Cookies</h2>
      <p>For information about cookie use, please see our <a href="/en/cookie-policy/">Cookie Policy</a>.</p>

      <p class="legal-date">Last updated: 28 May 2026.</p>
    </div>
  </div>
</section>
HTML;

$_privacy_de = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Datenschutzerkl&auml;rung</h1>
      <p>Gem&auml;&szlig; der EU-Verordnung 2016/679 (DSGVO) informieren wir Sie &uuml;ber die Verarbeitung Ihrer personenbezogenen Daten auf dieser Website.</p>

      <h2>Verantwortlicher</h2>
      <p>Verantwortlicher f&uuml;r die Datenverarbeitung ist +QUECLIMA, Inhaber von masqueclima.es. Kontakt: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>.</p>

      <h2>Erhobene Daten</h2>
      <p>Wir erfassen &uuml;ber das Kontaktformular Name, Telefonnummer, E-Mail-Adresse (optional), Art der gew&uuml;nschten Dienstleistung und Auftragsbeschreibung. Besondere Kategorien personenbezogener Daten werden nicht erhoben.</p>

      <h2>Zweck und Rechtsgrundlage</h2>
      <p>Die Daten werden ausschlie&szlig;lich verwendet, um Ihre Anfrage oder Ihr Angebot zu bearbeiten und zu beantworten. Rechtsgrundlage ist die Einwilligung der betroffenen Person (Art.&nbsp;6 Abs.&nbsp;1 lit.&nbsp;a DSGVO) sowie vorvertragliche Ma&szlig;nahmen auf Anfrage der betroffenen Person (Art.&nbsp;6 Abs.&nbsp;1 lit.&nbsp;b DSGVO).</p>

      <h2>Aufbewahrung</h2>
      <p>Die Daten werden so lange gespeichert, wie es zur Bearbeitung Ihrer Anfrage erforderlich ist, anschlie&szlig;end f&uuml;r maximal 12 Monate, sofern kein Vertragsverh&auml;ltnis einen l&auml;ngeren Zeitraum rechtfertigt.</p>

      <h2>Empf&auml;nger</h2>
      <p>Die Daten werden nicht zu kommerziellen Zwecken an Dritte weitergegeben oder verkauft. F&uuml;r die Zustellung der Antwortnachricht wird ein E-Mail-Dienstleister als Auftragsverarbeiter eingesetzt. Die Website kann Google Analytics 4 mit anonymisierter IP f&uuml;r aggregierte Nutzungsanalysen verwenden.</p>

      <h2>Internationale &Uuml;bermittlungen</h2>
      <p>Google Analytics 4 kann eine Daten&uuml;bermittlung in die USA beinhalten. Google LLC unterliegt den von der Europ&auml;ischen Kommission genehmigten Standardvertragsklauseln und ist nach dem EU-US-Datenschutzrahmen zertifiziert.</p>

      <h2>Ihre Rechte</h2>
      <p>Sie k&ouml;nnen jederzeit Ihre Rechte auf Auskunft, Berichtigung, L&ouml;schung, Einschr&auml;nkung der Verarbeitung, Daten&uuml;bertragbarkeit und Widerspruch geltend machen, indem Sie sich an <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a> wenden. Sie haben zudem das Recht, Beschwerde bei der zust&auml;ndigen Aufsichtsbeh&ouml;rde einzulegen.</p>

      <h2>Cookies</h2>
      <p>Informationen zur Cookie-Verwendung finden Sie in unserer <a href="/de/cookie-richtlinie/">Cookie-Richtlinie</a>.</p>

      <p class="legal-date">Stand: 28. Mai 2026.</p>
    </div>
  </div>
</section>
HTML;

$_privacy_nl = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Privacybeleid</h1>
      <p>In overeenstemming met EU-Verordening 2016/679 (AVG) informeren wij u over de verwerking van uw persoonsgegevens op deze website.</p>

      <h2>Verwerkingsverantwoordelijke</h2>
      <p>De verwerkingsverantwoordelijke is +QUECLIMA, eigenaar van masqueclima.es. Contact: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>.</p>

      <h2>Gegevens die wij verzamelen</h2>
      <p>Via het contactformulier verzamelen wij naam, telefoonnummer, e-mailadres (optioneel), type gevraagde dienst en werkbeschrijving. Er worden geen bijzondere categorie&euml;n persoonsgegevens verzameld.</p>

      <h2>Doel en rechtsgrondslag</h2>
      <p>De gegevens worden uitsluitend gebruikt om uw informatie- of offerteaanvraag te beheren en te beantwoorden. De rechtsgrondslag is de toestemming van de betrokkene (art.&nbsp;6 lid 1 sub a AVG) en precontractuele maatregelen op verzoek van de betrokkene (art.&nbsp;6 lid 1 sub b AVG).</p>

      <h2>Bewaartermijn</h2>
      <p>De gegevens worden bewaard zo lang als nodig is om uw aanvraag te behandelen, daarna maximaal 12 maanden, tenzij een contractuele relatie een langere periode rechtvaardigt.</p>

      <h2>Ontvangers</h2>
      <p>De gegevens worden niet verkocht of doorgegeven aan derden voor commerci&euml;le doeleinden. Voor het verzenden van het antwoordbericht wordt een e-maildienstprovider ingezet als verwerker. De website kan Google Analytics 4 gebruiken met geanonimiseerd IP voor geaggregeerde gebruiksanalyse.</p>

      <h2>Internationale doorgiften</h2>
      <p>Google Analytics 4 kan een doorgifte van gegevens naar de VS inhouden. Google LLC is onderworpen aan de door de Europese Commissie goedgekeurde standaard contractbepalingen en is gecertificeerd onder het EU-VS-gegevensbeschermingskader.</p>

      <h2>Uw rechten</h2>
      <p>U kunt te allen tijde uw rechten op inzage, rectificatie, verwijdering, beperking van verwerking, gegevensoverdraagbaarheid en bezwaar uitoefenen door contact op te nemen via <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>. U heeft ook het recht een klacht in te dienen bij de bevoegde toezichthoudende autoriteit.</p>

      <h2>Cookies</h2>
      <p>Voor informatie over het gebruik van cookies verwijzen wij u naar ons <a href="/nl/cookiebeleid/">Cookiebeleid</a>.</p>

      <p class="legal-date">Laatste update: 28 mei 2026.</p>
    </div>
  </div>
</section>
HTML;

$_privacy_ru = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Политика конфиденциальности</h1>
      <p>В соответствии с Регламентом ЕС 2016/679 (GDPR) мы информируем вас об обработке ваших персональных данных на данном сайте.</p>

      <h2>Контролёр данных</h2>
      <p>Контролёром данных является +QUECLIMA, владелец masqueclima.es. Контакт: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>.</p>

      <h2>Собираемые данные</h2>
      <p>Через контактную форму мы собираем имя, номер телефона, адрес электронной почты (по желанию), тип запрашиваемой услуги и описание работ. Данные особых категорий не собираются.</p>

      <h2>Цель и правовое основание</h2>
      <p>Данные используются исключительно для обработки и ответа на ваш запрос информации или коммерческого предложения. Правовым основанием является согласие субъекта данных (ст.&nbsp;6 п.&nbsp;1 (a) GDPR) и принятие преддоговорных мер по запросу субъекта данных (ст.&nbsp;6 п.&nbsp;1 (b) GDPR).</p>

      <h2>Хранение данных</h2>
      <p>Данные хранятся в течение времени, необходимого для обработки вашего запроса, а после — не более 12 месяцев, если только договорные отношения не обусловливают более длительный срок.</p>

      <h2>Получатели</h2>
      <p>Данные не продаются и не передаются третьим лицам в коммерческих целях. Для отправки ответного сообщения используется поставщик услуг электронной почты, действующий в качестве обработчика данных. Сайт может использовать Google Analytics 4 с анонимизированным IP для агрегированной аналитики использования.</p>

      <h2>Международные передачи</h2>
      <p>Google Analytics 4 может предполагать передачу данных в США. Google LLC подчиняется Стандартным договорным оговоркам, утверждённым Европейской комиссией, и имеет сертификат в рамках Рамочного соглашения о конфиденциальности данных между ЕС и США.</p>

      <h2>Ваши права</h2>
      <p>Вы вправе в любое время воспользоваться правами на доступ, исправление, удаление, ограничение обработки, переносимость данных и возражение, обратившись по адресу <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>. Также вы вправе подать жалобу в надзорный орган по защите данных.</p>

      <h2>Файлы cookie</h2>
      <p>Информацию об использовании файлов cookie см. в нашей <a href="/ru/cookie-policy/">Политике использования файлов cookie</a>.</p>

      <p class="legal-date">Дата последнего обновления: 28 мая 2026 г.</p>
    </div>
  </div>
</section>
HTML;

$_privacy_no = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Personvernerkl&aelig;ring</h1>
      <p>I samsvar med EU-forordning 2016/679 (GDPR) informerer vi deg om behandlingen av dine personopplysninger p&aring; dette nettstedet.</p>

      <h2>Behandlingsansvarlig</h2>
      <p>Behandlingsansvarlig er +QUECLIMA, eier av masqueclima.es. Kontakt: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>.</p>

      <h2>Data vi samler inn</h2>
      <p>Via kontaktskjemaet samler vi inn navn, telefonnummer, e-postadresse (valgfritt), &oslash;nsket tjenestetype og jobbeskrivelse. Ingen s&aelig;rlige kategorier av personopplysninger samles inn.</p>

      <h2>Form&aring;l og rettslig grunnlag</h2>
      <p>Opplysningene brukes utelukkende til &aring; behandle og besvare din informasjons- eller tilbudsf&oslash;resp&oslash;rsel. Rettslig grunnlag er den registrertes samtykke (art.&nbsp;6 nr.&nbsp;1 bokstav a GDPR) og prekon­traktuelle tiltak p&aring; den registrertes anmodning (art.&nbsp;6 nr.&nbsp;1 bokstav b GDPR).</p>

      <h2>Oppbevaring</h2>
      <p>Data oppbevares s&aring; lenge det er n&oslash;dvendig for &aring; behandle din foresp&oslash;rsel, og deretter i maksimalt 12 m&aring;neder, med mindre et kontraktsforhold begrunner en lengre periode.</p>

      <h2>Mottakere</h2>
      <p>Opplysningene selges ikke og overf&oslash;res ikke til tredjeparter for kommersielle form&aring;l. En e-posttjenesteleverand&oslash;r som fungerer som databehandler, benyttes til &aring; sende svarmelding. Nettstedet kan bruke Google Analytics 4 med anonymisert IP for aggregert bruksanalyse.</p>

      <h2>Internasjonale overf&oslash;ringer</h2>
      <p>Google Analytics 4 kan inneb&aelig;re overf&oslash;ring av data til USA. Google LLC er underlagt Europakommisjonens godkjente standardkontraktsbestemmelser og er sertifisert under EU-US Data Privacy Framework.</p>

      <h2>Dine rettigheter</h2>
      <p>Du kan n&aring;r som helst ut&oslash;ve rettighetene dine til innsyn, retting, sletting, begrensning av behandling, dataportabilitet og innsigelse ved &aring; kontakte <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a>. Du har ogs&aring; rett til &aring; klage til din nasjonale tilsynsmyndighet.</p>

      <h2>Informasjonskapsler</h2>
      <p>For informasjon om bruk av informasjonskapsler, se v&aring;r <a href="/no/cookie-policy/">Informasjonskapselerklæring</a>.</p>

      <p class="legal-date">Sist oppdatert: 28. mai 2026.</p>
    </div>
  </div>
</section>
HTML;

// ── Legal Notice ──────────────────────────────────────────────────────────────

$_legal_es = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Aviso legal</h1>

      <h2>Datos del titular</h2>
      <p>Nombre comercial: <strong>+QUECLIMA</strong><br>
      Sitio web: masqueclima.es<br>
      Correo electr&oacute;nico: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a></p>

      <h2>Actividad</h2>
      <p>+QUECLIMA ofrece servicios de instalaci&oacute;n, mantenimiento y reparaci&oacute;n de sistemas de climatizaci&oacute;n, calefacci&oacute;n y energ&iacute;a solar en la provincia de Alicante (Espa&ntilde;a).</p>

      <h2>Propiedad intelectual</h2>
      <p>Todos los contenidos de este sitio web &mdash;textos, im&aacute;genes, dise&ntilde;o, marcas y cualquier otro elemento&mdash; son propiedad del titular o han sido cedidos bajo licencia. Queda prohibida su reproducci&oacute;n, distribuci&oacute;n o modificaci&oacute;n sin autorizaci&oacute;n expresa.</p>

      <h2>Exenci&oacute;n de responsabilidad</h2>
      <p>El titular no se hace responsable de los da&ntilde;os derivados del uso del sitio web, de posibles errores en los contenidos ni de la disponibilidad del servicio. Los contenidos son informativos y no constituyen asesoramiento t&eacute;cnico vinculante.</p>

      <h2>Legislaci&oacute;n aplicable y fuero</h2>
      <p>Este aviso legal se rige por la legislaci&oacute;n espa&ntilde;ola. Para cualquier controversia las partes se someten a los Juzgados y Tribunales de Alicante, salvo que la normativa aplicable establezca un fuero distinto.</p>

      <p class="legal-date">Fecha de &uacute;ltima actualizaci&oacute;n: 28 de mayo de 2026.</p>
    </div>
  </div>
</section>
HTML;

$_legal_en = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Legal Notice</h1>

      <h2>Site owner</h2>
      <p>Trade name: <strong>+QUECLIMA</strong><br>
      Website: masqueclima.es<br>
      Email: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a></p>

      <h2>Activity</h2>
      <p>+QUECLIMA provides installation, maintenance and repair services for air conditioning, heating and solar energy systems in the province of Alicante, Spain.</p>

      <h2>Intellectual property</h2>
      <p>All content on this website &mdash; texts, images, design, trademarks and any other elements &mdash; is owned by the site owner or licensed to them. Reproduction, distribution or modification without express authorisation is prohibited.</p>

      <h2>Disclaimer</h2>
      <p>The site owner accepts no liability for damages arising from the use of this website, any errors in content, or service availability. Content is informational and does not constitute binding technical advice.</p>

      <h2>Applicable law and jurisdiction</h2>
      <p>This legal notice is governed by Spanish law. For any dispute the parties submit to the Courts of Alicante, unless applicable regulations provide otherwise.</p>

      <p class="legal-date">Last updated: 28 May 2026.</p>
    </div>
  </div>
</section>
HTML;

$_legal_de = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Impressum</h1>

      <h2>Angaben gem&auml;&szlig; &sect; 5 TMG / Anbieteridentifikation</h2>
      <p>Handelsname: <strong>+QUECLIMA</strong><br>
      Website: masqueclima.es<br>
      E-Mail: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a></p>

      <h2>T&auml;tigkeit</h2>
      <p>+QUECLIMA bietet Installations-, Wartungs- und Reparaturdienstleistungen f&uuml;r Klimaanlagen, Heizungs- und Solarenergiesysteme in der Provinz Alicante (Spanien) an.</p>

      <h2>Urheberrecht</h2>
      <p>Alle Inhalte dieser Website &mdash; Texte, Bilder, Design, Marken und sonstige Elemente &mdash; sind Eigentum des Inhabers oder wurden unter Lizenz &uuml;berlassen. Vervielf&auml;ltigung, Verbreitung oder Ver&auml;nderung ohne ausdr&uuml;ckliche Genehmigung sind untersagt.</p>

      <h2>Haftungsausschluss</h2>
      <p>Der Inhaber &uuml;bernimmt keine Haftung f&uuml;r Sch&auml;den, die durch die Nutzung der Website entstehen, f&uuml;r etwaige Fehler in den Inhalten oder f&uuml;r die Verf&uuml;gbarkeit des Dienstes. Die Inhalte sind informativ und stellen keine verbindliche Fachberatung dar.</p>

      <h2>Anwendbares Recht und Gerichtsstand</h2>
      <p>Dieses Impressum unterliegt dem spanischen Recht. F&uuml;r Streitigkeiten unterwerfen sich die Parteien den Gerichten von Alicante, sofern nicht anwendbare Vorschriften etwas anderes vorsehen.</p>

      <p class="legal-date">Stand: 28. Mai 2026.</p>
    </div>
  </div>
</section>
HTML;

$_legal_nl = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Juridische mededeling</h1>

      <h2>Eigenaar van de website</h2>
      <p>Handelsnaam: <strong>+QUECLIMA</strong><br>
      Website: masqueclima.es<br>
      E-mail: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a></p>

      <h2>Activiteit</h2>
      <p>+QUECLIMA biedt installatie-, onderhouds- en reparatiediensten voor airconditioning, verwarming en zonne-energiesystemen in de provincie Alicante, Spanje.</p>

      <h2>Intellectuele eigendom</h2>
      <p>Alle inhoud op deze website &mdash; teksten, afbeeldingen, ontwerp, handelsmerken en andere elementen &mdash; is eigendom van de site-eigenaar of in licentie gegeven. Reproductie, verspreiding of wijziging zonder uitdrukkelijke toestemming is verboden.</p>

      <h2>Aansprakelijkheidsbeperking</h2>
      <p>De site-eigenaar aanvaardt geen aansprakelijkheid voor schade als gevolg van het gebruik van deze website, fouten in de inhoud of de beschikbaarheid van de dienst. Inhoud is informatief en vormt geen bindend technisch advies.</p>

      <h2>Toepasselijk recht en bevoegde rechter</h2>
      <p>Deze juridische mededeling wordt beheerst door Spaans recht. Voor geschillen onderwerpen de partijen zich aan de rechtbanken van Alicante, tenzij toepasselijke regelgeving anders bepaalt.</p>

      <p class="legal-date">Laatste update: 28 mei 2026.</p>
    </div>
  </div>
</section>
HTML;

$_legal_ru = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Правовое уведомление</h1>

      <h2>Владелец сайта</h2>
      <p>Торговое наименование: <strong>+QUECLIMA</strong><br>
      Сайт: masqueclima.es<br>
      Эл. почта: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a></p>

      <h2>Деятельность</h2>
      <p>+QUECLIMA предоставляет услуги по монтажу, техническому обслуживанию и ремонту систем кондиционирования воздуха, отопления и солнечной энергетики в провинции Аликанте (Испания).</p>

      <h2>Интеллектуальная собственность</h2>
      <p>Всё содержимое этого сайта — тексты, изображения, дизайн, торговые марки и прочие элементы — принадлежит владельцу сайта или предоставлено по лицензии. Воспроизведение, распространение или изменение без явного разрешения запрещено.</p>

      <h2>Ограничение ответственности</h2>
      <p>Владелец сайта не несёт ответственности за ущерб, возникший в результате использования сайта, ошибок в содержимом или недоступности сервиса. Содержимое носит информационный характер и не является обязательной технической рекомендацией.</p>

      <h2>Применимое право и юрисдикция</h2>
      <p>Настоящее правовое уведомление регулируется законодательством Испании. По любым спорам стороны подчиняются судам Аликанте, если только применимое законодательство не предусматривает иной юрисдикции.</p>

      <p class="legal-date">Дата последнего обновления: 28 мая 2026 г.</p>
    </div>
  </div>
</section>
HTML;

$_legal_no = <<<HTML
<section class="legal-page" aria-labelledby="legal-title">
  $_css
  <div class="container">
    <div class="legal-wrap">
      <h1 id="legal-title">Juridisk varsel</h1>

      <h2>Nettstedseier</h2>
      <p>Handelsnavn: <strong>+QUECLIMA</strong><br>
      Nettsted: masqueclima.es<br>
      E-post: <a href="mailto:administracion@masqueclima.es">administracion@masqueclima.es</a></p>

      <h2>Virksomhet</h2>
      <p>+QUECLIMA tilbyr installasjons-, vedlikeholds- og reparasjonstjenester for klimaanlegg, varme og solenergi­systemer i provinsen Alicante, Spania.</p>

      <h2>Immateri­elle rettigheter</h2>
      <p>Alt innhold p&aring; dette nettstedet &mdash; tekster, bilder, design, varemerker og andre elementer &mdash; eies av nettstedseieren eller er lisensiert til dem. Reproduksjon, distribusjon eller endring uten uttrykkelig tillatelse er forbudt.</p>

      <h2>Ansvarsfraskrivelse</h2>
      <p>Nettstedseieren fraskriver seg ansvar for skader som f&oslash;lge av bruk av nettstedet, feil i innhold eller tjenestens tilgjengelighet. Innholdet er informativt og utgjør ikke bindende teknisk r&aring;dgivning.</p>

      <h2>Gjeldende lov og verneting</h2>
      <p>Dette juridiske varselet reguleres av spansk lov. For tvister underlegger partene seg domstolene i Alicante, med mindre gjeldende forskrifter bestemmer noe annet.</p>

      <p class="legal-date">Sist oppdatert: 28. mai 2026.</p>
    </div>
  </div>
</section>
HTML;

// ---------------------------------------------------------------------------
// Return full page config array indexed by URL path
// ---------------------------------------------------------------------------
return [

  // ── COOKIES ────────────────────────────────────────────────────────────────
  '/es/politica-de-cookies/' => [
    'type' => 'cookies', 'lang' => 'es',
    'title' => 'Política de cookies | +QUECLIMA',
    'description' => 'Información sobre el uso de cookies técnicas, de sesión y de terceros en la web de +QUECLIMA.',
    'canonical' => $_base . '/es/politica-de-cookies/',
    'body' => $_cookies_es,
  ],
  '/en/cookie-policy/' => [
    'type' => 'cookies', 'lang' => 'en',
    'title' => 'Cookie Policy | +QUECLIMA',
    'description' => 'Information about the use of technical, session and third-party cookies on the +QUECLIMA website.',
    'canonical' => $_base . '/en/cookie-policy/',
    'body' => $_cookies_en,
  ],
  '/de/cookie-richtlinie/' => [
    'type' => 'cookies', 'lang' => 'de',
    'title' => 'Cookie-Richtlinie | +QUECLIMA',
    'description' => 'Informationen zur Verwendung von technischen, Sitzungs- und Drittanbieter-Cookies auf der Website von +QUECLIMA.',
    'canonical' => $_base . '/de/cookie-richtlinie/',
    'body' => $_cookies_de,
  ],
  '/nl/cookiebeleid/' => [
    'type' => 'cookies', 'lang' => 'nl',
    'title' => 'Cookiebeleid | +QUECLIMA',
    'description' => 'Informatie over het gebruik van technische, sessie- en derde-partij cookies op de website van +QUECLIMA.',
    'canonical' => $_base . '/nl/cookiebeleid/',
    'body' => $_cookies_nl,
  ],
  '/ru/cookie-policy/' => [
    'type' => 'cookies', 'lang' => 'ru',
    'title' => 'Политика использования файлов cookie | +QUECLIMA',
    'description' => 'Информация об использовании технических, сеансовых и сторонних файлов cookie на сайте +QUECLIMA.',
    'canonical' => $_base . '/ru/cookie-policy/',
    'body' => $_cookies_ru,
  ],
  '/no/cookie-policy/' => [
    'type' => 'cookies', 'lang' => 'no',
    'title' => 'Informasjonskapselerklæring | +QUECLIMA',
    'description' => 'Informasjon om bruk av tekniske, sesjons- og tredjepartsinformasjonskapsler på nettstedet til +QUECLIMA.',
    'canonical' => $_base . '/no/cookie-policy/',
    'body' => $_cookies_no,
  ],

  // ── PRIVACY ────────────────────────────────────────────────────────────────
  '/es/politica-de-privacidad/' => [
    'type' => 'privacy', 'lang' => 'es',
    'title' => 'Política de privacidad | +QUECLIMA',
    'description' => 'Información sobre el tratamiento de datos personales en la web de +QUECLIMA conforme al RGPD.',
    'canonical' => $_base . '/es/politica-de-privacidad/',
    'body' => $_privacy_es,
  ],
  '/en/privacy-policy/' => [
    'type' => 'privacy', 'lang' => 'en',
    'title' => 'Privacy Policy | +QUECLIMA',
    'description' => 'Information about the processing of personal data on the +QUECLIMA website in accordance with the GDPR.',
    'canonical' => $_base . '/en/privacy-policy/',
    'body' => $_privacy_en,
  ],
  '/de/datenschutzerklaerung/' => [
    'type' => 'privacy', 'lang' => 'de',
    'title' => 'Datenschutzerklärung | +QUECLIMA',
    'description' => 'Informationen zur Verarbeitung personenbezogener Daten auf der Website von +QUECLIMA gemäß DSGVO.',
    'canonical' => $_base . '/de/datenschutzerklaerung/',
    'body' => $_privacy_de,
  ],
  '/nl/privacybeleid/' => [
    'type' => 'privacy', 'lang' => 'nl',
    'title' => 'Privacybeleid | +QUECLIMA',
    'description' => 'Informatie over de verwerking van persoonsgegevens op de +QUECLIMA-website conform de AVG.',
    'canonical' => $_base . '/nl/privacybeleid/',
    'body' => $_privacy_nl,
  ],
  '/ru/politika-konfidentsialnosti/' => [
    'type' => 'privacy', 'lang' => 'ru',
    'title' => 'Политика конфиденциальности | +QUECLIMA',
    'description' => 'Информация об обработке персональных данных на сайте +QUECLIMA в соответствии с GDPR.',
    'canonical' => $_base . '/ru/politika-konfidentsialnosti/',
    'body' => $_privacy_ru,
  ],
  '/no/personvernerklaering/' => [
    'type' => 'privacy', 'lang' => 'no',
    'title' => 'Personvernerklæring | +QUECLIMA',
    'description' => 'Informasjon om behandling av personopplysninger på nettstedet til +QUECLIMA i samsvar med GDPR.',
    'canonical' => $_base . '/no/personvernerklaering/',
    'body' => $_privacy_no,
  ],

  // ── LEGAL NOTICE ───────────────────────────────────────────────────────────
  '/es/aviso-legal/' => [
    'type' => 'legal', 'lang' => 'es',
    'title' => 'Aviso legal | +QUECLIMA',
    'description' => 'Aviso legal de +QUECLIMA: datos del titular, propiedad intelectual y condiciones de uso del sitio web.',
    'canonical' => $_base . '/es/aviso-legal/',
    'body' => $_legal_es,
  ],
  '/en/legal-notice/' => [
    'type' => 'legal', 'lang' => 'en',
    'title' => 'Legal Notice | +QUECLIMA',
    'description' => 'Legal notice for +QUECLIMA: site owner details, intellectual property and terms of use.',
    'canonical' => $_base . '/en/legal-notice/',
    'body' => $_legal_en,
  ],
  '/de/impressum/' => [
    'type' => 'legal', 'lang' => 'de',
    'title' => 'Impressum | +QUECLIMA',
    'description' => 'Impressum von +QUECLIMA: Anbieteridentifikation, Urheberrecht und Nutzungsbedingungen.',
    'canonical' => $_base . '/de/impressum/',
    'body' => $_legal_de,
  ],
  '/nl/juridische-mededeling/' => [
    'type' => 'legal', 'lang' => 'nl',
    'title' => 'Juridische mededeling | +QUECLIMA',
    'description' => 'Juridische mededeling van +QUECLIMA: eigenaar, intellectueel eigendom en gebruiksvoorwaarden.',
    'canonical' => $_base . '/nl/juridische-mededeling/',
    'body' => $_legal_nl,
  ],
  '/ru/pravovoe-uvedomlenie/' => [
    'type' => 'legal', 'lang' => 'ru',
    'title' => 'Правовое уведомление | +QUECLIMA',
    'description' => 'Правовое уведомление +QUECLIMA: данные о владельце, интеллектуальная собственность и условия использования сайта.',
    'canonical' => $_base . '/ru/pravovoe-uvedomlenie/',
    'body' => $_legal_ru,
  ],
  '/no/juridisk-varsel/' => [
    'type' => 'legal', 'lang' => 'no',
    'title' => 'Juridisk varsel | +QUECLIMA',
    'description' => 'Juridisk varsel for +QUECLIMA: nettstedseier, immaterielle rettigheter og bruksvilkår.',
    'canonical' => $_base . '/no/juridisk-varsel/',
    'body' => $_legal_no,
  ],
];
