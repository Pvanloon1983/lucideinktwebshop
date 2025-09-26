<x-layout>
    <main class="page home">

        <!-- ===== HERO + CLOCK (Blade) ===== -->
        <section class="hero-section">
            <!-- Achterste laag: draaiende rotor -->
            <div class="layer layer-rotor">
                <img class="rotating-image" src="{{ asset('images/clockassets/inner-turning2.webp') }}" alt="">
            </div>

            <!-- Middenlaag: gradient / ornament achtergrond -->
            <div class="layer layer-bg">
                <img class="grd-bg" src="{{ asset('images/grd_bg.webp') }}" alt="">
            </div>

            <!-- Bovenlaag: sier-ring -->
            <div class="layer layer-ring">
                <img src="{{ asset('images/ring_85.webp') }}" alt="">
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

        <div class="section-wrapper">

            <section class="intro-section">

                <div class="bismillah">
                    <img src="{{ url('/images/bismillah_red.webp') }}" alt="">
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
