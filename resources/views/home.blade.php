<x-layout>
    <main class="page home">

        {{-- <div class="logo-container mobile">
          <a href="{{ route('home') }}"><img src="{{ url('/images/Lucide-Inkt-Logo3.svg') }}" alt=""></a>
      </div>    --}}
      

        <div class="hero-section">

            <div class="hero-image">

                <div class="hero-image-background desktop"
                    style="background-image: url('{{ asset('images/clockassets/clock-faded-6.webp') }}');">

                    <div class="upper-text">
                        Stichting
                    </div>

                    <img src="{{ asset('images/clockassets/ClockAsset5.webp') }}" alt="Decorative Ring" class="ring-image">
                    <img src="{{ asset('images/clockassets/sier.webp') }}" alt="Hero Image"
                        class="rotating-image">
                    <img class="title" src="{{ asset('images/clockassets/clock-faded-5.webp') }}" alt="">
                    <img class="sub-title" src="{{ asset('images/clockassets/text-2.webp') }}" alt="">



                    <div class="css-clock-wrapper">
                        <div class="css-clock">
                            <div class="css-hour-hand"></div>
                            <div class="css-minute-hand"></div>
                            <div class="css-second-hand"></div>
                            <div class="css-clock-center"></div>
                        </div>
                    </div>
                </div>

                <style>
                    .hero-image-background {
                        position: relative;
                        z-index: 20;
                        background-size: 100%;
                        background-repeat: no-repeat;
                        background-position: center;
                        width: 95vw;
                        max-width: 650px;
                        min-width: 250px;
                        aspect-ratio: 1/1;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        border-radius: 50%;
                        overflow: visible;
                        /* Allow ring to overflow */
                        box-shadow:
                            0 8px 32px 0 rgba(98, 5, 5, 0.18),
                            0 2px 8px 0 rgba(171, 15, 20, 0.10),
                            0 1.5px 0.5px 0 rgba(0, 0, 0, 0.10);               
                           /* box-shadow: 0 8px 32px 0 rgba(98, 5, 5, 0.18),
                                       0 2px 16px 0 rgba(14, 30, 37, 0.18),
                                       0 1.5px 8px 0 rgba(171, 15, 20, 0.10),
                                       0 0 32px 8px rgba(255, 255, 255, 0.10); */
                       /* box-shadow: rgba(0, 0, 0, 0.09) 0px 3px 12px; */
                           margin-top: -50px;
                    }

                    .hero-image-background::before {
                        content: "";
                        position: absolute;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        border-radius: 50%;
                        box-sizing: border-box;
                        pointer-events: none;
                        z-index: 2;
                    }

                    .rotating-image {
                        max-width: 500px;
                        min-width: 250px;
                        width: 95vw;
                        position: relative;
                        z-index: 1;      
                        opacity: 0.2;                  
                    }

                    .ring-image {
                        max-width: 700px;
                        min-width: 300px;
                        width: 110vw;
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        transform: translate(-50%, -50%);
                        z-index: 0;
                        pointer-events: none;
                    }


                    .css-clock-wrapper {
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        transform: translate(-50%, -50%);
                        z-index: 20;
                        pointer-events: none;
                        width: 180px;
                        height: 180px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    .css-clock {
                        position: relative;
                        width: 180px;
                        height: 180px;
                        background: transparent;
                        border-radius: 50%;
                    }

                    .css-hour-hand,
                    .css-minute-hand,
                    .css-second-hand {
                        position: absolute;
                        left: 50%;
                        bottom: 50%;
                        transform-origin: 50% 100%;
                        background: linear-gradient(0deg, #300d0e 0%, #712022 100%);
                        border-radius: 0 0 6px 6px;
                        clip-path: polygon(48% 100%, 52% 100%, 54% 20%, 50% 0, 46% 20%);
                        box-shadow: 0 4px 12px 0 rgba(0, 0, 0, 0.18), 0 1.5px 4px 0 rgba(86, 32, 28, 0.12);
                    }

                    .css-hour-hand {
                        width: 50px;
                        height: 170px;
                        z-index: 3;
                        clip-path: polygon(38% 100%, 62% 100%, 52% 10%, 48% 10%);
                    }

                    .css-minute-hand {
                        width: 50px;
                        height: 210px;
                        z-index: 2;
                        clip-path: polygon(40% 100%, 60% 100%, 52% 10%, 48% 10%);
                    }

                    .css-second-hand {
                        width: 20px;
                        height: 270px;
                        z-index: 4;
                        clip-path: polygon(42% 100%, 58% 100%, 53% 10%, 47% 10%);
                    }

                    .css-clock-center {
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        width: 26px;
                        height: 26px;
                        background: linear-gradient(0deg, #2f0d0d 0%, #712022 100%);
                        border-radius: 50%;
                        transform: translate(-50%, -50%);
                        z-index: 10;
                    }

                    @media screen and (max-width: 1200px) {

                        .css-hour-hand {
                            height: 180px;
                        }

                        .css-second-hand {
                            height: 270px;
                        }

                        .css-minute-hand {
                            height: 210px;
                        }

                    }

                    @media screen and (max-width: 1200px) {

                        .css-hour-hand {
                            height: 100px;
                        }

                        .css-second-hand {
                            height: 160px;
                        }

                        .css-minute-hand {
                            height: 120px;
                        }

                        .css-clock-center {
                        position: absolute;
                        left: 50%;
                        top: 50%;
                        width: 20px;
                        height: 20px;
                        background: linear-gradient(0deg, #2f0d0d 0%, #712022 100%);
                        border-radius: 50%;
                        transform: translate(-50%, -50%);
                        z-index: 10;
                    }

                    }
                </style>
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Configurable speed (degrees per second) and direction (1=clockwise, -1=counterclockwise) for each hand
                        const config = {
                            hour: {
                                speed: 500,
                                direction: 1
                            }, // 1x real speed, clockwise
                            minute: {
                                speed: 100,
                                direction: 1
                            }, // 1x real speed, clockwise
                            second: {
                                speed: 7,
                                direction: 1
                            } // 1x real speed, clockwise
                        };

                        // Internal state for virtual time
                        let base = new Date();
                        let last = Date.now();
                        let hourAngle = ((base.getHours() % 12) + base.getMinutes() / 60 + base.getSeconds() / 3600) * 30;
                        let minuteAngle = (base.getMinutes() + base.getSeconds() / 60 + base.getMilliseconds() / 60000) * 6;
                        let secondAngle = (base.getSeconds() + base.getMilliseconds() / 1000) * 6;

                        function animateHands() {
                            const now = Date.now();
                            const delta = (now - last) / 1000; // seconds since last frame
                            last = now;

                            // Advance each hand by its own speed and direction
                            hourAngle += config.hour.speed * config.hour.direction * (360 / 43200) * delta; // 12h = 43200s
                            minuteAngle += config.minute.speed * config.minute.direction * (360 / 3600) * delta; // 1h = 3600s
                            secondAngle += config.second.speed * config.second.direction * (360 / 60) * delta; // 1m = 60s

                            // Normalize angles
                            hourAngle = ((hourAngle % 360) + 360) % 360;
                            minuteAngle = ((minuteAngle % 360) + 360) % 360;
                            secondAngle = ((secondAngle % 360) + 360) % 360;

                            document.querySelector('.css-hour-hand').style.transform =
                                `translate(-50%, 0) rotate(${hourAngle}deg)`;
                            document.querySelector('.css-minute-hand').style.transform =
                                `translate(-50%, 0) rotate(${minuteAngle}deg)`;
                            document.querySelector('.css-second-hand').style.transform =
                                `translate(-50%, 0) rotate(${secondAngle}deg)`;
                            requestAnimationFrame(animateHands);
                        }
                        animateHands();

                        // Example: to change speed/direction dynamically, you can do:
                        // config.hour.speed = 2; // 2x speed
                        // config.minute.direction = -1; // counterclockwise
                    });
                </script>
                </script>

            </div>

         </div>

            <div class="hero-text">
                <div>
                    <img class="hero-text-image" src="{{ asset('images/bismillah.png') }}" alt="">
                    <h1 class="main-heading">Het doel van de Risale-i Nur is het redden van het geloof.</h1>
                    <p class="sub-text">Lucide Inkt is een non-profit organisatie die zich ter bevordering van het
                        persoonlijke evenals het maatschappelijke welzijn richt op de verspreiding van geloofswaarheden die
                        omschreven zijn in de boekenreeks van de Risale-i Nur.</p>

                    <div class="btn-box">
                        <a href="#"><button class="btn">Lees de vertalingen</button></a>
                        <a href="{{ route('shop') }}"><button class="btn outlined">Naar de winkel</button></a>
                    </div>

                </div>
            </div>




        </div>
    </main>
</x-layout>
