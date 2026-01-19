<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'Admin') - AirportServices</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50 text-gray-800">
  <header class="bg-white shadow">
    <div class="container mx-auto px-4 py-4">
      <a href="/" class="font-semibold">AirportServices</a>
    </div>
  </header>

  <main class="container mx-auto px-4 py-6">
    @yield('content')
  </main>

  <footer class="border-t mt-10 py-4 text-center text-sm text-gray-500">
    &copy; {{ date('Y') }} AirportServices
  </footer>
</body>
</html>