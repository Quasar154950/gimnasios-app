<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <title>
        Comprobante {{ str_pad($pago->numero_comprobante, 6, '0', STR_PAD_LEFT) }}
    </title>

    <style>
        @page {
            margin: 35px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            color: #292524;
            font-size: 13px;
            margin: 0;
            padding: 0;
        }

        .comprobante {
            border: 1px solid #d6d3d1;
            border-radius: 8px;
            overflow: hidden;
        }

        .cabecera {
            background: #f5f5f4;
            border-bottom: 1px solid #d6d3d1;
            padding: 25px;
            text-align: center;
        }

        .gimnasio {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }

        .tipo {
            color: #78716c;
            margin-top: 5px;
        }

        .numero {
            color: #ea580c;
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
        }

        .contenido {
            padding: 28px;
        }

        .fila {
            width: 100%;
            margin-bottom: 20px;
        }

        .columna {
            width: 49%;
            display: inline-block;
            vertical-align: top;
        }

        .etiqueta {
            color: #78716c;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
        }

        .valor {
            font-size: 14px;
            font-weight: bold;
        }

        .importe {
            margin: 25px 0;
            padding: 20px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            text-align: center;
        }

        .importe .etiqueta {
            color: #15803d;
        }

        .importe .monto {
            color: #15803d;
            font-size: 28px;
            font-weight: bold;
            margin-top: 5px;
        }

        .seccion {
            border-top: 1px solid #e7e5e4;
            padding-top: 18px;
            margin-top: 18px;
        }

        .vencimiento {
            color: #7e22ce;
            font-size: 16px;
            font-weight: bold;
        }

        .estado {
            color: #15803d;
            font-weight: bold;
        }

        .pie {
            background: #f5f5f4;
            border-top: 1px solid #d6d3d1;
            padding: 18px;
            text-align: center;
            color: #78716c;
            font-size: 10px;
        }

        .aclaracion {
            margin-top: 5px;
            color: #a8a29e;
        }
    </style>
</head>

<body>

    <div class="comprobante">

        {{-- CABECERA --}}
        <div class="cabecera">

            <p class="gimnasio">
                {{ auth()->user()->name }}
            </p>

            <div class="tipo">
                Comprobante interno de pago
            </div>

            <div class="numero">
                N.º {{ str_pad($pago->numero_comprobante, 6, '0', STR_PAD_LEFT) }}
            </div>

        </div>


        {{-- CONTENIDO --}}
        <div class="contenido">

            <div class="fila">

                <div class="columna">
                    <div class="etiqueta">
                        Socio
                    </div>

                    <div class="valor">
                        {{ $pago->cliente->nombre }}
                    </div>
                </div>

                <div class="columna">
                    <div class="etiqueta">
                        Fecha de pago
                    </div>

                    <div class="valor">
                        {{ $pago->fecha_pago?->format('d/m/Y') ?? 'Sin fecha' }}
                    </div>
                </div>

            </div>


            <div class="fila">

                <div class="columna">
                    <div class="etiqueta">
                        Método de pago
                    </div>

                    <div class="valor">
                        {{ $pago->metodo_pago ?: 'Sin especificar' }}
                    </div>
                </div>

                <div class="columna">
                    <div class="etiqueta">
                        Estado
                    </div>

                    <div class="valor estado">
                        Pago aprobado
                    </div>
                </div>

            </div>


            {{-- IMPORTE --}}
            <div class="importe">

                <div class="etiqueta">
                    Importe abonado
                </div>

                <div class="monto">
                    $ {{ number_format((float) $pago->monto, 2, ',', '.') }}
                </div>

            </div>


            {{-- CONCEPTO --}}
            <div class="seccion">

                <div class="etiqueta">
                    Concepto
                </div>

                <div class="valor">
                    {{ $pago->observacion ?: 'Renovación de cuota mensual' }}
                </div>

            </div>


            {{-- VENCIMIENTO --}}
            <div class="seccion">

                <div class="etiqueta">
                    Cuota vigente hasta
                </div>

                <div class="vencimiento">
                    {{ $pago->vencimiento_cuota?->format('d/m/Y') ?? 'Sin vencimiento' }}
                </div>

            </div>


            {{-- ORIGEN --}}
            <div class="seccion">

                <div class="etiqueta">
                    Origen del pago
                </div>

                <div class="valor">
                    {{ $pago->origen === 'mercadopago'
                        ? 'Mercado Pago'
                        : 'Registrado por el gimnasio' }}
                </div>

            </div>

        </div>


        {{-- PIE --}}
        <div class="pie">

            Comprobante N.º
            {{ str_pad($pago->numero_comprobante, 6, '0', STR_PAD_LEFT) }}

            <div class="aclaracion">
                Comprobante interno emitido por el gimnasio.
            </div>

        </div>

    </div>

</body>
</html>