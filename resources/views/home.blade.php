<x-layout>
    <main class="page home">

        {{-- <div class="logo-container mobile">
          <a href="{{ route('home') }}"><img src="{{ url('/images/Lucide-Inkt-Logo3.svg') }}" alt=""></a>
      </div>    --}}


        <div class="hero-section">

            {{--                <div class="logo-container desktop">--}}
            {{--                    <a href="{{ route('home') }}"><img src="{{ url('/images/Lucide-Inkt-Logo3.svg') }}" alt=""></a>--}}
            {{--                </div>--}}

            <div class="hero-image hero-wrap">

                <div class="hero-image-background desktop"
                     style="background-image: url('{{ asset('images/clockassets/clock-faded-6.webp') }}');">

                    <div class="upper-text">
                        Stichting
                    </div>

                    {{--                    <img class="logo-in-clock" src="{{ asset('images/Lucide-Inkt-Logo3.svg') }}" alt="">--}}

                    <img class="ring-image" src="{{ asset('images/clockassets/clock-ring-15.webp') }}" alt="Decorative Ring">

                    <img class="ring-image-mobile" src="{{ asset('images/clockassets/MobielBackGround.webp') }}" alt="Decorative Ring">

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


        <section class="li-wrap li-section li-intro" aria-labelledby="welkom-title">
            <h2 id="welkom-title">Welkom bij Stichting Lucide Inkt</h2>
            <p>
                Lucide Inkt is een non-profit organisatie toegewijd aan de vertaling en publicatie van de <em>Risale-i Nur</em>, in het Nederlands en Engels. Wij brengen deze betekenisvolle werken uit om geloofswaarheden helder en toegankelijk te maken.
            </p>
            <div class="li-divider" aria-hidden="true"></div>

            <!-- De Risale-i Nur & vertalingen -->
            <div class="li-grid-3" role="list">
                <article class="li-card" role="listitem">
                    <h3>De Risale-i Nur</h3>
                    <p>Een verzameling van traktaten geschreven door Bediüzzaman Said Nursi waarin geloof en rede samenkomen om geloofsfundamenten te verduidelijken.</p>
                    <a class="li-btn li-btn--primary" href="/risale-i-nur">Meer over Risale-i Nur</a>
                </article>

                <article class="li-card" role="listitem">
                    <h3>Vertalingen & Uitgaven</h3>
                    <p>Beschikbaar in het Nederlands en Engels. Elke uitgave is met zorg vertaald, geredigeerd en vormgegeven.</p>
                    <a class="li-btn" href="/boeken">Bekijk onze boeken</a>
                </article>

                <article class="li-card" role="listitem">
                    <h3>Webshop</h3>
                    <p>Kies je uitgave en bestel direct. Mogelijkheid om online te lezen waar beschikbaar.</p>
                    <a class="li-btn" href="/winkel">Ga naar winkel</a>
                </article>
            </div>
        </section>


    </main>
</x-layout>
