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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

  <script src="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.12/dist/js/splide.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@splidejs/splide@3.6.12/dist/css/splide.min.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/css/lightbox.min.css" integrity="sha512-xtV3HfYNbQXS/1R1jP53KbFcU9WXiSA1RFKzl5hRlJgdOJm4OxHCWYpskm6lN0xp0XtKGpAfVShpbvlFH3MDAA==" crossorigin="anonymous" referrerpolicy="no-referrer" /><script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.5/js/lightbox.min.js" integrity="sha512-KbRFbjA5bwNan6DvPl1ODUolvTTZ/vckssnFhka5cG80JVa5zSlRPCr055xSgU/q6oMIGhZWLhcbgIC0fyw3RQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
  @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>

<body style="position: relative;">
    <div
        style="position: fixed; inset: 0; z-index: 40; background-image: url('{{ asset('images/sand-texture-min.webp') }}'); background-size: cover; background-position: center; opacity: 0.1; pointer-events: none;">
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

{{--                  <div class="logo-container desktop">--}}
{{--                      <a href="{{ route('home') }}"><img src="{{ url('/images/LogoLight.webp') }}" alt=""></a>--}}
{{--                  </div>--}}

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

<script>
    let splide = new Splide( '#main-slider', {
        pagination: false,
    } );

    let thumbnails = document.getElementsByClassName( 'thumbnail' );
    let current;

    for ( let i = 0; i < thumbnails.length; i++ ) {
        initThumbnail( thumbnails[ i ], i );
    }

    function initThumbnail( thumbnail, index ) {
        thumbnail.addEventListener( 'click', function () {
            splide.go( index );
        } );
    }

    splide.on( 'mounted move', function () {
        let thumbnail = thumbnails[ splide.index ];

        if ( thumbnail ) {
            if ( current ) {
                current.classList.remove( 'is-active' );
            }

            thumbnail.classList.add( 'is-active' );
            current = thumbnail;
        }
    } );

    splide.mount();

    lightbox.option({
        'albumLabel': "%1 / %2",
        'alwaysShowNavOnTouchDevices': true
    })
</script>

</html>
