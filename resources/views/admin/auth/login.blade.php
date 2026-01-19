<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login - AirportServices</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* small helper to ensure full-height on body when using grid */
    html, body { height: 100%; }
  </style>
</head>
<body class="min-h-screen bg-gray-50">
  <!-- Top header with logo (positioned to match requested offset) -->
  <header class="absolute top-0 left-0 w-full z-30">
    <div class="max-w-7xl mx-auto px-8 md:px-16 py-4 relative">
      <!-- logo placed with absolute offsets so it aligns with the content per design -->
      <img src="{{ asset('images/aero-cab-logo.png') }}" alt="Aero-Cab logo" class="h-12 absolute md:left-20 lg:left-20 top-6" onerror="this.src='https://via.placeholder.com/96x96?text=A';" />
    </div>
  </header>

  <div class="min-h-screen grid lg:grid-cols-12">
    <!-- Left column: form -->
    <div class="lg:col-span-4 flex flex-col justify-center px-8 md:px-16 pt-20 pb-10 bg-white relative">

      <div class="max-w-md w-full mx-auto">


        <div class="mb-2">
          <h2 class="text-2xl font-bold text-gray-900">Welcome Back !</h2>
          <p class="text-sm text-gray-500 mt-1">Sign in to continue to BAT.</p>
        </div>

        @if($errors->any())
          <div class="mt-4 mb-2 text-sm text-red-600">
            <ul>
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}" class="mt-6" novalidate>
          @csrf

          <div class="mb-4">
            <label class="block text-xs font-medium text-gray-700">Username</label>
            <input type="text" name="username" value="{{ old('username') }}" required placeholder="Enter username" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
          </div>

          <div class="mb-4 relative">
            <label class="block text-xs font-medium text-gray-700">Password</label>
            <input id="password" type="password" name="password" required placeholder="Enter password" class="mt-1 block w-full rounded-md border border-gray-200 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" />
            <button type="button" id="togglePw" class="absolute right-2 top-8 text-gray-500 focus:outline-none" aria-label="Show password">
              <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
              <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.269-2.943-9.543-7a9.967 9.967 0 012.19-3.528M6.1 6.1L18 18m-1.875-1.875A3 3 0 1010 9"/></svg>
            </button>
          </div>

          <div class="flex items-center justify-between mb-6">
            <label class="inline-flex items-center text-sm">
              <input type="checkbox" name="remember" class="mr-2" /> Remember me
            </label>
            <a href="#" class="text-sm text-indigo-600">Forgot?</a>
          </div>

          <div>
            <button type="submit" class="w-full py-3 rounded-md bg-indigo-600 text-white font-medium shadow">Log In</button>
          </div>
        </form>

        <p class="text-xs text-gray-400 mt-6">© {{ date('Y') }} BAT . Crafted with ❤️ by BXS</p>
      </div>
    </div>

    <!-- Right column: image/background -->
    <div class="hidden lg:block lg:col-span-8 relative">
      <div class="absolute inset-0 bg-[url('https://images.unsplash.com/photo-1522075469751-3a6694fb2f61?auto=format&fit=crop&w=1400&q=80')] bg-cover bg-center"></div>
      <div class="absolute inset-0 bg-indigo-700/60 backdrop-blur-sm"></div>
      <div class="relative z-10 h-full flex items-center justify-center">
        <!-- Optional content over the image -->
        <div class="text-white text-center px-8 max-w-lg">
          <h3 class="text-3xl font-bold">Nice to see you again</h3>
          <p class="mt-4 text-indigo-100">Manage bookings, drivers and settings from the admin panel.</p>
        </div>
      </div>
    </div>
  </div>

  <script>
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
  </script>
</body>
</html>