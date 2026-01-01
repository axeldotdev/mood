<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ $title ?? config('app.name') }}</title>
<meta name="description" content="{{ $description ?? '' }}">
<link rel="canonical" href="{{ $canonical ?? url()->current() }}"/>

<link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96"/>
<link rel="icon" type="image/svg+xml" href="/favicon.svg"/>
<link rel="shortcut icon" href="/favicon.ico"/>
<link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png"/>
<meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}"/>
<link rel="manifest" href="/site.webmanifest"/>
<meta name="theme-color" content="#ffffff">

<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=fira-code:400,500,600,700" rel="stylesheet" />

@vite(['resources/css/app.css', 'resources/js/app.js'])

@if (config('services.fathom.siteId'))
    <script src="https://cdn.usefathom.com/script.js" data-site="{{ config('services.fathom.siteId') }}" defer></script>
@endif
