<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="flex items-center gap-2 text-xl font-semibold leading-tight text-gray-800">
                <svg class="w-5 h-5" style="color:#6A2C75" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
                </svg>
                Noticias
            </h2>
        </div>
    </x-slot>

    <style>
        :root { --brand:#6A2C75; --brand-dark:#541f5c; --brand-light:#f3eef5; --brand-mid:#BBA4C0; --gold:#D6A644; --gold-pale:#faf3e0; --brown:#473524; --rose:#AA4969; --beige-light:#f7f3ef; --border:#e6d9ed; --text:#2c1a30; --muted:#7a6682; }

        .news-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:22px; }

        .news-card { background:#fff; border-radius:14px; border:1px solid var(--border); overflow:hidden; cursor:pointer; box-shadow:0 1px 3px rgba(106,44,117,.06),0 6px 18px rgba(106,44,117,.05); transition:transform .2s,box-shadow .2s; }
        .news-card:hover { transform:translateY(-4px); box-shadow:0 8px 28px rgba(106,44,117,.14); }

        .news-card-img { width:100%; height:190px; object-fit:cover; display:block; }

        .news-card-body { padding:16px; }
        .news-card-date  { font-size:11px; color:var(--muted); font-weight:600; letter-spacing:.04em; margin-bottom:5px; }
        .news-card-title { font-size:15px; font-weight:700; color:var(--text); line-height:1.35; margin-bottom:8px; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
        .news-card-excerpt { font-size:13px; color:var(--muted); line-height:1.55; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden; }

        .news-card-footer { padding:10px 16px 14px; display:flex; align-items:center; justify-content:space-between; border-top:1px solid #f5f0f7; }
        .news-card-tag  { background:var(--brand-light); color:var(--brand); font-size:11px; font-weight:700; padding:2px 8px; border-radius:4px; }
        .news-card-read { font-size:12px; font-weight:600; color:var(--brand); display:flex; align-items:center; gap:4px; }

        /* Modal */
        .news-modal { position:fixed; inset:0; z-index:50; display:flex; align-items:center; justify-content:center; background:rgba(42,10,50,.6); backdrop-filter:blur(4px); padding:20px; opacity:0; pointer-events:none; transition:opacity .2s; }
        .news-modal.show { opacity:1; pointer-events:auto; }
        .news-modal-box { background:#fff; border-radius:16px; width:100%; max-width:760px; max-height:90vh; overflow-y:auto; position:relative; box-shadow:0 24px 60px rgba(0,0,0,.25); transform:translateY(12px); transition:transform .2s; }
        .news-modal.show .news-modal-box { transform:translateY(0); }

        .modal-cover { width:100%; height:280px; object-fit:cover; border-radius:16px 16px 0 0; display:block; }
        @media(max-width:640px){ .modal-cover{height:180px;} }
        .modal-close { position:absolute; top:14px; right:14px; width:32px; height:32px; border-radius:50%; background:rgba(0,0,0,.35); border:none; cursor:pointer; color:#fff; font-size:16px; display:flex; align-items:center; justify-content:center; transition:background .15s; }
        .modal-close:hover { background:rgba(0,0,0,.6); }

        .modal-body   { padding:26px 32px 32px; }
        @media(max-width:640px){ .modal-body{padding:20px;} }
        .modal-meta   { font-size:12px; color:var(--muted); margin-bottom:12px; display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
        .modal-tag    { background:var(--brand-light); color:var(--brand); font-weight:700; font-size:11px; padding:2px 8px; border-radius:4px; }
        .modal-title  { font-size:clamp(1.2rem,2.5vw,1.6rem); font-weight:800; color:var(--text); line-height:1.3; margin-bottom:14px; }
        .modal-content{ font-size:14.5px; color:#4a3550; line-height:1.75; }

        .modal-gallery-title { font-size:12px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--muted); margin:18px 0 8px; }
        .modal-gallery-grid  { display:flex; flex-wrap:wrap; gap:8px; }
        .modal-gallery-img   { width:90px; height:90px; object-fit:cover; border-radius:8px; cursor:pointer; border:2px solid var(--border); transition:transform .15s,box-shadow .15s; }
        .modal-gallery-img:hover { transform:scale(1.06); box-shadow:0 4px 12px rgba(106,44,117,.2); }

        /* Lightbox */
        .lightbox { position:fixed; inset:0; z-index:60; background:rgba(0,0,0,.88); backdrop-filter:blur(6px); display:flex; align-items:center; justify-content:center; padding:20px; opacity:0; pointer-events:none; transition:opacity .2s; }
        .lightbox.show { opacity:1; pointer-events:auto; }
        .lightbox img { max-width:100%; max-height:90vh; border-radius:10px; box-shadow:0 16px 48px rgba(0,0,0,.5); }
        .lightbox-close { position:absolute; top:20px; right:20px; width:36px; height:36px; border-radius:50%; background:rgba(255,255,255,.15); border:none; color:#fff; font-size:20px; cursor:pointer; display:flex; align-items:center; justify-content:center; }

        .page-title { font-size:clamp(1.6rem,3vw,2.2rem); font-weight:800; color:var(--text); margin-bottom:6px; }
        .page-sub   { font-size:14px; color:var(--muted); margin-bottom:28px; }
    </style>

    <div class="py-12" style="background-image:url('{{ asset('images/background-pattern.png') }}'); min-height:calc(100vh - 64px);">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div style="background:#fff; border-radius:16px; padding:32px 32px 40px; border:1px solid var(--border); box-shadow:0 1px 3px rgba(106,44,117,.07),0 8px 24px rgba(106,44,117,.06);">

                <p class="page-title">📰 Últimas Noticias</p>
                <p class="page-sub">Mantente al día con todo lo que pasa en la empresa.</p>

                <div class="news-grid">
                    @foreach($noticias as $noticia)
                        <div class="news-card" onclick="openModal({{ $noticia->id }})">
                            <img src="{{ $noticia->imagen ? asset('storage/'.$noticia->imagen) : asset('images/placeholder.jpg') }}"
                                class="news-card-img" alt="{{ $noticia->titulo }}">
                            <div class="news-card-body">
                                <p class="news-card-date">{{ \Carbon\Carbon::parse($noticia->fecha)->format('d/m/Y') }}</p>
                                <h3 class="news-card-title">{{ $noticia->titulo }}</h3>
                                <p class="news-card-excerpt">{{ Str::limit(strip_tags($noticia->contenido), 100) }}</p>
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

                <div style="margin-top:28px;">
                    {{ $noticias->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modales --}}
    @foreach($noticias as $noticia)
        <div class="news-modal" id="modal-{{ $noticia->id }}" onclick="handleBg(event,{{ $noticia->id }})">
            <div class="news-modal-box">
                <button class="modal-close" onclick="closeModal({{ $noticia->id }})">✕</button>
                @if($noticia->imagen)
                    <img src="{{ asset('storage/'.$noticia->imagen) }}" class="modal-cover" alt="">
                @endif
                <div class="modal-body">
                    <div class="modal-meta">
                        <span class="modal-tag">Noticia</span>
                        <span>{{ \Carbon\Carbon::parse($noticia->fecha)->format('d \d\e F \d\e Y') }}</span>
                        <span>✍ {{ $noticia->autor }}</span>
                    </div>
                    <h2 class="modal-title">{{ $noticia->titulo }}</h2>
                    @php $txt = strip_tags($noticia->contenido); @endphp
                    <div class="modal-content">
                        <p id="short-{{ $noticia->id }}">
                            {{ Str::limit($txt, 300) }}
                            @if(strlen($txt) > 300)
                                <button onclick="expandText({{ $noticia->id }})" style="color:var(--brand);font-weight:600;background:none;border:none;cursor:pointer;">Ver más ↓</button>
                            @endif
                        </p>
                        <p id="full-{{ $noticia->id }}" style="display:none;">
                            {{ $txt }}
                            <button onclick="collapseText({{ $noticia->id }})" style="display:block;margin-top:8px;color:var(--brand);font-weight:600;background:none;border:none;cursor:pointer;">Ver menos ↑</button>
                        </p>
                    </div>
                    @if($noticia->multimedia)
                        <p style="margin-top:14px;font-size:13.5px;">
                            <a href="{{ $noticia->multimedia }}" target="_blank" style="color:var(--brand);font-weight:600;">🎬 Ver multimedia</a>
                        </p>
                    @endif
                    @if($noticia->galeria->count())
                        <p class="modal-gallery-title">Galería</p>
                        <div class="modal-gallery-grid">
                            @foreach($noticia->galeria as $img)
                                <img src="{{ asset('storage/'.$img->imagen) }}" class="modal-gallery-img"
                                    onclick="openLightbox('{{ asset('storage/'.$img->imagen) }}')" alt="">
                            @endforeach
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
        function openModal(id)  { document.getElementById('modal-'+id).classList.add('show'); document.body.style.overflow='hidden'; }
        function closeModal(id) { document.getElementById('modal-'+id).classList.remove('show'); document.body.style.overflow=''; }
        function handleBg(e,id){ if(e.target===e.currentTarget) closeModal(id); }
        function expandText(id)  { document.getElementById('short-'+id).style.display='none'; document.getElementById('full-'+id).style.display='block'; }
        function collapseText(id){ document.getElementById('full-'+id).style.display='none'; document.getElementById('short-'+id).style.display='block'; }
        function openLightbox(src){ document.getElementById('lightbox-img').src=src; document.getElementById('lightbox').classList.add('show'); }
        function closeLightbox()  { document.getElementById('lightbox').classList.remove('show'); }
    </script>
</x-app-layout>