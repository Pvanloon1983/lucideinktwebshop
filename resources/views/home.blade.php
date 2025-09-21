<x-layout>
    <main class="page home">

        {{-- <div class="logo-container mobile">
          <a href="{{ route('home') }}"><img src="{{ url('/images/Lucide-Inkt-Logo3.svg') }}" alt=""></a>
      </div>    --}}


        <section class="hero-section">

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

        </section>

        <div class="section-wrapper">

            <section class="intro-section">

                <div class="bismillah">
                    <img src="{{ url('/images/bismillah_final.webp') }}" alt="">
                </div>

                <h2 class="title">Welkom bij Stichting Lucide Inkt</h2>
                <div class="sub-text">
                    <p>
                        Lucide Inkt is een non-profit organisatie toegewijd aan de vertaling en publicatie van de <em>Risale-i Nur</em>, in het Nederlands en Engels. Wij brengen deze betekenisvolle werken uit om geloofswaarheden helder en toegankelijk te maken.
                    </p>
                </div>

                <div class="grid">
                    <div class="card one">
                        <h3>De Risale-i Nur</h3>
                        <p>Een verzameling van traktaten geschreven door Bediüzzaman Said Nursi waarin geloof en rede samenkomen om geloofsfundamenten te verduidelijken.</p>
                    </div>

                    <div class="card">
                        <h3>Vertalingen & Uitgaven</h3>
                        <p>Beschikbaar in het Nederlands en Engels. Elke uitgave is met zorg vertaald, geredigeerd en vormgegeven.</p>
                    </div>

                    <div class="card">
                        <h3>Webshop</h3>
                        <p>Kies je uitgave en bestel direct. Mogelijkheid om online te lezen waar beschikbaar.</p>
                    </div>
                </div>
            </section>

            <section class="quote-section">
                <p class="text">Er is geen Schepper en geen Onderhouder behalve
                    Hij. Voor- en tegenspoed rusten in Zijn Handen. Bovendien is Hij Alwijs; Hij
                    vermijdt futiliteit. Ook is Hij Genadig; Zijn Goedgunstigheid en Zijn Erbarmen
                    zijn omvangrijk.</p>
                <p class="sub-text"><em>- Risale-i Nur</em></p>
            </section>

            <section class="book-presentation">
                <div class="text">
                    <h2 class="title">Geloofswaarheden</h2>
                  <p>In dit boek worden uiteenlopende geloofskwesties helder en zorgvuldig behandeld. Het verduidelijkt de waarheden achter kernpunten van het islamitische geloof, zoals de diepe wijsheid in de vijf dagelijkse gebedstijden en de betekenis en beproeving achter de schepping van de duivel. Met heldere redeneringen, voorbeelden uit het dagelijks leven en verwijzingen naar klassieke bronnen helpt het boek hardnekkige misvattingen te corrigeren en twijfel te verminderen. Controversiële onderwerpen worden stap voor stap ontrafeld, zodat de lezer niet alleen begrijpt wát de leer stelt, maar vooral waarom. Daardoor biedt het zowel de zoekende lezer als degene die verdieping wil een betrouwbare gids: een uitnodiging tot reflectie, een versterking van geloof en vertrouwen, en een praktische handreiking om overtuigingen met rust en inzicht te beleven.</p>
                </div>
                <div class="book-image">
                    <img src="{{ url('/images/geloofswaarheden.png') }}" alt="">
                </div>
            </section>

        </div>


    </main>
</x-layout>
