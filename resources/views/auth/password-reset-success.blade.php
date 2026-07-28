<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Contraseña restablecida</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;

            font-family:
                Arial,
                Helvetica,
                sans-serif;

            background:
                linear-gradient(
                    145deg,
                    #07111f,
                    #10263e
                );

            color: #ffffff;
        }

        .card {
            width: 100%;
            max-width: 440px;
            padding: 36px 28px;

            text-align: center;

            background: rgba(255, 255, 255, 0.08);

            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 24px;

            box-shadow:
                0 20px 50px rgba(0, 0, 0, 0.35);
        }

        .icon {
            width: 76px;
            height: 76px;
            margin: 0 auto 24px;

            display: flex;
            align-items: center;
            justify-content: center;

            border-radius: 50%;

            background: #22c55e;

            font-size: 40px;
            font-weight: bold;

            box-shadow:
                0 12px 28px rgba(34, 197, 94, 0.32);
        }

        h1 {
            margin: 0 0 16px;

            font-size: 27px;
            line-height: 1.2;
        }

        p {
            margin: 0;

            color: #d5deea;

            font-size: 17px;
            line-height: 1.6;
        }

        .highlight {
            display: block;
            margin-top: 20px;

            color: #ffffff;

            font-weight: bold;
        }

        .close-message {
            margin-top: 28px;

            color: #9fb0c5;

            font-size: 14px;
        }
    </style>
</head>

<body>

    <main class="card">

        <div class="icon">
            ✓
        </div>

        <h1>
            Contraseña restablecida
        </h1>

        <p>
            Tu contraseña fue actualizada correctamente.

            <span class="highlight">
                Volvé a la aplicación del gimnasio e iniciá sesión
                con tu nueva contraseña.
            </span>
        </p>

        <div class="close-message">
            Ya podés cerrar esta página.
        </div>

    </main>

</body>
</html>