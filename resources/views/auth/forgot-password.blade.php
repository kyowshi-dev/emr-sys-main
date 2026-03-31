<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - BHCIS Sta. Ana</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;0,9..144,700&family=Source+Sans+3:ital,wght@0,400;0,500;0,600;0,700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        :root {
            --bg-page: #f5f0e8;
            --bg-card: #fff;
            --ink: #1a1f1c;
            --ink-muted: #5c6560;
            --border: rgba(26, 31, 28, 0.12);
            --primary: #0d4a3c;
            --accent-strong: #b13f2f;
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center" style="background: var(--bg-page); font-family:'Source Sans 3', sans-serif;">
    <div class="w-full max-w-sm p-8 bg-white rounded-2xl border border-gray-200 shadow-lg">
        <h1 class="text-2xl font-bold text-emerald-900 mb-4">Forgot Password</h1>
        <p class="text-sm text-gray-600 mb-6">Enter your username and an admin will be notified for manual reset in account management.</p>

        @if(session('success'))
            <div class="mb-4 p-3 rounded bg-emerald-100 text-emerald-800">{{ session('success') }}</div>
        @endif

        @error('username')
            <div class="mb-4 p-3 rounded bg-rose-100 text-rose-800">{{ $message }}</div>
        @enderror

        <form action="{{ route('password.forgot.submit') }}" method="POST">
            @csrf
            <div class="mb-5">
                <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Username</label>
                <input id="username" name="username" value="{{ old('username') }}" required
                       class="w-full px-3 py-2 rounded-lg border border-gray-300 focus:border-emerald-500 focus:ring focus:ring-emerald-200"
                       placeholder="Your username"/>
            </div>

            <div class="flex items-center justify-between gap-2">
                <a href="{{ route('login') }}" class="text-sm text-sky-600 hover:underline">Back to sign in</a>
                <button type="submit" class="px-4 py-2 rounded-lg bg-emerald-600 text-white font-semibold hover:bg-emerald-700">Submit</button>
            </div>
        </form>
    </div>
</body>
</html>