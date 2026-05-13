<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const systemTheme = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            const theme = savedTheme || systemTheme;
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
                document.body.classList.add('dark', 'bg-gray-900');
            }
        })();
    </script>
</head>
<body class="min-h-screen bg-gray-100 dark:bg-gray-900 flex items-center justify-center">
    <div class="w-full max-w-md p-8 bg-white dark:bg-slate-900 rounded-2xl shadow-lg">
        <!-- Logo / Nama Toko -->
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">POS System</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Masuk ke akun Anda</p>
        </div>

        <!-- Error message -->
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 text-red-700 rounded-lg text-sm dark:bg-red-900 dark:text-red-200">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Success message -->
        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-700 rounded-lg text-sm dark:bg-green-900 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none
                           focus:ring-2 focus:ring-blue-500 dark:bg-slate-800 dark:border-slate-700
                           dark:text-white" placeholder="email@example.com">
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none
                           focus:ring-2 focus:ring-blue-500 dark:bg-slate-800 dark:border-slate-700
                           dark:text-white" placeholder="••••••••">
            </div>
            <button type="submit"
                    class="w-full py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium">
                Masuk
            </button>
        </form>
    </div>
</body>
</html>
