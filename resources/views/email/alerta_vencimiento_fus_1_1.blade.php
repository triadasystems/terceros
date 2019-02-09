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
    <p>La siguiente lista muestra los FUSES que estan proximos a vencer</p>
    <div>
        <table border="1">
            <thead bgcolor="#fcfcfc">
                <th><p><b>Número de FUS</b></p></th>
                <th><p><b>Número de FUS Fisico</b></p></th>
                <th><p><b>Descripción</b></p></th>
                <th><p><b>Autorizador/Responsable</b></p></th>
                <th><p><b>Aplicaciones</b></p></th>
            </thead>
            <tbody>
                <tr>
                    <td align="center">{{ $obj_mail->data["id_generate_fus"]}}</td>
                    <td align="center">{{ $obj_mail->data["fus_physical"] }}</td>
                    <td align="center">{{ $obj_mail->data["description"] }}</td>
                    <td align="center">{{ $obj_mail->data["aut_res"] }}</td>
                    <td align="center">{{ $obj_mail->data["app"] }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>