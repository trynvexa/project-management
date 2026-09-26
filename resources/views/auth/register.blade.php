<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1a2027">
    <title>Create account · Digital Code</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.5/gsap.min.js" defer></script>
    <style>
        * { box-sizing: border-box; }
        html, body { height: 100%; }
        body { margin: 0; font-family: "Plus Jakarta Sans", "DM Sans", system-ui, sans-serif; background: #05070b; }
        .bg-photo { position: fixed; inset: 0; width: 100%; height: 100%; object-fit: cover; z-index: 0; }
        .bg-veil { position: fixed; inset: 0; z-index: 1; pointer-events: none;
            background: linear-gradient(180deg, rgba(10,14,18,.30), rgba(10,14,18,.38));
        }
        .page { position: relative; z-index: 2; min-height: 100dvh; display: grid; place-items: center; padding: 24px 16px; }
        .card {
            width: min(100%, 430px);
            background: rgba(13, 18, 26, 0.38);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255,255,255,.18);
            padding: 34px 34px 28px;
        }
        .brand { display: flex; justify-content: center; margin-bottom: 16px; }
        .brand img { height: 2rem; width: auto; }
        .title { font-size: 24px; font-weight: 700; color: #fff; margin: 0 0 4px; text-align: center; letter-spacing: .01em; text-shadow: 0 2px 12px rgba(0,0,0,.4); }
        .subtitle { text-align: center; font-size: 12.5px; color: rgba(255,255,255,.78); margin: 0 0 24px; text-shadow: 0 1px 8px rgba(0,0,0,.4); }
        .error { background: rgba(120,30,45,.4); border: 1px solid rgba(255,255,255,.25); color: #ffe3e7; font-size: 12.5px; font-weight: 600; border-radius: 14px; padding: 10px 14px; margin-bottom: 16px; text-align: center; }
        .field { position: relative; margin-bottom: 16px; }
        .input { width: 100%; border: 1px solid rgba(255,255,255,.28); border-radius: 999px; background: rgba(255,255,255,.14); padding: 14px 24px; font-size: 14px; font-weight: 500; color: #fff; outline: none; transition: border-color .25s, background-color .25s, box-shadow .25s; box-shadow: inset 0 1px 3px rgba(0,0,0,.2); text-shadow: 0 1px 4px rgba(0,0,0,.3); }
        .input::placeholder { color: rgba(255,255,255,.6); letter-spacing: .02em; text-shadow: none; }
        .input:focus { border-color: rgba(255,255,255,.55); background: rgba(255,255,255,.2); box-shadow: 0 0 0 4px rgba(255,255,255,.1), inset 0 1px 3px rgba(0,0,0,.2); }
        .btn { width: 100%; border: 1px solid rgba(255,255,255,.25); border-radius: 999px; background: linear-gradient(135deg, #9c4257, #7e3243); color: #fff; font-weight: 800; font-size: 14px; letter-spacing: .18em; text-indent: .18em; padding: 15px; cursor: pointer; box-shadow: 0 10px 24px rgba(126,50,67,.35), inset 0 1px 0 rgba(255,255,255,.25); transition: transform .25s, box-shadow .25s, filter .25s; }
        .btn:hover { transform: translateY(-1px); filter: brightness(1.07); box-shadow: 0 14px 30px rgba(126,50,67,.42), inset 0 1px 0 rgba(255,255,255,.25); }
        .btn:disabled { opacity: .65; cursor: wait; transform: none; }
        .signin { text-align: center; font-size: 13px; color: rgba(255,255,255,.78); margin: 18px 0 0; font-weight: 600; text-shadow: 0 1px 6px rgba(0,0,0,.45); }
        .signin a { color: #fff; font-weight: 700; text-decoration: none; }
        .signin a:hover { text-decoration: underline; }
        @media (prefers-reduced-motion: reduce) { .card { animation: none; } }
    </style>
</head>
<body>
<img class="bg-photo" src="{{ asset('images/login-bg.jpg') }}" alt="" aria-hidden="true" fetchpriority="high">
<div class="bg-veil" aria-hidden="true"></div>

<div class="page">
    <main class="card">
        <a class="brand" href="{{ route('login') }}"><img src="{{ asset('images/logo-dc.png') }}" alt="Digital Code"></a>
        <h1 class="title">Create Account</h1>
        <p class="subtitle">Set up your account in a minute</p>

        @if ($errors->any())
            <div class="error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('register') }}" method="POST" id="registerForm">
            @csrf
            <div class="field">
                <input id="name" class="input" type="text" name="name" value="{{ old('name') }}" autocomplete="name" required autofocus placeholder="Full name">
            </div>
            <div class="field">
                <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required placeholder="Email">
            </div>
            <div class="field">
                <input id="password" class="input" type="password" name="password" autocomplete="new-password" required placeholder="Password">
            </div>
            <div class="field">
                <input id="password_confirmation" class="input" type="password" name="password_confirmation" autocomplete="new-password" required placeholder="Confirm password">
            </div>
            <button type="submit" class="btn" data-loading="Creating account…">CREATE ACCOUNT</button>
        </form>

        <p class="signin">Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
    </main>
</div>

<script>
    document.getElementById('registerForm')?.addEventListener('submit', (e) => {
        const btn = e.target.querySelector('button[type="submit"]');
        if (btn && !btn.dataset.done) { btn.dataset.done = '1'; btn.disabled = true; btn.textContent = btn.dataset.loading || 'Creating account…'; }
    });
    window.addEventListener('load', () => {
        if (!window.gsap || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
        tl.from('.card', { y: 30, autoAlpha: 0, duration: .7 })
          .from(['.brand', '.title', '.subtitle'], { y: 16, autoAlpha: 0, duration: .5, stagger: .09 }, '-=.4')
          .from('.field', { y: 16, autoAlpha: 0, duration: .5, stagger: .08 }, '-=.3')
          .from(['.btn', '.signin'], { y: 14, autoAlpha: 0, duration: .5, stagger: .1 }, '-=.25');
    });
</script>
</body>
</html>
