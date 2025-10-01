<x-layout>
    <main class="page home">

        <!-- ===== HERO + CLOCK (Blade) ===== -->
        <div class="video-bg-wrapper">
            <video autoplay loop muted playsinline class="bg-video">
                <source src="{{ asset('video/bg_final_video.mp4') }}" type="video/mp4">
            </video>

            <div class="bismillah">
                <img src="{{ url('/images/logo_new_logo.webp') }}" alt="">
            </div>


            <nav class="home-button-grid" role="navigation" aria-label="Hoofdmenu">
                <div class="inner-grid" role="group" aria-label="Linkerkolom">
                    <button class="btn left one focusable" tabindex="0">Home</button>
                    <button class="btn left two focusable" tabindex="0">Said Nursi</button>
                    <button class="btn left three focusable" tabindex="0">Contact</button>
                </div>
                <div class="inner-grid" role="group" aria-label="Rechterkolom">
                    <button class="btn right one focusable" tabindex="0">Risale-i Nur</button>
                    <button class="btn right two focusable" tabindex="0">Winkel</button>
                    <button class="btn right three focusable" tabindex="0">Account</button>
                </div>
            </nav>

            <section class="hero-section">

                <!-- Achterste laag: draaiende rotor -->
                <div class="layer layer-rotor">
                    <img class="rotating-image" src="{{ asset('images/inner-turning2.webp') }}" alt="">
                </div>

                <!-- Middenlaag: gradient / ornament achtergrond -->
                {{--            <div class="layer layer-bg">--}}
                {{--                <img class="grd-bg" src="{{ asset('images/grd_bg.webp') }}" alt="">--}}
                {{--            </div>--}}

                <div class="text-lucideinkt">
                    <img src="{{ asset('images/Bismillah_1.webp') }}" alt="">
                </div>

                <div class="text-life-minutes">
                    <img src="{{ asset('images/life_minutes.webp') }}" alt="">
                </div>

                <!-- Middenlaag: text inside clock -->
                <div class="text-clock">
                    <button class="clock-button" id="openModalBtn">Lees meer</button>
                </div>

                <!-- Bovenlaag: sier-ring -->
                <div class="layer layer-ring">
                    <img src="{{ asset('images/ring_clock_10.webp') }}" alt="">
                </div>

                <!-- Voorste laag: CSS klok -->
                <div class="layer layer-clock">
                    <div class="css-clock-wrapper">
                        <div class="css-clock">
                            <div class="css-hour-hand"></div>
                            <div class="css-minute-hand"></div>
                            <div class="css-second-hand"></div>
                            <div class="css-clock-center"></div>
                        </div>
                    </div>
                </div>

            </section>

        </div>

        <div class="section-wrapper">

            <section class="intro-section">

                <h2 class="title">Welkom bij Stichting Lucide Inkt</h2>
                <div class="sub-text">
                    <p>
                        Lucide Inkt is een non-profit organisatie toegewijd aan de vertaling en publicatie van de <em>Risale-i Nur</em>, in het Nederlands en Engels. Wij brengen deze betekenisvolle werken uit om geloofswaarheden helder en toegankelijk te maken.
                    </p>
                </div>

                <div class="grid">
                    <div class="card one">
                        <h3>De Risale-i Nur</h3>
                        <p>Een omvangrijke verzameling traktaten van Bediüzzaman Said Nursi, waarin geloof en rede samenkomen om de fundamenten van het geloof helder uiteen te zetten en richting te geven aan het dagelijks leven.</p>
                    </div>

                    <div class="card">
                        <h3>Vertalingen & Uitgaven</h3>
                        <p>Beschikbaar in zowel Nederlands als Engels, zorgvuldig vertaald en geredigeerd, met aandacht voor stijl en vormgeving zodat de essentie en diepgang van de boodschap behouden blijft voor iedere lezer.</p>
                    </div>

                    <div class="card">
                        <h3>Webshop</h3>
                        <p>Ontdek de beschikbare uitgaven in verschillende edities, bestel eenvoudig en veilig online, en lees geselecteerde delen direct digitaal waar dit mogelijk is, zodat je altijd toegang hebt tot de boodschap.</p>
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
                    <p>Waarlijk, dit voortrazende leven is een diepe slaap; het vloeit als een droom voorbij… Maar wees beraden, want de vluchtige minuten des levens dienen als zaden. Terwijl ze ogenschijnlijk verwelken en vergaan tijdens dit aardse bestaan, ontkiemen en floreren ze in het rijk der eeuwigheid. Afhankelijk van de wijze waarop ze verstrijken, zullen ze ofwel als duistere voortbrengselen van onachtzaamheid, ofwel als stralende vruchten van weldaden tot de mens wederkeren.</p>
                </div>
                <div class="book-image">
                    <img src="{{ url('/images/geloofswaarheden.png') }}" alt="">
                </div>
            </section>

            <!-- Modal Structure -->
            <div id="leesMeerModal" class="custom-modal">
                <div class="custom-modal-overlay"></div>
                <div class="custom-modal-content scroll-effect" id="scrollModalContent">
                    <span class="custom-modal-close" id="closeModalBtn">&times;</span>
                    <div class="scroll-inner">
                        <p>
                            Waarlijk, dit voortrazende leven is een diepe slaap; het vloeit als een droom voorbij… Maar wees beraden, want de vluchtige minuten des levens dienen als zaden. Terwijl ze ogenschijnlijk verwelken en vergaan tijdens dit aardse bestaan, ontkiemen en floreren ze in het rijk der eeuwigheid. Afhankelijk van de wijze waarop ze verstrijken, zullen ze ofwel als duistere voortbrengselen van onachtzaamheid, ofwel als stralende vruchten van weldaden tot de mens wederkeren.
                        </p>
                    </div>
                </div>
            </div>
       </div>


    </main>
</x-layout>
