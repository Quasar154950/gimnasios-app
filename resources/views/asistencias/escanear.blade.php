<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escanear QR</title>

    <script src="https://unpkg.com/html5-qrcode"></script>
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        #reader {
            border: none !important;
        }

        #reader button {
            background: #f97316 !important;
            color: white !important;
            border: none !important;
            border-radius: 16px !important;
            padding: 14px 20px !important;
            font-weight: 700 !important;
            font-size: 16px !important;
            cursor: pointer !important;
        }

        #reader button:hover {
            background: #ea580c !important;
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

        #reader__scan_region {
            background: white !important;
            border-radius: 16px !important;
            padding: 12px !important;
        }

        #reader__dashboard_section {
            padding: 16px !important;
        }
    </style>
</head>

<body class="bg-zinc-100">

    <div class="min-h-screen p-6">
        <div class="max-w-3xl mx-auto">

            @if(session('error'))
                <div class="mb-4 rounded-2xl bg-red-100 border border-red-300 p-4 text-red-800 font-semibold">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-stone-200 border border-stone-300 rounded-2xl shadow-md p-6">

                <h1 class="text-3xl font-bold text-zinc-800 mb-2">
                    📷 Escanear QR
                </h1>

                <p class="text-zinc-600 mb-6">
                    Escaneá el QR del socio para registrar ingreso o egreso.
                </p>

                <div class="bg-white rounded-2xl p-4 border border-stone-300">
                    <div id="reader" class="w-full"></div>
                </div>

                <div id="resultado"
                     class="hidden mt-6 rounded-2xl bg-green-100 border border-green-300 p-4 text-green-800 font-semibold">
                </div>

                <button
                    id="btnApagar"
                    type="button"
                    class="hidden mt-6 w-full rounded-2xl bg-red-600 px-5 py-3 text-base font-bold text-white hover:bg-red-700 transition"
                >
                    ⛔ Apagar cámara
                </button>

                <div id="bloqueVolver" class="hidden mt-6">
                    <a href="{{ route('asistencias.index') }}"
                       class="inline-flex items-center rounded-xl bg-zinc-800 px-4 py-2 text-sm font-bold text-white hover:bg-zinc-900 transition">
                        Volver a asistencias
                    </a>
                </div>

            </div>

        </div>
    </div>

<script>

    const btnApagar = document.getElementById('btnApagar');

    function onScanSuccess(decodedText, decodedResult) {

        const resultado = document.getElementById('resultado');

        resultado.classList.remove('hidden');
        resultado.innerHTML = '✅ QR detectado. Registrando asistencia...';

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
                facingMode: { ideal: "environment" }
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

        observer.observe(document.getElementById('reader'), {
            childList: true,
            subtree: true
        });

    }, 500);

    btnApagar.addEventListener('click', async function () {

        try {

            await html5QrcodeScanner.clear();

            document.getElementById('reader').innerHTML = '';

            btnApagar.classList.add('hidden');

            document.getElementById('bloqueVolver').classList.remove('hidden');

        } catch (e) {
            console.error(e);
        }

    });

</script>

</body>
</html>