<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Chat</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<nav class="bg-blue-700  py-3">
    <div class="ml-4 flex items-center md:ml-6">
            <x-nav-link href="/">Home</x-nav-link>
            <x-nav-link href="/chat">Chat</x-nav-link>
            @guest()
                <x-nav-link href="/login" class="btn-main">Log In</x-nav-link>
                <x-nav-link href="/register" class="btn-main">Register</x-nav-link>
            @endguest

            <div class="justify-end">
                @auth()
                    <form method="POST" action="/logout">
                        @csrf

                        <x-form-button>Log Out</x-form-button>
                    </form>
                @endauth
            </div>

    </div>
</nav>

    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8 sm:flex sm:justify-between">
            <h1 class="text-3xl font-bol  d tracking-tight text-gray-900">{{$heading}}</h1>

        </div>
    </header>
    <main>
        <div class="mx-auto max-w-7xl py-6 sm:px-6 lg:px-8">
            {{$slot}}
        </div>
    </main>
</body>
</html>
