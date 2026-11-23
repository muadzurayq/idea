<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>idea</title>
</head>
<body class="bg-background text-foreground">

    <x-layout.nav />

    <main>
         {{$slot}}
    </main>

    @session('success')
        <div

            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition.opacity.duration.100ms
            class="bg-primary px-4 absolute bottom-4 right-4 rounded-lg"
        >
            {{$value}}
        </div>
    @endsession

</body>
</html>
