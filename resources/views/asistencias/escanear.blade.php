<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Escanear QR</title>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.tailwindcss.com"></script>


    <style>

        /*
        |--------------------------------------------------------------------------
        | READER
        |--------------------------------------------------------------------------
        */

        #reader {
            border: none !important;
            background: transparent !important;
        }


        #reader button {
            background: #f97316 !important;
            color: white !important;

            border: none !important;
            border-radius: 14px !important;

            padding: 14px 20px !important;

            font-weight: 800 !important;
            font-size: 16px !important;

            cursor: pointer !important;

            box-shadow:
                0 8px 18px rgba(249, 115, 22, 0.25);

            transition:
                transform .16s ease,
                background-color .16s ease,
                box-shadow .16s ease !important;
        }


        #reader button:hover {
            background: #ea580c !important;

            transform: translateY(-2px);

            box-shadow:
                0 12px 24px rgba(249, 115, 22, 0.32);
        }


        #reader button:active {
            transform: scale(.96);
        }


        #reader a,
        #reader__dashboard_section_swaplink {
            display: none !important;
        }


        #reader__dashboard_section_csr button {
            font-size: 0 !important;
        }


        #reader__dashboard_section_csr button::after {
            content: "📷 Activar cámara";
            font-size: 18px !important;
        }


        /*
        |--------------------------------------------------------------------------
        | ZONA DE ESCANEO
        |--------------------------------------------------------------------------
        */

        #reader__scan_region {
            background: white !important;

            border-radius: 20px !important;

            padding: 14px !important;

            overflow: hidden;

            box-shadow:
                0 10px 24px rgba(0, 0, 0, .25);
        }


        #reader__scan_region video {
            border-radius: 16px !important;
        }


        #reader__dashboard_section {
            padding: 16px !important;
        }


        /*
        |--------------------------------------------------------------------------
        | CURSORES
        |--------------------------------------------------------------------------
        */

        button:not(:disabled),
        a[href] {
            cursor: pointer !important;
        }


        button:disabled {
            cursor: not-allowed !important;
        }

    </style>

</head>


<body class="bg-slate-950">

    <div class="min-h-screen p-4 sm:p-6">

        <div class="mx-auto max-w-3xl space-y-6">


            {{-- ERROR --}}
            @if(session('error'))

                <div
                    class="rounded-2xl border border-red-800 bg-red-950/40 p-4
                           font-semibold text-red-300 shadow-lg"
                >
                    ❌ {{ session('error') }}
                </div>

            @endif


            {{-- CABECERA --}}
            <section
                class="rounded-3xl border border-orange-900/50 bg-zinc-900 p-6
                       shadow-xl transition duration-200
                       hover:-translate-y-0.5 hover:shadow-2xl"
            >

                <div class="flex items-start gap-4">

                    <div
                        class="flex h-14 w-14 shrink-0 items-center justify-center
                               rounded-2xl bg-orange-950 text-2xl"
                    >
                        📷
                    </div>


                    <div>

                        <div
                            class="mb-2 inline-flex items-center rounded-full
                                   bg-orange-950 px-3 py-1
                                   text-xs font-black uppercase text-orange-300"
                        >
                            Control de acceso
                        </div>


                        <h1 class="text-3xl font-black text-white">
                            Escanear QR
                        </h1>


                        <p class="mt-2 text-sm leading-6 text-zinc-400">
                            Escaneá el QR del socio para registrar su ingreso o egreso.
                        </p>

                    </div>

                </div>

            </section>


            {{-- ESCÁNER --}}
            <section
                class="rounded-3xl border border-orange-900/40 bg-zinc-900
                       p-5 shadow-xl sm:p-6"
            >

                <div class="mb-5">

                    <h2 class="text-xl font-black text-white">
                        📱 Cámara
                    </h2>

                    <p class="mt-1 text-sm text-zinc-500">
                        Apuntá la cámara al código QR del socio.
                    </p>

                </div>


                {{-- CONTENEDOR LECTOR --}}
                <div
                    class="rounded-2xl border border-zinc-800 bg-zinc-950 p-4
                           shadow-inner"
                >

                    <div
                        id="reader"
                        class="w-full"
                    ></div>

                </div>


                {{-- RESULTADO --}}
                <div
                    id="resultado"
                    class="hidden mt-5 rounded-2xl border border-green-800
                           bg-green-950/40 p-4 font-bold text-green-300 shadow-lg"
                >
                </div>


                {{-- APAGAR CÁMARA --}}
                <button
                    id="btnApagar"
                    type="button"
                    style="cursor: pointer !important;"
                    class="hidden mt-5 w-full rounded-xl bg-red-600 px-5 py-3
                           text-base font-black text-white shadow-md
                           transition duration-150
                           hover:-translate-y-0.5 hover:bg-red-500 hover:shadow-xl
                           active:scale-[0.97]"
                >
                    ⛔ Apagar cámara
                </button>


                {{-- VOLVER --}}
                <div
                    id="bloqueVolver"
                    class="hidden mt-5"
                >

                    <a
                        href="{{ route('asistencias.index') }}"
                        style="cursor: pointer !important;"
                        class="inline-flex items-center justify-center gap-2
                               rounded-xl border border-zinc-700 bg-zinc-800
                               px-5 py-3 text-sm font-bold text-white
                               shadow-md transition duration-150
                               hover:-translate-y-0.5 hover:bg-zinc-700 hover:shadow-xl
                               active:scale-[0.97]"
                    >
                        ← Volver a asistencias
                    </a>

                </div>

            </section>


            {{-- AYUDA --}}
            <section
                class="rounded-2xl border border-orange-900/40
                       bg-orange-950/20 p-5"
            >

                <div class="flex items-start gap-3">

                    <div class="text-xl">
                        💡
                    </div>

                    <div>

                        <h3 class="font-black text-orange-300">
                            Escaneo rápido
                        </h3>

                        <p class="mt-1 text-sm leading-6 text-zinc-400">
                            Una vez detectado el código QR, la asistencia se registra automáticamente.
                        </p>

                    </div>

                </div>

            </section>


        </div>

    </div>


<script>

    const btnApagar = document.getElementById('btnApagar');


    function onScanSuccess(decodedText, decodedResult) {

        const resultado = document.getElementById('resultado');

        resultado.classList.remove('hidden');

        resultado.innerHTML = `
            <div class="flex items-center gap-3">

                <div class="text-xl">
                    ✅
                </div>

                <div>
                    QR detectado. Registrando asistencia...
                </div>

            </div>
        `;

        html5QrcodeScanner.clear();


        const form = document.createElement('form');

        form.method = 'POST';

        form.action = decodedText;


        const csrf = document.createElement('input');

        csrf.type = 'hidden';

        csrf.name = '_token';

        csrf.value = '{{ csrf_token() }}';


        form.appendChild(csrf);

        document.body.appendChild(form);

        form.submit();

    }


    const html5QrcodeScanner = new Html5QrcodeScanner(
        "reader",
        {
            fps: 10,

            qrbox: 250,

            videoConstraints: {
                facingMode: {
                    ideal: "environment"
                }
            }
        },
        false
    );


    html5QrcodeScanner.render(onScanSuccess);


    setTimeout(() => {

        const observer = new MutationObserver(() => {

            const video = document.querySelector('#reader video');

            if (video) {

                btnApagar.classList.remove('hidden');

            }

        });


        observer.observe(
            document.getElementById('reader'),
            {
                childList: true,
                subtree: true
            }
        );

    }, 500);


    btnApagar.addEventListener('click', async function () {

        try {

            btnApagar.disabled = true;

            btnApagar.innerHTML = '⏳ Apagando cámara...';

            btnApagar.style.cursor = 'wait';


            await html5QrcodeScanner.clear();


            document.getElementById('reader').innerHTML = '';


            btnApagar.classList.add('hidden');

            btnApagar.disabled = false;

            btnApagar.innerHTML = '⛔ Apagar cámara';


            document
                .getElementById('bloqueVolver')
                .classList
                .remove('hidden');


        } catch (e) {

            console.error(e);

            btnApagar.disabled = false;

            btnApagar.innerHTML = '⛔ Apagar cámara';

            btnApagar.style.cursor = 'pointer';

        }

    });

</script>


</body>

</html>