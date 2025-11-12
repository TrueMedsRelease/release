<!DOCTYPE html>
<html
@if (session('locale', 'en') == 'arb')
    lang="ar"
@elseif (session('locale', 'en') == 'gr')
    lang="el"
@elseif (session('locale', 'en') == 'hans')
    lang="zh-Hans"
@elseif (session('locale', 'en') == 'hant')
    lang="zh-Hant"
@elseif (session('locale', 'en') == 'no')
    lang="nb"
@else
    lang="{{ session('locale', 'en') }}"
@endif
>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>404 — Page not found</title>
        <style>
            :root{
                --bg-1:#22c55e;
                --bg-2:#16a34a;
                --bg-3:#84cc16;
                --card-bg: rgba(255,255,255,.85);
                --text:#0f172a; --muted:#64748b;
                --ring:#22c55e;
                --btn:#14532d;
                --btn-text:#ffffff; --btn-hover:#0f3a22;
                --chip-1: color-mix(in oklab, var(--bg-2), transparent 30%);
                --chip-2: color-mix(in oklab, var(--bg-1), transparent 30%);
            }

            @media (prefers-color-scheme:dark){
                :root{
                    --card-bg: rgba(15,23,42,.72);
                    --text:#e5e7eb; --muted:#94a3b8;
                    --btn:#e5e7eb; --btn-text:#0f172a; --btn-hover:#ffffff;
                }
            }

            *{
                box-sizing:border-box;
            }

            html,body{
                height:100%;
            }

            body{
                margin:0;
                font-family: ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, Inter, "Helvetica Neue", Arial, "Noto Sans", "Apple Color Emoji", "Segoe UI Emoji";
                color:var(--text);
                background:
                    radial-gradient(1200px 600px at 10% 10%, color-mix(in oklab, var(--bg-1), transparent 75%), transparent 70%),
                    radial-gradient(1000px 700px at 90% 20%, color-mix(in oklab, var(--bg-2), transparent 75%), transparent 70%),
                    radial-gradient(900px 600px at 50% 100%, color-mix(in oklab, var(--bg-3), transparent 75%), transparent 70%),
                    #0b1020;
            }

            .wrap{
                min-height:100%;
                display:grid;
                place-items:center;
                padding:4vmin;
            }

            .card{
                width:min(820px, 100%);
                background:var(--card-bg);
                backdrop-filter: blur(14px) saturate(120%);
                border:1px solid rgba(255,255,255,.12);
                border-radius:24px;
                padding: clamp(24px, 4vmin, 48px);
                box-shadow: 0 20px 60px rgba(0,0,0,.25);
                text-align:center;
                position:relative;
                overflow:hidden;

            }
            .badge{
                display:inline-flex;
                align-items:center;
                gap:.5rem;
                padding:.35rem .7rem;
                border-radius:999px;
                background:rgba(34,197,94,.15);
                border:1px solid rgba(34,197,94,.35);
                font-size:.9rem;
                letter-spacing:.02em;
            }

            .code{
                font-size: clamp(48px, 16vw, 140px);
                line-height:.9;
                font-weight:900;
                margin:.15em 0 .15em;
                letter-spacing:.02em;
                background: linear-gradient(135deg, var(--bg-1), var(--bg-2), var(--bg-3));
                -webkit-background-clip:text;
                background-clip:text;
                color:transparent;
            }

            .title{
                font-size: clamp(20px, 4.2vw, 32px);
                margin:.25rem 0 .5rem;
                font-weight:800;
            }

            .text{
                font-size: clamp(15px, 2.2vw, 18px);
                color:var(--muted);
                argin:0 auto 1.25rem;
                max-width:60ch;
            }

            .actions{
                display:flex;
                flex-wrap:wrap;
                gap:.75rem;
                justify-content:center;
                margin-top:1.25rem;
            }

            .btn{
                display:inline-flex;
                align-items:center;
                justify-content:center;
                gap:.5rem;
                padding:.85rem 1.1rem;
                border-radius:14px;
                text-decoration:none;
                background:var(--btn);
                color:var(--btn-text);
                font-weight:700;
                border:1px solid color-mix(in oklab, var(--btn), transparent 60%);
                transition: transform .08s ease, background .15s ease, box-shadow .15s ease;
                box-shadow: 0 10px 24px rgba(0,0,0,.2);
            }

            .btn:focus-visible{
                outline:3px solid var(--ring);
                outline-offset:2px;
            }

            .btn:hover{
                background:var(--btn-hover);
                transform:translateY(-1px);
            }

            .link{
                appearance:none; border:none;
                background:transparent;
                color:var(--text);
                padding:.85rem 1rem;
                border-radius:12px;
                font-weight:600;
                cursor:pointer;
                border:1px dashed rgba(34,197,94,.35);
            }

            .link:focus-visible{
                outline:3px solid var(--ring);
                outline-offset:2px;
            }

            .chip{
                position:absolute;
                inset:auto -40px -40px auto;
                width:220px;
                height:220px;
                border-radius:40px;
                background:
                    radial-gradient(60% 60% at 40% 40%, rgba(255,255,255,.22), transparent 60%),
                    linear-gradient(135deg, var(--chip-1), var(--chip-2));
                transform: rotate(12deg);
                filter: blur(2px);
                pointer-events:none;
            }

            .sep{
                height:1px;
                width:100%;
                background:linear-gradient(90deg, transparent, rgba(34,197,94,.35), transparent);
                margin:1.25rem 0;
            }

            .small{
                font-size:.9rem;
                color:var(--muted);
            }

            code{
                padding:.15rem .45rem;
                border-radius:.5rem;
                background:rgba(16,185,129,.12);
            }
        </style>
    </head>
    <body>
        <main class="wrap">
            <section class="card" role="alert" aria-live="polite">
                <div class="code">404</div>
                <h1 class="title">Oooops… Page not found</h1>
                <div class="actions">
                    <a class="btn" href="{{ route('home.index') }}" aria-label="Back to Shop">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M3 11L12 3l9 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M5 10v10a1 1 0 0 0 1 1h4v-6h4v6h4a1 1 0 0 0 1-1V10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Back to Shop
                    </a>
                </div>
                <div class="sep"></div>
            </section>
        </main>
    </body>
</html>
