<x-layout>
    <main class="page home container">
        <div class="hero-section">
            <div class="hero-image">
                <div class="hero-image-background" style="background-image: url('{{ asset('images/clockassets/ClockAsset1.webp') }}');">
                    <img src="{{ asset('images/clockassets/ClockAsset5.webp') }}" alt="Decorative Ring" class="ring-image">
                    <img src="{{ asset('images/clockassets/ClockAsset4.webp') }}" alt="Hero Image" class="rotating-image">
                    <img class="title" src="{{ asset('images/clockassets/ClockAsset2.webp') }}" alt="">
                    <img class="sub-title" src="{{ asset('images/clockassets/ClockAsset3.webp') }}" alt="">
                </div>
            </div>
            <div class="hero-text">
                <div>
                    <img class="hero-text-image" src="{{ asset('images/bismillah.png') }}" alt="">
                    <h1 class="main-heading">Het doel van de Risale-i Nur is het redden van het geloof.</h1>
                    <p class="sub-text">Lucide Inkt is een non-profit organisatie die zich ter bevordering van het persoonlijke evenals het maatschappelijke welzijn richt op de verspreiding van geloofswaarheden die omschreven zijn in de boekenreeks van de Risale-i Nur.</p>
                    <div class="btn-box">
                        <a href="#"><button class="btn">Lees de vertalingen!</button></a>
                        <a href="{{ route('shop') }}"><button class="btn outlined">Naar de winkel</button></a>
                    </div>
                </div>
            </div>
        </div>
    </main>
</x-layout>
