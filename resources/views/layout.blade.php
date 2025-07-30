<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Laravel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css"
    integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ=="
    crossorigin="anonymous" referrerpolicy="no-referrer" />
  @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

  <header class="header">
    <div class="container">
      <div class="logo-container">
        <a href="{{ route('home') }}"><img src="{{ url('/images/Lucide-Inkt-Logo2.png') }}" alt=""></a>
      </div>
      <div class="desktop-navbar-container">
        <nav class="navbar">
          <x-navbar></x-navbar>
        </nav>
      </div>
      
      <div class="navbar-cart-sidebar-toggle">
        <li class="nav-item">
          <a class="{{ request()->routeIs('contact') ? 'active' : '' }}" href="#"><i
              class="fa-solid fa-cart-shopping"></i>
            @if(session('cart') && count(session('cart')))
            <span class="cart-quantity">
              {{
              collect(session('cart'))->sum('quantity')
              }}
            </span>
            @endif
          </a>
        </li>

        <div class="sidebar-toggle">
          <i class="fa-solid fa-bars"></i>
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