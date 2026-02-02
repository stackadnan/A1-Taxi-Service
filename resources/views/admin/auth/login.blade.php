<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login - AirportServices</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
  <!-- Tailwind (kept) -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <style>
    html, body { height: 100%; font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial; }

    /* Subtle entrance animation */
    .fade-in-up { transform: translateY(8px); opacity: 0; animation: fadeUp 420ms ease forwards; }
    @keyframes fadeUp { to { transform: translateY(0); opacity: 1; } }

    /* Decorative plane icon */
    .plane-svg { filter: drop-shadow(0 8px 20px rgba(0,0,0,0.25)); }
  </style>
</head>
<body class="min-h-screen bg-gradient-to-r from-white via-gray-50 to-gray-100">

  <div class="min-h-screen grid lg:grid-cols-12">
    <!-- Left column: centered card -->
    <div class="lg:col-span-5 flex items-center justify-center px-6 md:px-16 py-10">
      <div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 md:p-10 transform transition-all duration-300 fade-in-up">

        <div class="flex items-center gap-4 mb-6">
          <img src="{{ asset('images/aero-cab-logo.png') }}" alt="Aero" class="h-12 w-12 rounded-md object-cover" onerror="this.src='https://via.placeholder.com/64?text=A';" />
          <div>
            <h1 class="text-2xl font-bold text-gray-900">Welcome Back 👋</h1>
            <p class="text-sm text-gray-500">Sign in to manage bookings, drivers and settings.</p>
          </div>
        </div>

        @if($errors->any())
          <div class="mb-4 rounded-md bg-red-50 border border-red-100 p-3 text-sm text-red-700">
            <strong class="block font-semibold">There were problems with your submission</strong>
            <ul class="mt-2 list-disc list-inside">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" class="space-y-4" novalidate>
          @csrf

          <div>
            <label class="block text-xs font-medium text-gray-700 mb-1">Username</label>
            <input type="text" name="username" value="{{ old('username') }}" required placeholder="Enter username" class="block w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" autofocus />
          </div>

          <div class="relative">
            <label class="block text-xs font-medium text-gray-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required placeholder="Enter password" class="block w-full rounded-lg border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-400" />
            <button type="button" id="togglePw" class="absolute right-3 top-9 text-gray-400 hover:text-gray-600 focus:outline-none" aria-label="Show password">
              <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a9.967 9.967 0 012.19-3.528M6.1 6.1L18 18m-1.875-1.875A3 3 0 1010 9"/></svg>
            </button>
          </div>

          <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center text-gray-600">
              <input type="checkbox" name="remember" class="mr-2" /> Remember me
            </label>
            <a href="#" class="text-indigo-600 hover:underline">Forgot?</a>
          </div>

          <div>
            <button type="submit" class="w-full py-3 rounded-lg bg-gradient-to-r from-indigo-600 to-indigo-500 text-white font-semibold shadow hover:from-indigo-700 hover:to-indigo-600 transition">Log In</button>
          </div>

        </form>

        <p class="text-xs text-gray-400 mt-6 text-center">© {{ date('Y') }} BAT · Crafted with ❤️ by BXS</p>
      </div>
    </div>

    <!-- Right column: image/background -->
    <div class="hidden lg:block lg:col-span-7 relative">
      <div class="absolute inset-0 bg-gradient-to-tr from-indigo-700 to-purple-600"></div>
      <div class="absolute inset-0 opacity-30 bg-[url('https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=1400&q=80')] bg-cover bg-center"></div>

      <div class="relative z-10 h-full flex flex-col items-center justify-center text-white px-8">
        <svg class="plane-svg mb-6 w-32 h-32 text-white opacity-90" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true"><path d="M2 32c12-2 24-6 42-14 4-2 10 0 6 3-4 3-12 8-18 11-6 3-20 11-30 14-4 1-6 2-6 2s2-6 6-16z" fill="rgba(255,255,255,0.9)"/></svg>

        <h2 class="text-3xl font-bold mb-2">Nice to see you again</h2>
        <p class="text-indigo-100 max-w-lg text-center">Welcome to the admin panel — manage bookings, drivers, and settings with ease.</p>

      </div>
    </div>
  </div>

  <script>
    // toggle password visibility
    document.getElementById('togglePw')?.addEventListener('click', function () {
      var pw = document.getElementById('password');
      var open = document.getElementById('eyeOpen');
      var closed = document.getElementById('eyeClosed');
      if (pw.type === 'password') {
        pw.type = 'text';
        open.classList.add('hidden');
        closed.classList.remove('hidden');
      } else {
        pw.type = 'password';
        open.classList.remove('hidden');
        closed.classList.add('hidden');
      }
    });

    // basic client-side validation to improve UX (non-blocking)
    (function(){
      var form = document.querySelector('form');
      if (!form) return;
      form.addEventListener('submit', function(e){
        var u = form.querySelector('input[name="username"]');
        var p = form.querySelector('input[name="password"]');
        if (!u.value || !p.value) {
          e.preventDefault();
          alert('Please enter username and password');
        }
      });
    })();
  </script>
</body>
</html>