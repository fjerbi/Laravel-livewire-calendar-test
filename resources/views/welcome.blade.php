<!-- main.blade.php (or the file where you're using Livewire) -->

<html>
<head>
    <!-- Other head elements -->
    @vite('resources/css/app.css')
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    @livewireStyles
</head>
<body>
    <!-- Body content -->

    <!-- Your Livewire component -->
    @livewire('calendar-component')

    <!-- Other scripts and content -->

    @livewireScripts
</body>
</html>
