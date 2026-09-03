<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#ffd84d">
    <title>Sign in · Flowbase</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="auth-page">
    <main class="auth-shell">
        <section class="auth-intro" aria-label="About Flowbase">
            <a href="{{ route('login') }}" class="auth-brand"><span>F</span><b>Flowbase</b></a>
            <div><p class="eyebrow">PROJECT MANAGEMENT</p><h1>Work moves better when everyone sees the same plan.</h1><p>One focused workspace for projects, tasks, clients, and your team.</p></div>
            <ul class="auth-checklist" aria-label="Product benefits"><li>Clear project ownership</li><li>Live task progress</li><li>Team-ready workspace</li></ul>
        </section>
        <section class="auth-card-wrap"><div class="auth-card"><a href="{{ route('login') }}" class="auth-brand auth-brand-mobile"><span>F</span><b>Flowbase</b></a><p class="eyebrow">WELCOME BACK</p><h1>Sign in</h1><p class="auth-copy">Continue to your workspace.</p>
            @if ($errors->any())<div class="form-alert" role="alert">{{ $errors->first() }}</div>@endif
            <form action="{{ route('login') }}" method="POST" class="auth-form">@csrf
                <div><label class="form-label" for="email">Email</label><input id="email" class="form-input" type="email" name="email" value="{{ old('email') }}" autocomplete="email" required autofocus placeholder="you@example.com"></div>
                <div><div class="flex items-center justify-between gap-3"><label class="form-label" for="password">Password</label><a class="text-link" href="{{ route('password.request') }}">Forgot password?</a></div><input id="password" class="form-input" type="password" name="password" autocomplete="current-password" required></div>
                <button type="submit" class="btn-primary w-full" data-loading="Signing in…">Sign in</button>
            </form><p class="auth-footer">New to Flowbase? <a class="text-link" href="{{ route('register') }}">Create an account</a></p></div></section>
    </main>
</body>
</html>
