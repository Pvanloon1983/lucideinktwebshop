<x-layout>
    <main class="page home">

        <!-- ===== HERO + CLOCK (Blade) ===== -->
        <div class="video-bg-wrapper"
             style="background-image: url('{{ url('/images/bg_background_website.png') }}'); background-repeat: no-repeat; background-size: cover; background-position: center center;">

            <section class="home-hero">

                <div class="book-image">
                    <img class="book-shot" src="{{ asset('images/banner_hero.png') }}" alt="">
                </div>

                <div class="clock-image" >
                    <div class="hero-section">

                        <!-- Achterste laag: draaiende rotor -->
                        <div class="layer layer-rotor">
                            <img class="rotating-image" src="{{ asset('images/inner-turning2.webp') }}" alt="">
                        </div>

                        <!-- Middenlaag: text inside clock -->
                        <div class="text-clock">
                            <div class="text-lucideinkt">
                                <img src="{{ asset('images/Bismillah5.webp') }}" alt="">
                            </div>
                            <div class="text-life-minutes">
                                <img src="{{ asset('images/clockassets/Text-2.webp') }}" alt="">
                            </div>
                            <button class="clock-button" id="openModalBtn">Lees meer</button>
                        </div>

                        <!-- Bovenlaag: sier-ring -->
                        <div class="layer layer-ring">
                            <img src="{{ asset('images/clockassets/clockeffect_2.png') }}" alt="">
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
                    <p>In dit waardevolle werk schuilt een verheven zegenrijkheid en een grenzeloze voortreffelijkheid die nooit eerder in een dergelijke mate zijn geconstateerd. En het is gebleken dat dit werk de zegeningen van het Goddelijke Licht, de Zon van leiding en de Schittering van gelukzaligheid alias de Heilige Qur’an als geen enkel ander werk heeft geërfd. Aldus is het niet meer dan evident dat de essentie van dit werk uit het Pure Licht van de Qur’an bestaat, dat dit werk meer zegeningen uit de Mohammedaanse Lichten dan de werken van heiligen draagt, dat het aandeel, de betrokkenheid en de heilige inbreng van de onberispelijke profeet bij dit werk meer dan bij de werken van heiligen voorkomen, en dat de begiftigde en de vertolker van dit werk een spirituele persoonlijkheid bezit met gaven en volmaaktheden die naar dezelfde verhouding verheven en onvergelijkelijk zijn; dit is een waarheid die zo helder is als de zon.</p>
                </div>
                <div class="book-image">
                    <img src="{{ url('/images/IMG_9082_modified_png_shadow.png') }}" alt="">
                </div>
            </section>

            <!-- Modal Structure -->
            <div id="leesMeerModal" class="custom-modal">
                <div class="custom-modal-overlay"></div>
                <div class="custom-modal-content scroll-effect" id="scrollModalContent">
                    <span class="custom-modal-close" id="closeModalBtn">&times;</span>
                    <div class="scroll-inner">
                        <p>
                            Waarlijk, dit voortrazende leven is een diepe slaap; het vloeit als een droom voorbij… Maar wees beraden, want de vluchtige minuten des levens dienen als zaden. Terwijl ze ogenschijnlijk verwelken en vergaan tijdens dit aardse bestaan, ontkiemen en floreren ze in het rijk der eeuwigheid. Afhankelijk van de wijze waarop ze verstrijken, zullen ze ofwel als duistere voortbrengselen van onachtzaamheid, ofwel als stralende vruchten van weldaden tot de mens wederkeren.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </main>
</x-layout>
