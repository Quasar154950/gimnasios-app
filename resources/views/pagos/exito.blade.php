<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pago aprobado</title>

    <style>
        body{
            margin:0;
            font-family:Arial, Helvetica, sans-serif;
            background:#f5f7fa;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .card{
            background:#fff;
            padding:40px;
            border-radius:18px;
            box-shadow:0 15px 35px rgba(0,0,0,.10);
            text-align:center;
            max-width:420px;
        }

        .icon{
            font-size:70px;
        }

        h1{
            color:#16a34a;
            margin-top:20px;
        }

        p{
            color:#555;
            line-height:1.6;
        }

        .btn{
            display:inline-block;
            margin-top:25px;
            padding:12px 28px;
            background:#16a34a;
            color:white;
            text-decoration:none;
            border-radius:8px;
            font-weight:bold;
        }
    </style>
</head>
<body>

<div class="card">

    <div class="icon">✅</div>

    <h1>¡Pago realizado!</h1>

    <p>
        Tu cuota fue acreditada correctamente.
    </p>

    <p>
        Ya podés cerrar esta ventana y volver a la aplicación.
    </p>

    <a href="/" class="btn">Aceptar</a>

</div>

</body>
</html>