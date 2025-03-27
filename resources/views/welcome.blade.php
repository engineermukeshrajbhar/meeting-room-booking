<!-- resources/views/welcome.blade.php -->
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Meeting Room Booking</title>
    
    <!-- Load CSS and JS through Vite -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Fallback for when Vite is not running -->
    @production
        <link href="{{ asset('build/assets/app.css') }}" rel="stylesheet">
        <script src="{{ asset('build/assets/app.js') }}" defer></script>
    @endproduction
</head>
<body>
    <div id="app"></div>
</body>
</html>