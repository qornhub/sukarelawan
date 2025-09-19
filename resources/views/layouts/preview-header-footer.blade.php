<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Header & Footer Preview</title>
    <link rel="stylesheet" href="{{ asset('css/your-style.css') }}"> {{-- Replace with actual CSS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    {{-- Header --}}
    @include('layouts.volunteer_header')

    <main class="container mt-5 mb-5 text-center">
        <h2>This is a test page to preview Header and Footer</h2>
    </main>

    {{-- Footer --}}
    @include('layouts.volunteer_footer')

</body>
</html>
