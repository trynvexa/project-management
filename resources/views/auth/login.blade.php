<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="theme-color" content="#1a2027">
    <title>Login · Digital Code</title>
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
        /* Light frosted glass card */
        .card {
            width: min(100%, 430px);
            background: rgba(13, 18, 26, 0.38);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.35), inset 0 1px 0 rgba(255,255,255,.18);
            padding: 34px 34px 28px;
        }
        .brand { display: flex; justify-content: center; margin-bottom: 16px; }
        .brand img { height: 2.1rem; width: auto; }
        .title { font-size: 31px; font-weight: 800; color: #fff; margin: 0 0 4px; text-align: center; letter-spacing: .01em; text-shadow: 0 2px 12px rgba(0,0,0,.4); }
        .subtitle { text-align: center; font-size: 13px; font-weight: 500; color: rgba(255,255,255,.78); margin: 0 0 24px; text-shadow: 0 1px 8px rgba(0,0,0,.4); }
        .error { background: rgba(120,30,45,.4); border: 1px solid rgba(255,255,255,.25); color: #ffe3e7; font-size: 12.5px; font-weight: 600; border-radius: 14px; padding: 10px 14px; margin-bottom: 16px; text-align: center; }
        .field { position: relative; margin-bottom: 16px; }
        .input { width: 100%; border: 1px solid rgba(255,255,255,.28); border-radius: 999px; background: rgba(255,255,255,.14); padding: 14px 46px 14px 24px; font-size: 14px; font-weight: 500; color: #fff; outline: none; transition: border-color .25s, background-color .25s, box-shadow .25s; box-shadow: inset 0 1px 3px rgba(0,0,0,.2); text-shadow: 0 1px 4px rgba(0,0,0,.3); }
        .input::placeholder { color: rgba(255,255,255,.6); letter-spacing: .04em; text-shadow: none; }
        .input:focus { border-color: rgba(255,255,255,.55); background: rgba(255,255,255,.2); box-shadow: 0 0 0 4px rgba(255,255,255,.1), inset 0 1px 3px rgba(0,0,0,.2); }
        .input[type="password"] { letter-spacing: .18em; font-weight: 700; }
        .input[type="password"]::placeholder { letter-spacing: .02em; font-weight: 400; }
        .eye { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); border: 0; background: transparent; color: rgba(255,255,255,.75); width: 36px; height: 36px; display: grid; place-items: center; cursor: pointer; border-radius: 999px; }
        .eye:hover { background: rgba(255,255,255,.16); color: #fff; }
        .eye svg { width: 19px; height: 19px; }
        .btn { width: 100%; border: 1px solid rgba(255,255,255,.25); border-radius: 999px; background: linear-gradient(135deg, #9c4257, #7e3243); color: #fff; font-weight: 900; font-size: 15px; letter-spacing: .22em; text-indent: .22em; padding: 15px; cursor: pointer; text-shadow: 0 1px 3px rgba(0,0,0,.35); box-shadow: 0 10px 24px rgba(126,50,67,.35), inset 0 1px 0 rgba(255,255,255,.25); transition: transform .25s, box-shadow .25s, filter .25s; }
        .btn:hover { transform: translateY(-1px); filter: brightness(1.07); box-shadow: 0 14px 30px rgba(126,50,67,.42), inset 0 1px 0 rgba(255,255,255,.25); }
        .btn:active { transform: none; }
        .btn:disabled { opacity: .65; cursor: wait; transform: none; }
        .links { display: flex; align-items: center; justify-content: space-between; margin: 18px 4px 0; font-size: 13px; }
        .links a { color: #fff; text-decoration: none; font-weight: 600; position: relative; padding-bottom: 2px; text-shadow: 0 1px 6px rgba(0,0,0,.45); }
        .links a::after { content: ""; position: absolute; left: 0; bottom: 0; height: 1.5px; width: 0; background: #8E3A4D; transition: width .25s ease; }
        .links a:hover { color: #8E3A4D; }
        .links a:hover::after { width: 100%; }
        .divider { border: 0; border-top: 1px solid rgba(255,255,255,.55); margin: 22px 0 0; }
        .orlogin { display: flex; align-items: center; gap: 12px; text-align: center; font-size: 11px; letter-spacing: .22em; text-indent: .22em; color: rgba(255,255,255,.8); margin: 16px 0 14px; font-weight: 700; text-shadow: 0 1px 6px rgba(0,0,0,.45); }
        .orlogin::before, .orlogin::after { content: ""; flex: 1; height: 1px; background: rgba(255,255,255,.3); }
        .social { display: flex; justify-content: center; align-items: center; gap: 22px; }
        .social a { width: 74px; height: 74px; display: grid; place-items: center; border-radius: 999px; position: relative; transition: transform .3s cubic-bezier(.2,.8,.2,1), background-color .3s, box-shadow .3s, border-color .3s; }
        .social a svg { transition: transform .3s cubic-bezier(.2,.8,.2,1); }
        .social a:hover svg { transform: scale(1.12) rotate(-4deg); }
        .social a.google { background: rgba(255,255,255,.88); border: 1px solid rgba(255,255,255,.95); box-shadow: 0 6px 18px rgba(0,0,0,.22), inset 0 1px 0 #fff; }
        .social a.google:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(66,133,244,.4), 0 4px 14px rgba(0,0,0,.2); }
        .social a.github { background: rgba(18,23,30,.62); border: 1px solid rgba(255,255,255,.35); box-shadow: 0 6px 18px rgba(0,0,0,.3), inset 0 1px 0 rgba(255,255,255,.18); }
        .social a.github:hover { transform: translateY(-3px); box-shadow: 0 12px 28px rgba(0,0,0,.45), 0 0 0 4px rgba(255,255,255,.12); border-color: rgba(255,255,255,.6); }
        .social a:active { transform: translateY(0) scale(.97); }
        .social svg { width: 36px; height: 36px; display: block; filter: drop-shadow(0 1px 3px rgba(0,0,0,.35)); }
        @media (prefers-reduced-motion: reduce) { .card { animation: none; } }
    </style>
</head>
<body>
<img class="bg-photo" src="{{ asset('images/login-bg.jpg') }}" alt="" aria-hidden="true" fetchpriority="high">
<div class="bg-veil" aria-hidden="true"></div>

<div class="page">
    <main class="card">
        <a class="brand" href="{{ route('login') }}"><img src="{{ asset('images/logo-dc.png') }}" alt="Digital Code"></a>
        <h1 class="title">Welcome</h1>
        <p class="subtitle">Sign in to continue to Digital Code</p>

        @if ($errors->any())
            <div class="error" role="alert">{{ $errors->first() }}</div>
        @endif

        <form action="{{ route('login') }}" method="POST" id="loginForm">
            @csrf
            <div class="field">
                <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="Email">
            </div>

            <div class="field">
                <input id="password" class="input" type="password" name="password" autocomplete="current-password" required placeholder="Password">
                <button type="button" class="eye" id="eyeBtn" aria-label="Show password">
                    <svg id="eyeOpen" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg id="eyeClosed" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="display:none"><path d="M3 3l18 18"/><path d="M10.6 5.1A10.9 10.9 0 0 1 12 5c6.5 0 10 7 10 7a17 17 0 0 1-3.2 3.9M6.6 6.6A16.6 16.6 0 0 0 2 12s3.5 7 10 7c1.4 0 2.7-.3 3.8-.8"/><path d="M9.9 9.9a3 3 0 0 0 4.2 4.2"/></svg>
                </button>
            </div>

            <button type="submit" class="btn" data-loading="Signing in…">LOGIN</button>
        </form>

        <div class="links">
            <a href="{{ route('password.request') }}">Forgot Password ?</a>
            <a href="{{ route('register') }}">Sign Up</a>
        </div>

        <p class="orlogin">OR LOGIN WITH</p>
        <div class="social">
            <a class="google" href="#" onclick="return false" aria-label="Google">
                <svg width="22" height="22" viewBox="0 0 24 24"><path fill="#4285F4" d="M23.5 12.3c0-.9-.1-1.5-.3-2.3H12v4.3h6.5c-.1 1.1-.8 2.7-2.4 3.8l3.6 2.8c2.2-2 3.8-5 3.8-8.6Z"/><path fill="#34A853" d="M12 24c3.2 0 5.9-1.1 7.9-2.9l-3.8-2.9c-1 .7-2.4 1.2-4.1 1.2-3.1 0-5.8-2.1-6.8-5l-3.8 3C3.4 21.5 7.4 24 12 24Z"/><path fill="#FBBC05" d="M5.2 14.4c-.2-.7-.4-1.5-.4-2.4s.1-1.7.4-2.4l-3.7-2.9C.5 8.7 0 10.3 0 12s.5 3.3 1.4 4.7l3.8-2.3Z"/><path fill="#EA4335" d="M12 4.7c1.8 0 3 .8 3.7 1.4l3.3-3.2C17.9 1.1 15.2 0 12 0 7.4 0 3.4 2.5 1.4 6.9l3.8 2.9c1-2.9 3.7-5.1 6.8-5.1Z"/></svg>
            </a>
            <a class="github" href="#" onclick="return false" aria-label="GitHub">
                <svg viewBox="0 0 24 24" fill="#fff" style="width:40px;height:40px"><path d="M12 .297c-6.63 0-12 5.373-12 12 0 5.303 3.438 9.8 8.205 11.385.6.113.82-.258.82-.577 0-.285-.01-1.04-.015-2.04-3.338.724-4.042-1.61-4.042-1.61C4.422 18.07 3.633 17.7 3.633 17.7c-1.087-.744.084-.729.084-.729 1.205.084 1.838 1.236 1.838 1.236 1.07 1.835 2.809 1.305 3.495.998.108-.776.417-1.305.76-1.605-2.665-.3-5.466-1.332-5.466-5.93 0-1.31.465-2.38 1.235-3.22-.135-.303-.54-1.523.105-3.176 0 0 1.005-.322 3.3 1.23.96-.267 1.98-.399 3-.405 1.02.006 2.04.138 3 .405 2.28-1.552 3.285-1.23 3.285-1.23.645 1.653.24 2.873.12 3.176.765.84 1.23 1.91 1.23 3.22 0 4.61-2.805 5.625-5.475 5.92.42.36.81 1.096.81 2.22 0 1.606-.015 2.896-.015 3.286 0 .315.21.69.825.57C20.565 22.092 24 17.592 24 12.297c0-6.627-5.373-12-12-12"/></svg>
            </a>
        </div>
    </main>
</div>

<script>
    const eyeBtn = document.getElementById('eyeBtn');
    const pwd = document.getElementById('password');
    eyeBtn?.addEventListener('click', () => {
        const show = pwd.type === 'password';
        pwd.type = show ? 'text' : 'password';
        document.getElementById('eyeOpen').style.display = show ? 'none' : '';
        document.getElementById('eyeClosed').style.display = show ? '' : 'none';
    });
    document.getElementById('loginForm')?.addEventListener('submit', (e) => {
        const btn = e.target.querySelector('button[type="submit"]');
        if (btn && !btn.dataset.done) { btn.dataset.done = '1'; btn.disabled = true; btn.textContent = btn.dataset.loading || 'Signing in…'; }
    });

    // GSAP entrance (halus, sekali jalan; diam total jika CDN gagal / reduced-motion)
    window.addEventListener('load', () => {
        if (!window.gsap || matchMedia('(prefers-reduced-motion: reduce)').matches) return;
        const tl = gsap.timeline({ defaults: { ease: 'power3.out' } });
        tl.from('.card', { y: 30, autoAlpha: 0, duration: .7 })
          .from('.brand', { y: 14, autoAlpha: 0, duration: .5 }, '-=.45')
          .from(['.title', '.subtitle'], { y: 16, autoAlpha: 0, duration: .5, stagger: .09 }, '-=.35')
          .from('.field', { y: 16, autoAlpha: 0, duration: .5, stagger: .1 }, '-=.3')
          .from('.btn', { y: 14, autoAlpha: 0, duration: .5 }, '-=.25')
          .from(['.links', '.orlogin'], { autoAlpha: 0, duration: .4, stagger: .1 }, '-=.3')
          .from('.social a', { scale: .5, autoAlpha: 0, duration: .45, stagger: .09, ease: 'back.out(1.6)' }, '-=.25');
    });
</script>
</body>
</html>
