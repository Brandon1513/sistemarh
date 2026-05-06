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

    .rh-card { background:#fff; border-radius:16px; box-shadow:0 1px 3px rgba(106,44,117,.08),0 8px 28px rgba(106,44,117,.07); overflow:hidden; border:1px solid var(--border); }

    /* Filtros */
    .rh-filter-bar { background:var(--beige-light); border-bottom:1.5px solid var(--border); padding:18px 24px; display:flex; flex-wrap:wrap; gap:12px; align-items:flex-end; }
    .rh-filter-group { display:flex; flex-direction:column; gap:5px; flex:1; min-width:160px; }
    .rh-filter-label { font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); }
    .rh-filter-input,.rh-filter-select { height:38px; padding:0 12px; border:1.5px solid var(--border); border-radius:8px; font-size:13.5px; color:var(--text); background:#fff; transition:border-color .2s,box-shadow .2s; outline:none; width:100%; }
    .rh-filter-input:focus,.rh-filter-select:focus { border-color:var(--brand); box-shadow:0 0 0 3px rgba(106,44,117,.12); }

    /* Barra de exportaciones */
    .rh-export-bar { display:flex; align-items:center; gap:10px; padding:12px 24px; background:#fff; border-bottom:1px solid var(--border); flex-wrap:wrap; }

    /* Botones */
    .rh-btn { display:inline-flex; align-items:center; gap:6px; padding:0 16px; height:38px; border-radius:8px; font-size:13px; font-weight:600; cursor:pointer; border:none; transition:all .18s; white-space:nowrap; text-decoration:none; }
    .rh-btn-sm { height:30px; padding:0 11px; font-size:12px; border-radius:6px; }
    .rh-btn-primary { background:var(--brand); color:#fff; }
    .rh-btn-primary:hover { background:var(--brand-dark); transform:translateY(-1px); box-shadow:0 4px 12px rgba(106,44,117,.3); color:#fff; }
    .rh-btn-outline { background:#fff; color:var(--muted); border:1.5px solid var(--border); }
    .rh-btn-outline:hover { border-color:var(--brand-mid); color:var(--brand); }
    .rh-btn-gold   { background:var(--gold); color:#fff; }
    .rh-btn-gold:hover   { background:#bf922d; color:#fff; transform:translateY(-1px); }
    .rh-btn-brown  { background:var(--brown); color:#fff; }
    .rh-btn-brown:hover  { background:#352718; color:#fff; }
    /* Exportaciones */
    .rh-btn-export-blue  { background:#2563eb; color:#fff; }
    .rh-btn-export-blue:hover  { background:#1d4ed8; color:#fff; transform:translateY(-1px); }
    .rh-btn-export-green { background:#2d7a4f; color:#fff; }
    .rh-btn-export-green:hover { background:#236040; color:#fff; transform:translateY(-1px); }
    .rh-btn-export-rose  { background:var(--rose); color:#fff; }
    .rh-btn-export-rose:hover  { background:#923d59; color:#fff; transform:translateY(-1px); }

    /* Tabla */
    .rh-table { width:100%; border-collapse:collapse; }
    .rh-table thead th { padding:12px 16px; background:var(--beige-light); border-bottom:1.5px solid var(--border); font-size:11px; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:var(--brand); text-align:left; }
    .rh-table tbody tr { border-bottom:1px solid #f0e6f5; transition:background .15s; }
    .rh-table tbody tr:hover { background:#faf4fc; }
    .rh-table tbody tr:last-child { border-bottom:none; }
    .rh-table td { padding:13px 16px; font-size:14px; color:var(--text); vertical-align:middle; }

    /* Avatar */
    .rh-avatar    { width:34px; height:34px; border-radius:50%; object-fit:cover; border:2px solid var(--border); flex-shrink:0; }
    .rh-avatar-ph { width:34px; height:34px; border-radius:50%; background:linear-gradient(135deg,var(--brand),var(--brand-mid)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:700; flex-shrink:0; }

    /* Tipo entrada/salida */
    .rh-tipo-tag     { display:inline-flex; align-items:center; gap:4px; padding:2px 9px; border-radius:6px; font-size:11px; font-weight:700; }
    .rh-tipo-entrada { background:#e0eaff; color:#1d4ed8; }
    .rh-tipo-salida  { background:var(--gold-pale); color:var(--brown); }

    /* Tipo de solicitud (ausencias) */
    .rh-sol-permiso    { background:var(--brand-light); color:var(--brand); border:1px solid var(--brand-mid); border-radius:5px; font-size:11px; font-weight:700; padding:2px 8px; display:inline-block; }
    .rh-sol-comision   { background:var(--gold-pale); color:var(--brown); border:1px solid var(--gold-light); border-radius:5px; font-size:11px; font-weight:700; padding:2px 8px; display:inline-block; }
    .rh-sol-suspension { background:#fce8ee; color:var(--rose); border:1px solid #f0b3c3; border-radius:5px; font-size:11px; font-weight:700; padding:2px 8px; display:inline-block; }

    /* Período fechas */
    .rh-date-range { display:flex; flex-direction:column; gap:2px; }
    .rh-date-from  { font-size:13px; font-weight:600; color:var(--text); }
    .rh-date-to    { font-size:11px; color:var(--muted); }

    /* Días badge */
    .rh-days-pill { display:inline-flex; align-items:center; justify-content:center; min-width:36px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:700; }
    .rh-days-few  { background:#e8f5ee; color:#1b6b38; }
    .rh-days-mid  { background:var(--gold-pale); color:var(--brown); }
    .rh-days-many { background:var(--brand-light); color:var(--brand); }

    /* Status */
    .rh-badge { display:inline-flex; align-items:center; gap:5px; padding:3px 10px; border-radius:20px; font-size:12px; font-weight:600; }
    .rh-badge-pendiente,.rh-badge-Pendiente { background:#fef9c3; color:#854d0e; }
    .rh-badge-aprobado ,.rh-badge-Aprobado  { background:#e8f5ee; color:#1b6b38; }
    .rh-badge-rechazado,.rh-badge-Rechazado { background:#fce8ee; color:var(--rose); }
    .rh-dot { width:6px; height:6px; border-radius:50%; display:inline-block; }
    .rh-badge-pendiente .rh-dot,.rh-badge-Pendiente .rh-dot { background:#d97706; }
    .rh-badge-aprobado  .rh-dot,.rh-badge-Aprobado  .rh-dot { background:#2d7a4f; }
    .rh-badge-rechazado .rh-dot,.rh-badge-Rechazado .rh-dot { background:var(--rose); }

    /* Alertas */
    .rh-alert { padding:12px 16px; border-radius:10px; font-size:13.5px; margin-bottom:16px; display:flex; align-items:center; gap:10px; }
    .rh-alert-success { background:#e8f5ee; color:#1b6b38; border:1px solid #b3dfc3; }

    /* Empty */
    .rh-empty { text-align:center; padding:56px 24px; color:var(--muted); }
    .rh-empty svg { width:52px; height:52px; margin:0 auto 14px; opacity:.3; }
    .rh-empty p { font-size:14px; }

    /* Footer */
    .rh-footer { padding:14px 24px; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; }

    @media(max-width:640px){ .hide-mobile{display:none;} .rh-filter-bar{padding:14px;} .rh-export-bar{padding:12px;} }
</style>