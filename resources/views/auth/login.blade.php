<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BHCIS Sta. Ana</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --font-display: 'Fraunces', Georgia, serif;
            --font-body: 'Source Sans 3', system-ui, sans-serif;
            --bg-page: #f5f0e8;
            --bg-surface: #fdfcfa;
            --bg-card: #ffffff;
            --ink: #1a1f1c;
            --ink-muted: #5c6560;
            --border: rgba(26, 31, 28, 0.12);
            --primary: #0d4a3c;
            --accent: #c45c41;
            --accent-strong: #b13f2f;
            --accent-hover: #a84d36;
            --teal-soft: rgba(13, 74, 60, 0.08);
            --shadow-md: 0 4px 12px rgba(26, 31, 28, 0.08);
            --shadow-lg: 0 12px 32px rgba(26, 31, 28, 0.12);
        }

        input[type="text"], input[type="password"] {
            border: 1px solid var(--border);
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 0.25rem rgba(13, 74, 60, 0.2);
        }

        .accent-button {
            background: var(--accent-strong);
            color: #ffffff;
            box-shadow: 0 2px 8px rgba(196, 92, 65, 0.35);
            border: 1px solid transparent;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }

        .accent-button:hover {
            background: var(--accent-hover);
        }

        .accent-button:focus-visible {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(13, 74, 60, 0.35);
        }

        .grain::before {
            content: '';
            position: absolute;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.9' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }
        @keyframes fadeSlideUp {
            from { opacity: 0; transform: translateY(16px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-in { animation: fadeSlideUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards; }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center font-sans antialiased overflow-hidden" style="background: var(--bg-page); font-family: var(--font-body);">
    <div class="grain fixed inset-0 z-0"></div>
    <div class="absolute inset-0 z-0 opacity-50" style="background: linear-gradient(145deg, var(--teal-soft) 0%, transparent 40%, rgba(196,92,65,0.06) 100%);"></div>

    <div class="relative z-10 w-full max-w-md mx-4">
        <div class="rounded-2xl border p-8 lg:p-10 animate-in opacity-0" style="background: var(--bg-card); border-color: var(--border); box-shadow: var(--shadow-lg);">
            <div class="text-center mb-8">
                
                <h1 class="font-display font-semibold text-2xl lg:text-3xl mb-2" style="color: var(--ink); font-family: var(--font-display);">BHCIS</h1>
                <p class="text-sm" style="color: var(--ink-muted);">Sta. Ana Health Center — sign in to continue</p>
            </div>

            <form action="{{ route('login.process') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-lg border-l-4 text-sm" style="background: rgba(196, 92, 65, 0.08); border-color: var(--accent); color: var(--accent-hover);">
                        <p class="font-semibold">Login failed</p>
                        <p class="mt-1">{{ $errors->first() }}</p>
                    </div>
                @endif

                <div class="mb-5">
                    <label for="username" class="block text-sm font-medium mb-2" style="color: var(--ink);">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}"
                           class="w-full px-4 py-3 rounded-lg border text-[var(--ink)] placeholder-[var(--ink-muted)] focus:outline-none focus:ring-2 transition"
                           style="border-color: var(--border); --tw-ring-color: var(--primary);"
                           placeholder="Enter your username" required autofocus>
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium mb-2" style="color: var(--ink);">Password</label>
                    <input type="password" name="password" id="password"
                           class="w-full px-4 py-3 rounded-lg border text-[var(--ink)] placeholder-[var(--ink-muted)] focus:outline-none focus:ring-2 transition"
                           style="border-color: var(--border); --tw-ring-color: var(--primary);"
                           placeholder="Enter your password" required>
                </div>

                <div class="flex items-center justify-between mb-6">
                    <label class="flex items-center text-sm cursor-pointer" style="color: var(--ink-muted);">
                        <input type="checkbox" name="remember" class="mr-2 h-4 w-4 rounded border-[var(--border)]" style="accent-color: var(--primary);">
                        Remember me
                    </label>
                    <a href="{{ route('password.forgot') }}" class="text-sm font-medium" style="color: var(--primary); text-decoration: underline;">
                        Forgot Password?
                    </a>
                </div>

                <button type="submit" class="w-full py-3 rounded-xl text-sm font-semibold transition-all duration-200 active:scale-[0.99] accent-button">
                    Sign in
                </button>
            </form>

            <p class="text-center text-xs mt-8" style="color: var(--ink-muted);">
                &copy; {{ date('Y') }} Barangay Sta. Ana Health Center
            </p>
        </div>
    </div>
</body>
</html>
