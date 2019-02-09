<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Notificación de alerta de</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" media="screen" href="main.css" />
    <script src="main.js"></script>
</head>
<body>
    <p>Notificación de periodo de alerta de vencimiento</p>
    <p>La siguiente lista muestra los usuarios que estan proximos a ser dados de baja</p>
    <div>
        <table border="1">
            <thead bgcolor="#fcfcfc">
                <th><p><b>Número de FUS</b></p></th>
                <th><p><b>Número de FUS FIsico</b></p></th>
                <th><p><b>Descripción</b></p></th>
                <th><p><b>Autorizador/Responsable</b></p></th>
                <th><p><b>Aplicaciones</b></p></th>
            </thead>
            <tbody>
                @foreach($obj_mail->data as $val)
                <tr>
                    <td align="center">{{ $val["id_generate_fus"]}}</td>
                    <td align="center">{{ $val["fus_physical"] }}</td>
                    <td align="center">{{ $val["description"] }}</td>
                    <td align="center">{{ $val["aut_res"] }}</td>
                    <td align="center">{{ $val["app"] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>