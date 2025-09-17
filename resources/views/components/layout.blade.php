<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Laravel</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
        integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
        
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/vue@3.4"></script>
  <script src="https://cdn.jsdelivr.net/npm/@myparcel/delivery-options@6/dist/myparcel.lib.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@myparcel/delivery-options@6/dist/style.css" />
  @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body style="position: relative;">
    <div
        style="position: fixed; inset: 0; z-index: 0; background-image: url('{{ asset('images/sand-texture-min.webp') }}'); background-size: cover; background-position: center; opacity: 0.1; pointer-events: none;">
    </div>

    <div class="logo-container desktop">
        <a href="{{ route('home') }}"><img src="{{ url('/images/Lucide-Inkt-Logo3.svg') }}" alt=""></a>
    </div>
    <header class="header">
        <div class="header-box">

         <div class="navbar-cart-sidebar-toggle">
            <li class="nav-item">
                    <a href="{{ route('cartPage') }}"><i
                            class="fa-solid fa-cart-shopping"></i>
                        @if (session('cart') && count(session('cart')))
                            <span class="cart-quantity">
                                {{ collect(session('cart'))->sum('quantity') }}
                            </span>
                        @endif
                    </a>
                </li>
                </div>

   
              <div class="desktop-navbar-container">
                <nav class="navbar">
                    <x-navbar></x-navbar>
                </nav>
            </div>

            <div class="navbar-cart-sidebar-toggle">



                <div class="sidebar-toggle">
                    <i class="fa-solid fa-bars"></i>
                </div>
            </div>

        </div>

        </div>
    </header>

    <div class="sidebar">
        <div class="close-toggle">
            <i class="fa-solid fa-xmark"></i>
        </div>
        <nav class="navbar">
            <x-navbar></x-navbar>
        </nav>
    </div>

    {{ $slot }}
</body>



</html>
