<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Laravel</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css" integrity="sha512-DxV+EoADOkOygM4IR9yXP8Sb2qwgidEmeqAEmDKIOfPRQZOWbXCzLC6vjbZyy0vPisbH2SyW27+ddLVCN+OMzQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
  <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@myparcel/delivery-options@6.3.1/dist/style.css" />
  <script src="https://cdn.jsdelivr.net/npm/@myparcel/delivery-options@6.3.1/dist/myparcel.js"></script>
   @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body style="position: relative">
  <div
    style="position: fixed; inset: 0; z-index: 0; background-image: url('{{ asset('images/sand-texture-min.webp') }}'); background-size: cover; background-position: center; opacity: 0.1; pointer-events: none;">
</div>

<header class="header dashboard">
    <div class="container">
      <div class="admin-sidebar-toggle">
          <i class="fa-solid fa-list"></i>
      </div>
      <div class="desktop-navbar-container">
        <nav class="navbar">
          <x-navbar></x-navbar>
        </nav>
      </div>

      <div class="navbar-cart-sidebar-toggle">
        <li class="nav-item">
          <a class="{{ request()->routeIs('cartPage') ? 'active' : '' }}" href="{{ route('cartPage') }}"><i
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

<div class="sidebar admin-panel">
  <div class="close-toggle">
    <i class="fa-solid fa-xmark"></i>
  </div>
  <nav class="navbar">
    <ul>

      @anyrole('admin','user') 
      <a href="{{ route('dashboard') }}">
        <li class="nav-item {{ request()->routeIs('dashboard') ? 'active-admin-link' : '' }}"><span class="{{ request()->routeIs('dashboard') ? 'active-admin-link' : '' }}">Dashboard</span></li>  
      </a>      
      <a href="{{ route('editProfile') }}">
        <li class="nav-item {{ request()->routeIs('editProfile') ? 'active-admin-link' : '' }}"><span class="{{ request()->routeIs('editProfile') ? 'active-admin-link' : '' }}">Mijn Profiel</span></li>
      </a>
      @endanyrole

      @role('admin')
      <a href="{{ route('productIndex') }}">
        <li class="nav-item {{ request()->routeIs('productIndex') || request()->routeIs('productCreatePage') || request()->routeIs('productEditPage') ? 'active-admin-link' : '' }}">
        <span class="{{ request()->routeIs('productIndex') || request()->routeIs('productCreatePage') || request()->routeIs('productEditPage') ? 'active-admin-link' : '' }}">Producten</span>
        </li>  
      </a>
      @endrole

      @role('admin')
      <a href="{{ route('productCategoryIndex') }}">
        <li class="nav-item {{ request()->routeIs('productCategoryIndex') || request()->routeIs('productCategoryCreatePage') || request()->routeIs('productCategoryEditPage') ? 'active-admin-link' : '' }}">
        <span class="{{ request()->routeIs('productCategoryIndex') ? 'active-admin-link' : '' }}">Productcategorieën</span>
        </li>  
      </a>
      @endrole  

      @role('admin')
      <a href="{{ route('orderIndex') }}">
        <li class="nav-item {{ request()->routeIs('orderIndex') || request()->routeIs('orderShow') || request()->routeIs('orderCreatePage') ? 'active-admin-link' : '' }}">
        <span class="{{ request()->routeIs('orderIndex') || request()->routeIs('orderShow') || request()->routeIs('orderCreatePage') ? 'active-admin-link' : '' }}">Bestellingen</span>
        </li>  
      </a>
      @endrole

      @role('admin')
      <a href="{{ route('discountIndex') }}">
        <li class="nav-item {{ request()->routeIs('discountIndex') || request()->routeIs('discountEdit') ? 'active-admin-link' : '' }}">
          <span class="{{ request()->routeIs('discountIndex') || request()->routeIs('discountEdit') ? 'active-admin-link' : '' }}">Kortingscodes</span>
        </li>
      </a>
      @endrole

      @anyrole('admin','user') 
      <a href="{{ route('showMyOrders') }}">
        <li class="nav-item {{ request()->routeIs('showMyOrders') || request()->routeIs('showMyOrder') ? 'active-admin-link' : '' }}">
        <span class="{{ request()->routeIs('showMyOrders') || request()->routeIs('showMyOrder') ? 'active-admin-link' : '' }}">Mijn bestellingen</span>
        </li>  
      </a>
      @endanyrole

      @role('admin')
      <a href="{{ route('customerIndex') }}">
        <li class="nav-item {{ request()->routeIs('customerIndex') || request()->routeIs('customerShow') ? 'active-admin-link' : '' }}">
        <span class="{{ request()->routeIs('customerIndex') || request()->routeIs('customerShow') ? 'active-admin-link' : '' }}">Klanten</span>
        </li>  
      </a>
      @endrole

      @role('admin')
      <a href="{{ route('userIndex') }}">
        <li class="nav-item {{ request()->routeIs('userIndex') || request()->routeIs('userShow') || request()->routeIs('userCreate') ? 'active-admin-link' : '' }}">
          <span class="{{ request()->routeIs('userIndex') || request()->routeIs('userShow') || request()->routeIs('userCreate') ? 'active-admin-link' : '' }}">Gebruikers</span>
        </li>
      </a>
      @endrole

          @auth
      <li class="nav-item">
          <form class="logout-button" action="{{ route('logout') }}" method="POST">
              @csrf
              <button type="submit" class="btn btn-primary">
                  <span class="loader"></span>Uitloggen
              </button>
          </form>
      </li>
      @endauth
    </ul>   
  </nav>
</div>

{{ $slot  }}
</body>
</html>
