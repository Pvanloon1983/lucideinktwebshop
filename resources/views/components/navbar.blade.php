<ul>
    <li class="nav-item">
        <a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
    </li>
    <li class="nav-item">
        <a class="{{ request()->routeIs('risale') ? 'active' : '' }}" href="{{ route('risale') }}">Risale-i Nur</a>
    </li>
    <li class="nav-item">
        <a class="{{ request()->routeIs('saidnursi') ? 'active' : '' }}"  href="{{ route("saidnursi")  }}">Said Nursi</a>
    </li>
    <li class="nav-item">
        <a class="{{ request()->routeIs('shop') ? 'active' : '' }}" href="{{ route('shop') }}">Winkel</a>
    </li>
    <li class="nav-item">
        <a class="{{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route("contact") }}">Contact</a>
    </li>

    @guest
    <li class="nav-item">
        <a class="{{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route("login") }}">Account</a>
    </li>
    @endguest

    <li class="nav-item cart">
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

    @auth
    <li class="nav-item" style="margin-right: 20px;">
        <a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route("dashboard") }}"></i> Dashboard</a>
    </li>
    @endauth
</ul>

