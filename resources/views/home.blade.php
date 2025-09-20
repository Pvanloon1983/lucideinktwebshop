<x-layout>
    <main class="page home">

        {{-- <div class="logo-container mobile">
          <a href="{{ route('home') }}"><img src="{{ url('/images/Lucide-Inkt-Logo3.svg') }}" alt=""></a>
      </div>    --}}


        <div class="hero-section">

            {{--                <div class="logo-container desktop">--}}
            {{--                    <a href="{{ route('home') }}"><img src="{{ url('/images/Lucide-Inkt-Logo3.svg') }}" alt=""></a>--}}
            {{--                </div>--}}

            <div class="hero-image">

                <div class="hero-image-background desktop"
                     style="background-image: url('{{ asset('images/clockassets/clock-faded-6.webp') }}');">

                    <div class="upper-text">
                        Stichting
                    </div>

                    {{--                    <img class="logo-in-clock" src="{{ asset('images/Lucide-Inkt-Logo3.svg') }}" alt="">--}}

                    <img class="ring-image" src="{{ asset('images/wittehtergrond.webp') }}" alt="Decorative Ring">

                    <img class="rotating-image" src="{{ asset('images/clockassets/inner-turning2.webp') }}" alt="">

                    <img class="title" src="{{ asset('images/clockassets/clock-faded-5.webp') }}" alt="">
                    <img class="sub-title" src="{{ asset('images/clockassets/Text-2.webp') }}" alt="">

                    <a href="{{ route('home') }}"><img src="{{ url('/images/Lucide-Inkt-Logo3.svg') }}" alt=""></a>


                    <div class="css-clock-wrapper">
                        <div class="css-clock">
                            <div class="css-hour-hand"></div>
                            <div class="css-minute-hand"></div>
                            <div class="css-second-hand"></div>
                            <div class="css-clock-center"></div>
                        </div>
                    </div>
                </div>

            </div>

        </div>

        <div style="display: none;" class="hero-text">
            <div>
                <img class="hero-text-image" src="{{ asset('images/bismillah.png') }}" alt="">
                <h1 class="main-heading">Het doel van de Risale-i Nur is het redden van het geloof.</h1>
                <p class="sub-text">Lucide Inkt is een non-profit organisatie die zich ter bevordering van het
                    persoonlijke evenals het maatschappelijke welzijn richt op de verspreiding van geloofswaarheden die
                    omschreven zijn in de boekenreeks van de Risale-i Nur.</p>

                <div class="btn-box">
                    <a href="#">
                        <button class="btn">Lees de vertalingen</button>
                    </a>
                    <a href="{{ route('shop') }}">
                        <button class="btn outlined">Naar de winkel</button>
                    </a>
                </div>

            </div>
        </div>



    </main>
</x-layout>
