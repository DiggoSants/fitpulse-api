<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'FIT PULSE') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    @vite(['resources/css/style.css'])

    <style>
        /* ── GUEST LAYOUT ─────────────────────────────── */
        .auth-page {
            min-height: 100vh;
            background: var(--bg);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 16px;
            position: relative;
            overflow: hidden;
        }

        /* Watermark de fundo */
        .auth-watermark {
            position: fixed;
            inset: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0;
            pointer-events: none;
            z-index: 0;
            opacity: 0.045;
        }
        .auth-watermark span {
            font-family: var(--font-primary);
            font-size: clamp(80px, 20vw, 260px);
            letter-spacing: 8px;
            line-height: 0.85;
            color: transparent;
            -webkit-text-stroke: 2px #fff;
            text-transform: uppercase;
            white-space: nowrap;
        }

        /* Logo */
        .auth-logo {
            position: relative;
            z-index: 2;
            text-align: center;
            text-decoration: none;
            margin-bottom: 32px;
            display: inline-block;
        }
        .auth-logo-title {
            font-family: var(--font-primary);
            font-size: 48px;
            font-weight: 900;
            letter-spacing: 4px;
            color: var(--text);
            line-height: 1;
        }
        .auth-logo-title span {
            color: var(--red);
        }
        .auth-logo-sub {
            font-family: var(--font-secondary);
            font-size: 11px;
            letter-spacing: 6px;
            color: var(--muted);
            margin-top: 4px;
            text-transform: uppercase;
        }

        /* Card do formulário */
        .auth-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 420px;
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.10);
            border-radius: 20px;
            padding: 36px 32px;
            box-shadow: 0 24px 60px rgba(0,0,0,0.5);
        }

        /* Inputs */
        .auth-card label {
            display: block;
            font-family: var(--font-secondary);
            font-size: 12px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 6px;
        }
        .auth-card input[type="text"],
        .auth-card input[type="email"],
        .auth-card input[type="password"] {
            width: 100%;
            padding: 11px 14px;
            background: rgba(255,255,255,0.05);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 10px;
            color: var(--text);
            font-family: var(--font-secondary);
            font-size: 14px;
            outline: none;
            transition: border-color .2s, background .2s;
        }
        .auth-card input[type="text"]:focus,
        .auth-card input[type="email"]:focus,
        .auth-card input[type="password"]:focus {
            border-color: var(--red);
            background: rgba(214,21,50,0.06);
        }

        /* Checkbox */
        .auth-card input[type="checkbox"] {
            accent-color: var(--red);
            width: 15px;
            height: 15px;
        }
        .auth-card .remember-label {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
            color: var(--muted);
            text-transform: none;
            letter-spacing: 0;
            cursor: pointer;
        }

        /* Erros */
        .auth-card .text-red-600,
        .auth-card .text-red-400 {
            color: #ff4d6a !important;
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }

        /* Botão primário */
        .auth-btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            background: var(--red);
            border: 2px solid var(--red);
            color: #fff;
            font-family: var(--font-secondary);
            font-size: 13px;
            font-weight: 800;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
            transition: transform .15s, box-shadow .2s, filter .2s;
        }
        .auth-btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(214,21,50,0.4);
            filter: brightness(1.08);
        }

        /* Link secundário */
        .auth-link {
            color: var(--muted);
            font-size: 13px;
            font-weight: 600;
            text-decoration: none;
            transition: color .2s;
        }
        .auth-link:hover {
            color: var(--red);
        }

        /* Linha separadora de ações */
        .auth-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 24px;
            gap: 12px;
            flex-wrap: wrap;
        }

        /* Texto de status da sessão */
        .auth-status {
            margin-bottom: 16px;
            font-size: 13px;
            font-weight: 600;
            color: #4ade80;
            background: rgba(74,222,128,0.08);
            border: 1px solid rgba(74,222,128,0.2);
            border-radius: 8px;
            padding: 10px 14px;
        }

        /* Divider entre campos */
        .auth-field {
            margin-bottom: 18px;
        }
        .auth-field:last-of-type {
            margin-bottom: 0;
        }

        @media (max-width: 480px) {
            .auth-card {
                padding: 28px 20px;
            }
            .auth-logo-title {
                font-size: 36px;
            }
        }
    </style>
</head>
<body>

<div class="auth-page">

    {{-- Watermark de fundo --}}
    <div class="auth-watermark" aria-hidden="true">
        <span>FIT</span>
        <span>PULSE</span>
    </div>

    {{-- Logo --}}
    <a href="/" class="auth-logo">
        <div class="auth-logo-title">FIT<span>PULSE</span></div>
        <div class="auth-logo-sub">Academia</div>
    </a>

    {{-- Card com o slot do formulário --}}
    <div class="auth-card">
        {{ $slot }}
    </div>

</div>

</body>
</html>