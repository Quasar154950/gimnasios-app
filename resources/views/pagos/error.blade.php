<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago no realizado</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            box-sizing: border-box;
        }

        .card {
            background: #ffffff;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, .10);
            text-align: center;
            max-width: 420px;
            width: 100%;
            box-sizing: border-box;
        }

        .icon {
            font-size: 70px;
        }

        h1 {
            color: #dc2626;
            margin-top: 20px;
        }

        p {
            color: #555;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            margin-top: 25px;
            padding: 12px 28px;
            background: #dc2626;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
        }
    </style>
</head>
<body>

<div class="card">

    <div class="icon">❌</div>

    <h1>Pago no realizado</h1>

    <p>
        El pago fue cancelado o no pudo completarse.
    </p>

    <p>
        No se realizó ningún cargo en tu cuenta.
    </p>

    <p>
        Podés volver a intentarlo desde la aplicación cuando lo desees.
    </p>

    <a href="/" class="btn">Aceptar</a>

</div>

</body>
</html>