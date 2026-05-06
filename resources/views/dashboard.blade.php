<x-app-layout>
<style>
    :root {
        --brand:       #6A2C75;
        --brand-dark:  #541f5c;
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
    }

    /* ── Carrusel ── */
    .hero-carousel {
        position: relative; width: 100%; overflow: hidden;
        height: 520px; background: var(--brand-dark);
    }
    @media(max-width:640px){ .hero-carousel { height: 320px; } }

    .carousel-item {
        position: absolute; inset: 0;
        transition: opacity .8s ease, transform .8s ease;
        opacity: 0; transform: scale(1.03);
    }
    .carousel-item.active { opacity: 1; transform: scale(1); }

    .carousel-item img {
        width: 100%; height: 100%; object-fit: cover;
    }

    /* Overlay degradado más elegante */
    .carousel-overlay {
        position: absolute; inset: 0;
        background: linear-gradient(
            to top,
            rgba(42,10,50,.85) 0%,
            rgba(42,10,50,.3) 50%,
            transparent 100%
        );
        display: flex; flex-direction: column;
        align-items: flex-start; justify-content: flex-end;
        padding: 40px 56px;
    }
    @media(max-width:640px){ .carousel-overlay { padding: 24px; } }

    .carousel-tag {
        display: inline-block;
        background: var(--gold); color: var(--brown);
        font-size: 11px; font-weight: 800; letter-spacing: .1em;
        text-transform: uppercase; padding: 4px 12px; border-radius: 20px;
        margin-bottom: 10px;
    }
    .carousel-title {
        font-size: clamp(1.4rem, 3vw, 2.4rem);
        font-weight: 800; color: #fff; line-height: 1.2;
        max-width: 600px; text-shadow: 0 2px 12px rgba(0,0,0,.3);
    }
    .carousel-desc {
        font-size: clamp(.85rem, 1.5vw, 1.05rem);
        color: rgba(255,255,255,.82); margin-top: 8px;
        max-width: 500px;
    }

    /* Indicadores */
    .carousel-indicators {
        position: absolute; bottom: 20px; left: 50%;
        transform: translateX(-50%);
        display: flex; gap: 8px;
    }
    .carousel-dot {
        width: 8px; height: 8px; border-radius: 50%;
        background: rgba(255,255,255,.4); cursor: pointer;
        transition: all .3s; border: none;
    }
    .carousel-dot.active { background: var(--gold); width: 24px; border-radius: 4px; }

    /* Flechas */
    .carousel-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 44px; height: 44px; border-radius: 50%;
        background: rgba(255,255,255,.15); backdrop-filter: blur(8px);
        border: 1px solid rgba(255,255,255,.25);
        color: #fff; font-size: 18px; cursor: pointer;
        display: flex; align-items: center; justify-content: center;
        transition: background .2s;
    }
    .carousel-btn:hover { background: rgba(255,255,255,.28); }
    .carousel-btn.prev { left: 20px; }
    .carousel-btn.next { right: 20px; }

    /* ── Sección noticias ── */
    .news-section {
        padding: 56px 0;
        background-image: url('{{ asset('images/background-pattern.png') }}');
        background-size: cover;
    }

    .news-inner { max-width: 80rem; margin: 0 auto; padding: 0 1.5rem; }

    .news-header {
        display: flex; align-items: flex-end; justify-content: space-between;
        margin-bottom: 28px;
    }
    .news-header-left {}
    .news-eyebrow {
        font-size: 11px; font-weight: 800; letter-spacing: .1em;
        text-transform: uppercase; color: var(--brand-mid);
        margin-bottom: 4px;
    }
    .news-title {
        font-size: clamp(1.4rem, 2.5vw, 1.9rem);
        font-weight: 800; color: var(--text); line-height: 1.2;
    }
    .news-link {
        font-size: 13.5px; font-weight: 600; color: var(--brand);
        text-decoration: none; display: flex; align-items: center; gap: 5px;
        padding: 8px 16px; border: 1.5px solid var(--border);
        border-radius: 8px; transition: all .15s; white-space: nowrap;
    }
    .news-link:hover { background: var(--brand-light); border-color: var(--brand-mid); }

    /* Carrusel de noticias */
    .news-track-wrap { position: relative; }
    .news-track {
        display: flex; gap: 20px;
        overflow: hidden; scroll-behavior: smooth;
    }

    /* Card noticia */
    .news-card {
        flex: none; width: 280px;
        background: #fff; border-radius: 14px;
        border: 1px solid var(--border);
        box-shadow: 0 1px 3px rgba(106,44,117,.06), 0 6px 18px rgba(106,44,117,.05);
        cursor: pointer; overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .news-card:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(106,44,117,.14); }

    .news-card-img { width: 100%; height: 160px; object-fit: cover; display: block; }

    .news-card-body { padding: 14px 16px 16px; }
    .news-card-date {
        font-size: 11px; color: var(--muted); font-weight: 600;
        letter-spacing: .04em; margin-bottom: 5px;
    }
    .news-card-title {
        font-size: 14px; font-weight: 700; color: var(--text);
        line-height: 1.35; margin-bottom: 6px;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .news-card-excerpt {
        font-size: 12.5px; color: var(--muted); line-height: 1.5;
        display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    }
    .news-card-footer {
        padding: 10px 16px 14px;
        display: flex; align-items: center; justify-content: space-between;
        border-top: 1px solid #f5f0f7;
    }
    .news-card-tag {
        background: var(--brand-light); color: var(--brand);
        font-size: 11px; font-weight: 700; padding: 2px 8px; border-radius: 4px;
    }
    .news-card-read {
        font-size: 12px; font-weight: 600; color: var(--brand);
        display: flex; align-items: center; gap: 4px;
    }

    /* Botones scroll noticias */
    .news-nav-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 38px; height: 38px; border-radius: 50%;
        background: #fff; border: 1.5px solid var(--border);
        color: var(--brand); cursor: pointer; z-index: 10;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 2px 8px rgba(106,44,117,.12);
        transition: all .15s;
    }
    .news-nav-btn:hover { background: var(--brand-light); border-color: var(--brand-mid); }
    .news-nav-btn.prev { left: -18px; }
    .news-nav-btn.next { right: -18px; }
    @media(max-width:640px){
        .news-nav-btn.prev { left: 0; }
        .news-nav-btn.next { right: 0; }
    }

    /* ── Modal noticia ── */
    .news-modal {
        position: fixed; inset: 0; z-index: 50;
        display: flex; align-items: center; justify-content: center;
        background: rgba(42,10,50,.6); backdrop-filter: blur(4px);
        padding: 20px;
        opacity: 0; pointer-events: none;
        transition: opacity .2s;
    }
    .news-modal.show { opacity: 1; pointer-events: auto; }

    .news-modal-box {
        background: #fff; border-radius: 16px;
        width: 100%; max-width: 760px; max-height: 90vh;
        overflow-y: auto; position: relative;
        box-shadow: 0 24px 60px rgba(0,0,0,.25);
        transform: translateY(12px);
        transition: transform .2s;
    }
    .news-modal.show .news-modal-box { transform: translateY(0); }

    .modal-cover { width: 100%; height: 280px; object-fit: cover; border-radius: 16px 16px 0 0; display: block; }
    @media(max-width:640px){ .modal-cover { height: 180px; } }

    .modal-body { padding: 28px 32px 32px; }
    @media(max-width:640px){ .modal-body { padding: 20px; } }

    .modal-meta { font-size: 12px; color: var(--muted); margin-bottom: 12px; display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
    .modal-meta-tag { background: var(--brand-light); color: var(--brand); font-weight: 700; font-size: 11px; padding: 2px 8px; border-radius: 4px; }

    .modal-title { font-size: clamp(1.2rem, 2.5vw, 1.6rem); font-weight: 800; color: var(--text); line-height: 1.3; margin-bottom: 14px; }

    .modal-content { font-size: 14.5px; color: #4a3550; line-height: 1.75; }

    .modal-gallery { margin-top: 20px; }
    .modal-gallery-title { font-size: 13px; font-weight: 700; color: var(--muted); text-transform: uppercase; letter-spacing: .06em; margin-bottom: 10px; }
    .modal-gallery-grid { display: flex; flex-wrap: wrap; gap: 8px; }
    .modal-gallery-img { width: 100px; height: 100px; object-fit: cover; border-radius: 8px; cursor: pointer; transition: transform .15s, box-shadow .15s; border: 2px solid var(--border); }
    .modal-gallery-img:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(106,44,117,.2); }

    .modal-close-btn {
        position: absolute; top: 14px; right: 14px;
        width: 32px; height: 32px; border-radius: 50%;
        background: rgba(0,0,0,.35); border: none; cursor: pointer;
        color: #fff; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        transition: background .15s;
    }
    .modal-close-btn:hover { background: rgba(0,0,0,.6); }

    /* Lightbox */
    .lightbox {
        position: fixed; inset: 0; z-index: 60;
        background: rgba(0,0,0,.88); backdrop-filter: blur(6px);
        display: flex; align-items: center; justify-content: center;
        padding: 20px;
        opacity: 0; pointer-events: none; transition: opacity .2s;
    }
    .lightbox.show { opacity: 1; pointer-events: auto; }
    .lightbox img { max-width: 100%; max-height: 90vh; border-radius: 10px; box-shadow: 0 16px 48px rgba(0,0,0,.5); }
    .lightbox-close { position: absolute; top: 20px; right: 20px; width: 36px; height: 36px; border-radius: 50%; background: rgba(255,255,255,.15); border: none; color: #fff; font-size: 20px; cursor: pointer; display: flex; align-items: center; justify-content: center; }
</style>

    {{-- ══════════════════════════════════════════
         CARRUSEL HERO
    ══════════════════════════════════════════ --}}
    <div class="hero-carousel" id="heroCarousel">

        @foreach($carruselImages as $index => $image)
            <div class="carousel-item {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}">
                <img src="{{ asset('storage/' . $image->image) }}" alt="{{ $image->titulo ?? '' }}">
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

        <button class="carousel-btn prev" id="heroPrev">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </button>
        <button class="carousel-btn next" id="heroNext">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
        </button>

        <div class="carousel-indicators" id="heroDots">
            @foreach($carruselImages as $index => $image)
                <button class="carousel-dot {{ $index === 0 ? 'active' : '' }}" data-target="{{ $index }}"></button>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         SECCIÓN NOTICIAS
    ══════════════════════════════════════════ --}}
    <div class="news-section">
        <div class="news-inner">

            <div class="news-header">
                <div class="news-header-left">
                    <p class="news-eyebrow">Lo más reciente</p>
                    <h2 class="news-title">Últimas Noticias</h2>
                </div>
                <a href="{{ route('noticias.public') }}" class="news-link">
                    Ver todas
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="news-track-wrap">
                <button class="news-nav-btn prev" id="newsPrev">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                </button>

                <div id="newsTrack" class="news-track">
                    @foreach($noticias as $noticia)
                        <div class="news-card" onclick="openNewsModal({{ $noticia->id }})">
                            <img src="{{ $noticia->imagen ? asset('storage/'.$noticia->imagen) : asset('images/placeholder.jpg') }}"
                                class="news-card-img" alt="{{ $noticia->titulo }}">
                            <div class="news-card-body">
                                <p class="news-card-date">{{ \Carbon\Carbon::parse($noticia->fecha)->format('d/m/Y') }}</p>
                                <h3 class="news-card-title">{{ $noticia->titulo }}</h3>
                                <p class="news-card-excerpt">{{ Str::limit(strip_tags($noticia->contenido), 80) }}</p>
                            </div>
                            <div class="news-card-footer">
                                <span class="news-card-tag">Noticia</span>
                                <span class="news-card-read">
                                    Leer más
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <button class="news-nav-btn next" id="newsNext">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════
         MODALES NOTICIAS
    ══════════════════════════════════════════ --}}
    @foreach($noticias as $noticia)
        <div class="news-modal" id="modal-{{ $noticia->id }}" onclick="handleModalClick(event, {{ $noticia->id }})">
            <div class="news-modal-box">
                <button class="modal-close-btn" onclick="closeNewsModal({{ $noticia->id }})">✕</button>

                @if($noticia->imagen)
                    <img src="{{ asset('storage/' . $noticia->imagen) }}" class="modal-cover" alt="{{ $noticia->titulo }}">
                @endif

                <div class="modal-body">
                    <div class="modal-meta">
                        <span class="modal-meta-tag">Noticia</span>
                        <span>{{ \Carbon\Carbon::parse($noticia->fecha)->format('d \d\e F \d\e Y') }}</span>
                        <span>✍ {{ $noticia->autor }}</span>
                    </div>

                    <h2 class="modal-title">{{ $noticia->titulo }}</h2>

                    {{-- Contenido con ver más --}}
                    @php $contenido = strip_tags($noticia->contenido); @endphp
                    <div class="modal-content">
                        <p id="short-{{ $noticia->id }}">
                            {{ Str::limit($contenido, 250) }}
                            @if(strlen($contenido) > 250)
                                <button onclick="expandText({{ $noticia->id }})"
                                    style="color:var(--brand);font-weight:600;background:none;border:none;cursor:pointer;">
                                    Ver más ↓
                                </button>
                            @endif
                        </p>
                        <p id="full-{{ $noticia->id }}" style="display:none;">
                            {{ $contenido }}
                            <button onclick="collapseText({{ $noticia->id }})"
                                style="display:block;margin-top:8px;color:var(--brand);font-weight:600;background:none;border:none;cursor:pointer;">
                                Ver menos ↑
                            </button>
                        </p>
                    </div>

                    @if($noticia->multimedia)
                        <p style="margin-top:14px; font-size:13.5px;">
                            <a href="{{ $noticia->multimedia }}" target="_blank"
                               style="color:var(--brand); font-weight:600; display:inline-flex; align-items:center; gap:4px;">
                                🎬 Ver multimedia
                            </a>
                        </p>
                    @endif

                    {{-- Galería --}}
                    @if($noticia->galeria->count())
                        <div class="modal-gallery">
                            <p class="modal-gallery-title">Galería de imágenes</p>
                            <div class="modal-gallery-grid">
                                @foreach($noticia->galeria as $imagen)
                                    <img src="{{ asset('storage/' . $imagen->imagen) }}"
                                        class="modal-gallery-img"
                                        onclick="openLightbox('{{ asset('storage/' . $imagen->imagen) }}')"
                                        alt="Imagen">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach

    {{-- Lightbox --}}
    <div class="lightbox" id="lightbox" onclick="closeLightbox()">
        <button class="lightbox-close">✕</button>
        <img id="lightbox-img" src="" alt="">
    </div>

    <script>
    // ── Carrusel hero ─────────────────────────────────────────────────────────
    (() => {
        const items = document.querySelectorAll('.carousel-item');
        const dots  = document.querySelectorAll('.carousel-dot');
        let current = 0, timer;

        function goTo(n) {
            items[current].classList.remove('active');
            dots[current].classList.remove('active');
            current = (n + items.length) % items.length;
            items[current].classList.add('active');
            dots[current].classList.add('active');
        }

        function autoPlay() { timer = setInterval(() => goTo(current + 1), 5000); }
        function resetTimer() { clearInterval(timer); autoPlay(); }

        document.getElementById('heroPrev').onclick = () => { goTo(current - 1); resetTimer(); };
        document.getElementById('heroNext').onclick = () => { goTo(current + 1); resetTimer(); };
        dots.forEach(d => d.addEventListener('click', () => { goTo(+d.dataset.target); resetTimer(); }));

        autoPlay();
    })();

    // ── Carrusel noticias ─────────────────────────────────────────────────────
    (() => {
        const track = document.getElementById('newsTrack');
        const step  = 300;
        document.getElementById('newsPrev').onclick = () => track.scrollBy({ left: -step, behavior: 'smooth' });
        document.getElementById('newsNext').onclick = () => track.scrollBy({ left:  step, behavior: 'smooth' });
    })();

    // ── Modales noticias ──────────────────────────────────────────────────────
    function openNewsModal(id) {
        document.getElementById('modal-' + id).classList.add('show');
        document.body.style.overflow = 'hidden';
    }
    function closeNewsModal(id) {
        document.getElementById('modal-' + id).classList.remove('show');
        document.body.style.overflow = '';
    }
    function handleModalClick(e, id) {
        if (e.target === e.currentTarget) closeNewsModal(id);
    }

    function expandText(id) {
        document.getElementById('short-' + id).style.display = 'none';
        document.getElementById('full-'  + id).style.display = 'block';
    }
    function collapseText(id) {
        document.getElementById('full-'  + id).style.display = 'none';
        document.getElementById('short-' + id).style.display = 'block';
    }

    // ── Lightbox ──────────────────────────────────────────────────────────────
    function openLightbox(src) {
        document.getElementById('lightbox-img').src = src;
        document.getElementById('lightbox').classList.add('show');
    }
    function closeLightbox() {
        document.getElementById('lightbox').classList.remove('show');
    }
    </script>

</x-app-layout>