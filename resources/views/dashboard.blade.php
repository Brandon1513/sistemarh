<x-app-layout>
@php
    // Prepara los datos de presentación de cada noticia (texto plano con párrafos, fecha en español, tiempo de lectura)
    $prepararNoticia = function ($noticia) {
        $html  = preg_replace(['/<br\s*\/?>/i', '/<\/(p|div|li|h[1-6]|blockquote)>/i'], ["\n", "\n\n"], $noticia->contenido ?? '');
        $texto = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $texto = trim(preg_replace("/\n{3,}/", "\n\n", preg_replace("/[ \t]+/", ' ', $texto)));
        $fecha = \Carbon\Carbon::parse($noticia->fecha ?? $noticia->created_at)->locale('es');
        $palabras = count(preg_split('/\s+/u', $texto, -1, PREG_SPLIT_NO_EMPTY));

        return (object) [
            'texto'   => $texto,
            'resumen' => Str::limit(preg_replace('/\s+/u', ' ', $texto), 170),
            'minutos' => max(1, (int) ceil($palabras / 200)),
            'fecha'   => $fecha,
            'nueva'   => $fecha->between(now()->subDays(7), now()->endOfDay()),
            'imagen'  => $noticia->imagen ? asset('storage/' . $noticia->imagen) : null,
            'inicial' => Str::upper(Str::substr(trim($noticia->autor ?? '') ?: 'D', 0, 1)),
            'fotos'   => $noticia->galeria->count(),
        ];
    };

    $info      = $noticias->mapWithKeys(fn ($n) => [$n->id => $prepararNoticia($n)]);
    $destacada = $noticias->first();
    $resto     = $noticias->slice(1);
@endphp

<style>
    :root {
        --brand:       #6A2C75;
        --brand-dark:  #541f5c;
        --brand-deep:  #2a0a32;
        --brand-light: #f3eef5;
        --brand-mid:   #BBA4C0;
        --gold:        #D6A644;
        --gold-light:  #EED39B;
        --gold-pale:   #faf3e0;
        --brown:       #473524;
        --rose:        #AA4969;
        --beige-light: #f7f3ef;
        --border:      #e6d9ed;
        --text:        #2c1a30;
        --muted:       #7a6682;
        --radius-lg:   22px;
        --radius-md:   14px;
        --shadow-sm:   0 1px 2px rgba(42,10,50,.05), 0 2px 8px rgba(42,10,50,.04);
        --shadow-md:   0 10px 30px -10px rgba(42,10,50,.22);
        --shadow-lg:   0 30px 70px -20px rgba(42,10,50,.45);
        --ease:        cubic-bezier(.22,.61,.36,1);
    }

    /* ══ Carrusel hero ══════════════════════════════════════ */
    .hero-carousel {
        position: relative; width: 100%; overflow: hidden;
        height: 520px; background: var(--brand-dark);
    }
    @media(max-width:640px){ .hero-carousel { height: 340px; } }

    .carousel-item {
        position: absolute; inset: 0;
        opacity: 0; visibility: hidden;
        transition: opacity .9s var(--ease), visibility 0s .9s;
    }
    .carousel-item.active { opacity: 1; visibility: visible; transition: opacity .9s var(--ease); }
    .carousel-item img {
        width: 100%; height: 100%; object-fit: cover;
        transform: scale(1.08); transition: transform 6s linear;
    }
    .carousel-item.active img { transform: scale(1); }

    .carousel-overlay {
        position: absolute; inset: 0;
        background:
            linear-gradient(to top, rgba(42,10,50,.88) 0%, rgba(42,10,50,.35) 45%, transparent 75%),
            linear-gradient(to right, rgba(42,10,50,.35), transparent 60%);
        display: flex; flex-direction: column; justify-content: flex-end;
        padding: 48px max(1.5rem, calc((100% - 80rem) / 2 + 1.5rem)) 72px;
    }
    @media(max-width:640px){ .carousel-overlay { padding: 24px 20px 56px; } }

    .carousel-tag {
        align-self: flex-start;
        display: inline-flex; align-items: center; gap: 6px;
        background: rgba(214,166,68,.95); color: var(--brown);
        font-size: 11px; font-weight: 800; letter-spacing: .12em;
        text-transform: uppercase; padding: 5px 12px; border-radius: 999px;
        margin-bottom: 14px;
    }
    .carousel-tag::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--brown); }
    .carousel-title {
        font-size: clamp(1.5rem, 3.2vw, 2.7rem);
        font-weight: 800; color: #fff; line-height: 1.12; letter-spacing: -.02em;
        max-width: 640px; text-shadow: 0 2px 18px rgba(0,0,0,.25);
    }
    .carousel-desc {
        font-size: clamp(.9rem, 1.4vw, 1.08rem);
        color: rgba(255,255,255,.85); margin-top: 10px; line-height: 1.55;
        max-width: 520px;
    }

    /* Indicadores con barra de progreso */
    .carousel-indicators {
        position: absolute; bottom: 26px; left: 50%; transform: translateX(-50%);
        display: flex; gap: 8px; z-index: 5;
    }
    .carousel-dot {
        position: relative; overflow: hidden;
        width: 8px; height: 8px; border-radius: 999px; border: none; padding: 0;
        background: rgba(255,255,255,.4); cursor: pointer;
        transition: width .4s var(--ease), background .3s;
    }
    .carousel-dot:hover { background: rgba(255,255,255,.7); }
    .carousel-dot.active { width: 36px; background: rgba(255,255,255,.3); }
    .carousel-dot.active::after {
        content: ''; position: absolute; inset: 0; background: var(--gold);
        transform-origin: left; animation: dotProgress var(--hero-interval, 5s) linear forwards;
    }
    .hero-carousel.paused .carousel-dot.active::after { animation-play-state: paused; }
    @keyframes dotProgress { from { transform: scaleX(0); } to { transform: scaleX(1); } }

    .carousel-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 46px; height: 46px; border-radius: 50%; z-index: 5;
        background: rgba(255,255,255,.12); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,.22);
        color: #fff; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        opacity: 0; transition: background .2s, opacity .3s, transform .2s;
    }
    .hero-carousel:hover .carousel-btn, .carousel-btn:focus-visible { opacity: 1; }
    .carousel-btn:hover { background: rgba(255,255,255,.25); }
    .carousel-btn.prev { left: 22px; }
    .carousel-btn.next { right: 22px; }
    @media(hover:none){ .carousel-btn { display: none; } }

    /* Hero sin imágenes */
    .hero-empty {
        height: 300px; display: flex; flex-direction: column; justify-content: flex-end;
        padding: 40px max(1.5rem, calc((100% - 80rem) / 2 + 1.5rem));
        background:
            radial-gradient(circle at 85% 20%, rgba(214,166,68,.35), transparent 40%),
            linear-gradient(135deg, var(--brand) 0%, var(--brand-deep) 100%);
    }

    /* ══ Sección noticias ═══════════════════════════════════ */
    .news-section {
        position: relative; padding: 64px 0 80px;
        background:
            linear-gradient(180deg, rgba(250,247,251,.94) 0%, rgba(247,243,239,.97) 100%),
            url('{{ asset('images/background-pattern.png') }}') center / cover;
    }
    .news-inner { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }

    .news-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        gap: 16px; flex-wrap: wrap; margin-bottom: 32px;
    }
    .news-eyebrow {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 11.5px; font-weight: 800; letter-spacing: .14em;
        text-transform: uppercase; color: var(--brand);
        margin-bottom: 10px;
    }
    .news-eyebrow .pulse {
        position: relative; width: 8px; height: 8px; border-radius: 50%; background: var(--gold);
    }
    .news-eyebrow .pulse::after {
        content: ''; position: absolute; inset: -4px; border-radius: 50%;
        border: 2px solid var(--gold); opacity: 0; animation: pulse 2s var(--ease) infinite;
    }
    @keyframes pulse { 0% { transform: scale(.5); opacity: .8; } 100% { transform: scale(1.6); opacity: 0; } }

    .news-title {
        font-size: clamp(1.7rem, 3vw, 2.4rem);
        font-weight: 800; color: var(--text); line-height: 1.1; letter-spacing: -.025em;
    }
    .news-subtitle { margin-top: 8px; font-size: 15px; color: var(--muted); }

    .news-link {
        display: inline-flex; align-items: center; gap: 8px;
        font-size: 14px; font-weight: 700; color: #fff; text-decoration: none;
        padding: 11px 20px; border-radius: 999px;
        background: linear-gradient(135deg, var(--brand), var(--brand-dark));
        box-shadow: 0 8px 20px -8px rgba(106,44,117,.6);
        transition: transform .2s var(--ease), box-shadow .2s;
    }
    .news-link svg { transition: transform .2s var(--ease); }
    .news-link:hover { transform: translateY(-1px); box-shadow: 0 12px 26px -8px rgba(106,44,117,.7); }
    .news-link:hover svg { transform: translateX(3px); }

    /* Layout: destacada + lista */
    .news-layout {
        display: grid; grid-template-columns: minmax(0, 7fr) minmax(0, 5fr);
        gap: 24px; align-items: stretch;
    }
    .news-layout.single { grid-template-columns: minmax(0, 1fr); }
    @media(max-width:1023px){ .news-layout { grid-template-columns: minmax(0, 1fr); } }

    /* Placeholder sin imagen */
    .news-ph {
        width: 100%; height: 100%;
        display: flex; align-items: center; justify-content: center;
        background:
            radial-gradient(circle at 80% 15%, rgba(214,166,68,.45), transparent 45%),
            linear-gradient(135deg, var(--brand) 0%, var(--brand-deep) 100%);
        color: rgba(255,255,255,.55);
    }

    /* ── Noticia destacada ── */
    .news-featured {
        position: relative; min-height: 460px;
        border-radius: var(--radius-lg); overflow: hidden; cursor: pointer;
        background: var(--brand-deep); isolation: isolate;
        box-shadow: var(--shadow-md);
        transition: transform .35s var(--ease), box-shadow .35s var(--ease);
        outline: none;
    }
    @media(max-width:640px){ .news-featured { min-height: 380px; } }
    .news-featured:hover, .news-featured:focus-visible { transform: translateY(-4px); box-shadow: var(--shadow-lg); }
    .news-featured:focus-visible { box-shadow: 0 0 0 3px #fff, 0 0 0 6px var(--brand), var(--shadow-lg); }

    .news-featured-media { position: absolute; inset: 0; z-index: -2; }
    .news-featured-media img, .news-featured-media .news-ph {
        width: 100%; height: 100%; object-fit: cover;
        transition: transform .8s var(--ease);
    }
    .news-featured:hover .news-featured-media img,
    .news-featured:hover .news-featured-media .news-ph { transform: scale(1.05); }
    .news-featured::before {
        content: ''; position: absolute; inset: 0; z-index: -1;
        background: linear-gradient(to top, rgba(28,6,34,.95) 0%, rgba(42,10,50,.6) 40%, rgba(42,10,50,.05) 75%);
    }

    .news-featured-top {
        position: absolute; top: 20px; left: 20px; right: 20px;
        display: flex; gap: 8px; flex-wrap: wrap;
    }
    .chip {
        display: inline-flex; align-items: center; gap: 6px;
        font-size: 11px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase;
        padding: 5px 11px; border-radius: 999px;
    }
    .chip-gold  { background: var(--gold); color: var(--brown); }
    .chip-glass { background: rgba(255,255,255,.16); color: #fff; backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px); border: 1px solid rgba(255,255,255,.2); }
    .chip-new   { background: #fff; color: var(--rose); }
    .chip-new::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--rose); }

    .news-featured-body {
        position: absolute; left: 0; right: 0; bottom: 0;
        padding: 28px 30px 28px; color: #fff;
    }
    @media(max-width:640px){ .news-featured-body { padding: 20px; } }

    .news-meta {
        display: flex; align-items: center; flex-wrap: wrap; gap: 6px 14px;
        font-size: 12.5px; font-weight: 600;
    }
    .news-meta span { display: inline-flex; align-items: center; gap: 5px; }
    .news-featured .news-meta { color: rgba(255,255,255,.75); margin-bottom: 12px; }

    .news-featured-title {
        font-size: clamp(1.35rem, 2.4vw, 2rem); font-weight: 800;
        line-height: 1.15; letter-spacing: -.02em;
        display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;
    }
    .news-featured-excerpt {
        margin-top: 10px; font-size: 15px; line-height: 1.6; color: rgba(255,255,255,.8);
        max-width: 60ch;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    @media(max-width:640px){ .news-featured-excerpt { display: none; } }

    .news-featured-foot {
        margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 12px;
    }
    @media(max-width:400px){ .read-btn .read-label { display: none; } .read-btn { padding: 10px 12px; } }
    .author { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .avatar {
        flex: none; width: 34px; height: 34px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 800;
        background: linear-gradient(135deg, var(--gold-light), var(--gold)); color: var(--brown);
    }
    .author-name { font-size: 13.5px; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .author-role { font-size: 11.5px; opacity: .7; }

    .read-btn {
        flex: none; display: inline-flex; align-items: center; gap: 8px;
        font-size: 13.5px; font-weight: 700;
        padding: 10px 16px; border-radius: 999px;
        background: #fff; color: var(--brand);
        transition: gap .25s var(--ease), background .2s;
    }
    .news-featured:hover .read-btn { gap: 12px; background: var(--gold-pale); }

    /* ── Lista de noticias ── */
    .news-list {
        display: flex; flex-direction: column; gap: 4px;
        background: rgba(255,255,255,.75); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(230,217,237,.9); border-radius: var(--radius-lg);
        padding: 10px; box-shadow: var(--shadow-sm);
    }
    .news-list-head {
        display: flex; align-items: center; justify-content: space-between;
        padding: 10px 12px 8px;
        font-size: 11.5px; font-weight: 800; letter-spacing: .12em; text-transform: uppercase; color: var(--muted);
    }
    .news-list-count {
        font-size: 11px; letter-spacing: 0; background: var(--brand-light); color: var(--brand);
        padding: 2px 9px; border-radius: 999px;
    }

    .news-row {
        flex: 1 1 auto;
        position: relative; display: grid; grid-template-columns: 108px minmax(0,1fr);
        gap: 14px; align-items: center;
        padding: 10px; border-radius: var(--radius-md); cursor: pointer; outline: none;
        transition: background .25s var(--ease), box-shadow .25s var(--ease), transform .25s var(--ease);
    }
    .news-row::before {
        content: ''; position: absolute; left: 0; top: 18px; bottom: 18px; width: 3px;
        border-radius: 0 3px 3px 0; background: var(--brand);
        transform: scaleY(0); transition: transform .25s var(--ease);
    }
    .news-row:hover, .news-row:focus-visible { background: #fff; box-shadow: var(--shadow-sm); transform: translateX(2px); }
    .news-row:hover::before, .news-row:focus-visible::before { transform: scaleY(1); }
    .news-row:focus-visible { box-shadow: 0 0 0 2px var(--brand); }
    .news-row + .news-row { border-top: 1px solid transparent; }

    .news-row-thumb {
        position: relative; width: 108px; height: 84px; border-radius: 12px; overflow: hidden;
        background: var(--brand-light);
    }
    .news-row-thumb img, .news-row-thumb .news-ph {
        width: 100%; height: 100%; object-fit: cover; transition: transform .5s var(--ease);
    }
    .news-row:hover .news-row-thumb img, .news-row:hover .news-row-thumb .news-ph { transform: scale(1.08); }
    .news-row-thumb .photos {
        position: absolute; right: 5px; bottom: 5px;
        display: inline-flex; align-items: center; gap: 3px;
        font-size: 10px; font-weight: 700; color: #fff;
        background: rgba(28,6,34,.65); backdrop-filter: blur(4px);
        padding: 2px 6px; border-radius: 999px;
    }

    .news-row .news-meta { font-size: 11.5px; color: var(--muted); margin-bottom: 4px; gap: 4px 10px; }
    .news-row .dot-new { color: var(--rose); font-weight: 800; }
    .news-row .dot-new::before { content: ''; width: 6px; height: 6px; border-radius: 50%; background: var(--rose); }
    .news-row-title {
        font-size: 14.5px; font-weight: 700; color: var(--text); line-height: 1.35;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        transition: color .2s;
    }
    .news-row:hover .news-row-title { color: var(--brand); }
    .news-row-excerpt {
        margin-top: 3px; font-size: 12.5px; color: var(--muted); line-height: 1.45;
        display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;
    }
    @media(max-width:640px){
        .news-row { grid-template-columns: 88px minmax(0,1fr); gap: 12px; }
        .news-row-thumb { width: 88px; height: 72px; }
        .news-row-excerpt { display: none; }
    }

    /* Estado vacío */
    .news-empty {
        text-align: center; padding: 56px 24px;
        background: rgba(255,255,255,.75); border: 1.5px dashed var(--border); border-radius: var(--radius-lg);
        color: var(--muted);
    }
    .news-empty-icon {
        width: 56px; height: 56px; margin: 0 auto 14px; border-radius: 16px;
        display: flex; align-items: center; justify-content: center;
        background: var(--brand-light); color: var(--brand);
    }
    .news-empty h3 { font-size: 17px; font-weight: 800; color: var(--text); margin-bottom: 4px; }

    /* Entrada escalonada */
    .reveal { opacity: 0; transform: translateY(14px); animation: reveal .6s var(--ease) forwards; }
    @keyframes reveal { to { opacity: 1; transform: none; } }

    /* ══ Modal noticia ══════════════════════════════════════ */
    .news-modal {
        position: fixed; inset: 0; z-index: 50;
        display: flex; align-items: center; justify-content: center;
        background: rgba(28,6,34,.62); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
        padding: 20px;
        opacity: 0; visibility: hidden;
        transition: opacity .25s var(--ease), visibility 0s .25s;
    }
    .news-modal.show { opacity: 1; visibility: visible; transition: opacity .25s var(--ease); }
    @media(max-width:640px){ .news-modal { padding: 0; align-items: flex-end; } }

    .news-modal-box {
        position: relative; width: 100%; max-width: 820px; max-height: 92vh;
        display: flex; flex-direction: column;
        background: #fff; border-radius: var(--radius-lg); overflow: hidden;
        box-shadow: var(--shadow-lg);
        transform: translateY(18px) scale(.98);
        transition: transform .3s var(--ease);
    }
    .news-modal.show .news-modal-box { transform: none; }
    @media(max-width:640px){ .news-modal-box { max-height: 94vh; border-radius: var(--radius-lg) var(--radius-lg) 0 0; } }

    .modal-scroll { overflow-y: auto; overscroll-behavior: contain; }

    .modal-hero {
        position: relative; min-height: 320px;
        display: flex; flex-direction: column; justify-content: flex-end;
        padding: 28px 36px; color: #fff; isolation: isolate;
    }
    .modal-hero.no-img { min-height: 220px; }
    @media(max-width:640px){ .modal-hero { min-height: 240px; padding: 20px; } .modal-hero.no-img { min-height: 180px; } }
    .modal-hero > img, .modal-hero > .news-ph { position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: -2; }
    .modal-hero::before {
        content: ''; position: absolute; inset: 0; z-index: -1;
        background: linear-gradient(to top, rgba(28,6,34,.92) 0%, rgba(42,10,50,.45) 50%, rgba(42,10,50,.1) 100%);
    }
    .modal-hero .chips { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 12px; }
    .modal-title {
        font-size: clamp(1.35rem, 3vw, 2.05rem); font-weight: 800;
        line-height: 1.15; letter-spacing: -.02em; max-width: 30ch;
    }

    .modal-body { padding: 26px 36px 36px; }
    @media(max-width:640px){ .modal-body { padding: 20px; } }

    .modal-byline {
        display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap;
        padding-bottom: 20px; margin-bottom: 22px; border-bottom: 1px solid var(--border);
    }
    .modal-byline .author-name { color: var(--text); }
    .modal-byline .author-role { color: var(--muted); opacity: 1; }
    .modal-byline .news-meta { color: var(--muted); }

    .modal-content {
        font-size: 16px; color: #3d2943; line-height: 1.8;
        white-space: pre-line; max-width: 68ch; overflow-wrap: anywhere;
    }

    .modal-media {
        margin-top: 24px; display: flex; align-items: center; gap: 14px;
        padding: 14px 16px; border-radius: var(--radius-md);
        background: var(--brand-light); color: var(--brand); text-decoration: none;
        border: 1px solid var(--border); transition: background .2s, transform .2s var(--ease);
    }
    .modal-media:hover { background: #ebe1ef; transform: translateY(-1px); }
    .modal-media-icon {
        flex: none; width: 42px; height: 42px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        background: var(--brand); color: #fff;
    }
    .modal-media strong { display: block; font-size: 14px; }
    .modal-media small { font-size: 12px; color: var(--muted); }

    .modal-gallery { margin-top: 28px; }
    .modal-gallery-title {
        display: flex; align-items: center; gap: 8px;
        font-size: 12px; font-weight: 800; color: var(--muted);
        text-transform: uppercase; letter-spacing: .1em; margin-bottom: 12px;
    }
    .modal-gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; }
    .modal-gallery-item {
        position: relative; aspect-ratio: 1; border-radius: 12px; overflow: hidden;
        border: none; padding: 0; cursor: zoom-in; background: var(--brand-light);
    }
    .modal-gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform .4s var(--ease); }
    .modal-gallery-item:hover img { transform: scale(1.08); }
    .modal-gallery-item:focus-visible { outline: 3px solid var(--brand); outline-offset: 2px; }

    .modal-close-btn {
        position: absolute; top: 14px; right: 14px; z-index: 5;
        width: 38px; height: 38px; border-radius: 50%;
        background: rgba(28,6,34,.45); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,.25); cursor: pointer; color: #fff;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s, transform .2s var(--ease);
    }
    .modal-close-btn:hover { background: rgba(28,6,34,.7); transform: rotate(90deg); }

    /* ══ Lightbox ═══════════════════════════════════════════ */
    .lightbox {
        position: fixed; inset: 0; z-index: 60;
        background: rgba(10,2,12,.92); backdrop-filter: blur(8px);
        display: flex; align-items: center; justify-content: center; padding: 20px;
        opacity: 0; visibility: hidden; transition: opacity .2s, visibility 0s .2s;
    }
    .lightbox.show { opacity: 1; visibility: visible; transition: opacity .2s; }
    .lightbox img { max-width: 100%; max-height: 86vh; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,.5); }
    .lb-btn {
        position: absolute; width: 44px; height: 44px; border-radius: 50%;
        background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
        color: #fff; cursor: pointer; display: flex; align-items: center; justify-content: center;
        transition: background .15s;
    }
    .lb-btn:hover { background: rgba(255,255,255,.25); }
    .lb-close { top: 20px; right: 20px; }
    .lb-prev  { left: 20px; top: 50%; transform: translateY(-50%); }
    .lb-next  { right: 20px; top: 50%; transform: translateY(-50%); }
    .lb-count { position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%); color: rgba(255,255,255,.75); font-size: 13px; font-weight: 600; }
    .lightbox.single .lb-prev, .lightbox.single .lb-next, .lightbox.single .lb-count { display: none; }

    @media (prefers-reduced-motion: reduce) {
        *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; }
        .reveal { opacity: 1; transform: none; }
    }
</style>

    {{-- ══════════════════════════════════════════
         CARRUSEL HERO
    ══════════════════════════════════════════ --}}
    @if($carruselImages->isNotEmpty())
        <div class="hero-carousel" id="heroCarousel" style="--hero-interval: 6s;">
            @foreach($carruselImages as $index => $image)
                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                    <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->titulo ?? '' }}"
                         @if($index > 0) loading="lazy" @endif>
                    <div class="carousel-overlay">
                        <span class="carousel-tag">Intranet</span>
                        @if($image->titulo)
                            <h2 class="carousel-title">{{ $image->titulo }}</h2>
                        @endif
                        @if($image->descripcion)
                            <p class="carousel-desc">{{ $image->descripcion }}</p>
                        @endif
                    </div>
                </div>
            @endforeach

            @if($carruselImages->count() > 1)
                <button class="carousel-btn prev" id="heroPrev" aria-label="Anterior">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>
                <button class="carousel-btn next" id="heroNext" aria-label="Siguiente">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>

                <div class="carousel-indicators" id="heroDots">
                    @foreach($carruselImages as $index => $image)
                        <button class="carousel-dot {{ $index === 0 ? 'active' : '' }}" data-target="{{ $index }}" aria-label="Ir a la imagen {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    @else
        <div class="hero-empty">
            <span class="carousel-tag">Intranet</span>
            <h2 class="carousel-title">Hola, {{ Str::before(auth()->user()->name, ' ') }}</h2>
            <p class="carousel-desc">Bienvenido(a) a la intranet de Dasavena.</p>
        </div>
    @endif

    {{-- ══════════════════════════════════════════
         SECCIÓN NOTICIAS
    ══════════════════════════════════════════ --}}
    <section class="news-section">
        <div class="news-inner">

            <div class="news-header">
                <div>
                    <p class="news-eyebrow"><span class="pulse"></span> Lo más reciente</p>
                    <h2 class="news-title">Últimas noticias</h2>
                    <p class="news-subtitle">Entérate de lo que está pasando en la empresa.</p>
                </div>
                <a href="{{ route('noticias.public') }}" class="news-link">
                    Ver todas
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M5 12h14M13 6l6 6-6 6"/></svg>
                </a>
            </div>

            @if($noticias->isEmpty())
                <div class="news-empty">
                    <div class="news-empty-icon">
                        <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg>
                    </div>
                    <h3>Aún no hay noticias</h3>
                    <p>Cuando se publique una noticia aparecerá aquí.</p>
                </div>
            @else
                @php $d = $info[$destacada->id]; @endphp
                <div class="news-layout {{ $resto->isEmpty() ? 'single' : '' }}">

                    {{-- Noticia destacada --}}
                    <article class="news-featured reveal" tabindex="0" role="button"
                             data-news="{{ $destacada->id }}" aria-label="Leer: {{ $destacada->titulo }}">
                        <div class="news-featured-media">
                            @if($d->imagen)
                                <img src="{{ $d->imagen }}" alt="">
                            @else
                                <div class="news-ph"><svg width="64" height="64" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.4" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg></div>
                            @endif
                        </div>

                        <div class="news-featured-top">
                            <span class="chip chip-gold">Destacada</span>
                            @if($d->nueva)<span class="chip chip-new">Nuevo</span>@endif
                        </div>

                        <div class="news-featured-body">
                            <div class="news-meta">
                                <span>
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $d->fecha->isoFormat('D [de] MMMM, YYYY') }}
                                </span>
                                <span>
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $d->minutos }} min de lectura
                                </span>
                                @if($d->fotos)
                                    <span>
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        {{ $d->fotos }} {{ $d->fotos === 1 ? 'foto' : 'fotos' }}
                                    </span>
                                @endif
                            </div>

                            <h3 class="news-featured-title">{{ $destacada->titulo }}</h3>
                            <p class="news-featured-excerpt">{{ $d->resumen }}</p>

                            <div class="news-featured-foot">
                                <div class="author">
                                    <span class="avatar">{{ $d->inicial }}</span>
                                    <div style="min-width:0;">
                                        <div class="author-name">{{ $destacada->autor ?: 'Dasavena' }}</div>
                                        <div class="author-role">{{ ucfirst($d->fecha->diffForHumans()) }}</div>
                                    </div>
                                </div>
                                <span class="read-btn">
                                    <span class="read-label">Leer noticia</span>
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4" d="M5 12h14M13 6l6 6-6 6"/></svg>
                                </span>
                            </div>
                        </div>
                    </article>

                    {{-- Resto de noticias --}}
                    @if($resto->isNotEmpty())
                        <div class="news-list reveal" style="animation-delay:.08s;">
                            <div class="news-list-head">
                                <span>También te puede interesar</span>
                                <span class="news-list-count">{{ $resto->count() }}</span>
                            </div>

                            @foreach($resto as $noticia)
                                @php $n = $info[$noticia->id]; @endphp
                                <article class="news-row reveal" tabindex="0" role="button"
                                         style="animation-delay: {{ 0.12 + $loop->index * 0.06 }}s;"
                                         data-news="{{ $noticia->id }}" aria-label="Leer: {{ $noticia->titulo }}">
                                    <div class="news-row-thumb">
                                        @if($n->imagen)
                                            <img src="{{ $n->imagen }}" alt="" loading="lazy">
                                        @else
                                            <div class="news-ph"><svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.6" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/></svg></div>
                                        @endif
                                        @if($n->fotos)
                                            <span class="photos">
                                                <svg width="10" height="10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                                {{ $n->fotos }}
                                            </span>
                                        @endif
                                    </div>
                                    <div style="min-width:0;">
                                        <div class="news-meta">
                                            @if($n->nueva)<span class="dot-new">Nuevo</span>@endif
                                            <span>{{ $n->fecha->isoFormat('D MMM YYYY') }}</span>
                                            <span>· {{ $n->minutos }} min</span>
                                        </div>
                                        <h3 class="news-row-title">{{ $noticia->titulo }}</h3>
                                        <p class="news-row-excerpt">{{ $n->resumen }}</p>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </section>

    {{-- ══════════════════════════════════════════
         MODALES NOTICIAS
    ══════════════════════════════════════════ --}}
    @foreach($noticias as $noticia)
        @php $n = $info[$noticia->id]; @endphp
        <div class="news-modal" id="modal-{{ $noticia->id }}" role="dialog" aria-modal="true" aria-labelledby="modal-title-{{ $noticia->id }}">
            <div class="news-modal-box">
                <button class="modal-close-btn" data-close aria-label="Cerrar">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <div class="modal-scroll">
                    <header class="modal-hero {{ $n->imagen ? '' : 'no-img' }}">
                        @if($n->imagen)
                            <img src="{{ $n->imagen }}" alt="" loading="lazy">
                        @else
                            <div class="news-ph"></div>
                        @endif
                        <div class="chips">
                            <span class="chip chip-glass">Noticia</span>
                            @if($n->nueva)<span class="chip chip-new">Nuevo</span>@endif
                        </div>
                        <h2 class="modal-title" id="modal-title-{{ $noticia->id }}">{{ $noticia->titulo }}</h2>
                    </header>

                    <div class="modal-body">
                        <div class="modal-byline">
                            <div class="author">
                                <span class="avatar">{{ $n->inicial }}</span>
                                <div>
                                    <div class="author-name">{{ $noticia->autor ?: 'Dasavena' }}</div>
                                    <div class="author-role">{{ $n->fecha->isoFormat('dddd D [de] MMMM [de] YYYY') }}</div>
                                </div>
                            </div>
                            <div class="news-meta">
                                <span>
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ $n->minutos }} min de lectura
                                </span>
                            </div>
                        </div>

                        <div class="modal-content">{{ $n->texto }}</div>

                        @if($noticia->multimedia)
                            <a href="{{ $noticia->multimedia }}" target="_blank" rel="noopener" class="modal-media">
                                <span class="modal-media-icon">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5.14v13.72a1 1 0 001.52.85l10.6-6.86a1 1 0 000-1.7L9.52 4.29A1 1 0 008 5.14z"/></svg>
                                </span>
                                <span>
                                    <strong>Ver contenido multimedia</strong>
                                    <small>Se abrirá en una pestaña nueva</small>
                                </span>
                            </a>
                        @endif

                        @if($n->fotos)
                            <div class="modal-gallery">
                                <p class="modal-gallery-title">
                                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Galería · {{ $n->fotos }} {{ $n->fotos === 1 ? 'imagen' : 'imágenes' }}
                                </p>
                                <div class="modal-gallery-grid">
                                    @foreach($noticia->galeria as $imagen)
                                        <button type="button" class="modal-gallery-item" data-lightbox="{{ asset('storage/' . $imagen->imagen) }}" aria-label="Ver imagen {{ $loop->iteration }}">
                                            <img src="{{ asset('storage/' . $imagen->imagen) }}" alt="" loading="lazy">
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Lightbox --}}
    <div class="lightbox" id="lightbox" role="dialog" aria-modal="true" aria-label="Imagen ampliada">
        <button class="lb-btn lb-close" data-lb="close" aria-label="Cerrar">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <button class="lb-btn lb-prev" data-lb="prev" aria-label="Anterior">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <img id="lightbox-img" src="" alt="">
        <button class="lb-btn lb-next" data-lb="next" aria-label="Siguiente">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>
        <span class="lb-count" id="lightbox-count"></span>
    </div>

    <script>
    // ── Carrusel hero ─────────────────────────────────────────────────────────
    (() => {
        const hero  = document.getElementById('heroCarousel');
        if (!hero) return;
        const items = hero.querySelectorAll('.carousel-item');
        const dots  = hero.querySelectorAll('.carousel-dot');
        if (items.length < 2) return;

        const interval = parseFloat(getComputedStyle(hero).getPropertyValue('--hero-interval')) * 1000 || 6000;
        let current = 0, timer = null, remaining = interval, startedAt = 0;

        function goTo(n) {
            items[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = (n + items.length) % items.length;
            items[current].classList.add('active');
            // Reinicia la animación de la barra de progreso
            void dots[current].offsetWidth;
            dots[current].classList.add('active');
            remaining = interval;
            if (!hero.classList.contains('paused')) schedule();
        }
        function schedule() {
            clearTimeout(timer);
            startedAt = Date.now();
            timer = setTimeout(() => goTo(current + 1), remaining);
        }
        function pause() {
            hero.classList.add('paused');
            clearTimeout(timer);
            remaining -= Date.now() - startedAt;
        }
        function resume() {
            hero.classList.remove('paused');
            schedule();
        }

        document.getElementById('heroPrev').onclick = () => goTo(current - 1);
        document.getElementById('heroNext').onclick = () => goTo(current + 1);
        dots.forEach(d => d.addEventListener('click', () => goTo(+d.dataset.target)));

        hero.addEventListener('mouseenter', pause);
        hero.addEventListener('mouseleave', resume);

        // Deslizar en pantallas táctiles
        let touchX = null;
        hero.addEventListener('touchstart', e => { touchX = e.touches[0].clientX; }, { passive: true });
        hero.addEventListener('touchend', e => {
            if (touchX === null) return;
            const dx = e.changedTouches[0].clientX - touchX;
            if (Math.abs(dx) > 40) goTo(current + (dx < 0 ? 1 : -1));
            touchX = null;
        });

        document.addEventListener('visibilitychange', () => document.hidden ? pause() : resume());

        schedule();
    })();

    // ── Modales noticias ──────────────────────────────────────────────────────
    let openModal = null, lastFocus = null;

    function openNewsModal(id) {
        const modal = document.getElementById('modal-' + id);
        if (!modal) return;
        lastFocus = document.activeElement;
        modal.classList.add('show');
        modal.querySelector('.modal-scroll').scrollTop = 0;
        document.body.style.overflow = 'hidden';
        openModal = modal;
        setTimeout(() => modal.querySelector('[data-close]').focus(), 50);
    }
    function closeNewsModal() {
        if (!openModal) return;
        openModal.classList.remove('show');
        document.body.style.overflow = '';
        openModal = null;
        if (lastFocus) lastFocus.focus();
    }

    document.querySelectorAll('[data-news]').forEach(card => {
        card.addEventListener('click', () => openNewsModal(card.dataset.news));
        card.addEventListener('keydown', e => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openNewsModal(card.dataset.news); }
        });
    });
    document.querySelectorAll('.news-modal').forEach(modal => {
        modal.addEventListener('click', e => {
            if (e.target === modal || e.target.closest('[data-close]')) closeNewsModal();
        });
    });

    // ── Lightbox ──────────────────────────────────────────────────────────────
    const lightbox = document.getElementById('lightbox');
    const lbImg    = document.getElementById('lightbox-img');
    const lbCount  = document.getElementById('lightbox-count');
    let lbImages = [], lbIndex = 0;

    function showLightbox(i) {
        lbIndex = (i + lbImages.length) % lbImages.length;
        lbImg.src = lbImages[lbIndex];
        lbCount.textContent = (lbIndex + 1) + ' / ' + lbImages.length;
    }
    function closeLightbox() { lightbox.classList.remove('show'); }

    document.querySelectorAll('[data-lightbox]').forEach(btn => {
        btn.addEventListener('click', () => {
            lbImages = [...btn.closest('.modal-gallery-grid').querySelectorAll('[data-lightbox]')].map(b => b.dataset.lightbox);
            lightbox.classList.toggle('single', lbImages.length < 2);
            lightbox.classList.add('show');
            showLightbox(lbImages.indexOf(btn.dataset.lightbox));
        });
    });
    lightbox.addEventListener('click', e => {
        const action = e.target.closest('[data-lb]')?.dataset.lb;
        if (action === 'prev') showLightbox(lbIndex - 1);
        else if (action === 'next') showLightbox(lbIndex + 1);
        else if (action === 'close' || e.target === lightbox) closeLightbox();
    });

    // ── Teclado ───────────────────────────────────────────────────────────────
    document.addEventListener('keydown', e => {
        if (lightbox.classList.contains('show')) {
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowLeft')  showLightbox(lbIndex - 1);
            if (e.key === 'ArrowRight') showLightbox(lbIndex + 1);
        } else if (openModal && e.key === 'Escape') {
            closeNewsModal();
        }
    });
    </script>

</x-app-layout>
